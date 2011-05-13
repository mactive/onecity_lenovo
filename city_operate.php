<?php
/**
 * SINEMALL 资料分类 * $Author: testyang $
 * $Id: city_operate.php 14481 2008-04-18 11:23:01Z testyang $
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
    $act = "show";
	$smarty->assign('act_step',      "show");
}else{
	$smarty->assign('act_step',       $_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 获得页面的缓存ID */
$cache_id = sprintf('%X', crc32($_SESSION['user_id'] . '-' . $page . '-' . $_CFG['lang']));
if (!$smarty->is_cached('city_operate.dwt', $cache_id) && $act == 'show')
{
    /* 如果页面没有被缓存则重新获得页面的内容 */
    $children = get_city_children($user_region);


	    
	$smarty->assign('full_page',        '1');  // 当前位置

    assign_template('a', array($city_id));
    $position = assign_ur_here($city_id);
    $smarty->assign('page_title',           $position['title']);     // 页面标题
    $smarty->assign('ur_here',              $position['ur_here']);   // 当前位置

	$city_list = get_city_list($children);
	$smarty->assign('city_list',    $city_list['citys']);	
    $smarty->assign('filter',       $city_list['filter']);
	$smarty->assign('record_count', $city_list['record_count']);
    $smarty->assign('page_count',   $city_list['page_count']);
    $smarty->assign('page_size',   $city_list['page_size']);
    $smarty->assign('sql',   $city_list['sql']);
    $smarty->assign('count_sql',   $city_list['count_sql']);
	
	$smarty->display('city_operate.dwt');	
}
/* query_show*/
elseif ($_REQUEST['act'] == 'query_show')
{
	$province = isset($_REQUEST['province'])   && intval($_REQUEST['province'])  > 0 ? intval($_REQUEST['province'])  : 0;   
	$smarty->assign('full_page',        '0');  // 当前位置

    $children = get_city_children($user_region);
	
	//print_r($res);
	
	$city_list = get_city_list($children);
	$smarty->assign('city_list',    $city_list);
	
	$smarty->assign('city_list',    $city_list['citys']);	
    $smarty->assign('filter',       $city_list['filter']);
	$smarty->assign('record_count', $city_list['record_count']);
    $smarty->assign('page_count',   $city_list['page_count']);
    $smarty->assign('page_size',   $city_list['page_size']);

	$smarty->assign('sql',   	$city_list['sql']);
	$smarty->assign('count_sql',   $city_list['count_sql']);
    
    $order_id = isset($_REQUEST['order_id'])   && intval($_REQUEST['order_id'])  > 0 ? intval($_REQUEST['order_id'])  : 0;
//	$smarty->assign('order_id',       $order_id);
	
    make_json_result($smarty->fetch('city_operate.dwt'), '', array('filter' => $city_list['filter'], 'order_id' => $order_id , 'page_count' => $city_list['page_count'],'record_count' => $city_list['record_count'],'page_size' => $city_list['page_size']));
	
}
elseif ($_REQUEST['act'] == 'city_ad_list')
{
	$city_id = isset($_REQUEST['city_id'])   && intval($_REQUEST['city_id'])  > 0 ? intval($_REQUEST['city_id'])  : 0;   
	$ad_list = get_ad_list_by_cityid($city_id);
	$smarty->assign('ad_list',   $ad_list);

	$smarty->display('city_view.dwt');	
}
//$smarty->display('city_operate.dwt', $cache_id);
elseif($_REQUEST['act'] == 'upload_panel')
{
	//删除临时文件
	$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('city_temp') . " WHERE user_id = $_SESSION[user_id]");
	
	$smarty->display('city_upload.dwt');	
	
}
elseif($_REQUEST['act'] == 'upload_file')
{
	include_once(ROOT_PATH . 'includes/upload_file.php');
	require(dirname(__FILE__) . '/includes/excel_reader2.php');
	error_reporting(0);
	
	//创建目录
	$dir = "data/excel/".$_SESSION['user_id'];
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

			$xls = new Spreadsheet_Excel_Reader();
			//$xls->setOutputEncoding('gb2312');
			$xls->setOutputEncoding('utf-8');
			$file_path = $val['path'];

			$xls->read($file_path);
			$all_sheets = $xls->sheets;

			for ($y=0;$y<count($all_sheets);$y++) 
			{ 
				//echo "sheet: $y  "." row_count:".$all_sheets[$y]['numRows']."    col_count:".$all_sheets[$y]['numCols']."<br>";
				$province_array = array();
				$city_array = array();
				$county_array = array();

				for ($row=3;$row<=$all_sheets[$y]['numRows'];$row++) 
				{
					$city_content = make_city_content();
					$city_content = full_city_content($all_sheets[$y]['cells'][$row],$city_content); //填充数据
					//print_r($city_content);
					if($city_content['col_3']){
						array_push($all_city_content,$city_content);
						// 一旦开始审核 就不能用 excel 导入了
						
						$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_temp'), $city_content, 'INSERT');
					}
									    
									
				}
			}


		  	//格式为：  Array
		  	//   (
		  	//    [0] => Array(
		  	//        [name] => example.txt
		    //        [type] => txt
		    //        [size] => 526
		    //        [path] => j:/tmp/example-1108898806.txt
		    //        )
		    //   )
		}
		
		$smarty->assign('upload_message', $num."个文件上传成功,请在5分钟之内检查下面的表格,确认数据是否正确");

	}
	else {
	  	echo "上传失败<br>";
	}
	
	$smarty->assign('all_city_content',       $all_city_content);
	$smarty->assign('save_Info', $save_Info);
	
	
	$smarty->display('city_upload.dwt');
}
elseif($_REQUEST['act'] == 'confirm_insert')
{
	$now = gmtime();
	$offset_time = 300; //300秒的操作时间
	$problem_array = array(); //有问题的数据
	
	$sql = "SELECT  *  FROM " .$GLOBALS['ecs']->table('city_temp') .
			" WHERE user_id = $_SESSION[user_id] AND ($now - user_time) < $offset_time ";
	//echo $sql;
	$res = $GLOBALS['db']->getAll($sql);
	
	
	foreach($res AS $key => $val)
	{	
		
	  	if($val['city_id']!=0)
		{

		$val['user_time'] = $now;
		
		//county 和 地址相同 那么 询问郭婷
		$exist_ad_info = is_exist_city_ad($val['city_id'],$val['col_7']);
		$sys_level = get_sys_level($val['city_id']);
		$record_id = $exist_ad_info['record_id'];
		
			//echo "**".$sys_level."--$record_id"."<br>";
			if($val['col_1'] == NULL || $val['col_2'] == NULL || $val['col_3'] == NULL || $val['col_4'] == NULL || $val['col_5'] == NULL || $val['col_6'] == NULL || $val['col_7'] == NULL || $val['col_8'] == NULL || $val['col_9'] == NULL || $val['col_10'] == NULL || $val['col_11'] == NULL || $val['col_12'] == NULL || $val['col_13'] == NULL || $val['col_14'] == NULL || $val['col_15'] == NULL || $val['col_16'] == NULL || $val['col_17'] == NULL || $val['col_18'] == NULL || $val['col_19'] == NULL || $val['col_20'] == NULL || $val['col_22'] == NULL || $val['col_24'] == NULL || $val['col_25'] == NULL || $val['col_26'] == NULL || $val['col_27'] == NULL || $val['col_28'] == NULL || $val['col_29'] == NULL || $val['col_30'] == NULL || $val['col_31'] == NULL || $val['col_32'] == NULL || $val['col_34'] == NULL || $val['col_35'] == NULL || $val['col_36'] == NULL || $val['col_37'] == NULL || $val['col_38'] == NULL || $val['col_39'] == NULL || $val['col_40'] == NULL){
				$issue = $val;
				$issue['temp_status'] = "除 佣金(U列)、媒体评分(W列)、制作费备注(AG列)、更改城市备注(AO列) 其他均为必填项,如果没有佣金也清填写0";
				array_push($problem_array,$issue);
			}
			elseif($record_id){
				if($exist_ad_info['audit_status'] <= 1){
					$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $val, 'update', "record_id='$record_id'");
				}else{
					$issue = $val;
					$issue['temp_status'] = "该广告已经开始审核，请勿再上传";
					array_push($problem_array,$issue);
				}
			//echo "update<br>";
			}
			elseif($sys_level < 5 ){
				$issue = $val;
				//echo "level-".$sys_level."<br>";
				$issue['temp_status'] = "该地区级别过高，请选择下级城市或辖区";
				array_push($problem_array,$issue);
			}
			else{
				$city_ad_num = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('city') . " WHERE city_id = $val[city_id]");
				//echo "**city_ad_num - $city_ad_num - insert<br>";
			
				if(CITY_AD_LIMIT - $city_ad_num > 0)
				{
				//echo "**insert<br>";
								
					$tmp = $val;
					$tmp['city_name'] = $val['col_3'];
					$tmp['is_upload'] = 1; //要等上传完照片
					$tmp['audit_status'] = 1;
					$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad'), $tmp, 'INSERT');
				
					$tmp['ad_id'] = $GLOBALS['db']->insert_id();
					$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $tmp, 'INSERT');
				
					//更新分类信息 for 一城一牌
					$sql = "UPDATE " . $GLOBALS['ecs']->table('category') . " SET is_upload = '1',  audit_status = '1'  WHERE cat_id = '$val[city_id]'";
		        	$GLOBALS['db']->query($sql);
					
				}else{
					$issue = $val;
					$issue['temp_status'] = "5条上限已经满";
					array_push($problem_array,$issue);				
				}
			}
		
		//清除 city_temp 库中的数据
		$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('city_temp') . " WHERE temp_id = $val[temp_id] LIMIT 1");
        
	  	}
		else{
			$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('city_temp') . " WHERE temp_id = $val[temp_id] LIMIT 1");
			
			$issue = $val;
			$issue['temp_status'] = "城市名称不正确，请核实！";
			array_push($problem_array,$issue);
	  	}
	}
	
	
	$success_insert_num = count($res) - count($problem_array);
	if(count($problem_array)){
		$insert_message =  "成功录入".$success_insert_num."条数据,以下是有问题的数据,请检查重新传输.";
	}else{
		$insert_message = "成功录入".$success_insert_num."条数据.";
	}
	
	$smarty->assign('insert_message', $insert_message);
	$smarty->assign('problem_array', $problem_array);
	$smarty->display('city_upload.dwt');
	
}
elseif($_REQUEST['act'] == 'cancel_insert')
{
	$now = gmtime();
	$offset_time = 300; //300秒的操作时间
	$problem_array = array(); //有问题的数据
	
	$sql = "SELECT  *  FROM " .$GLOBALS['ecs']->table('city_temp') .
			" WHERE user_id = $_SESSION[user_id] AND ($now - user_time) < $offset_time ";
	//echo $sql;
	$res = $GLOBALS['db']->getAll($sql);
	
	foreach($res AS $key => $val)
	{
		$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('city_temp') . " WHERE temp_id = $val[temp_id] LIMIT 1");
	}
	
	show_message("重新上传", $_LANG['profile_lnk'], 'city_operate.php?act=upload_panel', 'info', true);        
	
	
}

elseif($_REQUEST['act'] == 'edit_ad' || $_REQUEST['act'] == 'view_ad')
{
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	
	$ad_detail = get_city_info($ad_id);
	$smarty->assign('ad_detail', $ad_detail);
	
	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);

	if($ad_info['audit_status'] > 1){
		if($ad_info['is_audit_confirm'] == 1){
			if($ad_info['audit_status'] < 3){
				$upload_message = "分区可以修改 从媒体净价——更改城市备注 的所有项 ，其中媒体评分不能更改 .";
			}else{
				$upload_message = "已经开始审核不能修改了.<a href='city_operate.php?act=view_ad&ad_id=$ad_id'>点此查看</a>";
			}
		}else{
			$upload_message = "审核不通过,可以修改,但是不能修改 具体位置描述 .";
		}
	}
	
	$smarty->assign('upload_message', $upload_message);
	

	$photo_info = get_ad_photo_info($ad_id);
	$smarty->assign('photo_info', $photo_info);
	
	$audit_path = get_audit_path($ad_id,$audit_level_array); //审核路径图
	$smarty->assign('audit_path', $audit_path);
	
	$smarty->display('city_view.dwt');
	
}
elseif($_REQUEST['act'] == 'update_ad')
{
	$city_content = make_city_content();
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';
	$form_audit = !empty($_REQUEST['form_audit']) ? intval($_REQUEST['form_audit']) : 0;
	$col = $_REQUEST['col'];
	if($form_audit){
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

	for($i=0;$i<count($col);$i++)
	{	
		$i_plus = $i +1;
		$city_content['col_'.$i_plus] = $col[$i];
	}	
	$city_content['user_time'] = gmtime();
	
	if($ad_id){
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $city_content, 'update', "ad_id='$ad_id'");
	}
	
	if($form_audit){
		show_message("修改成功", $_LANG['profile_lnk'], 'city_operate.php?act=audit&ad_id='.$ad_id, 'info', true);       
	}else{
		show_message("修改成功", $_LANG['profile_lnk'], 'city_operate.php?act=edit_ad&ad_id='.$ad_id, 'info', true);       
	}
}
elseif($_REQUEST['act'] == 'delete_ad')
{	
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
	$json = new JSON;
    $filters = $json->decode($_GET['JSON']);
	$ad_id = $filters->ad_id;
	
	//$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	
	$ad_detail = get_city_info($ad_id);
	$ad_info = get_ad_info($ad_id);
	
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_delete'), $ad_detail, 'INSERT');
	act_city_request($ad_id,$ad_info['audit_status'],1);
	
	$GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table('city') . "WHERE ad_id =  $ad_id ");
	$GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table('city_ad') . "WHERE ad_id =  $ad_id ");
	
	
	$message = "删除成功";
	$loaction = 'city_operate.php?act=city_ad_list&city_id='.$ad_detail['city_id'];
	make_json_result($loaction,$message);
    
	//show_message("删除成功", $_LANG['profile_lnk'], 'city_operate.php?act=city_ad_list&city_id='.$ad_detail['city_id'], 'info', true);       
	
	
}

elseif($_REQUEST['act'] == 'view_log')
{
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : 0;
	$col_name = !empty($_REQUEST['col_name']) ? trim($_REQUEST['col_name']) : '';
	 
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('city_ad_log') .
			" WHERE ad_id = $ad_id AND col_name LIKE '".$col_name."' ORDER BY time DESC ";
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $k => $v){
		$res[$k]['time'] = local_date('Y-m-d H:i:s', $v['time']);
	}
	
	$smarty->assign('log_list', $res);
	$smarty->assign('col_name', $col_name);
	$smarty->display('city_view.dwt');
	
}


/*审核人员*/
elseif($_REQUEST['act'] == 'audit')
{
	$just_view =  !empty($_POST['just_view']) ? intval($_POST['just_view']) : 0;
	$smarty->assign('just_view', $just_view);
	
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	
	$ad_detail = get_city_info($ad_id);
	$smarty->assign('ad_detail', $ad_detail);
	
	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);
	
	$photo_info = get_ad_photo_info($ad_id);
	$smarty->assign('photo_info', $photo_info);
	$smarty->assign('ad_id', $ad_id);
	

	$audit_path = get_audit_path($ad_id,$audit_level_array); //审核路径图
	$smarty->assign('audit_path', $audit_path);
	
	$sql_2 = "SELECT MAX(user_rank) FROM " . $GLOBALS['ecs']->table('city_ad_audit') .
			" WHERE ad_id = $ad_id ";
	$highest_audit_level = $GLOBALS['db']->getOne($sql_2);
	$smarty->assign('highest_audit_level', $highest_audit_level);
	
	$smarty->display('city_view.dwt');
	
}

elseif($_REQUEST['act'] == 'update_audit')
{
	$ad_id =  !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : 0;
	$city_id =  !empty($_REQUEST['city_id']) ? intval($_REQUEST['city_id']) : 0;
	$confirm = !empty($_REQUEST['confirm']) ? intval($_REQUEST['confirm']) : 0;
	
	
	/* 写入审核历史数据库*/
	$audit_info = array();
	$audit_info['ad_id'] = $ad_id;
	$audit_info['user_id'] = $_SESSION['user_id'];
	$audit_info['user_rank'] = $_SESSION['user_rank'];
	$audit_info['audit_note'] = $confirm > 0 ? "审核通过": trim($_POST['audit_note']);
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad_audit'), $audit_info, 'INSERT');
	
	$return_url = "city_operate.php?act=city_ad_list&city_id=$city_id";
	
	if($confirm == 1){
		$cat_info['audit_status'] = $_SESSION['user_rank'];
		$cat_info['is_audit_confirm'] = 1;
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad'), $cat_info, 'UPDATE', "ad_id = '$ad_id'");
		
		//大列表的流程状态
		$audit_status = $cat_info['audit_status'] + 1; //上一级别可看
		$sql = "UPDATE " . $GLOBALS['ecs']->table('category') . " SET audit_status = '$audit_status'  WHERE cat_id = '$city_id'";
        $GLOBALS['db']->query($sql);
		act_city_request($ad_id,$_SESSION['user_rank']);//更新请求库	
		
		show_message("审核通过,其他人会看到。", $_LANG['back_home_lnk'], $return_url, 'info', true);
		
	}else{
		//
		$cat_info['audit_status'] = $_SESSION['user_rank'];
		$cat_info['is_audit_confirm'] = 0;
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad'), $cat_info, 'UPDATE', "ad_id = '$ad_id'");
		act_city_request($ad_id,$_SESSION['user_rank'],1);//更新请求库	
		
		show_message("审核信息已经提交。", $_LANG['back_home_lnk'], $return_url, 'info', true);
		//$smarty->display('city_view.dwt');
	}
}



/*上传城市照片*/
elseif($_REQUEST['act'] == 'upload_photo')
{
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);
	
	$photo_info = get_ad_photo_info($ad_id);
	$smarty->assign('photo_info', $photo_info);
	$smarty->assign('reupload_message', "重新上传将替换照片");
	
	$smarty->assign('ad_id', $ad_id);
	
	$smarty->display('city_view.dwt');
}

elseif($_REQUEST['act'] == "act_upload_photo")
{
	$ad_id  = empty($_REQUEST['ad_id']) ? 30 : $_REQUEST['ad_id'] ;
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

	
	handle_ad_gallery_image($ad_info['city_id'],$ad_id, $photo, $desc_array, $sort_array,$id_array);
	//算是完整上传
	//$sql = "UPDATE " . $GLOBALS['ecs']->table('city_ad') . " SET is_upload = '1'  WHERE ad_id = '$ad_id'";
    //$GLOBALS['db']->query($sql);
	act_city_request($ad_id,AUDIT_1);//更新请求库	
	show_message("恭喜您,照片上传成功。", $_LANG['back_home_lnk'], "city_operate.php?act=city_ad_list&city_id=$ad_info[city_id]", 'info', true);
}
//删除照片
elseif($_REQUEST['act'] == "delete_photo")
{
	
}

// 修改订单名字
elseif ($_REQUEST['act'] == 'edit_order_name')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	require_once(ROOT_PATH . 'admin/includes/lib_main.php');
	
    $order_id = intval($_POST['id']);
    $order_name = json_str_iconv(trim($_POST['val']));

	
    if ($exc_order->edit("order_name = '$order_name'", $order_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($order_name));

    }
	
}

/*------------------------------------------------------ */
//-- 导出状态库存统计表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'export_db')
{	
	$children = get_city_children_a($user_region);
	$limit = 2500;

    $ad_list = getFull_ad_list($children,$limit,$_LANG['resource']);
	
	$file_name = "Report_".$_SESSION['user_name']."_".local_date('Y-m-d-H-i-s', gmtime());

	$city_title = $_LANG['city_title'];
	$title_expend = array(
			"lv_2"=>$_LANG['AUDIT']['2'],
			"lv_3"=>$_LANG['AUDIT']['3'],
			"lv_4"=>$_LANG['AUDIT']['4'],
			"lv_5"=>$_LANG['AUDIT']['5'],
			"resource_type"=>$_LANG['resource_title']);
	$title = array_merge($city_title,$title_expend);
	//print_r($title);
		
	$tt = excel_write_with_sub_array($file_name,$title,$ad_list,'city');		
	
	//print_r($title);	
	//print_r($city_list['citys']);
	
	if(true)
	{
		$link[0]['text'] = '下载地址';
	    $link[0]['href'] = 'city_operate.php?act=show';

		$link[1]['text'] = '请下载报表';
	    $link[1]['href'] = 'xls/city/'.$file_name.'.xls';
	
	    show_message($link[1]['text'], $link[0]['text'], $link[1]['href'], 'info',false);
   	}

}
//中央leader
elseif($_REQUEST['act'] == 'batch_audit')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
	$json = new JSON;
    $filters = $json->decode($_GET['JSON']);
	$confirm = $filters->confirm;
	
	//符合要求的数据列表
	$sql = "SELECT ad_id,city_id FROM ".$GLOBALS['ecs']->table('city_ad'). " WHERE audit_status = 4 AND is_audit_confirm =1 ";
	$res = $GLOBALS['db']->getAll($sql);
	
	foreach($res AS $val){
		$cat_info['audit_status'] = $_SESSION['user_rank'];
		$cat_info['is_audit_confirm'] = 1;
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad'), $cat_info, 'UPDATE', "ad_id = '$val[ad_id]'");
		
		//大列表的流程状态
		$audit_status = $cat_info['audit_status'] + 1; //上一级别可看
		$sql = "UPDATE " . $GLOBALS['ecs']->table('category') . " SET audit_status = '$audit_status'  WHERE cat_id = '$val[city_id]'";
        $GLOBALS['db']->query($sql);
		act_city_request($val['ad_id'],$_SESSION['user_rank']);//更新请求库
		
		/* 写入审核历史数据库*/
		$audit_info = array();
		$audit_info['ad_id'] = $val['ad_id'];
		$audit_info['user_id'] = $_SESSION['user_id'];
		$audit_info['user_rank'] = $_SESSION['user_rank'];
		$audit_info['audit_note'] = "审核通过";
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad_audit'), $audit_info, 'INSERT');
				
	}
	//批量写入审核通过记录
	
	if($confirm){
		$str = count($res)."个广告位成功通过审核";
		make_json_result($str);		
	}
}

/*统一整理数据*/

elseif($_REQUEST['act'] == 'batch_recount')
{
	if($_SESSION['user_id'] != 1){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$sql = "SELECT COUNT( * ) AS Rows , city_id FROM ".$GLOBALS['ecs']->table('city_ad')." GROUP BY city_id ORDER BY city_id ";
    $res = $GLOBALS['db']->getAll($sql);
	echo count($res);
	foreach($res AS $k => $v){
		$lv = array();
		$lv['lv_1'] = 0;
		$lv['lv_2'] = 0;
		$lv['lv_3'] = 0;
		$lv['lv_4'] = 0;
		$lv['lv_5'] = 0;
		$lv['lv_6'] = 0;
		
		$sql_3 = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_gallery') . " AS g ON g.ad_id = ad.ad_id ". 
			 	
				" WHERE ad.city_id = $v[city_id] AND ad.audit_status = 1  AND ad.is_upload = 1 AND g.img_id > 0 ";
		$tmp1 = $GLOBALS['db']->getOne($sql_3);
		$lv['lv_2'] = $tmp1 / 4 ;
		echo $sql_3."<br>";
		
		$sql = "SELECT audit_status FROM ".$GLOBALS['ecs']->table('city_ad')." WHERE city_id = $v[city_id] AND is_upload = 1 AND is_audit_confirm = 1 ";
		$tt = $GLOBALS['db']->getAll($sql);
		echo $sql."<br>";
		


	    foreach($tt AS $m){
			switch($m['audit_status'])
		    {
		        case 2:
		            $lv['lv_3'] += 1 ;
		            break;
				case 3:
		            $lv['lv_4'] += 1 ;
		            break;
				case 4:
		            $lv['lv_5'] += 1 ;
		            break;
				case 5:
		            $lv['lv_6'] += 1 ;
		            break;
		    }
		
		}
		print_r($lv) ;
		echo "<br>";
		
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_request'), $lv, 'update', "city_id='$v[city_id]'");

	}
	
}


?>