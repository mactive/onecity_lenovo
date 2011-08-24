<?php

/**
 * SINEMALL WAP棣栭〉
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2008 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛沨ttp://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: testyang $
 * $Id: goods_list.php 14543 2008-05-04 02:18:37Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$type = !empty($_GET['type']) ? $_GET['type'] : 'best';
if ($type != 'best' && $type != 'promote' && $type != 'hot' && $type != 'new')
{
    $type = 'best';
}
$smarty->assign('type', $type);

$goods = get_recommend_goods($type);
$num = count($goods);
if ($num > 0)
{
    foreach ($goods as $key => $data)
    {
        $sort_array[$data['id']] = $key;
    }
    krsort($sort_array);
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
    foreach ($sort_array as $goods_key)
    {
        if (($i > ($page_num * ($page - 1 ))) && ($i <= ($page_num * $page)))
        {
            $price = empty($goods[$goods_key]['promote_price_org']) ? $goods[$goods_key]['shop_price'] : $goods[$goods_key]['promote_price'];
            //$wml_data .= "<a href='goods.php?id={}'>".encode_output($goods[$goods_key]['name'])."</a>[".encode_output($price)."]<br/>";
            $goods_data[] = array('i' => $i , 'price' => encode_output($price) , 'id' => $goods[$goods_key]['id'] , 'name' => encode_output($goods[$goods_key]['name']));
        }
        $i++;
    }
    $smarty->assign('goods_data', $goods_data);
    $pagebar = get_wap_pager($num, $page_num, $page, 'goods_list.php?type='.$type, 'page');
    $smarty->assign('pagebar' , $pagebar);
}

$smarty->assign('footer', get_footer());
$smarty->display('goods_list.wml');

?>