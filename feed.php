<?php

/**
 * SINEMALL RSS Feed 生成程序 * $Author: testyang $
 * $Id: feed.php 14588 2008-05-19 05:49:41Z testyang $
*/

define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_rss.php');

header('Content-Type: application/xml; charset=' . EC_CHARSET);
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
header('Last-Modified: ' . date('r'));
header('Pragma: no-cache');

$ver = isset($_REQUEST['ver']) ? $_REQUEST['ver'] : '2.00';
$cat = isset($_REQUEST['cat']) ? ' AND ' . get_children(intval($_REQUEST['cat'])) : '';
$brd = isset($_REQUEST['brand']) ? ' AND g.brand_id=' . intval($_REQUEST['brand']) . ' ' : '';
$uri = $ecs->url();

$rss = new RSSBuilder(EC_CHARSET, $uri, htmlspecialchars($_CFG['shop_name']), htmlspecialchars($_CFG['shop_desc']), $uri . 'animated_favicon.gif');
$rss->addDCdata('', 'http://www.ecshop.com', date('r'));

$in_cat = $cat > 0 ? ' AND ' . get_children($cat) : '';

$sql = 'SELECT c.cat_name, g.goods_id, g.goods_name, g.goods_brief, g.last_update ' .
        'FROM ' . $ecs->table('category') . ' AS c, ' . $ecs->table('goods') . ' AS g ' .
        'WHERE c.cat_id = g.cat_id AND g.is_delete = 0 AND g.is_alone_sale = 1 ' . $brd . $cat .
        'ORDER BY g.goods_id DESC LIMIT 20';
$res = $db->query($sql);

if ($res !== false)
{
    while ($row = $db->fetchRow($res))
    {
        $item_url = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
        $about    = $uri . $item_url;
        $title    = htmlspecialchars($row['goods_name']);
        $link     = $uri . $item_url . '&amp;from=rss';
        $desc     = htmlspecialchars($row['goods_brief']);
        $subject  = htmlspecialchars($row['cat_name']);
        $date     = local_date($_CFG['timezone'], $row['last_update']);

        $rss->addItem($about, $title, $link, $desc, $subject, $date);
    }

    $rss->outputRSS($ver);
}

?>