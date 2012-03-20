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



if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$all_city_content = array();
$smarty->assign('city_title', $_LANG['city_title']);
$smarty->assign('audit_title', $_LANG['AUDIT']);
$smarty->assign('CONTENT_COLS', CONTENT_COLS);

//用户信息和权限
$your_user_rank = get_rank_info();
$smarty->assign('your_user_rank', $your_user_rank['rank_name']);
$audit_level_array = array("2","3","4","5"); //审核资料表
$smarty->assign('audit_level_array', $audit_level_array);  // 当前位置


//用户权限下的已经上传的城市列表
$user_region = get_user_region();
$user_permission = get_user_permission($user_region);
$smarty->assign('user_permission',        $user_permission);  // 当前位置

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 获得当前页码 */
$page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;


/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
	$url = "user.php?act=login";
	ecs_header('Location: ' .$url. "\n");
	exit;
}
if (!isset($_REQUEST['act']))
{
    $act = "list_dealer";
	$smarty->assign('act_step',      "list_dealer");
}else{
	$smarty->assign('act_step',       $_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- PROCESSOR *done -padding
//*	list_dealer	项目列表
//*	add_dealer edit_dealer 增加修改
//* update_dealer	修改项目
//--	execute_dealer 执行excel 执行状态
//
//	list_city_to_select 查看城市的列表 并选择
//	list_request(user_rank,)  act=list_dealer
//	add_request_city delete_request_city 选购或者不选择 AJAX 页面内切换
//  update_request_price 2个price
/*------------------------------------------------------ */

$position['title'] = "项目管理";
$position['ur_here'] = '<li><a href="city_dealer.php">渠道管理</a></li>'; 
 

/* 获得页面的缓存ID */
$cache_id = sprintf('%X', crc32($user_id . '-' . $page . '-' . $_CFG['lang']));

if (!$smarty->is_cached('city_dealer.dwt', $cache_id) && $act == 'list_dealer')
{
    /* 如果页面没有被缓存则重新获得页面的内容 */
	
	$position['ur_here'] .= "<li>项目列表</li>"; 
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

	$smarty->assign('full_page',        '1');  // 当前位置

	$dealer_list = get_dealer_list();	

	$smarty->assign('dealer_list',    $dealer_list['dealers']);	
    $smarty->assign('filter',       $dealer_list['filter']);
	$smarty->assign('record_count', $dealer_list['record_count']);
    $smarty->assign('page_count',   $dealer_list['page_count']);
    $smarty->assign('page_size',   $dealer_list['page_size']);
    $smarty->assign('sql',   $dealer_list['sql']);
    $smarty->assign('count_sql',   $dealer_list['count_sql']);
	
	$smarty->display('city_dealer.dwt');
}
/* 响应页面内刷新 query_show*/
elseif ($_REQUEST['act'] == 'query_dealer')
{
	$province = isset($_REQUEST['province'])   && intval($_REQUEST['province'])  > 0 ? intval($_REQUEST['province'])  : 0;   
	$smarty->assign('full_page',        '0');  // 当前位置

	
	$dealer_list = get_dealer_list();
	
	$smarty->assign('dealer_list',  $dealer_list['dealers']);	
    $smarty->assign('filter',       $dealer_list['filter']);
	$smarty->assign('record_count', $dealer_list['record_count']);
    $smarty->assign('page_count',   $dealer_list['page_count']);
    $smarty->assign('page_size',   	$dealer_list['page_size']);

	$smarty->assign('sql',   		$dealer_list['sql']);
	$smarty->assign('count_sql',   	$dealer_list['count_sql']);
    
    $order_id = isset($_REQUEST['order_id'])   && intval($_REQUEST['order_id'])  > 0 ? intval($_REQUEST['order_id'])  : 0;
//	$smarty->assign('order_id',       $order_id);
	
    make_json_result($smarty->fetch('city_dealer.dwt'), '', array('filter' => $dealer_list['filter'], 'order_id' => $order_id , 'page_count' => $dealer_list['page_count'],'record_count' => $dealer_list['record_count'],'page_size' => $dealer_list['page_size']));
	
}
elseif($_REQUEST['act'] == 'check'){

	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$sql_1 = "SELECT a.col_43,a.col_44,a.col_45,a.col_46,a.col_1, ad.ad_id ".
			" FROM ".$GLOBALS['ecs']->table('city') . " AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = a.ad_id ". 
			" WHERE ad.audit_status = 5 AND ad.is_upload = 1 AND ad.is_audit_confirm = 1 ";
			
	$res = $GLOBALS['db']->getAll($sql_1);
	
	foreach($res AS $val){
		// $sql = "SELECT dealer_id  FROM " . $GLOBALS['ecs']->table('city_dealer') . 
		//     " WHERE dealer_sn LIKE '$val[col_43]' AND dealer_name LIKE '$val[col_44]' ";
		// 	    $res =  $GLOBALS['db']->getOne($sql);
		insert_dealer($val['col_43'],$val['col_44'],$val['col_1'],$val['ad_id']);
		insert_dealer($val['col_45'],$val['col_46'],$val['col_1'],$val['ad_id']);
		
		// if( ){
		// 	echo "Update".$res . "-".$val['col_43']."<br>";			
		// }else{
		// 	echo "insert数据库里没有<br>";
		// }
		// 
		
	}
	
}





?>