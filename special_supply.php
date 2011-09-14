<?php

/**
 * SINEMALL 专题前台
 * ============================================================================
 * @author:     mactive <mactive@gmail.com>
 * @version:    v.1.51
 * ---------------------------------------------
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');


//
if(empty($_REQUEST['id']))
{
	
	assign_template();
	$position = assign_ur_here();
	
	$smarty->assign('page_title',       $position['title']);       // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);     // 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
	$smarty->assign('user_id', $user_id);
	

	$smarty->display('special_supply.dwt');
	exit;
}



?>