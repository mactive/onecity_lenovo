<?php
/**
 * SINEMALL 品牌列表 * $Author: testyang $
 * $Id: brand.php 14641 2008-06-04 06:15:32Z testyang $
*/


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/excel_reader2.php');

$file_name = $_REQUEST['file_name'];
//$array1 = get_my_goods_list();
//$array2 = get_my_brand_list();
//$tb = get_my_tb_ys_list();
//$items = array_merge($array1, $array2);

ini_set("display_errors",1);
error_reporting(E_ALL);

$xls = new Spreadsheet_Excel_Reader();
$xls->setOutputEncoding('gb2312');
$file_path = "data/excel/".$file_name.".xls";

$xls->read($file_path);
$all_sheets = $xls->sheets;
$last_update = gmtime();


for ($y=0;$y<count($all_sheets);$y++) 
{ 
	echo "sheet: $y  "." row_count:".$all_sheets[$y]['numRows']."    col_count:".$all_sheets[$y]['numCols']."<br>";
	
	for ($row=2;$row<=$all_sheets[$y]['numRows'];$row++) 
	{

		$goods_id = intval($all_sheets[$y]['cells'][$row][1]);
		//$goods_number = intval($all_sheets[$y]['cells'][$row][3]);
		//$promote_price = intval($all_sheets[$y]['cells'][$row][4]);
		//$promote_start_date = '1239004800';
		//$promote_end_date = '1241856000';
		//$average_price = intval($all_sheets[$y]['cells'][$row][3]);
		$goods_name = trim($all_sheets[$y]['cells'][$row][2]);
		
		$agency_price = intval($all_sheets[$y]['cells'][$row][3]);
		$salebase_price = intval($all_sheets[$y]['cells'][$row][4]);
		$shop_price = intval($all_sheets[$y]['cells'][$row][5]);
		$market_price = intval($all_sheets[$y]['cells'][$row][6]);
		
		//$goods_number = intval($all_sheets[$y]['cells'][$row][4]);

			/*
			//更新产品价格
			$sql = "UPDATE " .$GLOBALS['ecs']->table('goods'). " SET ".
		           "salebase_price  = '$salebase_price', ".
		           "shop_price     	= '$shop_price', ".
				   "last_update 	= '$last_update' ".
		           "WHERE goods_id = '$goods_id' LIMIT 1";
			

			echo $sql."<br>";
		    $GLOBALS['db']->query($sql);
			
			//显示 剔除产品
			$sql2 = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('goods'). 
		           "WHERE goods_id = '$goods_id' LIMIT 1";
		
			$count = $GLOBALS['db']->getOne($sql2);
		    if($count<1){
				echo $goods_id."  : ".$goods_name." : ".$salebase_price."<br>";
				
			}
			*/
			//更新产品价格
			$sql_me = "UPDATE " .$GLOBALS['ecs']->table('goods'). " SET ".
		           "agency_price     	= '$agency_price', ".
		           "salebase_price     	= '$salebase_price', ".
		           "shop_price     		= '$shop_price', ".
		           "market_price     	= '$market_price', ".
				   "last_update 	= '$last_update' ".
		           "WHERE goods_id = '$goods_id' LIMIT 1";
			

			echo $sql_me."<br>";
		    $GLOBALS['db']->query($sql_me);
			
			/*
			$sql3 = "UPDATE " .$GLOBALS['ecs']->table('goods'). " SET ".
		           "promote_price  = '$promote_price', ".
		           "is_promote  = '1', ".
		           "promote_start_date  = '$promote_start_date', ".
		           "promote_end_date  = '$promote_end_date', ".
		           "goods_number  = '$goods_number', ".
				   "last_update 	= '$last_update' ".
		           "WHERE goods_id = '$goods_id' LIMIT 1";
			

			echo $sql3."<br>";
		    $GLOBALS['db']->query($sql3);
			*/
			//UPDATE `sinemall`.`sm_goods` SET average_price = '4500', last_update = '1238059618' WHERE goods_id = '4107' LIMIT 1
	}
}

?>

