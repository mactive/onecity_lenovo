<?php

/**
 * SINEMALL 站点地图
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
if(1)
{
    include_once(ROOT_PATH . 'includes/lib_solution_operate.php');
	
	assign_template();
	$position = assign_ur_here();
	
	$smarty->assign('page_title',       '站点地图_sitemap-'.$GLOBALS['_CFG']['shop_title']);       // 页面标题
	$smarty->assign('ur_here',          '站点地图');     // 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
	$smarty->assign('user_id', $user_id);
	

	
	//解决方案		$_CFG['index_solutions_article_cat'] = 14;
	$solution_article = get_articlecat_subcat($_CFG['index_solutions_article_cat']);
	$smarty->assign('solution_article', $solution_article);
	//print_r($solution_article);
	
	// 新闻和活动
	$news_article_cat_id = 13;
	$news_article = get_articlecat_subcat($news_article_cat_id);
	$smarty->assign('news_article', $news_article);
	
	//产品分类
	$smarty->assign('categories',      get_categories_tree()); // 分类树
    

	//成功案例		$_CFG['index_cases_article_cat'] = 12;//成功案例资料主分类
	$smarty->assign('tag_name',      $tag_name);	
	$case_article = get_articlecat_subcat($_CFG['index_cases_article_cat'],20,110,'DESC',$tag_name);
	$smarty->assign('case_article', $case_article);

	//产品系列
    $goods_series = get_goods_series(100,45,0,1,$tag_name);
	$smarty->assign('goods_series',       $goods_series);

	//产品直销 推荐品牌
	$smarty->assign('recommond_brand_list',  get_brands('0','brand','1','0')); //品牌列表
    
}

/* 显示模板 */
$smarty->display('sitemap.dwt');


?>