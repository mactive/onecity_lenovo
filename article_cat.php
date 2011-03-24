<?php

/**
 * SINEMALL 资料分类 * $Author: testyang $
 * $Id: article_cat.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
	$url = "user.php?act=login&back_act=article_cat.php";
	ecs_header('Location: ' .$url. "\n");
	exit;
}

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 获得当前页码 */
$page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$type = !empty($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
$keywords = !empty($_REQUEST['keywords']) ? trim($_REQUEST['keywords']) : '';


/* 获得指定的分类ID */
if (!empty($_GET['id']))
{
    $cat_id = intval($_GET['id']);
}
elseif(!empty($_GET['category']))
{
    $cat_id = intval($_GET['category']);
}
else{
	/*------------------------------------------------------ */
	//-- PROCESSOR
	/*------------------------------------------------------ */

	//如果没有ID 那么进入列表页面
	if (empty($cat_id))
	{
	    /* 如果分类ID为0，则返回首页 */
		assign_template();
		$position = assign_ur_here();
		$_LANG['pager_1'] = '总计 ';
		$_LANG['pager_2'] = ' 篇文章';
		$smarty->assign('lang', $_LANG);
		
		$smarty->assign('page_title',       $position['title']);       // 页面标题
		$smarty->assign('ur_here',          '资料列表');     // 当前位置
		
		$cat_id = 0;

		/* 获得当前页码 */
		$page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	    
	    /* 获得文章总数 */
	    $size   = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 20;
	
		$articles_list = get_cat_articles($cat_id, $page, $size,150,'DESC',$keywords);
		$smarty->assign('articles_list',   $articles_list);
		
		$count  = count($articles_list);
		
	
	    $pages  = ($count > 0) ? ceil($count / $size) : 1;

	    if ($page > $pages)
	    {
	        $page = $pages;
	    }

		
		$smarty->assign('meter_cate_tree',  get_articlecat_subcat()); //资料分类
		$smarty->assign('hot_info_list',  get_hot_info_list()); //资料分类
		$smarty->assign('article_list_new',  get_article_list_new()); //最新资料
		
	    // 分页
	    assign_pager('article_cat',         $cat_id, $count, $size, '', '', $page,$keywords);
	    assign_dynamic('article_cat');
	
		$smarty->assign('cat_id',  $cat_id); //客服中心
		$smarty->assign('keywords',  $keywords); //客服中心
		$smarty->display('article_cat.dwt');


		exit;
	}

}


/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 获得页面的缓存ID */
$cache_id = sprintf('%X', crc32($cat_id . '-' . $page . '-' . $_CFG['lang']));

if (!$smarty->is_cached('article_cat.dwt', $cache_id))
{
    /* 如果页面没有被缓存则重新获得页面的内容 */

	$_LANG['pager_1'] = '总计 ';
	$_LANG['pager_2'] = ' 篇资料';
	$smarty->assign('lang', $_LANG);
    

    assign_template('a', array($cat_id));
    $position = assign_ur_here($cat_id);
    $smarty->assign('page_title',           $position['title']);     // 页面标题
    $smarty->assign('ur_here',              $position['ur_here']);   // 当前位置

    $smarty->assign('categories',           get_categories_tree(0)); // 分类树
    $smarty->assign('article_categories',   article_categories_tree($cat_id)); //资料分类树
    $smarty->assign('promotion_info', get_promotion_info());

    /* Meta */
    $meta = $db->getRow("SELECT keywords, cat_desc FROM " . $ecs->table('article_cat') . " WHERE cat_id = '$cat_id'");

    if ($meta === false || empty($meta))
    {
        /* 如果没有找到任何记录则返回首页 */
        ecs_header("Location: ./\n");
        exit;
    }

   
    $cat_info = $db->getRow("SELECT * FROM " . $ecs->table('article_cat') . " WHERE cat_id = '$cat_id'");
	$cat_info['cat_logo'] = !empty($cat_info['cat_logo']) ? 'data/articlelogo/'.$cat_info['cat_logo'] :"";
	$smarty->assign('cat_info',    $cat_info);
	$smarty->assign('cat_id',    $cat_id);
	$smarty->assign('nav_index',    $cat_info['sort_order']);
	
	
	$tmp_cat = get_article_catinfo_by_cat($cat_id);
	$tmp_article_id = $tmp_cat['cat_article_id'];
	
	
	$smarty->assign('meter_cate_tree',  get_articlecat_subcat()); //资料分类
	$smarty->assign('hot_info_list',  get_hot_info_list()); //资料分类
	$smarty->assign('article_list_new',  get_article_list_new()); //最新资料
	
	/* 获得当前页码 */
	$page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    
    /* 获得文章总数 */
    $size   = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 20;

	$articles_list = get_cat_articles($cat_id, $page, $size,150,'DESC',$keywords);
	$smarty->assign('articles_list',   $articles_list);
	
	$count  = count($articles_list);
	

    $pages  = ($count > 0) ? ceil($count / $size) : 1;

    if ($page > $pages)
    {
        $page = $pages;
    }

    // 分页
    assign_pager('article_cat',         $cat_id, $count, $size, '', '', $page,$keywords);
    assign_dynamic('article_cat');
	


	$smarty->display('article_cat.dwt');	

}
//$smarty->display('article_cat.dwt', $cache_id);


?>