<?php

/**
 * SINEfive 首页文件 * $Author: testyang $
 * $Id: index.php 14481 2008-04-18 11:23:01 testyang $
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
	$url = "user.php?act=login&back_act=five";
	ecs_header('Location: ' .$url. "\n");
	exit;
}

/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
$cache_id = sprintf('%X', crc32($_SESSION['user_id'] . '-' . $_CFG['lang']));

if (!$smarty->is_cached('vote.dwt', $cache_id))
{
    assign_template();

    $position = assign_ur_here();
    $smarty->assign('page_title',      $_LANG['five_year']."－投票评选－我最喜欢的晚会节目");     // 页面标题
    $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置

    /* meta information */
    $smarty->assign('keywords',        htmlspecialchars($_CFG['shop_keywords']));
    $smarty->assign('description',     htmlspecialchars($_CFG['shop_desc']));

    $smarty->assign('feed_url',        ($_CFG['rewrite'] == 1) ? 'feed.xml' : 'feed.php'); // RSS URL

    $searchkeywords = !empty($_CFG['search_keywords']) ? explode(' ', trim($_CFG['search_keywords'])) : array();
    $smarty->assign('searchkeywords', $searchkeywords);

    $vote = get_vote(2);
    if (!empty($vote))
    {
        $GLOBALS['smarty']->assign('vote_id',     $vote['id']);
        $GLOBALS['smarty']->assign('vote',        $vote['content']);
    }



    /* 页面中的动态内容 */
    assign_dynamic('five');
}

$smarty->display('vote.dwt', $cache_id);

?>