<?php
/**
 * SINEMALL 资料分类 * $Author: testyang $
 * $Id: city_project.php 14481 2008-04-18 11:23:01Z testyang $
*/


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_city.php');
require(dirname(__FILE__) . '/includes/lib_clips.php');
require_once(ROOT_PATH . 'admin/includes/lib_main.php');
require_once(ROOT_PATH . 'admin/includes/cls_exchange.php');
include_once(ROOT_PATH . 'includes/cls_json.php');
require_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);



if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$all_city_content = array();
$smarty->assign('city_title', $_LANG['city_title']);
$smarty->assign('audit_title', $_LANG['AUDIT']);
$smarty->assign('CONTENT_COLS', CONTENT_COLS);




$real_name = $GLOBALS['db']->getOne("SELECT real_name FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = $_SESSION[user_id]");
$smarty->assign('real_name', $real_name);
$your_user_rank = get_rank_info();
$smarty->assign('your_user_rank', $your_user_rank['rank_name']);
$audit_level_array = array("2","3","4","5");
$smarty->assign('audit_level_array', $audit_level_array);  // 当前位置

$lite_audit_level_array = array("2");


//用户权限下的已经上传的城市列表
$user_region = get_user_region();
$user_permission = get_user_permission($user_region);
$smarty->assign('user_permission',        $user_permission);  // 当前位置

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 获得当前页码 */
$page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$type = !empty($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
$keywords = !empty($_REQUEST['keywords']) ? trim($_REQUEST['keywords']) : '';


/* 获得指定的分类ID */
if (!empty($_GET['id']))
{
    $city_id = intval($_GET['id']);
}
elseif(!empty($_GET['category']))
{
    $city_id = intval($_GET['category']);
}

/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
	$url = "user.php?act=login";
	ecs_header('Location: ' .$url. "\n");
	exit;
}
if (!isset($_REQUEST['act']))
{
    $act = "list_project";
	$smarty->assign('act_step',      "list_project");
}else{
	$smarty->assign('act_step',       $_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- PROCESSOR *done -padding
//*	list_project	项目列表
//*	add_project edit_project 增加修改
//* update_project	修改项目
//--	execute_project 执行excel 执行状态
//
//	list_city_to_select 查看城市的列表 并选择
//	list_request(user_rank,)  act=list_project
//	add_request_city delete_request_city 选购或者不选择 AJAX 页面内切换
//  update_request_price 2个price
/*------------------------------------------------------ */

$position['title'] = "项目管理";
$position['ur_here'] = '<li><a href="city_project.php">项目管理</a></li>'; 
 

/* 获得页面的缓存ID */
$cache_id = sprintf('%X', crc32($user_id . '-' . $page . '-' . $_CFG['lang']));

/* 项目列表 所有季度 */
if (!$smarty->is_cached('project_list.dwt', $cache_id) && $act == 'list_project')
{
    /* 如果页面没有被缓存则重新获得页面的内容 */
	
	$position['ur_here'] .= "<li>项目列表</li>"; 
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

	$children = get_city_children_a($user_region);

	$project_list = get_project_list($children);
	$smarty->assign('project_list',    $project_list);
	
	$smarty->display('project_view.dwt');	
}
elseif ($_REQUEST['act'] == 'ad_list' )
{
	$project_id= !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
	$smarty->assign('project_id',    $project_id);	
	
	$position['ur_here'] .= "<li>选择城市加入本期项目</li>"; 	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$children = get_city_children($user_region);
	$smarty->assign('full_page',    1);	
	
    $city_list = get_base_info_list($children);
	$smarty->assign('city_list',    $city_list['citys']);	
    $smarty->assign('filter',       $city_list['filter']);
	$smarty->assign('record_count', $city_list['record_count']);
    $smarty->assign('page_count',   $city_list['page_count']);
    $smarty->assign('page_size',   $city_list['page_size']);
    $smarty->assign('sql',   $city_list['sql']);
    $smarty->assign('count_sql',   $city_list['count_sql']);

	$smarty->display('base_info.dwt');	
	
}
/**
 * query 页面内刷新 显示某一个项目下的城市列表
 */
elseif ($_REQUEST['act'] == 'query_ad_list')
{
	$smarty->assign('full_page',        '0');  // 当前位置
    $children = get_city_children($user_region);

	$project_id= !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
	$smarty->assign('project_id',    $project_id);	
	
	
	$city_list = get_base_info_list($children);
	$smarty->assign('city_list',    $city_list);
	
	$smarty->assign('city_list',    $city_list['citys']);	
    $smarty->assign('filter',       $city_list['filter']);
	$smarty->assign('record_count', $city_list['record_count']);
    $smarty->assign('page_count',   $city_list['page_count']);
    $smarty->assign('page_size',   $city_list['page_size']);

	$smarty->assign('sql',   	$city_list['sql']);
	$smarty->assign('count_sql',   $city_list['count_sql']);
    	
    make_json_result($smarty->fetch('base_info.dwt'), '', array('filter' => $city_list['filter'], 'page_count' => $city_list['page_count'],'record_count' => $city_list['record_count'],'page_size' => $city_list['page_size']));	
}

/**
 * 更新城市的合同资料
 */
elseif ($_REQUEST['act'] == 'update_ad_info')
{
	$position['ur_here'] .= "<li>填写签约信息</li>"; 	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	$project_id = isset($_REQUEST['project_id']) && intval($_REQUEST['project_id']) > 0 ? intval($_REQUEST['project_id']) : 0;
	
	$ad_detail = get_city_info($ad_id);
	$smarty->assign('ad_detail', $ad_detail);
	
	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);
	
	$audit_note = $GLOBALS['db']->getOne('SELECT audit_note FROM ' .$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = $ad_id AND feedback_audit = 9 ORDER BY record_id DESC limit 1");
	
	$smarty->assign('audit_note', $audit_note);

	
	if($ad_detail['col_43']){
		if($audit_note != "审核通过" && !empty($audit_note) ){
			$upload_message = "审核不通过,需要修改.";			
		}else{
			$can_modify_q = "can_modify_q".$project_id;
			if($ad_detail[$can_modify_q] == 1 ){
				$upload_message = "已经填写过,重新开启修改.";
			}else{
				$upload_message = "已经最终填写过不可以再修改.若想修改需要中央专员开启.";
			}
		}
	}
	
	$smarty->assign('upload_message', $upload_message);
	

	$photo_info = get_ad_photo_info($ad_id);
	$smarty->assign('photo_info', $photo_info);
	
	$audit_path = get_audit_path($ad_id,$audit_level_array); //审核路径图
	$smarty->assign('audit_path', $audit_path);
	
	$base_info = get_base_info($ad_info['city_id']);
	$city_name = $base_info['region_name'];
	$smarty->assign('city_name',   $city_name);
	$smarty->assign('project_id',   $project_id);
	
	$smarty->display('base_info_view.dwt');	
}
/* 更新城市的合同资料 
elseif ($_REQUEST['act'] == 'edit_update_ad_info')
{
	$position['ur_here'] .= "<li>填写签约信息</li>"; 	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	$project_id = isset($_REQUEST['project_id']) && intval($_REQUEST['project_id']) > 0 ? intval($_REQUEST['project_id']) : 0;
	
	$ad_detail = get_city_info($ad_id);
	$smarty->assign('ad_detail', $ad_detail);
	
	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);
	
	$audit_note = $GLOBALS['db']->getOne('SELECT audit_note FROM ' .$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = $ad_id AND feedback_audit > 0 ORDER BY record_id DESC limit 1");
	
	$smarty->assign('audit_note', $audit_note);

	if($ad_info['audit_status'] > 1){
		if($ad_info['is_audit_confirm'] == 1){
			if($ad_info['audit_status'] < AUDIT_5){
				$upload_message = "因为还没有完全过审核 所以目前不能更新.<a href='city_operate.php?act=view_ad&ad_id=$ad_id'>点此查看</a>";
			}else{
				$upload_message = "广告牌已经完全通过审核,可以填写和修改换色底图的项目.";
			}
		}
	}
	
	if($ad_detail['col_43']){
		if($audit_note != "审核通过" && !empty($audit_note) ){
			$upload_message = "审核不通过,需要修改.";			
		}else{
			$upload_message = "已经最终填写过不可以再修改.";
		}
	}
	
	$smarty->assign('upload_message', $upload_message);
	

	$photo_info = get_ad_photo_info($ad_id);
	$smarty->assign('photo_info', $photo_info);
	
	$audit_path = get_audit_path($ad_id,$audit_level_array); //审核路径图
	$smarty->assign('audit_path', $audit_path);
	
	$smarty->display('project_view.dwt');	
}
*/
/* 响应更新城市的合同资料 */
elseif($_REQUEST['act'] == 'act_update_ad_info')
{
	$city_content = make_city_content();
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';
	$form_audit = !empty($_REQUEST['form_audit']) ? intval($_REQUEST['form_audit']) : 0;
	$col = $_REQUEST['col'];

	for($i=0;$i<count($col);$i++)
	{	
		$i_plus = $i +1;
		$city_content['col_'.$i_plus] = trim($col[$i]);
	}


	//$city_content['base_info_modify'] = 0; 电通来点
	$width_array = array();
	$width_array = explode("+",$city_content['col_12'],2);
	$width_add = $width_array[0] + $width_array[1];
	$city_content['col_13'] = round($width_add * $city_content['col_11']); //面积 = 宽 * 高
	$city_content['col_15'] = intval($city_content['col_13']) *  intval($city_content['col_14']); //总面积
	$city_content['col_18'] = sep_days( $city_content['col_17'],$city_content['col_16']); //发布天数
	
	if($ad_id){
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $city_content, 'update', "ad_id='$ad_id'");
		
		//记录修改记录
		$old_col = $_REQUEST['old_col'];
		
		foreach($old_col AS $key => $val){
			if($val != $col[$key] && !empty($val) && !empty($col[$key]) ){
				//echo $val."-".$col[$key]."<br>";
				
				$log = array();
				$log['ad_id'] 	= $ad_id;
				$log['user_id'] = $_SESSION['user_id'];
				$name = $key + 1;
				$log['col_name']= "col_".$name;
				$log['value'] 	= $col[$key];
				$log['old_value'] = $val;
				$log['time'] 	= gmtime();
				//print_r($log);
				$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad_log'), $log, 'INSERT');	
			}
		}
		
	}
	
	
	show_message("修改成功", "返回基础信息", 'city_base_info.php?act=update_ad_info&ad_id='.$ad_id, 'info', true);       
}

function sep_days($end_date,$start_date)
{
 	$temp = strtotime($end_date)-strtotime($start_date);
 	$days = $temp/(60*60*24);
 	return $days+1;
}

?>