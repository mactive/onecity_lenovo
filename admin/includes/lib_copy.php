<?php

/**
 * SINEMALL 订单管理    $Author: testyang $
 * $Id: order.php 14647 2008-06-05 09:09:23Z testyang $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/admin/includes/lib_inventory.php');
$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');
$exc_inv = new exchange($ecs->table('inventory'), $db, 'inv_id', 'serial_number');
$exc_inv_status = new exchange($ecs->table('inventory_status'), $db, 'status_id', 'status_name');

/*------------------------------------------------------ */
//-- part_number 查询
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'inspect_part_number')
{
    /* 检查权限 */
    admin_priv('inventory_view');

    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);

    /* 模板赋值 */
    $ur_here = $_LANG['inspect_part_number'];
    $smarty->assign('ur_here', $ur_here);

   	$action_link = array('href' => 'inventory.php?act=list', 'text' => $_LANG['inventory_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('cat_list',     cat_list(0, $cat_id));
    $smarty->assign('brand_list',   get_brand_list());
	$smarty->assign('status_list',   get_status_list());
    $smarty->assign('intro_list',   get_intro_list());
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    $goods_list = goods_list(0, 1);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('full_page',    1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('inspect_part_number.htm');}

/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('inventory_view');

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['01_inv_list']);
    $smarty->assign('action_link', array('href' => 'inventory.php?act=add', 'text' => $_LANG['add_inventory']));

    $smarty->assign('status_list',get_status_list('inv'));   // 订单状态
	$smarty->assign('batch_list',get_status_list('invdesc'));   // 批量操作
    

    $smarty->assign('full_page',        1);

    $inventory_list = inventory_list();
    $smarty->assign('inventory_list',   $inventory_list['orders']);
    $smarty->assign('filter',       $inventory_list['filter']);
    $smarty->assign('record_count', $inventory_list['record_count']);
    $smarty->assign('page_count',   $inventory_list['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('inventory_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $inventory_list = inventory_list();

    $smarty->assign('inventory_list',   $inventory_list['orders']);
    $smarty->assign('filter',       $inventory_list['filter']);
    $smarty->assign('record_count', $inventory_list['record_count']);
    $smarty->assign('page_count',   $inventory_list['page_count']);
    $sort_flag  = sort_flag($inventory_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('inventory_list.htm'), '', array('filter' => $inventory_list['filter'], 'page_count' => $inventory_list['page_count']));
}


/*------------------------------------------------------ */
//-- 商品核对排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'goods_query')
{
    $goods_list = goods_list(0, 1);

    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('inspect_part_number.htm'), '', array('filter' => $goods_list['filter'], 'page_count' => $goods_list['page_count']));
}
/*------------------------------------------------------ */
//-- 修改商品 part_number
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_part_number')
{
    check_authz_json('inventory_manage');

    $goods_id = intval($_POST['id']);
    $part_number = json_str_iconv(trim($_POST['val']));

    /* 检查是否重复 */
    if (!$exc->is_only('part_number', $part_number, $goods_id))
    {
        make_json_error($_LANG['part_number_exists']);
    }

    if ($exc->edit("part_number = '$part_number', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($part_number));

    }
}

/*------------------------------------------------------ */
//-- 切换商品类型
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_serial_list')
{

    $serial_count   = empty($_GET['serial_count']) ? 0 : intval($_GET['serial_count']);

    $content    = build_serial_html($serial_count);

    make_json_result($content);
}

/*------------------------------------------------------ */
//-- 修改订单（载入页面）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
	/* 检查权限 */
    admin_priv('inventory_manage');	
	
    /* 取得参数 act */
    $act = $_GET['act'];
	if($act == 'add'){
		$part_number = empty($_GET['part_number']) ? '' : trim($_GET['part_number']);
		$smarty->assign('part_number', $part_number);
		$smarty->assign('start_time',local_date($_CFG['time_format'], gmtime() ));
		$smarty->assign('status', 1);	    
	}
	else{
		$inv_id = empty($_GET['inv_id']) ? '' : trim($_GET['inv_id']);
		$smarty->assign('inv_id', $inv_id);
		//获得库存信息
		$infoarr = get_inventory_info($inv_id);
		$smarty->assign('part_number', $infoarr['part_number']);
		$smarty->assign('serial_number', $infoarr['serial_number']);
		$smarty->assign('start_time', local_date($_CFG['time_format'], $infoarr['inv_start_time']));
		$smarty->assign('end_time', local_date($_CFG['time_format'], $infoarr['inv_end_time']));
		$smarty->assign('status', $infoarr['status_id']);
	}
    $smarty->assign('ur_here', (($act == 'add') ?
        $_LANG['add_inventory'] : $_LANG['edit_inventory']));
    $smarty->assign('step_act', $act);

	/* 模板赋值 */
    $smarty->assign('action_link', array('href' => 'inventory.php?act=list', 'text' => $_LANG['inventory_list']));

    $smarty->assign('status_list',get_status_list('inv'));   // 订单状态

    
    /* 显示模板 */
    assign_query_info();
    $smarty->display('inventory_operate.htm');
}

/*------------------------------------------------------ */
//-- 插入库存新数据
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == 'insert')
{
	/*检查品牌名是否重复*/
    admin_priv('inventory_manage');

	$action_user = $_SESSION['admin_name'];

	$start_time = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $end_time   = empty($_REQUEST['end_time']) ? 0 : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

	$attr_value = $_POST['attr_value_list'];
	if($attr_value)
	{
		//print_r($attr_value);
		foreach($attr_value as $vaule){
			if($vaule != ''){
				/*
				$is_only = $exc_inv->is_only('serial_number', $vaule);
				if (!$is_only)
			    {
			        sys_msg(sprintf($_LANG['serial_number_exist'], stripslashes($_POST['serial_number'])), 1);
			    }
				*/
				$sql = "INSERT INTO ".$ecs->table('inventory')."(inv_id, inv_start_time, inv_end_time, status_id, part_number,serial_number, action_user,action_note) ".
			           "VALUES (NULL, $start_time, $end_time, '$_POST[status]', '$_POST[part_number]', '$vaule', '$action_user', '$_POST[action_note]')";
				echo $sql;
			    $db->query($sql);
			    
				
			}
		    
			
		}
	}
	
	$is_only = $exc_inv->is_only('serial_number', $_POST['serial_number']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['serial_number_exist'], stripslashes($_POST['serial_number'])), 1);
    }
	
    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('inventory')."(inv_id, inv_start_time, inv_end_time, status_id, part_number,serial_number, action_user,action_note) ".
           "VALUES (NULL, $start_time, $end_time, '$_POST[status]', '$_POST[part_number]', '$_POST[serial_number]', '$action_user', '$_POST[action_note]')";
    $db->query($sql);

    admin_log($_POST[''],'add','inventory');

    /* 清除缓存 
    clear_cache_files();
	
	$link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'inventory.php?act=add&part_number='.$_POST['part_number'];

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'inventory.php?act=list';

    sys_msg($_LANG['inventoryadd_succed'], 0, $link);
	*/
}

/*------------------------------------------------------ */
//-- 更新库存数据
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == 'update')
{
	/*检查品牌名是否重复*/
    admin_priv('inventory_manage');

	//$is_only = $exc_inv->is_only('serial_number', $_POST['serial_number']);
	/*
    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['serial_number_exist'], stripslashes($_POST['serial_number'])), 1);
    }*/

	$action_user = $_SESSION['admin_name'];

	$start_time = empty($_REQUEST['start_time']) ? 0 : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $end_time   = empty($_REQUEST['end_time']) ? 0 : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

    /*更新数据*/
	$sql = "UPDATE " . $ecs->table('inventory') .
			" SET inv_start_time = '$start_time', " .
				"inv_end_time = '$end_time', ".
				"status_id 	  = '$_POST[status]', ".
				"part_number  = '$_POST[part_number]', ".
				"serial_number = '$_POST[serial_number]', ".
				"action_user = '$action_user' ".
            "WHERE inv_id = '$_POST[inv_id]' LIMIT 1";
	//echo $sql;
    $db->query($sql);

    admin_log($_POST[''],'update','inventory');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = 'inventory.php?act=list';

    sys_msg($_LANG['inventoryedit_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 删除库存数据
/*------------------------------------------------------ */


elseif($_REQUEST['act'] == 'delete')
{
	$inv_id = intval($_REQUEST['id']);
	
	/* 检查权限 */
    check_authz_json('inventory');

    /*删除*/
	
	$sql = "DELETE FROM " . $ecs->table('inventory') . " WHERE inv_id = '$inv_id'";
	$db->query($sql);

    /* 清除缓存 */
	$exc_inv->drop($inv_id);
    clear_cache_files();

	$url = 'inventory.php?act=query&' . str_replace('act=delete', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;

}


/*------------------------------------------------------ */
//-- 库存状态详细页面
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'list_status' || $_REQUEST['act'] == 'add_status' || $_REQUEST['act'] == 'edit_status')
{
	/* 检查权限 */
    admin_priv('inventory_manage');

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['05_inv_status']);
    $smarty->assign('action_link', array('href' => 'inventory.php?act=add_status', 'text' => $_LANG['add_inventory_status']));

    $smarty->assign('status_list',get_status_list('inv'));   // 订单状态
    $smarty->assign('full_page',        1);

    $list_status = get_status_list('fullinv');
    $smarty->assign('list_status',   $list_status);

	/* 取得参数 act */
    $act = $_GET['act'];
	$smarty->assign('step_act',$act);
	if($act == 'edit_status'){
		$edit_status_id = empty($_REQUEST['status_id']) ? 0 : intval($_REQUEST['status_id']);
		$smarty->assign('edit_status_id', $edit_status_id);
		$edit_status_info = get_status_info($edit_status_id);
		$smarty->assign('status_info', $edit_status_info);
	}
	

    /* 显示模板 */
    assign_query_info();
    $smarty->display('inventory_list_status.htm');
	
}

/*------------------------------------------------------ */
//-- 插入库存状态新数据
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == 'insert_status')
{
	/*检查库存状态是否重复*/
    admin_priv('inventory_manage');

	$is_only = $exc_inv_status->is_only('status_name', $_POST['status_name']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['status_name_exist'], stripslashes($_POST['status_name'])), 1);
    }

    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('inventory_status')."(status_id, status_name,status_desc) ".
           "VALUES (NULL, '$_POST[status_name]', '$_POST[status_desc]')";
    $db->query($sql);

    admin_log($_POST[''],'add_status','inventory');

    /* 清除缓存 */
    clear_cache_files();
	
	$link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'inventory.php?act=add_status';

    $link[1]['text'] = $_LANG['back_list_status'];
    $link[1]['href'] = 'inventory.php?act=list_status';

    sys_msg($_LANG['inventory_status_add_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 更新库存状态数据
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == 'update_status')
{
	/*检查品牌名是否重复*/
    admin_priv('inventory_manage');

	//$is_only = $exc_inv->is_only('serial_number', $_POST['serial_number']);
	/*
    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['serial_number_exist'], stripslashes($_POST['serial_number'])), 1);
    }*/

    /*更新数据*/
	$sql = "UPDATE " . $ecs->table('inventory_status') .
			" SET status_name	  = '$_POST[status_name]', ".
				"status_desc  = '$_POST[status_desc]' ".
            "WHERE status_id = '$_POST[status_id]' LIMIT 1";
	//echo $sql;
    $db->query($sql);

    admin_log($_POST[''],'update_status','inventory');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['back_list_status'];
    $link[0]['href'] = 'inventory.php?act=list_status';

    sys_msg($_LANG['inventory_status_edit_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 删除库存状态数据
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == 'delete_status')
{
	$status_id = intval($_REQUEST['id']);
	
	/* 检查权限 */
    check_authz_json('inventory');

    /*删除*/
	
	$sql = "DELETE FROM " . $ecs->table('inventory_status') . " WHERE status_id = '$status_id'";
	$db->query($sql);

    /* 清除缓存 */
	$exc_inv_status->drop($status_id);
    clear_cache_files();

	$url = 'inventory.php?act=query_status&' . str_replace('act=delete_status', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;

}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query_status')
{
    $list_status = get_status_list('fullinv');
    $smarty->assign('list_status',   $list_status);

    make_json_result($smarty->fetch('inventory_list_status.htm'));
}

/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch')
{
    $code = empty($_REQUEST['extension_code'])? '' : trim($_REQUEST['extension_code']);

    /* 取得要操作的商品编号 */
	$checkarray = array('21', '22', '23');
    //$inv_id = !empty($checkarray) ? join(',', $checkarray) : 0;
    $inv_id = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;

    if (isset($_POST['type']))
    {
        /* 放入回收站 */
        if ($_POST['type'] == 'trash')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            update_goods($inv_id, 'is_delete', '1');

            /* 记录日志 */
            admin_log('', 'batch_trash', 'inventory');
        }
		
        /* 还原 */
        elseif ($_POST['type'] == 'restore')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            update_goods($goods_id, 'is_delete', '0');

            /* 记录日志 */
            admin_log('', 'batch_restore', 'goods');
        }
        /* 删除 */
        elseif ($_POST['type'] == 'drop')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            delete_goods($goods_id);

            /* 记录日志 */
            admin_log('', 'batch_remove', 'goods');
        }
    }

    /* 清除缓存 */
    clear_cache_files();

    if ($_POST['type'] == 'trash' || $_POST['type'] == 'drop')
    {
        $link[] = array('href' => 'inventory.php?act=list', 'text' => $_LANG['11_goods_trash']);
    }
    else
    {
        $link[] = list_link(true, $code);
    }
    sys_msg($_LANG['batch_handle_ok'], 0, $link);
}












/*------------------------------------------------------ */
//-- 处理
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'process')
{
    /* 取得参数 func */
    $func = isset($_GET['func']) ? $_GET['func'] : '';

    /* 删除订单商品 */
    if ('drop_order_goods' == $func)
    {
        /* 检查权限 */
        admin_priv('order_edit');

        /* 取得参数 */
        $rec_id = intval($_GET['rec_id']);
        $step_act = $_GET['step_act'];
        $order_id = intval($_GET['order_id']);

        /* 删除 */
        $sql = "DELETE FROM " . $ecs->table('order_goods') .
                " WHERE rec_id = '$rec_id' LIMIT 1";
        $db->query($sql);

        /* 更新商品总金额和订单总金额 */
        update_order($order_id, array('goods_amount' => order_amount($order_id)));
        update_order_amount($order_id);

        /* 跳回订单商品 */
        ecs_header("Location: order.php?act=" . $step_act . "&order_id=" . $order_id . "&step=goods\n");
        exit;
    }

    /* 取消刚添加或编辑的订单 */
    elseif ('cancel_order' == $func)
    {
        $step_act = $_GET['step_act'];
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        if ($step_act == 'add')
        {
            /* 如果是添加，删除订单，返回订单列表 */
            if ($order_id > 0)
            {
                $sql = "DELETE FROM " . $ecs->table('order_info') .
                        " WHERE order_id = '$order_id' LIMIT 1";
                $db->query($sql);
            }
            ecs_header("Location: order.php?act=list\n");
            exit;
        }
        else
        {
            /* 如果是编辑，返回订单信息 */
            ecs_header("Location: order.php?act=info&order_id=" . $order_id . "\n");
            exit;
        }
    }

    /* 编辑订单时由于订单已付款且金额减少而退款 */
    elseif ('refund' == $func)
    {
        /* 处理退款 */
        $order_id       = $_REQUEST['order_id'];
        $refund_type    = $_REQUEST['refund'];
        $refund_note    = $_REQUEST['refund_note'];
        $refund_amount  = $_REQUEST['refund_amount'];
        $order          = order_info($order_id);
        order_refund($order, $refund_type, $refund_note, $refund_amount);

        /* 修改应付款金额为0，已付款金额减少 $refund_amount */
        update_order($order_id, array('order_amount' => 0, 'money_paid' => $order['money_paid'] - $refund_amount));

        /* 返回订单详情 */
        ecs_header("Location: order.php?act=info&order_id=" . $order_id . "\n");
        exit;
    }

    /* 载入退款页面 */
    elseif ('load_refund' == $func)
    {
        $refund_amount = floatval($_REQUEST['refund_amount']);
        $smarty->assign('refund_amount', $refund_amount);
        $smarty->assign('formated_refund_amount', price_format($refund_amount));

        $anonymous = $_REQUEST['anonymous'];
        $smarty->assign('anonymous', $anonymous); // 是否匿名

        $order_id = intval($_REQUEST['order_id']);
        $smarty->assign('order_id', $order_id); // 订单id

        /* 显示模板 */
        $smarty->assign('ur_here', $_LANG['refund']);
        assign_query_info();
        $smarty->display('order_refund.htm');
    }

    else
    {
        die('invalid params');
    }
}

/*------------------------------------------------------ */
//-- 合并订单
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'merge')
{
    /* 检查权限 */
    admin_priv('order_os_edit');

    /* 取得满足条件的订单 */
    $sql = "SELECT o.order_sn, u.user_name " .
            "FROM " . $ecs->table('order_info') . " AS o " .
                "LEFT JOIN " . $ecs->table('users') . " AS u ON o.user_id = u.user_id " .
            "WHERE o.user_id > 0 " .
            "AND o.extension_code = '' " . order_query_sql('unprocessed');
    $smarty->assign('inventory_list', $db->getAll($sql));

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['04_merge_order']);
    $smarty->assign('action_link', array('href' => 'order.php?act=list', 'text' => $_LANG['02_inventory_list']));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('merge_order.htm');
}

/*------------------------------------------------------ */
//-- 订单打印模板（载入页面）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'templates')
{
    /* 检查权限 */
    admin_priv('order_os_edit');

    /* 读入订单打印模板文件 */
    $file_path    = ROOT_PATH.'data/order_print.html';
    $file_content = file_get_contents($file_path);
    @fclose($file_content);

    include_once(ROOT_PATH."includes/fckeditor/fckeditor.php");

    /* 编辑器 */
    $editor = new FCKeditor('FCKeditor1');
    $editor->BasePath   = "../includes/fckeditor/";
    $editor->ToolbarSet = "Normal";
    $editor->Width      = "95%";
    $editor->Height     = "500";
    $editor->Value      = $file_content;

    $fckeditor = $editor->CreateHtml();
    $smarty->assign('fckeditor', $fckeditor);

    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['edit_order_templates']);
    $smarty->assign('action_link',  array('href' => 'order.php?act=list', 'text' => $_LANG['02_inventory_list']));
    $smarty->assign('act', 'edit_templates');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('order_templates.htm');
}
/*------------------------------------------------------ */
//-- 订单打印模板（提交修改）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_templates')
{
    /* 更新模板文件的内容 */
    $file_name = @fopen("../data/order_print.html", 'w+');
    @fwrite($file_name, stripslashes($_POST['FCKeditor1']));
    @fclose($file_name);

    /* 提示信息 */
    $link[] = array('text' => $_LANG['back_list'], 'href'=>'order.php?act=list');
    sys_msg($_LANG['edit_template_success'], 0, $link);
}


/*------------------------------------------------------ */
//-- 操作订单状态（载入页面）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'operate')
{
    /* 取得订单id（可能是多个，多个sn）和操作备注（可能没有） */
    $order_id       = $_REQUEST['order_id'];
    $batch          = isset($_REQUEST['batch']); // 是否批处理
    $action_note    = isset($_REQUEST['action_note']) ? trim($_REQUEST['action_note']) : '';

    /* 确认 */
    if (isset($_POST['confirm']))
    {
        $require_note   = false;
        $action         = $_LANG['op_confirm'];
        $operation      = 'confirm';
    }
    /* 付款 */
    elseif (isset($_POST['pay']))
    {
        $require_note   = $_CFG['order_pay_note'] == 1;
        $action         = $_LANG['op_pay'];
        $operation      = 'pay';
    }
    /* 未付款 */
    elseif (isset($_POST['unpay']))
    {
        $require_note   = $_CFG['order_unpay_note'] == 1;
        $order          = order_info($order_id);
        if ($order['money_paid'] > 0)
        {
            $show_refund = true;
        }
        $anonymous      = $order['user_id'] == 0;
        $action         = $_LANG['op_unpay'];
        $operation      = 'unpay';
    }
    /* 配货 */
    elseif (isset($_POST['prepare']))
    {
        $require_note   = false;
        $action         = $_LANG['op_prepare'];
        $operation      = 'prepare';
    }
    /* 发货 */
    elseif (isset($_POST['ship']))
    {
        $require_note   = $_CFG['order_ship_note'] == 1;
        $show_invoice_no= true;
        $action         = $_LANG['op_ship'];
        $operation      = 'ship';
    }
    /* 未发货 */
    elseif (isset($_POST['unship']))
    {
        $require_note   = $_CFG['order_unship_note'] == 1;
        $action         = $_LANG['op_unship'];
        $operation      = 'unship';
    }
    /* 收货确认 */
    elseif (isset($_POST['receive']))
    {
        $require_note   = $_CFG['order_receive_note'] == 1;
        $action         = $_LANG['op_receive'];
        $operation      = 'receive';
    }
    /* 取消 */
    elseif (isset($_POST['cancel']))
    {
        $require_note   = $_CFG['order_cancel_note'] == 1;
        $action         = $_LANG['op_cancel'];
        $operation      = 'cancel';
        $show_cancel_note   = true;
        $order          = order_info($order_id);
        if ($order['money_paid'] > 0)
        {
            $show_refund = true;
        }
        $anonymous      = $order['user_id'] == 0;
    }
    /* 无效 */
    elseif (isset($_POST['invalid']))
    {
        $require_note   = $_CFG['order_invalid_note'] == 1;
        $action         = $_LANG['op_invalid'];
        $operation      = 'invalid';
    }
    /* 售后 */
    elseif (isset($_POST['after_service']))
    {
        $require_note   = true;
        $action         = $_LANG['op_after_service'];
        $operation      = 'after_service';
    }
    /* 退货 */
    elseif (isset($_POST['return']))
    {
        $require_note   = $_CFG['order_return_note'] == 1;
        $order          = order_info($order_id);
        if ($order['money_paid'] > 0)
        {
            $show_refund = true;
        }
        $anonymous      = $order['user_id'] == 0;
        $action         = $_LANG['op_return'];
        $operation      = 'return';
    }
    /* 指派 */
    elseif (isset($_POST['assign']))
    {
        /* 取得参数 */
        $new_agency_id  = isset($_POST['agency_id']) ? intval($_POST['agency_id']) : 0;
        if ($new_agency_id == 0)
        {
            sys_msg($_LANG['js_languages']['pls_select_agency']);
        }

        /* 查询订单信息 */
        $order = order_info($order_id);

        /* 如果管理员属于某个办事处，检查该订单是否也属于这个办事处 */
        $sql = "SELECT agency_id FROM " . $ecs->table('admin_user') . " WHERE user_id = '$_SESSION[admin_id]'";
        $admin_agency_id = $db->getOne($sql);
        if ($admin_agency_id > 0)
        {
            if ($order['agency_id'] != $admin_agency_id)
            {
                sys_msg($_LANG['priv_error']);
            }
        }

        /* 修改订单所属的办事处 */
        if ($new_agency_id != $order['agency_id'])
        {
            $sql = "UPDATE " . $ecs->table('order_info') . " SET agency_id = '$new_agency_id' " .
                    "WHERE order_id = '$order_id' LIMIT 1";
            $db->query($sql);
        }

        /* 操作成功 */
        $links[] = array('href' => 'order.php?act=list&' . list_link_postfix(), 'text' => $_LANG['02_inventory_list']);
        sys_msg($_LANG['act_ok'], 0, $links);
    }
    /* 删除 */
    elseif (isset($_POST['remove']))
    {
        $require_note = false;
        $operation = 'remove';
        if (!$batch)
        {
            /* 检查能否操作 */
            $order = order_info($order_id);
            $operable_list = operable_list($order);
            if (!isset($operable_list['remove']))
            {
                die('Hacking attempt');
            }

            /* 删除订单 */
            $db->query("DELETE FROM ".$ecs->table('order_info'). " WHERE order_id = '$order_id'");
            $db->query("DELETE FROM ".$ecs->table('order_goods'). " WHERE order_id = '$order_id'");
            $db->query("DELETE FROM ".$ecs->table('order_action'). " WHERE order_id = '$order_id'");

            /* todo 记录日志 */
            admin_log($order['order_sn'], 'remove', 'order');

            /* 返回 */
            sys_msg($_LANG['order_removed'], 0, array(array('href'=>'order.php?act=list&' . list_link_postfix(), 'text' => $_LANG['return_list'])));
        }
    }
    /* 批量打印订单 */
    elseif (isset($_POST['print']))
    {
        if (empty($_POST['order_id']))
        {
            sys_msg($_LANG['pls_select_order']);
        }

        /* 赋值公用信息 */
        $smarty->assign('shop_name',    $_CFG['shop_name']);
        $smarty->assign('shop_url',     $ecs->url());
        $smarty->assign('shop_address', $_CFG['shop_address']);
        $smarty->assign('service_phone',$_CFG['service_phone']);
        $smarty->assign('print_time',   local_date($_CFG['time_format']));
        $smarty->assign('action_user',  $_SESSION['admin_name']);

        $html = '';
        $order_sn_list = explode(',', $_POST['order_id']);
        foreach ($order_sn_list as $order_sn)
        {
            /* 取得订单信息 */
            $order = order_info(0, $order_sn);
            if (empty($order))
            {
                continue;
            }

            /* 根据订单是否完成检查权限 */
            if (order_finished($order))
            {
                if (!admin_priv('order_view_finished', '', false))
                {
                    continue;
                }
            }
            else
            {
                if (!admin_priv('order_view', '', false))
                {
                    continue;
                }
            }

            /* 如果管理员属于某个办事处，检查该订单是否也属于这个办事处 */
            $sql = "SELECT agency_id FROM " . $ecs->table('admin_user') . " WHERE user_id = '$_SESSION[admin_id]'";
            $agency_id = $db->getOne($sql);
            if ($agency_id > 0)
            {
                if ($order['agency_id'] != $agency_id)
                {
                    continue;
                }
            }

            /* 取得用户名 */
            if ($order['user_id'] > 0)
            {
                $user = user_info($order['user_id']);
                if (!empty($user))
                {
                    $order['user_name'] = $user['user_name'];
                }
            }

            /* 取得区域名 */
            $sql = "SELECT concat(IFNULL(c.region_name, ''), '  ', IFNULL(p.region_name, ''), " .
                        "'  ', IFNULL(t.region_name, ''), '  ', IFNULL(d.region_name, '')) AS region " .
                    "FROM " . $ecs->table('order_info') . " AS o " .
                        "LEFT JOIN " . $ecs->table('region') . " AS c ON o.country = c.region_id " .
                        "LEFT JOIN " . $ecs->table('region') . " AS p ON o.province = p.region_id " .
                        "LEFT JOIN " . $ecs->table('region') . " AS t ON o.city = t.region_id " .
                        "LEFT JOIN " . $ecs->table('region') . " AS d ON o.district = d.region_id " .
                    "WHERE o.order_id = '$order[order_id]'";
            $order['region'] = $db->getOne($sql);

            /* 其他处理 */
            $order['order_time']    = local_date($_CFG['time_format'], $order['add_time']);
            $order['pay_time']      = $order['pay_time'] > 0 ?
                local_date($_CFG['time_format'], $order['pay_time']) : $_LANG['ps'][PS_UNPAYED];
            $order['shipping_time'] = $order['shipping_time'] > 0 ?
                local_date($_CFG['time_format'], $order['shipping_time']) : $_LANG['ss'][SS_UNSHIPPED];
            $order['status']        = $_LANG['os'][$order['order_status']] . ',' . $_LANG['ps'][$order['pay_status']] . ',' . $_LANG['ss'][$order['shipping_status']];
            $order['invoice_no']    = $order['shipping_status'] == SS_UNSHIPPED || $order['shipping_status'] == SS_PREPARING ? $_LANG['ss'][SS_UNSHIPPED] : $order['invoice_no'];

            /* 此订单的发货备注(此订单的最后一条操作记录) */
            $sql = "SELECT action_note FROM " . $ecs->table('order_action').
                   " WHERE order_id = '$order[order_id]' AND shipping_status = 1 ORDER BY log_time DESC";
            $order['invoice_note'] = $db->getOne($sql);

            /* 参数赋值：订单 */
            $smarty->assign('order', $order);

            /* 取得订单商品 */
            $goods_list = array();
            $goods_attr = array();
            $sql = "SELECT o.*, g.goods_number AS storage, o.goods_attr, IFNULL(b.brand_name, '') AS brand_name " .
                    "FROM " . $ecs->table('order_goods') . " AS o ".
                    "LEFT JOIN " . $ecs->table('goods') . " AS g ON o.goods_id = g.goods_id " .
                    "LEFT JOIN " . $ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
                    "WHERE o.order_id = '$order[order_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                /* 虚拟商品支持 */
                if ($row['is_real'] == 0)
                {
                    /* 取得语言项 */
                    $filename = ROOT_PATH . 'plugins/' . $row['extension_code'] . '/languages/common_' . $_CFG['lang'] . '.php';
                    if (file_exists($filename))
                    {
                        include_once($filename);
                        if (!empty($_LANG[$row['extension_code'].'_link']))
                        {
                            $row['goods_name'] = $row['goods_name'] . sprintf($_LANG[$row['extension_code'].'_link'], $row['goods_id'], $order['order_sn']);
                        }
                    }
                }

                $row['formated_subtotal']       = price_format($row['goods_price'] * $row['goods_number']);
                $row['formated_goods_price']    = price_format($row['goods_price']);

                $goods_attr[] = explode(' ', trim($row['goods_attr'])); //将商品属性拆分为一个数组
                $goods_list[] = $row;
            }

            $attr = array();
            $arr  = array();
            foreach ($goods_attr AS $index => $array_val)
            {
                foreach ($array_val AS $value)
                {
                    $arr = explode(':', $value);//以 : 号将属性拆开
                    $attr[$index][] =  @array('name' => $arr[0], 'value' => $arr[1]);
                }
            }

            $smarty->assign('goods_attr', $attr);
            $smarty->assign('goods_list', $goods_list);

            $smarty->template_dir = '../data';
            $html .= $smarty->fetch('order_print.html') .
                '<div style="PAGE-BREAK-AFTER:always"></div>';
        }

        echo $html;
        exit;
    }

    /* 直接处理还是跳到详细页面 */
    if (($require_note && $action_note == '') || isset($show_invoice_no) || isset($show_refund))
    {
        /* 模板赋值 */
        $smarty->assign('require_note', $require_note); // 是否要求填写备注
        $smarty->assign('action_note', $action_note);   // 备注
        $smarty->assign('show_cancel_note', isset($show_cancel_note)); // 是否显示取消原因
        $smarty->assign('show_invoice_no', isset($show_invoice_no)); // 是否显示发货单号
        $smarty->assign('show_refund', isset($show_refund)); // 是否显示退款
        $smarty->assign('anonymous', isset($anonymous) ? $anonymous : true); // 是否匿名
        $smarty->assign('order_id', $order_id); // 订单id
        $smarty->assign('batch', $batch);   // 是否批处理
        $smarty->assign('operation', $operation); // 操作

        /* 显示模板 */
        $smarty->assign('ur_here', $_LANG['order_operate'] . $action);
        assign_query_info();
        $smarty->display('order_operate.htm');
    }
    else
    {
        /* 直接处理 */
        if (!$batch)
        {
            /* 一个订单 */
            ecs_header("Location: order.php?act=operate_post&order_id=" . $order_id .
                    "&operation=" . $operation . "&action_note=" . urlencode($action_note) . "\n");
            exit;
        }
        else
        {
            /* 多个订单 */
            ecs_header("Location: order.php?act=batch_operate_post&order_id=" . $order_id .
                    "&operation=" . $operation . "&action_note=" . urlencode($action_note) . "\n");
            exit;
        }
    }
}

/*------------------------------------------------------ */
//-- 操作订单状态（处理批量提交）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch_operate_post')
{
    /* 取得参数 */
    $order_id   = $_REQUEST['order_id'];        // 订单id（逗号格开的多个订单id）
    $operation  = $_REQUEST['operation'];       // 订单操作
    $action_note= $_REQUEST['action_note'];     // 操作备注

    $order_id_list = explode(',', $order_id);

    /* 初始化处理的订单sn */
    $sn_list = array();
    $sn_not_list = array();

    /* 确认 */
    if ('confirm' == $operation)
    {
        foreach($order_id_list as $id_order)
        {
            $sql = "SELECT * FROM " . $ecs->table('order_info') .
                " WHERE order_sn = '$id_order'" .
                " AND order_status = '" . OS_UNCONFIRMED . "'";
            $order = $db->getRow($sql);

            if($order)
            {
                 /* 检查能否操作 */
                $operable_list = operable_list($order);
                if (!isset($operable_list[$operation]))
                {
                    $sn_not_list[] = $id_order;
                    continue;
                }

                $order_id = $order['order_id'];

                /* 标记订单为已确认 */
                update_order($order_id, array('order_status' => OS_CONFIRMED, 'confirm_time' => gmtime()));
                update_order_amount($order_id);

                /* 记录log */
                order_action($order['order_sn'], OS_CONFIRMED, SS_UNSHIPPED, PS_UNPAYED, $action_note);

                /* 发送邮件 */
                if ($_CFG['send_confirm_email'] == '1')
                {
                    $tpl = get_mail_template('order_confirm');
                    $order['formated_add_time'] = local_date($GLOBALS['_CFG']['time_format'], $order['add_time']);
                    $smarty->assign('order', $order);
                    $smarty->assign('shop_name', $_CFG['shop_name']);
                    $smarty->assign('send_date', local_date($_CFG['date_format']));
                    $smarty->assign('sent_date', local_date($_CFG['date_format']));
                    $content = $smarty->fetch('str:' . $tpl['template_content']);
                    send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']);
                }

                $sn_list[] = $order['order_sn'];
            }
            else
            {
                $sn_not_list[] = $id_order;
            }
        }

        $sn_str = $_LANG['confirm_order'];
    }
    /* 无效 */
    elseif ('invalid' == $operation)
    {
        foreach($order_id_list as $id_order)
        {
            $sql = "SELECT * FROM " . $ecs->table('order_info') .
                " WHERE order_sn = $id_order" . order_query_sql('unpay_unship');

            $order = $db->getRow($sql);

            if($order)
            {
                 /* 检查能否操作 */
                $operable_list = operable_list($order);
                if (!isset($operable_list[$operation]))
                {
                    $sn_not_list[] = $id_order;
                    continue;
                }

                $order_id = $order['order_id'];

                /* 标记订单为“无效” */
                update_order($order_id, array('order_status' => OS_INVALID));

                /* 记录log */
                order_action($order['order_sn'], OS_INVALID, SS_UNSHIPPED, PS_UNPAYED, $action_note);

                /* 如果使用库存，且下订单时减库存，则增加库存 */
                if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
                {
                    change_order_goods_storage($order_id, false);
                }

                /* 发送邮件 */
                if ($_CFG['send_invalid_email'] == '1')
                {
                    $tpl = get_mail_template('order_invalid');
                    $smarty->assign('order', $order);
                    $smarty->assign('shop_name', $_CFG['shop_name']);
                    $smarty->assign('send_date', local_date($_CFG['date_format']));
                    $smarty->assign('sent_date', local_date($_CFG['date_format']));
                    $content = $smarty->fetch('str:' . $tpl['template_content']);
                    send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']);
                }

                /* 退还用户余额、积分、红包 */
                return_user_surplus_integral_bonus($order);

                $sn_list[] = $order['order_sn'];
            }
            else
            {
                $sn_not_list[] = $id_order;
            }
        }

        $sn_str = $_LANG['invalid_order'];
    }
    elseif ('cancel' == $operation)
    {
        foreach($order_id_list as $id_order)
        {
            $sql = "SELECT * FROM " . $ecs->table('order_info') .
                " WHERE order_sn = $id_order" . order_query_sql('unpay_unship');

            $order = $db->getRow($sql);
            if($order)
            {
                 /* 检查能否操作 */
                $operable_list = operable_list($order);
                if (!isset($operable_list[$operation]))
                {
                    $sn_not_list[] = $id_order;
                    continue;
                }

                $order_id = $order['order_id'];

                /* 标记订单为“取消”，记录取消原因 */
                $cancel_note = trim($_REQUEST['cancel_note']);
                update_order($order_id, array('order_status' => OS_CANCELED, 'to_buyer' => $cancel_note));

                /* 记录log */
                order_action($order['order_sn'], OS_CANCELED, $order['shipping_status'], PS_UNPAYED, $action_note);

                /* 如果使用库存，且下订单时减库存，则增加库存 */
                if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
                {
                    change_order_goods_storage($order_id, false);
                }

                /* 发送邮件 */
                if ($_CFG['send_cancel_email'] == '1')
                {
                    $tpl = get_mail_template('order_cancel');
                    $smarty->assign('order', $order);
                    $smarty->assign('shop_name', $_CFG['shop_name']);
                    $smarty->assign('send_date', local_date($_CFG['date_format']));
                    $smarty->assign('sent_date', local_date($_CFG['date_format']));
                    $content = $smarty->fetch('str:' . $tpl['template_content']);
                    send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']);
                }

                /* 退还用户余额、积分、红包 */
                return_user_surplus_integral_bonus($order);

                $sn_list[] = $order['order_sn'];
             }
            else
            {
                $sn_not_list[] = $id_order;
            }
        }

        $sn_str = $_LANG['cancel_order'];
    }
    elseif ('remove' == $operation)
    {
        foreach ($order_id_list as $id_order)
        {
            /* 检查能否操作 */
            $order = order_info('', $id_order);
            $operable_list = operable_list($order);
            if (!isset($operable_list['remove']))
            {
                $sn_not_list[] = $id_order;
                continue;
            }

            /* 删除订单 */
            $db->query("DELETE FROM ".$ecs->table('order_info'). " WHERE order_id = '$order[order_id]'");
            $db->query("DELETE FROM ".$ecs->table('order_goods'). " WHERE order_id = '$order[order_id]'");
            $db->query("DELETE FROM ".$ecs->table('order_action'). " WHERE order_id = '$order[order_id]'");

            /* todo 记录日志 */
            admin_log($order['order_sn'], 'remove', 'order');

            $sn_list[] = $order['order_sn'];
        }

        $sn_str = $_LANG['remove_order'];
    }
    else
    {
        die('invalid params');
    }

    /* 取得备注信息 */
//    $action_note = $_REQUEST['action_note'];

    if(empty($sn_not_list))
    {
        $sn_list = empty($sn_list) ? '' : $_LANG['updated_order'] . join($sn_list, ',');
        $msg = $sn_list;
        $links[] = array('text' => $_LANG['return_list'], 'href' => 'order.php?act=list&' . list_link_postfix());
        sys_msg($msg, 0, $links);
    }
    else
    {
        $inventory_list_no_fail = array();
        $sql = "SELECT * FROM " . $ecs->table('order_info') .
                " WHERE order_sn " . db_create_in($sn_not_list);
        $res = $db->query($sql);
        while($row = $db->fetchRow($res))
        {
            $inventory_list_no_fail[$row['order_id']]['order_id'] = $row['order_id'];
            $inventory_list_no_fail[$row['order_id']]['order_sn'] = $row['order_sn'];
            $inventory_list_no_fail[$row['order_id']]['order_status'] = $row['order_status'];
            $inventory_list_no_fail[$row['order_id']]['shipping_status'] = $row['shipping_status'];
            $inventory_list_no_fail[$row['order_id']]['pay_status'] = $row['pay_status'];

            $inventory_list_fail = '';
            foreach(operable_list($row) as $key => $value)
            {
                if($key != $operation)
                {
                    $inventory_list_fail .= $_LANG['op_' . $key] . ',';
                }
            }
            $inventory_list_no_fail[$row['order_id']]['operable'] = $inventory_list_fail;
        }

        /* 模板赋值 */
        $smarty->assign('order_info', $sn_str);
        $smarty->assign('action_link', array('href' => 'order.php?act=list', 'text' => $_LANG['02_inventory_list']));
        $smarty->assign('inventory_list',   $inventory_list_no_fail);

        /* 显示模板 */
        assign_query_info();
        $smarty->display('order_operate_info.htm');
    }
}

/*------------------------------------------------------ */
//-- 操作订单状态（处理提交）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'operate_post')
{
    /* 取得参数 */
    $order_id   = $_REQUEST['order_id'];        // 订单id
    $operation  = $_REQUEST['operation'];       // 订单操作

    /* 查询订单信息 */
    $order = order_info($order_id);

    /* 检查能否操作 */
    $operable_list = operable_list($order);
    if (!isset($operable_list[$operation]))
    {
        die('Hacking attempt');
    }

    /* 取得备注信息 */
    $action_note = $_REQUEST['action_note'];

    /* 初始化提示信息 */
    $msg = '';

    /* 确认 */
    if ('confirm' == $operation)
    {
        /* 标记订单为已确认 */
        update_order($order_id, array('order_status' => OS_CONFIRMED, 'confirm_time' => gmtime()));
        update_order_amount($order_id);

        /* 记录log */
        order_action($order['order_sn'], OS_CONFIRMED, SS_UNSHIPPED, PS_UNPAYED, $action_note);

        /* 如果原来状态不是“未确认”，且使用库存，且下订单时减库存，则减少库存 */
        if ($order['order_status'] != OS_UNCONFIRMED && $_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
        {
            change_order_goods_storage($order_id);
        }

        /* 发送邮件 */
        $cfg = $_CFG['send_confirm_email'];
        if ($cfg == '1')
        {
            $tpl = get_mail_template('order_confirm');
            $smarty->assign('order', $order);
            $smarty->assign('shop_name', $_CFG['shop_name']);
            $smarty->assign('send_date', local_date($_CFG['date_format']));
            $smarty->assign('sent_date', local_date($_CFG['date_format']));
            $content = $smarty->fetch('str:' . $tpl['template_content']);
            if (!send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']))
            {
                $msg = $_LANG['send_mail_fail'];
            }
        }
    }
    /* 付款 */
    elseif ('pay' == $operation)
    {
        /* 标记订单为已确认、已付款，更新付款时间和已支付金额，如果是货到付款，同时修改订单为“收货确认” */
        if ($order['order_status'] != OS_CONFIRMED)
        {
            $arr['order_status']    = OS_CONFIRMED;
            $arr['confirm_time']    = gmtime();
        }
        $arr['pay_status']  = PS_PAYED;
        $arr['pay_time']    = gmtime();
        $arr['money_paid']  = $order['money_paid'] + $order['order_amount'];
        $arr['order_amount']= 0;
        $payment = payment_info($order['pay_id']);
        if ($payment['is_cod'])
        {
            $arr['shipping_status'] = SS_RECEIVED;
            $order['shipping_status'] = SS_RECEIVED;
        }
        update_order($order_id, $arr);

        /* 记录log */
        order_action($order['order_sn'], OS_CONFIRMED, $order['shipping_status'], PS_PAYED, $action_note);
    }
    /* 设为未付款 */
    elseif ('unpay' == $operation)
    {
        /* 标记订单为未付款，更新付款时间和已付款金额 */
        $arr = array(
            'pay_status'    => PS_UNPAYED,
            'pay_time'      => 0,
            'money_paid'    => 0,
            'order_amount'  => $order['money_paid']
        );
        update_order($order_id, $arr);

        /* todo 处理退款 */
        $refund_type = @$_REQUEST['refund'];
        $refund_note = @$_REQUEST['refund_note'];
        order_refund($order, $refund_type, $refund_note);

        /* 记录log */
        order_action($order['order_sn'], OS_CONFIRMED, SS_UNSHIPPED, PS_UNPAYED, $action_note);
    }
    /* 配货 */
    elseif ('prepare' == $operation)
    {
        /* 标记订单为已确认，配货中 */
        if ($order['order_status'] != OS_CONFIRMED)
        {
            $arr['order_status']    = OS_CONFIRMED;
            $arr['confirm_time']    = gmtime();
        }
        $arr['shipping_status']     = SS_PREPARING;
        update_order($order_id, $arr);

        /* 记录log */
        order_action($order['order_sn'], OS_CONFIRMED, SS_PREPARING, $order['pay_status'], $action_note);

        /* 清除缓存 */
        clear_cache_files();
    }
    /* 发货 */
    elseif ('ship' == $operation)
    {
        /* 取得发货单号 */
        $invoice_no = $_REQUEST['invoice_no'];

        /* 对虚拟商品的支持 */
        $virtual_goods = get_virtual_goods($order_id);
        if (!empty($virtual_goods))
        {
            if (!virtual_goods_ship($virtual_goods, $msg, $order['order_sn']))
            {
                sys_msg($msg);
            }
        }

        /* 标记订单为已确认 “已发货” */
        /* 更新发货时间和发货单号 */
        $shipping_status = SS_SHIPPED;
        if ($order['order_status'] != OS_CONFIRMED)
        {
            $arr['order_status']    = OS_CONFIRMED;
            $arr['confirm_time']    = gmtime();
        }
        $arr['shipping_status']     = $shipping_status;
        $arr['shipping_time']       = gmtime();
        $arr['invoice_no']          = $invoice_no;
        update_order($order_id, $arr);
        $order['invoice_no'] = $invoice_no;

        /* 记录log */
        order_action($order['order_sn'], OS_CONFIRMED, $shipping_status, $order['pay_status'], $action_note);

        /* 如果订单用户不为空，计算积分，并发给用户；发红包 */
        if ($order['user_id'] > 0)
        {
            /* 取得用户信息 */
            $user = user_info($order['user_id']);

            /* 计算并发放积分 */
            $integral = integral_to_give($order);

            log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($_LANG['order_gift_integral'], $order['order_sn']));

            /* 发放红包 */
            send_order_bonus($order_id);
        }

        /* 如果使用库存，且发货时减库存，则修改库存 */
        if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_SHIP)
        {
            change_order_goods_storage($order['order_id']);
        }

        /* 发送邮件 */
        $cfg = $_CFG['send_ship_email'];
        if ($cfg == '1')
        {
            $tpl = get_mail_template('deliver_notice');
            $smarty->assign('order', $order);
            $smarty->assign('send_time', local_date($_CFG['time_format']));
            $smarty->assign('shop_name', $_CFG['shop_name']);
            $smarty->assign('send_date', local_date($_CFG['date_format']));
            $smarty->assign('sent_date', local_date($_CFG['date_format']));
            $smarty->assign('confirm_url', $ecs->url() . 'receive.php?id=' . $order['order_id'] . '&con=' . rawurlencode($order['consignee']));
            $smarty->assign('send_msg_url',$ecs->url() . 'user.php?message_list&order_id=' . $order['order_id']);
            $content = $smarty->fetch('str:' . $tpl['template_content']);
            if (!send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']))
            {
                $msg = $_LANG['send_mail_fail'];
            }
        }

        /* 如果需要，发短信 */
        if ($GLOBALS['_CFG']['sms_order_shipped'] == '1' && $order['mobile'] != '')
        {
            include_once('../includes/cls_sms.php');
            $sms = new sms();
            $sms->send($order['mobile'], sprintf($GLOBALS['_LANG']['order_shipped_sms'], $order['order_sn'],
                local_date($GLOBALS['_LANG']['sms_time_format']), $GLOBALS['_CFG']['shop_name']), 0);
        }

        /* 清除缓存 */
        clear_cache_files();
    }
    /* 设为未发货 */
    elseif ('unship' == $operation)
    {
        /* 标记订单为“未发货”，更新发货时间 */
        update_order($order_id, array('shipping_status' => SS_UNSHIPPED, 'shipping_time' => 0));

        /* 记录log */
        order_action($order['order_sn'], $order['order_status'], SS_UNSHIPPED, $order['pay_status'], $action_note);

        /* 如果订单用户不为空，计算积分，并退回 */
        if ($order['user_id'] > 0)
        {
            /* 取得用户信息 */
            $user = user_info($order['user_id']);

            /* 计算并退回积分 */
            $integral = integral_to_give($order);
            log_account_change($order['user_id'], 0, 0, (-1) * intval($integral['rank_points']), (-1) * intval($integral['custom_points']), sprintf($_LANG['return_order_gift_integral'], $order['order_sn']));

            /* todo 计算并退回红包 */
            return_order_bonus($order_id);
        }

        /* 如果使用库存，则增加库存 */
        if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_SHIP)
        {
            change_order_goods_storage($order['order_id'], false);
        }

        /* 清除缓存 */
        clear_cache_files();
    }
    /* 收货确认 */
    elseif ('receive' == $operation)
    {
        /* 标记订单为“收货确认”，如果是货到付款，同时修改订单为已付款 */
        $arr = array('shipping_status' => SS_RECEIVED);
        $payment = payment_info($order['pay_id']);
        if ($payment['is_cod'])
        {
            $arr['pay_status'] = PS_PAYED;
            $order['pay_status'] = PS_PAYED;
        }
        update_order($order_id, $arr);

        /* 记录log */
        order_action($order['order_sn'], $order['order_status'], SS_RECEIVED, $order['pay_status'], $action_note);
    }
    /* 取消 */
    elseif ('cancel' == $operation)
    {
        /* 标记订单为“取消”，记录取消原因 */
        $cancel_note = isset($_REQUEST['cancel_note']) ? trim($_REQUEST['cancel_note']) : '';
        $arr = array(
            'order_status'  => OS_CANCELED,
            'to_buyer'      => $cancel_note,
            'pay_status'    => PS_UNPAYED,
            'pay_time'      => 0,
            'money_paid'    => 0,
            'order_amount'  => $order['money_paid']
        );
        update_order($order_id, $arr);

        /* todo 处理退款 */
        if ($order['money_paid'] > 0)
        {
            $refund_type = $_REQUEST['refund'];
            $refund_note = $_REQUEST['refund_note'];
            order_refund($order, $refund_type, $refund_note);
        }

        /* 记录log */
        order_action($order['order_sn'], OS_CANCELED, $order['shipping_status'], PS_UNPAYED, $action_note);

        /* 如果使用库存，且下订单时减库存，则增加库存 */
        if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
        {
            change_order_goods_storage($order_id, false);
        }

        /* 退还用户余额、积分、红包 */
        return_user_surplus_integral_bonus($order);

        /* 发送邮件 */
        $cfg = $_CFG['send_cancel_email'];
        if ($cfg == '1')
        {
            $tpl = get_mail_template('order_cancel');
            $smarty->assign('order', $order);
            $smarty->assign('shop_name', $_CFG['shop_name']);
            $smarty->assign('send_date', local_date($_CFG['date_format']));
            $smarty->assign('sent_date', local_date($_CFG['date_format']));
            $content = $smarty->fetch('str:' . $tpl['template_content']);
            if (!send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']))
            {
                $msg = $_LANG['send_mail_fail'];
            }
        }
    }
    /* 设为无效 */
    elseif ('invalid' == $operation)
    {
        /* 标记订单为“无效”、“未付款” */
        update_order($order_id, array('order_status' => OS_INVALID));

        /* 记录log */
        order_action($order['order_sn'], OS_INVALID, $order['shipping_status'], PS_UNPAYED, $action_note);

        /* 如果使用库存，且下订单时减库存，则增加库存 */
        if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
        {
            change_order_goods_storage($order_id, false);
        }

        /* 发送邮件 */
        $cfg = $_CFG['send_invalid_email'];
        if ($cfg == '1')
        {
            $tpl = get_mail_template('order_invalid');
            $smarty->assign('order', $order);
            $smarty->assign('shop_name', $_CFG['shop_name']);
            $smarty->assign('send_date', local_date($_CFG['date_format']));
            $smarty->assign('sent_date', local_date($_CFG['date_format']));
            $content = $smarty->fetch('str:' . $tpl['template_content']);
            if (!send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']))
            {
                $msg = $_LANG['send_mail_fail'];
            }
        }

        /* 退货用户余额、积分、红包 */
        return_user_surplus_integral_bonus($order);
    }
    /* 退货 */
    elseif ('return' == $operation)
    {
        /* 标记订单为“退货”、“未付款”、“未发货” */
        $arr = array('order_status'     => OS_RETURNED,
                     'pay_status'       => PS_UNPAYED,
                     'shipping_status'  => SS_UNSHIPPED,
                     'money_paid'       => 0,
                     'order_amount'     => $order['money_paid']);
        update_order($order_id, $arr);

        /* todo 处理退款 */
        if ($order['pay_status'] != PS_UNPAYED)
        {
            $refund_type = $_REQUEST['refund'];
            $refund_note = $_REQUEST['refund_note'];
            order_refund($order, $refund_type, $refund_note);
        }

        /* 记录log */
        order_action($order['order_sn'], OS_RETURNED, SS_UNSHIPPED, PS_UNPAYED, $action_note);

        /* 如果订单用户不为空，计算积分，并退回 */
        if ($order['user_id'] > 0)
        {
            /* 取得用户信息 */
            $user = user_info($order['user_id']);

            /* 计算并退回积分 */
            $integral = integral_to_give($order);
            log_account_change($order['user_id'], 0, 0, (-1) * intval($integral['rank_points']), (-1) * intval($integral['custom_points']), sprintf($_LANG['return_order_gift_integral'], $order['order_sn']));

            /* todo 计算并退回红包 */
            return_order_bonus($order_id);
        }

        /* 如果使用库存，则增加库存（不论何时减库存都需要） */
        if ($_CFG['use_storage'] == '1')
        {
            change_order_goods_storage($order['order_id'], false);
        }

        /* 退货用户余额、积分、红包 */
        return_user_surplus_integral_bonus($order);

        /* 清除缓存 */
        clear_cache_files();
    }
    elseif ('after_service' == $operation)
    {
        /* 记录log */
        order_action($order['order_sn'], $order['order_status'], $order['shipping_status'], $order['pay_status'], '[' . $_LANG['op_after_service'] . '] ' . $action_note);
    }
    else
    {
        die('invalid params');
    }

    /* 操作成功 */
    $links[] = array('text' => $_LANG['order_info'], 'href' => 'order.php?act=info&order_id=' . $order_id);
    sys_msg($_LANG['act_ok'] . $msg, 0, $links);
}

elseif ($_REQUEST['act'] == 'json')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $func = $_REQUEST['func'];
    if ($func == 'get_goods_info')
    {
        /* 取得商品信息 */
        $goods_id = $_REQUEST['goods_id'];
        $sql = "SELECT goods_id, c.cat_name, goods_sn, goods_name, b.brand_name, " .
                "goods_number, market_price, shop_price, promote_price, " .
                "promote_start_date, promote_end_date, goods_brief, goods_type, is_promote " .
                "FROM " . $ecs->table('goods') . " AS g " .
                "LEFT JOIN " . $ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
                "LEFT JOIN " . $ecs->table($GLOBALS['year']."_".'category') . " AS c ON g.cat_id = c.cat_id " .
                " WHERE goods_id = '$goods_id'";
        $goods = $db->getRow($sql);
        $today = gmtime();
        $goods['goods_price'] = ($goods['is_promote'] == 1 &&
            $goods['promote_start_date'] <= $today && $goods['promote_end_date'] >= $today) ?
            $goods['promote_price'] : $goods['shop_price'];

        /* 取得会员价格 */
        $sql = "SELECT p.user_price, r.rank_name " .
                "FROM " . $ecs->table('member_price') . " AS p, " .
                    $ecs->table('user_rank') . " AS r " .
                "WHERE p.user_rank = r.rank_id " .
                "AND p.goods_id = '$goods_id' ";
        $goods['user_price'] = $db->getAll($sql);

        /* 取得商品属性 */
        $sql = "SELECT a.attr_id, a.attr_name, g.goods_attr_id, g.attr_value, g.attr_price " .
                "FROM " . $ecs->table('goods_attr') . " AS g, " .
                    $ecs->table('attribute') . " AS a " .
                "WHERE g.attr_id = a.attr_id " .
                "AND g.goods_id = '$goods_id' ";
        $goods['attr_list'] = array();
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res))
        {
            $goods['attr_list'][$row['attr_id']][] = $row;
        }
        $goods['attr_list'] = array_values($goods['attr_list']);

        echo $json->encode($goods);
    }
}

/*------------------------------------------------------ */
//-- 合并订单
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'ajax_merge_order')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $from_order_sn = empty($_POST['from_order_sn']) ? '' : json_str_iconv(substr($_POST['from_order_sn'], 1));
    $to_order_sn = empty($_POST['to_order_sn']) ? '' : json_str_iconv(substr($_POST['to_order_sn'], 1));

    $m_result = merge_order($from_order_sn, $to_order_sn);
    $result = array('error'=>0,  'content'=>'');
    if ($m_result === true)
    {
        $result['message'] = $GLOBALS['_LANG']['act_ok'];
    }
    else
    {
        $result['error'] = 1;
        $result['message'] = $m_result;
    }
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 删除订单
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_order')
{
    $order_id = intval($_REQUEST['id']);

    /* 检查权限 */
    check_authz_json('order_edit');

    /* 检查订单是否允许删除操作 */
    $order = order_info($order_id);
    $operable_list = operable_list($order);
    if (!isset($operable_list['remove']))
    {
        make_json_error('Hacking attempt');
        exit;
    }

    $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('order_info'). " WHERE order_id = '$order_id'");
    $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('order_goods'). " WHERE order_id = '$order_id'");
    $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('order_action'). " WHERE order_id = '$order_id'");

    if ($GLOBALS['db'] ->errno() == 0)
    {
        $url = 'order.php?act=query&' . str_replace('act=remove_order', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error($GLOBALS['db']->errorMsg());
    }
}

/*------------------------------------------------------ */
//-- 根据关键字和id搜索用户
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'search_users')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $id_name = empty($_GET['id_name']) ? '' : json_str_iconv(trim($_GET['id_name']));

    $result = array('error'=>0, 'message'=>'', 'content'=>'');
    if ($id_name != '')
    {
        $sql = "SELECT user_id, user_name FROM " . $GLOBALS['ecs']->table('users') .
                " WHERE user_id LIKE '%" . mysql_like_quote($id_name) . "%'" .
                " OR user_name LIKE '%" . mysql_like_quote($id_name) . "%'" .
                " LIMIT 20";
        $res = $GLOBALS['db']->query($sql);

         $result['userlist'] = array();
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
             $result['userlist'][] = array('user_id' => $row['user_id'], 'user_name' => $row['user_name']);
        }
    }
    else
    {
        $result['error'] = 1;
        $result['message'] = 'NO KEYWORDS!';
    }

    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 根据关键字搜索商品
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'search_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $keyword = empty($_GET['keyword']) ? '' : json_str_iconv(trim($_GET['keyword']));

    $result = array('error'=>0, 'message'=>'', 'content'=>'');

    if ($keyword != '')
    {
        $sql = "SELECT goods_id, goods_name, goods_sn FROM " . $GLOBALS['ecs']->table('goods') .
                " WHERE is_delete = 0" .
                " AND is_on_sale = 1" .
                " AND is_alone_sale = 1" .
                " AND (goods_id LIKE '%" . mysql_like_quote($keyword) . "%'" .
                " OR goods_name LIKE '%" . mysql_like_quote($keyword) . "%'" .
                " OR goods_sn LIKE '%" . mysql_like_quote($keyword) . "%')" .
                " LIMIT 20";
        $res = $GLOBALS['db']->query($sql);

        $result['goodslist'] = array();
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $result['goodslist'][] = array('goods_id' => $row['goods_id'], 'name' => $row['goods_id'] . '  ' . $row['goods_name'] . '  ' . $row['goods_sn']);
        }
    }
    else
    {
        $result['error'] = 1;
        $result['message'] = 'NO KEYWORDS';
    }
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 编辑收货单号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_invoice_no')
{
    /* 检查权限 */
    check_authz_json('order_edit');

    $no = empty($_POST['val']) ? 'N/A' : json_str_iconv(trim($_POST['val']));
    $no = $no=='N/A' ? '' : $no;
    $order_id = empty($_POST['id']) ? 0 : intval($_POST['id']);

    if ($order_id == 0)
    {
        make_json_error('NO ORDER ID');
        exit;
    }

    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') . " SET invoice_no='$no' WHERE order_id = '$order_id'";
    if ($GLOBALS['db']->query($sql))
    {
        if (empty($no))
        {
            make_json_result('N/A');
        }
        else
        {
            make_json_result(stripcslashes($no));
        }
    }
    else
    {
        make_json_error($GLOBALS['db']->errorMsg());
    }
}

/*------------------------------------------------------ */
//-- 编辑付款备注
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_pay_note')
{
    /* 检查权限 */
    check_authz_json('order_edit');

    $no = empty($_POST['val']) ? 'N/A' : json_str_iconv(trim($_POST['val']));
    $no = $no=='N/A' ? '' : $no;
    $order_id = empty($_POST['id']) ? 0 : intval($_POST['id']);

    if ($order_id == 0)
    {
        make_json_error('NO ORDER ID');
        exit;
    }

    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') . " SET pay_note='$no' WHERE order_id = '$order_id'";
    if ($GLOBALS['db']->query($sql))
    {
        if (empty($no))
        {
            make_json_result('N/A');
        }
        else
        {
            make_json_result(stripcslashes($no));
        }
    }
    else
    {
        make_json_error($GLOBALS['db']->errorMsg());
    }
}

?>