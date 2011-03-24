<?php

/**
 * SINEMALL 商品相关函数库 * $Author: testyang $
 * $Id: lib_goods.php 14568 2008-05-12 08:34:26Z testyang $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 获得指定分类同级的所有分类以及该分类下的子分类
 *
 * @access  public
 * @param   integer     $cat_id     分类编号
 * @return  array
 */
function get_categories_tree($cat_id = 0)
{
    if ($cat_id > 0)
    {
        $sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'"." ORDER BY cat_id ASC";
        $parent_id = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        $parent_id = 0;
    }

    /*
     判断当前分类中全是是否是底级分类，
     如果是取出底级分类上级分类，
     如果不是取当前分类及其下的子分类
    */
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$cat_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $parent_id == 0)
    {
        /* 获取当前分类及其子分类 */
        $sql = 'SELECT a.cat_id, a.cat_name, a.sort_order AS parent_order, a.cat_id, a.is_show,' .
                    'b.cat_id AS child_id, b.cat_name AS child_name, b.sort_order AS child_order ' .
                'FROM ' . $GLOBALS['ecs']->table('category') . ' AS a ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS b ON b.parent_id = a.cat_id AND b.is_show = 1 ' .
                "WHERE a.parent_id = '$parent_id' ORDER BY a.cat_id ASC, child_order ASC";
    }
    else
    {
        /* 获取当前分类及其父分类 */
        $sql = 'SELECT a.cat_id, a.cat_name, b.cat_id AS child_id, b.cat_name AS child_name, b.sort_order, b.is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') . ' AS a ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS b ON b.parent_id = a.cat_id AND b.is_show = 1 ' .
                "WHERE b.parent_id = '$parent_id' ORDER BY sort_order ASC";
    }
    $res = $GLOBALS['db']->getAll($sql);

    $cat_arr = array();
    foreach ($res AS $row)
    {
        if ($row['is_show'])
        {
            $cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
            $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
            $cat_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

            if ($row['child_id'] != NULL)
            {
                $cat_arr[$row['cat_id']]['children'][$row['child_id']]['id']   = $row['child_id'];
                $cat_arr[$row['cat_id']]['children'][$row['child_id']]['name'] = $row['child_name'];
                $cat_arr[$row['cat_id']]['children'][$row['child_id']]['url']  = build_uri('category', array('cid' => $row['child_id']), $row['child_name']);
            }
        }
    }
    return $cat_arr;
}

/**
 * 判断有无子分类
 *
 * @access  public
 * @param   integer     $cat_id     分类编号
 * @return  boolean
 */
function have_no_subcategory($cat_id = 0){
	$sql = 'SELECT cat_id FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$cat_id'";
	$res = $GLOBALS['db']->getAll($sql);
	if(sizeof($res)>0){
		return false;
	}else{
		return true;
	}
    print_r($res);
}


/**
 * 判断有无子分类
 *
 * @access  public
 * @param   integer     $cat_id     分类编号
 * @return  boolean
 */
function get_name_form_ID($cat_id = 0){
	if($cat_id == 0){
		return "顶级分类";
	}else{
		$sql = 'SELECT cat_name FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
		$res = $GLOBALS['db']->getAll($sql);
	    foreach ($res AS $row){
			$cat_name = $row['cat_name'];
		}
		return $cat_name;
	}
}
/**
 * 获得指定分类的产品数量
 * get the goods num by cat_id
 * @access  public
 * @param   integer     $cat_id     分类编号
 * @return  goods_num  商品数量
*/
function get_goodsnum_by_cat($cat_id = 0)
{
	if($cat_id > 0)
	{
		$sql = 'SELECT COUNT(goods_id) AS goods_num  FROM ' . $GLOBALS['ecs']->table('goods') . " WHERE cat_id = '$cat_id'";
		$res = $GLOBALS['db']->getAll($sql);
	    foreach ($res AS $row){
			$goods_num = $row['goods_num'];
		}
		return $goods_num;
	}
	else{
		return "null";
	}
}


/**
 * 获得指定分类同级的所有分类以及该分类下的子分类
 *
 * @access  public
 * @param   integer     $cat_id     分类编号
 * @return  array
 */
function get_sub_categories_tree($cat_id = 0)
{
    if ($cat_id > 0)
    {
        $sql = 'SELECT cat_id FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
        $parent_id = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        $parent_id = 0;
    }

    /*
     判断当前分类中全是是否是底级分类，
     如果是取出底级分类上级分类，
     如果不是取当前分类及其下的子分类
    */
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$cat_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $parent_id == 0)
    {
        /* 获取当前分类及其子分类 */
        $sql = 'SELECT a.cat_id, a.cat_name, a.cat_logo, a.sort_order AS parent_order, a.cat_id, a.is_show,' .
                    'b.cat_id AS child_id, b.cat_name AS child_name, b.cat_logo AS child_logo, b.sort_order AS child_order ' .
                'FROM ' . $GLOBALS['ecs']->table('category') . ' AS a ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS b ON b.parent_id = a.cat_id AND b.is_show = 1 ' .
                "WHERE a.parent_id = '$parent_id' ORDER BY parent_order ASC, a.cat_id ASC, child_order ASC";
    }
    else
    {
        /* 获取当前分类及其父分类 */
        $sql = 'SELECT a.cat_id, a.cat_name, b.cat_id AS child_id, b.cat_name AS child_name, b.sort_order, b.is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') . ' AS a ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS b ON b.parent_id = a.cat_id AND b.is_show = 1 ' .
                "WHERE b.parent_id = '$parent_id' ORDER BY sort_order ASC";
    }
    $res = $GLOBALS['db']->getAll($sql);

    $cat_arr = array();
    foreach ($res AS $row)
    {
        if ($row['is_show'])
        {
            $cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
            $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
			$cat_arr[$row['cat_id']]['logo'] = $row['cat_logo'];
			$cat_arr[$row['cat_id']]['goods_num'] = get_goodsnum_by_cat($row['cat_id']);
			
            $cat_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

            if ($row['child_id'] != NULL)
            {
                $cat_arr[$row['cat_id']]['children'][$row['child_id']]['id']   = $row['child_id'];
                $cat_arr[$row['cat_id']]['children'][$row['child_id']]['name'] = $row['child_name'];
				$cat_arr[$row['cat_id']]['children'][$row['child_id']]['logo'] = $row['child_logo'];
				$cat_arr[$row['cat_id']]['children'][$row['child_id']]['goods_num'] = get_goodsnum_by_cat($row['child_id']);
                $cat_arr[$row['cat_id']]['children'][$row['child_id']]['url']  = build_uri('category', array('cid' => $row['child_id']), $row['child_name']);
            }
        }
    }
	
	$res = array();
	$res[$cat_id] = "子分类";
	
	foreach($cat_arr AS  $val)
	{
		$res[$val['id']] = $val['name'];
	}
//	print_r($cat_arr);
//	print_r($res);
    return $res;
}


/**
 * 调用当前分类的销售排行榜
 *
 * @access  public
 * @param   string  $cats   查询的分类
 * @return  array
 */
function get_top10($cats = '')
{
    $where = !empty($cats) ? "AND ($cats OR " . get_extension_goods($cats) . ") " : '';

    /* 排行统计的时间 */
    switch ($GLOBALS['_CFG']['top10_time'])
    {
        case 1: // 一年
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 365 * 86400) . "'";
        break;
        case 2: // 半年
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 180 * 86400) . "'";
        break;
        case 3: // 三个月
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 90 * 86400) . "'";
        break;
        case 4: // 一个月
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 30 * 86400) . "'";
        break;
        default:
            $top10_time = '';
    }

    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_img, SUM(og.goods_number) as goods_number ' .
           'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g, ' .
                $GLOBALS['ecs']->table('order_info') . ' AS o, ' .
                $GLOBALS['ecs']->table('order_goods') . ' AS og ' .
           "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 $where $top10_time " .
           'AND og.order_id = o.order_id AND og.goods_id = g.goods_id ' .
           "AND o.order_status = '" . OS_CONFIRMED . "' " .
           "AND (o.pay_status = '" . PS_PAYED . "' OR o.pay_status = '" . PS_PAYING . "') " .
           "AND (o.shipping_status = '" . SS_SHIPPED . "' OR o.shipping_status = '" . SS_RECEIVED . "') ".
           'GROUP BY g.goods_id ORDER BY goods_number DESC, g.goods_id DESC LIMIT ' . $GLOBALS['_CFG']['top_number'];
    $arr = $GLOBALS['db']->getAll($sql);

    for ($i = 0, $count = count($arr); $i < $count; $i++)
    {
        $arr[$i]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                    sub_str($arr[$i]['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $arr[$i]['goods_name'];
        $arr[$i]['url']        = build_uri('goods', array('gid' => $arr[$i]['goods_id']), $arr[$i]['goods_name']);
    }

    return $arr;
}


#
/**
* @package BugFree
* @version $Id: FunctionsMain.inc.php,v 1.32 2005/09/24 11:38:37 wwccss Exp $
*
* Sort an two-dimension array by some level two items use array_multisort() function.
*
* sysSortArray($Array,"Key1","SORT_ASC","SORT_RETULAR","Key2"……)
* @author Chunsheng Wang <wwccss@263.net>
* @param array $ArrayData the array to sort.
* @param string $KeyName1 the first item to sort by.
* @param string $SortOrder1 the order to sort by("SORT_ASC"|"SORT_DESC")
* @param string $SortType1 the sort type("SORT_REGULAR"|"SORT_NUMERIC"|"SORT_STRING")
* @return array sorted array.
*/
function sysSortArray($ArrayData,$KeyName1,$SortOrder1 = "SORT_ASC",$SortType1 = "SORT_REGULAR")
{
	if(!is_array($ArrayData))
	{
		return $ArrayData;
	}
 
	// Get args number.
	$ArgCount = func_num_args();
	// Get keys to sort by and put them to SortRule array.
	for($I = 1;$I < $ArgCount;$I ++)
	{
		$Arg = func_get_arg($I);
		if(!eregi("SORT",$Arg))
		{
			$KeyNameList[] = $Arg;
			$SortRule[] = '$'.$Arg;
		}
		else
		{
			$SortRule[] = $Arg;
		}
	}

	// Get the values according to the keys and put them to array.
	foreach($ArrayData AS $Key => $Info)
	{
		foreach($KeyNameList AS $KeyName)
		{
			${$KeyName}[$Key] = $Info[$KeyName];
		}
	}
	// Create the eval string and eval it.
	$EvalString = 'array_multisort('.join(",",$SortRule).',$ArrayData);';
	eval ($EvalString);
	return $ArrayData;
}

/*
   *Author:Fanbo
   *Date:2007.8.10
   *Description:排除二维数组里面同样的元素,功能是array_unique的重构升级版
*/
function array_unique_fb($array2D){
	foreach ($array2D as $v)
	{
		$v = join(",",$v); //降维,也可以用implode
		$temp[] = $v;
	}
  
	$temp = array_unique($temp);
	foreach ($temp as $k => $v){
		$temp[$k] = explode(",",$v);
	}
	return $temp;
}  

/*
	* 清除二维数组的重复项
	 *
	 * @access  public
	 * @param   string      $type       推荐类型，可以是 best, new, hot, promote
	 * @return  array
*/
function array_unique_2d($arr){
	foreach($arr   as   $i=>$vi)
	{  
    	foreach($arr   as   $j=>$vj)
		{  
        	if($i   !=   $j   &&   array_intersect_assoc($vi,$vj)){
				unset($arr[$i]);  
			}        
      	}  
  	}
   	var_export($arr);	
	return $arr;
}

/**
 * 重新安排最新商品 以适应首页AJAX
 *
 * @access  public
 * @param   array      $ptarray   -- pasttime array
 * @return  array list
 */
function arr_recommend_goods($ptarray = '')
{
	if(!$ptarray)
	{
		return array();
	}
	//arsort($ptarray);
	$temp = array();
	for($i=0;$i<sizeof($ptarray);$i++)
	{
		$temp[$i]['pcat_id']   = $ptarray[$i]['pcat_id'];
		$temp[$i]['pcat_name'] = $ptarray[$i]['pcat_name'];
	}
	$temp = array_unique_2d($temp);
	
		
	return $temp;
}

/**
 * 获得所有顶级分类
 *
 * @access  public
 * @param   string   null
 * @return  array[idx]['id']   array[idx]['name']
 */
function get_top_category()
{
	$sql = 'SELECT cat_id, cat_name FROM ' . $GLOBALS['ecs']->table('category') . 'WHERE parent_id = 0';
	$result = $GLOBALS['db']->getAll($sql);
	//print_r($result);
	
	$cate_arr = array();
	foreach ($result AS $idx => $row)
    {
		$cate_arr[$idx]['cat_id']   = $row['cat_id'];
		$cate_arr[$idx]['cat_name'] = $row['cat_name'];
	}
	return $cate_arr;
}


/**
 * 获得分类新上品
 *
 * @access  public
 * @param   string      $type       推荐类型，可以是 best, new, hot, promote
 * @return  array
 */
function get_recommend_goods_cate_new()
{
	$cate_top = get_top_category();
	$goods_more = array();
	
	$goods_more['0']['cat_id'] = 0;
	$goods_more['0']['cat_name'] = '全部';
	$goods_more['0']['data'] = get_category_recommend_goods('new',get_children(0));
	for($idx = 0 ;$idx < sizeof($cate_top);$idx++)
	{
		$goods_more[$idx+1]['cat_id'] = $cate_top[$idx]['cat_id'];
		$goods_more[$idx+1]['cat_name'] = $cate_top[$idx]['cat_name'];
		$goods_more[$idx+1]['data'] = get_category_recommend_goods('new',get_children($cate_top[$idx]['cat_id']));
	}
	//print_r($goods_more);
    return $goods_more;
}


/**
 * 获得分类新上品
 *
 * @access  public
 * @param   string      $type       推荐类型，可以是 best, new, hot, promote
 * @return  array
 */
function get_recommend_goods_cate_hot()
{
	$cate_top = get_top_category();
	$goods_more = array();
	for($idx = 0 ;$idx < sizeof($cate_top);$idx++)
	{
		$goods_more[$idx]['cat_id'] = $cate_top[$idx]['cat_id'];
		$goods_more[$idx]['cat_name'] = $cate_top[$idx]['cat_name'];
		$goods_more[$idx]['data'] = get_category_recommend_goods('hot_index',get_children($cate_top[$idx]['cat_id']));
	}
	//print_r($goods_more);
    return $goods_more;
}
/**
 * 获得推荐商品
 *
 * @access  public
 * @param   string      $type       推荐类型，可以是 best, new, hot, promote
 * @return  array
 */
function get_index_goods($limit = 12)
{
	/*
	$sql = " SELECT code,value FROM ". $GLOBALS['ecs']->table('shop_config') . " AS t1 " .
		   " JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM ". $GLOBALS['ecs']->table('shop_config').")-(SELECT MIN(id) FROM ".
			$GLOBALS['ecs']->table('shop_config')."))+(SELECT MIN(id) FROM ".$GLOBALS['ecs']->table('shop_config').")) AS id) AS t2".
		   " WHERE t1.id >= t2.id AND t1.parent_id = 100  AND t1.code LIKE '%index_goods%' ".
		   " ORDER BY t1.id LIMIT $limit";	
	*/
	/*
	$sql = "SELECT code,value FROM " . $GLOBALS['ecs']->table('shop_config') ." AS t1". 
			" JOIN (SELECT ROUND(RAND() * (SELECT MAX(id) FROM ".$GLOBALS['ecs']->table('shop_config') .")) AS id) AS t2 "
			" WHERE t1.parent_id = 100  AND t1.code LIKE '%index_goods%' ".
			"ORDER BY id ASC LIMIT 0,$limit";
	*/
	//echo $sql;
	$sql = "SELECT code,value FROM " . $GLOBALS['ecs']->table('shop_config') . " WHERE parent_id = 100  AND code LIKE '%index_goods%' ".
			"ORDER BY rand() LIMIT 0,$limit";
    $arr = $GLOBALS['db']->getAll($sql);
	$i = 0;
	$result = array();
	foreach($arr AS $key => $val){
		$result[$i] = get_goods_info(trim($val['value']));
		$i++;
	}
	return $result;
	//print_r($result);
}

/**
 * 获得推荐商品
 *
 * @access  public
 * @param   string      $type       推荐类型，可以是 best, new, hot, promote
 * @return  array
 */
function get_recommend_goods($type = '', $cats = '')
{
    if (!in_array($type, array('best', 'new', 'hot', 'promote','index_best')))
    {
        return array();
    }

    $time = gmtime();

    $order_type = 0;

    /* 取得每一项的数量限制 */
    $type2lib = array('best'=>'recommend_best', 'new'=>'recommend_new', 'hot'=>'recommend_hot', 'promote'=>'recommend_promotion','index_best'=>'index_recommend_best');
    $num = get_library_number($type2lib[$type]);
//	$num = 12;
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.cat_id, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name, " .
                "g.is_best, g.is_new, g.is_hot, g.is_promote, RAND() AS rnd " .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ';
    if ($type == 'best')
    {
        $sql .= ' AND g.is_best = 1 ';
    }
    elseif ($type == 'new')
    {
        $sql .= ' AND g.is_new = 1 ';
    }
    elseif ($type == 'index_best')
    {
        $sql .= ' AND g.is_best = 1 ';
    }
    elseif ($type == 'hot')
    {
        $sql .= ' AND g.is_hot = 1 ';
    }
    elseif ($type == 'promote')
    {
        $sql .= " AND g.is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time' ";
    }
    $sql .= $order_type == 0 ? ' ORDER BY g.goods_id desc' : ' ORDER BY rnd';
    $sql .= " LIMIT $num ";
    $result = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($result AS $idx => $row)
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id']           = $row['goods_id'];
        $goods[$idx]['name']         = $row['goods_name'];
        $goods[$idx]['brief']        = $row['goods_brief'];
        $goods[$idx]['brand_name']   = $row['brand_name'];
		$goods[$idx]['cat_id']		 = $row['cat_id'];
		
		$pcat_array 				 = get_parent_cats($row['cat_id']);//获得顶级分类的信息array
		$goods[$idx]['pcat_id']		 = $pcat_array[sizeof($pcat_array)-1]['cat_id'];// sizeof($pcat_array)-1 = [2]  顶级分类ID
		$goods[$idx]['pcat_name']	 = $pcat_array[sizeof($pcat_array)-1]['cat_name'];//sizeof($pcat_array)-1 =[2]  顶级分类名称
		
        $goods[$idx]['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);

        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                           sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['short_style_name']   = add_style($goods[$idx]['short_name'],$row['goods_name_style']);
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['thumb']        = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
        $goods[$idx]['goods_img']    = empty($row['goods_img'])   ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

    }

    return $goods;
}

/**
 * 获得指定分类下的推荐商品
 *
 * @access  public
 * @param   string      $type       推荐类型，可以是 best, new, hot, promote
 * @param   string      $cats       分类的ID
 * @param   integer     $brand      品牌的ID
 * @param   integer     $min        商品价格下限
 * @param   integer     $max        商品价格上限
 * @param   string      $ext        商品扩展查询
 * @return  array
 */
function get_category_recommend_goods($type = '', $cats = '', $brand = 0, $min =0,  $max = 0, $ext='')
{
    $brand_where = ($brand > 0) ? " AND g.brand_id = '$brand'" : '';

    $price_where = ($min > 0) ? " AND g.shop_price >= $min " : '';
    $price_where .= ($max > 0) ? " AND g.shop_price <= $max " : '';

    $sql =  'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' . $brand_where . $price_where . $ext;
//echo $sql;
    $num = 0;
    $type2lib = array('best'=>'recommend_best', 'new'=>'recommend_new', 'hot'=>'recommend_hot', 'hot_index'=>'recommend_hot_index', 'promote'=>'recommend_promotion');
    $num = get_library_number($type2lib[$type]);
	
    switch ($type)
    {
        case 'best':
            $sql .= ' AND is_best = 1';
        break;
        case 'new':
            $sql .= ' AND is_new = 1';
        break;
        case 'hot':
            $sql .= ' AND is_hot = 1';
        break;
    	case 'hot_index':
	        $sql .= ' AND is_hot = 1';
	    break;
        case 'promote':
            $time = gmtime();
            $sql .= " AND is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time'";
        break;
    }

    if (!empty($cats))
    {
        $sql .= " AND (" . $cats . " OR " . get_extension_goods($cats) .")";
    }

    $sql .= ' ORDER BY g.sort_order DESC';
	//echo $sql;
    $res = $GLOBALS['db']->selectLimit($sql, $num);

    $idx = 0;
    $goods = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id']           = $row['goods_id'];
        $goods[$idx]['name']         = $row['goods_name'];
        $goods[$idx]['brief']        = $row['goods_brief'];
        $goods[$idx]['brand_name']   = $row['brand_name'];
        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                       sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['thumb']        = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
        $goods[$idx]['goods_img']    = empty($row['goods_img'])   ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

		
        $goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'], $row['goods_name_style']);
        $idx++;
    }

    return $goods;
}

/**
 * 获得商品的详细信息
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  void
 */
function get_goods_info($goods_id)
{
    $time = gmtime();
    $sql = 'SELECT g.*,g.agency_price, c.measure_unit, b.brand_id, b.brand_name AS goods_brand, s.status_name AS goods_status_name,m.type_money AS bonus_money, ' .
                'IFNULL(AVG(r.comment_rank), 0) AS comment_rank, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS rank_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS c ON g.cat_id = c.cat_id ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON g.brand_id = b.brand_id ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_status') . ' AS s ON g.goods_status = s.status_id ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('comment') . ' AS r '.
                'ON r.id_value = g.goods_id AND comment_type = 0 AND r.parent_id = 0 AND r.status = 1 ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('bonus_type') . ' AS m ' .
                "ON g.bonus_type_id = m.type_id AND m.send_start_date <= '$time' AND m.send_end_date >= '$time'" .
            " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE g.goods_id = '$goods_id' AND g.is_delete = 0 " .
            "GROUP BY g.goods_id";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        /* 用户评论级别取整 */
        $row['comment_rank']  = ceil($row['comment_rank']) == 0 ? 5 : ceil($row['comment_rank']);

        
        /* 修正促销价格 */
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        /* 处理商品水印图片 */
        $watermark_img = '';

		
		if ($promote_price != 0){
            $watermark_img = "watermark_promote_small";
        }else{
			if($row['is_best'] != 0){
				$watermark_img = "watermark_best_small";
			}else{
				if($row['is_hot'] != 0){
					$watermark_img = 'watermark_hot_small';
				}else{
					if($row['is_new'] != 0){
						$watermark_img = "watermark_new_small";
					}
				}
			}
		}

		/*
		if ($row['is_new'] != 0)
        {
            $watermark_img = "watermark_new_small";
        }
        elseif ($row['is_hot'] != 0)
        {
            $watermark_img = 'watermark_hot_small';
        }
        elseif ($row['is_best'] != 0)
        {
            
        }
        elseif ($promote_price != 0)
        {
            $watermark_img = "watermark_promote_small";
        }
		*/
		

        if ($watermark_img != '')
        {
            $row['watermark_img'] =  $watermark_img;
        }

		/* 获得商品的销售价格 格式 ：RMB XX 元*/
        $row['market_price_formated']	= price_format($row['market_price']);
        $row['shop_price_formated'] 	= price_format($row['shop_price']);
		$row['salebase_price_formated'] = price_format($row['salebase_price']);
        $row['promote_price_formated'] 	= price_format($promote_price);
		$row['agency_price_formated']  	= price_format($row['agency_price']);

        
        $row['promote_price_org'] =  $promote_price;  //数字格式的促销价
		$row['salebase_price_num']= $row['salebase_price'];
        $row['shop_price_num']    = $row['shop_price'];
       	

        /* 修正重量显示 */
        $row['goods_weight']  = (intval($row['goods_weight']) > 0) ?
            $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
            ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正上架时间显示 */
        $row['add_time']      = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);



        /* 促销时间倒计时 */
        $time = gmtime();
        if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date'])
        {
             $row['gmt_end_time']  = $row['promote_end_date'];
             $row['gmt_start_time']  = $row['promote_start_date'];
        }
        else
        {
            $row['gmt_end_time'] = 0;
        }
		
		
        /* 是否显示商品库存数量 */
        $row['goods_number']  = ($GLOBALS['_CFG']['use_storage'] == 1) ? $row['goods_number'] : '';
        $row['url']  		  = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
		
        /* 修正积分：转换为可使用多少积分（原来是可以使用多少钱的积分） */
        $row['integral']      = $GLOBALS['_CFG']['integral_scale'] ? round($row['integral'] * 100 / $GLOBALS['_CFG']['integral_scale']) : 0;

        /* 修正优惠券 */
        $row['bonus_money']   = ($row['bonus_money'] == 0) ? 0 : price_format($row['bonus_money'], false);

        /* 修正商品图片 */
        if (empty($row['goods_img']))
        {
            $row['goods_img'] = $GLOBALS['_CFG']['no_picture'];
        }
        if (empty($row['goods_thumb']))
        {
            $row['goods_thumb'] = $GLOBALS['_CFG']['no_picture'];
        }
		
		$row['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);

		$row['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
		                       sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
		$row['short_style_name']   = add_style($row['short_name'],$row['goods_name_style']);
		$row['thumb']        = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
		$row['goods_img']    = empty($row['goods_img'])   ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];

        return $row;
    }
    else
    {
        return false;
    }
}


/**
 * 获得商品的详细信息
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  void
 */
function get_goods_less_info($goods_id)
{
    $time = gmtime();
    $sql = 'SELECT g.* ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            "WHERE g.goods_id = '$goods_id' AND g.is_delete = 0 " ;

    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        
        /* 修正促销价格 */
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }


		/* 获得商品的销售价格 格式 ：RMB XX 元*/
        $row['market_price_formated']	= price_format($row['market_price']);
        $row['shop_price_formated'] 	= price_format($row['shop_price']);
		$row['salebase_price_formated'] = price_format($row['salebase_price']);
        $row['promote_price_formated'] 	= price_format($promote_price);
		$row['agency_price_formated']  	= price_format($row['agency_price']);

        
        $row['promote_price_org'] =  $promote_price;  //数字格式的促销价
		$row['salebase_price_num']= $row['salebase_price'];
        $row['shop_price_num']    = $row['shop_price'];
       	

        /* 是否显示商品库存数量 */
        $row['goods_number']  = ($GLOBALS['_CFG']['use_storage'] == 1) ? $row['goods_number'] : '';
        $row['url']  		  = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
		

        /* 修正商品图片 */
        if (empty($row['goods_img']))
        {
            $row['goods_img'] = $GLOBALS['_CFG']['no_picture'];
        }
        if (empty($row['goods_thumb']))
        {
            $row['goods_thumb'] = $GLOBALS['_CFG']['no_picture'];
        }
		
		$row['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);

		$row['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
		                       sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
		$row['short_style_name']   = add_style($row['short_name'],$row['goods_name_style']);
		$row['thumb']        = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
		$row['goods_img']    = empty($row['goods_img'])   ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];
		

        return $row;
    }
    else
    {
        return false;
    }
}

/**
 * 获得商品的属性和规格
 *
 * @access  public
 * @param   integer $goods_id
 * @return  array
 */
function get_goods_properties($goods_id)
{
    /* 对属性进行重新排序和分组 */
    $sql = "SELECT attr_group ".
            "FROM " . $GLOBALS['ecs']->table('goods_type') . " AS gt, " . $GLOBALS['ecs']->table('goods') . " AS g ".
            "WHERE g.goods_id='$goods_id' AND gt.cat_id=g.goods_type";
    $grp = $GLOBALS['db']->getOne($sql);

    if (!empty($grp))
    {
        $groups = explode("\n", strtr($grp, "\r", ''));
    }

    /* 获得商品的规格 */
    $sql = "SELECT a.attr_id, a.attr_name, a.attr_group, a.is_linked, a.attr_type, a.attr_img, a.attr_brief, a.attr_desc, ".
                "g.goods_attr_id, g.attr_value, g.attr_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods_attr') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
            "WHERE g.goods_id = '$goods_id' " .
            'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';
    $res = $GLOBALS['db']->getAll($sql);

    $arr['pro'] = array();     // 属性
    $arr['spe'] = array();     // 规格
    $arr['lnk'] = array();     // 关联的属性

    foreach ($res AS $row)
    {
        if ($row['attr_type'] == 0)
        {
            $group = (isset($groups[$row['attr_group']])) ? $groups[$row['attr_group']] : $GLOBALS['_LANG']['goods_attr'];

            $arr['pro'][$group][$row['attr_id']]['name']  = $row['attr_name'];
            $arr['pro'][$group][$row['attr_id']]['value'] = $row['attr_value'];
        }
        else
        {
            $arr['spe'][$row['attr_id']]['name']     = $row['attr_name'];
			$arr['spe'][$row['attr_id']]['brief']    = $row['attr_brief'];
			$arr['spe'][$row['attr_id']]['desc']     = $row['attr_desc'];
			$arr['spe'][$row['attr_id']]['img']      = 'data/attrimg/'.$row['attr_img'];
            $arr['spe'][$row['attr_id']]['values'][] = array(
                                                        'label'        => $row['attr_value'],
                                                        'price'        => $row['attr_price'],
                                                        'format_price' => price_format(abs($row['attr_price']), false),
                                                        'id'           => $row['goods_attr_id']);
        }

        if ($row['is_linked'] == 1)
        {
            /* 如果该属性需要关联，先保存下来 */
            $arr['lnk'][$row['attr_id']]['name']  = $row['attr_name'];
            $arr['lnk'][$row['attr_id']]['value'] = $row['attr_value'];
        }
    }
	//echo "<pre>".print_r($arr)."</pre>";
    return $arr;
}

/**
 * 获得属性相同的商品
 *
 * @access  public
 * @param   array   $attr   // 包含了属性名称,ID的数组
 * @return  array
 */
function get_same_attribute_goods($attr)
{
    $lnk = array();

    if (!empty($attr))
    {
        foreach ($attr['lnk'] AS $key => $val)
        {
            $lnk[$key]['title'] = sprintf($GLOBALS['_LANG']['same_attrbiute_goods'], $val['name'], $val['value']);

            /* 查找符合条件的商品 */
            $sql = 'SELECT g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
                        "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                        'g.market_price, g.promote_price, g.promote_start_date, g.promote_end_date ' .
                    'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_attr') . ' as a ON g.goods_id = a.goods_id ' .
                    "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                        "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
                    "WHERE a.attr_id = '$key' AND a.attr_value = '$val[value]' AND g.goods_id <> '$_REQUEST[id]' " .
                    'LIMIT ' . $GLOBALS['_CFG']['attr_related_number'];
			//echo $sql;
            $res = $GLOBALS['db']->getAll($sql);
			
			//print_r($res);
            foreach ($res AS $row)
            {
                $lnk[$key]['goods'][$row['goods_id']]['goods_id']      = $row['goods_id'];
                $lnk[$key]['goods'][$row['goods_id']]['goods_name']    = $row['goods_name'];
                $lnk[$key]['goods'][$row['goods_id']]['short_name']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
                $lnk[$key]['goods'][$row['goods_id']]['goods_img']     = (empty($row['goods_img'])) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];
                $lnk[$key]['goods'][$row['goods_id']]['market_price']  = price_format($row['market_price']);
                $lnk[$key]['goods'][$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
                $lnk[$key]['goods'][$row['goods_id']]['promote_price'] = bargain_price($row['promote_price'],
                    $row['promote_start_date'], $row['promote_end_date']);
                $lnk[$key]['goods'][$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
            }
        }
    }

    return $lnk;
}

/**
 * 获得指定商品的相册
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_goods_gallery($goods_id)
{
    $sql = 'SELECT img_id, img_url, thumb_url, img_desc' .
        ' FROM ' . $GLOBALS['ecs']->table('goods_gallery') .
        " WHERE goods_id = '$goods_id' LIMIT " . $GLOBALS['_CFG']['goods_gallery_number'];
    $row = $GLOBALS['db']->getAll($sql);

    return $row;
}

/**
 * 获得指定分类下的商品
 *
 * @access  public
 * @param   integer     $cat_id     分类ID
 * @param   integer     $num        数量
 * @param   string      $from       来自web/wap的调用
 * @return  array
 */
function assign_cat_goods($cat_id, $num = 0, $from = 'web')
{
    $children = get_children($cat_id);

    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
               'g.promote_price, promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img ' .
            "FROM " . $GLOBALS['ecs']->table('goods') . ' AS g '.
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND '.
                'g.is_delete = 0 AND (' . $children . 'OR ' . get_extension_goods($children) . ') ' .
            'ORDER BY g.sort_order, g.goods_id DESC';
    if ($num > 0)
    {
        $sql .= ' LIMIT ' . $num;
    }
    $res = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($res AS $idx => $row)
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id']           = $row['goods_id'];
        $goods[$idx]['name']         = $row['goods_name'];
        $goods[$idx]['brief']        = $row['goods_brief'];
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                        sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['thumb']        = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
        $goods[$idx]['goods_img']    = empty($row['goods_img']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

    }

    if ($from == 'web')
    {
        $GLOBALS['smarty']->assign('cat_goods_' . $cat_id, $goods);
    }
    elseif ($from == 'wap')
    {
        $cat['goods'] = $goods;
    }

    /* 分类信息 */
    $sql = 'SELECT cat_name FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
    $cat['name'] = $GLOBALS['db']->getOne($sql);
    $cat['url']  = build_uri('category', array('cid' => $cat_id), $cat['name']);
    $cat['id']   = $cat_id;

    return $cat;
}

/**
 * 获得指定的品牌下的商品
 *
 * @access  public
 * @param   integer     $brand_id       品牌的ID
 * @param   integer     $num            数量
 * @param   integer     $cat_id         分类编号
 * @return  void
 */
function assign_brand_goods($brand_id, $num = 0, $cat_id = 0)
{
    $sql =  'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'g.promote_price, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand_id'";

    if ($cat_id > 0)
    {
        $sql .= get_children($cat_id);
    }

    $sql .= ' ORDER BY g.sort_order, g.goods_id DESC';
    if ($num > 0)
    {
        $res = $GLOBALS['db']->selectLimit($sql, $num);
    }
    else
    {
        $res = $GLOBALS['db']->query($sql);
    }

    $idx = 0;
    $goods = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        $goods[$idx]['id']            = $row['goods_id'];
        $goods[$idx]['name']          = $row['goods_name'];
        $goods[$idx]['short_name']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['market_price']  = $row['market_price'];
        $goods[$idx]['shop_price']    = price_format($row['shop_price']);
        $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        $goods[$idx]['brief']         = $row['goods_brief'];
        $goods[$idx]['thumb']         = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
        $goods[$idx]['goods_img']     = empty($row['goods_img']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];
        $goods[$idx]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        $idx++;
    }

    /* 分类信息 */
    $sql = 'SELECT brand_name FROM ' . $GLOBALS['ecs']->table('brand') . " WHERE brand_id = '$brand_id'";

    $brand['id']   = $brand_id;
    $brand['name'] = $GLOBALS['db']->getOne($sql);
    $brand['url']  = build_uri('brand', array('bid' => $brand_id), $brand['name']);

    $brand_goods = array('brand' => $brand, 'goods' => $goods);

    return $brand_goods;
}

/**
 * 获得所有扩展分类属于指定分类的所有商品ID
 *
 * @access  public
 * @param   string $cat_id     分类查询字符串
 * @return  string
 */
function get_extension_goods($cats)
{
    $sql = 'SELECT goods_id FROM ' . $GLOBALS['ecs']->table('goods_cat') . " AS g WHERE $cats";
    $row = $GLOBALS['db']->getCol($sql);

    return db_create_in($row, 'g.goods_id');
}

/**
 * 判断某个商品是否正在特价促销期
 *
 * @access  public
 * @param   float   $price      促销价格
 * @param   string  $start      促销开始日期
 * @param   string  $end        促销结束日期
 * @return  float   如果还在促销期则返回促销价，否则返回0
 */
function bargain_price($price, $start, $end)
{
    if ($price == 0)
    {
        return 0;
    }
    else
    {
        $time = gmtime();
        if ($time >= $start && $time <= $end)
        {
            return $price;
        }
        else
        {
            return 0;
        }
    }
}

/**
 * 获得指定的规格的价格
 *
 * @access  public
 * @param   mix     $spec   规格ID的数组或者逗号分隔的字符串
 * @return  void
 */
function spec_price($spec)
{
    if (!empty($spec))
    {
        $where = db_create_in($spec, 'goods_attr_id');

        $sql = 'SELECT SUM(attr_price) AS attr_price FROM ' . $GLOBALS['ecs']->table('goods_attr') . " WHERE $where";
        $price = floatval($GLOBALS['db']->getOne($sql));
    }
    else
    {
        $price = 0;
    }

    return $price;
}

/**
 * 取得团购活动信息
 * @param   int     $group_buy_id   团购活动id
 * @param   int     $current_num    本次购买数量（计算当前价时要加上的数量）
 * @return  array
 *                  status          状态：
 */
function group_buy_info($group_buy_id, $current_num = 0)
{
    /* 取得团购活动信息 */
    $group_buy_id = intval($group_buy_id);
    $sql = "SELECT *, act_id AS group_buy_id, act_desc AS group_buy_desc, start_time AS start_date, end_time AS end_date " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            "WHERE act_id = '$group_buy_id' " .
            "AND act_type = '" . GAT_GROUP_BUY . "'";
    $group_buy = $GLOBALS['db']->getRow($sql);

    /* 如果为空，返回空数组 */
    if (empty($group_buy))
    {
        return array();
    }

    $ext_info = unserialize($group_buy['ext_info']);
    $group_buy = array_merge($group_buy, $ext_info);

    /* 格式化时间 */
    $group_buy['formated_start_date'] = local_date('Y-m-d H:i', $group_buy['start_time']);
    $group_buy['formated_end_date'] = local_date('Y-m-d H:i', $group_buy['end_time']);

    /* 格式化保证金 */
    $group_buy['formated_deposit'] = price_format($group_buy['deposit'], false);

    /* 处理价格阶梯 */
    $price_ladder = $group_buy['price_ladder'];
    if (!is_array($price_ladder) || empty($price_ladder))
    {
        $price_ladder = array(array('amount' => 0, 'price' => 0));
    }
    else
    {
        foreach ($price_ladder as $key => $amount_price)
        {
            $price_ladder[$key]['formated_price'] = price_format($amount_price['price'], false);
        }
    }
    $group_buy['price_ladder'] = $price_ladder;

    /* 统计信息 */
    $stat = group_buy_stat($group_buy_id, $group_buy['deposit']);
    $group_buy = array_merge($group_buy, $stat);

    /* 计算当前价 */
    $cur_price  = $price_ladder[0]['price']; // 初始化
    $cur_amount = $stat['valid_goods'] + $current_num; // 当前数量
    foreach ($price_ladder as $amount_price)
    {
        if ($cur_amount >= $amount_price['amount'])
        {
            $cur_price = $amount_price['price'];
        }
        else
        {
            break;
        }
    }
    $group_buy['cur_price'] = $cur_price;
    $group_buy['formated_cur_price'] = price_format($cur_price, false);

    /* 最终价 */
    $group_buy['trans_price'] = $group_buy['cur_price'];
    $group_buy['formated_trans_price'] = $group_buy['formated_cur_price'];
    $group_buy['trans_amount'] = $group_buy['valid_goods'];

    /* 状态 */
    $group_buy['status'] = group_buy_status($group_buy);
    if (isset($GLOBALS['_LANG']['gbs'][$group_buy['status']]))
    {
        $group_buy['status_desc'] = $GLOBALS['_LANG']['gbs'][$group_buy['status']];
    }

    $group_buy['start_time'] = $group_buy['formated_start_date'];
    $group_buy['end_time'] = $group_buy['formated_end_date'];

    return $group_buy;
}

/*
 * 取得某团购活动统计信息
 * @param   int     $group_buy_id   团购活动id
 * @param   float   $deposit        保证金
 * @return  array   统计信息
 *                  total_order     总订单数
 *                  total_goods     总商品数
 *                  valid_order     有效订单数
 *                  valid_goods     有效商品数
 */
function group_buy_stat($group_buy_id, $deposit)
{
    /* 取得总订单数和总商品数 */
    $group_buy_id = intval($group_buy_id);
    $sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " .
            "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
                $GLOBALS['ecs']->table('order_goods') . " AS g " .
            " WHERE o.order_id = g.order_id " .
            "AND o.extension_code = 'group_buy' " .
            "AND o.extension_id = '$group_buy_id' " .
            "AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
    $stat = $GLOBALS['db']->getRow($sql);
    if ($stat['total_order'] == 0)
    {
        $stat['total_goods'] = 0;
    }

    /* 取得有效订单数和有效商品数 */
    $deposit = floatval($deposit);
    if ($deposit > 0 && $stat['total_order'] > 0)
    {
        $sql .= " AND (o.money_paid + o.surplus) >= '$deposit'";
        $row = $GLOBALS['db']->getRow($sql);
        $stat['valid_order'] = $row['total_order'];
        if ($stat['valid_order'] == 0)
        {
            $stat['valid_goods'] = 0;
        }
        else
        {
            $stat['valid_goods'] = $row['total_goods'];
        }
    }
    else
    {
        $stat['valid_order'] = $stat['total_order'];
        $stat['valid_goods'] = $stat['total_goods'];
    }

    return $stat;
}

/**
 * 获得团购的状态
 *
 * @access  public
 * @param   array
 * @return  integer
 */
function group_buy_status($group_buy)
{
    $now = gmtime();
    if ($group_buy['is_finished'] == 0)
    {
        /* 未处理 */
        if ($now < $group_buy['start_time'])
        {
            $status = GBS_PRE_START;
        }
        elseif ($now > $group_buy['end_time'])
        {
            $status = GBS_FINISHED;
        }
        else
        {
            if ($group_buy['restrict_amount'] == 0 || $group_buy['valid_goods'] < $group_buy['restrict_amount'])
            {
                $status = GBS_UNDER_WAY;
            }
            else
            {
                $status = GBS_FINISHED;
            }
        }
    }
    elseif ($group_buy['is_finished'] == GBS_SUCCEED)
    {
        /* 已处理，团购成功 */
        $status = GBS_SUCCEED;
    }
    elseif ($group_buy['is_finished'] == GBS_FAIL)
    {
        /* 已处理，团购失败 */
        $status = GBS_FAIL;
    }

    return $status;
}

/**
 * 取得拍卖活动信息
 * @param   int     $act_id     活动id
 * @return  array
 */
function auction_info($act_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods_activity') . " WHERE act_id = '$act_id'";
    $auction = $GLOBALS['db']->getRow($sql);
    if ($auction['act_type'] != GAT_AUCTION)
    {
        return array();
    }
    $auction['status_no'] = auction_status($auction);
    $auction['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $auction['start_time']);
    $auction['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $auction['end_time']);
    $ext_info = unserialize($auction['ext_info']);
    $auction = array_merge($auction, $ext_info);
    $auction['formated_start_price'] = price_format($auction['start_price']);
    $auction['formated_end_price'] = price_format($auction['end_price']);
    $auction['formated_amplitude'] = price_format($auction['amplitude']);
    $auction['formated_deposit'] = price_format($auction['deposit']);

    /* 查询出价用户数和最后出价 */
    $sql = "SELECT COUNT(DISTINCT bid_user) FROM " . $GLOBALS['ecs']->table('auction_log') .
            " WHERE act_id = '$act_id'";
    $auction['bid_user_count'] = $GLOBALS['db']->getOne($sql);
    if ($auction['bid_user_count'] > 0)
    {
        $sql = "SELECT a.*, u.user_name " .
                "FROM " . $GLOBALS['ecs']->table('auction_log') . " AS a, " .
                        $GLOBALS['ecs']->table('users') . " AS u " .
                "WHERE a.bid_user = u.user_id " .
                "AND act_id = '$act_id' " .
                "ORDER BY a.log_id DESC";
        $row = $GLOBALS['db']->getRow($sql);
        $row['formated_bid_price'] = price_format($row['bid_price'], false);
        $row['bid_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['bid_time']);
        $auction['last_bid'] = $row;
    }

    /* 查询已确认订单数 */
    if ($auction['status_no'] > 1)
    {
        $sql = "SELECT COUNT(*)" .
                " FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE extension_code = 'auction'" .
                " AND extension_id = '$act_id'" .
                " AND order_status " . db_create_in(array(OS_CONFIRMED, OS_UNCONFIRMED));
        $auction['order_count'] = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        $auction['order_count'] = 0;
    }

    /* 当前价 */
    $auction['current_price'] = isset($auction['last_bid']) ? $auction['last_bid']['bid_price'] : $auction['start_price'];
    $auction['formated_current_price'] = price_format($auction['current_price'], false);

    return $auction;
}

/**
 * 取得拍卖活动出价记录
 * @param   int     $act_id     活动id
 * @return  array
 */
function auction_log($act_id)
{
    $log = array();
    $sql = "SELECT a.*, u.user_name " .
            "FROM " . $GLOBALS['ecs']->table('auction_log') . " AS a," .
                      $GLOBALS['ecs']->table('users') . " AS u " .
            "WHERE a.bid_user = u.user_id " .
            "AND act_id = '$act_id' " .
            "ORDER BY a.log_id DESC";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['bid_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['bid_time']);
        $row['formated_bid_price'] = price_format($row['bid_price'], false);
        $log[] = $row;
    }

    return $log;
}

/**
 * 计算拍卖活动状态（注意参数一定是原始信息）
 * @param   array   $auction    拍卖活动原始信息
 * @return  int
 */
function auction_status($auction)
{
    $now = gmtime();
    if ($auction['is_finished'] == 0)
    {
        if ($now < $auction['start_time'])
        {
            return PRE_START; // 未开始
        }
        elseif ($now > $auction['end_time'])
        {
            return FINISHED; // 已结束，未处理
        }
        else
        {
            return UNDER_WAY; // 进行中
        }
    }
    elseif ($auction['is_finished'] == 1)
    {
        return FINISHED; // 已结束，未处理
    }
    else
    {
        return SETTLED; // 已结束，已处理
    }
}

/**
 * 取得商品信息
 * @param   int     $goods_id   商品id
 * @return  array
 */
function goods_info($goods_id)
{
    $sql = "SELECT g.*, b.brand_name " .
            "FROM " . $GLOBALS['ecs']->table('goods') . " AS g " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('brand') . " AS b ON g.brand_id = b.brand_id " .
            "WHERE g.goods_id = '$goods_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
        /* 修正重量显示 */
        $row['goods_weight'] = (intval($row['goods_weight']) > 0) ?
            $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
            ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正图片 */
        if (empty($row['goods_img']))
        {
            $row['goods_img'] = $GLOBALS['_CFG']['no_picture'];
        }
    }

    return $row;
}

/**
 * 取得优惠活动信息
 * @param   int     $act_id     活动id
 * @return  array
 */
function favourable_info($act_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE act_id = '$act_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
        $row['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['start_time']);
        $row['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['end_time']);
        $row['formated_min_amount'] = price_format($row['min_amount']);
        $row['formated_max_amount'] = price_format($row['max_amount']);
        $row['gift'] = unserialize($row['gift']);
        if ($row['act_type'] == FAT_GOODS)
        {
            $row['act_type_ext'] = round($row['act_type_ext']);
        }
    }

    return $row;
}

/**
 * 批发信息
 * @param   int     $act_id     活动id
 * @return  array
 */
function wholesale_info($act_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('wholesale') .
            " WHERE act_id = '$act_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
        $row['price_list'] = unserialize($row['prices']);
    }

    return $row;
}

/**
 * 添加商品名样式
 * @param   string     $goods_name     商品名称
 * @param   string     $style          样式参数
 * @return  string
 */
function add_style($goods_name, $style)
{
    $goods_style_name = $goods_name;

    $arr   = explode('+', $style);

    $font_color     = !empty($arr[0]) ? $arr[0] : '';
    $font_style = !empty($arr[1]) ? $arr[1] : '';

    if ($font_color!='')
    {
        $goods_style_name = '<font color=' . $font_color . '>' . $goods_style_name . '</font>';
    }
    if ($font_style != '')
    {
        $goods_style_name = '<' . $font_style .'>' . $goods_style_name . '</' . $font_style . '>';
    }
    return $goods_style_name;
}

/**
 * 取得商品属性
 * @param   int     $goods_id   商品id
 * @return  array
 */
function get_goods_attr($goods_id)
{
    $attr_list = array();
    $sql = "SELECT a.attr_id, a.attr_name " .
            "FROM " . $GLOBALS['ecs']->table('goods') . " AS g, " . $GLOBALS['ecs']->table('attribute') . " AS a " .
            "WHERE g.goods_id = '$goods_id' " .
            "AND g.goods_type = a.cat_id " .
            "AND a.attr_type = 1";
    $attr_id_list = $GLOBALS['db']->getCol($sql);
    $res = $GLOBALS['db']->query($sql);
    while ($attr = $GLOBALS['db']->fetchRow($res))
    {
        if (defined('ECS_ADMIN'))
        {
            $attr['goods_attr_list'] = array(0 => $GLOBALS['_LANG']['select_please']);
        }
        else
        {
            $attr['goods_attr_list'] = array();
        }
        $attr_list[$attr['attr_id']] = $attr;
    }

    $sql = "SELECT attr_id, goods_attr_id, attr_value " .
            "FROM " . $GLOBALS['ecs']->table('goods_attr') .
            " WHERE goods_id = '$goods_id' " .
            "AND attr_id " . db_create_in($attr_id_list);
    $res = $GLOBALS['db']->query($sql);
    while ($goods_attr = $GLOBALS['db']->fetchRow($res))
    {
        $attr_list[$goods_attr['attr_id']]['goods_attr_list'][$goods_attr['goods_attr_id']] = $goods_attr['attr_value'];
    }

    return $attr_list;
}

?>