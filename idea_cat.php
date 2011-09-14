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

/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
	$url = "user.php?act=login&back_act=five";
	ecs_header('Location: ' .$url. "\n");
	exit;
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 获得页面的缓存ID */
$cache_id = sprintf('%X', crc32($cat_id . '-' . $page . '-' . $_CFG['lang']));

if (!$smarty->is_cached('idea_cat.dwt', $cache_id))
{
    /* 如果页面没有被缓存则重新获得页面的内容 */

	$_LANG['pager_1'] = '总计 ';
	$_LANG['pager_2'] = ' 篇资料';
	$smarty->assign('lang', $_LANG);
    

    assign_template('a', array($cat_id));
    $position = assign_ur_here($cat_id);
    $smarty->assign('page_title',           "联想广告平台");     // 页面标题
    $smarty->assign('ur_here',              $position['ur_here']);   // 当前位置


    /* Meta */
    $meta = $db->getRow("SELECT keywords, cat_desc FROM " . $ecs->table('idea_cat') . " WHERE cat_id = '$cat_id'");

    if ($meta === false || empty($meta))
    {
        /* 如果没有找到任何记录则返回首页 */
        ecs_header("Location: ./\n");
        exit;
    }

   
    $cat_info = $db->getRow("SELECT * FROM " . $ecs->table('idea_cat') . " WHERE cat_id = '$cat_id'");
	$cat_info['cat_logo'] = !empty($cat_info['cat_logo']) ? 'data/idealogo/'.$cat_info['cat_logo'] :"";
	$smarty->assign('cat_info',    $cat_info);
	$smarty->assign('cat_id',    $cat_id);
	$smarty->assign('nav_index',    $cat_info['sort_order']);
	
	
	
	
	/* 获得当前页码 */
	$page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    
    /* 获得文章总数 */
    $size   = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 20;

	
	$ideas_list = get_cat_ideas($cat_id, $page, $size,150,'DESC',$keywords);
	$smarty->assign('ideas_list',   $ideas_list);
	$count  = get_idea_count($cat_id);

    $pages  = ($count > 0) ? ceil($count / $size) : 1;

    if ($page > $pages)
    {
        $page = $pages;
    }

    // 分页
    assign_pager('idea_cat',         $cat_id, $count, $size, '', '', $page,$keywords);
    assign_dynamic('idea_cat');

	$smarty->display('idea_cat.dwt');	

}
//$smarty->display('idea_cat.dwt', $cache_id);


?>