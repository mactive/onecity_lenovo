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
    include_once(ROOT_PATH . 'includes/lib_solution_operate.php');
	
	assign_template();
	$position = assign_ur_here();
	
	$smarty->assign('page_title',       $position['title']);       // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);     // 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
	$smarty->assign('user_id', $user_id);
	
	$tag_list = get_case_tag_list('case',$_CFG['index_cases_article_cat']); // 12 是 成功案例的资料分类 cat_id
	$smarty->assign('tag_list', $tag_list);
	
	//$_CFG['index_solutions_article_cat'] = 14;
	$solution_article = get_articlecat_subcat($_CFG['index_solutions_article_cat']);
	$solution_article = add_order_to_solution_array($solution_article); //增加订单数组  按cat.keywords 搜索 order.tag_name
	$smarty->assign('solution_article', $solution_article);
	
	//$_CFG['index_cases_article_cat'] = 12;//解决方案资料主分类
	$smarty->assign('tag_name',      $tag_name);	
	$case_article = get_articlecat_subcat($_CFG['index_cases_article_cat'],20,110,'DESC',$tag_name);
	$smarty->assign('case_article', $case_article);

	$index_series = get_goods_series(3,30,0);//显示数量 , 文字显示limit
	$smarty->assign('index_series',       $index_series);

	$smarty->display('case.dwt');
	exit;
}

/* 显示模板 */
$smarty->display('case.dwt');


?>