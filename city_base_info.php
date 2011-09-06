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
    $act = "ad_list";
	$smarty->assign('act_step',      "ad_list");
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

$position['title'] = "基础信息修改";
$position['ur_here'] = '<li><a href="city_base_info.php?act=ad_list&project_id=9">基础信息修改</a></li>'; 
 

/* 获得页面的缓存ID */
$cache_id = sprintf('%X', crc32($user_id . '-' . $page . '-' . $_CFG['lang']));

/* 项目列表 所有季度 */
if ($_REQUEST['act'] == 'ad_list')
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
	
	
	//已经审核完成的照片
	$old_photo_info = get_ad_photo_info($ad_id); //no feedback
	$smarty->assign('old_photo_info', $old_photo_info);
	
	//正在审核的照片
	$photo_info = get_ad_photo_info($ad_id,$project_id); //feedback
	$smarty->assign('photo_info', $photo_info);
	
	$ad_detail = get_city_info($ad_id);
	$ad_detail['base_info_modify'] = $GLOBALS['db']->getOne('SELECT base_info_modify FROM ' .$GLOBALS['ecs']->table('city_ad')." WHERE ad_id = $ad_id limit 1");
	$smarty->assign('ad_detail', $ad_detail);
	
	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);
	
	$audit_status = get_audit_status($ad_id,$project_id);
	$smarty->assign('audit_status',   $audit_status);
	
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
	
	$audit_path = get_audit_path($ad_id,$lite_audit_level_array,$project_id); //审核路径图
	$smarty->assign('audit_path', $audit_path);
	
	$base_info = get_base_info($ad_info['city_id']);
	$city_name = $base_info['region_name'];
	$smarty->assign('city_name',   $city_name);
	$smarty->assign('project_id',   $project_id);	
	
	$smarty->display('base_info_view.dwt');	
}

/* 响应更新城市的合同资料 */
elseif($_REQUEST['act'] == 'act_update_ad_info')
{
	$city_content = make_city_content();
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';
	$project_id = isset($_REQUEST['project_id']) && intval($_REQUEST['project_id']) > 0 ? intval($_REQUEST['project_id']) : 0;
	
	$form_audit = !empty($_REQUEST['form_audit']) ? intval($_REQUEST['form_audit']) : 0;
	$col = $_REQUEST['col'];

	for($i=0;$i<count($col);$i++)
	{	
		$i_plus = $i +1;
		$city_content['col_'.$i_plus] = trim($col[$i]);
	}


	//$city_content['base_info_modify'] = 0; 电通来点
	
	$city_content['col_13'] = round($city_content['col_12'] * $city_content['col_11'],1); //面积 = 宽 * 高
	$city_content['col_15'] = $city_content['col_13'] *  intval($city_content['col_14']); //总面积
	$city_content['col_18'] = sep_days( $city_content['col_17'],$city_content['col_16']); //发布天数
	

	
	if($ad_id){
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $city_content, 'update', "ad_id='$ad_id'");
		
		$city_ad_array = array();
		$city_ad_array['base_info_changed'] = 1;//已经修改过
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad'), $city_ad_array, 'update', "ad_id='$ad_id'");
		
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
		
		//分区2次修改 之后
		$audit_note = $GLOBALS['db']->getOne("SELECT audit_note FROM " . $GLOBALS['ecs']->table('city_ad_audit') . " WHERE ad_id = $ad_id AND feedback_audit = 9 ORDER BY record_id DESC LIMIT 1 ");
		if(!empty($audit_note) && $audit_note != "审核通过"){
			$city_ad_array = array();
			$city_ad_array['base_info_modify'] = 0;//已经修改过
			echo $audit_note;
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad'), $city_ad_array, 'update', "ad_id='$ad_id'");
		}
		
	}
	
	
	
	
	show_message("修改成功", "返回基础信息", 'city_base_info.php?act=update_ad_info&project_id='.$project_id.'&ad_id='.$ad_id, 'info', true);       
}


elseif($_REQUEST['act'] == 'send_material')
{
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';
	
	$city_ad_array = array();
	$city_ad_array['is_send'] = 1;//已经修改过
	$city_ad_array['ad_id'] = $ad_id;//已经修改过
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_material'), $city_ad_array, 'INSERT');
	
	show_message("成功寄出 时间:".date("Y-m-d H:i:s"),"关闭该页","javascript:self.close()");       
	
}
elseif($_REQUEST['act'] == 'receive_material')
{
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';
	
	$city_ad_array = array();
	$city_ad_array['is_receive'] = 1;//已经修改过
	$city_ad_array['ad_id'] = $ad_id;//已经修改过
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_material'), $city_ad_array, 'INSERT');
	
	show_message("成功收到 时间:".date("Y-m-d H:i:s"),"关闭该页","javascript:self.close()");       
	
}

elseif($_REQUEST['act'] == 're_send_material')
{
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';

	$is_send_time = $GLOBALS['db']->getOne("SELECT MAX(`is_send`) FROM " . $GLOBALS['ecs']->table('city_material') . " WHERE ad_id = $ad_id ");

	
	$city_ad_array = array();
	$city_ad_array['is_send'] = $is_send_time + 1;//已经修改过
	$city_ad_array['ad_id'] = $ad_id;//已经修改过
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_material'), $city_ad_array, 'INSERT');
	
	show_message("成功第".$city_ad_array['is_send']."次寄出 时间:".date("Y-m-d H:i:s"),"关闭该页","javascript:self.close()");       
	
}
elseif($_REQUEST['act'] == 're_receive_material')
{
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';
	
	$is_receive_time = $GLOBALS['db']->getOne("SELECT MAX(`is_receive`) FROM " . $GLOBALS['ecs']->table('city_material') . " WHERE ad_id = $ad_id ");
	
	$city_ad_array = array();
	$city_ad_array['is_receive'] = $is_receive_time + 1;//已经修改过
	$city_ad_array['ad_id'] = $ad_id;//已经修改过
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_material'), $city_ad_array, 'INSERT');
	
	show_message("成功第".$city_ad_array['is_receive']."次收到 时间:".date("Y-m-d H:i:s"),"关闭该页","javascript:self.close()");       
	
}

/* 上传反馈照片 驱动页面 */
elseif($_REQUEST['act'] == 'base_info_audit')
{
	$position['ur_here'] .= "<li>审核基础信息</li>"; 	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	$project_id = isset($_REQUEST['project_id']) && intval($_REQUEST['project_id']) > 0 ? intval($_REQUEST['project_id']) : 0;
	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);
	
	$smarty->assign('project_id', $project_id);
	$project_info = get_project_info($project_id);
	$smarty->assign('project_info', $project_info);
	
	$audit_path = get_audit_path($ad_id,$lite_audit_level_array,$project_id); //审核路径图
	$smarty->assign('audit_path', $audit_path);
	
	$ad_detail = get_city_info($ad_id);
	$smarty->assign('ad_detail', $ad_detail);
	
	//已经审核完成的照片
	$old_photo_info = get_ad_photo_info($ad_id); //no feedback
	$smarty->assign('old_photo_info', $old_photo_info);
	
	
	$audit_note = get_last_audit_note($ad_id,$project_id);
	$feedback_confirm = $audit_note == "审核通过" ? 1 : 0;
	$smarty->assign('feedback_confirm', $feedback_confirm);
	
	$smarty->assign('reupload_message', "重新上传将替换照片");
	
	$smarty->assign('ad_id', $ad_id);
	
	$base_info = get_base_info($ad_info['city_id']);
	$city_name = $base_info['region_name'];
	$smarty->assign('city_name',   $city_name);
	
	$audit_status = get_audit_status($ad_id,$project_id);
	$smarty->assign('audit_status',   $audit_status);
	
	$smarty->display('base_info_view.dwt');
}

/* 响应合同项目的审核 */
elseif($_REQUEST['act'] == 'update_base_info_audit')
{
	$ad_id =  !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : 0;
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
	$confirm = !empty($_REQUEST['confirm']) ? intval($_REQUEST['confirm']) : 0;
	$recycle = $_POST['recycle'];
	
	
	/* 写入审核历史数据库*/
	$audit_info = array();
	$audit_info['ad_id'] = $ad_id;
	$audit_info['user_id'] = $_SESSION['user_id'];
	$audit_info['user_rank'] = $_SESSION['user_rank'];
	$audit_info['feedback_audit'] = $project_id;
	$audit_info['audit_note'] = $confirm > 0 ? "审核通过": trim($_POST['audit_note']);
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad_audit'), $audit_info, 'INSERT');
	
	$return_url = "city_base_info.php?act=ad_list&project_id=$project_id";
	
	if($confirm == 1){
		$city_content['base_info_modify'] = 0;	
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad'), $city_content, 'update', "ad_id='$ad_id'");
		
		show_message("审核通过,其他人会看到。", $_LANG['back_home_lnk'], $return_url, 'info', true);
		
	}else{
		if($recycle){
			$city_content['base_info_modify'] = 2;	//除了修改还需要重新寄出
		}else{
			$city_content['base_info_modify'] = 1;	
		}
		//打开修改权限
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad'), $city_content, 'update', "ad_id='$ad_id'");
		
		show_message("审核信息已经提交。", $_LANG['back_home_lnk'], $return_url, 'info', true);
		//$smarty->display('city_view.dwt');
	}
}

// 07/01/2011 -> 2011-07-01
function transdate($data){
	$tmp = date( 'Y-m-d ',strtotime($data));//preg_replace( "/\d{2}\/\d{2}\/\d{4}/ ", "2007-09-18 ",$data); 
	//echo $tmp;
	return $tmp;
}

function sep_days($end_date,$start_date)
{
 	$temp = strtotime(transdate($end_date))-strtotime(transdate($start_date));
 	$days = $temp/(60*60*24);
 	return $days+1;
}

?>