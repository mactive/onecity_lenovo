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
$smarty->assign('audit_title', $_LANG['audit']);
$smarty->assign('CONTENT_COLS', CONTENT_COLS);




$real_name = $GLOBALS['db']->getOne("SELECT real_name FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = $_SESSION[user_id]");
$smarty->assign('real_name', $real_name);
$your_user_rank = get_rank_info();
$smarty->assign('your_user_rank', $your_user_rank['rank_name']);


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
    $cat_id = intval($_GET['id']);
}
elseif(!empty($_GET['category']))
{
    $cat_id = intval($_GET['category']);
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
$cache_id = sprintf('%X', crc32($cat_id . '-' . $page . '-' . $_CFG['lang']));

if (!$smarty->is_cached('city_operate.dwt', $cache_id) && $act == 'show')
{
    /* 如果页面没有被缓存则重新获得页面的内容 */
	//用户权限下的已经上传的城市列表
	$user_region = get_user_region();
    $children = get_city_children($user_region);
	
	//$user_permission = get_user_permission($user_region);
	//print_r($res);


	$_LANG['pager_1'] = '总计 ';
	$_LANG['pager_2'] = ' 篇审批';
	$smarty->assign('lang', $_LANG);
    
	$smarty->assign('full_page',        '1');  // 当前位置

    assign_template('a', array($cat_id));
    $position = assign_ur_here($cat_id);
    $smarty->assign('page_title',           $position['title']);     // 页面标题
    $smarty->assign('ur_here',              $position['ur_here']);   // 当前位置

    // 分页
    //暂时assign_pager('city_operate',         $cat_id, $count, $size, '', '', $page,$keywords);
    //assign_dynamic('city_operate');
	//assign_query_info();
	$city_list = get_city_list($children);
	$smarty->assign('city_list',    $city_list['citys']);	
    $smarty->assign('filter',       $city_list['filter']);
	$smarty->assign('record_count', $city_list['record_count']);
    $smarty->assign('page_count',   $city_list['page_count']);
    $smarty->assign('sql',   $city_list['sql']);
	
	$smarty->display('city_operate.dwt');	

}

/* query_show*/
elseif ($_REQUEST['act'] == 'query_show')
{
	$province = isset($_REQUEST['province'])   && intval($_REQUEST['province'])  > 0 ? intval($_REQUEST['province'])  : 0;   
	$brand = isset($_REQUEST['brand']) && intval($_REQUEST['brand']) > 0 ? intval($_REQUEST['brand']) : 0;
	$smarty->assign('category',         $cat_id);
	$smarty->assign('brand',         $brand);
	$smarty->assign('full_page',        '0');  // 当前位置
	
	
	$user_region = get_user_region();
	//print_r($user_region);
    $children = get_city_children($user_region);
	
	//print_r($res);
	
	$city_list = get_city_list($children,$province);
	$smarty->assign('city_list',    $city_list);
	
	
	
    $city_list = get_city_list($children);
	$smarty->assign('city_list',    $city_list['citys']);	
    $smarty->assign('filter',       $city_list['filter']);
	$smarty->assign('record_count', $city_list['record_count']);
    $smarty->assign('page_count',   $city_list['page_count']);

	$smarty->assign('sql',   	$city_list['sql']);
	
    $order_id = isset($_REQUEST['order_id'])   && intval($_REQUEST['order_id'])  > 0 ? intval($_REQUEST['order_id'])  : 0;
//	$smarty->assign('order_id',       $order_id);
	
    make_json_result($smarty->fetch('city_operate.dwt'), '', array('filter' => $city_list['filter'], 'order_id' => $order_id , 'page_count' => $city_list['page_count']));
	
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

				for ($row=2;$row<=$all_sheets[$y]['numRows'];$row++) 
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
	  if($val['cat_id']!=0){

		$val['user_time'] = $now;
		//如果存在那么update
		//county 和 地址相同 那么
		$is_exist = $GLOBALS['db']->getOne("SELECT cat_id FROM " . $GLOBALS['ecs']->table('city') . " WHERE cat_id = $val[cat_id]");
		if($is_exist){
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $val, 'update', "cat_id='$val[cat_id]'");
		}else{
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $val, 'INSERT');
		}
		
		//清除 city_temp 库中的数据
		$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('city_temp') . " WHERE temp_id = $val[temp_id] LIMIT 1");
        
		//更新分类信息
		$sql = "UPDATE " . $GLOBALS['ecs']->table('category') . " SET is_upload = '1'  WHERE cat_id = '$val[cat_id]'";
        $GLOBALS['db']->query($sql);
	  }else{
		$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('city_temp') . " WHERE temp_id = $val[temp_id] LIMIT 1");
		array_push($problem_array,$val);
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


elseif($_REQUEST['act'] == 'edit_city' || $_REQUEST['act'] == 'view_city')
{
	$cat_id = isset($_REQUEST['cat_id']) && intval($_REQUEST['cat_id']) > 0 ? intval($_REQUEST['cat_id']) : 0;
	$city_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('city') . " WHERE cat_id = $cat_id");
	$smarty->assign('city_info', $city_info);
	$photo_info = $GLOBALS['db']->getAll("SELECT * FROM " . $GLOBALS['ecs']->table('ideas_gallery') . " WHERE idea_id = $cat_id");
	$smarty->assign('photo_info', $photo_info);
	
	$smarty->display('city_view.dwt');
	
}
elseif($_REQUEST['act'] == 'update_city')
{
	$city_content = make_city_content();
	$cat_id = !empty($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : '';
	$col = $_REQUEST['col'];
	for($i=0;$i<count($col);$i++)
	{	
		$i_plus = $i +1;
		$city_content['col_'.$i_plus] = $col[$i];
	}	
	$city_content['user_time'] = gmtime();
	
	if($cat_id){
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city'), $city_content, 'update', "cat_id='$cat_id'");
	}
	
	show_message("修改成功", $_LANG['profile_lnk'], 'city_operate.php?act=edit_city&cat_id='.$cat_id, 'info', true);        
}
/*审核人员*/
elseif($_REQUEST['act'] == "audit")
{
	$just_view =  !empty($_POST['just_view']) ? intval($_POST['just_view']) : 0;
	$smarty->assign('just_view', $just_view);
	
	$cat_id = isset($_REQUEST['cat_id']) && intval($_REQUEST['cat_id']) > 0 ? intval($_REQUEST['cat_id']) : 0;
	$city_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('city') . " WHERE cat_id = $cat_id");
	$smarty->assign('city_info', $city_info);
	
	$photo_info = $GLOBALS['db']->getAll("SELECT * FROM " . $GLOBALS['ecs']->table('ideas_gallery') . " WHERE idea_id = $cat_id");
	$smarty->assign('photo_info', $photo_info);
	$smarty->assign('cat_id', $cat_id);
	
	$sql = "SELECT c.*, u.user_name , r.rank_name FROM " . $GLOBALS['ecs']->table('city_audit') . " AS c ".
 			" LEFT JOIN " .$GLOBALS['ecs']->table('users') . " AS u ON u.user_id = c.user_id ". 
 			" LEFT JOIN " .$GLOBALS['ecs']->table('user_rank') . " AS r ON r.rank_id = c.user_rank ". 
			" WHERE cat_id = $cat_id ORDER BY time DESC ";
	$audit_list = $GLOBALS['db']->getAll($sql);
	
	$smarty->assign('audit_list', $audit_list);
	
	$sql_2 = "SELECT MAX(user_rank) FROM " . $GLOBALS['ecs']->table('city_audit') .
			" WHERE cat_id = $cat_id ";
	$highest_audit_level = $GLOBALS['db']->getOne($sql_2);
	$smarty->assign('highest_audit_level', $highest_audit_level);
	
	$smarty->display('city_view.dwt');
	
}

elseif($_REQUEST['act'] == "update_audit")
{
	$cat_id =  !empty($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
	$confirm = !empty($_REQUEST['confirm']) ? intval($_REQUEST['confirm']) : 0;
	
	if($confirm == 1){
		$cat_confirm_id = !empty($_REQUEST['cat_confirm_id']) ? intval($_REQUEST['cat_confirm_id']) : 0;
		$cat_info['audit_status'] = $_SESSION['user_rank'];
		$cat_info['is_audit_confirm'] = 1;
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category'), $cat_info, 'UPDATE', "cat_id = '$cat_confirm_id'");
		show_message("审核通过,其他人会看到。", $_LANG['back_home_lnk'], 'city_operate.php', 'info', true);
		
	}else{
		$audit_info = array();
		$audit_info['city_record_id'] = !empty($_POST['city_record_id']) ? intval($_POST['city_record_id']) : 0;// 基于那条记录的
		$audit_info['cat_id'] = $cat_id;
		$audit_info['audit_note'] = trim($_POST['audit_note']);
		$audit_info['user_id'] = $_SESSION['user_id'];
		$audit_info['user_rank'] = $_SESSION['user_rank'];

		// 写入历史数据库
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_audit'), $audit_info, 'INSERT');
		
		//
		$cat_info['audit_status'] = $_SESSION['user_rank'];
		$cat_info['is_audit_confirm'] = 0;
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category'), $cat_info, 'UPDATE', "cat_id = '$cat_id'");

		show_message("审核信息已经提交。", $_LANG['back_home_lnk'], 'city_operate.php', 'info', true);
		//$smarty->display('city_view.dwt');
	}
	
	
	
	
    
	
	
	
	
}



/*上传城市照片*/
elseif($_REQUEST['act'] == 'upload_photo')
{
	
	$cat_id = isset($_REQUEST['cat_id']) && intval($_REQUEST['cat_id']) > 0 ? intval($_REQUEST['cat_id']) : 0;
	$city_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('category') . " WHERE cat_id = $cat_id");
	$smarty->assign('city_info', $city_info);
	
	$photo_info = $GLOBALS['db']->getAll("SELECT * FROM " . $GLOBALS['ecs']->table('ideas_gallery') . " WHERE idea_id = $cat_id");
	$smarty->assign('photo_info', $photo_info);
	$smarty->assign('reupload_message', "重新上传将替换照片");
	
	
	$smarty->assign('cat_id', $cat_id);
	
	$smarty->display('city_view.dwt');
}

elseif($_REQUEST['act'] == "act_upload_photo")
{
	$idea_id  = empty($_REQUEST['idea_id']) ? 30 : $_REQUEST['idea_id'] ;
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
			if(($val < 450000 || $val > 1024000 ) && $modify == 0 ){
				$key_1 = $key + 1;
				show_message("第".$key_1."张图片尺寸不足500KB或大于1MB");
			}else{
				//操作写入数据库
				$desc_array[$key] = $desc[$key];
				$sort_array[$key] = $key;
				$id_array[$key] = $img_id[$key];
			}
		}
	}

	
	handle_idea_gallery_image($idea_id, $photo, $desc_array, $sort_array,$id_array);
	show_message("恭喜您,照片上传成功。", $_LANG['back_home_lnk'], 'city_operate.php', 'info', true);
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


?>