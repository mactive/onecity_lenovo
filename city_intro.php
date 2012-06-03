<?php
/**
 * lenovo-one 资料分类 * $Author: mactive $
 * 此文件用来执行一些批量操作
 * $Id: city_sql.php 14481 2011-05-18 11:23:01Z mactive $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_city.php');
require(dirname(__FILE__) . '/includes/lib_dealer.php');
require(dirname(__FILE__) . '/includes/lib_clips.php');
require_once(ROOT_PATH . 'admin/includes/lib_main.php');
require_once(ROOT_PATH . 'admin/includes/cls_exchange.php');
include_once(ROOT_PATH . 'includes/cls_json.php');


$real_name = $GLOBALS['db']->getOne("SELECT real_name FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = $_SESSION[user_id]");
$smarty->assign('real_name', $real_name);
$your_user_rank = get_rank_info();
$smarty->assign('your_user_rank', $your_user_rank['rank_name']);
//用户权限下的已经上传的城市列表
$user_region = get_user_region();
$user_permission = get_user_permission($user_region);
$smarty->assign('user_permission',        $user_permission);  // 当前位置


/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
	$url = "user.php?act=login";
	ecs_header('Location: ' .$url. "\n");
	exit;
}
if (!isset($_REQUEST['act']))
{
	$_REQUEST['act'] = "intro_page";
	$smarty->assign('act_step',      "intro_page");
}else{
	$smarty->assign('act_step',       $_REQUEST['act']);
}
/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 获得当前页码 */
$page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

 
if($_REQUEST['act'] == 'switch_year')
{

	$tmp = !empty($_REQUEST['year']) ? intval($_REQUEST['year']) : 2012;
	$url = !empty($_REQUEST['url']) ? trim($_REQUEST['url']) : 'city_operate.php';
	$url = urldecode($url);
	if($tmp == 2012){
		$_SESSION['year'] = 2011;
		show_message("切换成功", "打开", $url, 'info', true);
	}else{
		$_SESSION['year'] = 2012;
		show_message("切换成功",  "打开",$url, 'info', true);
	}
}
elseif($_REQUEST['act'] == 'intro_page'){
	$smarty->display("city_intro.dwt");
}

?>