<?php
/**
 * lenovo-one 资料分类 * $Author: mactive $
 * 此文件用来执行一些批量操作
 * $Id: city_sql.php 14481 2011-05-18 11:23:01Z mactive $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_city.php');
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

if (!$smarty->is_cached('_project_list.dwt', $cache_id) && $act == 'list_project')
{
    /* 如果页面没有被缓存则重新获得页面的内容 */
	
	$position['ur_here'] .= "<li>项目列表</li>"; 
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

	$children = get_city_children_a($user_region);

	$project_list = get_project_list($children);
	$smarty->assign('project_list',    $project_list);

}

/**
*	当插入新的4级城市的时候才响应
* 	插入4级城市的request数据 
*/
elseif($_REQUEST['act'] == 'refreshv_lv_4_request'){
	echo "rr";
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	$sql_1 = "SELECT a.cat_id AS city_id,a.cat_name AS city_name , a.parent_id, a.sys_level,b.is_upload, b.audit_status	, b.is_audit_confirm, b.lv_0, b.lv_1, b.lv_2, b.lv_3, b.lv_4, b.lv_5, b.lv_6 ".
			" FROM ".$GLOBALS['ecs']->table('category') . " AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_request_back') . " AS b ON b.city_name = a.cat_name AND b.parent_id = a.parent_id ". 
			" WHERE a.cat_id > 0 ";
	$res = $GLOBALS['db']->getAll($sql_1);
	
	foreach($res AS $val){
		echo $val['city_name']."<br>";
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_request'), $val, 'INSERT');
		
	}
	
}

/* 
*	当插入新的4级城市的时候才响应
*	插入4级城市的resource数据 
*/
elseif($_REQUEST['act'] == 'refresh_lv_4_resource'){
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	$sql_1 = "SELECT a.cat_id AS city_id,a.cat_name AS city_name , a.parent_id, a.sys_level,a.market_level,a.resource, b.Q1, b.Q2, b.Q3, b.Q4 ".
			" FROM ".$GLOBALS['ecs']->table('category') . " AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS b ON b.city_name = a.cat_name AND b.parent_id = a.parent_id ". 
			" WHERE a.cat_id > 0 ";
	$res = $GLOBALS['db']->getAll($sql_1);
	echo count($res)."<br>";
	foreach($res AS $val){
		//echo $val['city_name']."<br>";
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_resource_1'), $val, 'INSERT');
		
	}
	
}

/**
 *	如果待审核条数 有错误的时候
 * 	统一整理数据 生成新的待审核数量
 */
elseif($_REQUEST['act'] == 'batch_recount')
{
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$sql = "SELECT city_id FROM ".$GLOBALS['ecs']->table('city_request')." WHERE sys_level = 5 ";
    $res = $GLOBALS['db']->getAll($sql);
	echo count($res);
	foreach($res AS $k => $v){
		$lv = array();
		$lv['lv_0'] = 0;
		$lv['lv_1'] = 0;
		$lv['lv_2'] = 0;
		$lv['lv_3'] = 0;
		$lv['lv_4'] = 0;
		$lv['lv_5'] = 0;
		$lv['lv_6'] = 0;
		
		//通过照片给电通初始值
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

/**
 * 	生成所有IDEA类型的城市列表  
 *	$_LANG['pic_type_array']['SMBIDEA'] = "1";
 *	$_LANG['pic_type_array']['IDEA'] = "2";
 *	$_LANG['pic_type_array']['MIDHKAB'] = "3";
 *	empty 	所有的城市
 *	AND ad.audit_status = 5 AND ad.is_upload = 1 已经过审核的
 *	AND ad.audit_status < 5 AND ad.is_upload = 1 上传但没有通过审核
 */	
elseif($_REQUEST['act'] == 'IDEA_2'){
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$type =  !empty($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;
	switch ($type) {
		case '1':
			$sql_plus = " AND au.audit_note='审核通过' AND user_rank = 5 ";
			break;
		//
		case '2':
			$sql_plus = " AND ad.audit_status = 5 AND ad.is_upload = 1 AND ad.is_audit_confirm = 1";
			break;
		//
		case '3':
			$sql_plus = " AND ad.audit_status < 5 AND ad.is_upload = 1  AND ad.audit_status != 1 ";
			break;
		//
		case '4':
			$sql_plus = " AND ad.audit_status is NULL AND ad.is_audit_confirm is NULL ";
			break;
	}
	
	$sql_1 = "SELECT a.cat_id,a.cat_name AS county , au.ad_id ".
				//",a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region ".
				" FROM ".$GLOBALS['ecs']->table('city_ad_audit') ." AS au ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ".
				//" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	//" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	//" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
			 	//" LEFT JOIN " .$GLOBALS['ecs']->table('sm_city') . " AS c ON c.city_id = a.cat_id ".
			" WHERE a.resource = 2 AND a.sys_level = 5 ". $sql_plus .
			" GROUP BY ad.city_id ";
	echo $sql_1;
	$res = $GLOBALS['db']->getAll($sql_1);
	
	
	echo count($res)."<br>";
	echo "<table>";
	
	foreach($res AS $val){
		echo "<tr>";
		echo "<td>".$val['region']."</td>";
		echo "<td>".$val['province']."</td>";
		echo "<td>".$val['city']."</td>";
		echo "<td>".$val['county']."</td>";
		echo "<td>".$_LANG['audit_level'][$val['audit_status']]."</td>";
		echo "<td>".$val['is_audit_confirm']."</td>";
		echo "</tr>";
	}
	echo "</table>";
}


elseif($_REQUEST['act'] == 'IDEA'){
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$type =  !empty($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;
	switch ($type) {
		case '1':
			$sql_plus = " ";
			break;
		//
		case '2':
			$sql_plus = " AND ad.audit_status = 5 AND ad.is_upload = 1 ";
			break;
		//
		case '3':
			$sql_plus = " AND ad.audit_status < 5 AND ad.is_upload = 1  AND ad.audit_status != 1 ";
			break;
		//
		case '4':
			$sql_plus = " AND ad.audit_status is NULL AND ad.is_audit_confirm is NULL ";
			break;
	}
	
	$sql_1 = "SELECT a.cat_id,a.cat_name AS county , ad.audit_status, ad.is_audit_confirm , ".
				"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region ".
				" FROM ".$GLOBALS['ecs']->table('category') ." AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
			 	//" LEFT JOIN " .$GLOBALS['ecs']->table('sm_city') . " AS c ON c.city_id = a.cat_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.city_id = a.cat_id ".
			" WHERE a.resource = 2 AND a.sys_level = 5 ". $sql_plus .
			" GROUP BY ad.city_id ORDER BY ad.city_id ASC ";
	//echo $sql_1;
	$res = $GLOBALS['db']->getAll($sql_1);
	
	
	echo count($res)."<br>";
	echo "<table>";
	
	foreach($res AS $val){
		echo "<tr>";
		echo "<td>".$val['region']."</td>";
		echo "<td>".$val['province']."</td>";
		echo "<td>".$val['city']."</td>";
		echo "<td>".$val['county']."</td>";
		echo "<td>".$_LANG['audit_level'][$val['audit_status']]."</td>";
		echo "<td>".$val['is_audit_confirm']."</td>";
		echo "</tr>";
	}
	echo "</table>";
}


/** 废弃不再使用
* 	根据 删除城市数据库 中的数据 删除gallery中的数据
*	已经不需要了 因为在  city_operate.php?act=delete_ad 做了联动
*/
elseif($_REQUEST['act'] == 'delete_photo'){
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$sql_1 = "SELECT ad_id "." FROM ".$GLOBALS['ecs']->table('city_delete') .
			" WHERE 1  ";
	$res = $GLOBALS['db']->getAll($sql_1);
	
	echo count($res)."<br>";
	
	foreach($res AS $val){
		//echo $val['city_name']."<br>";
		$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('city_gallery') . " WHERE ad_id = $val[ad_id]");	
	}
	
}
/** 废弃不再使用
*	当服务器空间不足的时候
*	批量删除用户上传的原始照片
*	整个系统中不再使用原始大图 也不在存储原始大图了
*/
elseif($_REQUEST['act'] == "batch_delete_photo")
{
	if($_SESSION['user_id'] != 1){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$sql = "SELECT ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " WHERE audit_status = 5 AND is_audit_confirm = 1  ";
	$tt = $GLOBALS['db']->getAll($sql);//审核完成的广告牌子
	foreach($tt AS $v){
		$sql = "SELECT img_id,img_original FROM ".$GLOBALS['ecs']->table('city_gallery'). " WHERE ad_id = $v[ad_id]  ";
		$res = $GLOBALS['db']->getAll($sql);
		foreach($res AS $val){
			echo "--".$val['img_original']."<br>";
			if(!empty($val['img_original'])){
				@unlink(ROOT_PATH .$val['img_original']);
				$sql = "UPDATE " . $GLOBALS['ecs']->table('city_gallery') . " SET img_original = '0' WHERE img_id = $val[img_id] ";
		    	$GLOBALS['db']->query($sql);
			}
		}
	    echo "成功删除".count($res)."张原始照片";
	}
	echo "一共成功删除".count($tt)."座城市的原始照片";
	    	
}
elseif($_REQUEST['act'] == "jinmeng")
{
	if($_SESSION['user_id'] != 1){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$sichuan =  array_keys(cat_list(8, 0, false,4)); //晋ID 18

	/*
	$children = get_city_children(array('18'));

	$sql_5 = "SELECT cat_id FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
			" WHERE $children  AND a.sys_level = 5 ";
	$sichuan = $GLOBALS['db']->getCol($sql_5);
	*/
	//print_r($sichuan);
	$count = 0;
	foreach($sichuan AS $val){
		$city_id = $val;
		$sql = "SELECT ad_id,city_name FROM ".$GLOBALS['ecs']->table('city_ad')." WHERE city_id = $city_id AND audit_status = 3 AND is_audit_confirm = 1";
		$res = $GLOBALS['db']->getRow($sql);
		if($res['ad_id']){
			$ad_info = get_ad_info($res['ad_id']);
			echo $res['ad_id']."-".$res['city_name']."<br>";
			$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('city_ad') . " WHERE ad_id = $res[ad_id]");	
			act_city_request_delete($city_id,$ad_info['audit_status'],1);
			$count += 1;
			
		}
		
		
	}
	
	echo $count;
}


elseif($_REQUEST['act'] == 'CITY'){
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$type =  !empty($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;
	$is_upload =  !empty($_REQUEST['is_upload']) ? intval($_REQUEST['is_upload']) : 0;
	switch ($type) {
		case '4':
			$sql_plus = " AND a.market_level = 4 ";
			break;
		//
		case '5':
			$sql_plus = " AND a.market_level = 5 ";
			break;
		//
		case '6':
		
			$sql_plus = " AND a.market_level LIKE  '%6%' ";
		//	$sql_plus .= " AND a.resource = 2 ";
		//	$sql_plus .= " AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 ";
		//	$sql_plus .= " AND ad.is_upload = 1 AND  ad.audit_status < 5";
			
			break;
		//
		case 'baiqiang':
			$sql_plus = " AND a.market_level LIKE  '百强镇' ";
			break;
		case '6_plus':
		
			$sql_plus = " AND ( a.market_level LIKE '%6%' OR a.market_level LIKE '百强镇' ) ";			
			break;
		//
	}
	if($is_upload == 1){
		$sql_plus .= " AND ad.is_upload = 1 ";
	}
	
	
	
	$sql_1 = "SELECT a.cat_id,a.cat_name AS county ,a.market_level,a.resource, ".
				"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region ".
				//", count(ag.img_id) AS ag_num  ".
				" FROM ".$GLOBALS['ecs']->table('category') ." AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.city_id = a.cat_id ".
			 	//" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad_audit') . " AS au ON au.ad_id = ad.ad_id  ".
			" WHERE a.sys_level = 5 ". $sql_plus .
			" GROUP BY a.cat_id ORDER BY a.cat_id ASC ";
	//echo $sql_1;
	$res_1 = $GLOBALS['db']->getAll($sql_1);
	
	if($is_upload == 2)
	{
		$sql_plus .= " AND ad.is_upload = 1 ";
		$sql_2 = "SELECT a.cat_id,a.cat_name AS county ,a.market_level,a.resource, ".
					"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region ".
					//", count(ag.img_id) AS ag_num  ".
					" FROM ".$GLOBALS['ecs']->table('category') ." AS a ".
					" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
				 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
				 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
				 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.city_id = a.cat_id ".
				" WHERE a.sys_level = 5 ". $sql_plus . 
				" GROUP BY a.cat_id ORDER BY a.cat_id DESC ";
		//echo $sql_1;
		$res_2 = $GLOBALS['db']->getAll($sql_2);


		$diff_1 = array();
		$diff_2 = array();
		foreach($res_1 AS $val){
			$diff_1[$val['cat_id']] = $val;
		}
		foreach($res_2 AS $val){
			$diff_2[$val['cat_id']] = $val;
		}

		$res_1 = array_diff_key($diff_1,$diff_2);
		
	}
	
	foreach($res_1 AS $key => $val){
		$info = get_city_progress($val['cat_id']);
		$res_1[$key]['is_audit_confirm'] = $info['is_audit_confirm'];
		$res_1[$key]['is_shanghua'] = $info['is_shanghua'];
		$res_1[$key]['col_7'] = $info['col_7'];
	}
	
	
	echo count($res_1)."<br>";
	echo "<table>";
		echo "<tr>";
		echo "<td>".$_LANG['region']."</td>";
		echo "<td>".$_LANG['province']."</td>";
		echo "<td>".$_LANG['city']."</td>";
		echo "<td>".$_LANG['county']."</td>";
		echo "<td>".$_LANG['city_title']['col_7']."</td>";
		echo "<td>".$_LANG['market_level']."</td>";
		echo "<td>".$_LANG['resource_title']."</td>";
		echo "<td>".$_LANG['is_audit_confirm']."</td>";
		echo "<td>".$_LANG['is_shanghua']."</td>";
		echo "</tr>";
	foreach($res_1 AS $val){
		$is_upload = $val['ag_num'] > 0 ? "是" : "否" ;
		echo "<tr>";
		echo "<td>".$val['region']."</td>";
		echo "<td>".$val['province']."</td>";
		echo "<td>".$val['city']."</td>";
		echo "<td>".$val['county']."</td>";
		echo "<td>".$val['col_7']."</td>";
		echo "<td>".$val['market_level']."</td>";
		echo "<td>".$_LANG['resource'][$val['resource']]."</td>";
		echo "<td>".$val['is_audit_confirm']."</td>";
		echo "<td>".$val['is_shanghua']."</td>";
		echo "</tr>";
	}
	echo "</table>";
}
/*更新分区上传错误的数据*/
elseif($_REQUEST['act'] == 'querenlv_data')
{
	$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET col_1 = '山东大区' WHERE col_1 = '山东分区'";
	echo $sql;
	$GLOBALS['db']->query($sql);
	
	$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET col_1 = '江苏大区' WHERE col_1 = '江苏分区'";
	echo $sql;
	$GLOBALS['db']->query($sql);
	
	$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET col_1 = '浙江大区' WHERE col_1 = '浙江分区'";
	echo $sql;
	$GLOBALS['db']->query($sql);

	$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET col_1 = '京津冀大区' WHERE col_1 = '京津冀分区'";
	echo $sql;
	$GLOBALS['db']->query($sql);
	
}

/*更新分区上传错误的数据*/
elseif($_REQUEST['act'] == 'array_cha')
{
	$sql = "SELECT ad_id FROM ".$GLOBALS['ecs']->table('city')." WHERE 1 ORDER BY user_time DESC";
	$a1 = $GLOBALS['db']->getCol($sql);
	
	$sql = "SELECT ad_id FROM ".$GLOBALS['ecs']->table('city_ad')." WHERE 1 ";
	$a2 = $GLOBALS['db']->getCol($sql);
	
	$res = array_diff($a1,$a2);
	foreach($res AS $val){
		$sql = "SELECT col_3,user_time FROM ".$GLOBALS['ecs']->table('city')." WHERE ad_id = $val ";
		$a = $GLOBALS['db']->getRow($sql);
		
		$sql = "SELECT audit_note,user_rank,time FROM ".$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = $val ORDER BY time DESC limit 1  ";
		$b = $GLOBALS['db']->getRow($sql);
		
		
		echo $a['col_3']."-".local_date('Y-m-d', $a['user_time'])."-".$val."-".$b['audit_note']."-".$b['user_rank']."-".$b['time']."<br>";
		
		//$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('city') . " WHERE ad_id = $val");	
		
		
	}
	
}
/* exec_4_level*/
elseif($_REQUEST['act'] == 'exec_4_level')
{
	$sql = "SELECT cat_id AS city_id,cat_name AS city_name FROM ".$GLOBALS['ecs']->table('category')." WHERE market_level = 4 ";
	$res = $GLOBALS['db']->getAll($sql);
	$count = 0;
	foreach($res AS $val){
		$sql = "SELECT ad_id FROM ".$GLOBALS['ecs']->table('city')." WHERE city_id = $val[city_id] ";
		$ad_id = $GLOBALS['db']->getOne($sql);
		if($ad_id){
			echo $val['city_id']."-".$val['city_name']."<br>";
			act_level_4_city_upload($ad_id,$val['city_id']);
			
			$sql_1 = "UPDATE " . $GLOBALS['ecs']->table('city_ad') . " SET audit_status = '5', is_audit_confirm = '1' WHERE ad_id = '$ad_id' ";
			echo $sql_1;
			$GLOBALS['db']->query($sql_1);
			
			$sql_2 = "UPDATE " . $GLOBALS['ecs']->table('city_request') . " SET lv_0 = '0',lv_1 = '0',lv_2 = '0',lv_3 = '0',lv_4 = '0',lv_5 = '0',lv_6 = '1' WHERE city_id = '$val[city_id]'";
			echo $sql_2;
			$GLOBALS['db']->query($sql_2);
			
			$sql_3 = "UPDATE " . $GLOBALS['ecs']->table('city_gallery') . " SET feedback = '1' WHERE ad_id = '$ad_id'";
			echo $sql_3;
			$GLOBALS['db']->query($sql_3);
			
			$audit = array();
			$audit['ad_id'] = $ad_id;
			$audit['user_id'] = 63;
			$audit['user_rank'] = 5;
			$audit['audit_note'] = "审核通过";			
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad_audit'), $audit, 'INSERT');
			
			
			
			$count = $count+1;
		}
		
	}
	echo $count."<br>";	
}


/*更新分区上传错误的数据*/
elseif($_REQUEST['act'] == 'fankui_pic')
{
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 1;
	
	$sql = "SELECT ad_id FROM ".$GLOBALS['ecs']->table('city_gallery')." WHERE feedback  = $project_id GROUP BY ad_id LIMIT 4 ";
	echo $sql."<br>";
	
	$res = $GLOBALS['db']->getCol($sql);
	
	

	foreach($res AS $val){
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('city_gallery')." WHERE feedback  = $project_id AND ad_id = $val ORDER BY img_id DESC";
		echo $sql."<br>";
		$row = $GLOBALS['db']->getAll($sql);
		
		echo "<div>";
		echo $val;
		foreach($row AS $v){
//			echo '<img src="'.$v['thumb_url'].'" width="100" height="75" /> ';
			echo '<a href="city_sql.php?act=rename&ad_id='.$val.'&img_sort='.$v['img_sort'].'&path='.$v['img_url'].'">'.
			$v['img_id']."_".$v['img_sort'].'<a/> &nbsp;';
		}
		echo "</div>";
		
	}	
}
elseif($_REQUEST['act'] == 'rename')
{
	$ad_id =  !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';
	$img_sort =  !empty($_REQUEST['img_sort']) ? intval($_REQUEST['img_sort']) : 0;
	$path =  !empty($_REQUEST['path']) ? trim($_REQUEST['path']) : '';
	//echo $path;
	$img_sort = $img_sort + 1;
	$ad_info = get_ad_info($ad_id);
	$city_name  = $ad_info['city_name'];
	
	$info = array();
	$info['path'] = $path;
	$info['filename'] = $city_name."_".$img_sort.".jpg";
	
	pic_download($info);
}



elseif($_REQUEST['act'] == 'DIFF'){
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$type =  !empty($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;
	$is_upload =  !empty($_REQUEST['is_upload']) ? intval($_REQUEST['is_upload']) : 0;
	
	$sql_1 = "SELECT a.cat_id,a.cat_name AS county ,a.market_level,a.resource, ".
				"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region ".
				//", count(ag.img_id) AS ag_num  ".
				" FROM ".$GLOBALS['ecs']->table('category') ." AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
	//		 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.city_id = a.cat_id ".
			 	//" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad_audit') . " AS au ON au.ad_id = ad.ad_id  ".
			" WHERE a.sys_level = 5 ".
			" GROUP BY a.cat_id ORDER BY a.cat_id ASC ";
	//echo $sql_1;
	$res_1 = $GLOBALS['db']->getAll($sql_1);
	
	if($is_upload == 2)
	{
		$sql_plus .= " AND ad.is_upload = 1 ";
		$sql_2 = "SELECT a.cat_id,a.cat_name AS county ,a.market_level , ".
					"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region ".
					//", count(ag.img_id) AS ag_num  ".
					" FROM ".$GLOBALS['ecs']->table('category_0414') ." AS a ".
					" LEFT JOIN " .$GLOBALS['ecs']->table('category_0414') . " AS a1 ON a1.cat_id = a.parent_id ".
				 	" LEFT JOIN " .$GLOBALS['ecs']->table('category_0414') . " AS a2 ON a2.cat_id = a1.parent_id ". 
				 	" LEFT JOIN " .$GLOBALS['ecs']->table('category_0414') . " AS a3 ON a3.cat_id = a2.parent_id ".
				" WHERE a.sys_level = 5 ".
				" GROUP BY a.cat_id ORDER BY a.cat_id DESC ";
		//echo $sql_1;
		$res_2 = $GLOBALS['db']->getAll($sql_2);


		$diff_1 = array();
		$diff_2 = array();
		foreach($res_1 AS $val){
			$diff_1[$val['cat_id']] = $val;
		}
		foreach($res_2 AS $val){
			$diff_2[$val['cat_id']] = $val;
		}

		$res_1 = array_diff_key($diff_2,$diff_1);
		
	}
	
	foreach($res_1 AS $key => $val){
		$info = get_city_progress($val['cat_id']);
		//$res_1[$key]['is_audit_confirm'] = $info['is_audit_confirm'];
		//$res_1[$key]['is_shanghua'] = $info['is_shanghua'];
		//$res_1[$key]['col_7'] = $info['col_7'];
	}
	
	
	echo count($res_1)."<br>";
	echo "<table>";
		echo "<tr>";
		echo "<td>".$_LANG['region']."</td>";
		echo "<td>".$_LANG['province']."</td>";
		echo "<td>".$_LANG['city']."</td>";
		echo "<td>".$_LANG['county']."</td>";
		echo "<td>".$_LANG['city_title']['col_7']."</td>";
		echo "<td>".$_LANG['market_level']."</td>";
		echo "<td>".$_LANG['resource_title']."</td>";
		echo "<td>".$_LANG['is_audit_confirm']."</td>";
		echo "<td>".$_LANG['is_shanghua']."</td>";
		echo "</tr>";
	foreach($res_1 AS $val){
		$is_upload = $val['ag_num'] > 0 ? "是" : "否" ;
		echo "<tr>";
		echo "<td>".$val['region']."</td>";
		echo "<td>".$val['province']."</td>";
		echo "<td>".$val['city']."</td>";
		echo "<td>".$val['county']."</td>";
		echo "<td>".$val['col_7']."</td>";
		echo "<td>".$val['market_level']."</td>";
		echo "<td>".$_LANG['resource'][$val['resource']]."</td>";
		echo "<td>".$val['is_audit_confirm']."</td>";
		echo "<td>".$val['is_shanghua']."</td>";
		echo "</tr>";
	}
	echo "</table>";
}

/**
 * 
 */
function get_city_progress($city_id){
	$array['is_audit_confirm'] = "";
	$array['is_shanghua'] = "";
	$sql = "SELECT ad_id FROM ".$GLOBALS['ecs']->table('city_ad')." WHERE city_id = $city_id AND is_upload = 1 AND audit_status = 5 AND is_audit_confirm = 1 limit 1";
	$ad_id = $GLOBALS['db']->getOne($sql);
	if($ad_id){
		$array['is_audit_confirm'] = "是";
		
		$sql = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_gallery')." WHERE ad_id = $ad_id AND feedback = 1 ";
		$count = $GLOBALS['db']->getOne($sql); 			
			
		$sql_2 = "SELECT col_7 FROM ".$GLOBALS['ecs']->table('city')." WHERE ad_id = $ad_id ";
		$col_7 = $GLOBALS['db']->getOne($sql_2);
		$array['col_7'] = $col_7;
		
		if($count){
			$array['is_shanghua'] = "是";			
		}else{
			$array['is_shanghua'] = "否";
		}
		
	}else{
		$array['is_audit_confirm'] = "否";
		$array['is_shanghua'] = "否";
	}
	
	return $array;
	
}



?>