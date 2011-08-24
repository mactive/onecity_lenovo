<?php

/**
 * SINEMALL wap鍓嶅彴鍏?叡鍑芥暟
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2008 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛沨ttp://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: testyang $
 * $Id: init.php 14543 2008-05-04 02:18:37Z testyang $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
define('ECS_WAP', true);

error_reporting(E_ALL);

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 鍙栧緱褰撳墠ecshop鎵€鍦ㄧ殑鏍圭洰褰 */
define('ROOT_PATH', str_replace('wap/includes/init.php', '', str_replace('\\', '/', __FILE__)));

/* 鍒濆?鍖栬?缃 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);
@ini_set("arg_separator.output","&amp;");

if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path',      '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path',      '.:' . ROOT_PATH);
}

if (file_exists(ROOT_PATH . 'data/config.php'))
{
    include(ROOT_PATH . 'data/config.php');
}
else
{
    include(ROOT_PATH . 'includes/config.php');
}

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 7);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);

require(ROOT_PATH . 'includes/cls_ecshop.php');
require(ROOT_PATH . 'includes/lib_goods.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_main.php');
require(ROOT_PATH . 'wap/includes/lib_main.php');
require(ROOT_PATH . 'includes/inc_constant.php');

/* 瀵圭敤鎴蜂紶鍏ョ殑鍙橀噺杩涜?杞?箟鎿嶄綔銆?/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
}

/* 鍒涘缓 ECSHOP 瀵硅薄 */
$ecs = new ECS($db_name, $prefix);

/* 鍒濆?鍖栨暟鎹?簱绫 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 杞藉叆绯荤粺鍙傛暟 */
$_CFG = load_config();

/* 鍒濆?鍖杝ession */
require(ROOT_PATH . 'includes/cls_session.php');
$sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'), 'ecsid');


if (!defined('INIT_NO_SMARTY'))
{
    header('Cache-control: private');
    header('Content-type: text/html; charset=utf-8');

    /* 鍒涘缓 Smarty 瀵硅薄銆?/
    require(ROOT_PATH . 'includes/cls_template.php');
    $smarty = new cls_template;

    $smarty->cache_lifetime = $_CFG['cache_time'];
    $smarty->template_dir   = ROOT_PATH . 'wap/templates';
    $smarty->cache_dir      = ROOT_PATH . 'templates/caches';
    $smarty->compile_dir    = ROOT_PATH . 'templates/compiled/wap';

    if ((DEBUG_MODE & 2) == 2)
    {
        $smarty->direct_output = true;
        $smarty->force_compile = true;
    }
    else
    {
        $smarty->direct_output = false;
        $smarty->force_compile = false;
    }
}

if (!defined('INIT_NO_USERS'))
{
    /* 浼氬憳淇℃伅 */
    $user =& init_users();
    if (empty($_SESSION['user_id']))
    {
        if ($user->get_cookie())
        {
            /* 濡傛灉浼氬憳宸茬粡鐧诲綍骞朵笖杩樻病鏈夎幏寰椾細鍛樼殑甯愭埛浣欓?銆佺Н鍒嗕互鍙婁紭鎯犲埜 */
            if ($_SESSION['user_id'] > 0 && !isset($_SESSION['user_money']))
            {
                update_user_info();
            }
        }
        else
        {
            $_SESSION['user_id']     = 0;
            $_SESSION['user_name']   = '';
            $_SESSION['email']       = '';
            $_SESSION['user_rank']   = 0;
            $_SESSION['discount']    = 1.00;
        }
    }
}

if ((DEBUG_MODE & 1) == 1)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(E_ALL ^ E_NOTICE);
}
if ((DEBUG_MODE & 4) == 4)
{
    include(ROOT_PATH . 'includes/lib.debug.php');
}

/* 鍒ゆ柇鏄?惁鏀?寔gzip妯″紡 */
if (gzip_enabled())
{
    ob_start('ob_gzhandler');
}

/* wap澶存枃浠 */
//if (substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')) != '/user.php')
//{}
header("Content-Type:text/vnd.wap.wml; charset=utf-8");
echo "<?xml version='1.0' encoding='utf-8'?>";
if (empty($_CFG['wap_config']))
{
    echo "<!DOCTYPE wml PUBLIC '-//WAPFORUM//DTD WML 1.1//EN' 'http://www.wapforum.org/DTD/wml_1.1.xml'><wml><head><meta http-equiv='Cache-Control' content='max-age=0'/></head><card id='ecshop' title='ECShop_WAP'><p align='left'>瀵逛笉璧?{$_CFG['shop_name']}鏆傛椂娌℃湁寮€鍚疻AP鍔熻兘</p></card></wml>";
    exit();
}

?>