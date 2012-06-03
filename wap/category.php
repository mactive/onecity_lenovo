<?php

/**
 * SINEMALL 鍟嗗搧鍒嗙被椤
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2008 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛沨ttp://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: testyang $
 * $Id: category.php 14671 2008-06-16 01:54:17Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$c_id = !empty($_GET['c_id']) ? intval($_GET['c_id']) : 0;
if ($c_id <= 0)
{
    exit();
}
$cat_array = get_categories_tree($c_id);
$smarty->assign('c_id', $c_id);
$cat_name = $db->getOne('SELECT cat_name FROM ' . $ecs->table($GLOBALS['year']."_".'category') . ' WHERE cat_id=' . $c_id);
$smarty->assign('cat_name', encode_output($cat_name));
if (!empty($cat_array[$c_id]['children']))
{
    foreach ($cat_array[$c_id]['children'] as $key => $child_data)
    {
        $cat_array[$c_id]['children'][$key]['name'] = encode_output($child_data['name']);
    }
    $smarty->assign('cat_children', $cat_array[$c_id]['children']);
}
$cat_goods = assign_cat_goods($c_id, 0, 'wap');
$num = count($cat_goods['goods']);
if ($num > 0)
{
    $page_num = '10';
    $page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
    $pages = ceil($num / $page_num);
    if ($page <= 0)
    {
        $page = 1;
    }
    if ($pages == 0)
    {
        $pages = 1;
    }
    if ($page > $pages)
    {
        $page = $pages;
    }
    $i = 1;
    foreach ($cat_goods['goods'] as $goods_data)
    {
        if (($i > ($page_num * ($page - 1 ))) && ($i <= ($page_num * $page)))
        {
            $price = empty($goods_info['promote_price_org']) ? $goods_data['shop_price'] : $goods_data['promote_price'];
            //$wml_data .= "<a href='goods.php?id={$goods_data['id']}'>".encode_output($goods_data['name'])."</a>[".encode_output($price)."]<br/>";
            $data[] = array('i' => $i , 'price' => encode_output($price) , 'id' => $goods_data['id'] , 'name' => encode_output($goods_data['name']));
        }
        $i++;
    }
    $smarty->assign('goods_data', $data);
    $pagebar = get_wap_pager($num, $page_num, $page, 'category.php?c_id='.$c_id, 'page');
    $smarty->assign('pagebar', $pagebar);
}

$pcat_array = get_parent_cats($c_id);
if (!empty($pcat_array[1]['cat_name']))
{
    $pcat_array[1]['cat_name'] = encode_output($pcat_array[1]['cat_name']);
    $smarty->assign('pcat_array', $pcat_array[1]);
}

$smarty->assign('footer', get_footer());
$smarty->display('category.wml');

?>