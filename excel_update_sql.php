<?php

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

	
	for ($row=1;$row<=$all_sheets[$y]['numRows'];$row++) 
	{
		//$city = trim($all_sheets[$y]['cells'][$row][4]);
		$county = trim($all_sheets[$y]['cells'][$row][3]);
		//echo "城市名字:".$county."<br>";
		
		//$parent_id = get_cat_id_by_name($city);
		$city_id = get_cat_id_by_name($county);

		if(!empty($city_id)){
			
			
			$sql = "UPDATE " . $GLOBALS['ecs']->table('city_resource') . " SET Q2 = '6'  WHERE city_id = $city_id AND sys_level = 5 LIMIT 1 ";
			
			echo $sql."<br>";
	
			
			
			$GLOBALS['db']->query($sql);
		    //$GLOBALS['db']->query($sql_2);
		    //$GLOBALS['db']->query($sql_3);
			
		$count = $count + 1; 			
			
		}else{
			echo $county."不存在<br>";
		}
	}
	echo "===========".$count."<br>";
}

function dd(){
	

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
			
}

?>

