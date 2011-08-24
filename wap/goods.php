<?php

/**
 * SINEMALL 鍟嗗搧椤
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2008 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛沨ttp://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: testyang $
 * $Id: goods.php 14545 2008-05-04 02:54:30Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$goods_id = !empty($_GET['id']) ? intval($_GET['id']) : '';
$act = !empty($_GET['act']) ? $_GET['act'] : '';

$_LANG['kilogram'] = '鍗冨厠';
$_LANG['gram'] = '鍏?;
$_LANG['home'] = '棣栭〉';
$smarty->assign('goods_id', $goods_id);
$goods_info = get_goods_info($goods_id);
$goods_info['goods_name'] = encode_output($goods_info['goods_name']);
$goods_info['goods_brief'] = encode_output($goods_info['goods_brief']);
$goods_info['promote_price'] = encode_output($goods_info['promote_price']);
$goods_info['market_price'] = encode_output($goods_info['market_price']);
$goods_info['shop_price'] = encode_output($goods_info['shop_price']);
$goods_info['shop_price_formated'] = encode_output($goods_info['shop_price_formated']);
$smarty->assign('goods_info', $goods_info);
$smarty->assign('footer', get_footer());

/* 鏌ョ湅鍟嗗搧鍥剧墖鎿嶄綔 */
if ($act == 'view_img')
{
    $smarty->display('goods_img.wml');
    exit();
}

/* 妫€鏌ユ槸鍚︽湁鍟嗗搧鍝佺墝 */
if (!empty($goods_info['brand_id']))
{
    $brand_name = $db->getOne("SELECT brand_name FROM " . $ecs->table('brand') . " WHERE brand_id={$goods_info['brand_id']}");
    $smarty->assign('brand_name', encode_output($brand_name));
}
/* 鏄剧ず鍒嗙被鍚嶇О */
$cat_array = get_parent_cats($goods_info['cat_id']);
krsort($cat_array);
$cat_str = '';
foreach ($cat_array as $key => $cat_data)
{
    $cat_array[$key]['cat_name'] = encode_output($cat_data['cat_name']);
    $cat_str .= "<a href='category.php?c_id={$cat_data['cat_id']}'>" . encode_output($cat_data['cat_name']) . "</a>-&gt;";
}
$smarty->assign('cat_array', $cat_array);
$comment = assign_comment($goods_id, 0);
$smarty->assign('comment', $comment);
$smarty->display('goods.wml');

?>