<?php

/**
 * SINEMALL 公用函数库 * $Author: testyang $
 * $Id: lib_common.php 14699 2008-07-04 07:36:04Z testyang $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 获得用户的订单列表
 * @param   integer $cat_id
 *
 * @return  array order_id ,order_name
 */
function get_order_list($tag_name,$is_exe = 0)
{
    $filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
    $filter['tag_name'] = empty($_REQUEST['tag_name']) ? '' : trim($_REQUEST['tag_name']);
    $filter['order_name'] = empty($_REQUEST['order_name']) ? '' : trim($_REQUEST['order_name']);
    $filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);
    $filter['part_number'] = empty($_REQUEST['part_number']) ? '' : trim($_REQUEST['part_number']);
    $filter['contact_name'] = empty($_REQUEST['contact_name']) ? '' : trim($_REQUEST['contact_name']);
    $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);
	
	$filter['start_price'] = empty($_REQUEST['start_price']) ? 0 : $_REQUEST['start_price'];
    $filter['end_price'] = empty($_REQUEST['end_price']) ? 1000000 : $_REQUEST['end_price'];
    
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'order_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = 'WHERE 1 AND o.is_show = 1 AND o.is_public = 1';
	
	if($is_exe){
        $where .= " AND o.is_exe = 1 ";
	}
	
	if($tag_name){
        $where .= " AND o.order_tag LIKE '%" . mysql_like_quote($tag_name) . "%' ";
	}
	if($filter['tag_name']){
        $where .= " AND o.order_tag LIKE '%" . mysql_like_quote($filter['tag_name']) . "%' ";
	}
	
	if($filter['contact_name']){
        $where .= " AND c.user_name LIKE '%" . mysql_like_quote($filter['contact_name']) . "%' ";
	}
	
	if($filter['order_name']){
        $where .= " AND o.order_name LIKE '%" . mysql_like_quote($filter['order_name']) . "%' ";
	}
	if($filter['user_name']){
        $where .= " AND u.user_name LIKE '%" . mysql_like_quote($filter['user_name']) . "%' ";
	}
	if($filter['part_number']){
        $where .= " AND s.part_number LIKE '%" . mysql_like_quote($filter['part_number']) . "%' ";
	}
	if ($filter['start_time'] AND $filter['end_time'] )
    {
        $where .= " AND o.add_time >= $filter[start_time] ";
		$where .= " AND o.add_time <= $filter[end_time] ";
    }
	
	
	if ($filter['start_price'] || $filter['end_price'] )
    {
       	$where .= " AND o.order_amount >= $filter[start_price] * 10000 ";
		$where .= " AND o.order_amount <= $filter[end_price] * 10000 ";
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
    if ($filter['contact_name'])
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('step_order') . " AS o ".
               " LEFT JOIN " .$GLOBALS['ecs']->table('agency_contact'). " AS c ON c.contact_id=o.contact_id ".
        	 	$where;
    }
	elseif($filter['user_name'])
	{
		$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('step_order') . " AS o ".
            	" LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ".
        	 	$where;
	}
	elseif($filter['part_number'])
	{
		$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('step_order') . " AS o ".
    			" LEFT JOIN " .$GLOBALS['ecs']->table('step_db'). " AS s ON s.order_id = o.order_id ".
        	 	$where;
	}
    else
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('step_order') ." AS o " . $where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$sql = 'SELECT '.
				'o.* , s.part_number ,' .
				'u.user_name, ' .
				'c.contact_name,'.
				'au.agency_name' .
				
            " FROM " . $GLOBALS['ecs']->table('step_order') . " AS o ".
            //" FROM " . $GLOBALS['ecs']->table('inventory') ." AS o " .
    		" LEFT JOIN " .$GLOBALS['ecs']->table('step_db'). " AS s ON s.order_id = o.order_id ".
        	" LEFT JOIN " .$GLOBALS['ecs']->table('agency_contact'). " AS c ON c.contact_id=o.contact_id ".
            " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('agency'). " AS au ON au.agency_id=o.agency_id ".
            "$where GROUP BY o.order_id ".
			"ORDER BY " . $filter['sort_by']  ." ". $filter['sort_order'] .
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
			//echo $sql;
	
			$row = $GLOBALS['db']->getAll($sql);
		 	/* 格式话数据 */
		    foreach ($row AS $key => $val)
		    {
		        $row[$key]['add_time'] 		= local_date('Y-m-d H:i:s', $row[$key]['add_time']);
				//$row[$key]['step_info'] 	= get_goods_info($value['goods_id']);
				$row[$key]['order_level'] 	= order_level($val['order_amount']); 
				$row[$key]['tag_list'] 		= explode(" ",$val['order_tag']);
				$row[$key]['url'] 			= 'solution_operate.php?act=order_detail&order_id='.$val['order_id'];
				
		    }
			//print_r($row);
			$arr = array('orders' => $row, 'sql' => $sql, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
		    return $arr;
}

/**
 * 获得全部的订单列表
 * @param   integer $cat_id
 *
 * @return  array order_id ,order_name
 */
function get_my_order_list($user_id,$is_public = 0,$is_exe = 0)
{
	$exe_sql = $is_exe > 0 ? " AND is_exe = 1" : "  AND is_exe = 0 " ;
	
	$sql =	"SELECT * FROM ". $GLOBALS['ecs']->table('step_order') . 
			" WHERE user_id = '$user_id' AND is_show = 1 AND is_public = $is_public". $exe_sql .
			" ORDER BY add_time DESC ";
	$arr = $GLOBALS['db']->getAll($sql);
	foreach($arr AS $key => $val){
		$arr[$key]['order_level'] = order_level($val['order_amount']); 
	}
	return $arr;
}

/**
 * 从location地址信息中找到想要的值
 * @param   integer $cat_id
 *
 * @return  array order_id ,order_name
 */
function get_value_form_location($location_array,$value ='')
{	
	$value_array = array();
	if(is_array($location_array)){
		foreach ($location_array AS $key => $val){
			if($key == 0)
			{
				if (strlen($val)){
					$val = substr($val,1,strlen($val)-1);
					$val_array = explode("=",$val);
					$value_array[$val_array[0]] = $val_array[1];
				}
				
			}else{
				$val_array = explode("=",$val);
				$value_array[$val_array[0]] = $val_array[1];
			}
		}
		
	}
	
	if($value)
	{
		if($value_array[$value]){
			return $value_array[$value];
		}else{
			return '';
		}
	}else{
		return $value_array;
	}
	 
}

/**
 * 制作正确的location地址
 * @param   integer $cat_id
 *
 * @return  array order_id ,order_name
 */
function make_added_location($array,$order_id)
{
	if($array['order_id'])
	{
		$added = '';
	}elseif($array['category'] || $array['brand']){
		$added = "&order_id=".$order_id;
	}
	else{
		$added = "?order_id=".$order_id;
	}
	return $added;
}

/**
 * 制作正确的location地址
 * @param   integer $cat_id
 *
 * @return  array order_id ,order_name
 */
function make_location_for_add_step($array,$order_id,$act="add_order")
{
	$added = '';
	if($array['act'])
	{
		if($act == "remove_step"){
			$added .= "?act=order_detail";
		}elseif($act == "add_order"){
			$added .= "?act=show";
		}elseif($act == "order_detail"){
			$added .= "?act=order_detail";
		}else{
			$added .= '';
		}
	}else{
		$added .= '?act=show';
	}
	
	
	if($array['category'])
	{
		$added .= "?category=".$array['category'];
	}
	if($array['brand'])
	{
		if($array['category']){
			$added .= "&brand=".$array['brand'];
		}else{
			$added .= "?brand=".$array['brand'];
		}
	}
	if($order_id)
	{	
		if(count($array)){
			$added .= "&order_id=".$order_id;
		}else{
			$added .= "&order_id=".$order_id;
		}
	}

	return $added;
}


/**
 * 为解决方案获得品牌列表
 *
 * @access  public
 * @param   int     $cat
 * @return  array
 */
function get_brands_for_solution($cat = 0, $order_id = 0, $app = 'solution_operate', $iscommend = 1, $limitnum = 0)
{
    $children = ($cat > 0) ? ' AND ' . get_children($cat) : '';
	$iscommandsql = ($iscommend > 0) ? 'AND is_commend = '. $iscommend :''; 
	$limitnum_sql = ($limitnum > 0)  ? ' LIMIT 0,'.$limitnum :'';
	//display commend brand case 1: display commend , case 0: display all
	
    $sql = "SELECT b.brand_id, b.brand_name, b.brand_logo, COUNT(g.goods_id) AS goods_num, IF(b.brand_logo > '', '1', '0') AS tag ".
            "FROM " . $GLOBALS['ecs']->table('brand') . "AS b, ".
                $GLOBALS['ecs']->table('goods') . " AS g ".
            "WHERE g.brand_id = b.brand_id $children AND is_show = 1 $iscommandsql " .
            " AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
            "GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY tag DESC, b.brand_name ASC ".
			$limitnum_sql ;
	//print($sql);
	
    $row = $GLOBALS['db']->getAll($sql);
	
	$all_brand = $row[0];
	$all_brand['brand_id'] = '0';
	$all_brand['brand_name'] = '全部品牌';
	$all_brand['goods_num'] = '0';
//	print_r($all_brand);
	$row[count($row)] = $all_brand;
	
    foreach ($row AS $key => $val)
    {
        $row[$key]['url'] = build_uri($app, array('cid' => $cat, 'bid' => $val['brand_id'],'oid' => $order_id));
    }

    return $row;
}

/**
 * 获得 标签列表 教育(34)
 *
 * @access  public
 * @param   int     $cat
 * @return  array
 */
function get_s_order_tag_list($app="solution_operate",$is_exe = 0)
{	
	$exe_sql = $is_exe > 0 ? " AND is_exe = 1" : "" ;
    $sql = "SELECT order_tag AS tag_name".
            " FROM " . $GLOBALS['ecs']->table('step_order') .
            " WHERE is_show = 1 AND is_public = 1 ". $exe_sql;
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
		
		$sql = 'SELECT count(*) AS tag_num FROM ' . $GLOBALS['ecs']->table('step_order')  .
				" WHERE order_tag LIKE '%" . $val. "%' AND is_show = 1 AND is_public = 1 " . $exe_sql;
		$result[$key]['tag_num'] = $GLOBALS['db']->getOne($sql);
		
		$result[$key]['url'] = 'solution_operate.php?act=search_order&tag_name='.$val.'&is_exe='.$is_exe;
	}
    return $result;
}


/**
 * 获得某个order_id下的所有step 非AJax 的获得函数
 *
 * @access  public
 * @param   int     $cat
 * @return  array
 */
function get_order_step_detail($order_id)
{
	$sql = 'SELECT * ' .
                'FROM ' . $GLOBALS['ecs']->table('step_db')  .
                "WHERE order_id = '$order_id' AND is_show = 1 ".
 				"ORDER BY add_time DESC";

    $detail = $GLOBALS['db']->getAll($sql);
	foreach($detail AS $key => $val)
	{
		$detail[$key]['goods'] = get_goods_less_info($val['goods_id']);
	}
	return $detail;
}



/**
 * 获得某个order的详细信息
 * 获得并计算
 * @access  public
 * @param   int     $cat
 * @return  array amount count
 */
function get_order_info($order_id,$need_update = 1)
{		
	$exc   = new exchange($GLOBALS['ecs']->table("step_db"), $GLOBALS['db'], 'step_id', 'goods_id');
	$exc_order   = new exchange($GLOBALS['ecs']->table("step_order"), $GLOBALS['db'], 'order_id', 'order_name');	
	$exc_contract= new exchange($GLOBALS['ecs']->table("order_contract"), $GLOBALS['db'], 'contract_id', 'contract_name');
	
	$sql = 'SELECT '.
				'o.* , o.order_amount AS order_amount_after_tax ,'.
				' c.contact_name ,' . // C is 联系人信息
				'u.real_name AS u_name, u.email AS u_email, u.mobile_phone AS u_mobile, u.office_phone AS u_office ,'.
				'a.agency_name ,a.agency_id'.
			//	'st.status_name, st.status_desc' .
				
            " FROM " . $GLOBALS['ecs']->table('step_order') . " AS o ".
            " LEFT JOIN " .$GLOBALS['ecs']->table('agency') ." AS a ON a.agency_id=o.agency_id " .
            " LEFT JOIN " .$GLOBALS['ecs']->table('agency_contact'). " AS c ON c.contact_id = o.contact_id ".
            " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ".
			//" LEFT JOIN " .$GLOBALS['ecs']->table('inventory_status'). " AS st ON st.status_id=o.status_id ".
			" WHERE o.order_id = '$order_id'";
			//echo $sql;

    $order_info = $GLOBALS['db']->getRow($sql);
	
	if($order_info['order_id'])
	{
		$order_info['order_amount'] 	= round($exc->sum('order_id', $order_id,'','','`step_count` *`step_price`'));
		$order_info['order_goods_types']= $exc->num('order_id', $order_id);
		$order_info['order_count'] 		= $exc->sum('order_id', $order_id,'','','step_count');
		$order_info['order_amount_rmb'] = toCNcap($order_info['order_amount']); 
		$order_info['order_level'] 		= order_level($order_info['order_amount']);
		$order_info['add_time'] 		= local_date('Y-m-d H:i:s', $order_info['add_time']);
		
		$order_info['other_fee'] 		= $order_info['wire_fee'] +  $order_info['travel_fee'] + $order_info['training_fee'];
		
		$order_info['all_fee'] 			= $order_info['other_fee'] + $order_info['order_amount'];
		
		$order_info['order_amount_after_tax'] 	= $order_info['tax_fee'] > 0 ?  round($order_info['all_fee'] * (1 + $order_info['tax_fee']/100)) : $order_info['all_fee'];
		
		
	}
	
	//	need_update
	if($need_update){		
		$exc_order->edit("order_count = '$order_info[order_count]'", $order_id);
		$exc_order->edit("order_amount = '$order_info[order_amount]'", $order_id);
		$exc_contract->edit("order_amount = '$order_info[order_amount]'", $order_id);
	}
	//print_r($order_info);
	return $order_info;
    
}

/**
 * 获得某个order的 状态信息
 * 获得并计算
 * @access  public
 * @param   int     $cat
 * @return  array amount count
 */
function order_status_list($order_id,$type)
{
	$step_count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('step_db') . " WHERE order_id = $order_id " ;
    $step_count = $GLOBALS['db']->getOne($step_count_sql);

	$status_sum_sql = "SELECT SUM(`". $type ."`)  FROM " . $GLOBALS['ecs']->table('step_db') . " WHERE order_id = $order_id " ;
    $status_sum = $GLOBALS['db']->getOne($status_sum_sql);
	
	$res =  array('step_count'=>$step_count,'status_sum'=>$status_sum);
	return $res;
}
/*
* 获得库存列表
* ====================================================
*/
function get_step_list($order_id,$_page_size = 0){
	$filter['part_number'] = empty($_REQUEST['part_number']) ? '' : trim($_REQUEST['part_number']);
    $filter['serial_number'] = empty($_REQUEST['serial_number']) ? '' : trim($_REQUEST['serial_number']);
    $filter['order_id'] = empty($_REQUEST['order_id']) ? 0 : intval($_REQUEST['order_id']);

    $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);
	
	$filter['start_price'] = empty($_REQUEST['start_price']) ? 0 : $_REQUEST['start_price'];
    $filter['end_price'] = empty($_REQUEST['end_price']) ? 1000000 : $_REQUEST['end_price'];
    
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'step_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = 'WHERE 1 ';
	if($order_id){
		$where .= " AND o.order_id = '$order_id' AND o.is_show = 1";
	}
	if($filter['order_id']){
		$where .= " AND o.order_id = '$filter[order_id]' AND o.is_show = 1";
	}
    if ($filter['part_number'])
    {
        $where .= " AND o.part_number LIKE '%" . mysql_like_quote($filter['part_number']) . "%'";
    }
    if ($filter['serial_number'])
    {
        $where .= " AND o.serial_number LIKE '%" . mysql_like_quote($filter['serial_number']) . "%'";
    }
    if ($filter['status_id'])
    {
        $where .= " AND o.status_id = '$filter[status_id]'";
    }
	if ($filter['start_price'] || $filter['end_price'] )
    {
        $where .= " AND o.step_price >= '$filter[start_price]'";
		$where .= " AND o.step_price <= '$filter[end_price]'";
    }
	
	if($filter['status_id'] == 1)
	{
		if ($filter['start_time'] AND $filter['end_time'] )
	    {
	        $where .= " AND o.inv_start_time >= '$filter[start_time]'";
			$where .= " AND o.inv_start_time <= '$filter[end_time]'";
	    }
	}
	if($filter['status_id'] != 1)
	{
		if ($filter['start_time'] AND $filter['end_time'] )
	    {
	        $where .= " AND o.inv_end_time >= '$filter[start_time]'";
			$where .= " AND o.inv_end_time <= '$filter[end_time]'";
	    }
	}
	

	/* 分页大小 */
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

	if($_page_size > 0 )
	{
		$filter['page_size'] = $_page_size;
	}
    elseif (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
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
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('step_db') ." AS o " . $where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$sql = 'SELECT '.
				'o.* ,'.
				'g.goods_id , g.goods_name  '.
//				', s.supplier_name , sc.contact_name '.
			//	'st.status_name, st.status_desc' .
				
            " FROM " . $GLOBALS['ecs']->table('step_db') . " AS o ".
            " LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS g ON g.goods_id = o.goods_id " .
//			" LEFT JOIN " .$GLOBALS['ecs']->table('supplier') ." AS s ON s.supplier_id = o.supplier_id " .
//			" LEFT JOIN " .$GLOBALS['ecs']->table('supplier_contact') ." AS sc ON sc.contact_id = o.supplier_contact_id " .
            //" FROM " . $GLOBALS['ecs']->table('inventory') ." AS o " .
            //" LEFT JOIN " .$GLOBALS['ecs']->table($GLOBALS['year']."_".'category'). " AS c ON c.cat_id=o.cat_id ".
			//" LEFT JOIN " .$GLOBALS['ecs']->table('inventory_status'). " AS st ON st.status_id=o.status_id ".
            "$where ORDER BY  o.cat_id ASC, o.step_price DESC ".
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
			//echo $sql;
	
			$row = $GLOBALS['db']->getAll($sql);
		 	/* 格式话数据 */
		    foreach ($row AS $key => $value)
		    {
		        $row[$key]['add_time'] 		= local_date('Y-m-d H:i:s', $row[$key]['add_time']);
		        //$row[$key]['serial_number'] = empty($row[$key]['serial_number']) ? '未指定' : $row[$key]['serial_number'];

				$tmp_array = array_fill(0,$value['step_count'],'未指定');
				
				$tmp_from = explode(",",$value['serial_number']);
				foreach($tmp_from AS $k => $v){
					if($v){
						$tmp_array[$k] = $v;
					}
					//echo $k ."->".$v;
				}
				$row[$key]['serial_number_array'] = $tmp_array;
				
		        $row[$key]['step_count_array'] = array_fill(0,$value['step_count'],"ser");
				$row[$key]['version_time'] 	= local_date('Y-m-d H:i:s', $row[$key]['version_time']);
				$row[$key]['goods'] 		= get_goods_info($value['goods_id']);
				$row[$key]['sub_price'] 	= $value['step_count'] * $value['step_price'];
				
		    }
			//print_r($row);
			$arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
		    return $arr;
}


/**
 * 获得某个order的详细信息
 * 获得并计算
 * @access  public
 * @param   int     $cat
 * @return  array amount count
 */
function get_step_info($step_id){
	$sql = 	"SELECT * FROM ". $GLOBALS['ecs']->table('step_db') .
			"WHERE step_id = $step_id ";
			
	$row = $GLOBALS['db']->getRow($sql);
	$row['purchase_time'] = local_date('Y-m-d H:i:s', $row['purchase_time']);
	
	return $row;
}
/**
 * 获得某个contract的详细信息
 * 获得并计算
 * @access  public
 * @param   int     $cat
 * @return  array amount count
 */
function get_contract_info($contract_id)
{
	$sql = 	"SELECT * FROM ". $GLOBALS['ecs']->table('order_contract') .
			"WHERE contract_id = $contract_id ";
	$arr = $GLOBALS['db']->getRow($sql);
	$arr['user'] = get_user_info_detail($arr['user_id']);
	$arr['custom'] = get_user_info_detail($arr['contact_id']);
	$arr['order_amount_rmb'] = toCNcap($arr['order_amount']);
	return $arr;
}
/**
 * 获得某个contract的详细信息
 * 获得并计算
 * @access  public
 * @param   int     $cat
 * @return  array amount count
 */
function get_contact_list($agency_id = 0)
{    
	if($agency_id){
	//	$agency_sql = $agency_id ? " agency_id = $agency_id " : '' ;
		$sql = 	"SELECT contact_id,contact_name FROM ". $GLOBALS['ecs']->table('agency_contact') .
				" WHERE agency_id = $agency_id ";
		$temp_list = $GLOBALS['db']->getAll($sql);
		foreach ($temp_list AS $key => $value)
	    {
			$key = $value['contact_id'];
	        $list[$key] = $value['contact_name'];
	    }
		
	}else{
		$list = array();
	}

	return $list;
}

/**
 * 获得某个contract的详细信息
 * 获得并计算
 * @access  public
 * @param   int     $cat
 * @return  array amount count
 */
function get_agency_list($user_id = 0)
{
	$list = array();
	$user_sql = $user_id > 0 ? " WHERE user_id = $user_id " : "" ;

	$sql = 	"SELECT agency_id,agency_name FROM ". $GLOBALS['ecs']->table('agency') . $user_sql;
	
	$temp_list = $GLOBALS['db']->getAll($sql);
	foreach ($temp_list AS $key => $value)
    {
		$key = $value['agency_id'];
        $list[$key] = $value['agency_name'];
    }
//	print_r($list);
	return $list;
}
/*
* 获得客户联系人列表
* ====================================================
*/
function get_agency_contactList($user_id){
    $filter['contact_name'] = empty($_REQUEST['contact_name']) ? '' : trim($_REQUEST['contact_name']);
    $filter['contact_mobile_phone'] = empty($_REQUEST['contact_mobile_phone']) ? '' : trim($_REQUEST['contact_mobile_phone']);
    $filter['contact_office_phone'] = empty($_REQUEST['contact_office_phone']) ? '' : trim($_REQUEST['contact_office_phone']);
    $filter['agency_name'] = empty($_REQUEST['agency_name']) ? '' : trim($_REQUEST['agency_name']);
    $filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);

    
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'contact_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = 'WHERE 1 ';
	if($user_id){
		$where .= " AND c.user_id = '$user_id' ";
	}
	if($filter['user_id']){
		$where .= " AND c.user_id = '$filter[user_id]' ";
	}
    if ($filter['contact_name'])
    {
        $where .= " AND c.contact_name LIKE '%" . mysql_like_quote($filter['contact_name']) . "%'";
    }
    if ($filter['contact_mobile_phone'])
    {
        $where .= " AND c.contact_mobile_phone LIKE '%" . mysql_like_quote($filter['contact_mobile_phone']) . "%'";
    }
    if ($filter['contact_office_phone'])
    {
        $where .= " AND c.contact_office_phone LIKE '%" . mysql_like_quote($filter['contact_office_phone']) . "%'";
    }
    if ($filter['agency_name'])
    {
        $where .= " AND o.agency_name LIKE '%" . mysql_like_quote($filter['agency_name']) . "%'";
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
    if ($filter['agency_name'])
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('agency_contact') . " AS c ,".
               $GLOBALS['ecs']->table('agency') . " AS o " . $where;
    }
    else
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('agency_contact') ." AS c " . $where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$sql = 'SELECT '.
				'c.* ,' .
				'o.* ' .
				
            " FROM " . $GLOBALS['ecs']->table('agency_contact') . " AS c " .
			" LEFT JOIN " .$GLOBALS['ecs']->table('agency') ." AS o ON o.agency_id = c.agency_id " .
            //" FROM " . $GLOBALS['ecs']->table('inventory') ." AS o " .
            //" LEFT JOIN " .$GLOBALS['ecs']->table($GLOBALS['year']."_".'category'). " AS c ON c.cat_id=o.cat_id ".
			//" LEFT JOIN " .$GLOBALS['ecs']->table('inventory_status'). " AS st ON st.status_id=o.status_id ".
            "$where ORDER BY c.contact_id DESC  ".
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
			//echo $sql;
	
			$row = $GLOBALS['db']->getAll($sql);

			//print_r($row);
			$arr = array('agency_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
		    return $arr;
}

/**
 *  由 员工编号得到 合同号
 *  200903-0014-003  年月-员工编号-3位编号
*/
function get_contract_sn($user_id)
{
	$yearmouth = date('Ym',gmtime());
	$contract_sn = $yearmouth."-".substr(strval($user_id+10000),1,4);
	$sql = 	"SELECT MAX(`contract_sn`) FROM ". $GLOBALS['ecs']->table('order_contract') .
			"WHERE contract_sn LIKE '%".$yearmouth."%'";
	$biggest = $GLOBALS['db']->getOne($sql);
	$new_sn = substr($biggest,strlen($biggest)-3,3);
	$new_sn = substr(strval(intval($new_sn)+1001),1,3);
	
	$contract_sn = $contract_sn."-".$new_sn;
	return strval($contract_sn);
}

/*
* 阿拉伯数字转 人民币大写
* ====================================================
*/
function toCNcap($data){
   $capnum=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
   $capdigit=array("","拾","佰","仟");
   $subdata=explode(".",$data);
   $yuan=$subdata[0];
   $j=0; $nonzero=0;
   for($i=0;$i<strlen($subdata[0]);$i++){
      if(0==$i){ //确定个位 
         if($subdata[1]){ 
            $cncap=(substr($subdata[0],-1,1)!=0)?"元":"元";
         }else{
            $cncap="元";
         }
      }   
      if(4==$i){ $j=0;  $nonzero=0; $cncap="万".$cncap; } //确定万位
      if(8==$i){ $j=0;  $nonzero=0; $cncap="亿".$cncap; } //确定亿位
      $numb=substr($yuan,-1,1); //截取尾数
      $cncap=($numb)?$capnum[$numb].$capdigit[$j].$cncap:(($nonzero)?"零".$cncap:$cncap);
      $nonzero=($numb)?1:$nonzero;
      $yuan=substr($yuan,0,strlen($yuan)-1); //截去尾数	  
      $j++;
   }

   if($subdata[1]){
     //$chiao=(substr($subdata[1],0,1))?$capnum[substr($subdata[1],0,1)]."角":"零";
     //$cent=(substr($subdata[1],1,1))?$capnum[substr($subdata[1],1,1)]."分":"零分";
   }
   $cncap .= $chiao.$cent."整";
   $cncap=preg_replace("/(零)+/","\\1",$cncap); //合并连续“零”
   return $cncap;
}


/*
* XX 万元级
* ====================================================
*/
function order_level($data){
	
   	$subdata=explode(".",$data);
   	$yuan=$subdata[0];
	if($yuan<10000){
		$num = '1';
	}else{
		$wan_yuan = substr($yuan,0,strlen($yuan)-4);
	   	if($wan_yuan > 20 )
		{
			$num = floor($wan_yuan/10)*10;
		}else{
			$num = $wan_yuan;
		}
	}


   return $num;
}

/**
 *  发送配单过程邮件
 *
 * @access  public
 * @param   string  $contact_name    顾客的名字
 * @param   string  $customer_email	顾客的邮箱
 * @param   string  $subject        邮件主题
 * @param   string  $order_list     html配单信息
 * @param   string  $mail_from  	发出的邮箱
 *
 * @return  boolen  $result;
 */
function send_solution_order_email($contact_name,$customer_email,$subject,$order_list,$mail_from)
{

    /* 设置重置邮件模板所需要的内容信息 */
    $template    = get_mail_template('solution_order');

    $GLOBALS['smarty']->assign('user_name',   $contact_name);
    $GLOBALS['smarty']->assign('shop_name',   $GLOBALS['_CFG']['shop_name']);
    $GLOBALS['smarty']->assign('send_date',   date('Y-m-d'));
    $GLOBALS['smarty']->assign('order_list',  $order_list);

    $content = $GLOBALS['smarty']->fetch('str:' . $template['template_content']);

    /* 发送确认重置密码的确认邮件 */
    if (send_mail($contact_name, $customer_email, $subject, $content, $template['is_html'],'false',$mail_from))
    {
        return true;
    }
    else
    {
        return false;
    }
}
/**
* 获得顶级的分类ID  录音分类单列出来 顺序按照默认的
* 返回 cat_id
*/
function get_father_cat_id($old_id)
{
	//循环部分 获得父 ID
	$sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table($GLOBALS['year']."_".'category') . ' WHERE cat_id = '.$old_id;
	$tmp_id = $GLOBALS['db']->getOne($sql);
	
	if($tmp_id ==1){
		return $old_id;
	}
	// 判断部分
	$top_cat_id_array = get_top_cat_id_array(1); //拆开 1 录音类
	//echo "$old_id=>$tmp_id | ";
	if (in_array($tmp_id,$top_cat_id_array)){
		//echo $tmp_id;
		return $tmp_id;
	}else{
		return get_father_cat_id($tmp_id);
	}
}

/** 
*  获德顶级分类 
*  priority_cat_id 优先哪一类拆开 放在数组 的最前面
*/
function get_top_cat_id_array($priority_cat_id = 0)
{
	$sql = 'SELECT cat_id FROM ' . $GLOBALS['ecs']->table($GLOBALS['year']."_".'category') . ' WHERE parent_id = 0' .' ORDER BY cat_id ASC';
    $res = $GLOBALS['db']->getAll($sql);

	if($priority_cat_id){
		$sql2 = 'SELECT cat_id FROM ' . $GLOBALS['ecs']->table($GLOBALS['year']."_".'category') . ' WHERE parent_id = ' . $priority_cat_id . ' ORDER BY cat_id ASC';
	    $res2 = $GLOBALS['db']->getAll($sql2);
		$result = array_merge($res2,$res);	
	}
	
	$result2 = array();
	
	if($priority_cat_id){
		foreach($result AS $key=>$val){
			if($val['cat_id'] != $priority_cat_id){ //排除大类
				array_push($result2,$val['cat_id']);
			}
		}
	}else{
		foreach($res AS $key=>$val){
			if($val['cat_id'] != $priority_cat_id){ //排除大类
				array_push($result2,$val['cat_id']);
			}
		}
	}
	
	return $result2;

}
/**
*	获得 需要的分类数组 输入 order_detail
*	return [8] => Array ( [cat_name] => Pro Tools [cat_id] => 8 [keywords] => [cat_desc] => [parent_id] => 1 ) 
*
*/ 
function get_cat_array($order_detail)
{
	// return [8] =>
	$tmp1 = array();
	foreach($order_detail AS $key => $val){
		array_push($tmp1,$val['cat_id']);
	}
	$tmp1 = array_unique($tmp1);
	$tmp2 = array();
	foreach($tmp1 AS $key => $val){
		$tmp2[$val] = get_cat_info($val);
	}
	
	// 增加step_info 为数组
	foreach($tmp2 AS $k => $v){
			$tmp2[$k]['step_info'] = array();
	}
	
	//重新整理order_detail
	//return [8] => Array ( [cat_name] => Pro Tools [cat_id] => 8 [step_info] => order_detail_array  ) 
	foreach($order_detail AS $key => $val){
		$cat_key = $val['cat_id'];
		foreach($tmp2 AS $k => $v){
			if($cat_key == $k ){
				array_push($tmp2[$k]['step_info'],$val); //将压入数组的	step_info 里		
			}
		}
		//$tmp2[$cat_key]['more'] = $val['cat_id'];
	}
	
	return $tmp2;
}

/**
*	获得 需要的分类数组 输入 order_detail
*	return [8] => Array ( [cat_name] => Pro Tools [cat_id] => 8 [keywords] => [cat_desc] => [parent_id] => 1 ) 
*
*/ 
function add_order_to_solution_array($solution_article)
{
	foreach($solution_article AS $key => $val){
		$order_list 	= get_order_list($val['keywords']);	
		$solution_article[$key]['orders'] = $order_list['orders'];
	}
	return $solution_article;
}

/*
* ====================================================
* 从配单到采购单
* ====================================================
*/
function make_purchase($order_id){
	$order_id = empty($order_id) ? 0 : intval($order_id);
	if($order_id == 0) 
	{
		return false;
	}else{
		$step_list_tmp 	= get_step_list($order_id,$page_size = 200); //获得一个订单下的详细产品
	    $step_list = $step_list_tmp['orders'];
	
		foreach($step_list AS $key => $val){
			$goods = $val['goods'];
			$where = " WHERE goods_id = ".$val['goods_id'] ." AND purchase_status = 0 ";
			//库中未采购的的商品中是否已经存在了
			$sql_exist =  "SELECT goods_id,purchase_count,can_use_count FROM " . $GLOBALS['ecs']->table('purchase') . $where ;
			$exist_goods = $GLOBALS['db']->getRow($sql_exist);
			
			//print_r($exist_goods);
			
			if($exist_goods['goods_id']){
				$c_1 = $exist_goods['purchase_count'] + ( $val['step_count'] - $exist_goods['can_use_count'] );
				$c_2 = $exist_goods['can_use_count']  - $val['step_count'];
				
				if($exist_goods['can_use_count'] == 0)
				{
					$sql = "UPDATE " .$GLOBALS['ecs']->table('purchase') .
							" SET purchase_count = '$c_1' ". $where ;
				}else{
					$sql = "UPDATE " . $GLOBALS['ecs']->table('purchase') .
							" SET purchase_count = '$c_1' , can_use_count = '$c_2' ". $where ;
				}
						
			    $GLOBALS['db']->query($sql);
			
			}else{
				//$goods_can_use_count = get_can_use_count($val['goods_id']);
				//$goods_number  = $goods_can_use_count > 0 ? $goods_can_use_count : $val['goods']['goods_number'] ;
				if($val['step_count'] > $goods['goods_number']){
					$purchase_count = $val['step_count'] - $goods['goods_number'];

					$sql = 'INSERT INTO '. $GLOBALS['ecs']->table('purchase').
							'(purchase_id, user_id, goods_id, goods_name, part_number, purchase_count) '.
							"VALUE ('','$val[user_id]', '$goods[goods_id]', '$goods[goods_name]', '$goods[part_number]','$purchase_count')";

				    $GLOBALS['db']->query($sql);
				}else{
					$can_use_count 	=  $goods['goods_number'] - $val['step_count'];
					$sql = 'INSERT INTO '. $GLOBALS['ecs']->table('purchase').
							'(purchase_id, user_id, goods_id , goods_name, part_number, can_use_count) '.
							"VALUE ('','$val[user_id]', '$goods[goods_id]', '$goods[goods_name]', '$goods[part_number]','$can_use_count')";

				    $GLOBALS['db']->query($sql);
				}
			}
			
		}
		
		return true;
	}
	
    
}
//商品的可用库存  已经执行但是没有 出库的 标记
function get_can_use_count($goods_id){
	$sql = "SELECT purchase_count,can_use_count FROM " . $GLOBALS['ecs']->table('purchase') . " WHERE goods_id = $goods_id " ;
    $arr = $GLOBALS['db']->getRow($sql);
	if($arr['purchase_count'] == 0 && $arr['can_use_count'] == 0){
		
	}
	return $can_use_count;
}


?>