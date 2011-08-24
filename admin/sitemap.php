<?php

/**
 * SINEMALL 站点地图生成程序    $Author: testyang $
 * $Id: sitemap.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    /*------------------------------------------------------ */
    //-- 设置更新频率
    /*------------------------------------------------------ */
    assign_query_info();
    $config = unserialize($_CFG['sitemap']);
    $smarty->assign('config',           $config);
    $smarty->assign('ur_here',          $_LANG['sitemap']);
    $smarty->assign('arr_changefreq',   array(1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1));
    $smarty->display('sitemap.htm');
}
else
{
    /*------------------------------------------------------ */
    //-- 生成站点地图
    /*------------------------------------------------------ */
    include_once('includes/cls_phpzip.php');
    include_once('includes/cls_google_sitemap.php');

    $domain = $ecs->url();
    $today  = local_date('Y-m-d');

    $sm     =& new google_sitemap();
    $smi    =& new google_sitemap_item($domain, $today, $_POST['homepage_changefreq'], $_POST['homepage_priority']);
    $sm->add_item($smi);

    $config = array(
        'homepage_changefreq' => $_POST['homepage_changefreq'],
        'homepage_priority' => $_POST['homepage_priority'],
        'category_changefreq' => $_POST['category_changefreq'],
        'category_priority' => $_POST['category_priority'],
        'content_changefreq' => $_POST['content_changefreq'],
        'content_priority' => $_POST['content_priority'],
        );
    $config = serialize($config);

    $db->query("UPDATE " .$ecs->table('shop_config'). " SET VALUE='$config' WHERE code='sitemap'");

    /* 商品分类 */
    $sql = "SELECT cat_id FROM " .$ecs->table('category'). " ORDER BY parent_id";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $smi =& new google_sitemap_item($domain . build_uri('category', array('cid' => $row['cat_id'])), $today,
            $_POST['category_changefreq'], $_POST['category_priority']);
        $sm->add_item($smi);
    }

    /* 资料分类 */
    $sql = "SELECT cat_id FROM " .$ecs->table('article_cat'). " WHERE cat_type=1";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $smi =& new google_sitemap_item($domain . build_uri('article_cat', array('acid' => $row['cat_id'])), $today,
            $_POST['category_changefreq'], $_POST['category_priority']);
        $sm->add_item($smi);
    }

    /* 商品 */
    $sql = "SELECT goods_id FROM " .$ecs->table('goods'). " WHERE is_delete = 0";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $smi =& new google_sitemap_item($domain . build_uri('goods', array('gid' => $row['goods_id'])), $today,
            $_POST['content_changefreq'], $_POST['content_priority']);
        $sm->add_item($smi);
    }

    /* 资料 */
    $sql = "SELECT article_id FROM " .$ecs->table('article'). " WHERE is_open=1";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $smi =& new google_sitemap_item($domain . build_uri('article', array('aid' => $row['article_id'])),
            $today, $_POST['content_changefreq'], $_POST['content_priority']);
        $sm->add_item($smi);
    }

    clear_cache_files();    // 清除缓存

    $sm_file = '../sitemaps.xml';
    if ($sm->build($sm_file))
    {
        sys_msg(sprintf($_LANG['generate_success'], $ecs->url()."sitemaps.xml"));
    }
    else
    {
        $sm_file = '../data/sitemaps.xml';
        if ($sm->build($sm_file))
        {
            sys_msg(sprintf($_LANG['generate_success'], $ecs->url()."data/sitemaps.xml"));
        }
        else
        {
            sys_msg(sprintf($_LANG['generate_failed']));
        }
    }
}

?>