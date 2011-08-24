<?php
/**
 * UCenter API
 * ===========================================================
 * 版权所有 (C) 2005-2008 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；http://www.comsenz.com
 * ----------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ==========================================================
 * $Author: testyang $
 * $Id: uc.php 14649 2008-06-05 10:13:34Z testyang $
 */

define('UC_VERSION', '1.0.0');        //UCenter 版本标识
define('API_DELETEUSER', 1);        //用户删除 API 接口开关
define('API_GETTAG', 1);        //获取标签 API 接口开关
define('API_SYNLOGIN', 1);        //同步登录 API 接口开关
define('API_SYNLOGOUT', 1);        //同步登出 API 接口开关
define('API_UPDATEPW', 1);        //更改用户密码 开关
define('API_UPDATEBADWORDS', 1);    //更新关键字列表 开关
define('API_UPDATEHOSTS', 1);        //更新域名解析缓存 开关
define('API_UPDATEAPPS', 1);        //更新应用列表 开关
define('API_UPDATECLIENT', 1);        //更新客户端缓存 开关
define('API_UPDATECREDIT', 1);        //更新用户积分 开关
define('API_GETCREDITSETTINGS', 1);    //向 UCenter 提供积分设置 开关
define('API_UPDATECREDITSETTINGS', 1);    //更新应用积分设置 开关
define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

define('IN_ECS', TRUE);
require './init.php';
include(ROOT_PATH . 'uc_client/client.php');
include(ROOT_PATH . 'uc_client/lib/xml.class.php');
$ecs_url = str_replace('/api', '', $ecs->url());

$code = $_GET['code'];
parse_str(authcode($code, 'DECODE', UC_KEY), $get);

if(time() - $get['time'] > 3600)
{
    exit('Authracation has expiried');
}

if(empty($get))
{
    exit('Invalid Request');
}

$action = $get['action'];
$timestamp = time();

if($action == 'test')
{
    exit(API_RETURN_SUCCEED);
}

/* 用户删除 API 接口 */
elseif($action == 'deleteuser')
{
    !API_DELETEUSER && exit(API_RETURN_FORBIDDEN);
    $uids = $get['ids'];

    if (delete_user($uids))
    {
        exit(API_RETURN_SUCCEED);
    }
}

/* 获取标签 API 接口 */
elseif($action == 'gettag')
{
    !API_GETTAG && exit(API_RETURN_FORBIDDEN);
    $name = trim($get['id']);
    $tags = fetch_tag($name);
    $return = array($name, $tags);
    echo uc_serialize($return, 1);
}

/* 同步登录 API 接口 */
elseif($action == 'synlogin' && $_GET['time'] == $get['time'])
{

    !API_SYNLOGIN && exit(API_RETURN_FORBIDDEN);

    $uid = intval($get['uid']);
    header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
    set_login($uid, $get['username']);
}

/* 同步登出 API 接口 */
elseif($action == 'synlogout')
{

    !API_SYNLOGOUT && exit(API_RETURN_FORBIDDEN);

    header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
    set_cookie();
    set_session();
}

/* 更新客户端缓存 */
elseif($action == 'updateclient')
{
    !API_UPDATECLIENT && exit(API_RETURN_FORBIDDEN);

    $post = xml_unserialize(file_get_contents('php://input'));
    $cachefile = ROOT_PATH . 'uc_client/data/cache/settings.php';
    $fp = fopen($cachefile, 'w');
    $s = "<?php\r\n";
    $s .= '$_CACHE[\'settings\'] = '.var_export($post, TRUE).";\r\n";
    fwrite($fp, $s);
    fclose($fp);
    exit(API_RETURN_SUCCEED);
}

/* 更改用户密码 */
elseif($action == 'updatepw')
{
    !API_UPDATEPW && exit(API_RETURN_FORBIDDEN);

    $username = $get['username'];
    $password = $get['password'];
    $newpw = md5(time().rand(100000, 999999));
    $db->query("UPDATE {$tablepre}members SET password='$newpw' WHERE username='$username'");
    exit(API_RETURN_SUCCEED);
}

/* 更新关键字列表 */
elseif($action == 'updatebadwords')
{

    !API_UPDATEBADWORDS && exit(API_RETURN_FORBIDDEN);

    $post = xml_unserialize(file_get_contents('php://input'));
    $cachefile = ROOT_PATH .'uc_client/data/cache/badwords.php';
    $fp = fopen($cachefile, 'w');
    $s = "<?php\r\n";
    $s .= '$_CACHE[\'badwords\'] = '.var_export($post, TRUE).";\r\n";
    fwrite($fp, $s);
    fclose($fp);
    exit(API_RETURN_SUCCEED);
}

/* 更新HOST文件 */
elseif($action == 'updatehosts')
{

    !API_UPDATEHOSTS && exit(API_RETURN_FORBIDDEN);

    $post = xml_unserialize(file_get_contents('php://input'));
    $cachefile = ROOT_PATH .'uc_client/data/cache/hosts.php';
    $fp = fopen($cachefile, 'w');
    $s = "<?php\r\n";
    $s .= '$_CACHE[\'hosts\'] = '.var_export($post, TRUE).";\r\n";
    fwrite($fp, $s);
    fclose($fp);
    exit(API_RETURN_SUCCEED);
}

/* 更新应用列表 */
elseif($action == 'updateapps')
{
    !API_UPDATEAPPS && exit(API_RETURN_FORBIDDEN);
    $applog_path = ROOT_PATH . './data/app.log';
    $post = uc_unserialize(file_get_contents('php://input'));
    unset($post['UC_API']);
    if (file_exists($applog_path))
    {
        $old_app = unserialize(file_get_contents($applog_path));
    }
    foreach ($post as $app_data)
    {
        if ($app_data['type'] != 'ECSHOP')
        {
            //检查老的APP是否存在
            if (!empty($old_app[$app_data['appid']]))
            {
                //检查应用名称是否变更
                if (($old_app[$app_data['appid']]['name'] != $app_data['name']) || ($old_app[$app_data['appid']]['url'] != $app_data['url']))
                {
                    $change_app[] = $app_data['appid'];
                }
            }
            else
            {
                $add_app[] = $app_data['appid'];
            }
            $appid_list[] = $app_data['appid'];
            $app_list[$app_data['appid']]['type'] = $app_data['type'];
            $app_list[$app_data['appid']]['url'] = $app_data['url'];
            $app_list[$app_data['appid']]['name'] = $app_data['name'];
        }
    }

    //删除过期的应用
    if (!empty($old_app))
    {
        foreach ($old_app as $app_id => $tmp_data)
        {
            if (!in_array($app_id, $appid_list))
            {
                $del_app[] = $app_id;
            }
        }
    }
    //生成app缓存文件
    file_put_contents($applog_path, serialize($app_list));
    //如果添加了新的应用
    if (count($add_app) > 0)
    {
        $item_order = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = '". $item_type ."'") + 1;
        $insert .= '';
        foreach ($add_app as $app_id)
        {
            $insert_str .= "('{$app_list[$app_id][name]}', '1', '{$item_order}', '1', '{$app_list[$app_id][url]}', 'middle'),";
        }
        //插入导航数据
        $db->query("INSERT INTO " . $ecs->table('nav') . "(name, ifshow, vieworder, opennew, url, type) values " . substr($insert_str, 0, -1));
    }
    if (count($change_app) > 0)
    {
        foreach($change_app as $app_id)
        {
            $db->query("UPDATE " . $ecs->table('nav') . " SET name = '{$app_list[$app_id][name]}', url = '{$app_list[$app_id][url]}' WHERE name = '{$old_app[$app_id][name]}' AND url =
            '{$old_app[$app_id][url]}'");
        }
    }
    if (count($del_app) > 0)
    {
        foreach($del_app as $app_id)
        {
            $db->query("DELETE FROM " . $ecs->table('nav') . " WHERE name = '{$old_app[$app_id][name]}' AND url = '{$old_app[$app_id][url]}'");
        }
    }

    clear_cache_files();
    exit(API_RETURN_SUCCEED);
}

/* 更新用户积分 */
elseif($action == 'updatecredit')
{
    !UPDATECREDIT && exit(API_RETURN_FORBIDDEN);
    $credit = intval($get['credit']);
    $amount = intval($get['amount']);
    $uid = intval($get['uid']);
    $sql = "UPDATE " . $ecs-> table('users') . " SET rank_point = rank_point + ('$amount') WHERE user_id = $uid";
    $rs = $db->query($sql);
    $sql2 = "INSERT INTO " . $ecs->table('account_log') . "(user_id, rank_points, change_time, change_desc, change_type)" .
     "VALUES ('$uid', '$amount', 'UCenter', '99')";
    $rs2 = $db->query($sql);
    exit(API_RETURN_SUCCEED);
}


/* 向 UCenter 提供积分设置 */
elseif($action == 'getcreditsettings')
{
    !GETCREDITSETTINGS && exit(API_RETURN_FORBIDDEN);
    $credits = array();
    $credit[] =array('等级积分' => '');
    echo uc_serialize($credits);
}

/* 更新应用积分设置 */
elseif($action == 'updatecreditsettings') {

    !API_UPDATECREDITSETTINGS && exit(API_RETURN_FORBIDDEN);

    exit(API_RETURN_SUCCEED);
}

else
{
    exit(API_RETURN_FAILED);
}

/* 解密函数 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 4;
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
    for($i = 0; $i <= 255; $i++)
    {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++)
    {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++)
    {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE')
    {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))
        {
            return substr($result, 26);
        }
        else
        {
            return '';
        }
    }
    else
    {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}

/**
 * 设置用户登陆
 *
 * @access  public
 * @param int $uid
 * @return void
 */
function set_login($user_id = '', $user_name = '')
{
    if (empty($user_id))
    {
        return ;
    }
    else
    {
        $sql = "SELECT user_name, email FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id='$user_id' LIMIT 1";
        $row = $GLOBALS['db']->getRow($sql);
        if ($row)
        {
            set_cookie($user_id, $row['user_name'], $row['email']);
            set_session($user_id, $row['user_name'], $row['email']);
            include_once(ROOT_PATH . 'includes/lib_main.php');
            update_user_info();
        }
        else
        {
           if($data = uc_get_user($user_name))
           {
                list($uid, $uname, $email) = $data;
                $sql = "REPLACE INTO " . $GLOBALS['ecs']->table('users') ."(user_id, user_name, email) VALUES('$uid', '$uname', '$email')";
                $GLOBALS['db']->query($sql);
                set_login($uid);
            }
            else
            {
                //echo '用户不存在';
                return false;
            }
        }
    }
}


/**
 *  设置cookie
 *
 * @access  public
 * @param
 * @return void
 */
function set_cookie($user_id='', $user_name = '', $email = '')
{
    if (empty($user_id))
    {
        /* 摧毁cookie */
        $time = time() - 3600;
        setcookie('ECS[user_id]',  '', $time);
        setcookie('ECS[username]', '', $time);
        setcookie('ECS[email]',    '', $time);
    }
    else
    {
        /* 设置cookie */
        $time = time() + 3600 * 24 * 30;
        setcookie("ECS[user_id]",  $user_id,   $time, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
        setcookie("ECS[username]", $user_name, $time, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
        setcookie("ECS[email]",    $email,     $time, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
    }
}

/**
 *  设置指定用户SESSION
 *
 * @access  public
 * @param
 * @return void
 */
function set_session ($user_id = '', $user_name = '', $email = '')
{
    if (empty($user_id))
    {
        $GLOBALS['sess']->destroy_session();
    }
    else
    {
        $_SESSION['user_id']   = $user_id;
        $_SESSION['user_name'] = $user_name;
        $_SESSION['email']     = $email;
    }
}

/**
 *  删除用户接口函数
 *
 * @access  public
 * @param   int $uids
 * @return  void
 */
function delete_user($uids = '')
{
    if (empty($uids))
    {
        return;
    }
    else
    {
        $uids = stripslashes($uids);
        $sql = "DELETE FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id IN ($uids)";
        $result = $GLOBALS['db']->query($sql);
        return true;
    }
}


/**
 *  获取EC的TAG数据
 *
 * @access  public
 * @param  string $tagname
 * @param   int    $num 获取的数量 默认取最新的100条
 * @return  array
 */
function fetch_tag($tagname, $num=100)
{
    $rewrite = intval($GLOBALS['_CFG']['rewrite']) > 0;
    $sql = "SELECT t.*, u.user_name, g.goods_name, g.goods_img, g.shop_price FROM " . $GLOBALS['ecs']->table('tag') . " as t, " . $GLOBALS['ecs']->table('users') ." as u, " .
    $GLOBALS['ecs']->table('goods') ." as g WHERE tag_words = '$tagname' AND t.user_id = u.user_id AND g.goods_id = t.goods_id ORDER BY t.tag_id DESC LIMIT " . $num;
    $arr = $GLOBALS['db']->getAll($sql);
    $tag_list = array();
    foreach ($arr as $k=>$v)
    {
        $tag_list[$k]['goods_name']  = $v['goods_name'];
        $tag_list[$k]['uid']         = $v['user_id'];
        $tag_list[$k]['username']    = $v['user_name'];
        $tag_list[$k]['dateline']    = time();
        $tag_list[$k]['url']         = $GLOBALS['ecs_url'] . 'goods.php?id=' . $v['goods_id'];
        $tag_list[$k]['image']       = $GLOBALS['ecs_url'] . $v['goods_img'];
        $tag_list[$k]['goods_price'] = $v['shop_price'];
    }

    return $tag_list;
}
?>