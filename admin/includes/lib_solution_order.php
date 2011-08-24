<?php

/**
 * SINEMALL 管理中心商品相关函数    $Author: testyang $
 * $Id: lib_goods.php 14481 2008-04-18 11:23:01Z testyang $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 取得推荐类型列表
 * @return  array   推荐类型列表
 */
function get_intro_list()
{
    return array(
        'is_best'    => $GLOBALS['_LANG']['is_best'],
        'is_new'     => $GLOBALS['_LANG']['is_new'],
        'is_hot'     => $GLOBALS['_LANG']['is_hot'],
        'is_promote' => $GLOBALS['_LANG']['is_promote'],
    );
}

/**
 * 取得品牌列表
 * @return array 品牌列表 id => name
 */
function get_solution_step_list()
{
    $sql = 'SELECT step_id, step_name FROM ' . $GLOBALS['ecs']->table('solution_step') . ' ORDER BY step_id ASC';
    $res = $GLOBALS['db']->getAll($sql);

    $step_list = array();
    foreach ($res AS $row)
    {
        $step_list[$row['step_id']] = $row['step_name'];
    }

    return $step_list;
}
//
function solution_detail_info($order_id,$step_id)
{
	$sql = "SELECT * ".
            'FROM ' . $GLOBALS['ecs']->table('solution_order_detail') .
			"WHERE order_id = ".$order_id ." AND step_id = " . $step_id ;
	//echo $sql."<br />";
    $res = $GLOBALS['db']->getRow($sql);
	return $res;
}
/**
 * 前台取得商品属性：
 * @param   只查询 goods库
 */
function get_one_goods_info($goods_id)
{
	//$where = " WHERE step_id = $step_id";
    /* 取得数据 */
    $sql = 'SELECT * '.
           'FROM ' . $GLOBALS['ecs']->table('goods') . " WHERE goods_id = '$goods_id' " ;
    $row = $GLOBALS['db']->getRow($sql);

    return $row;
}

/**
 * 获得指定分类下的子分类的数组
 *
 * @access  public
 * @param   int     $cat_id     分类的ID
 * @param   int     $selected   当前选中分类的ID
 * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
 * @param   int     $level      限定返回的级数。为0时返回所有级数
 * @param   int     $is_show_all 如果为true显示所有分类，如果为false隐藏不可见分类。
 * @return  mix
 */

function solution_list($order_id, $solution_id = 0, $selected = 0, $re_type = true, $level = 0, $is_show_all = true)
{
    static $res = NULL;

	if($solution_id == 0){
		$where = "WHERE 1";		
	}else{
		$where = "WHERE c.clusters_id =". $solution_id;	
	}

    if ($res === NULL)
    {
        $sql = "SELECT c.*, COUNT(s.solution_id) AS has_children  ".
                'FROM ' . $GLOBALS['ecs']->table('solution') . " AS c ".
                "LEFT JOIN " . $GLOBALS['ecs']->table('solution') . " AS s ON s.parent_id=c.solution_id ".
 //               "LEFT JOIN " . $GLOBALS['ecs']->table('solution_order') . " AS o ON o.solution_id = c.solution_id ".
				$where.
                " GROUP BY c.solution_id ".
                ' ORDER BY parent_id, sort_step ASC';
		//echo $sql."<br />";
        $res = $GLOBALS['db']->getAllCached($sql);

		/*
        $sql = "SELECT c.solution_id as solution_id, COUNT(g.goods_id) AS goods_num ".
                'FROM ' . $GLOBALS['ecs']->table('solution') . " AS c ".
                "LEFT JOIN " . $GLOBALS['ecs']->table('solution_step') . " AS g ON g.solution_id=c.solution_id ".
                "GROUP BY c.solution_id ";
        $res2 = $GLOBALS['db']->getAllCached($sql);

        $newres = array();
        foreach($res2 as $k=>$v)
        {
            $newres[$v['solution_id']] = $v['goods_num'];
        }
		*/
        foreach($res as $k=>$val)
        {
			$res[$k]['goods_num'] =  $res[$k]['has_children'];
			
            $order_detail = solution_detail_info($order_id,$val['step_id']);
			$res[$k]['goods_id'] =  $order_detail['goods_id'];			
			$res[$k]['order_price'] =  $order_detail['order_price'];
			$res[$k]['action_note'] =  $order_detail['action_note'];
			$res[$k]['goods_count'] =  $order_detail['goods_count'];
			
			$goods_info = get_one_goods_info($order_detail['goods_id']);
			$res[$k]['goods_name'] =  $goods_info['goods_name'];
			$res[$k]['shop_price'] =  $goods_info['shop_price'];
			
            
        }
    }

    if (empty($res) == true)
    {
        return $re_type ? '' : array();
    }
	//print_r($res);
    $options = solution_options(0, $res); // 获得指定分类下的子分类的数组

    $children_level = 99999; //大于这个分类的将被删除
    if ($is_show_all == false)
    {
        foreach ($options as $key => $val)
        {
            if ($val['level'] > $children_level)
            {
                unset($options[$key]);
            }
            else
            {
                if ($val['is_show'] == 0)
                {
                    unset($options[$key]);
                    if ($children_level > $val['level'])
                    {
                        $children_level = $val['level']; //标记一下，这样子分类也能删除
                    }
                }
                else
                {
                    $children_level = 99999; //恢复初始值
                }
            }
        }
    }

    /* 截取到指定的缩减级别 */
    if ($level > 0)
    {
        if ($solution_id == 0)
        {
            $end_level = $level;
        }
        else
        {
            $first_item = reset($options); // 获取第一个元素
            $end_level  = $first_item['level'] + $level;
        }

        /* 保留level小于end_level的部分 */
        foreach ($options AS $key => $val)
        {
            if ($val['level'] >= $end_level)
            {
                unset($options[$key]);
            }
        }
    }

    if ($re_type == true)
    {
        $select = '';
        foreach ($options AS $var)
        {
            $select .= '<option value="' . $var['solution_id'] . '" ';
            $select .= ($selected == $var['solution_id']) ? "selected='ture'" : '';
            $select .= '>';
            if ($var['level'] > 0)
            {
                $select .= str_repeat('&nbsp;', $var['level'] * 4);
            }
            $select .= htmlspecialchars($var['solution_name'], ENT_QUOTES) . '</option>';
        }

        return $select;
    }
    else
    {
        foreach ($options AS $key => $value)
        {
            $options[$key]['url'] = build_uri('solution', array('cid' => $value['solution_id']), $value['solution_name']);
        }

        return $options;
    }
}

/**
 * 取得品牌列表
 * @return array 品牌列表 id => name
 */
function get_solution_list($solution_id)
{
    $sql = 'SELECT solution_id, solution_name FROM ' . $GLOBALS['ecs']->table('solution') ;
	if($solution_id){
		$sql.= " WHERE 1  AND solution_id = $solution_id";
	}
	//echo $sql;
    $res = $GLOBALS['db']->getAll($sql);

    $solution_list = array();
    foreach ($res AS $row)
    {
        $solution_list[$row['solution_id']] = $row['solution_name'];
    }

    return $solution_list;
}

/**
 * 获取solution order 列表
 *
 * @access  public
 * @return  array
 */
function get_solution_order_list()
{
    $filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
    $filter['contact_id'] = empty($_REQUEST['contact_id']) ? 0 : intval($_REQUEST['contact_id']);
    $filter['solution_id'] = empty($_REQUEST['solution_id']) ? 0 : intval($_REQUEST['solution_id']);
    $filter['order_amount'] = empty($_REQUEST['order_amount']) ? 0 : intval($_REQUEST['order_amount']);
    //$filter['inv_price'] = empty($_REQUEST['inv_price']) ? 0 : floatval($_REQUEST['inv_price']);
    $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);
	
	$filter['solution_name'] = empty($_REQUEST['solution_name']) ? '' : trim($_REQUEST['solution_name']);
	
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'order_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = 'WHERE 1 ';
    if ($filter['user_id'])
    {
        $where .= " AND o.user_id = '$filter[user_id]'";
    }
    if ($filter['contact_id'])
    {
        $where .= " AND o.contact_id = '$filter[contact_id]'";
    }
    if ($filter['solution_id'])
    {
        $where .= " AND o.solution_id = '$filter[solution_id]'";
    }
    if ($filter['order_amount'])
    {
        $where .= " AND o.order_amount > '$filter[order_amount]'";
    }
	if ($filter['solution_name'])
    {
        $where .= " AND g.solution_name LIKE '%" . mysql_like_quote($filter['solution_name']) . "%'";
    }
	if ($filter['start_time'] AND $filter['end_time'] )
    {
        $where .= " AND o.add_time >= '$filter[start_time]'";
		$where .= " AND o.add_time <= '$filter[end_time]'";
    }

	/* 分页大小 */
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    }
    elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
    {
        $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
    }
    else
    {
        $filter['page_size'] = 15;
    }
	
	/* 记录总数 */
    if ($filter['user_name'])
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_info') . " AS o ,".
               $GLOBALS['ecs']->table('users') . " AS u " . $where;
    }
    else
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('solution_order') ." AS o " . $where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$sql = 'SELECT '.
				'o.order_id,o.user_id, o.contact_id, o.add_time, o.solution_id,' .
                'o.order_note,o.order_amount, '.
				'g.solution_name, st.user_name AS user_name, ct.user_name AS custom_name'.
            " FROM " . $GLOBALS['ecs']->table('solution_order') . " AS o ".
            " LEFT JOIN " .$GLOBALS['ecs']->table('solution') ." AS g ON g.solution_id=o.solution_id " .
			" LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS st ON st.user_id =o.user_id ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS ct ON ct.user_id =o.contact_id ".
            "$where ORDER BY $filter[sort_by] $filter[sort_order] ".
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
			//echo $sql;
	
			$row = $GLOBALS['db']->getAll($sql);
			//print_r($row);
		 	/* 格式话数据 */
		    foreach ($row AS $key => $value)
		    {
		        $row[$key]['add_time'] = local_date('Y-m-d H:i:s', $row[$key]['add_time']);
		    }
			//print_r($row);
			$arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
		    return $arr;
}



/**
 * 获取品牌列表
 *
 * @access  public
 * @return  array
 */
function get_steplist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

        /* 记录总数以及页数 */
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('solution_step');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 查询记录 */
        $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('solution_step')." ORDER BY step_id ASC";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $step_logo = empty($rows['step_logo']) ? '' :
            '<a href="../data/steplogo/'.$rows['step_logo'].'" target="_brank"><img src="../data/steplogo/'.$rows['step_logo'].'" width="30" height="30" border="0" alt='.$GLOBALS['_LANG']['step_logo'].' /></a>';
        $rows['step_logo'] = $step_logo;
        $rows['formated_step_goods']   = $rows['step_goods'];

        $arr[] = $rows;
    }

    return array('step' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


/**
 * 获取品牌列表
 *
 * @access  public
 * @return  array
 */
function get_brandlist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();

        /* 记录总数以及页数 */
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('brand');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 查询记录 */
        $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('brand')." ORDER BY sort_order ASC";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $brand_logo = empty($rows['brand_logo']) ? '' :
            '<a href="../data/steplogo/'.$rows['brand_logo'].'" target="_brank"><img src="images/picflag.gif" width="16" height="16" border="0" alt='.$GLOBALS['_LANG']['brand_logo'].' /></a>';
        $site_url   = empty($rows['site_url']) ? 'N/A' : '<a href="'.$rows['site_url'].'" target="_brank">'.$rows['site_url'].'</a>';
		$is_commend = $rows['is_commend'];
        $rows['brand_logo'] = $brand_logo;
        $rows['site_url']   = $site_url;
		$rows['is_commend'] = $is_commend;
        $arr[] = $rows;
    }

    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}



?>
