<?php
/**
 * SINEMALL 品牌列表 * $Author: testyang $
 * $Id: brand.php 14641 2008-06-04 06:15:32Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$tb = get_my_tb_ys_list();
//$items = array_merge($array1, $array2);
echo "================"."<br>";
$_REQUEST['act'] = "update_member";

if($_REQUEST['act'] == 'update_member')
{
	//echo "================"."<br>";
	foreach($tb AS $key => $val){
		if($key > 1){
			$username = $val['username'];
			$uid = $val['uid'];
			$email = $val['email'];
			$salt = $val['salt'];
			$md55 = md5($salt);
			$md5newpw = md5($md55.$salt);
			echo $username."-". $salt."-".$md5newpw."-".$email."-"."-<br>";
			$sql = "UPDATE ".$GLOBALS['ecs']->table('members')." SET ".
	           " password = '$md5newpw' ".
	           " WHERE uid = $uid ";
			echo $sql."<br>";
			//$GLOBALS['db']->query($sql);
			
			/**/
			$to      = $email;
			$subject = '五周年活动登录密码重置';
			$message = 'passward:'.$salt;
			/**/
			$headers = 'From: mactive@gmail.cn' . "\r\n" .
			    'Reply-To: mactive@gmail.cn' . "\r\n" .
			    'X-Mailer: PHP/' . phpversion();
			

			mail($to, $subject, $message,$headers);
			echo "<br><br>";
			
		}	
	}	
}

/**
 * 取得品牌列表
 * @return array 品牌列表 id => name
 */
function get_my_tb_ys_list()
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('members') ." WHERE uid > 0 ";
    $res = $GLOBALS['db']->getAll($sql);

    return $res;
}


?>