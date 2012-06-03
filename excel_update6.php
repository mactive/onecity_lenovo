<?php
/**
 * SINEMALL 品牌列表 * $Author: testyang $
 * $Id: brand.php 14641 2008-06-04 06:15:32Z testyang $
*/


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_city.php');
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


/* all array */
$exist_array = array();
//$sichuan =  array_keys(cat_list(18, 0, false,4)); //四川ID 18
//print_r($sichuan);

/*
$children = get_city_children(array('18'));

$sql_5 = "SELECT cat_id FROM ".$GLOBALS['ecs']->table($GLOBALS['year']."_".'category'). " AS a " .
		" WHERE $children  AND a.sys_level = 5 ";
$sichuan = $GLOBALS['db']->getCol($sql_5);
//print_r($sichuan);
echo count($sichuan);
*/

for ($y=0;$y<count($all_sheets);$y++) 
{ 
	echo "sheet: $y  "." row_count:".$all_sheets[$y]['numRows']."    col_count:".$all_sheets[$y]['numCols']."<br>";
	$count = 0;

	
	for ($row=1;$row<=$all_sheets[$y]['numRows'];$row++) 
	{
		// $city = trim($all_sheets[$y]['cells'][$row][4]);
		// $county = trim($all_sheets[$y]['cells'][$row][5]);
		// $level = trim($all_sheets[$y]['cells'][$row][6]);
		// $mature_emerging = trim($all_sheets[$y]['cells'][$row][8]);
		// $mulching = trim($all_sheets[$y]['cells'][$row][9]);
		
		// $region = trim($all_sheets[$y]['cells'][$row][1]);
		// $province = trim($all_sheets[$y]['cells'][$row][2]);
		$region = trim($all_sheets[$y]['cells'][$row][1]);
		$sn = trim($all_sheets[$y]['cells'][$row][2]);
		$name = trim($all_sheets[$y]['cells'][$row][3]);
		
		$region_id = get_region_id($region);
		
		echo $region_id."-".$sn."-".$name;
		
		
		
		// SMB
		if(!empty($region_id)){
			
			$is_dealer = stripos($sn,'K') === 0 ? 1 : 0;
			echo " ==$is_dealer== "."<br>";
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table('city_dealer') .
					" (`dealer_sn`,`dealer_name`,`region_id`,`is_dealer` ) ".
					"VALUES ('$sn', '$name', '$region_id','$is_dealer')";
			//echo $sql."<br>";
			
			$GLOBALS['db']->query($sql);
			
		$count = $count + 1; 			
			
		}else{
			echo $county."不存在<br>";
		}
	}
	echo "===========".$count."<br>";
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
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'category') .
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
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'category') .
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
		
		$sql = "INSERT INTO " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'category') .
				" (`cat_id`,`cat_name`,`parent_id` ) VALUES (NULL, '$tt[1]', '$region_id')";
		echo $sql."<br>";
	    //$GLOBALS['db']->query($sql);
	}
}

function get_cat_id($cat_name){
	$sql = 'SELECT cat_id FROM ' .$GLOBALS['ecs']->table($GLOBALS['year']."_".'category').
           " WHERE cat_name LIKE '%$cat_name%' ";
	
    return $GLOBALS['db']->getOne($sql);
	
}

function get_region_id($cat_name){
	$sql = 'SELECT cat_id FROM ' .$GLOBALS['ecs']->table($GLOBALS['year']."_".'category').
           " WHERE cat_name LIKE '%$cat_name%' AND  parent_id = 1 ";
	
    return $GLOBALS['db']->getOne($sql);
	
}


?>

