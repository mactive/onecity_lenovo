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
require_once(ROOT_PATH . 'includes/lib_goods.php');
require_once(ROOT_PATH . 'admin/includes/lib_main.php');

require_once(ROOT_PATH . 'includes/lib_solution.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/solution.php');

$tag_name = empty($_REQUEST['tag_name']) ? '' : trim($_REQUEST['tag_name']);

//
if(empty($_REQUEST['case_id']))
{
	
	assign_template();
	$position = assign_ur_here();
	
	$smarty->assign('page_title',       $position['title']);       // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);     // 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
	$smarty->assign('user_id', $user_id);
	$smarty->assign('nav_index', 6);


	$smarty->display('contact_us.dwt');
	exit;
}

/* 显示模板 */
$smarty->display('contact_us.dwt');


?>