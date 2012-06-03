<?php
/**
 * SINEMALL 资料分类 * $Author: testyang $
 * $Id: city_renew.php 14481 2008-04-18 11:23:01Z testyang $
*/

//error_reporting(0); 
define('IN_ECS', true);
//error_reporting(E_ALL ^ E_NOTICE);


require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_city_renew.php');
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
$smarty->assign('city_dis_title', $_LANG['city_dis_title']);
$smarty->assign('publish_fee_title', $_LANG['publish_fee_title']);
$smarty->assign('publish_fee_note', $_LANG['publish_fee_note']);
$smarty->assign('renew_fee_note', $_LANG['renew_fee_note']);
$smarty->assign('audit_title', $_LANG['AUDIT']);
$smarty->assign('CONTENT_COLS', CONTENT_COLS);
$col_42_array = $_LANG['pic_type_select_lite'];



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
	$_REQUEST['act'] = "show";
	$smarty->assign('act_step',      "show");
}else{
	$smarty->assign('act_step',       $_REQUEST['act']);
}
	$smarty->assign('PHP_SELF',       get_page_name($_SERVER['PHP_SELF']));


/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 获得页面的缓存ID */
$cache_id = sprintf('%X', crc32($_SESSION['user_id'] . '-' . $page . '-' . $_CFG['lang']));
if (!$smarty->is_cached('city_renew.dwt', $cache_id) && $_REQUEST['act'] == 'show')
//if ($_REQUEST['act'] == 'show')
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
	
	$smarty->display('city_renew.dwt');	
}
/* 响应页面内刷新 query_show*/
elseif ($_REQUEST['act'] == 'query_show')
{
	$province = isset($_REQUEST['province'])   && intval($_REQUEST['province'])  > 0 ? intval($_REQUEST['province'])  : 0;   
	$smarty->assign('full_page',        '0');  // 当前位置

    $children = get_city_children($user_region);
	
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
	
    make_json_result($smarty->fetch('city_renew.dwt'), '', array('filter' => $city_list['filter'], 'order_id' => $order_id , 'page_count' => $city_list['page_count'],'record_count' => $city_list['record_count'],'page_size' => $city_list['page_size']));
	
}
/**
 * 显示城市的 全部牌子列表
 */
elseif ($_REQUEST['act'] == 'city_ad_list')
{
	$city_id = isset($_REQUEST['city_id'])   && intval($_REQUEST['city_id'])  > 0 ? intval($_REQUEST['city_id'])  : 0;
	
	$base_info = get_base_info($city_id);
	$city_name = $base_info['region_name'];
	$smarty->assign('city_name',   $city_name);
	$smarty->assign('has_new',   $base_info['has_new']);
	
	$ad_list = get_ad_list_by_cityid($city_id);
	$smarty->assign('ad_list',   $ad_list);

	$smarty->display('city_renew_view.dwt');	
}
/**
 * 删除excel导入的临时文件
 * city_temp 为导入临时数据库
 */
elseif($_REQUEST['act'] == 'upload_panel')
{
	//删除临时文件
	$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_temp') . " WHERE user_id = $_SESSION[user_id]");
	
	$smarty->display('city_upload.dwt');	
	
}
/**
 * 上传报表文件
 */
elseif($_REQUEST['act'] == 'upload_file')
{
	error_reporting(0); 
	
	include_once(ROOT_PATH . 'includes/upload_file.php');
	require(dirname(__FILE__) . '/includes/excel_reader2.php');
	
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
						
						$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_temp'), $city_content, 'INSERT');
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
		$content_count = count($all_city_content);
		if($content_count){
			$smarty->assign('upload_message', $num."个文件上传成功,请在5分钟之内检查下面的".$content_count."条数据,确认数据是否正确");
		}else{
			$smarty->assign('upload_message', "Excel文件未能识别,请下最新的模版,将数据复制记事本，然后重新粘贴到空白模版文件,再次上传");
		}

	}
	else {
	  	echo "上传失败<br>";
	}
	
	$smarty->assign('all_city_content',       $all_city_content);
	$smarty->assign('save_Info', $save_Info);
	
	
	$smarty->display('city_upload.dwt');
}
/**
 * 确认写入数据库
 */
elseif($_REQUEST['act'] == 'confirm_insert')
{
	error_reporting(0); 
	
	$now = gmtime();
	$offset_time = 300; //300秒的操作时间
	$problem_array = array(); //有问题的数据
	
	$sql = "SELECT  *  FROM " .$GLOBALS['ecs']->table($GLOBALS['year']."_".'city_temp') .
			" WHERE user_id = $_SESSION[user_id] AND ($now - user_time) < $offset_time ";
	//echo $sql;
	$res = $GLOBALS['db']->getAll($sql);
	
	
	foreach($res AS $key => $val)
	{	
		
	  	if($val['city_id']!=0)
		{

		$val['user_time'] = $now;
		
		$exist_ad_info = is_exist_city_ad($val['city_id'],$val['col_7']);
		$sys_level = get_sys_level($val['city_id']);
		$record_id = $exist_ad_info['record_id'];
		
			//echo "**".$sys_level."--$record_id"."<br>";
			if($val['col_1'] == NULL || $val['col_2'] == NULL || $val['col_3'] == NULL || $val['col_4'] == NULL || $val['col_5'] == NULL || $val['col_6'] == NULL || $val['col_7'] == NULL || $val['col_8'] == NULL || $val['col_9'] == NULL || $val['col_10'] == NULL || $val['col_11'] == NULL || $val['col_12'] == NULL || $val['col_14'] == NULL || $val['col_16'] == NULL || $val['col_17'] == NULL || $val['col_19'] == NULL || $val['col_20'] == NULL || $val['col_24'] == NULL || $val['col_25'] == NULL || $val['col_26'] == NULL || $val['col_27'] == NULL || $val['col_28'] == NULL || $val['col_29'] == NULL || $val['col_30'] == NULL || $val['col_31'] == NULL ){
				$issue = $val;
				$issue['temp_status'] = "除 佣金(U列) 媒体评分(W列)、备注(AF列) 其他均为必填项,如果没有佣金也清填写0";
				array_push($problem_array,$issue);
			}
			elseif($record_id){
				if($exist_ad_info['audit_status'] <= 1){
					$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city'), $val, 'update', "record_id='$record_id'");
				}else{
					$issue = $val;
					$issue['temp_status'] = "该广告已经开始审核，请勿再上传";
					array_push($problem_array,$issue);
				}
			}
			elseif($sys_level < 5 ){
				$issue = $val;
				//echo "level-".$sys_level."<br>";
				$issue['temp_status'] = "该地区级别过高，请选择下级城市或辖区";
				array_push($problem_array,$issue);
			}
			else{
				$city_ad_num = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city') . " WHERE city_id = $val[city_id]");
				$city_confirm_ad_num = get_city_confirm_ad_num($val['city_id']);
			
				if(CITY_AD_LIMIT - $city_ad_num > 0)
				{
					if($city_confirm_ad_num >= 2){
						$issue = $val;
						$issue['temp_status'] = "该城市已经有通过的牌子,不可以再上传新的";
						array_push($problem_array,$issue);
					}else{
						// 自动处理灰色的列表
						$tmp = $val;
						$market_level = get_market_level("",$val['city_id']);
						$base_info = get_base_info($val['city_id']);
						$tmp['city_name'] = $val['col_3'];
						$tmp['col_1'] = $base_info['region_name'];
						$tmp['col_2'] = $base_info['province_name'];
						$tmp['col_4'] = $market_level;
						
						// 2012 REPAIRMENT
						
						$tmp['col_13'] = round($tmp['col_12'] * $tmp['col_11'],1); //面积 = 宽 * 高
						$tmp['col_15'] = $tmp['col_13'] *  intval($tmp['col_14']); //总面积
						$tmp['col_18'] = sep_days( $val['col_17'],$val['col_16']); //发布天数
						$tmp['col_22'] = intval($val['col_19']) + intval($val['col_20']) + intval($val['col_21']); //媒体总价
												
						$tmp['col_40'] = $val['col_31']; //责任人
						$tmp['col_41'] = $val['col_32']; //备注
						
						$tmp['col_27'] = $tmp['col_28'] = $tmp['col_29'] = $tmp['col_30'] = $tmp['col_31'] = $tmp['col_32']  = "";
						$tmp['col_43'] = $val['col_27']; //甲方编号
						$tmp['col_44'] = $val['col_28']; //甲方名称
						$tmp['col_45'] = $val['col_29']; //上级编号
						$tmp['col_46'] = $val['col_30']; //上级名称
						
						// echo "=================<br>";
						// print_r($tmp);
						
						$tmp['is_upload'] = 1; //要等上传完照片
						$tmp['audit_status'] = 1;
						
						// 如果数据有错误
						if($tmp['col_18'] < 2){
							$issue = $val;
							$issue['temp_status'] = "发布时期为零,请检查开始日期和结束日期";
							array_push($problem_array,$issue);
							break;
						}else{
							/*成功写入*/
							$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad'), $tmp, 'INSERT');
							$tmp['ad_id'] = $GLOBALS['db']->insert_id();
							$tmp['ad_sn'] = make_ad_sn($tmp['ad_id'], $val['city_id']);
							$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city'), $tmp, 'INSERT');
							insert_dealer($tmp['col_43'],$tmp['col_44'],$base_info['region_name'],$tmp['ad_id']);
							insert_dealer($tmp['col_45'],$tmp['col_46'],$base_info['region_name'],$tmp['ad_id']);
							
							//4级城市直接通过
							if($market_level == "4"){
								act_level_4_city_upload($val['city_id'],$tmp['ad_id']);
							}	
						}
						

					}
				}else{
					$issue = $val;
					$issue['temp_status'] = "5条上限已经满";
					array_push($problem_array,$issue);				
				}
			}
		
		//清除 city_temp 库中的数据
		$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_temp') . " WHERE temp_id = $val[temp_id] LIMIT 1");
        
	  	}
		else{
			$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_temp') . " WHERE temp_id = $val[temp_id] LIMIT 1");
			
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
/**
 * 取消写入 并清空自己的临时数据
 */
elseif($_REQUEST['act'] == 'cancel_insert')
{
	$now = gmtime();
	$offset_time = 300; //300秒的操作时间
	$problem_array = array(); //有问题的数据
	
	$sql = "SELECT  *  FROM " .$GLOBALS['ecs']->table($GLOBALS['year']."_".'city_temp') .
			" WHERE user_id = $_SESSION[user_id] AND ($now - user_time) < $offset_time ";
	//echo $sql;
	$res = $GLOBALS['db']->getAll($sql);
	
	foreach($res AS $key => $val)
	{
		$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_temp') . " WHERE temp_id = $val[temp_id] LIMIT 1");
	}
	
	show_message("重新上传", $_LANG['profile_lnk'], 'city_renew.php?act=upload_panel', 'info', true);        
	
	
}
#续签广告牌子
elseif ($_REQUEST['act'] == 'renew_ad') {
	# code... 更新牌子 日期加1年 然后 is_renew = 1
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;

	$ad_detail = get_city_info($ad_id);
	$ad_info = get_ad_info($ad_id);
	if ($ad_info['is_renew'] == 1) {
		show_message("已经续签过，不要重复续签", $_LANG['profile_lnk'], 'city_renew.php?act=city_ad_list&city_id='.$ad_detail['city_id'], 'info', true);
	}

	$year = intval(date( 'Y',strtotime($ad_detail['col_16']))) + 1;
	$year2 = intval(date( 'Y',strtotime($ad_detail['col_17']))) + 1;

	$new_start_time = trim(date('m/d/',strtotime($ad_detail['col_16'])).$year);
	$new_end_time = trim(date('m/d/',strtotime($ad_detail['col_17'])).$year2);

	$detail_data['col_16'] = $new_start_time;
	$detail_data['col_17'] = $new_end_time;
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city'), $detail_data, 'UPDATE', "ad_id = '$ad_id'");

	$data['is_renew'] = 1;
	$data['checked_time'] = gmtime();
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad'), $data, 'UPDATE', "ad_id = '$ad_id'");

	act_renew_plus($ad_info['city_id']);

	$message = "续签成功.<br>"."时间从:&nbsp;&nbsp;".$ad_detail['col_16']."-".$ad_detail['col_17']."<br>变成为:&nbsp;&nbsp;".$new_start_time."-".$new_end_time;

	insert_ad_log($ad_id,'col_16',$ad_detail['col_16'],$new_start_time);
	insert_ad_log($ad_id,'col_17',$ad_detail['col_17'],$new_end_time);
	show_message($message, $_LANG['profile_lnk'], 'city_renew.php?act=city_ad_list&city_id='.$ad_detail['city_id'], 'info', false);


}

/* 修改城市信息 */
/**
 * 添加编辑 广告牌子
 */
elseif($_REQUEST['act'] == 'edit_ad' || $_REQUEST['act'] == 'view_ad')
{
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	
	$ad_detail = get_city_info($ad_id);
	$ad_detail['col_42'] = $col_42_array[$ad_detail['col_42']];
	$smarty->assign('ad_detail', $ad_detail);
	
	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);
	
	if($ad_info['is_new'] == 1){
		$another_ad_id = get_another_ad_id($ad_info['city_id'],$ad_id);
		$smarty->assign('another_ad_id', $another_ad_id);
		
		$overlap_info = get_overlap_info($another_ad_id,$ad_id);
		$smarty->assign('overlap_info', $overlap_info);
		
	}else{
		$another_ad_id = get_another_ad_id($ad_info['city_id'],$ad_id);
		$smarty->assign('another_ad_id', $another_ad_id);
		
		$overlap_info = array();	
		$tt = $ad_detail['col_19'] + $ad_detail['col_20'];
		$overlap_info['fee_1'] = intval($tt * 0.50 );
		$overlap_info['fee_2'] = intval($tt * 0.15 );
		$overlap_info['fee_3'] = intval($tt * 0.65 );
		$smarty->assign('overlap_info', $overlap_info);
		
		$photo_info = get_ad_photo_info($ad_id,2);
		$smarty->assign('photo_info', $photo_info);
	}

	if($ad_info['audit_status']> 3){
		$upload_message = "上个财年通过的牌子才能修改.";
	}
	
	$smarty->assign('upload_message', $upload_message);
	

	$photo_info = get_ad_photo_info($ad_id);
	$smarty->assign('photo_info', $photo_info);
	
	$audit_path = get_audit_path($ad_id,$audit_level_array); //审核路径图
	$smarty->assign('audit_path', $audit_path);
	
	$smarty->display('city_renew_view.dwt');
	
}
/**
 * 更新牌子页面
 */
elseif($_REQUEST['act'] == 'act_update_renew_info')
{
	$city_content = make_city_content();
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';
	$ad_info = get_ad_info($ad_id);
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
	$city_content['col_22'] = intval($city_content['col_19']) + intval($city_content['col_20']) + intval($city_content['col_21']); //媒体总价


	
	if($ad_id){
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city'), $city_content, 'update', "ad_id='$ad_id'");
		
		$data = array();
		$data['is_change'] = 1;//已经修改过
		$data['checked_time'] = gmtime();
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad'), $data, 'update', "ad_id='$ad_id'");
		
		act_change_plus($ad_info['city_id']);


		//记录修改记录
		$old_col = $_REQUEST['old_col'];
		
		foreach($old_col AS $key => $val){
			if($val != $col[$key] && !empty($val) && !empty($col[$key]) ){
				//echo $val."-".$col[$key]."<br>";
				$col_key = $key + 1;
				insert_ad_log($ad_id,"col_".$col_key,$val,$col[$key]);
			}
		}

		//act_city_request($ad_info['city_id'],$_SESSION['user_rank']);//更新请求库	
		
	}
	
	show_message("修改成功", "返回改城市牌子列表", 'city_renew.php?act=city_ad_list&city_id='.$ad_info['city_id'], 'info', true);       
}
/**
 * 删除牌子
 */
elseif($_REQUEST['act'] == 'delete_ad')
{	
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
	$json = new JSON;
    $filters = $json->decode($_GET['JSON']);
	$ad_id = $filters->ad_id;
	$ad_detail = get_city_info($ad_id);
	// $ad_info = get_ad_info($ad_id);
	$city_id = get_city_id($ad_id);
	echo "ad_id".$ad_id;
	$data['is_delete'] = 1;
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad'), $data, 'UPDATE', "ad_id = '$ad_id'");

	// $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_delete'), $ad_detail, 'INSERT');
	
	// $GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city') . "WHERE ad_id =  $ad_id ");
	// $GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad') . "WHERE ad_id =  $ad_id ");
	// $GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad_audit') . "WHERE ad_id =  $ad_id ");
	// $GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_material') . "WHERE ad_id =  $ad_id ");	
	// $GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_gallery') . "WHERE ad_id =  $ad_id ");
	
	act_city_request_delete($city_id,$ad_info['audit_status'],1);
	
	
	$message = "删除成功";
	$loaction = 'city_renew.php?act=city_ad_list&city_id='.$ad_detail['city_id'];
	make_json_result($loaction,$message);
    
	//show_message("删除成功", $_LANG['profile_lnk'], 'city_renew.php?act=city_ad_list&city_id='.$ad_detail['city_id'], 'info', true);       
	
	
}
/**
 * 查看牌子的 某一项的 修改记录
 */
elseif($_REQUEST['act'] == 'view_log')
{
	$ad_id = !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : 0;
	$col_name = !empty($_REQUEST['col_name']) ? trim($_REQUEST['col_name']) : '';
	 
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad_log') .
			" WHERE ad_id = $ad_id AND col_name LIKE '".$col_name."' ORDER BY time DESC ";
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $k => $v){
		$res[$k]['time'] = local_date('Y-m-d H:i:s', $v['time']);
	}
	
	$smarty->assign('log_list', $res);
	$smarty->assign('col_name', $col_name);
	$smarty->display('city_renew_view.dwt');
	
}
/**
 * 广告牌 审核界面
 */
elseif($_REQUEST['act'] == 'audit')
{
	$just_view =  !empty($_POST['just_view']) ? intval($_POST['just_view']) : 0;
	$smarty->assign('just_view', $just_view);
	
	$ad_id = isset($_REQUEST['ad_id']) && intval($_REQUEST['ad_id']) > 0 ? intval($_REQUEST['ad_id']) : 0;
	
	$ad_detail = get_city_info($ad_id);
	$smarty->assign('ad_detail', $ad_detail);

	$ad_info = get_ad_info($ad_id);
	$smarty->assign('ad_info', $ad_info);
	

		$another_ad_id = get_another_ad_id($ad_info['city_id'],$ad_id);
		$smarty->assign('another_ad_id', $another_ad_id);
			
		$overlap_info = array();	
		$tt = $ad_detail['col_19'] + $ad_detail['col_20'];
		$overlap_info['fee_1'] = intval($tt * 0.50 );
		$overlap_info['fee_2'] = intval($tt * 0.15 );
		$overlap_info['fee_3'] = intval($tt * 0.65 );
		$smarty->assign('overlap_info', $overlap_info);
	
	
	
	$photo_info = get_ad_photo_info($ad_id);
	$smarty->assign('photo_info', $photo_info);
	$smarty->assign('ad_id', $ad_id);
	
	// 获得消失的id
		
	$changed_detail = get_changed_detail($ad_id);
	$smarty->assign('changed_detail', $changed_detail);
		

	$audit_path = get_audit_path($ad_id,$audit_level_array); //审核路径图
	$smarty->assign('audit_path', $audit_path);
	
	$sql_2 = "SELECT MAX(user_rank) FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad_audit') .
			" WHERE ad_id = $ad_id ";
	$highest_audit_level = $GLOBALS['db']->getOne($sql_2);
	$smarty->assign('highest_audit_level', $highest_audit_level);
	
	$smarty->display('city_renew_view.dwt');
	
}
/**
 * 更新审核记录 写入数据库
 */
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
	$audit_info['audit_note'] = $confirm > 0 ? "续签审核通过": trim($_POST['audit_note']);
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad_audit'), $audit_info, 'INSERT');
	
	$return_url = "city_renew.php?act=city_ad_list&city_id=$city_id";
	
	if($confirm == 1){
		$cat_info['audit_status'] = $_SESSION['user_rank'];
		$cat_info['is_audit_confirm'] = 1;
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad'), $cat_info, 'UPDATE', "ad_id = '$ad_id'");
		
		//大列表的流程状态
		act_renew_audit($city_id);
		act_change_plus($city_id,0);
		//act_city_request($city_id,$_SESSION['user_rank']);//更新请求库	
		
		show_message("审核通过,其他人会看到。", $_LANG['back_home_lnk'], $return_url, 'info', true);
		
	}else{
		//
		$cat_info['audit_status'] = $_SESSION['user_rank'];
		$cat_info['is_audit_confirm'] = 0;
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad'), $cat_info, 'UPDATE', "ad_id = '$ad_id'");
		//act_city_request($city_id,$_SESSION['user_rank'],1);//更新请求库	
		
		show_message("审核信息已经提交。", $_LANG['back_home_lnk'], $return_url, 'info', true);
		//$smarty->display('city_renew_view.dwt');
	}
}


elseif($_REQUEST['act'] == 'export_page')
{
	//分区
	$region_array = get_region_array();
	$smarty->assign('region_array', $region_array);
	
	//城市级别
	$market_level_array = get_market_level_array();
	$smarty->assign('market_level_array', $market_level_array);
	
	//审核状态
	$audit_status_array = get_audit_status_array();
	$smarty->assign('audit_status_array', $audit_status_array);
	
	
	//market_level_array
	//audit_status_array
	$smarty->display('city_renew_view.dwt');
}
/*------------------------------------------------------ */
//-- 导出报表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'export_db')
{	
	error_reporting(0);
	$region 		= empty($_REQUEST['region']) ? "0" : intval($_REQUEST['region']);
	$market_level 	= empty($_REQUEST['market_level']) ? "" : trim($_REQUEST['market_level']);
	$audit_status 	= empty($_REQUEST['audit_status']) ? "" : trim($_REQUEST['audit_status']);
	$resource 		= empty($_REQUEST['resource']) ? "" : intval($_REQUEST['resource']);
	$has_new 		= empty($_REQUEST['has_new']) ? "" : intval($_REQUEST['has_new']);
    
	$start_time = empty($_REQUEST['start_time']) ? '0' : trim($_REQUEST['start_time']);
    $end_time   = empty($_REQUEST['end_time']) ? '0' : trim($_REQUEST['end_time']);


	if($region){
		$temp = array($region);
		$children = get_city_children_a($temp);
		
	}else{
		$children = get_city_children_a($user_region);
	}	
	
	// echo "region:".$region."<br>"; 
	$limit = 5000;
	$r_title = $_LANG['resource'];

    $ad_list = getFull_ad_list($children,$market_level,$audit_status,$resource,$start_time,$end_time,$r_title,$has_new,$limit);
	
	$file_name = "Report_".$_SESSION['user_name']."_".local_date('Y-m-d-H-i-s', gmtime());

	$ad_sn = array("ad_sn" => "广告编号");
	$tmp = $_LANG['city_title'];
	$publish_fee_title = $_LANG['publish_fee_title'];
	$city_title = array_merge($ad_sn,$tmp);
	$title_expend = array(
			"lv_2"=>$_LANG['AUDIT']['2'],
			"lv_3"=>$_LANG['AUDIT']['3'],
			"lv_4"=>$_LANG['AUDIT']['4'],
			"lv_5"=>$_LANG['AUDIT']['5'],
			// "quarter"=>"Q1审核记录",
			"last_audit_time"=>"最终审核时间",
			"resource_type"=>$_LANG['resource_title'],
			"start_date"=>"开始日期[数字]",
			"end_date"=>"结束日期[数字]");
	
	$title_expend = array_merge($title_expend,$publish_fee_title);
	$title = array_merge($city_title,$title_expend);
	//print_r($title);
	
	//echo "count_data:".count($ad_list);
	
	$tt = excel_write_with_sub_array($file_name,$title,$ad_list,'city');		
	
	//print_r($title);	
	//print_r($city_list['citys']);
	
	if(true)
	{
		$link[0]['text'] = '下载地址';
	    $link[0]['href'] = 'city_renew.php?act=show';

		$link[1]['text'] = '请下载报表.'."共".count($ad_list)."条数据";
	    $link[1]['href'] = 'xls/city/'.$file_name.'.xls';
	
	    show_message($link[1]['text'], $link[0]['text'], $link[1]['href'], 'info',false);
   	}

}
/**
 * 中央leader 批量审核
 */
elseif($_REQUEST['act'] == 'batch_audit')
{
	if($_SESSION['user_rank'] != 5){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_renew.php', 'info', true);        
	}
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
	$json = new JSON;
    $filters = $json->decode($_GET['JSON']);
	$confirm = $filters->confirm;
	
	//符合要求的数据列表
	$sql = "SELECT ad_id,city_id FROM ".$GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad'). " WHERE audit_status = 4 AND is_audit_confirm =1 ";
	$res = $GLOBALS['db']->getAll($sql);
	
	foreach($res AS $val){
		$cat_info['audit_status'] = $_SESSION['user_rank'];
		$cat_info['is_audit_confirm'] = 1;
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad'), $cat_info, 'UPDATE', "ad_id = '$val[ad_id]'");
		
		//大列表的流程状态
		$audit_status = $cat_info['audit_status'] + 1; //上一级别可看
		$sql = "UPDATE " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'category') . " SET audit_status = '$audit_status'  WHERE cat_id = '$val[city_id]'";
        $GLOBALS['db']->query($sql);
		act_city_request($val['city_id'],$_SESSION['user_rank']);//更新请求库
		
		/* 写入审核历史数据库*/
		$audit_info = array();
		$audit_info['ad_id'] = $val['ad_id'];
		$audit_info['user_id'] = $_SESSION['user_id'];
		$audit_info['user_rank'] = $_SESSION['user_rank'];
		$audit_info['audit_note'] = "审核通过";
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad_audit'), $audit_info, 'INSERT');
				
	}
	//批量写入审核通过记录
	
	if($confirm){
		$str = count($res)."个广告位成功通过审核";
		make_json_result($str);		
	}
}



?>