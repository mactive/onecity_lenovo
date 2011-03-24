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
$tb = get_my_tb_ys_list();
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

$action_user = '王丽贤';

$start_time = gmtime();
$action_note = '批量入库';
$status = 1;

for ($y=0;$y<count($all_sheets);$y++) 
{ 
	echo "sheet: $y  "." row_count:".$all_sheets[$y]['numRows']."    col_count:".$all_sheets[$y]['numCols']."<br>";
	
	for ($row=2;$row<=$all_sheets[$y]['numRows'];$row++) 
	{

		$user_name = trim($all_sheets[$y]['cells'][$row][1]);
		$primary = trim($all_sheets[$y]['cells'][$row][2]);
		$function = trim($all_sheets[$y]['cells'][$row][3]);
		$division = trim($all_sheets[$y]['cells'][$row][4]);
		$department = trim($all_sheets[$y]['cells'][$row][5]);
		$mail_name = trim($all_sheets[$y]['cells'][$row][6]);
		$email = $mail_name."@legendsec.com";
		echo $user_name ."-". $primary."-".$function."-".$division."-".$department."-".$mail_name;
		
		$tt = "legendsec5"; //默认密码
		$md55 = md5($tt);
		echo $md55."<br>";//e70f3eb49a7b6a02155dfd95bdd5273c
		$salt = substr(uniqid(rand()), 0, 6);
		$md5newpw = md5($md55.$salt);
		
		
		$sql = "INSERT INTO ".$ecs->table('users')." (`user_id`, `email`, `user_name`, `password`, `question`, `answer`, `sex`, `birthday`, `user_money`, `frozen_money`, `pay_points`, `rank_points`, `address_id`, `reg_time`, `last_login`, `last_time`, `last_ip`, `visit_count`, `user_rank`, `is_special`, `salt`, `parent_id`, `flag`, `alias`, `msn`, `qq`, `office_phone`, `real_name`, `mobile_phone`, `agency_id`, `is_validated`, `credit_line`, `primary`, `function`, `division`, `department`) ".
	           "VALUES (NULL, '$email', '$mail_name', '', '', '', '0', '1910-01-01', '0.00', '0.00', '0', '0', '0', '1291717121', '0', '0000-00-00 00:00:00', '', '0', '1', '0', '$salt', '0', '0', '', '', '', '', '$user_name', '', '0', '1', '0.00', '$primary', '$function', '$division', '$department')";
		//user_rank
		echo $sql."<br>";
		
		//"INSERT INTO `db_legendsec_com`.`sm_users` (`user_id`, `email`, `user_name`, `password`, `question`, `answer`, `sex`, `birthday`, `user_money`, `frozen_money`, `pay_points`, `rank_points`, `address_id`, `reg_time`, `last_login`, `last_time`, `last_ip`, `visit_count`, `user_rank`, `is_special`, `salt`, `parent_id`, `flag`, `alias`, `msn`, `qq`, `office_phone`, `real_name`, `mobile_phone`, `agency_id`, `is_validated`, `credit_line`, `primary`, `funtion`, `division`, `department`) VALUES (NULL, '', '顾问', '', '', '', '0', '0000-00-00', '0.00', '0.00', '0', '0', '0', '0', '0', '0000-00-00 00:00:00', '', '0', '0', '0', '0', '0', '0', '', '', '', '', '', '', '0', '0', '0.00', '总裁室', '总裁室', '总裁室', '总裁室')";
	
		//$GLOBALS['db']->query($sql);
		
		
		
		
		
		$sql_uc = "INSERT INTO `db_legendsec_com`.`uc_members` ". "(username, password,salt,regdate) ".
	           "VALUES ('$mail_name','$md5newpw','$salt', '1291717120')";
		//$GLOBALS['db']->query($sql_uc);
		
		echo $sql_uc."<br>";

		echo "<br>";
		
	}
}
/* 修改库存和平均成本价 
*  输入 part_number
*  操作 goods 库 修改库存数量和 成本平均价格
*/
function act_goods_number_and_average_price($part_number)
{

	$goods_group= goods_group_by_part_number($part_number);
	$average_price = floatval($goods_group['price_amount']) / intval($goods_group['goods_number']) ;
	//print_r($goods_group);
	//echo "sdsd".floatval($average_price);
	$sql = "UPDATE " . $GLOBALS['ecs']->table('goods') .
            " SET goods_number =  '$goods_group[goods_number]' , " .
			" average_price =  '$average_price' ".
            " WHERE goods_id = '$goods_group[goods_id]' LIMIT 1";

    $GLOBALS['db']->query($sql);
    return $goods_group;
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

