<?php

/**
 * SINEMALL 公用函数库 * $Author: testyang $
 * $Id: lib_common.php 14699 2008-07-04 07:36:04Z testyang $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
/*
* 获得采购列表
* ====================================================
*/
function get_purchase_list($purchase_status = 0){
	$filter['part_number'] = empty($_REQUEST['part_number']) ? '' : trim($_REQUEST['part_number']);
	$filter['goods_name'] = empty($_REQUEST['goods_name']) ? '' : trim($_REQUEST['goods_name']);
	$filter['brand_name'] = empty($_REQUEST['brand_name']) ? '' : trim($_REQUEST['brand_name']);
    $filter['serial_number'] = empty($_REQUEST['serial_number']) ? '' : trim($_REQUEST['serial_number']);

    $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);
	
	$filter['start_price'] = empty($_REQUEST['start_price']) ? 0 : $_REQUEST['start_price'];
    $filter['end_price'] = empty($_REQUEST['end_price']) ? 1000000 : $_REQUEST['end_price'];
    
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'step_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);

	// 确定条件 库存 < 需要的 		配单已经执行	已经显示的订单
	$where = 'WHERE p.purchase_count > 0 AND p.is_show = 1 ';
	
	if(isset($purchase_status)){
		$where .= " AND p.purchase_status = $purchase_status ";
	}
    if ($filter['part_number'])
    {
        $where .= " AND p.part_number LIKE '%" . mysql_like_quote($filter['part_number']) . "%'";
    }
    if ($filter['goods_name'])
    {
        $where .= " AND g.goods_name LIKE '%" . mysql_like_quote($filter['goods_name']) . "%'";
    }
    if ($filter['brand_name'])
    {
        $where .= " AND b.brand_name LIKE '%" . mysql_like_quote($filter['brand_name']) . "%'";
    }

	if ($filter['start_price'] || $filter['end_price'] )
    {
        $where .= " AND p.purchase_price >= '$filter[start_price]'";
		$where .= " AND p.purchase_price <= '$filter[end_price]'";
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
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_info') . " AS s ,".
				" LEFT JOIN " .$GLOBALS['ecs']->table('step_order') ." AS o ON o.order_id = s.order_id " .
               $GLOBALS['ecs']->table('users') . " AS u " . $where;
    }
    else
    {
        $count_sql = 	"SELECT p.purchase_id  FROM " . $GLOBALS['ecs']->table('purchase') ." AS p " . 
				" LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS g ON g.goods_id = p.goods_id " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('brand') ." AS b ON b.brand_id = g.brand_id " .
		        " $where  GROUP BY p.goods_id ORDER BY  p.goods_id  ";
    }

    $tmp  = $GLOBALS['db']->getAll($count_sql);
    $filter['record_count']   = count($tmp);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$sql = 'SELECT  '.
				'p.*,' .
				'g.goods_id ,g.goods_name, g.goods_number, g.promote_price, g.shop_price, g.salebase_price,g.goods_thumb,g.brand_id, b.brand_name ,'.
				'su.supplier_name , sa.contact_name '. 
			//	'st.status_name, st.status_desc' .
				
            " FROM " . $GLOBALS['ecs']->table('purchase') . " AS p ".
	        " LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS g ON g.goods_id = p.goods_id " .
			" LEFT JOIN " .$GLOBALS['ecs']->table('brand') ." AS b ON b.brand_id = g.brand_id " .
			" LEFT JOIN " .$GLOBALS['ecs']->table('supplier') ." AS su ON su.supplier_id = p.supplier_id " .
			" LEFT JOIN " .$GLOBALS['ecs']->table('supplier_contact') ." AS sa ON sa.contact_id = p.supplier_contact_id " .
            //" FROM " . $GLOBALS['ecs']->table('inventory') ." AS inv " .
            //" LEFT JOIN " .$GLOBALS['ecs']->table('category'). " AS c ON c.cat_id=o.cat_id ".
			//" LEFT JOIN " .$GLOBALS['ecs']->table('inventory_status'). " AS st ON st.status_id=o.status_id ".
          //  "$where ORDER BY  o.add_time DESC , g.goods_id DESC ".
	        "$where  ORDER BY  p.purchase_id ". $filter['sort_order'].

			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
			//echo $sql;
	
			$row = $GLOBALS['db']->getAll($sql);
		 	/* 格式话数据 */
		    foreach ($row AS $key => $value)
		    {
		        $row[$key]['add_time'] 		= local_date('Y-m-d H:i:s', $row[$key]['add_time']);
				$row[$key]['version_time'] 	= local_date('Y-m-d H:i:s', $row[$key]['version_time']);
				$row[$key]['purchase_time'] = local_date('Y-m-d', $row[$key]['purchase_time']);								
		    }
			//print_r($row);
			$arr = array('purchase_list' => $row, 'filter' => $filter,'sql' => $sql,'count_sql' => $count_sql, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
		    return $arr;
}

/*
* 获得 分订单的 采购列表
* ====================================================
*/
function get_step_array($goods_id,$user_id)
{
	$where = 'WHERE g.goods_number < s.step_count AND o.is_exe = 1 AND s.is_show = 1 ';
	if($goods_id)
	{
		$where.= "AND s.goods_id = $goods_id ";
	}
	$sql = 'SELECT '.
				's.*, ' .
				'o.order_name , g.goods_name ' .
				
            " FROM " . $GLOBALS['ecs']->table('step_db') . " AS s " .
			" LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS g ON g.goods_id = s.goods_id " .
            " LEFT JOIN " .$GLOBALS['ecs']->table('step_order'). " AS o ON o.order_id = s.order_id ".
			//" LEFT JOIN " .$GLOBALS['ecs']->table('inventory_status'). " AS st ON st.status_id=o.status_id ".
            "$where ORDER BY s.order_id DESC  ";
			//echo $sql;
	
			$row = $GLOBALS['db']->getAll($sql);

			//print_r($row);
			foreach($row AS $key => $val){
				$row[$key]['purchase_time'] = local_date('Y-m-d H:i:s', $val['purchase_time']);
				if($val['supplier_id']){
					$row[$key]['supplier_contact_list'] = getSupplierContactList($user_id,$val['supplier_id']);//架构 客户 联系人
				}
			}

		    return $row;
	
}

/*
* 获得供应商联系人列表
* ====================================================
*/
function get_supplier_contact_list($user_id){
    $filter['contact_name'] = empty($_REQUEST['contact_name']) ? '' : trim($_REQUEST['contact_name']);
    $filter['contact_mobile_phone'] = empty($_REQUEST['contact_mobile_phone']) ? '' : trim($_REQUEST['contact_mobile_phone']);
    $filter['contact_office_phone'] = empty($_REQUEST['contact_office_phone']) ? '' : trim($_REQUEST['contact_office_phone']);
    $filter['contact_desc'] = empty($_REQUEST['contact_desc']) ? '' : trim($_REQUEST['contact_desc']);
    $filter['supplier_name'] = empty($_REQUEST['supplier_name']) ? '' : trim($_REQUEST['supplier_name']);
    $filter['supplier_desc'] = empty($_REQUEST['supplier_desc']) ? '' : trim($_REQUEST['supplier_desc']);
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
    if ($filter['contact_desc'])
    {
        $where .= " AND c.contact_desc LIKE '%" . mysql_like_quote($filter['contact_desc']) . "%'";
    }
    if ($filter['contact_mobile_phone'])
    {
        $where .= " AND c.contact_mobile_phone LIKE '%" . mysql_like_quote($filter['contact_mobile_phone']) . "%'";
    }
    if ($filter['contact_office_phone'])
    {
        $where .= " AND c.contact_office_phone LIKE '%" . mysql_like_quote($filter['contact_office_phone']) . "%'";
    }
    if ($filter['supplier_name'])
    {
        $where .= " AND o.supplier_name LIKE '%" . mysql_like_quote($filter['supplier_name']) . "%'";
    }
    if ($filter['supplier_desc'])
    {
        $where .= " AND o.supplier_desc LIKE '%" . mysql_like_quote($filter['supplier_desc']) . "%'";
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
    if ($filter['supplier_name'] || $filter['supplier_desc'])
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('supplier_contact') . " AS c ,".
               $GLOBALS['ecs']->table('supplier') . " AS o " . $where;
    }
    else
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('supplier_contact') ." AS c " . $where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$sql = 'SELECT '.
				'c.* ,' .
				'o.* ' .
				
            " FROM " . $GLOBALS['ecs']->table('supplier_contact') . " AS c " .
			" LEFT JOIN " .$GLOBALS['ecs']->table('supplier') ." AS o ON o.supplier_id = c.supplier_id " .
            //" FROM " . $GLOBALS['ecs']->table('inventory') ." AS o " .
            //" LEFT JOIN " .$GLOBALS['ecs']->table('category'). " AS c ON c.cat_id=o.cat_id ".
			//" LEFT JOIN " .$GLOBALS['ecs']->table('inventory_status'). " AS st ON st.status_id=o.status_id ".
            "$where ORDER BY c.contact_id DESC  ".
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
			//echo $sql;
	
			$row = $GLOBALS['db']->getAll($sql);

			//print_r($row);
			$arr = array('supplier_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
		    return $arr;
}


/**
 * 获得某个 供应商的详细信息
 * 获得并计算
 * @access  public
 * @param   int     $cat
 * @return  array amount count
 */
function get_supplier_list($user_id = 0)
{
	$list = array();
	$user_sql = $user_id > 0 ? " WHERE user_id = $user_id " : "" ;

	$sql = 	"SELECT supplier_id,supplier_name FROM ". $GLOBALS['ecs']->table('supplier') . $user_sql;
	
	$temp_list = $GLOBALS['db']->getAll($sql);
	foreach ($temp_list AS $key => $value)
    {
		$key = $value['supplier_id'];
        $list[$key] = $value['supplier_name'];
    }
//	print_r($list);
	return $list;
}

/**
 * 获得某个 供应商的de 联系人列表
 * 获得并计算
 * @access  public
 * @param   int     $cat
 * @return  select_list
 */
function getSupplierContactList($user_id,$supplier_id,$supplier_contact_id = 0) 
{
	$list = array();
	$user_sql = $user_id > 0 ? " WHERE user_id = $user_id  " : "" ;
	$supplier_sql = $supplier_id > 0 ? " AND  supplier_id = $supplier_id  " : "" ;
	//supplier_contact_id  如果已经存在 采购联系人那么只显示改联系人
	
	$supplier_contact_sql = $supplier_contact_id > 0 ? " AND  contact_id = $supplier_contact_id  " : "" ;
	$sql = 	"SELECT contact_id,contact_name FROM ". $GLOBALS['ecs']->table('supplier_contact') . $user_sql . $supplier_sql . $supplier_contact_sql;
	
	//echo $sql;
	$temp_list = $GLOBALS['db']->getAll($sql);
	foreach ($temp_list AS $key => $value)
    {
		$key = $value['contact_id'];
        $list[$key] = $value['contact_name'];
    }
//	print_r($list);
	return $list;
	
}

?>