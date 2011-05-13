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
//$xls->setOutputEncoding('gb2312');
$xls->setOutputEncoding('utf-8');
$file_path = "data/excel/".$file_name.".xls";

$xls->read($file_path);
$all_sheets = $xls->sheets;
$last_update = gmtime();


$start_time = gmtime();
$action_note = '批量入库';
$status = 1;

for ($y=0;$y<count($all_sheets);$y++) 
{ 
	echo "sheet: $y  "." row_count:".$all_sheets[$y]['numRows']."    col_count:".$all_sheets[$y]['numCols']."<br>";
	$province_array = array();
	$city_array = array();
	$county_array = array();
	
	for ($row=2;$row<=$all_sheets[$y]['numRows'];$row++) 
	{

		$big_region = trim($all_sheets[$y]['cells'][$row][1]);
		$region = trim($all_sheets[$y]['cells'][$row][2]);
		$province = trim($all_sheets[$y]['cells'][$row][3]);
		$city = trim($all_sheets[$y]['cells'][$row][4]);
		$county = trim($all_sheets[$y]['cells'][$row][5]);
		$level = trim($all_sheets[$y]['cells'][$row][6]);
		$market_level = trim($all_sheets[$y]['cells'][$row][7]);
		$mature_emerging = trim($all_sheets[$y]['cells'][$row][8]);
		$mulching = trim($all_sheets[$y]['cells'][$row][9]);

		//echo $big_region ."-". $region."-".$province."-".$city."-".$county."-".$level."-".$level."-".$market_level."-".$mature_emerging."-".$mulching."<br>";
		//各种数组
		//array_push($province_array,$region."-".$province);
		//array_push($city_array,$province."-".$city);
		array_push($county_array,$city."-".$county."-".$level."-".$market_level."-".$mature_emerging."-".$mulching);
		

	}
	
	//$province_array_2 = array_unique($province_array);
	//act_province_array($province_array_2);
	
	//$city_array_2 = array_unique($city_array);
	//act_city_array($city_array_2);
	
	$county_array_2 = array_unique($county_array);
	act_county_array($county_array_2);
	
}
/* 修改库存和平均成本价 
*  输入 part_number
*  操作 goods 库 修改库存数量和 成本平均价格
*/
function act_county_array($array)
{
	foreach($array AS $key => $val){
		$tt = explode("-",$val);
		$region_id = get_cat_id($tt[0]);
		if($region_id){
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table('category') .
					" (`cat_id`,`cat_name`,`parent_id`,`level`,`market_level`,`mature_emerging`,`mulching` ) ".
					"VALUES (NULL, '$tt[1]', '$region_id','$tt[2]','$tt[3]','$tt[4]','$tt[5]')";
			echo $sql."<br>";
		    //	$GLOBALS['db']->query($sql);
		}else{
			print_r($tt);
			echo "error<br>";
		}		
	}
}

function act_city_array($array)
{
	foreach($array AS $key => $val){
		$tt = explode("-",$val);
		$region_id = get_cat_id($tt[0]);
		if($region_id){
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table('category') .
					" (`cat_id`,`cat_name`,`parent_id` ) VALUES (NULL, '$tt[1]', '$region_id')";
			echo $sql."<br>";
		    //$GLOBALS['db']->query($sql);
		}else{
			print_r($tt);
			echo "error<br>";
		}		
	}
}

function act_province_array($province_array)
{
	foreach($province_array AS $key => $val){
		$tt = explode("-",$val);
		$region_id = get_cat_id($tt[0]);
		
		$sql = "INSERT INTO " . $GLOBALS['ecs']->table('category') .
				" (`cat_id`,`cat_name`,`parent_id` ) VALUES (NULL, '$tt[1]', '$region_id')";
		echo $sql."<br>";
	    //$GLOBALS['db']->query($sql);
	}
}

function get_cat_id($cat_name){
	$sql = 'SELECT cat_id FROM ' .$GLOBALS['ecs']->table('category').
           " WHERE cat_name = '$cat_name' ";
	
    return $GLOBALS['db']->getOne($sql);
	
}
/*
* 当前part_number 的库存数量
* o.status_id != 2  库存状态 不是 出库 
* @param   array   $get_inventory_info  订单信息
*/
function goods_group_by_part_number($part_number)
{
	
	$sql = "SELECT 'o.part_number',COUNT(o.part_number) AS goods_number, SUM(o.inv_price) AS price_amount, g.goods_id FROM ".
	 		$GLOBALS['ecs']->table('inventory') . " AS o ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS g ON g.part_number=o.part_number " .
			"WHERE o.part_number LIKE '%" . $part_number . "%'". "AND o.status_id != 2 ".
			"GROUP BY o.part_number";
			
	$result =$GLOBALS['db']->getRow($sql);
	//echo $sql;
	//print_r($result);
	return $result;
}


?>

