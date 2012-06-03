<?php

/**
 * SINEMALL 购物流程 * $Author: testyang $
 * $Id: flow.php 14698 2008-07-04 07:25:39Z testyang $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

if (!isset($_REQUEST['step']))
{
    $_REQUEST['step'] = "cart";
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

assign_template();
assign_dynamic('flow');
$position = assign_ur_here(0, $_LANG['shopping_flow']);
$smarty->assign('page_title',       $position['title']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

$smarty->assign('categories',       get_categories_tree()); // 分类树
$smarty->assign('helps',            get_shop_help());       // 网店帮助
$smarty->assign('lang',             $_LANG);
$smarty->assign('show_marketprice', $_CFG['show_marketprice']);

if ($_REQUEST['step'] == 'add_to_cart')
{
    /*------------------------------------------------------ */
    //-- 添加商品到购物车
    /*------------------------------------------------------ */
    /*
	include_once('includes/cls_json.php');
    $_POST['goods'] = json_str_iconv($_POST['goods']);

    if (!empty($_REQUEST['goods_id']) && empty($_POST['goods']))
    {
        if (!is_numeric($_REQUEST['goods_id']) || intval($_REQUEST['goods_id']) <= 0)
        {
            ecs_header("Location:./\n");
        }
        $goods_id = intval($_REQUEST['goods_id']);
        exit;
    }

    $json  = new JSON;

    if (empty($_POST['goods']))
    {
        $result['error'] = 1;
        die($json->encode($result));
    }

    $goods = $json->decode($_POST['goods']);
	*/
	
	
	$result = array('error' => 0, 'message' => '', 'content' => '', 'goods_id' => '');
    
	$goods = array();
	$goods['goods_id'] 	= empty($_REQUEST['goods_id']) ? 1 : $_REQUEST['goods_id'] ;
	$goods['number'] 	= empty($_REQUEST['number']) ? 1 : $_REQUEST['number'] ;	
	$goods['buy_price'] = empty($_REQUEST['buy_price']) ? 0 : $_REQUEST['buy_price'] ;
	$goods['parent']	= empty($_REQUEST['parent']) ? 0 : $_REQUEST['parent'] ;
	$goods['spec']		= array() ;
		
	$temp_goods_number = $db->getOne("SELECT goods_number FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id='$goods[goods_id]'");
	
	
	
	if($temp_goods_number == 0)
	{
		show_message("已经有人秒杀了,下次吧.", $_LANG['back_home_lnk'], '', 'info', true,'five');
		exit;
	}
	
	/*
	$goods->goods_id 	= empty($_REQUEST['goods_id']) ? 	1 : $_REQUEST['goods_id'] ;
	$goods->number 		= empty($_REQUEST['number']) ? 		1 : $_REQUEST['cat_id'] ;
	$goods->buy_pirce	= empty($_REQUEST['buy_pirce']) ? 	0 : $_REQUEST['buy_pirce'] ;
	$goods->parent		= empty($_REQUEST['parent']) ? 		0 : $_REQUEST['parent'] ;
	$goods->spec		= array() ;
	*/
	
	
    /* 如果商品有规格，而post的数据没有规格，跳到商品详情页 
    if (empty($goods->spec))
    {
        $sql = "SELECT COUNT(*) " .
                "FROM " . $ecs->table('goods_attr') . " AS ga, " .
                          $ecs->table('attribute') . " AS a " .
                "WHERE ga.attr_id = a.attr_id " .
                "AND ga.goods_id = '" . $goods->goods_id . "' " .
                "AND a.attr_type = 1";
        if ($db->getOne($sql) > 0)
        {
            $result['error']   = ERR_NEED_SELECT_ATTR;
            $result['goods_id'] = $goods->goods_id;
            $result['message'] = $_LANG['please_select_attr'];

            die($json->encode($result));
        }
    }
	*/
    /* 如果是一步购物，先清空购物车 */
    if ($_CFG['one_step_buy'] == '1')
    {
        clear_cart();
    }

    /* 商品数量是否合法 */
    if (!is_numeric($goods['number']) || intval($goods['number']) <= 0)
    {
        $result['error']   = 1;
        $result['message'] = $_LANG['invalid_number'];
    }
    else
    {	
		echo $goods['buy_pirce']."<br>";
        /* 添加到购物车 */
        if (addto_cart($goods['goods_id'],$goods['number'], $goods['spec'], $goods['parent'],$goods['buy_price']))
        {
						
			//print_r($goods);
	
            if ($_CFG['cart_confirm'] > 2)
            {
                $result['message'] = '';
            }
            else
            {
                $result['message'] = $_CFG['cart_confirm'] == 1 ? $_LANG['addto_cart_success_1'] : $_LANG['addto_cart_success_2'];
            }

            $result['content'] = insert_cart_info();
            $result['one_step_buy'] = $_CFG['one_step_buy'];
        }
        else
        {
            $result['message']  = $err->last_message();
            $result['error']    = $err->error_no;
            $result['goods_id'] = stripslashes($goods['goods_id']);
        }
    }
    $result['confirm_type'] = !empty($_CFG['cart_confirm']) ? $_CFG['cart_confirm'] : 2;
    //die($json->encode($result));

}
elseif ($_REQUEST['step'] == 'done')
{
    /*------------------------------------------------------ */
    //-- 完成所有订单操作，提交到数据库
    /*------------------------------------------------------ */

    include_once('includes/lib_clips.php');
    include_once('includes/lib_payment.php');

	
	$result = array('error' => 0, 'message' => '', 'content' => '', 'goods_id' => '');
    
	$goods = array();
	$goods['goods_id'] 	= empty($_REQUEST['goods_id']) ? 1 : $_REQUEST['goods_id'] ;
	$goods['number'] 	= empty($_REQUEST['number']) ? 1 : $_REQUEST['number'] ;
	$goods['buy_price'] = empty($_REQUEST['buy_price']) ? 0 : $_REQUEST['buy_price'] ;
	$goods['parent']	= empty($_REQUEST['parent']) ? 0 : $_REQUEST['parent'] ;
	$goods['spec']		= array() ;
		
	$temp_goods_number = $db->getOne("SELECT goods_number FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id='$goods[goods_id]'");
	
	if($temp_goods_number == 0)
	{
		show_message("已经有人秒杀了,下次吧.", $_LANG['back_home_lnk'], '', 'info', true,'five');
		exit;
	}else{
		$max_jinbi = empty($_REQUEST['max_jinbi']) ? 0 : $_REQUEST['max_jinbi'] ;
		$max_jinbi = $max_jinbi - $max_jinbi * 2;
		log_account_change($_SESSION['user_id'], $max_jinbi, 0, 0, 0, "秒杀扣去金币");






		//

		if (addto_cart($goods['goods_id'],$goods['number'], $goods['spec'], $goods['parent'],$goods['buy_price']))
	    {

			//print_r($goods);

	        if ($_CFG['cart_confirm'] > 2)
	        {
	            $result['message'] = '';
	        }
	        else
	        {
	            $result['message'] = $_CFG['cart_confirm'] == 1 ? $_LANG['addto_cart_success_1'] : $_LANG['addto_cart_success_2'];
	        }

	        $result['content'] = insert_cart_info();
	        $result['one_step_buy'] = $_CFG['one_step_buy'];
	    }
	    else
	    {
	        $result['message']  = $err->last_message();
	        $result['error']    = $err->error_no;
	        $result['goods_id'] = stripslashes($goods['goods_id']);
	    }

	    /*
	     * 检查用户是否已经登录
	     * 如果用户已经登录了则检查是否有默认的收货地址
	     * 如果没有登录则跳转到登录和注册页面
	     */
	    if (empty($_SESSION['direct_shopping']) && $_SESSION['user_id'] == 0)
	    {
	        /* 用户没有登录且没有选定匿名购物，转向到登录页面 */
	        ecs_header("Location: flow.php?step=login\n");
	        exit;
	    }

	    $consignee = get_consignee($_SESSION['user_id']);

	    $_POST['how_oos'] = isset($_POST['how_oos']) ? intval($_POST['how_oos']) : 0;
	    $_POST['card_message'] = isset($_POST['card_message']) ? htmlspecialchars($_POST['card_message']) : '';
	    $_POST['inv_type'] = !empty($_POST['inv_type']) ? htmlspecialchars($_POST['inv_type']) : '';
	    $_POST['inv_payee'] = isset($_POST['inv_payee']) ? htmlspecialchars($_POST['inv_payee']) : '';
	    $_POST['inv_content'] = isset($_POST['inv_content']) ? htmlspecialchars($_POST['inv_content']) : '';
	    $_POST['postscript'] = isset($_POST['postscript']) ? htmlspecialchars($_POST['postscript']) : '';

	    $order = array(
	        'shipping_id'     => intval($_POST['shipping']),
	        'pay_id'          => intval($_POST['payment']),
	        'pack_id'         => isset($_POST['pack']) ? intval($_POST['pack']) : 0,
	        'card_id'         => isset($_POST['card']) ? intval($_POST['card']) : 0,
	        'card_message'    => isset($_POST['card_message']) ? trim($_POST['card_message']) : '',
	        'surplus'         => isset($_POST['surplus']) ? floatval($_POST['surplus']) : 0.00,
	        'integral'        => isset($_POST['integral']) ? intval($_POST['integral']) : 0,
	        'bonus_id'        => isset($_POST['bonus']) ? intval($_POST['bonus']) : 0,
	        'need_inv'        => empty($_POST['need_inv']) ? 0 : 1,
	        'inv_type'        => empty($_POST['inv_type']) ? '' : $_POST['inv_type'],
	        'inv_payee'       => isset($_POST['inv_payee']) ? trim($_POST['inv_payee']) : '',
	        'inv_content'     => isset($_POST['inv_content']) ? $_POST['inv_content'] : '',
	        'postscript'      => isset($_POST['postscript']) ? trim($_POST['postscript']) : '',
	        'how_oos'         => isset($_LANG['oos'][$_POST['how_oos']]) ? addslashes($_LANG['oos'][$_POST['how_oos']]) : '',
	        'need_insure'     => isset($_POST['need_insure']) ? intval($_POST['need_insure']) : 0,
	        'user_id'         => $_SESSION['user_id'],
	        'add_time'        => gmtime(),
	        'order_status'    => OS_UNCONFIRMED,
	        'shipping_status' => SS_UNSHIPPED,
	        'pay_status'      => PS_UNPAYED,
			'goods_amount'	  => $goods['buy_price'],
			'order_amount'	  => $goods['buy_price']
	        );



	    $order['from_ad']          = $_SESSION['from_ad'];
	    $order['referer']          = is_null($_SESSION['referer']) ? '' : addslashes($_SESSION['referer']);



	    /* 插入订单表 */
	    $error_no = 0;
	    do
	    {
	        $order['order_sn'] = get_order_sn(); //获取新订单号
	        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order, 'INSERT');

	        $error_no = $GLOBALS['db']->errno();

	        if ($error_no > 0 && $error_no != 1062)
	        {
	            die($GLOBALS['db']->errorMsg());
	        }
	    }

		while ($error_no == 1062); //如果是订单号重复则重新提交数据

	    $new_order_id = $db->insert_id();
	    $order['order_id'] = $new_order_id;

	    /* 插入订单商品 */
	    $sql = "INSERT INTO " . $ecs->table('order_goods') . "( " .
	                "order_id, goods_id, goods_name, goods_sn, goods_number, market_price, ".
	                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift) ".
	            " SELECT '$new_order_id', goods_id, goods_name, goods_sn, goods_number, market_price, ".
	                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift ".
	            " FROM " .$ecs->table('cart') .
	            " WHERE session_id = '".SESS_ID."' AND rec_type = '$flow_type'";
	    $db->query($sql);

	    /* 如果使用库存，且下订单时减库存，则减少库存 */
	    if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
	    {
	        change_order_goods_storage($order['order_id']);
	    }

	    /* 清空购物车 */
	    clear_cart($flow_type);
	    /* 清除缓存，否则买了商品，但是前台页面读取缓存，商品数量不减少 */
	    clear_all_files();

	    /* 插入支付日志 */
	    $order['log_id'] = insert_pay_log($new_order_id, $order['order_amount'], PAY_ORDER);

	    /* 取得支付信息，生成支付代码 */
	    if ($order['order_amount'] > 0)
	    {
	        $payment = payment_info($order['pay_id']);

	        include_once('includes/modules/payment/' . $payment['pay_code'] . '.php');

	        $pay_obj    = new $payment['pay_code'];

	        $pay_online = $pay_obj->get_code($order, unserialize_config($payment['pay_config']));

	        $order['pay_desc'] = $payment['pay_desc'];

	        $smarty->assign('pay_online', $pay_online);
	    }

	    /* 订单信息 */
	    $smarty->assign('order',      $order);
	    $smarty->assign('total',      $total);
	    $smarty->assign('goods_list', $cart_goods);
	    $smarty->assign('order_submit_back', sprintf($_LANG['order_submit_back'], $_LANG['back_home'], $_LANG['goto_user_center'])); // 返回提示

	    add_feed($order['order_id'], BUY_GOODS); //推送feed到uc
	    unset($_SESSION['flow_consignee']); // 清除session中保存的收货人信息
	    unset($_SESSION['flow_order']);
	    unset($_SESSION['direct_shopping']);
		
	}
}
elseif ($_REQUEST['step'] == 'finish_miaosha')
{
	show_message("恭喜您 秒杀成功,请联系管理员拿回你心爱的礼物吧.", $_LANG['back_home_lnk'], '', 'info', true,'five');
	exit;
}
else
{
    
    /* 标记购物流程为普通商品 */
    $_SESSION['flow_type'] = CART_GENERAL_GOODS;


    /* 取得商品列表，计算合计 */
    $cart_goods = get_cart_goods();
    $smarty->assign('goods_list', $cart_goods['goods_list']);
    $smarty->assign('total', $cart_goods['total']);

    //购物车的描述的格式化
    $smarty->assign('shopping_money',         sprintf($_LANG['shopping_money'], $cart_goods['total']['goods_price']));
    $smarty->assign('market_price_desc',      sprintf($_LANG['than_market_price'],
        $cart_goods['total']['market_price'], $cart_goods['total']['saving'], $cart_goods['total']['save_rate']));

    // 显示收藏夹内的商品
    if ($_SESSION['user_id'] > 0)
    {
        require_once(ROOT_PATH . 'includes/lib_clips.php');
        $collection_goods = get_collection_goods($_SESSION['user_id']);
        $smarty->assign('collection_goods', $collection_goods);
    }

    /* 取得优惠活动 */
    $favourable_list = favourable_list($_SESSION['user_rank']);
    usort($favourable_list, 'cmp_favourable');
    //print_r($favourable_list);
    $smarty->assign('favourable_list', $favourable_list);

    /* 计算折扣 */
    $discount = compute_discount();
    $smarty->assign('discount', $discount['discount']);
    $favour_name = empty($discount['name']) ? '' : join(',', $discount['name']);
    $smarty->assign('your_discount', sprintf($_LANG['your_discount'], $favour_name, price_format($discount['discount'])));
}

$smarty->assign('currency_format', $_CFG['currency_format']);
$smarty->assign('integral_scale',  $_CFG['integral_scale']);
$smarty->assign('step',            $_REQUEST['step']);

assign_dynamic('shopping_flow');

$smarty->display('flow.dwt');

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得用户的可用积分
 *
 * @access  private
 * @return  integral
 */
function flow_available_points()
{
    $sql = "SELECT SUM(g.integral * c.goods_number) ".
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.session_id = '" . SESS_ID . "' AND c.goods_id = g.goods_id AND c.is_gift = 0 AND g.integral > 0 " .
            "AND c.rec_type = '" . CART_GENERAL_GOODS . "'";

    $val = intval($GLOBALS['db']->getOne($sql));

    return integral_of_value($val);
}

/**
 * 更新购物车中的商品数量
 *
 * @access  public
 * @param   array   $arr
 * @return  void
 */
function flow_update_cart($arr)
{
    foreach ($arr AS $key => $val)
    {
        $val = intval(make_semiangle($val));
        if ($val <= 0)
        {
            continue;
        }

        /* 系统启用了库存，检查输入的商品数量是否有效 */
        if (intval($GLOBALS['_CFG']['use_storage']) > 0)
        {
            $sql = "SELECT g.goods_name, g.goods_number ".
                    "FROM " .$GLOBALS['ecs']->table('goods'). " AS g, ".
                        $GLOBALS['ecs']->table('cart'). " AS c ".
                    "WHERE g.goods_id = c.goods_id AND c.rec_id = '$key'";
            $row = $GLOBALS['db']->getRow($sql);

            if ($row['goods_number'] < $val)
            {
                show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
                $row['goods_number'], $row['goods_number']));
                exit;
            }
        }

        /* 检查该项是否为基本件以及有没有配件存在 */
        $sql = "SELECT a.goods_number, a.rec_id FROM " .$GLOBALS['ecs']->table('cart') . " AS b ".
                "LEFT JOIN " . $GLOBALS['ecs']->table('cart') . " AS a ".
                    "ON a.parent_id = b.goods_id AND a.session_id = '" . SESS_ID . "'".
                "WHERE b.rec_id = '$key'";

        $fittings = $GLOBALS['db']->getAll($sql);

        if ($val > 0)
        {
            foreach ($fittings AS $k => $v)
            {
                if ($v['goods_number'] != null && $v['rec_id'] != null)
                {
                    /* 该商品有配件，更新配件的商品数量 */
                    $num = ($v['goods_number']) > $val ? $val : $v['goods_number'];

                    $sql = "UPDATE " . $GLOBALS['ecs']->table('cart') .
                            " SET goods_number = '$num' WHERE rec_id = $v[rec_id]";
                    $GLOBALS['db']->query($sql);
                }
            }

            /* 更新购物车中的商品数量 */
            $sql = "UPDATE " .$GLOBALS['ecs']->table('cart').
                    " SET goods_number = '$val' WHERE rec_id='$key' AND session_id='" . SESS_ID . "'";
        }
        else
        {
            if (is_object($fittings) && $fittings->goods_number != null && $fittings->rec_id != null)
            {
                $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart'). " WHERE rec_id=$fittings[rec_id]";
                $GLOBALS['db']->query($sql);
            }

            $sql = "DELETE FROM " .$GLOBALS['ecs']->table('cart').
                " WHERE rec_id='$key' AND session_id='" .SESS_ID. "'";
        }

        $GLOBALS['db']->query($sql);
    }

    /* 删除所有赠品 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '" .SESS_ID. "' AND is_gift <> 0";
    $GLOBALS['db']->query($sql);
}

/**
 * 删除购物车中的商品
 *
 * @access  public
 * @param   integer $id
 * @return  void
 */
function flow_drop_cart_goods($id)
{
    /* 取得商品id */
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('cart'). " WHERE rec_id = '$id'";
    $row = $GLOBALS['db']->getRow($sql);
    if ($row)
    {
        /* 如果是普通商品，同时删除所有赠品及其配件 */
        if ($row['parent_id'] == 0 && $row['is_gift'] == 0)
        {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
                    " WHERE session_id = '" . SESS_ID . "' " .
                    "AND (rec_id = '$id' OR parent_id = '$row[goods_id]' OR is_gift <> 0)";
        }
        /* 如果不是普通商品，只删除该商品即可 */
        else
        {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
                    " WHERE session_id = '" . SESS_ID . "' " .
                    "AND rec_id = '$id' LIMIT 1";
        }
        $GLOBALS['db']->query($sql);
    }
}

/**
 * 比较优惠活动的函数，用于排序（把可用的排在前面）
 * @param   array   $a      优惠活动a
 * @param   array   $b      优惠活动b
 * @return  int     相等返回0，小于返回-1，大于返回1
 */
function cmp_favourable($a, $b)
{
    if ($a['available'] == $b['available'])
    {
        if ($a['sort_order'] == $b['sort_order'])
        {
            return 0;
        }
        else
        {
            return $a['sort_order'] < $b['sort_order'] ? -1 : 1;
        }
    }
    else
    {
        return $a['available'] ? -1 : 1;
    }
}

/**
 * 取得某用户等级当前时间可以享受的优惠活动
 * @param   int     $user_rank      用户等级id，0表示非会员
 * @return  array
 */
function favourable_list($user_rank)
{
    /* 购物车中已有的优惠活动及数量 */
    $used_list = cart_favourable();

    /* 当前用户可享受的优惠活动 */
    $favourable_list = array();
    $user_rank = ',' . $user_rank . ',';
    $now = gmtime();
    $sql = "SELECT * " .
            "FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE CONCAT(',', user_rank, ',') LIKE '%" . $user_rank . "%'" .
            " AND start_time <= '$now' AND end_time >= '$now'" .
            " AND act_type = '" . FAT_GOODS . "'" .
            " ORDER BY sort_order";
    $res = $GLOBALS['db']->query($sql);
    while ($favourable = $GLOBALS['db']->fetchRow($res))
    {
        $favourable['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $favourable['start_time']);
        $favourable['end_time']   = local_date($GLOBALS['_CFG']['time_format'], $favourable['end_time']);
        $favourable['formated_min_amount'] = price_format($favourable['min_amount'], false);
        $favourable['formated_max_amount'] = price_format($favourable['max_amount'], false);
        $favourable['gift']       = unserialize($favourable['gift']);
        foreach ($favourable['gift'] as $key => $value)
        {
            $favourable['gift'][$key]['formated_price'] = price_format($value['price'], false);
        }

        $favourable['act_range_desc'] = act_range_desc($favourable);
        $favourable['act_type_desc'] = sprintf($GLOBALS['_LANG']['fat_ext'][$favourable['act_type']], $favourable['act_type_ext']);

        /* 是否能享受 */
        $favourable['available'] = favourable_available($favourable);
        if ($favourable['available'])
        {
            /* 是否尚未享受 */
            $favourable['available'] = !favourable_used($favourable, $used_list);
        }

        $favourable_list[] = $favourable;
    }

    return $favourable_list;
}

/**
 * 根据购物车判断是否可以享受某优惠活动
 * @param   array   $favourable     优惠活动信息
 * @return  bool
 */
function favourable_available($favourable)
{
    /* 员工级别是否符合 */
    $user_rank = $_SESSION['user_rank'];
    if (strpos(',' . $favourable['user_rank'] . ',', ',' . $user_rank . ',') === false)
    {
        return false;
    }

    /* 优惠范围内的商品总额 */
    $amount = cart_favourable_amount($favourable);

    /* 金额上限为0表示没有上限 */
    return $amount >= $favourable['min_amount'] &&
        ($amount <= $favourable['max_amount'] || $favourable['max_amount'] == 0);
}

/**
 * 取得优惠范围描述
 * @param   array   $favourable     优惠活动
 * @return  string
 */
function act_range_desc($favourable)
{
    if ($favourable['act_range'] == FAR_BRAND)
    {
        $sql = "SELECT brand_name FROM " . $GLOBALS['ecs']->table('brand') .
                " WHERE brand_id " . db_create_in($favourable['act_range_ext']);
        return join(',', $GLOBALS['db']->getCol($sql));
    }
    elseif ($favourable['act_range'] == FAR_CATEGORY)
    {
        $sql = "SELECT cat_name FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'category') .
                " WHERE cat_id " . db_create_in($favourable['act_range_ext']);
        return join(',', $GLOBALS['db']->getCol($sql));
    }
    elseif ($favourable['act_range'] == FAR_GOODS)
    {
        $sql = "SELECT goods_name FROM " . $GLOBALS['ecs']->table('goods') .
                " WHERE goods_id " . db_create_in($favourable['act_range_ext']);
        return join(',', $GLOBALS['db']->getCol($sql));
    }
    else
    {
        return '';
    }
}

/**
 * 取得购物车中已有的优惠活动及数量
 * @return  array
 */
function cart_favourable()
{
    $list = array();
    $sql = "SELECT is_gift, COUNT(*) AS num " .
            "FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "'" .
            " AND rec_type = '" . CART_GENERAL_GOODS . "'" .
            " AND is_gift > 0" .
            " GROUP BY is_gift";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $list[$row['is_gift']] = $row['num'];
    }

    return $list;
}

/**
 * 购物车中是否已经有某优惠
 * @param   array   $favourable     优惠活动
 * @param   array   $cart_favourable购物车中已有的优惠活动及数量
 */
function favourable_used($favourable, $cart_favourable)
{
    if ($favourable['act_type'] == FAT_GOODS)
    {
        return isset($cart_favourable[$favourable['act_id']]) &&
            $cart_favourable[$favourable['act_id']] >= $favourable['act_type_ext'] &&
            $favourable['act_type_ext'] > 0;
    }
    else
    {
        return isset($cart_favourable[$favourable['act_id']]);
    }
}

/**
 * 添加优惠活动（赠品）到购物车
 * @param   int     $act_id     优惠活动id
 * @param   int     $id         赠品id
 * @param   float   $price      赠品价格
 */
function add_gift_to_cart($act_id, $id, $price)
{
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('cart') . " (" .
                "user_id, session_id, goods_id, goods_sn, goods_name, market_price, goods_price, ".
                "goods_number, is_real, extension_code, parent_id, is_gift, rec_type ) ".
            "SELECT '$_SESSION[user_id]', '" . SESS_ID . "', goods_id, goods_sn, goods_name, market_price, ".
                "'$price', 1, is_real, extension_code, 0, '$act_id', '" . CART_GENERAL_GOODS . "' " .
            "FROM " . $GLOBALS['ecs']->table('goods') .
            " WHERE goods_id = '$id'";
    $GLOBALS['db']->query($sql);
}

/**
 * 添加优惠活动（非赠品）到购物车
 * @param   int     $act_id     优惠活动id
 * @param   string  $act_name   优惠活动name
 * @param   float   $amount     优惠金额
 */
function add_favourable_to_cart($act_id, $act_name, $amount)
{
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('cart') . "(" .
                "user_id, session_id, goods_id, goods_sn, goods_name, market_price, goods_price, ".
                "goods_number, is_real, extension_code, parent_id, is_gift, rec_type ) ".
            "VALUES('$_SESSION[user_id]', '" . SESS_ID . "', 0, '', '$act_name', 0, ".
                "'" . (-1) * $amount . "', 1, 0, '', 0, '$act_id', '" . CART_GENERAL_GOODS . "')";
    $GLOBALS['db']->query($sql);
}

/**
 * 取得购物车中某优惠活动范围内的总金额
 * @param   array   $favourable     优惠活动
 * @return  float
 */
function cart_favourable_amount($favourable)
{
    /* 查询优惠范围内商品总额的sql */
    $sql = "SELECT SUM(c.goods_price * c.goods_number) " .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.goods_id = g.goods_id " .
            "AND c.session_id = '" . SESS_ID . "' " .
            "AND c.rec_type = '" . CART_GENERAL_GOODS . "' " .
            "AND c.is_gift = 0 " .
            "AND c.goods_id > 0 ";

    /* 根据优惠范围修正sql */
    if ($favourable['act_range'] == FAR_ALL)
    {
        // sql do not change
    }
    elseif ($favourable['act_range'] == FAR_CATEGORY)
    {
        /* 取得优惠范围分类的所有下级分类 */
        $id_list = array();
        $cat_list = explode(',', $favourable['act_range_ext']);
        foreach ($cat_list as $id)
        {
            $id_list = array_merge($id_list, array_keys(cat_list(intval($id), 0, false)));
        }

        $sql .= "AND g.cat_id " . db_create_in($id_list);
    }
    elseif ($favourable['act_range'] == FAR_BRAND)
    {
        $id_list = explode(',', $favourable['act_range_ext']);

        $sql .= "AND g.brand_id " . db_create_in($id_list);
    }
    else
    {
        $id_list = explode(',', $favourable['act_range_ext']);

        $sql .= "AND g.goods_id " . db_create_in($id_list);
    }

    /* 优惠范围内的商品总额 */
    return $GLOBALS['db']->getOne($sql);
}

?>