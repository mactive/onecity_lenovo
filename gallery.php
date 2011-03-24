<?php

/**
 * SINEMALL 商品相册 * $Author: testyang $
 * $Id: gallery.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 参数 */
$_REQUEST['id']  = isset($_REQUEST['id'])  ? intval($_REQUEST['id'])  : 0; // 商品编号
$_REQUEST['img'] = isset($_REQUEST['img']) ? intval($_REQUEST['img']) : 0; // 图片编号

/* 获得商品名称 */
$sql = 'SELECT goods_name FROM ' . $ecs->table('goods') . "WHERE goods_id = '$_REQUEST[id]'";
$goods_name = $db->getOne($sql);

/* 如果该商品不存在，返回首页 */
if ($goods_name === false)
{
    ecs_header("Location: ./\n");

    exit;
}

/* 获得所有的图片 */
$sql = 'SELECT img_id, img_desc, thumb_url, img_url'.
       ' FROM ' .$ecs->table('goods_gallery').
       " WHERE goods_id = '$_REQUEST[id]' ORDER BY img_id";
$img_list = $db->getAll($sql);

$img_count = count($img_list);
if ($img_count == 0)
{
    /* 如果没有图片，返回商品详情页 */
    ecs_header('Location: goods.php?id=' . $_REQUEST['id'] . "\n");
    exit;
}
else
{
    /* 找到当前商品图片 */
    $current_key = 0;
    foreach ($img_list AS $key => $img)
    {
        if ($img['img_id'] == $_REQUEST['img'])
        {
            $current_key = $key;

            break;
        }
    }
}

$gallery = array(
    'goods_id'   => $_REQUEST['id'],
    'goods_name' => $goods_name,
    'img_id'     => $img_list[$current_key]['img_id'],
    'img_desc'   => $img_list[$current_key]['img_desc'],
    'img_url'    => $img_list[$current_key]['img_url']
);

/* 前一个和后一个图片 */
$prev_key = $current_key     > 0          ? $current_key - 1 : 0;
$next_key = $current_key + 1 < $img_count ? $current_key + 1 : $img_count - 1;

$smarty->assign('prev_img', $img_list[$prev_key]['img_id']);
$smarty->assign('next_img', $img_list[$next_key]['img_id']);
$smarty->assign('gallery',  $gallery);
$smarty->assign('thumbs',   $img_list);

$smarty->display('gallery.dwt');

?>