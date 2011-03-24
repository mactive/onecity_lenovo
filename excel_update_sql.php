<?php
/**
 * SINEMALL 品牌列表 * $Author: testyang $
 * $Id: brand.php 14641 2008-06-04 06:15:32Z testyang $
*/


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

ini_set("display_errors",1);
error_reporting(E_ALL);

$last_update = gmtime();


	$sql_from = "SELECT * FROM " .$GLOBALS['ecs']->table('goods_product'). 
           "WHERE goods_id >=1 ";
	$from = $GLOBALS['db']->getAll($sql_from);
	echo $sql_from."<br>";
	echo "rows:".count($from)."<br>";
	 
	foreach($from AS $val){
		$val['add_time'] = local_strtotime($val['add_time']);
		echo $val['add_time']."<br>";
		
		$sql = "INSERT INTO ".$ecs->table('goods').
				"(goods_id, goods_name, goods_shortname, goods_sn, is_real, cat_id, brand_id, goods_zhibao, goods_desc, goods_target, goods_thumb, goods_img, original_img, is_best, goods_status, add_time, click_count) ".
	           "VALUES ($val[goods_id], '$val[goods_name]', '$val[goods_shortname]', '$val[goods_sn]', '$val[is_real]', '$val[cat_id]', '$val[brand_id]', '$val[goods_zhibao]', '$val[goods_desc]', '$val[goods_target]', '$val[goods_thumb]', '$val[goods_img]', '$val[original_img]', '$val[is_best]', '$val[goods_status]', '$val[add_time]', '$val[click_count]')";
		//echo $sql."<br>";
		$GLOBALS['db']->query($sql);	    
	}
			


?>

