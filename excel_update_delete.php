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

$sql_5 = "SELECT cat_id FROM ".$GLOBALS['ecs']->table('category'). " AS a " .
		" WHERE $children  AND a.sys_level = 5 ";
$sichuan = $GLOBALS['db']->getCol($sql_5);
//print_r($sichuan);
echo count($sichuan);
*/

for ($y=0;$y<count($all_sheets);$y++) 
{ 
	echo "sheet: $y  "." row_count:".$all_sheets[$y]['numRows']."    col_count:".$all_sheets[$y]['numCols']."<br>";
	$count = 0;

	
	for ($row=2;$row<=$all_sheets[$y]['numRows'];$row++) 
	{
		//$city = trim($all_sheets[$y]['cells'][$row][4]);
		$county = trim($all_sheets[$y]['cells'][$row][4]);
		
		//$parent_id = get_cat_id_by_name($city);
		$city_id = get_cat_id_by_name($county);
		$sys_level = get_sys_level($city_id);
		
		echo "城市名字:".$county."系统级别: $sys_level <br>";
		if($sys_level != 5 ){
			echo "城市名字:".$county."不能删除<br>";
		}
		
		if(!empty($city_id)){
			
			$sql = "DELETE FROM" . $GLOBALS['ecs']->table('category') . " WHERE cat_id = $city_id LIMIT 1";
			echo $sql."<br>";
		
			$sql_2 = "DELETE FROM" . $GLOBALS['ecs']->table('city_request') . " WHERE city_id = $city_id LIMIT 1";
			echo $sql_2."<br>";
		
			$sql_3 = "DELETE FROM" . $GLOBALS['ecs']->table('city_resource') . " WHERE city_id = $city_id LIMIT 1";
			echo $sql_3."<br>";
			
			
			// 	$GLOBALS['db']->query($sql);
			//	$GLOBALS['db']->query($sql_2);
			// 	$GLOBALS['db']->query($sql_3);
			

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


?>

