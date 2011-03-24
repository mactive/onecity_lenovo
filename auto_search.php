<?php
/**
 * SINEMALL 品牌列表 * $Author: testyang $
 * $Id: brand.php 14641 2008-06-04 06:15:32Z testyang $
*/

$q = strtolower($_GET["q"]);
if (!$q) return;

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$array1 = get_my_goods_list();
$array2 = get_my_brand_list();
$items = array_merge($array1, $array2);
echo "[";
foreach ($items as $key=>$value) {
	if (strpos(strtolower($key), $q) !== false) {
		echo "{ name: \"$value[name]\", logo: \"$value[logo]\", price: \"$value[price]\", brief: \"$value[brief]\", url: \"$value[url]\" }, ";
	}
}
echo "]";


/**
 * 取得品牌列表
 * @return array 品牌列表 id => name
 */
function get_my_brand_list()
{
    $sql = "SELECT brand_id AS id, brand_name AS name ,brand_logo AS logo , jianjie FROM " . $GLOBALS['ecs']->table('brand') . ' ORDER BY brand_name ASC';
    $res = $GLOBALS['db']->getAll($sql);

    $brand_list = array();
    /**/
	foreach ($res AS $row)
    {
		
		$row['url'] = "brand.php?id=".$row['id'];
		$row['name']    = sub_str($row['name'], 30);
		$row['logo'] = "images/".$row['logo'];
		$row['brief'] = sub_str($row['jianjie'], 50);
        
        $brand_list[$row['name']] = $row;
    }
	
    return $brand_list;
}


/**
 * 取得品牌列表
 * @return array 品牌列表 id => name
 */
function get_my_goods_list()
{
    $sql = "SELECT goods_id AS id, goods_name AS name ,goods_thumb AS logo, shop_price AS price,goods_brief FROM " . $GLOBALS['ecs']->table('goods').
			"WHERE is_best = 1 and is_delete = 0";
    $res = $GLOBALS['db']->getAll($sql);

    $goods_list = array();
    /**/
	foreach ($res AS $row)
    {
		$row['url'] = "goods.php?id=".$row['id'];
        $row['name']    = sub_str($row['name'], 30);
        $row['brief']   = sub_str($row['goods_brief'], 50);
        $goods_list[$row['name']] = $row;
    }
	
    return $goods_list;
}




?>