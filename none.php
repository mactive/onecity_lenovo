<?php

/**
 * SINEMALL 资料分类 * $Author: testyang $
 * $Id: idea_cat.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

	show_message("暂时未开放", $_LANG['profile_lnk'], 'five.php', 'info', false, 1);
	
    exit;


?>