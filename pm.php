<?php

/**
 * SINEMALL 短消息文件
 * $Author: testyang $
 * $Id: pm.php 14626 2008-05-26 10:11:11Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if (empty($_SESSION['user_id']))
{
    ecs_header('Location:./');
}

uc_call("uc_pm_location", array($_SESSION['user_id']));
//$ucnewpm = uc_pm_checknew($_SESSION['user_id']);
//setcookie('checkpm', '');

?>