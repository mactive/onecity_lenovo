<?php

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
 * 取得状态列表
 * @param   string  $type   类型：all | order | shipping | payment
 */
function get_status_list($type = 'all')
{
    global $_LANG;

    $list = array();

    if ($type == 'all' || $type == 'inv')
    {
		$sql = 'SELECT status_id, status_name'." FROM " . $GLOBALS['ecs']->table('inventory_status') .
		 	   'WHERE 1 ORDER BY status_id ASC';
		$temp_list = array();
		$temp_list = $GLOBALS['db']->getAll($sql);
		foreach ($temp_list AS $key => $value)
        {
			$key = $value['status_id'];
            $list[$key] = $value['status_name'];
        }
    }
    if ($type == 'fullinv')
    {
        $sql = 'SELECT * '." FROM " . $GLOBALS['ecs']->table('inventory_status') .
		 	   'WHERE 1 ORDER BY status_id ASC';
		$list = $GLOBALS['db']->getAll($sql);
    }
	
	if ($type == 'full_id_inv')
    {
		$sql = 'SELECT status_id, status_name'." FROM " . $GLOBALS['ecs']->table('inventory_status') .
		 	   'WHERE 1 ORDER BY status_id ASC';
		$temp_list = array();
		$temp_list = $GLOBALS['db']->getAll($sql);
		foreach ($temp_list AS $key => $value)
        {
			$key = $value['status_id'];
            $list[$key] = '0';
        }
    }

	if ($type == 'noinbound')
    {
		$sql = 'SELECT status_id, status_name'." FROM " . $GLOBALS['ecs']->table('inventory_status') .
		 	   'WHERE status_id != 1 ORDER BY status_id ASC';
		$temp_list = array();
		$temp_list = $GLOBALS['db']->getAll($sql);
		foreach ($temp_list AS $key => $value)
        {
			$key = $value['status_id'];
            $list[$key] = $value['status_name'];
        }
    }
    
	
    if ($type == 'invdesc')
    {
		$sql = 'SELECT status_desc, status_name'." FROM " . $GLOBALS['ecs']->table('inventory_status') .
		 	   'WHERE 1 ORDER BY status_id ASC';
		$temp_list = array();
		$temp_list = $GLOBALS['db']->getAll($sql);
		foreach ($temp_list AS $key => $value)
        {
			$key = $value['status_desc'];
            $list[$key] = $value['status_name'];
        }
    }

    if ($type == 'all' || $type == 'payment')
    {
        $pre = $type == 'all' ? 'ps_' : '';
        foreach ($_LANG['ps'] AS $key => $value)
        {
            $list[$pre . $key] = $value;
        }
    }
	
	//print_r($list);
    return $list;
}
/*
* 获得单独订单信息
* @param   array   $get_inventory_info  订单信息
*/
function get_inventory_info($inv_id)
{
	if($inv_id > 0)
	{
		$sql = "SELECT * FROM ". $GLOBALS['ecs']->table('inventory') ."WHERE inv_id =" .$inv_id ;
		$result =$GLOBALS['db']->getRow($sql);
		//print_r($result);
		return $result;
	}else{
		return;
	}
	
}
/*
* 获得单独订单信息
* @param   array   $get_inventory_info  订单信息
*/
function get_status_info($status_id)
{
	if($status_id > 0)
	{
		$sql = "SELECT * FROM ". $GLOBALS['ecs']->table('inventory_status') ."WHERE status_id =" .$status_id ;
		$result =$GLOBALS['db']->getRow($sql);
		//print_r($result);
		return $result;
	}else{
		return;
	}
	
}

/*
* 获得单独订单信息
* @param   array   $get_inventory_info  订单信息
*/
function inventory_list_status($inv_id)
{
	if($inv_id > 0)
	{
		$sql = "SELECT * FROM ". $GLOBALS['ecs']->table('inventory') ."WHERE inv_id =" .$inv_id ;
		$result =$GLOBALS['db']->getRow($sql);
		//print_r($result);
		return $result;
	}else{
		return;
	}
	
}

/*
* 当前part_number 的库存数量
* o.status_id != 2  库存状态 不是 出库 
* @param   array   $get_inventory_info  订单信息
*/
function goods_group_by_part_number($part_number)
{
	
	$sql = "SELECT o.part_number,COUNT(o.part_number) AS goods_number, SUM(o.inv_price) AS price_amount, g.goods_id FROM ".
	 		$GLOBALS['ecs']->table('inventory') . " AS o ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS g ON g.part_number=o.part_number " .
			"WHERE o.part_number LIKE '" . $part_number . "'". "AND o.status_id = 1 ". //o.status_id != 2 => o.status_id = 1 只要不是出库的 => 只有在商品库的
			"GROUP BY o.part_number";
			
	$result =$GLOBALS['db']->getRow($sql);
	//echo $sql;
	$result = empty($result) ? 0 : $result;
	//print_r($result);
	return $result;
}

/*
* 当前inv_id 对应的part_number 的库存数量
* o.status_id != 2  库存状态 不是 出库 
* @param   array   $get_inventory_info  订单信息
*/
function goods_group_by_inv_id($inv_id)
{
	$sql2 = "SELECT part_number FROM " . $GLOBALS['ecs']->table('inventory') ." WHERE inv_id = " .$inv_id;
	$part_number = $GLOBALS['db']->getOne($sql2);
	
	$sql = "SELECT 'o.part_number',COUNT(o.part_number) AS goods_number, SUM(o.inv_price) AS price_amount, g.goods_id FROM ".
	 		$GLOBALS['ecs']->table('inventory') . " AS o ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS g ON g.part_number=o.part_number " .
			"WHERE o.part_number LIKE '%" . $part_number . "%'". "AND o.status_id = 1 ". //o.status_id != 2 => o.status_id = 1 只要不是出库的 => 只有在商品库的
			"GROUP BY o.part_number";
			
	$result =$GLOBALS['db']->getRow($sql);
	//echo $sql;
	$result = empty($result) ? 0 : $result;
	//print_r($result);
	return $result;
}

/* 修改库存和平均成本价 
*  输入 part_number
*  操作 goods 库 修改库存数量和 成本平均价格
*/
function act_goods_number_and_average_price($part_number)
{
	$goods_group= goods_group_by_part_number($part_number);
	if($goods_group){
		$average_price = floatval($goods_group['price_amount']) / intval($goods_group['goods_number']) ;
		//print_r($goods_group);
		//echo "sdsd".floatval($average_price);
		$sql = "UPDATE " . $GLOBALS['ecs']->table('goods') .
	            " SET goods_number =  '$goods_group[goods_number]' , " .
				" average_price =  '$average_price' ,".
				" goods_status =  '0' ".
	            " WHERE goods_id = '$goods_group[goods_id]' LIMIT 1";
	    $GLOBALS['db']->query($sql);
	}else{
		$sql = "UPDATE " . $GLOBALS['ecs']->table('goods') .
	            " SET goods_number =  '0' , " .
				" goods_status =  '1' ".
	            " WHERE part_number = '$part_number' LIMIT 1";
		//echo $sql;
		$GLOBALS['db']->query($sql);	
	}

    
	//$exc->edit("goods_number = '$goods_group[goods_number]', last_update=" .gmtime(), $goods_group['goods_id']);
	//$exc->edit("average_price = '$average_price', last_update=" .gmtime(), $goods_group['goods_id']);
	
}
/* 修改库存和平均成本价 
*  输入 inv_id
*  操作 goods 库 修改库存数量和 成本平均价格
*/
function act_goods_number_and_average_price_inv_id($inv_id)
{

	$goods_group= goods_group_by_inv_id($inv_id);
	if($goods_group){
		$average_price = floatval($goods_group['price_amount']) / intval($goods_group['goods_number']) ;
		//print_r($goods_group);
		//echo "sdsd".floatval($average_price);
		$sql = "UPDATE " . $GLOBALS['ecs']->table('goods') .
	            " SET goods_number =  '$goods_group[goods_number]' , " .
				" average_price =  '$average_price' ,".
				" goods_status =  '0' ".
	            " WHERE goods_id = '$goods_group[goods_id]' LIMIT 1";

	    $GLOBALS['db']->query($sql);
	}else{
		$sql2 = "SELECT part_number FROM " . $GLOBALS['ecs']->table('inventory') ." WHERE inv_id = " .$inv_id;
		$part_number = $GLOBALS['db']->getOne($sql2);
		
		$sql = "UPDATE " . $GLOBALS['ecs']->table('goods') .
	            " SET goods_number =  '0' , " .
				" goods_status =  '1' ".
	            " WHERE part_number = '$part_number' LIMIT 1";
		//echo $sql;
		$GLOBALS['db']->query($sql);
	}

	
}

/**
 * 退回余额、积分、红包（取消、无效、退货时），把订单使用余额、积分、红包设为0
 * @param   array   $order  订单信息
 */
function return_user_surplus_integral_bonus($order)
{
    /* 处理余额、积分、红包 */
    if ($order['user_id'] > 0 && $order['surplus'] > 0)
    {
        log_account_change($order['user_id'], $order['surplus'], 0, 0, 0, sprintf($GLOBALS['_LANG']['return_order_surplus'], $order['order_sn']));
    }

    if ($order['user_id'] > 0 && $order['integral'] > 0)
    {
        log_account_change($order['user_id'], 0, 0, 0, $order['integral'], sprintf($GLOBALS['_LANG']['return_order_integral'], $order['order_sn']));
    }

    if ($order['bonus_id'] > 0)
    {
        unuse_bonus($order['bonus_id']);
    }

    /* 修改订单 */
    $arr = array(
        'bonus_id'  => 0,
        'bonus'     => 0,
        'integral'  => 0,
        'integral_money'    => 0,
        'surplus'   => 0
    );
    update_order($order['order_id'], $arr);
}

/**
 * 更新订单总金额
 * @param   int     $order_id   订单id
 * @return  bool
 */
function update_order_amount($order_id)
{
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $sql = "UPDATE " . $GLOBALS['ecs']->table('order_info') .
            " SET order_amount = " . order_due_field() .
            " WHERE order_id = '$order_id' LIMIT 1";

    return $GLOBALS['db']->query($sql);
}

/**
 * 返回某个订单可执行的操作列表，包括权限判断
 * @param   array   $order      订单信息 order_status, shipping_status, pay_status
 * @param   bool    $is_cod     支付方式是否货到付款
 * @return  array   可执行的操作  confirm, pay, unpay, prepare, ship, unship, receive, cancel, invalid, return, drop
 * 格式 array('confirm' => true, 'pay' => true)
 */
function operable_list($order)
{
    /* 取得订单状态、发货状态、付款状态 */
    $os = $order['order_status'];
    $ss = $order['shipping_status'];
    $ps = $order['pay_status'];

    /* 取得订单操作权限 */
    $actions = $_SESSION['action_list'];
    if ($actions == 'all')
    {
        $priv_list  = array('os' => true, 'ss' => true, 'ps' => true, 'edit' => true);
    }
    else
    {
        $actions    = ',' . $actions . ',';
        $priv_list  = array(
            'os'    => strpos($actions, ',order_os_edit,') !== false,
            'ss'    => strpos($actions, ',order_ss_edit,') !== false,
            'ps'    => strpos($actions, ',order_ps_edit,') !== false,
            'edit'  => strpos($actions, ',order_edit,') !== false
        );
    }

    /* 取得订单支付方式是否货到付款 */
    $payment = payment_info($order['pay_id']);
    $is_cod  = $payment['is_cod'] == 1;

    /* 根据状态返回可执行操作 */
    $list = array();
    if (OS_UNCONFIRMED == $os)
    {
        /* 状态：未确认 => 未付款、未发货 */
        if ($priv_list['os'])
        {
            $list['confirm']    = true; // 确认
            $list['invalid']    = true; // 无效
            $list['cancel']     = true; // 取消
            if ($is_cod)
            {
                /* 货到付款 */
                if ($priv_list['ss'])
                {
                    $list['prepare'] = true; // 配货
                    $list['ship'] = true; // 发货
                }
            }
            else
            {
                /* 不是货到付款 */
                if ($priv_list['ps'])
                {
                    $list['pay'] = true;  // 付款
                }
            }
        }
    }
    elseif (OS_CONFIRMED == $os)
    {
        /* 状态：已确认 */
        if (PS_UNPAYED == $ps)
        {
            /* 状态：已确认、未付款 */
            if (SS_UNSHIPPED == $ss || SS_PREPARING == $ss)
            {
                /* 状态：已确认、未付款、未发货（或配货中） */
                if ($priv_list['os'])
                {
                    $list['cancel'] = true; // 取消
                    $list['invalid'] = true; // 无效
                }
                if ($is_cod)
                {
                    /* 货到付款 */
                    if ($priv_list['ss'])
                    {
                        if (SS_UNSHIPPED == $ss)
                        {
                            $list['prepare'] = true; // 配货
                        }
                        $list['ship'] = true; // 发货
                    }
                }
                else
                {
                    /* 不是货到付款 */
                    if ($priv_list['ps'])
                    {
                        $list['pay'] = true; // 付款
                    }
                }
            }
            else
            {
                /* 状态：已确认、未付款、已发货或已收货 => 货到付款 */
                if ($priv_list['ps'])
                {
                    $list['pay'] = true; // 付款
                }
                if ($priv_list['ss'])
                {
                    if (SS_SHIPPED == $ss)
                    {
                        $list['receive'] = true; // 收货确认
                    }
                    $list['unship'] = true; // 设为未发货
                    if ($priv_list['os'])
                    {
                        $list['return'] = true; // 退货
                    }
                }
            }
        }
        else
        {
            /* 状态：已确认、已付款和付款中 */
            if (SS_UNSHIPPED == $ss || SS_PREPARING == $ss)
            {
                /* 状态：已确认、已付款和付款中、未发货（配货中） => 不是货到付款 */
                if ($priv_list['ss'])
                {
                    if (SS_UNSHIPPED == $ss)
                    {
                        $list['prepare'] = true; // 配货
                    }
                    $list['ship'] = true; // 发货
                }
                if ($priv_list['ps'])
                {
                    $list['unpay'] = true; // 设为未付款
                    if ($priv_list['os'])
                    {
                        $list['cancel'] = true; // 取消
                    }
                }
            }
            else
            {
                /* 状态：已确认、已付款和付款中、已发货或已收货 */
                if ($priv_list['ss'])
                {
                    if (SS_SHIPPED == $ss)
                    {
                        $list['receive'] = true; // 收货确认
                    }
                    if (!$is_cod)
                    {
                        $list['unship'] = true; // 设为未发货
                    }
                }
                if ($priv_list['ps'] && $is_cod)
                {
                    $list['unpay']  = true; // 设为未付款
                }
                if ($priv_list['os'] && $priv_list['ss'] && $priv_list['ps'])
                {
                    $list['return'] = true; // 退货（包括退款）
                }
            }
        }
    }
    elseif (OS_CANCELED == $os)
    {
        /* 状态：取消 */
        if ($priv_list['os'])
        {
            $list['confirm'] = true;
        }
        if ($priv_list['edit'])
        {
            $list['remove'] = true;
        }
    }
    elseif (OS_INVALID == $os)
    {
        /* 状态：无效 */
        if ($priv_list['os'])
        {
            $list['confirm'] = true;
        }
        if ($priv_list['edit'])
        {
            $list['remove'] = true;
        }
    }
    elseif (OS_RETURNED == $os)
    {
        /* 状态：退货 */
        if ($priv_list['os'])
        {
            $list['confirm'] = true;
        }
    }

    /* 修正发货操作 */
    if (!empty($list['ship']))
    {
        /* 如果是团购活动且未处理成功，不能发货 */
        if ($order['extension_code'] == 'group_buy')
        {
            include_once(ROOT_PATH . 'includes/lib_goods.php');
            $group_buy = group_buy_info(intval($order['extension_id']));
            if ($group_buy['status'] != GBS_SUCCEED)
            {
                unset($list['ship']);
            }
        }
    }

    /* 售后 */
    $list['after_service'] = true;

    return $list;
}

/**
 * 处理编辑订单时订单金额变动
 * @param   array   $order  订单信息
 * @param   array   $msgs   提示信息
 * @param   array   $links  链接信息
 */
function handle_order_money_change($order, &$msgs, &$links)
{
    $order_id = $order['order_id'];
    if ($order['pay_status'] == PS_PAYED || $order['pay_status'] == PS_PAYING)
    {
        /* 应付款金额 */
        $money_dues = $order['order_amount'];
        if ($money_dues > 0)
        {
            /* 修改订单为未付款 */
            update_order($order_id, array('pay_status' => PS_UNPAYED, 'pay_time' => 0));
            $msgs[]     = $GLOBALS['_LANG']['amount_increase'];
            $links[]    = array('text' => $GLOBALS['_LANG']['order_info'], 'href' => 'order.php?act=info&order_id=' . $order_id);
        }
        elseif ($money_dues < 0)
        {
            $anonymous  = $order['user_id'] > 0 ? 0 : 1;
            $msgs[]     = $GLOBALS['_LANG']['amount_decrease'];
            $links[]    = array('text' => $GLOBALS['_LANG']['refund'], 'href' => 'order.php?act=process&func=load_refund&anonymous=' .
                $anonymous . '&order_id=' . $order_id . '&refund_amount=' . abs($money_dues));
        }
    }
}

/**
 *  获取订单列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function order_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤信息 */
        $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
        if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)
        {
            $_REQUEST['consignee'] = json_str_iconv($_REQUEST['consignee']);
            //$_REQUEST['address'] = json_str_iconv($_REQUEST['address']);
        }
        $filter['consignee'] = empty($_REQUEST['consignee']) ? '' : trim($_REQUEST['consignee']);
        $filter['email'] = empty($_REQUEST['email']) ? '' : trim($_REQUEST['email']);
        $filter['address'] = empty($_REQUEST['address']) ? '' : trim($_REQUEST['address']);
        $filter['zipcode'] = empty($_REQUEST['zipcode']) ? '' : trim($_REQUEST['zipcode']);
        $filter['tel'] = empty($_REQUEST['tel']) ? '' : trim($_REQUEST['tel']);
        $filter['mobile'] = empty($_REQUEST['mobile']) ? 0 : intval($_REQUEST['mobile']);
        $filter['country'] = empty($_REQUEST['country']) ? 0 : intval($_REQUEST['country']);
        $filter['province'] = empty($_REQUEST['province']) ? 0 : intval($_REQUEST['province']);
        $filter['city'] = empty($_REQUEST['city']) ? 0 : intval($_REQUEST['city']);
        $filter['district'] = empty($_REQUEST['district']) ? 0 : intval($_REQUEST['district']);
        $filter['shipping_id'] = empty($_REQUEST['shipping_id']) ? 0 : intval($_REQUEST['shipping_id']);
        $filter['pay_id'] = empty($_REQUEST['pay_id']) ? 0 : intval($_REQUEST['pay_id']);
        $filter['order_status'] = isset($_REQUEST['order_status']) ? intval($_REQUEST['order_status']) : -1;
        $filter['shipping_status'] = isset($_REQUEST['shipping_status']) ? intval($_REQUEST['shipping_status']) : -1;
        $filter['pay_status'] = isset($_REQUEST['pay_status']) ? intval($_REQUEST['pay_status']) : -1;
        $filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
        $filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);
        $filter['composite_status'] = isset($_REQUEST['composite_status']) ? intval($_REQUEST['composite_status']) : -1;
        $filter['group_buy_id'] = isset($_REQUEST['group_buy_id']) ? intval($_REQUEST['group_buy_id']) : 0;

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
        $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

        $where = 'WHERE 1 ';
        if ($filter['order_sn'])
        {
            $where .= " AND o.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
        }
        if ($filter['consignee'])
        {
            $where .= " AND o.consignee LIKE '%" . mysql_like_quote($filter['consignee']) . "%'";
        }
        if ($filter['email'])
        {
            $where .= " AND o.email LIKE '%" . mysql_like_quote($filter['email']) . "%'";
        }
        if ($filter['address'])
        {
            $where .= " AND o.address LIKE '%" . mysql_like_quote($filter['address']) . "%'";
        }
        if ($filter['zipcode'])
        {
            $where .= " AND o.zipcode LIKE '%" . mysql_like_quote($filter['zipcode']) . "%'";
        }
        if ($filter['tel'])
        {
            $where .= " AND o.tel LIKE '%" . mysql_like_quote($filter['tel']) . "%'";
        }
        if ($filter['mobile'])
        {
            $where .= " AND o.mobile LIKE '%" .mysql_like_quote($filter['mobile']) . "%'";
        }
        if ($filter['country'])
        {
            $where .= " AND o.country = '$filter[country]'";
        }
        if ($filter['province'])
        {
            $where .= " AND o.province = '$filter[province]'";
        }
        if ($filter['city'])
        {
            $where .= " AND o.city = '$filter[city]'";
        }
        if ($filter['district'])
        {
            $where .= " AND o.district = '$filter[district]'";
        }
        if ($filter['shipping_id'])
        {
            $where .= " AND o.shipping_id  = '$filter[shipping_id]'";
        }
        if ($filter['pay_id'])
        {
            $where .= " AND o.pay_id  = '$filter[pay_id]'";
        }
        if ($filter['order_status'] != -1)
        {
            $where .= " AND o.order_status  = '$filter[order_status]'";
        }
        if ($filter['shipping_status'] != -1)
        {
            $where .= " AND o.shipping_status = '$filter[shipping_status]'";
        }
        if ($filter['pay_status'] != -1)
        {
            $where .= " AND o.pay_status = '$filter[pay_status]'";
        }
        if ($filter['user_id'])
        {
            $where .= " AND o.user_id = '$filter[user_id]'";
        }
        if ($filter['user_name'])
        {
            $where .= " AND u.user_name LIKE '%" . mysql_like_quote($filter['user_name']) . "%'";
        }
        if ($filter['start_time'])
        {
            $where .= " AND o.add_time >= '$filter[start_time]'";
        }
        if ($filter['end_time'])
        {
            $where .= " AND o.add_time <= '$filter[end_time]'";
        }

        //综合状态
        switch($filter['composite_status'])
        {
            case CS_AWAIT_PAY :
                $where .= order_query_sql('await_pay');
                break;

            case CS_AWAIT_SHIP :
                $where .= order_query_sql('await_ship');
                break;

            case CS_FINISHED :
                $where .= order_query_sql('finished');
                break;

            default:
                if ($filter['composite_status'] != -1)
                {
                    $where .= " AND o.order_status = '$filter[composite_status]' ";
                }
        }

        /* 团购订单 */
        if ($filter['group_buy_id'])
        {
            $where .= " AND o.extension_code = 'group_buy' AND o.extension_id = '$filter[group_buy_id]' ";
        }

        /* 如果管理员属于某个办事处，只列出这个办事处管辖的订单 */
        $sql = "SELECT agency_id FROM " . $GLOBALS['ecs']->table('admin_user') . " WHERE user_id = '$_SESSION[admin_id]'";
        $agency_id = $GLOBALS['db']->getOne($sql);
        if ($agency_id > 0)
        {
            $where .= " AND o.agency_id = '$agency_id' ";
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
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_info') . " AS o ". $where;
        }

        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT o.inv_id, o.inv_start_time, o.inv_end_time, o.inv_status," .
                    "o.part_number, o.serial_number, o.goods_id, o.action_user, " .
                " FROM " . $GLOBALS['ecs']->table('inventory') . " AS o " .
                "$where ORDER BY $filter[sort_by] $filter[sort_order] ".
                " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";

        foreach (array('order_sn', 'consignee', 'email', 'address', 'zipcode', 'tel', 'user_name') AS $val)
        {
            $filter[$val] = stripslashes($filter[$val]);
        }
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    /* 格式话数据 */
    foreach ($row AS $key => $value)
    {
        $row[$key]['formated_order_amount'] = price_format($value['order_amount']);
        $row[$key]['formated_money_paid'] = price_format($value['money_paid']);
        $row[$key]['formated_total_fee'] = price_format($value['total_fee']);
        $row[$key]['short_order_time'] = local_date('m-d H:i', $value['add_time']);
        if ($value['order_status'] == OS_INVALID || $value['order_status'] == OS_CANCELED)
        {
            /* 如果该订单为无效或取消则显示删除链接 */
            $row[$key]['can_remove'] = 1;
        }
        else
        {
            $row[$key]['can_remove'] = 0;
        }
    }
    $arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}



/*
* 获得库存列表
* ====================================================
*/
function inventory_list(){
	$filter['part_number'] = empty($_REQUEST['part_number']) ? '' : trim($_REQUEST['part_number']);
    $filter['serial_number'] = empty($_REQUEST['serial_number']) ? '' : trim($_REQUEST['serial_number']);
	$filter['goods_name'] = empty($_REQUEST['goods_name']) ? '' : trim($_REQUEST['goods_name']);
    $filter['status_id'] = empty($_REQUEST['status_id']) ? 0 : intval($_REQUEST['status_id']);
    //$filter['inv_price'] = empty($_REQUEST['inv_price']) ? 0 : floatval($_REQUEST['inv_price']);
    $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);
	
	$filter['start_price'] = empty($_REQUEST['start_price']) ? 0 : $_REQUEST['start_price'];
    $filter['end_price'] = empty($_REQUEST['end_price']) ? 1000000 : $_REQUEST['end_price'];
    
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'inv_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = 'WHERE 1 ';
    if ($filter['part_number'])
    {
        $where .= " AND o.part_number LIKE '%" . mysql_like_quote($filter['part_number']) . "%'";
    }
	if ($filter['goods_name'])
    {
        $where .= " AND g.goods_name LIKE '%" . mysql_like_quote($filter['goods_name']) . "%'";
    }
    if ($filter['serial_number'])
    {
        $where .= " AND o.serial_number LIKE '%" . mysql_like_quote($filter['serial_number']) . "%'";
    }
    if ($filter['status_id'])
    {
        $where .= " AND o.status_id = '$filter[status_id]'";
    }
	if ($filter['start_price'] AND $filter['end_price'] )
    {
        $where .= " AND o.inv_price >= '$filter[start_price]'";
		$where .= " AND o.inv_price <= '$filter[end_price]'";
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
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('inventory') ." AS o " . 
				" LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS g ON g.part_number=o.part_number " .
				$where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$sql = 'SELECT '.
				'o.inv_id,o.inv_start_time, o.inv_end_time, o.inv_price, o.status_id,' .
                'o.part_number,o.serial_number,o.action_user,o.action_note,'.
				'g.goods_id ,g.goods_name,'.
				'st.status_name, st.status_desc' .
				
            " FROM " . $GLOBALS['ecs']->table('inventory') . " AS o ".
            " LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS g ON g.part_number=o.part_number " .
            //" FROM " . $GLOBALS['ecs']->table('inventory') ." AS o " .
            //" LEFT JOIN " .$GLOBALS['ecs']->table('goods'). " AS g ON g.part_number=o.part_number ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('inventory_status'). " AS st ON st.status_id=o.status_id ".
            "$where ORDER BY $filter[sort_by] $filter[sort_order] ".
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
			//echo $sql;
	
			$row = $GLOBALS['db']->getAll($sql);
		 	/* 格式话数据 */
		    foreach ($row AS $key => $value)
		    {
		        $row[$key]['inv_start_time'] = local_date('Y-m-d H:i:s', $row[$key]['inv_start_time']);
				$row[$key]['inv_end_time'] = local_date('Y-m-d H:i:s', $row[$key]['inv_end_time']);
		    }
			//print_r($row);
			$arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql' => $sql,);
		    return $arr;
}

/**
 * 更新订单对应的 pay_log
 * 如果未支付，修改支付金额；否则，生成新的支付log
 * @param   int     $order_id   订单id
 */
function update_pay_log($order_id)
{
    $order_id = intval($order_id);
    if ($order_id > 0)
    {
        $sql = "SELECT order_amount FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE order_id = '$order_id'";
        $order_amount = $GLOBALS['db']->getOne($sql);
        if (!is_null($order_amount))
        {
            $sql = "SELECT log_id FROM " . $GLOBALS['ecs']->table('pay_log') .
                    " WHERE order_id = '$order_id'" .
                    " AND order_type = '" . PAY_ORDER . "'" .
                    " AND is_paid = 0";
            $log_id = intval($GLOBALS['db']->getOne($sql));
            if ($log_id > 0)
            {
                /* 未付款，更新支付金额 */
                $sql = "UPDATE " . $GLOBALS['ecs']->table('pay_log') .
                        " SET order_amount = '$order_amount' " .
                        "WHERE log_id = '$log_id' LIMIT 1";
            }
            else
            {
                /* 已付款，生成新的pay_log */
                $sql = "INSERT INTO " . $GLOBALS['ecs']->table('pay_log') .
                        " (order_id, order_amount, order_type, is_paid)" .
                        "VALUES('$order_id', '$order_amount', '" . PAY_ORDER . "', 0)";
            }
            $GLOBALS['db']->query($sql);
        }
    }
}


/**
 * 获得商品列表
 *
 * @access  public
 * @params  integer $isdelete
 * @params  integer $real_goods
 * @return  array
 */
function goods_list($is_delete, $real_goods=1)
{
    /* 过滤条件 */
    $param_str = '-' . $is_delete . '-' . $real_goods;
    $result = get_filter($param_str);
    if ($result === false)
    {
        $day = getdate();
        $today = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);

        $filter['cat_id']           = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['intro_type']       = empty($_REQUEST['intro_type']) ? '' : trim($_REQUEST['intro_type']);
        $filter['is_promote']       = empty($_REQUEST['is_promote']) ? 0 : intval($_REQUEST['is_promote']);
        $filter['stock_warning']    = empty($_REQUEST['stock_warning']) ? 0 : intval($_REQUEST['stock_warning']);
        $filter['brand_id']         = empty($_REQUEST['brand_id']) ? 0 : intval($_REQUEST['brand_id']);
        $filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		$filter['start_price'] 		= empty($_REQUEST['start_price']) ? 0 : $_REQUEST['start_price'];
	    $filter['end_price'] 		= empty($_REQUEST['end_price']) ? 1000000 : $_REQUEST['end_price'];
		$filter['start_number'] 	= empty($_REQUEST['start_number']) ? 0 : $_REQUEST['start_number'];
	    $filter['end_number'] 		= empty($_REQUEST['end_number']) ? 100000 : $_REQUEST['end_number'];
		
        $filter['part_number']      = empty($_REQUEST['part_number']) ? '' : trim($_REQUEST['part_number']);
        if ($_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'goods_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['extension_code']   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
        $filter['is_delete']        = $is_delete;

        $where = $filter['cat_id'] > 0 ? " AND " . get_children($filter['cat_id']) : '';


        /* 库存警告 */
        if ($filter['stock_warning'])
        {
            $where .= ' AND goods_number <= warn_number ';
        }

        /* 品牌 */
        if ($filter['brand_id'])
        {
            $where .= " AND brand_id='$filter[brand_id]'";
        }
		/*平均价格区间*/
		if ($filter['start_price'] AND $filter['end_price'] )
	    {
	        $where .= " AND average_price >= '$filter[start_price]'";
			$where .= " AND average_price <= '$filter[end_price]'";
	    }
		/*库存数量区间*/
		if ($filter['start_number'] AND $filter['end_number'] )
	    {
	        $where .= " AND goods_number >= '$filter[start_number]'";
			$where .= " AND goods_number <= '$filter[end_number]'";
	    }
		
		

        /* 扩展 */
        if ($filter['extension_code'])
        {
            $where .= " AND extension_code='$filter[extension_code]'";
        }

        /* 关键字 */
        if (!empty($filter['keyword']))
        {
            $where .= " AND (goods_sn LIKE '%" . mysql_like_quote($filter['keyword']) . "%' OR goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%')";
        }
		/* partnumber */
        if (!empty($filter['part_number']))
        {
            $where .= " AND (part_number LIKE '%" . mysql_like_quote($filter['part_number']) . "%')";
//            $where .= " AND (part_number LIKE '%" . mysql_like_quote($filter['part_number']) . "%')";
        }

        if ($real_goods > -1)
        {
            $where .= " AND is_real='$real_goods'";
        }

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('goods'). " AS g WHERE is_delete='$is_delete' $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT goods_id, goods_name, goods_sn, shop_price,average_price, part_number, is_on_sale, is_best, is_new, is_hot, goods_number, integral " .
                    " FROM " . $GLOBALS['ecs']->table('goods') . " AS g WHERE is_delete='$is_delete' $where" .
                    " ORDER BY $filter[sort_by] $filter[sort_order] ".
                    " LIMIT " . $filter['start'] . ",$filter[page_size]";

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql, $param_str);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $row = $GLOBALS['db']->getAll($sql);

    return array('goods' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'sql' => $sql,'record_count' => $filter['record_count']);
}

/**
 * 获得商品列表
 *
 * @access  public
 * @params  integer $isdelete
 * @params  integer $real_goods
 * @return  array
 */
function status_accounting_list($is_delete, $real_goods=1)
{
    /* 过滤条件 */
    $param_str = '-' . $is_delete . '-' . $real_goods;
    $result = get_filter($param_str);
    if ($result === false)
    {
        $day = getdate();
        $today = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);

        $filter['cat_id']           = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['intro_type']       = empty($_REQUEST['intro_type']) ? '' : trim($_REQUEST['intro_type']);
        $filter['is_promote']       = empty($_REQUEST['is_promote']) ? 0 : intval($_REQUEST['is_promote']);
        $filter['stock_warning']    = empty($_REQUEST['stock_warning']) ? 0 : intval($_REQUEST['stock_warning']);
        $filter['brand_id']         = empty($_REQUEST['brand_id']) ? 0 : intval($_REQUEST['brand_id']);
        $filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		$filter['start_price'] 		= empty($_REQUEST['start_price']) ? 0 : $_REQUEST['start_price'];
	    $filter['end_price'] 		= empty($_REQUEST['end_price']) ? 1000000 : $_REQUEST['end_price'];
		$filter['start_number'] 	= empty($_REQUEST['start_number']) ? 0 : $_REQUEST['start_number'];
	    $filter['end_number'] 		= empty($_REQUEST['end_number']) ? 100000 : $_REQUEST['end_number'];
		
        $filter['part_number']      = empty($_REQUEST['part_number']) ? '' : trim($_REQUEST['part_number']);
        if ($_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'goods_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['extension_code']   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
        $filter['is_delete']        = $is_delete;

        $where = $filter['cat_id'] > 0 ? " AND " . get_children($filter['cat_id']) : '';


        /* 库存警告 */
        if ($filter['stock_warning'])
        {
            $where .= ' AND goods_number <= warn_number ';
        }

        /* 品牌 */
        if ($filter['brand_id'])
        {
            $where .= " AND brand_id='$filter[brand_id]'";
        }
		/*平均价格区间*/
		if ($filter['start_price'] AND $filter['end_price'] )
	    {
	        $where .= " AND average_price >= '$filter[start_price]'";
			$where .= " AND average_price <= '$filter[end_price]'";
	    }
		/*库存数量区间*/
		if ($filter['start_number'] AND $filter['end_number'] )
	    {
	        $where .= " AND goods_number >= '$filter[start_number]'";
			$where .= " AND goods_number <= '$filter[end_number]'";
	    }
		
		

        /* 扩展 */
        if ($filter['extension_code'])
        {
            $where .= " AND extension_code='$filter[extension_code]'";
        }

        /* 关键字 */
        if (!empty($filter['keyword']))
        {
            $where .= " AND (goods_sn LIKE '%" . mysql_like_quote($filter['keyword']) . "%' OR goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%')";
        }
		/* partnumber */
        if (!empty($filter['part_number']))
        {
            $where .= " AND (part_number LIKE '%" . mysql_like_quote($filter['part_number']) . "%')";
//            $where .= " AND (part_number LIKE '%" . mysql_like_quote($filter['part_number']) . "%')";
        }

        if ($real_goods > -1)
        {
            $where .= " AND is_real='$real_goods'";
        }

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('goods'). " AS g WHERE is_delete='$is_delete' $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT goods_id, goods_name, goods_sn, shop_price,average_price, part_number, is_on_sale, is_best, is_new, is_hot, goods_number, integral " .
                    " FROM " . $GLOBALS['ecs']->table('goods') . " AS g WHERE is_delete='$is_delete' $where" .
                    " ORDER BY $filter[sort_by] $filter[sort_order] ".
                    " LIMIT " . $filter['start'] . ",$filter[page_size]";

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql, $param_str);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $row = $GLOBALS['db']->getAll($sql);

	/* 为每行添加 分项库存 */
	foreach($row AS $key => $val){
		$row[$key]['status_accounting'] = get_status_accounting($val['part_number']);	
	}
		
    return array('goods' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'sql' => $sql,'record_count' => $filter['record_count']);
}

/* 为每行添加 分项库存 */
function get_status_accounting($part_number){
	$temp_list = get_status_list('full_id_inv');
	
	/*		*/
	$sql_st = "SELECT COUNT(*) AS accounting_num, status_id ". 
			" FROM ". $GLOBALS['ecs']->table('inventory') . 
			" WHERE part_number = '$part_number' ".
			" GROUP BY status_id ORDER BY status_id ASC";
	
	$sc = $GLOBALS['db']->getAll($sql_st);
//	echo $sql_st."<br>";
	if(!empty($sc))
	{
		foreach ($sc AS $key => $value)
    	{
			$temp_list[$value['status_id']] = $value['accounting_num'];
    	}
	}

	//print_r($sc);			echo '<br>';
	//print_r($temp_list);	echo '<br>';
	
	return $temp_list;
}



/**
 * 根据属性数组创建属性的表单
 *
 * @access  public
 * @param   int     $cat_id     分类编号
 * @param   int     $goods_id   商品编号
 * @return  string
 */
function build_serial_html($count)
{
    $attr = array();
	for ($i = 0; $i < $count; $i++) {
	    //echo $i;
		$attr[$i]['attr_id'] = $i;
	}//get_attr_list($cat_id, $goods_id);
	//print_r($attr);
    $html = '<table width="100%" id="attrTable">';
	
    $spec = 0;
	/**/
    foreach ($attr AS $key => $val)
    {
        $html .= "<tr><td width='30%' align='right'>";

            $html .= ($spec != $val['attr_id']) ?
                "<a href='javascript:;' onclick='removeSpec(this)'>[-]</a>":
                "<a href='javascript:;' onclick='addSpec(this)'>[+]</a>";
            $spec = $val['attr_id'];


        $html .= "$val[attr_name]</td><td>单品序列号 : <input type='hidden' name='attr_id_list[]' value='$val[attr_id]' />";
		$key_t = $key+1;
        $html .= '<input name="attr_value_list[]" type="text" id='."ser_".$key_t.' value="' .htmlspecialchars($val['attr_value']). '" size="30" onblur="validate_repeat(this.id,this.value)"/> ';

        $html .= '</td></tr>';
    }
	

    $html .= '</table>';

    return $html;
}

/**
 * 根据属性数组创建属性的表单
 *
 * @access  public
 * @param   int     $cat_id     分类编号
 * @param   int     $goods_id   商品编号
 * @return  string
 */
function build_batch_html($count)
{
    $attr = array();
	for ($i = 0; $i < $count; $i++) {
	    //echo $i;
		$attr[$i]['attr_id'] = $i;
	}//get_attr_list($cat_id, $goods_id);
	//print_r($attr);
    $html = '<table width="100%" id="attrTable">';
	
    $spec = 0;
	/**/
    foreach ($attr AS $key => $val)
    {
        $html .= "<tr bgcolor='#fafafa'><td class='label' style='height:50px;padding:4px 15px;'>";

            $html .= ($spec != $val['attr_id']) ?
                "<a href='javascript:;' onclick='removeSpec(this)'>[-]</a>":
                "<a href='javascript:;' onclick='addSpec(this)'>[+]</a>";
            $spec = $val['attr_id'];

	    $html .= "</td><td style='height:50px;padding:4px;'>";
        $html .= "Part_number : &nbsp; &nbsp;";
        $html .= '<input name="attr_part_list[]" id='."par_".$key.' type="text" size="20" /> ';

        $html .= "<br /><div style='height:8px;'></div>Serial_number : ";
        $html .= '<input name="attr_serial_list[]" id='."ser_".$key.' type="text" size="30" onblur="validate_part_and_serial(this.id,this.value)" /> ';
		

        $html .= '</td></tr>';
    }
	

    $html .= '</table>';

    return $html;
}



/**
 * 修改商品某字段值
 * @param   string  $goods_id   商品编号，可以为多个，用 ',' 隔开
 * @param   string  $field      字段名
 * @param   string  $value      字段值
 * @return  bool
 */
function update_inventory($goods_id, $field, $value)
{
    if ($goods_id)
    {
        /* 清除缓存 */
        clear_cache_files();

        $sql = "UPDATE " . $GLOBALS['ecs']->table('inventory') .
                " SET $field = '$value' , last_update = '". gmtime() ."' " .
                "WHERE inv_id " . db_create_in($goods_id);

        return $GLOBALS['db']->query($sql);
    }
    else
    {
        return false;
    }
}


/**
 * 从回收站删除多个商品
 * @param   mix     $goods_id   商品id列表：可以逗号格开，也可以是数组
 * @return  void
 */
function delete_inventory($inv_id)
{
    if (empty($inv_id))
    {
        return;
    }

    /* 删除商品 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('inventory') .
            " WHERE inv_id " . db_create_in($inv_id);
    $GLOBALS['db']->query($sql);



    /* 清除缓存 */
    clear_cache_files();
}

/**
 * 列表链接
 * @param   bool    $is_add         是否添加（插入）
 * @param   string  $extension_code 虚拟商品扩展代码，实体商品为空
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true, $extension_code = '')
{
    $href = 'goods.php?act=list';
    if (!empty($extension_code))
    {
        $href .= '&extension_code=' . $extension_code;
    }
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }

    if ($extension_code == 'virtual_card')
    {
        $text = $GLOBALS['_LANG']['50_virtual_card_list'];
    }
    else
    {
        $text = $GLOBALS['_LANG']['01_goods_list'];
    }

    return array('href' => $href, 'text' => $text);
}

//获得 status_id 通过 status_desc英文描述
function get_status_name_by_id($status_id)
{
	$sql = "SELECT status_name FROM " . $GLOBALS['ecs']->table('inventory_status') . " WHERE status_id = '$status_id'";
    $status_name = $GLOBALS['db']->getOne($sql);
	//echo $sql;
	if($status_name != '')
	{
		return $status_name;
	}
	else{return false;}
}

//获得 status_id 通过 status_desc英文描述
function get_status_id_by_desc($status_desc)
{
	$sql = "SELECT status_id FROM " . $GLOBALS['ecs']->table('inventory_status') . " WHERE status_desc = '$status_desc'";
    $status_id = $GLOBALS['db']->getOne($sql);
	//echo $sql;
	if($status_id > 0)
	{
		return $status_id;
	}
	else{return false;}
}

//获得 status_id 通过 status_desc英文描述
function get_status_name_by_desc($status_desc)
{
	$sql = "SELECT status_name FROM " . $GLOBALS['ecs']->table('inventory_status') . " WHERE status_desc = '$status_desc'";
    $status_name = $GLOBALS['db']->getOne($sql);
	//echo $sql;
	if($status_name != '')
	{
		return $status_name;
	}
	else{return false;}
}



/**
 * 记录管理员的库存操作内容
 *
 * @access  public
 * @param   string      $sn         数据的唯一值
 * @param   string      $action     操作的类型
 * @param   string      $content    操作的内容
 * @return  void
 */
function inventory_log($inv_id, $status_name, $action_note,$is_serialnumber = 0)
{
	if($is_serialnumber){
		$sql = "SELECT inv_id FROM " . $GLOBALS['ecs']->table('inventory') ." WHERE serial_number = '$inv_id' ";		
		$inv_id = $GLOBALS['db']->getOne($sql);
	}
	$gmtime = gmtime();
	$inv_id = explode(',', $inv_id);
	
	foreach($inv_id AS $key => $val){
		$sql = "INSERT INTO ". $GLOBALS['ecs']->table('inventory_log')."(inv_id,action_user,status_name,action_note,log_time) ".
	           "VALUES ( '$val', '$_SESSION[admin_name]', '$status_name', '$action_note', '$gmtime' )";
		//echo $sql;
	    $GLOBALS['db']->query($sql);
		
	}
	
	/*
    if (is_array($inv_id))
    {
//        $inv_id = explode(',', $inv_id);
    }
//    $inv_id = array_unique($inv_id);

	if(is_array($inv_id)){
		$inv_id = explode(',', $inv_id);
		foreach($inv_id AS $key => $val){
			$sql = "INSERT INTO ". $GLOBALS['ecs']->table('inventory_log')."(inv_id,action_user,status_name,action_note,log_time) ".
		           "VALUES ( '$val', '$_SESSION[admin_name]', '$status_name', '$action_note', '$gmtime' )";
			//echo $sql;
		    $GLOBALS['db']->query($sql);
		}
	}

	*/
}


	/*
	* 获得库存列表
	* ====================================================
	*/
	function inventory_list_log(){
		$filter['part_number'] = empty($_REQUEST['part_number']) ? '' : trim($_REQUEST['part_number']);
		$filter['serial_number'] = empty($_REQUEST['serial_number']) ? '' : trim($_REQUEST['serial_number']);

	    $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
	    $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

		
		$filter['action_note'] = empty($_REQUEST['action_note']) ? '' : trim($_REQUEST['action_note']);
		$filter['admin_name'] = empty($_REQUEST['admin_name']) ? '' : trim($_REQUEST['admin_name']);
		$filter['goods_name'] = empty($_REQUEST['goods_name']) ? '' : trim($_REQUEST['goods_name']);
		$filter['status_name'] = empty($_REQUEST['status_name']) ? '' : trim($_REQUEST['status_name']);
		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'action_id' : trim($_REQUEST['sort_by']);
	    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

		$where = 'WHERE 1 ';
	    if ($filter['part_number'])
	    {
	        $where .= " AND g.part_number LIKE '%" . mysql_like_quote($filter['part_number']) . "%'";
	    }
	    if ($filter['serial_number'])
	    {
	        $where .= " AND g.serial_number LIKE '%" . mysql_like_quote($filter['serial_number']) . "%'";
	    }
		if ($filter['goods_name'])
	    {
	        $where .= " AND goods_name LIKE '%" . mysql_like_quote($filter['goods_name']) . "%'";
	    }
	    if ($filter['status_name'])
	    {
	        $where .= " AND o.status_name LIKE '%" . mysql_like_quote($filter['status_name']) . "%'";
	    }
	
	    if ($filter['admin_name'])
	    {
	        $where .= " AND o.action_user LIKE '%" . mysql_like_quote($filter['admin_name']) . "%'";
	    }
		if ($filter['action_note'])
	    {
	        $where .= " AND o.action_note LIKE '%" . mysql_like_quote($filter['action_note']) . "%'";
	    }
		
		if ($filter['start_time'] && $filter['end_time'] )
		    {
		        $where .= " AND o.log_time >= '$filter[start_time]'";
				$where .= " AND o.log_time <= '$filter[end_time]'";
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
	    if ($filter['part_number'] || $filter['serial_number'] ||$filter['goods_name'] )
	    {
	        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('inventory_log') . " AS o ".
        		" LEFT JOIN " .$GLOBALS['ecs']->table('inventory') ." AS g ON g.inv_id = o.inv_id " .
    			" LEFT JOIN " .$GLOBALS['ecs']->table('goods') ." AS gb ON gb.part_number = g.part_number " . $where;
	    }
	    else
	    {
	        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('inventory_log') ." AS o " .$where;
	    }

	    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
	    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

		$sql = 'SELECT '.
					'o.inv_id,o.action_id, o.action_user, o.status_name, o.log_time, o.action_note,' .
					'g.inv_id,g.part_number ,g.serial_number,gd.goods_name'.

	            " FROM " . $GLOBALS['ecs']->table('inventory_log') . " AS o ".
	            " LEFT JOIN " .$GLOBALS['ecs']->table('inventory') ." AS g ON g.inv_id = o.inv_id " .
	            //" FROM " . $GLOBALS['ecs']->table('inventory') ." AS o " .
	            " LEFT JOIN " .$GLOBALS['ecs']->table('goods'). " AS gd ON gd.part_number = g.part_number ".
				//" LEFT JOIN " .$GLOBALS['ecs']->table('inventory_status'). " AS st ON st.status_id=o.status_id ".
	            "$where ORDER BY $filter[sort_by] $filter[sort_order] ".
				" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
				//echo $sql;

				$row = $GLOBALS['db']->getAll($sql);
				//print_r($row);
			 	/* 格式话数据 */
			    foreach ($row AS $key => $value)
			    {
			        $row[$key]['log_time'] = local_date('Y-m-d H:i:s', $row[$key]['log_time']);
			    }
				//print_r($row);
				$arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql' => $sql);
			    return $arr;
	}


?>