<?php
/**
 * SINEMALL 资料分类 * $Author: testyang $
 * $Id: city_operate.php 14481 2008-04-18 11:23:01Z testyang $
*/

//error_reporting(0); 
define('IN_ECS', true);
//error_reporting(E_ALL ^ E_NOTICE);


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
/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */


if($_REQUEST['act'] == 'new_querenlv')
{
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	$date = date('Y-m-d H:i:s',(gmtime()+28800));
	$smarty->assign('date',   $date);
	
	$based_new_nums = array();
	$based_new_nums = $_LANG['based_new_nums'];
	
	$sql = "SELECT COUNT( * ) AS  amount,col_1 FROM ".$GLOBALS['ecs']->table('city')." GROUP BY col_1  ORDER BY  CONVERT( col_1 USING GBK )   ASC ";
	$base = $GLOBALS['db']->getAll($sql);
	//echo $sql."<br>";
	foreach($base AS $key => $value){
		
		$cat_id = get_cat_id_by_name($value['col_1']);
		$children = get_city_children(array($cat_id));

		
		$sql_6_plus = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city'). " AS c " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = c.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS cat ON cat.cat_id = c.city_id ". 
				" WHERE c.col_1 LIKE '$value[col_1]' ". // 
				" AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 ".
				" AND cat.has_new = 1 AND ad.is_new = 1 ";
				
			 // echo $sql_6."<br>";
			  // echo $sql_6_plus."<br>";
		$base[$key]['lv_6']['amount'] = $based_new_nums[$cat_id];
		$base[$key]['lv_6']['confirm_amount'] = $GLOBALS['db']->getOne($sql_6_plus);
		$base[$key]['lv_6']['percent'] =round(($base[$key]['lv_6']['confirm_amount'] / $base[$key]['lv_6']['amount'] * 100),2);
		
		
	}
	
	$smarty->assign('data',   $base);
	$smarty->display('city_view.dwt');
	
}

/**
 * 动态确认率 显示页面
 */
elseif($_REQUEST['act'] == 'querenlv')
{
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	$date = date('Y-m-d H:i:s',(gmtime()+28800));
	$smarty->assign('date',   $date);
	
	$sql = "SELECT COUNT( * ) AS  amount,col_1 FROM ".$GLOBALS['ecs']->table('city')." GROUP BY col_1  ORDER BY  CONVERT( col_1 USING GBK )   ASC ";
	$base = $GLOBALS['db']->getAll($sql);
	//echo $sql."<br>";
	foreach($base AS $key => $value){
		
		$cat_id = get_cat_id_by_name($value['col_1']);
		$children = get_city_children(array($cat_id));
		
		$sql_4 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
						" WHERE $children  AND a.market_level = 4 ".
						" AND a.cat_id <= ". NEWCITYAFTERID ; //
						
				//echo $sql_4;
		$sql_4_plus = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city'). " AS c " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = c.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS cat ON cat.cat_id = c.city_id ". 
				" WHERE c.col_1 = '$value[col_1]'  AND cat.market_level = 4 ".
				" AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 ".
				" AND cat.cat_id <= " .NEWCITYAFTERID ." AND ad.is_new = 0 ";
				

		$base[$key]['lv_4']['amount'] = $GLOBALS['db']->getOne($sql_4);
		$base[$key]['lv_4']['confirm_amount'] = $GLOBALS['db']->getOne($sql_4_plus);
		if(!empty($base[$key]['lv_4']['confirm_amount']) || !empty($base[$key]['lv_4']['amount'])){
			$base[$key]['lv_4']['percent'] = round(($base[$key]['lv_4']['confirm_amount'] / $base[$key]['lv_4']['amount'] * 100),2);
		}
		
		
		$sql_5 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
				" WHERE $children  AND a.market_level = 5 ".
				" AND a.cat_id <= ". NEWCITYAFTERID ; //
				
		//echo $sql_5;
		$sql_5_plus = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city'). " AS c " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = c.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS cat ON cat.cat_id = c.city_id ". 
				" WHERE c.col_1 = '$value[col_1]'  AND cat.market_level = 5 ".
				" AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 ".
				" AND cat.cat_id <= " .NEWCITYAFTERID ." AND ad.is_new = 0 ";
		
		// echo $sql_5_plus."<br>";
		
				
		$base[$key]['lv_5']['amount'] = $GLOBALS['db']->getOne($sql_5);
		$base[$key]['lv_5']['confirm_amount'] = $GLOBALS['db']->getOne($sql_5_plus);
		if(!empty($base[$key]['lv_5']['confirm_amount']) || !empty($base[$key]['lv_5']['amount'])){
			$base[$key]['lv_5']['percent'] = round(($base[$key]['lv_5']['confirm_amount'] / $base[$key]['lv_5']['amount'] * 100),2);
		}
		
		$sql_6 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
				" WHERE $children  AND  a.market_level LIKE'%6%' ".
				" AND a.cat_id <= ". NEWCITYAFTERID ; //
		
		$sql_6_plus = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city'). " AS c " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = c.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS cat ON cat.cat_id = c.city_id ". 
				" WHERE c.col_1 LIKE '$value[col_1]'  AND cat.market_level LIKE'%6%'  ". // 
				" AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 ".
				" AND cat.cat_id <= " .NEWCITYAFTERID ." AND ad.is_new = 0 ";
				
			 // echo $sql_6."<br>";
			  // echo $sql_6_plus."<br>";
		$base[$key]['lv_6']['amount'] = $GLOBALS['db']->getOne($sql_6);
		$base[$key]['lv_6']['confirm_amount'] = $GLOBALS['db']->getOne($sql_6_plus);
		$base[$key]['lv_6']['percent'] =round(($base[$key]['lv_6']['confirm_amount'] / $base[$key]['lv_6']['amount'] * 100),2);
		
		
		
		$sql_7 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
				" WHERE $children  AND a.market_level LIKE'百强镇'".
				" AND a.cat_id <= ". NEWCITYAFTERID ; //
				
		
		$sql_7_plus = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city'). " AS c " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = c.ad_id ". 
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS cat ON cat.cat_id = c.city_id ". 
				" WHERE c.col_1 LIKE '$value[col_1]'  AND cat.market_level LIKE '百强镇'".
				" AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 ".
				" AND cat.cat_id <= " .NEWCITYAFTERID ." AND ad.is_new = 0 ";
				
				//echo $sql_7."<br>";
		$base[$key]['lv_7']['amount'] = $GLOBALS['db']->getOne($sql_7);
		$base[$key]['lv_7']['confirm_amount'] = $GLOBALS['db']->getOne($sql_7_plus);
		if(!empty($base[$key]['lv_7']['confirm_amount']) || !empty($base[$key]['lv_7']['amount'])){
			$base[$key]['lv_7']['percent'] =round(($base[$key]['lv_7']['confirm_amount'] / $base[$key]['lv_7']['amount'] * 100),2);
		}
		
		
	}
	
	$smarty->assign('data',   $base);
	$smarty->display('city_view.dwt');
	
}


/**
 * 动态确认率 显示页面
 */
elseif($_REQUEST['act'] == 'project_querenlv')
{
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 1;
	$smarty->assign('project_id',   $project_id);
	$date = date('Y-m-d H:i:s',(gmtime()+28800));
	$smarty->assign('date',   $date);
	
	
	$sql = "SELECT COUNT( * ) AS  amount,col_1 FROM ".$GLOBALS['ecs']->table('city')." GROUP BY col_1  ORDER BY  CONVERT( col_1 USING GBK )   ASC ";
	$base = $GLOBALS['db']->getAll($sql);
	//echo $sql."<br>";
	foreach($base AS $key => $value){
		
		$cat_id = get_cat_id_by_name($value['col_1']);
		$children = get_city_children(array($cat_id));
		
		/*4级城市*/		
		$sql_4 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
				" WHERE $children  AND a.market_level = 4 ";
		//echo $sql_4;
		$sql_4_upload = "SELECT g.ad_id FROM ".$GLOBALS['ecs']->table('city_gallery'). " AS g " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = g.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND g.feedback = '$project_id' AND a.market_level = 4 ".
				" GROUP BY g.ad_id ";
		//echo $sql_4_upload."<br>";

		$sql_4_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2 AND a.market_level = 4  ".
				" GROUP BY au.ad_id ";
		//echo $sql_4_plus."<br>";
		
				
		$base[$key]['lv_4']['amount'] = $GLOBALS['db']->getOne($sql_4);
		$base[$key]['lv_4']['upload_amount'] = count($GLOBALS['db']->getCol($sql_4_upload));
		$base[$key]['lv_4']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_4_plus));
		if(!empty($base[$key]['lv_4']['confirm_amount']) || !empty($base[$key]['lv_4']['amount'])){
			$base[$key]['lv_4']['upload_percent'] = round(($base[$key]['lv_4']['upload_amount'] / $base[$key]['lv_4']['amount'] * 100),2);
			if($base[$key]['lv_4']['upload_amount'] != 0){
				$base[$key]['lv_4']['confirm_percent'] = round(($base[$key]['lv_4']['confirm_amount'] / $base[$key]['lv_4']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_4']['confirm_percent'] = 0 ;
			}
		}
		
		/*5级城市*/		
		$sql_5 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
				" WHERE $children  AND a.market_level = 5 ";
		//echo $sql_5;
		$sql_5_upload = "SELECT g.ad_id FROM ".$GLOBALS['ecs']->table('city_gallery'). " AS g " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = g.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND g.feedback = '$project_id' AND a.market_level = 5 ".
				" GROUP BY g.ad_id ";
		//echo $sql_5_upload."<br>";

		$sql_5_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2 AND a.market_level = 5  ".
				" GROUP BY au.ad_id ";
		//echo $sql_5_plus."<br>";
		
				
		$base[$key]['lv_5']['amount'] = $GLOBALS['db']->getOne($sql_5);
		$base[$key]['lv_5']['upload_amount'] = count($GLOBALS['db']->getCol($sql_5_upload));
		$base[$key]['lv_5']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_5_plus));
		if(!empty($base[$key]['lv_5']['confirm_amount']) || !empty($base[$key]['lv_5']['amount'])){
			$base[$key]['lv_5']['upload_percent'] = round(($base[$key]['lv_5']['upload_amount'] / $base[$key]['lv_5']['amount'] * 100),2);
			if($base[$key]['lv_5']['upload_amount'] != 0){
				$base[$key]['lv_5']['confirm_percent'] = round(($base[$key]['lv_5']['confirm_amount'] / $base[$key]['lv_5']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_5']['confirm_percent'] = 0 ;
			}
		}
		
		/*6级城市*/
		$sql_6 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
				" WHERE $children  AND a.market_level LIKE'%6%' "; //
		
		$sql_6_upload = "SELECT g.ad_id FROM ".$GLOBALS['ecs']->table('city_gallery'). " AS g " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = g.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND g.feedback = '$project_id' AND a.market_level LIKE'%6%'  ".
				" GROUP BY g.ad_id ";
		//echo $sql_5_upload."<br>";

		$sql_6_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2 AND a.market_level LIKE'%6%' ".
				" GROUP BY au.ad_id ";
		//echo $sql_5_plus."<br>";
		//AND ( a.market_level LIKE'%6%'  OR a.market_level LIKE'%百强镇%' )  ".
				
		$base[$key]['lv_6']['amount'] = $GLOBALS['db']->getOne($sql_6);
		$base[$key]['lv_6']['upload_amount'] = count($GLOBALS['db']->getCol($sql_6_upload));
		$base[$key]['lv_6']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_6_plus));
		if(!empty($base[$key]['lv_6']['confirm_amount']) || !empty($base[$key]['lv_6']['amount'])){
			$base[$key]['lv_6']['upload_percent'] = round(($base[$key]['lv_6']['upload_amount'] / $base[$key]['lv_6']['amount'] * 100),2);
			if($base[$key]['lv_6']['upload_amount'] != 0){
				$base[$key]['lv_6']['confirm_percent'] = round(($base[$key]['lv_6']['confirm_amount'] / $base[$key]['lv_6']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_6']['confirm_percent'] = 0 ;
			}
		}
		
		
		/*单独百强镇*/
		$sql_7 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
				" WHERE $children  AND  a.market_level LIKE'%百强镇%' "; //

		$sql_7_upload = "SELECT g.ad_id FROM ".$GLOBALS['ecs']->table('city_gallery'). " AS g " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = g.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND g.feedback = '$project_id' AND  a.market_level LIKE'%百强镇%'  ".
				" GROUP BY g.ad_id ";
		//echo $sql_5_upload."<br>";

		$sql_7_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2 AND a.market_level LIKE'%百强镇%'  ".
				" GROUP BY au.ad_id ";
		//echo $sql_5_plus."<br>";


		$base[$key]['lv_7']['amount'] = $GLOBALS['db']->getOne($sql_7);
		$base[$key]['lv_7']['upload_amount'] = count($GLOBALS['db']->getCol($sql_7_upload));
		$base[$key]['lv_7']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_7_plus));
		if(!empty($base[$key]['lv_7']['confirm_amount']) || !empty($base[$key]['lv_7']['amount'])){
			$base[$key]['lv_7']['upload_percent'] = round(($base[$key]['lv_7']['upload_amount'] / $base[$key]['lv_7']['amount'] * 100),2);
			if($base[$key]['lv_7']['upload_amount'] != 0){
				$base[$key]['lv_7']['confirm_percent'] = round(($base[$key]['lv_7']['confirm_amount'] / $base[$key]['lv_7']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_7']['confirm_percent'] = 0 ;
			}
		}
		
		
	}
	
	$smarty->assign('data',   $base);
	$smarty->display('city_view.dwt');
	
}

/**
 * 动态确认率 显示页面
 */
elseif($_REQUEST['act'] == 'new_project_querenlv')
{
	if($_SESSION['user_rank'] < 4){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 2;
	$smarty->assign('project_id',   $project_id);
	$date = date('Y-m-d H:i:s',(gmtime()+28800));
	$smarty->assign('date',   $date);
	
	$based_new_nums = array();
	$based_new_nums = $_LANG['based_new_nums'];
	
	$sql = "SELECT COUNT( * ) AS  amount,col_1 FROM ".$GLOBALS['ecs']->table('city')." GROUP BY col_1  ORDER BY  CONVERT( col_1 USING GBK )   ASC ";
	$base = $GLOBALS['db']->getAll($sql);
	//echo $sql."<br>";
	foreach($base AS $key => $value){
		
		$cat_id = get_cat_id_by_name($value['col_1']);
		$children = get_city_children(array($cat_id));
		
		
		/*6级城市*/
		$sql_6 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
				" WHERE $children  AND a.market_level LIKE'%6%' "; //
		
		$sql_6_upload = "SELECT g.ad_id FROM ".$GLOBALS['ecs']->table('city_gallery'). " AS g " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = g.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND g.feedback = '$project_id'   ".
				" AND a.has_new = 1 AND ad.is_new = 1 ".
				" GROUP BY g.ad_id ";
		//echo $sql_5_upload."<br>";

		$sql_6_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a ON a.cat_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2  ".
				" AND a.has_new = 1 AND ad.is_new = 1 ".
				" GROUP BY au.ad_id ";
		//echo $sql_5_plus."<br>";
		//AND ( a.market_level LIKE'%6%'  OR a.market_level LIKE'%百强镇%' )  ".
				
		$base[$key]['lv_6']['amount'] = $based_new_nums[$cat_id];
		$base[$key]['lv_6']['upload_amount'] = count($GLOBALS['db']->getCol($sql_6_upload));
		$base[$key]['lv_6']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_6_plus));
		if(!empty($base[$key]['lv_6']['confirm_amount']) || !empty($base[$key]['lv_6']['amount'])){
			$base[$key]['lv_6']['upload_percent'] = round(($base[$key]['lv_6']['upload_amount'] / $base[$key]['lv_6']['amount'] * 100),2);
			if($base[$key]['lv_6']['upload_amount'] != 0){
				$base[$key]['lv_6']['confirm_percent'] = round(($base[$key]['lv_6']['confirm_amount'] / $base[$key]['lv_6']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_6']['confirm_percent'] = 0 ;
			}
		}
		
		
		
		
		
	}
	
	$smarty->assign('data',   $base);
	$smarty->display('city_view.dwt');
	
}


/**
 * 动态确认率 显示页面
 */
elseif($_REQUEST['act'] == 'base_info_querenlv')
{
	if($_SESSION['user_rank'] < 4 && $_SESSION['user_rank'] != 2  ){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 9;
	$smarty->assign('project_id',   $project_id);
	$date = date('Y-m-d H:i:s',(gmtime()+28800));
	$smarty->assign('date',   $date);
	
	
	$sql = "SELECT COUNT( * ) AS  amount,col_1 FROM ".$GLOBALS['ecs']->table('city')." GROUP BY col_1  ORDER BY  CONVERT( col_1 USING GBK )   ASC ";
	$base = $GLOBALS['db']->getAll($sql);
	//echo $sql."<br>";
	foreach($base AS $key => $value){
		
		$cat_id = get_cat_id_by_name($value['col_1']);
		$children = get_city_children_a(array($cat_id));
		
		/*4级城市*/		
		$sql_4 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children  AND a.market_level = 4 ";
		//echo $sql_4;
		
		$sql_4_upload = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.base_info_changed = 1 AND a.market_level = 4 ".
				" GROUP BY ad.ad_id ";

		$sql_4_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2 AND a.market_level = 4  ".
				" GROUP BY au.ad_id ";
		//echo $sql_4_plus."<br>";
		
		$sql_4_plus_2 = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.user_rank = 2 AND a.market_level = 4  ".
				" GROUP BY au.ad_id ";
		//
		$sql_4_plus_3 = "SELECT ma.ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
		 		" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_material') . " AS ma ON ma.ad_id = ad.ad_id ".
				" WHERE $children AND ma.is_send = 1 AND a.market_level  = 4 ".
				" GROUP BY ad.ad_id ";
		//
		$sql_4_plus_4 = "SELECT ma.ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
		 		" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_material') . " AS ma ON ma.ad_id = ad.ad_id ".
				" WHERE $children AND ma.is_receive = 1 AND a.market_level = 4 ".
				" GROUP BY ad.ad_id ";
				
		$base[$key]['lv_4']['amount'] = $GLOBALS['db']->getOne($sql_4);
		$base[$key]['lv_4']['upload_amount'] = count($GLOBALS['db']->getCol($sql_4_upload));
		$base[$key]['lv_4']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_4_plus));
		$base[$key]['lv_4']['audit_amount'] = count($GLOBALS['db']->getCol($sql_4_plus_2));
		$base[$key]['lv_4']['send_amount'] = count($GLOBALS['db']->getCol($sql_4_plus_3));
		$base[$key]['lv_4']['receive_amount'] = count($GLOBALS['db']->getCol($sql_4_plus_4));
		if(!empty($base[$key]['lv_4']['confirm_amount']) || !empty($base[$key]['lv_4']['amount'])){
			$base[$key]['lv_4']['upload_percent'] = round(($base[$key]['lv_4']['upload_amount'] / $base[$key]['lv_4']['amount'] * 100),2);
			if($base[$key]['lv_4']['upload_amount'] != 0){
				$base[$key]['lv_4']['confirm_percent'] = round(($base[$key]['lv_4']['confirm_amount'] / $base[$key]['lv_4']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_4']['confirm_percent'] = 0 ;
			}
		}
		
		/*5级城市*/		
		$sql_5 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children  AND a.market_level = 5 ";
		//echo $sql_5;
		
		$sql_5_upload = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.base_info_changed = 1 AND a.market_level = 5 ".
				" GROUP BY ad.ad_id ";
		//echo $sql_5_upload."<br>";

		$sql_5_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2 AND a.market_level = 5  ".
				" GROUP BY au.ad_id ";
		//echo $sql_5_plus."<br>";
		$sql_5_plus_2 = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.user_rank = 2 AND a.market_level = 5  ".
				" GROUP BY au.ad_id ";
		//
		$sql_5_plus_3 = "SELECT ma.ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
		 		" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_material') . " AS ma ON ma.ad_id = ad.ad_id ".
				" WHERE $children AND ma.is_send = 1 AND a.market_level = 5 ".
				" GROUP BY ad.ad_id ";
		//
		$sql_5_plus_4 = "SELECT ma.ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
		 		" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_material') . " AS ma ON ma.ad_id = ad.ad_id ".
				" WHERE $children AND ma.is_receive = 1 AND a.market_level  = 5 ".
				" GROUP BY ad.ad_id ";
		
		$base[$key]['lv_5']['amount'] = $GLOBALS['db']->getOne($sql_5);
		$base[$key]['lv_5']['upload_amount'] = count($GLOBALS['db']->getCol($sql_5_upload));
		$base[$key]['lv_5']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_5_plus));
		$base[$key]['lv_5']['audit_amount'] = count($GLOBALS['db']->getCol($sql_5_plus_2));
		$base[$key]['lv_5']['send_amount'] = count($GLOBALS['db']->getCol($sql_5_plus_3));
		$base[$key]['lv_5']['receive_amount'] = count($GLOBALS['db']->getCol($sql_5_plus_4));
		if(!empty($base[$key]['lv_5']['confirm_amount']) || !empty($base[$key]['lv_5']['amount'])){
			$base[$key]['lv_5']['upload_percent'] = round(($base[$key]['lv_5']['upload_amount'] / $base[$key]['lv_5']['amount'] * 100),2);
			if($base[$key]['lv_5']['upload_amount'] != 0){
				$base[$key]['lv_5']['confirm_percent'] = round(($base[$key]['lv_5']['confirm_amount'] / $base[$key]['lv_5']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_5']['confirm_percent'] = 0 ;
			}
		}
		
		/*6级城市*/
		$sql_6 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children  AND a.market_level LIKE'%6%' "; //
		
		$sql_6_upload = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.base_info_changed = 1 AND  a.market_level LIKE'%6%'  ".
				" GROUP BY ad.ad_id ";
				
		//echo $sql_5_upload."<br>";

		$sql_6_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2 AND a.market_level LIKE'%6%' ".
				" GROUP BY au.ad_id ";
		//echo $sql_5_plus."<br>";
		//AND ( a.market_level LIKE'%6%'  OR a.market_level LIKE'%百强镇%' )  ".
		
		$sql_6_plus_2 = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.user_rank = 2 AND a.market_level LIKE'%6%' ".
				" GROUP BY au.ad_id ";

				
		$sql_6_plus_3 = "SELECT ma.ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
		 		" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_material') . " AS ma ON ma.ad_id = ad.ad_id ".
				" WHERE $children AND ma.is_send = 1 AND a.market_level LIKE'%6%' ".
				" GROUP BY ad.ad_id ";
		//
		$sql_6_plus_4 = "SELECT ma.ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
		 		" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_material') . " AS ma ON ma.ad_id = ad.ad_id ".
				" WHERE $children AND ma.is_receive = 1 AND a.market_level LIKE'%6%' ".
				" GROUP BY ad.ad_id ";
				
								
		$base[$key]['lv_6']['amount'] = $GLOBALS['db']->getOne($sql_6);
		$base[$key]['lv_6']['upload_amount'] = count($GLOBALS['db']->getCol($sql_6_upload));
		$base[$key]['lv_6']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_6_plus));
		$base[$key]['lv_6']['audit_amount'] = count($GLOBALS['db']->getCol($sql_6_plus_2));
		$base[$key]['lv_6']['send_amount'] = count($GLOBALS['db']->getCol($sql_6_plus_3));
		$base[$key]['lv_6']['receive_amount'] = count($GLOBALS['db']->getCol($sql_6_plus_4));
		// 
		if(!empty($base[$key]['lv_6']['confirm_amount']) || !empty($base[$key]['lv_6']['amount'])){
			$base[$key]['lv_6']['upload_percent'] = round(($base[$key]['lv_6']['upload_amount'] / $base[$key]['lv_6']['amount'] * 100),2);
			if($base[$key]['lv_6']['upload_amount'] != 0){
				$base[$key]['lv_6']['confirm_percent'] = round(($base[$key]['lv_6']['confirm_amount'] / $base[$key]['lv_6']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_6']['confirm_percent'] = 0 ;
			}
		}
		
		
		/*单独百强镇*/
		$sql_7 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children  AND  a.market_level LIKE'%百强镇%' "; //

		//
		$sql_7_upload = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.base_info_changed = 1 AND  a.market_level LIKE'%百强镇%'  ".
				" GROUP BY ad.ad_id ";

		$sql_7_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2 AND a.market_level LIKE'%百强镇%'  ".
				" GROUP BY au.ad_id ";
		//echo $sql_5_plus."<br>";
		$sql_7_plus_2 = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.user_rank = 2 AND a.market_level LIKE'%百强镇%'  ".
				" GROUP BY au.ad_id ";
		//
		$sql_7_plus_3 = "SELECT ma.ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
		 		" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_material') . " AS ma ON ma.ad_id = ad.ad_id ".
				" WHERE $children AND ma.is_send = 1 AND a.market_level LIKE'%百强镇%' ".
				" GROUP BY ad.ad_id ";
		//
		$sql_7_plus_4 = "SELECT ma.ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
		 		" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_material') . " AS ma ON ma.ad_id = ad.ad_id ".
				" WHERE $children AND ma.is_receive = 1 AND a.market_level LIKE'%百强镇%' ".
				" GROUP BY ad.ad_id ";
		

		$base[$key]['lv_7']['amount'] = $GLOBALS['db']->getOne($sql_7);
		$base[$key]['lv_7']['upload_amount'] = count($GLOBALS['db']->getCol($sql_7_upload));
		$base[$key]['lv_7']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_7_plus));
		$base[$key]['lv_7']['audit_amount'] = count($GLOBALS['db']->getCol($sql_7_plus_2));
		$base[$key]['lv_7']['send_amount'] = count($GLOBALS['db']->getCol($sql_7_plus_3));
		$base[$key]['lv_7']['receive_amount'] = count($GLOBALS['db']->getCol($sql_7_plus_4));
		if(!empty($base[$key]['lv_7']['confirm_amount']) || !empty($base[$key]['lv_7']['amount'])){
			$base[$key]['lv_7']['upload_percent'] = round(($base[$key]['lv_7']['upload_amount'] / $base[$key]['lv_7']['amount'] * 100),2);
			if($base[$key]['lv_7']['upload_amount'] != 0){
				$base[$key]['lv_7']['confirm_percent'] = round(($base[$key]['lv_7']['confirm_amount'] / $base[$key]['lv_7']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_7']['confirm_percent'] = 0 ;
			}
		}
		
		
	}
	
	$smarty->assign('data',   $base);
	$smarty->display('base_info_view.dwt');
	
}

elseif($_REQUEST['act'] == 'new_base_info_querenlv')
{
	if($_SESSION['user_rank'] < 4 && $_SESSION['user_rank'] != 2  ){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	
	$based_new_nums = array();
	$based_new_nums = $_LANG['based_new_nums'];
	
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 9;
	$smarty->assign('project_id',   $project_id);
	$date = date('Y-m-d H:i:s',(gmtime()+28800));
	$smarty->assign('date',   $date);
	
	
	$sql = "SELECT COUNT( * ) AS  amount,col_1 FROM ".$GLOBALS['ecs']->table('city')." GROUP BY col_1  ORDER BY  CONVERT( col_1 USING GBK )   ASC ";
	$base = $GLOBALS['db']->getAll($sql);
	//echo $sql."<br>";
	foreach($base AS $key => $value){
		
		$cat_id = get_cat_id_by_name($value['col_1']);
		$children = get_city_children_a(array($cat_id));
		
		/*6级城市*/
		$sql_6 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children  AND a.market_level LIKE'%6%' "; //
		
		$sql_6_upload = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.base_info_changed = 1 ".
				" AND ad.is_new = 1 ".
				" GROUP BY ad.ad_id ";
				
		// echo $sql_6_upload."<br>";

		$sql_6_plus = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.audit_note = '审核通过' AND au.user_rank = 2  ".
				" AND ad.is_new = 1 ".
				" GROUP BY au.ad_id ";
		//echo $sql_5_plus."<br>";
		//AND ( a.market_level LIKE'%6%'  OR a.market_level LIKE'%百强镇%' )  ".
		
		$sql_6_plus_2 = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " AS au " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = au.ad_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND au.feedback_audit = '$project_id' AND au.user_rank = 2  ".
				" AND ad.is_new = 1 ".
				" GROUP BY au.ad_id ";
				
		$base[$key]['lv_6']['amount'] = $based_new_nums[$cat_id];
		$base[$key]['lv_6']['upload_amount'] = count($GLOBALS['db']->getCol($sql_6_upload));
		$base[$key]['lv_6']['confirm_amount'] = count($GLOBALS['db']->getCol($sql_6_plus));
		$base[$key]['lv_6']['audit_amount'] = count($GLOBALS['db']->getCol($sql_6_plus_2));
		if(!empty($base[$key]['lv_6']['confirm_amount']) || !empty($base[$key]['lv_6']['amount'])){
			$base[$key]['lv_6']['upload_percent'] = round(($base[$key]['lv_6']['upload_amount'] / $base[$key]['lv_6']['amount'] * 100),2);
			if($base[$key]['lv_6']['upload_amount'] != 0){
				$base[$key]['lv_6']['confirm_percent'] = round(($base[$key]['lv_6']['confirm_amount'] / $base[$key]['lv_6']['upload_amount'] * 100),2);
			}else{
				$base[$key]['lv_6']['confirm_percent'] = 0 ;
			}
		}		
		
	}
	
	$smarty->assign('data',   $base);
	$smarty->display('base_info_view.dwt');
	
}

/**
 * 动态确认率 显示页面
 */
elseif($_REQUEST['act'] == 'audit_status_summary')
{
	if($_SESSION['user_rank'] < 4 && $_SESSION['user_rank'] != 2  ){
		show_message("权限不够", $_LANG['profile_lnk'], 'city_operate.php', 'info', true);        
	}
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 9;
	$smarty->assign('project_id',   $project_id);
	$date = date('Y-m-d H:i:s',(gmtime()+28800));
	$smarty->assign('date',   $date);
	
	
	$sql = "SELECT COUNT( * ) AS  amount,col_1 FROM ".$GLOBALS['ecs']->table('city')." GROUP BY col_1  ORDER BY  CONVERT( col_1 USING GBK )   ASC ";
	$base = $GLOBALS['db']->getAll($sql);
	//echo $sql."<br>";
	foreach($base AS $key => $value){
		
		$cat_id = get_cat_id_by_name($value['col_1']);
		$children = get_city_children_a(array($cat_id));
		
		/*4级城市*/		
		$sql_4 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children  AND a.market_level = 4 ";
		//echo $sql_4;
		
		$sql_4_upload = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND a.market_level = 4 ".
				" GROUP BY ad.ad_id ";

		//
		$sql_4_plus_1 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 1 AND ad.is_audit_confirm = 0 AND a.market_level = 4 ".
				" GROUP BY ad.ad_id ";
		//
		$sql_4_plus_2 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 AND a.market_level = 4 ".
				" GROUP BY ad.ad_id ";
				
		$sql_4_plus_3 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 5 AND ad.is_audit_confirm = 0 AND a.market_level = 4 ".
				" GROUP BY ad.ad_id ";
		
		$base[$key]['lv_4']['amount'] = $GLOBALS['db']->getOne($sql_4);		
		$base[$key]['lv_4']['upload_amount'] = count($GLOBALS['db']->getCol($sql_4_upload));
		$base[$key]['lv_4']['wait_amount'] = count($GLOBALS['db']->getCol($sql_4_plus_1));
		$base[$key]['lv_4']['pass_amount'] = count($GLOBALS['db']->getCol($sql_4_plus_2));
		$base[$key]['lv_4']['unpass_amount'] = count($GLOBALS['db']->getCol($sql_4_plus_3));
		$base[$key]['lv_4']['upload_amount'] = $base[$key]['lv_4']['upload_amount'] > $base[$key]['lv_4']['amount'] ? $base[$key]['lv_4']['amount']: $base[$key]['lv_4']['upload_amount'];
		$base[$key]['lv_4']['wait_amount'] = ($base[$key]['lv_4']['pass_amount'] == $base[$key]['lv_4']['amount']) ? 0 : $base[$key]['lv_4']['wait_amount'];
		
		
		//#############################//
//
/*5级城市*/		
		$sql_5 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children  AND a.market_level = 5 ";
		//echo $sql_5;

		$sql_5_upload = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND a.market_level = 5 ".
				" GROUP BY ad.ad_id ";

		//
		$sql_5_plus_1 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 1 AND ad.is_audit_confirm = 0 AND a.market_level = 5 ".
				" GROUP BY ad.ad_id ";
		//
		$sql_5_plus_2 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 AND a.market_level = 5 ".
				" GROUP BY ad.ad_id ";

		$sql_5_plus_3 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 5 AND ad.is_audit_confirm = 0 AND a.market_level = 5 ".
				" GROUP BY ad.ad_id ";

		$base[$key]['lv_5']['amount'] = $GLOBALS['db']->getOne($sql_5);		
		$base[$key]['lv_5']['upload_amount'] = count($GLOBALS['db']->getCol($sql_5_upload));
		$base[$key]['lv_5']['wait_amount'] = count($GLOBALS['db']->getCol($sql_5_plus_1));
		$base[$key]['lv_5']['pass_amount'] = count($GLOBALS['db']->getCol($sql_5_plus_2));
		$base[$key]['lv_5']['unpass_amount'] = count($GLOBALS['db']->getCol($sql_5_plus_3));
		$base[$key]['lv_5']['upload_amount'] = $base[$key]['lv_5']['upload_amount'] > $base[$key]['lv_5']['amount'] ? $base[$key]['lv_5']['amount']: $base[$key]['lv_5']['upload_amount'];
		$base[$key]['lv_5']['wait_amount'] = ($base[$key]['lv_5']['pass_amount'] == $base[$key]['lv_5']['amount']) ? 0 : $base[$key]['lv_5']['wait_amount'];
		
			//
			/*6级城市*/		
		$sql_6 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children  AND a.market_level = 6 ";
		//echo $sql_6;
		
		$sql_6_upload = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND a.market_level LIKE'%6%'   ".
				" GROUP BY ad.ad_id ";

		//
		$sql_6_plus_1 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 1 AND ad.is_audit_confirm = 0 AND a.market_level LIKE'%6%'  ".
				" GROUP BY ad.ad_id ";
		//
		$sql_6_plus_2 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 AND a.market_level LIKE'%6%'  ".
				" GROUP BY ad.ad_id ";
				
		$sql_6_plus_3 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 5 AND ad.is_audit_confirm = 0 AND a.market_level LIKE'%6%' ".
				" GROUP BY ad.ad_id ";
		
		$base[$key]['lv_6']['amount'] = $GLOBALS['db']->getOne($sql_6);		
		$base[$key]['lv_6']['upload_amount'] = count($GLOBALS['db']->getCol($sql_6_upload));
		$base[$key]['lv_6']['wait_amount'] = count($GLOBALS['db']->getCol($sql_6_plus_1));
		$base[$key]['lv_6']['pass_amount'] = count($GLOBALS['db']->getCol($sql_6_plus_2));
		$base[$key]['lv_6']['unpass_amount'] = count($GLOBALS['db']->getCol($sql_6_plus_3));
		$base[$key]['lv_6']['upload_amount'] = $base[$key]['lv_6']['upload_amount'] > $base[$key]['lv_6']['amount'] ? $base[$key]['lv_6']['amount']: $base[$key]['lv_6']['upload_amount'];
		$base[$key]['lv_6']['wait_amount'] = ($base[$key]['lv_6']['pass_amount'] == $base[$key]['lv_6']['amount']) ? 0 : $base[$key]['lv_6']['wait_amount'];
		
	//
	/*7级城市*/		
		$sql_7 = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children  AND a.market_level = 7 ";
		//echo $sql_7;
		
		$sql_7_upload = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND a.market_level LIKE'%百强镇%'   ".
				" GROUP BY ad.ad_id ";

		//
		$sql_7_plus_1 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 1 AND ad.is_audit_confirm = 0 AND a.market_level LIKE'%百强镇%'  ".
				" GROUP BY ad.ad_id ";
		//
		$sql_7_plus_2 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 AND a.market_level LIKE'%百强镇%'  ".
				" GROUP BY ad.ad_id ";
				
		$sql_7_plus_3 = "SELECT ad.ad_id  FROM ".$GLOBALS['ecs']->table('city_ad'). " AS ad " .
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') . " AS a ON a.city_id = ad.city_id ". 
				" WHERE $children AND ad.is_upload = 1 AND ad.audit_status = 5 AND ad.is_audit_confirm = 0 AND a.market_level LIKE'%百强镇%' ".
				" GROUP BY ad.ad_id ";
		
		$base[$key]['lv_7']['amount'] = $GLOBALS['db']->getOne($sql_7);		
		$base[$key]['lv_7']['upload_amount'] = count($GLOBALS['db']->getCol($sql_7_upload));
		$base[$key]['lv_7']['wait_amount'] = count($GLOBALS['db']->getCol($sql_7_plus_1));
		$base[$key]['lv_7']['pass_amount'] = count($GLOBALS['db']->getCol($sql_7_plus_2));
		$base[$key]['lv_7']['unpass_amount'] = count($GLOBALS['db']->getCol($sql_7_plus_3));
		$base[$key]['lv_7']['upload_amount'] = $base[$key]['lv_7']['upload_amount'] > $base[$key]['lv_7']['amount'] ? $base[$key]['lv_7']['amount']: $base[$key]['lv_7']['upload_amount'];
		$base[$key]['lv_7']['wait_amount'] = ($base[$key]['lv_7']['pass_amount'] == $base[$key]['lv_7']['amount']) ? 0 : $base[$key]['lv_7']['wait_amount'];
		
	



	}

	$smarty->assign('data',   $base);
	$smarty->display('city_view.dwt');

}


?>