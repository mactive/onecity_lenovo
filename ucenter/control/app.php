<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: app.php 12180 2008-01-17 05:56:43Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends base {

	function control() {
		$this->base();
		$this->load('app');
		$this->load('misc');
	}

	function onls() {
		$this->init_input();
		$applist = $_ENV['app']->get_apps('appid, type, name, url, tagtemplates');
		$applist2 = array();
		foreach($applist as $key => $app) {
			$app['tagtemplates'] = $this->unserialize($app['tagtemplates']);
			$applist2[$app['appid']] = $app;
		}
		exit($this->serialize($applist2, 1));
	}

	//note public 提供给安装程序的接口，需要校验 Founder 帐号密码
	function onadd() {
		$ucfounderpw = getgpc('ucfounderpw', 'P');
		$apptype = getgpc('apptype', 'P');
		$apptype = getgpc('apptype', 'P');
		$appname = getgpc('appname', 'P');
		$appurl = getgpc('appurl', 'P');
		$appip = getgpc('appip', 'P');
		$appcharset = getgpc('appcharset', 'P');
		$appdbcharset = getgpc('appdbcharset', 'P');
		$apptagtemplates = getgpc('apptagtemplates', 'P');
			
		if(md5(md5($ucfounderpw).UC_FOUNDERSALT) == UC_FOUNDERPW || (strlen($ucfounderpw) == 32 && $ucfounderpw == md5(UC_FOUNDERPW))) {
			//note 判断是否存在
			@ob_start();
			$return  = '';
			
			$app = $this->db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."applications WHERE url='$appurl' AND type='$apptype'");
			
			if(empty($app)) {
				$authkey = $this->_generate_key();
				$apptagtemplates = $this->serialize($apptagtemplates, 1);
				$this->db->query("INSERT INTO ".UC_DBTABLEPRE."applications SET name='$appname', url='$appurl', ip='$appip', authkey='$authkey', synlogin='1', charset='$appcharset', dbcharset='$appdbcharset', type='$apptype', recvnote='1', tagtemplates='$apptagtemplates'");
				$appid = $this->db->insert_id();

				$_ENV['app']->alter_app_table($appid, 'ADD');
				//$return = "UC_STATUS_OK|$authkey|$appid|".UC_DBHOST.'|'.UC_DBNAME.'|'.UC_DBUSER.'|'.UC_DBPW.'|'.UC_DBCHARSET.'|'.UC_DBTABLEPRE.'|'.UC_CHARSET;
				$return = "$authkey|$appid|".UC_DBHOST.'|'.UC_DBNAME.'|'.UC_DBUSER.'|'.UC_DBPW.'|'.UC_DBCHARSET.'|'.UC_DBTABLEPRE.'|'.UC_CHARSET;
				$this->load('cache');
				$_ENV['cache']->updatedata('apps');

				$this->load('note');
				$notedata = $this->db->fetch_all("SELECT appid, type, name, url, ip, charset, synlogin, extra FROM ".UC_DBTABLEPRE."applications");
				$notedata = $this->_format_notedata($notedata);
				$notedata['UC_API'] = UC_API;
				$_ENV['note']->add('updateapps', '', $this->serialize($notedata));
				$_ENV['note']->send();
			} else {
				//$return = "UC_STATUS_OK|$app[authkey]|$app[appid]|".UC_DBHOST.'|'.UC_DBNAME.'|'.UC_DBUSER.'|'.UC_DBPW.'|'.UC_DBCHARSET.'|'.UC_DBTABLEPRE.'|'.UC_CHARSET;
				$return = "$app[authkey]|$app[appid]|".UC_DBHOST.'|'.UC_DBNAME.'|'.UC_DBUSER.'|'.UC_DBPW.'|'.UC_DBCHARSET.'|'.UC_DBTABLEPRE.'|'.UC_CHARSET;
			}
			@ob_end_clean();
			exit($return);
		} else {
			exit('-1');
		}
	}
	
	function onucinfo() {
		$arrapptypes = $this->db->fetch_all("SELECT DISTINCT type FROM ".UC_DBTABLEPRE."applications");
		$apptypes = $tab = '';
		foreach($arrapptypes as $apptype) {
			$apptypes .= $tab.$apptype['type'];
			$tab = "\t";
		}
		exit("UC_STATUS_OK|".UC_VERSION."|".UC_RELEASE."|".UC_CHARSET."|".UC_DBCHARSET."|".$apptypes);
	}

/*
	function onapptypeexists() {
		$apptype = getgpc('apptype', 'G');
		$exists = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."applications WHERE type='$apptype'");
		if($exists) {
			exit('1');
		} else {
			exit('0');
		}
	}
*/
	
	function _random($length, $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}

	function _generate_key() {
		$random = $this->_random(32);
		$info = md5($_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_NAME'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_PORT'].$_SERVER['HTTP_USER_AGENT'].time());
		$return = array();
		for($i=0; $i<32; $i++) {
			$return[$i] = $random[$i].$info[$i];
		}
		return implode('', $return);
	}
	
	function _format_notedata($notedata) {
		$arr = array();
		foreach($notedata as $key=>$note) {
			$arr[$note['appid']] = $note;
		}	
		return $arr;
	}
}

?>