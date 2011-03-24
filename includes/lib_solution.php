<?php

/**
 * SINEMALL 公用函数库 * $Author: testyang $
 * $Id: lib_common.php 14699 2008-07-04 07:36:04Z testyang $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
define('IN_ECS', true);
function get_single_solution_info($solution_id)
{
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('solution') . " WHERE solution_id = '$solution_id'";
    $solution = $GLOBALS['db']->getRow($sql);
	return $solution;
    
}
function p_get_first_child($solution_id)
{
	$sql = 'SELECT * ' .
                'FROM ' . $GLOBALS['ecs']->table('solution')  .
                "WHERE clusters_id = '$solution_id' AND parent_id = '$solution_id'  ".
 				"ORDER BY sort_step ASC";
    $child = $GLOBALS['db']->getAll($sql);
	
	return $child;
}
//获得子分类下的详细
function get_first_child($solution_id)
{
	$sql = 'SELECT * ' .
                'FROM ' . $GLOBALS['ecs']->table('solution')  .
                "WHERE clusters_id = '$solution_id' AND parent_id = '$solution_id'  ".
 				"ORDER BY sort_step ASC";
    $child = $GLOBALS['db']->getAll($sql);
    foreach ($child AS $key => $val)
    {
        $child[$key]['detail_values'] = get_child_detail($val['solution_id']);
    }

	return $child;
}
function get_child_detail($solution_id)
{
	$sql = 	"SELECT c.* , s.* ".
	            'FROM ' . $GLOBALS['ecs']->table('solution') . " AS c ".
	            "LEFT JOIN " . $GLOBALS['ecs']->table('solution_step') . " AS s ON s.step_id=c.step_id ".
				" WHERE parent_id = '$solution_id'  ".
	           	' ORDER BY sort_step ASC';
	
    $detail = $GLOBALS['db']->getAll($sql);

	foreach ($detail AS $key => $val)
    {		
		if(haschild($detail[$key]['solution_id']))
		{
			$detail[$key]['haschild'] = haschild($val['solution_id']);
			//$detail[$key]['goods_list'] =  get_step_goods_list($val['step_goods']);
			$detail[$key]['final_child'] = get_child_detail($val['solution_id']);
		}else
		{
			$detail[$key]['goods_list'] =  get_step_goods_list($val['step_goods']);
		}
		
    }
	return $detail;
}
function haschild($solution_id)
{
	$sql = 	"SELECT COUNT(*) AS child_number ".
	 		'FROM ' . $GLOBALS['ecs']->table('solution') . 
			" WHERE parent_id = '$solution_id' ";
	//echo $sql."<br>";
    $haschild = $GLOBALS['db']->getOne($sql);
	if( $haschild == 0)
	{
		$haschild = "";
	}
	return $haschild;
	
}
function get_step_goods_list($search)
{
	list($cat_id, $brand_id, $intro_type) = explode(",", $search);
	$where = "WHERE 1 ";
	if($cat_id)
	{
		$where .=' AND ' . get_children($cat_id) ;
		//$where .= "AND g.cat_id = '$cat_id' "; 
	}
	if($brand_id)
	{
		$where .= "AND g.brand_id = '$brand_id' "; 
	}
	if($intro_type)
	{
		$where .= "AND g.$intro_type = 1 "; 
	}
	if($cat_id == 0 && $brand_id == 0 &&  $intro_type == 0)
	{
		return 0;
	}
    /* 取得数据 */
    $sql = 'SELECT g.goods_id, g.goods_name '.
           'FROM ' . $GLOBALS['ecs']->table('goods') . 'AS g ' . $where;
	
    $res = $GLOBALS['db']->getAll($sql);
	
	$goods_list = array();
	
    foreach ($res AS $row)
    {
        $goods_list[$row['goods_id']] = $row['goods_name'];
    }
	//print_r($goods_list);
    return $goods_list;
    
}

/**
 *  产生随机的 订单数
 *
 * @access  public
 * @param  
 */
function get_solition_order_id()
{
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);

    //return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	$sql = "SELECT MAX(order_id) FROM " . $GLOBALS['ecs']->table('solution_order') .
             " WHERE 1";
    $sn = $GLOBALS['db']->getOne($sql);

    return $sn;
	
}

//获得 配单订单的详细信息
function get_solution_order_detail($order_id)
{
	$sql = 	"SELECT *  ".
	 		'FROM ' . $GLOBALS['ecs']->table('solution_order_detail') . 
			" WHERE order_id = '$order_id' ";
	//echo $sql."<br>";
    $row = $GLOBALS['db']->getAll($sql);
	//print_r($row);
	return $row;
	
}
//获得 单个配单订单的详细信息
function get_solution_order_info($order_id)
{
	$sql = 	"SELECT *  ".
	 		'FROM ' . $GLOBALS['ecs']->table('solution_order') . 
			" WHERE order_id = '$order_id' ";
	//echo $sql."<br>";
    $row = $GLOBALS['db']->getRow($sql);
	//print_r($row);
	return $row;
	
}


/**
 * 获得需要写入数据的  方案信息
 *
 * @access  public
 * @param   int     $cat_id     分类的ID
 * @param   int     $selected   当前选中分类的ID
 * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
 * @param   int     $level      限定返回的级数。为0时返回所有级数
 * @param   int     $is_show_all 如果为true显示所有分类，如果为false隐藏不可见分类。
 * @return  mix
 */

function post_solution_list($solution_id = 0)
{
    static $res = NULL;

	if($solution_id == 0){
		$where = "WHERE 1";		
	}else{
		$where = "WHERE c.clusters_id =". $solution_id . " AND c.step_type =1 ";
	}

    if ($res === NULL)
    {
        $sql = "SELECT c.*, COUNT(s.solution_id) AS has_children  ".
                'FROM ' . $GLOBALS['ecs']->table('solution') . " AS c ".
                "LEFT JOIN " . $GLOBALS['ecs']->table('solution') . " AS s ON s.parent_id=c.solution_id ".
				$where.
                " GROUP BY c.solution_id ".
                ' ORDER BY parent_id, sort_step ASC';
		//
		
		//echo $sql."<br />";
        $res = $GLOBALS['db']->getAllCached($sql);

        foreach($res as $k=>$v)
        {
            $res[$k]['goods_num'] =  $res[$k]['has_children'];//$newres[$v['solution_id']];
        }
    }
	return $res;
	//print_r($res);
	/*
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

    //截取到指定的缩减级别 
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

        // 保留level小于end_level的部分
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
		//print_r($options);
        return $options;
    }
	*/
	
}

/**
 * 获得 标签列表 教育(34)
 *
 * @access  public
 * @param   int     $cat
 * @return  array
 */
function get_case_tag_list($app="case",$cat_id = 0)
{	
	$cat_sql = $cat_id > 0 ? " AND cat_id = $cat_id " : "" ;
    $sql = "SELECT keywords AS tag_name".
            " FROM " . $GLOBALS['ecs']->table('article') .
            " WHERE is_open = 1 " . $cat_sql;
	//print($sql);
	
    $row = $GLOBALS['db']->getAll($sql);
	
	$all_tag_array = array();
    foreach ($row AS $key => $val)
    {
		$exp_array = explode(" ",$val['tag_name']); //空格分割

		foreach($exp_array AS $k => $v)
		{
			array_push($all_tag_array,$v);
		}		
    }
	$all_tag_array = array_unique($all_tag_array);
	// Array ( [0] => 10万 [1] => 20万 [19] => 音频 [20] => 测试 [52] => 增加配单 ) 

	//最终输出 指定数量 和 url
	$result = array();
	foreach($all_tag_array AS $key => $val){
		$result[$key]['tag_name'] = $val;
		
		$sql = 'SELECT count(*) AS tag_num FROM ' . $GLOBALS['ecs']->table('article')  .
				" WHERE keywords LIKE '%" . $val. "%' AND is_open = 1 ".$cat_sql;
		$result[$key]['tag_num'] = $GLOBALS['db']->getOne($sql);
		$result[$key]['url'] = 'case.php?tag_name='.$val;
	}
    return $result;
}

/**
 * 获得 标签列表 教育(34)
 *
 * @access  public
 * @param   int     $cat
 * @return  array
 */
function get_topic_tag_list($app="topic")
{	
    $sql = "SELECT topic_tag AS tag_name".
            " FROM " . $GLOBALS['ecs']->table('topic') .
            " WHERE 1 " ;
	//print($sql);
	
    $row = $GLOBALS['db']->getAll($sql);
	
	$all_tag_array = array();
    foreach ($row AS $key => $val)
    {
		$exp_array = explode(" ",$val['tag_name']); //空格分割

		foreach($exp_array AS $k => $v)
		{
			array_push($all_tag_array,$v);
		}		
    }
	$all_tag_array = array_unique($all_tag_array);
	// Array ( [0] => 10万 [1] => 20万 [19] => 音频 [20] => 测试 [52] => 增加配单 ) 

	//最终输出 指定数量 和 url
	$result = array();
	foreach($all_tag_array AS $key => $val){
		if(!empty($val)){
			$result[$key]['tag_name'] = $val;
		
			$sql = 'SELECT count(*) AS tag_num FROM ' . $GLOBALS['ecs']->table('topic')  .
				" WHERE topic_tag LIKE '%" . $val. "%' ";
		
			$result[$key]['tag_num'] = $GLOBALS['db']->getOne($sql);
			$result[$key]['url'] = 'topic.php?tag_name='.$val;
		}
	}
    return $result;
}
?>