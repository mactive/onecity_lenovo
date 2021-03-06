<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: base.php 12189 2008-01-17 07:29:56Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

/**
 * 该基类有三个用途:
 * 	1. 纯接口(任何人, 此时需要检查 $input 的值)
 *	2. 带交互界面的(限会员, 检查 $input 中 uid)
 * 	3. 管理(限管理员, 检查 $_COOKIE['uc_auth'] 是否为founder)
 */
class base {

	var $time;
	var $onlineip;
	var $db;
	var $view;
	var $user = array();
	var $settings = array();
	var $cache = array();
	var $app = array();
	var $lang = array();
	var $input = array();//note 如果为空，并且存在appid，则从外部传递参数非法。

	/**
	 * 初始化基类
	 *
	 */
	function base() {
		$this->init_var();
		$this->init_db();
		$this->init_cache();
		$this->init_app();
		$this->init_user();
		$this->init_template();
		$this->init_note();
//		$this->cron();
	}

	/**
	 * 初始化常用变量,如果有 code 则解开后,放入 $_GET 超级全局变量
	 *
	 */
	function init_var() {
		$this->time = time();
		$cip = getenv('HTTP_CLIENT_IP');
		$xip = getenv('HTTP_X_FORWARDED_FOR');
		$rip = getenv('REMOTE_ADDR');
		$srip = $_SERVER['REMOTE_ADDR'];
		if($cip && strcasecmp($cip, 'unknown')) {
			$this->onlineip = $cip;
		} elseif($xip && strcasecmp($xip, 'unknown')) {
			$this->onlineip = $xip;
		} elseif($rip && strcasecmp($rip, 'unknown')) {
			$this->onlineip = $rip;
		} elseif($srip && strcasecmp($srip, 'unknown')) {
			$this->onlineip = $srip;
		}
		preg_match("/[\d\.]{7,15}/", $this->onlineip, $match);
		$this->onlineip = $match[0] ? $match[0] : 'unknown';

		define('FORMHASH', $this->formhash());
		$_GET['page'] =  max(1, intval(getgpc('page')));

		include_once UC_ROOT.'./view/default/main.lang.php';
		$this->lang = &$lang;
	}

	function init_cache() {
		//note 全局设置
		$_CACHE = $this->cache('settings');
		$this->settings = &$_CACHE['settings'];
		$this->cache = &$_CACHE;
		if(PHP_VERSION > '5.1') {
			$timeoffset = intval($this->settings['timeoffset'] / 3600);
			@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
		}
	}

	//note 此参数仅为FLASH，或者第三方应用请求用户中心时使用。
	function init_input($getagent = '') {
		//note 解密应用提交的数据
		$input = getgpc('input', 'R');
		if($input) {
			$input = $this->authcode($input, 'DECODE', $this->app['authkey']);
			parse_str($input, $this->input);
			$this->input = daddslashes($this->input, 1, TRUE);
			$agent = $getagent ? $getagent : $this->input['agent'];

			if(($getagent && $getagent != $this->input['agent']) || (!$getagent && md5($_SERVER['HTTP_USER_AGENT']) != $agent)) {
				exit('Access denied for agent changed');
			} elseif($this->time - $this->input('time') > 3600) {
				exit('Authorization has expired');
			}
		}
		if(empty($this->input)) {
			exit('Invalid input');
		}
	}

	/**
	 * 实例化数据库类
	 *
	 */
	function init_db() {
		require_once UC_ROOT.'lib/db.class.php';
		$this->db = new db();
		$this->db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, UC_DBCHARSET, UC_DBCONNECT, UC_DBTABLEPRE);
	}

	function init_app() {
		$appid = intval(getgpc('appid'));
		$appid && $this->app = $this->cache['apps'][$appid];
	}

	/**
	 * 初始化用户数据
	 *
	 */
	function init_user() {
		//note 解密 cookie
		if(isset($_COOKIE['uc_auth'])) {
			@list($uid, $username, $agent) = explode('|', $this->authcode($_COOKIE['uc_auth'], 'DECODE', ($this->input ? $this->app['appauthkey'] : UC_KEY)));
			if($agent != md5($_SERVER['HTTP_USER_AGENT'])) {
				$this->setcookie('uc_auth', '');
			} else {
				@$this->user['uid'] = $uid;
				@$this->user['username'] = $username;
			}
		}
	}

	/**
	 * 实例化模板类
	 *
	 */
	function init_template() {
		$charset = UC_CHARSET;
		require_once UC_ROOT.'lib/template.class.php';
		$this->view = new template();
		$this->view->assign('dbhistories', $this->db->histories);
		$this->view->assign('charset', $charset);
		$this->view->assign('dbquerynum', $this->db->querynum);
		$this->view->assign('user', $this->user);
	}

	function init_note() {
		if($this->note_exists()) {
			$this->load('note');
			$_ENV['note']->send();
		}
	}

	/**
	 * 字符串加密以及解密函数
	 *
	 * @param string $string 原文或者密文
	 * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
	 * @param string $key 密钥
	 * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
	 * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
	 *
	 * @example
	 *
	 * 	$a = authcode('abc', 'ENCODE', 'key');
	 * 	$b = authcode($a, 'DECODE', 'key');  // $b(abc)
	 *
	 * 	$a = authcode('abc', 'ENCODE', 'key', 3600);
	 * 	$b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
	 */
	function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

		$ckey_length = 4;	// 随机密钥长度 取值 0-32;
					// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
					// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
					// 当此值为 0 时，则不产生随机密钥

		$key = md5($key ? $key : UC_KEY);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}

	}

	/**
	 * 翻页函数
	 *
	 * @param int $num 总纪录数
	 * @param int $perpage 每页大小
	 * @param int $curpage 当前页面
	 * @param string $mpurl url
	 * @return string 类似于: <div class="page">***</div>
	 */
	function page($num, $perpage, $curpage, $mpurl) {
		$multipage = '';
		$mpurl .= strpos($mpurl, '?') ? '&' : '?';
		if($num > $perpage) {
			$page = 10;
			$offset = 2;

			$pages = @ceil($num / $perpage);

			if($page > $pages) {
				$from = 1;
				$to = $pages;
			} else {
				$from = $curpage - $offset;
				$to = $from + $page - 1;
				if($from < 1) {
					$to = $curpage + 1 - $from;
					$from = 1;
					if($to - $from < $page) {
						$to = $page;
					}
				} elseif($to > $pages) {
					$from = $pages - $page + 1;
					$to = $pages;
				}
			}

			$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="first"'.$ajaxtarget.'>1 ...</a>' : '').
				($curpage > 1 && !$simple ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="prev"'.$ajaxtarget.'>&lsaquo;&lsaquo;</a>' : '');
			for($i = $from; $i <= $to; $i++) {
				$multipage .= $i == $curpage ? '<strong>'.$i.'</strong>' :
					'<a href="'.$mpurl.'page='.$i.($ajaxtarget && $i == $pages && $autogoto ? '#' : '').'"'.$ajaxtarget.'>'.$i.'</a>';
			}

			$multipage .= ($curpage < $pages && !$simple ? '<a href="'.$mpurl.'page='.($curpage + 1).'" class="next"'.$ajaxtarget.'>&rsaquo;&rsaquo;</a>' : '').
				($to < $pages ? '<a href="'.$mpurl.'page='.$pages.'" class="last"'.$ajaxtarget.'>... '.$realpages.'</a>' : '').
				(!$simple && $pages > $page && !$ajaxtarget ? '<kbd><input type="text" name="custompage" size="3" onkeydown="if(event.keyCode==13) {window.location=\''.$mpurl.'page=\'+this.value; return false;}" /></kbd>' : '');

			$multipage = $multipage ? '<div class="pages">'.(!$simple ? '<em>&nbsp;'.$num.'&nbsp;</em>' : '').$multipage.'</div>' : '';
		}
		return $multipage;
	}

	/**
	 * 对翻页的起始位置进行判断和调整
	 *
	 * @param int $page 页码
	 * @param int $ppp 每页大小
	 * @param int $totalnum 总纪录数
	 * @return unknown
	 */
	function page_get_start($page, $ppp, $totalnum) {
		$totalpage = ceil($totalnum / $ppp);
		$page =  max(1, min($totalpage, intval($page)));
		return ($page - 1) * $ppp;
	}

	/**
	 * 加载相应的 Model, 存入 $_ENV 超级全局变量
	 *
	 * @param string $model 模块名称
	 * @param 该模块相对的基类 $base 默认为该基类
	 * @return 此处不需要返回
	 */
	function load($model, $base = NULL) {
		$base = $base ? $base : $this;
		if(empty($_ENV[$model])) {
			require_once UC_ROOT."model/$model.php";
			eval('$_ENV[$model] = new '.$model.'model($base);');
		}
		return $_ENV[$model];
	}

	/**
	 * 得到设置的值
	 *
	 * @param string $k 设置的项
	 * @param string $decode 是否进行反序列化，一般为数组时，需要指定为TRUE
	 * @return string/array 设置的值
	 */
	function get_setting($k = array(), $decode = FALSE) {
		$return = array();
		$sqladd = $k ? "WHERE k IN (".$this->implode($k).")" : '';
		$settings = $this->db->fetch_all("SELECT * FROM ".UC_DBTABLEPRE."settings $sqladd");
		if(is_array($settings)) {
			foreach($settings as $arr) {
				$return[$arr['k']] = $decode ? unserialize($arr['v']) : $arr['v'];
			}
		}
		return $return;
	}

	/**
	 * 将设置保存
	 *
	 * @param string $k 设置的项
	 * @param string/array $v  设置的值
	 * @param string $encode 是否序列化， $v 为数组时需要指定为 TRUE
	 */
	function set_setting($k, $v, $encode = FALSE) {
		$v = is_array($v) || $encode ? addslashes(serialize($v)) : $v;
		$this->db->query("REPLACE INTO ".UC_DBTABLEPRE."settings SET k='$k', v='$v'");
	}

	/**
	 * 类似于 Discuz! showmessage() 函数
	 *
	 * @param string $message 消息
	 * @param string $redirect 下一页URL, 'BACK' 表示返回
	 * @param int $type 0 表示用户中心的提示 1 表示针对客户端的提示.主要给短消息使用
	 * @param array 消息中的变量
	 */
 	function message($message, $redirect = '', $type = 0, $vars = array()) {
 		include_once UC_ROOT.'view/default/messages.lang.php';
 		if(isset($lang[$message])) {
 			$message = $lang[$message] ? str_replace(array_keys($vars), array_values($vars), $lang[$message]) : $message;
 		}
 		$this->view->assign('message', $message);
 		$this->view->assign('redirect', $redirect);
 		if($type == 0) {
 			$this->view->display('message');
 		} elseif($type == 1) {
 			$this->view->display('message_client');
 		}
		exit;
	}

	/**
	 * Formhash 用来生成表单校验码,防止外部提交
	 *
	 * @return 16位的字串, 取时间的前面6位, 有效期为 9999 秒
	 */
	function formhash() {
		return substr(md5(substr($this->time, 0, -4).UC_KEY), 16);
	}

	/**
	 * Formhash 校验
	 *
	 * @return unknown
	 */
	function submitcheck() {
		return @getgpc('formhash', 'P') == FORMHASH ? true : false;
	}

	/**
	 * 日期格式化 默认为格式化到分钟
	 *
	 * @param int $time
	 * @param int $type 	1：只显示时间 2：只显示日期 3：日期时间均显示
	 * @return string
	 */
	function date($time, $type = 3) {
		$format[] = $type & 2 ? (!empty($this->settings['dateformat']) ? $this->settings['dateformat'] : 'Y-n-j') : '';
		$format[] = $type & 1 ? (!empty($this->settings['timeformat']) ? $this->settings['timeformat'] : 'H:i') : '';
		return gmdate(implode(' ', $format), $time + $this->settings['timeoffset']);
	}

	/**
	 * 对字符或者数组加逗号连接, 用来
	 *
	 * @param string/array $arr 可以传入数字或者字串
	 * @return string 这样的格式: '1','2','3'
	 */
	function implode($arr) {
		return "'".implode("','", (array)$arr)."'";
	}

	/**
	 * 创建用户的 home 目录
	 *
	 * @param int $uid
	 * @param string $dir
	 * @return string
	 */
	function set_home($uid, $dir = '.') {
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		!is_dir($dir.'/'.$dir1) && mkdir($dir.'/'.$dir1, 0777);
		!is_dir($dir.'/'.$dir1.'/'.$dir2) && mkdir($dir.'/'.$dir1.'/'.$dir2, 0777);
		!is_dir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3) && mkdir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3, 0777);
	}

	/**
	 * 根据用户的 uid 得到用户的 home 目录
	 *
	 * @param int $uid
	 * @return string
	 */
	function get_home($uid) {
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		return $dir1.'/'.$dir2.'/'.$dir3;
	}

	/**
	 * 根据用户的 uid 得到用户的头像
	 *
	 * @param int $uid
	 * @return string
	 */
	function get_avatar($uid, $size = 'big') {
		$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
		$uid = abs(intval($uid));
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2)."_avatar_$size.jpg";
	}

	/**
	 * 加载缓存文件, 如果不存在,则重新生成
	 *
	 * @param string $cachefile
	 */
	function &cache($cachefile) {
		$_CACHE = array();
		$cachepath = UC_DATADIR.'cache/'.$cachefile.'.php';
		if(!@include_once $cachepath) {
			$this->load('cache');
			$_ENV['cache']->updatedata('', $cachefile);
		}
		return $_CACHE;
	}

	function input($k) {
		return isset($this->input[$k]) ? $this->input[$k] : NULL;
	}

	function serialize($s, $htmlon = 0) {
		include_once UC_ROOT.'./lib/xml.class.php';
		return xml_serialize($s, $htmlon);
	}

	function unserialize($s) {
		include_once UC_ROOT.'./lib/xml.class.php';
		return xml_unserialize($s);
	}

	function cutstr($string, $length, $dot = ' ...') {
		if(strlen($string) <= $length) {
			return $string;
		}

		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

		$strcut = '';
		if(strtolower(UC_CHARSET) == 'utf-8') {

			$n = $tn = $noc = 0;
			while($n < strlen($string)) {

				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc += 2;
				} elseif(224 <= $t && $t < 239) {
					$tn = 3; $n += 3; $noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc += 2;
				} else {
					$n++;
				}

				if($noc >= $length) {
					break;
				}

			}
			if($noc > $length) {
				$n -= $tn;
			}

			$strcut = substr($string, 0, $n);

		} else {
			for($i = 0; $i < $length; $i++) {
				$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			}
		}

		$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

		return $strcut.$dot;
	}

	function setcookie($key, $value, $life = 0) {
		if(!defined('UC_COOKIEPATH')) {
			define('UC_COOKIEPATH', '/');
		}
		if(!defined('UC_COOKIEDOMAIN')) {
			define('UC_COOKIEDOMAIN', '');
		}
		setcookie($key, $value, ($life ? $this->time + $life : 0), UC_COOKIEPATH, UC_COOKIEDOMAIN, ($_SERVER['SERVER_PORT'] == 443 ? 1 : 0));
	}

	function note_exists() {
		$noteexists = $this->db->fetch_first("SELECT value FROM ".UC_DBTABLEPRE."vars WHERE name='noteexists'");
		if(empty($noteexists)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
}

?>