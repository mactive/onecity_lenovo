<?php

/**
 * SINEMALL 专题前台
 * ============================================================================
 * 版权所有 (C) 2005-2008 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；http://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * @author:     webboy <laupeng@163.com>
 * @version:    v2.1
 * ---------------------------------------------
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$topic_id  = empty($_REQUEST['topic_id']) ? 0 : intval($_REQUEST['topic_id']);
$sort_name = empty($_REQUEST['sort']) ? '' : $_REQUEST['sort'];
$tag_name = empty($_REQUEST['tag_name']) ? '' : trim($_REQUEST['tag_name']);


$sql = "SELECT template FROM " . $ecs->table('topic') .
        "WHERE topic_id = '$topic_id' and  " . gmtime() . " >= start_time and " . gmtime() . "<= end_time";

$topic = $db->getRow($sql);

if(empty($topic))
{
	require_once(ROOT_PATH . 'includes/lib_solution.php');
	
    assign_template();

	$position = assign_ur_here();
	
	$smarty->assign('page_title',       $position['title']);       // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);     // 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
	$smarty->assign('user_id', $user_id);
	
	
	$tag_list = get_topic_tag_list('case',$_CFG['index_cases_article_cat']); // 12 是 成功案例的资料分类 cat_id
	$smarty->assign('tag_list', $tag_list);
	$smarty->assign('tag_name',      $tag_name);
	
    $index_series = get_goods_series(100,45,0,1,$tag_name);
	$smarty->assign('index_series',       $index_series);

	$smarty->display('topic_all.dwt');
	
    exit;
}

$templates = empty($topic['template']) ? 'topic.dwt' : $topic['template'];

$cache_id = sprintf('%X', crc32($_SESSION['user_id'] . '-' . $_CFG['lang'] . '-' . $topic_id));

if (!$smarty->is_cached($templates, $cache_id))
{
    $sql = "SELECT * FROM " . $ecs->table('topic') . " WHERE topic_id = '$topic_id'";

    $topic = $db->getRow($sql);
    $topic['data'] = addcslashes($topic['data'], "'");
    $tmp = @unserialize($topic["data"]);
    $arr = (array)$tmp;
	//print_r($arr);
    $goods_id = array();

    foreach ($arr AS $key=>$value)
    {
        foreach($value AS $k => $val)
        {
            $opt = explode('|', $val);
            $arr[$key][$k] = $opt[1];
            $goods_id[] = $opt[1];
        }
    }
	//print_r($goods_id);
	
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, " .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
                "WHERE " . db_create_in($goods_id, 'g.goods_id');

    $res = $GLOBALS['db']->query($sql);


    $sort_goods_arr = array();
	$is_show_sort_goods_arr = 0;
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $row['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $row['promote_price'] = '';
        }

        if ($row['shop_price'] > 0)
        {
            $row['shop_price'] =  price_format($row['shop_price']);
        }
        else
        {
            $row['shop_price'] = '';
        }
		
        $row['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $row['goods_style_name'] = add_style($row['goods_name'], $row['goods_name_style']);
        $row['short_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $row['goods_thumb']      = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];		
		
        $row['short_style_name'] = add_style($row['short_name'], $row['goods_name_style']);
		$properties		 		 = get_goods_properties($row['goods_id']);
		$row['properties']		 = $properties['pro'];  // 获得商品的规格和属性
		if($row['properties']){
			$is_show_sort_goods_arr += 1;
		}
		$row['specification']    = $properties['spe'];  //商品的可选属性
		$row['pictures']         = get_goods_gallery($row['goods_id']);                    // 商品相册
		
	
		
		//print_r($arr);
		
        foreach ($arr AS $key => $value)
        {
            foreach ($value AS $val)
            {
                if ($val == $row['goods_id'])
                {
					//echo "key == " . $key;
                    $key = $key == 'default' ? $_LANG['all_goods'] : $key;
                    $sort_goods_arr[$key][] = $row;
                }
            }
        }
    }
	

    /* 模板赋值 */
    assign_template();
    $position = assign_ur_here();
    $smarty->assign('page_title',       $position['title']);       // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);     // 当前位置
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);

    $smarty->assign('is_show_sort_goods_arr',   $is_show_sort_goods_arr);          // 商品列表
    $smarty->assign('sort_goods_arr',   $sort_goods_arr);          // 商品列表
    $smarty->assign('topic',            $topic);                   // 专题信息

    $template_file = empty($topic['template']) ? 'topic.dwt' : $topic['template'];
}
/* 显示模板 */
$smarty->display($templates, $cache_id);

?>