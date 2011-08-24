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
/* 添加项目 一个季度一个项目 */
elseif ($_REQUEST['act'] == 'add_project' || $_REQUEST['act'] == 'edit_project' )
{
	if($_SESSION['user_rank'] < AUDIT_4){
		show_message("您的权限不够");
	}
	$project_id= !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
	
	$position['ur_here'] .= $_REQUEST['act'] == 'add_project' ? "<li>增加项目</li>" : "<li>修改项目</li>" ; 	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$project_info = array();
	$project_info = get_project_info($project_id);
	if(!$project_info){
		$project_info['project_name'] = "";
		$project_info['project_note'] = "";
		$project_info['start_time'] = "";
		$project_info['duration_time'] = "";
	}
	
	//print_r($project_info);
	
	$smarty->assign('project_info',    $project_info);
	
	$smarty->display('project_view.dwt');	
}
/* 更新修改项目 */
elseif ($_REQUEST['act'] == 'update_project' )
{
	if($_SESSION['user_rank'] < AUDIT_4){
		show_message("您的权限不够");
	}
	include_once(ROOT_PATH . 'includes/upload_file.php');
	
	$project_id= !empty($_POST['project_id']) ? intval($_POST['project_id']) : 0;
	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$excel_path_array = array();
	//创建目录
	$dir = "data/excel/project";
	/* 如果目标目录不存在，则创建它 */
    if (!file_exists($dir))
    {
        if (!make_dir($dir))
        {
            /* 创建目录失败 */
            $this->error_msg = sprintf($GLOBALS['_LANG']['directory_readonly'], $dir);
            $this->error_no  = ERR_DIRECTORY_READONLY;

            return false;
        }
    }

	//设置允许用户上传的文件类型。
	$type = array('gif', 'jpg', 'png', 'xls', 'zip', 'rar');
	//实例化上传类，第一个参数为用户上传的文件组、第二个参数为存储路径、
	//第三个参数为文件最大大小。如果不填则默认为2M
	//第四个参数为充许用户上传的类型数组。如果不填则默认为gif, jpg, png, zip, rar, txt, doc, pdf

	$upload = new UploadFile($_FILES['user_upload_file'], $dir, 1000000, $type);
	//上传用户文件，返回int值，为上传成功的文件个数。
	$num = $upload->upload();
	if ($num != 0) {
		//取得文件的有关信息，文件名、类型、大小、路径。用print_r()打印出来。
		$save_Info = $upload->getSaveInfo();
		foreach($save_Info AS $val)
		{
			ini_set("display_errors",1);

			array_push($excel_path_array,$val['path']);
		}
	}
	
	$new_project_id = $GLOBALS['db']->getOne('SELECT MAX(project_id)+1 AS project_id FROM ' .$GLOBALS['ecs']->table('project'));
	$project_info = array();

	$project_info['project_id'] = $project_id ? $project_id : $new_project_id;
	$project_info['project_name'] = trim($_POST['project_name']);
	$project_info['project_note'] = trim($_POST['project_note']);
	$project_info['start_time']   = trim($_POST['start_time']);
	$project_info['duration_time']= trim($_POST['duration_time']);
	
	
	/*
	if($project_id){
	
		$new_project_id = $GLOBALS['db']->getOne('SELECT MAX(project_id)+1 AS project_id FROM ' .$GLOBALS['ecs']->table('project'));
		$project_info = array();
	
		$project_info['project_id'] = $project_id ? $project_id : $new_project_id;
		$project_info['project_name'] = trim($_POST['project_name']);
		$project_info['project_note'] = trim($_POST['project_note']);
		$project_info['start_time']   = trim($_POST['start_time']);
		$project_info['duration_time']= trim($_POST['duration_time']);
		$project_info['6A_excel'] = $excel_path_array[0];
		$project_info['6B_excel'] = $excel_path_array[1];
		
	
	}else{
		$project_info['project_name'] = trim($_POST['project_name']);
		$project_info['project_note'] = trim($_POST['project_note']);
		$project_info['start_time']   = trim($_POST['start_time']);
		$project_info['duration_time']= trim($_POST['duration_time']);
		$project_info['6A_excel'] = $_POST['6A_excel'];
		$project_info['6B_excel'] = $_POST['6B_excel'];
		
		
	}

	*/	
	
	if($project_id){
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('project'), $project_info, 'update', "project_id='$project_id'");
		
	}else{
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('project'), $project_info, 'INSERT');
		
	}
	
	//print_r($project_info);
	show_message("操作成功", $_LANG['profile_lnk'], 'city_project.php', 'info', true);       
	
}
/**
 * 显示某一个项目下的城市列表
 */
elseif ($_REQUEST['act'] == 'list_city_to_select' )
{
	$project_id= !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
	$smarty->assign('project_id',    $project_id);	
	
	$position['ur_here'] .= "<li>选择城市加入本期项目</li>"; 	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$children = get_city_children($user_region);
	$smarty->assign('full_page',    1);	
	
    $city_list = get_project_city($children);
	$smarty->assign('city_list',    $city_list['citys']);	
    $smarty->assign('filter',       $city_list['filter']);
	$smarty->assign('record_count', $city_list['record_count']);
    $smarty->assign('page_count',   $city_list['page_count']);
    $smarty->assign('page_size',   $city_list['page_size']);
    $smarty->assign('sql',   $city_list['sql']);
    $smarty->assign('count_sql',   $city_list['count_sql']);

	$smarty->display('project_list.dwt');	
	
}
/**
 * query 页面内刷新 显示某一个项目下的城市列表
 */
elseif ($_REQUEST['act'] == 'query_list_city_to_select')
{
	$smarty->assign('full_page',        '0');  // 当前位置
    $children = get_city_children($user_region);

	$project_id= !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
	$smarty->assign('project_id',    $project_id);	
	
	
	$city_list = get_project_city($children);
	$smarty->assign('city_list',    $city_list);
	
	$smarty->assign('city_list',    $city_list['citys']);	
    $smarty->assign('filter',       $city_list['filter']);
	$smarty->assign('record_count', $city_list['record_count']);
    $smarty->assign('page_count',   $city_list['page_count']);
    $smarty->assign('page_size',   $city_list['page_size']);

	$smarty->assign('sql',   	$city_list['sql']);
	$smarty->assign('count_sql',   $city_list['count_sql']);
    	
    make_json_result($smarty->fetch('project_list.dwt'), '', array('filter' => $city_list['filter'], 'page_count' => $city_list['page_count'],'record_count' => $city_list['record_count'],'page_size' => $city_list['page_size']));	
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
	
	$audit_note = $GLOBALS['db']->getOne('SELECT audit_note FROM ' .$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = $ad_id AND feedback_audit > 0 ORDER BY record_id DESC limit 1");
	
	$smarty->assign('audit_note', $audit_note);

	if($ad_info['audit_status'] > 1){
		if($ad_info['is_audit_confirm'] == 1){
			if($ad_info['audit_status'] < AUDIT_5){
				$upload_message = "因为还没有完全过审核 所以目前不能更新.<a href='city_operate.php?act=view_ad&ad_id=$ad_id'>点此查看</a>";
			}else{
				$upload_message = "广告牌已经完全通过审核,可以填写和修改黄色底图的项目.";
			}
		}
	}
	
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
	
	$smarty->display('project_view.dwt');	
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
	$project_id = !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : '';
	$form_audit = !empty($_REQUEST['form_audit']) ? intval($_REQUEST['form_audit']) : 0;
	$col = $_REQUEST['col'];

	for($i=0;$i<count($col);$i++)
	{	
		$i_plus = $i +1;
		$city_content['col_'.$i_plus] = $col[$i];
	}
	$update_time_q = "update_time_q".$project_id;
	$can_modify_q = "can_modify_q".$project_id;
	$city_content[$update_time_q] = gmtime();
	$city_content[$can_modify_q] = 0;
	
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
	
	
	show_message("修改成功", "返回列表页", 'city_project.php?act=list_city_to_select&project_id='.$project_id, 'info', true);       
}
/* 上传反馈照片 驱动页面 */
elseif($_REQUEST['act'] == 'upload_photo')
{
	$position['ur_here'] .= "<li>反馈画画效果</li>"; 	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	$project_id = isset($_REQUEST['project_id']) && intval($_REQUEST['project_id']) > 0 ? intval($_REQUEST['project_id']) : 0;
	$is_change = isset($_REQUEST['is_change']) && intval($_REQUEST['is_change']) > 0 ? intval($_REQUEST['is_change']) : 0;
	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);
	
	$smarty->assign('is_change', $is_change);
	$smarty->assign('project_id', $project_id);
	$project_info = get_project_info($project_id);
	$smarty->assign('project_info', $project_info);
	
	$audit_path = get_audit_path($ad_id,$lite_audit_level_array,$project_id); //审核路径图
	$smarty->assign('audit_path', $audit_path);
	
	$ad_detail = get_city_info($ad_id);
	$smarty->assign('ad_detail', $ad_detail);
	if(!isset($ad_detail['col_20'])){
		show_message("请确认已经填写渠道税金");
	}
	//已经审核完成的照片
	$old_photo_info = get_project_ad_photo_info($ad_id,$project_id); //no feedback
	$smarty->assign('old_photo_info', $old_photo_info);
	
	//正在审核的照片
	$photo_info = get_ad_photo_info($ad_id,$project_id); //feedback
	$smarty->assign('photo_info', $photo_info);
	
	$audit_note = get_last_audit_note($ad_id,$project_id);
	$feedback_confirm = $audit_note == "审核通过" ? 1 : 0;
	$smarty->assign('feedback_confirm', $feedback_confirm);
	
	$smarty->assign('reupload_message', "重新上传将替换照片");
	
	$smarty->assign('ad_id', $ad_id);
	
	$base_info = get_base_info($ad_info['city_id']);
	$city_name = $base_info['region_name'];
	$smarty->assign('city_name',   $city_name);
	
	$smarty->display('project_view.dwt');
}
/* 上传反馈照片 响应页面*/
elseif($_REQUEST['act'] == "act_upload_photo")
{
	$ad_id  = empty($_REQUEST['ad_id']) ? 0 : $_REQUEST['ad_id'] ;
	$project_id  = empty($_REQUEST['project_id']) ? 0 : $_REQUEST['project_id'] ;
	$ad_info = get_ad_info($ad_id);
	$photo  = $_FILES['idea_photo'];
	$desc  = $_REQUEST['idea_desc'];
	$img_id  = $_POST['img_id'];

	$sort_array = array();
	$desc_array = array();
	$id_array = array();
	
	$modify  = empty($_REQUEST['modify']) ? 0 : $_REQUEST['modify'] ;
	//数量和大小判断
	foreach($photo['size'] AS $key => $val){
		if($val == 0 && $modify == 0 ){
			show_message("图片数量不全");
		}else{
			if($val > 1024000  && $modify == 0 ){
				$key_1 = $key + 1;
				show_message("第".$key_1."张图片尺寸大于1MB");
			}else{
				//操作写入数据库
				$desc_array[$key] = $desc[$key];
				$sort_array[$key] = $key;
				$id_array[$key] = $img_id[$key];
			}
		}
	}

	$feedback = $project_id;
	handle_ad_gallery_image($ad_info['city_id'],$ad_id, $photo, $desc_array, $sort_array,$id_array,$feedback); //feedback
	
	//上传图片也不可以修改
	$update_time_q = "update_time_q".$project_id;
	$can_modify_q = "can_modify_q".$project_id;
	$city_content[$update_time_q] = gmtime();
	$city_content[$can_modify_q] = 0;
	
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $city_content, 'update', "ad_id='$ad_id'");
	
	print_r($city_content);
	//算是完整上传
	//$sql = "UPDATE " . $GLOBALS['ecs']->table('city_ad') . " SET is_upload = '1'  WHERE ad_id = '$ad_id'";
    //$GLOBALS['db']->query($sql);
	show_message("恭喜您,照片上传成功。", $_LANG['back_home_lnk'], "city_project.php?act=upload_photo&project_id=$project_id&ad_id=$ad_info[ad_id]", 'info', true);
}
/* 响应合同项目的审核 */
elseif($_REQUEST['act'] == 'update_audit')
{
	$ad_id =  !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : 0;
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
	$confirm = !empty($_REQUEST['confirm']) ? intval($_REQUEST['confirm']) : 0;
	
	
	/* 写入审核历史数据库*/
	$audit_info = array();
	$audit_info['ad_id'] = $ad_id;
	$audit_info['user_id'] = $_SESSION['user_id'];
	$audit_info['user_rank'] = $_SESSION['user_rank'];
	$audit_info['feedback_audit'] = $project_id;
	$audit_info['audit_note'] = $confirm > 0 ? "审核通过": trim($_POST['audit_note']);
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad_audit'), $audit_info, 'INSERT');
	
	$return_url = "city_project.php?act=list_city_to_select&project_id=$project_id";
	
	if($confirm == 1){
		show_message("审核通过,其他人会看到。", $_LANG['back_home_lnk'], $return_url, 'info', true);
		
	}else{		
		//打开修改权限
		$can_modify_q = "can_modify_q".$project_id;
		$city_content[$can_modify_q] = 2;	
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $city_content, 'update', "ad_id='$ad_id'");
		
		show_message("审核信息已经提交。", $_LANG['back_home_lnk'], $return_url, 'info', true);
		//$smarty->display('city_view.dwt');
	}
}



//============================
//	下面是和广告大图有关的操作
//	
//	
//============================

elseif ($_REQUEST['act'] == 'project_picture' )
{
	$position['ur_here'] .= "<li>项目画面广告</li>"; 
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

	$children = get_city_children_a($user_region);

	$project_list = get_project_list($children);
	$smarty->assign('project_list',    $project_list);
	
	$smarty->display('city_picture_list.dwt');
}
/* 显示项目下面的 广告列表 */
elseif ($_REQUEST['act'] == 'list_picture' )
{
    /* 如果页面没有被缓存则重新获得页面的内容 */
	if($_SESSION['user_rank'] != AUDIT_2){
		show_message("您的权限不够,应该是电通的权限");
	}
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
	$project_info = get_project_info($project_id);
	$smarty->assign('project_info',    $project_info);
	
	$position['ur_here'] .= "<li>$project_info[project_name]</li><li>广告列表</li>"; 
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

	$picture_list = get_picture_list($project_id);
	$smarty->assign('picture_list',    $picture_list);
	
	$smarty->display('city_picture_list.dwt');	
}
/* 增加 编辑 查看 照片 */
elseif ($_REQUEST['act'] == 'add_picture' || $_REQUEST['act'] == 'edit_picture' || $_REQUEST['act'] == 'view_picture' )
{
	$picture_id= !empty($_REQUEST['picture_id']) ? intval($_REQUEST['picture_id']) : 0;
	$project_id= !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
	
	$position['ur_here'] .= $_REQUEST['act'] == 'add_picture' ? "<li>增加广告</li>" : "<li>修改/查看广告</li>" ; 	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$picture_info = array();
	$picture_info = get_picture_info($picture_id);
	if(!$picture_info){
		$picture_info['pic_name'] = "";
		$picture_info['project_id'] = $project_id;
		$picture_info['pic_note'] = "";
		$picture_info['pic_thumb'] = "";
		$picture_info['pic_type'] = "";
		$picture_info['file_url'] = "";
	}
	
	//print_r($picture_info);
	
	$smarty->assign('picture_info',    $picture_info);
	
	$smarty->display('city_picture_list.dwt');	
}
/* 修改更新广告大图 */
elseif ($_REQUEST['act'] == 'update_picture' )
{
	include_once(ROOT_PATH . 'includes/upload_file.php');
	
	$picture_id= !empty($_POST['picture_id']) ? intval($_POST['picture_id']) : 0;
	
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	
	$excel_path_array = array();
	//创建目录
	$dir = "data/picture/$picture_id";
	/* 如果目标目录不存在，则创建它 */
    if (!file_exists($dir))
    {
        if (!make_dir($dir))
        {
            /* 创建目录失败 */
            $this->error_msg = sprintf($GLOBALS['_LANG']['directory_readonly'], $dir);
            $this->error_no  = ERR_DIRECTORY_READONLY;

            return false;
        }
    }

	//设置允许用户上传的文件类型。
	$type = array('gif', 'jpg', 'png', 'xls', 'zip', 'rar');
	//实例化上传类，第一个参数为用户上传的文件组、第二个参数为存储路径、
	//第三个参数为文件最大大小。如果不填则默认为2M
	//第四个参数为充许用户上传的类型数组。如果不填则默认为gif, jpg, png, zip, rar, txt, doc, pdf

	$upload = new UploadFile($_FILES['user_upload_file'], $dir, 1000000, $type);
	//上传用户文件，返回int值，为上传成功的文件个数。
	$num = $upload->upload();
	if ($num != 0) {
		//取得文件的有关信息，文件名、类型、大小、路径。用print_r()打印出来。
		$save_Info = $upload->getSaveInfo();
		foreach($save_Info AS $val)
		{
			ini_set("display_errors",1);

			array_push($excel_path_array,$val['path']);
		}
	}
	
	$new_picture_id = $GLOBALS['db']->getOne('SELECT MAX(picture_id)+1 AS picture_id FROM ' .$GLOBALS['ecs']->table('project_picture'));
	$picture_info = array();
	
	$picture_info['picture_id'] = $picture_id ? $picture_id : $new_picture_id;
	$picture_info['project_id'] = trim($_POST['project_id']);
	$picture_info['pic_name'] 	= trim($_POST['pic_name']);
	$picture_info['pic_note'] 	= trim($_POST['pic_note']);
	$picture_info['pic_type'] 	= intval(trim($_POST['pic_type']));
	$picture_info['file_url']	= trim($_POST['file_url']);
	$picture_info['pic_thumb'] 	= !empty($excel_path_array[0]) ? $excel_path_array[0] : $_POST['pic_thumb'] ;
	$picture_info['upload_time']= empty($_POST['upload_time']) ? gmtime(): $_POST['upload_time'] ;
	
	/**/
	
	if($picture_id){
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('project_picture'), $picture_info, 'update', "picture_id='$picture_id'");
		
	}else{
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('project_picture'), $picture_info, 'INSERT');
		
	}
	
	//print_r($picture_info);
	show_message("操作成功", $_LANG['profile_lnk'], "city_project.php?act=list_picture&project_id=$picture_info[project_id]", 'info', true);
}
/* 删除大图 */

elseif($_REQUEST['act'] == 'delete_picture'){
	$picture_id= !empty($_REQUEST['picture_id']) ? intval($_REQUEST['picture_id']) : 0;
	if($picture_id){
		echo $picture_id;
		$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('project_picture') . " WHERE picture_id = $picture_id LIMIT 1");
		show_message("删除成功", $_LANG['back_home_lnk'], "city_project.php?act=project_picture", 'info', true);
	}	
}

/* 开启修改权限 */
elseif($_REQUEST['act'] == 'open_modify'){
	$ad_id= !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : 0;
	$project_id = isset($_REQUEST['project_id']) && intval($_REQUEST['project_id']) > 0 ? intval($_REQUEST['project_id']) : 0;
	
	$can_modify_q = "can_modify_q".$project_id;
	if($ad_id){
		$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET $can_modify_q = '1'  WHERE ad_id = '$ad_id'";
	    $GLOBALS['db']->query($sql);
		show_message("开启成功");
	}	
}

elseif ($_REQUEST['act'] == 'base_info' )
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
elseif ($_REQUEST['act'] == 'query_base_info')
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


?>