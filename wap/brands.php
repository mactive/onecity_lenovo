<?php

/**
 * SINEMALL 鍝佺墝涓撳尯
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2008 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛沨ttp://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: testyang $
 * $Id: brands.php 14679 2008-06-18 02:01:40Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$b_id = !empty($_GET['b_id']) ? intval($_GET['b_id']) : 0;
if ($b_id <= 0)
{
    exit();
}
$brands_array = assign_brand_goods($b_id);
$brands_array['brand']['name'] = encode_output($brands_array['brand']['name']);
$smarty->assign('brands_array' , $brands_array);
$num = count($brands_array['goods']);
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
    foreach ($brands_array['goods'] as $goods_data)
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
    $pagebar = get_wap_pager($num, $page_num, $page, 'brands.php?b_id=' . $b_id, 'page');
    $smarty->assign('pagebar', $pagebar);
}

$brands_array = get_brands('0','brand','0');
if (count($brands_array) > 1)
{
    foreach ($brands_array as $key => $brands_data)
    {
           $brands_array[$key]['brand_name'] =  encode_output($brands_data['brand_name']);
    }
    $smarty->assign('brand_id', $b_id);
    $smarty->assign('other_brands', $brands_array);
}

$smarty->assign('footer', get_footer());
$smarty->display('brands.wml');

?>