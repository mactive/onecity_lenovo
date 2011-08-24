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
    admin_priv('inspect_part_number');

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
    $smarty->assign('sql',   		$goods_list['sql']);
    $smarty->assign('full_page',    1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('inspect_part_number.htm');
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
//-- 状态库存统计表
/*------------------------------------------------------ */

elseif($_REQUEST['act']=='status_accounting')
{
    /* 检查权限 */
    admin_priv('inspect_part_number');

    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);

    /* 模板赋值 */
    $ur_here = $_LANG['status_accounting_list'];
    $smarty->assign('ur_here', $ur_here);

   	$action_link = array('href' => 'inventory.php?act=list', 'text' => $_LANG['inventory_list']);
    $smarty->assign('action_link',  $action_link);
    $smarty->assign('cat_list',     cat_list(0, $cat_id));
    $smarty->assign('brand_list',   get_brand_list());
	$smarty->assign('status_list',   get_status_list());
    $smarty->assign('intro_list',   get_intro_list());
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

	$smarty->assign('status_list',get_status_list('fullinv'));   // 订单状态
    
    $goods_list = status_accounting_list(0, 1);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('sql',   		$goods_list['sql']);
    $smarty->assign('full_page',    1);

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('status_accounting.htm');
}

/*------------------------------------------------------ */
//-- 商品核对排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'status_accounting_query')
{
    $goods_list = status_accounting_list(0, 1);
	$smarty->assign('status_list',get_status_list('fullinv'));   // 订单状态

    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('status_accounting.htm'), '', array('filter' => $goods_list['filter'], 'page_count' => $goods_list['page_count']));
}

/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('inventory_list');
	
	$part_number = empty($_REQUEST['part_number']) ? '' : trim($_REQUEST['part_number']);
	$serial_number = empty($_REQUEST['serial_number']) ? '' : trim($_REQUEST['serial_number']);
    $smarty->assign('part_number',     $part_number);
    $smarty->assign('serial_number',   $serial_number);
    

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['inventory_list']);
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
	$smarty->assign('sql',   $inventory_list['sql']);
    

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
    $smarty->assign('sql',   $inventory_list['sql']);
    $sort_flag  = sort_flag($inventory_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('inventory_list.htm'), '', array('filter' => $inventory_list['filter'], 'page_count' => $inventory_list['page_count']));
}


/*------------------------------------------------------ */
//-- 修改商品 part_number
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_part_number')
{
	admin_priv('inspect_part_number');
    
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
//-- 修改商品 shop_pirce
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_shop_price')
{
	admin_priv('inspect_part_number');
    
    $goods_id = intval($_POST['id']);
    $shop_price = json_str_iconv(trim($_POST['val']));


    if ($exc->edit("shop_price = '$shop_price', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($shop_price));

    }
}

/*------------------------------------------------------ */
//-- 修改商品库存数量
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_number')
{
    check_authz_json('goods_manage');

    $goods_id   = intval($_POST['id']);
    $goods_num  = $_POST['val'];

    if($goods_num < 0 || $goods_num == 0 && $_POST['val'] != "$goods_num")
    {
        make_json_error($_LANG['goods_number_error']);
    }

    if ($exc->edit("goods_number = '$goods_num', last_update=" .gmtime(), $goods_id))
    {
		if($goods_num > 0 ){
			$exc->edit("goods_status = 0", $goods_id);
		}else{
			$exc->edit("goods_status = 1", $goods_id);
		}
		
        clear_cache_files();
        make_json_result($goods_num);
    }
}

/*------------------------------------------------------ */
//-- 增加批量入库列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_serial_list')
{

    $serial_count   = empty($_GET['serial_count']) ? 0 : intval($_GET['serial_count']);

    $content    = build_serial_html($serial_count);

    make_json_result($content);
}

/*------------------------------------------------------ */
//-- 增加批量修改列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_bacth_list')
{

    $batch_count   = empty($_GET['batch_count']) ? 0 : intval($_GET['batch_count']);

    $content    = build_batch_html($batch_count);

    make_json_result($content);
}

/*------------------------------------------------------ */
//-- 增加批量修改列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'validate_part_and_serial')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
	$filters = $json->decode($_GET['JSON']);
	$part_number = trim($filters->part_number);
	$serial_number = trim($filters->serial_number);
	
	
	$sql = "SELECT part_number FROM " . $GLOBALS['ecs']->table('inventory') . " WHERE serial_number = '$serial_number'";
	$original_part_number = $db->getOne($sql);
	
	if($original_part_number == $part_number)
	{
		$arr = array('is_inventory' => 1);
	}else{
		$arr = array('is_inventory' => 0);
	}

    make_json_result($arr);
	

}



/*------------------------------------------------------ */
//-- 修改订单（载入页面）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
	/* 检查权限 */
    admin_priv('inspect_part_number');	
	
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
		$smarty->assign('inv_price', $infoarr['inv_price']);
		$smarty->assign('serial_number', $infoarr['serial_number']);
		$smarty->assign('start_time', local_date($_CFG['time_format'], $infoarr['inv_start_time']));
		$smarty->assign('end_time', local_date($_CFG['time_format'], $infoarr['inv_end_time']));
		$smarty->assign('status', $infoarr['status_id']);
		$smarty->assign('action_note', $infoarr['action_note']);
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
    admin_priv('inspect_part_number');

	$action_user = $_SESSION['admin_name'];

	$start_time = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $end_time   = empty($_REQUEST['end_time']) ? 0 : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

	$attr_value = $_POST['attr_value_list'];
	if($attr_value)
	{
		//print_r($attr_value);
		foreach($attr_value as $vaule){
			if($vaule != ''){
				
				$is_only = $exc_inv->is_only('serial_number', $vaule);
				if (!$is_only)
			    {
			        sys_msg(sprintf($vaule.$_LANG['serial_number_exist'], stripslashes($_POST['serial_number'])), 1);
			    }
				
				$max_inv_id     = $db->getOne("SELECT MAX(inv_id) + 1 FROM ".$ecs->table('inventory'));
		        
				$sql = "INSERT INTO ".$ecs->table('inventory')."(inv_id, inv_start_time, inv_end_time, inv_price,status_id, part_number,serial_number, action_user,action_note) ".
			           "VALUES ($max_inv_id, $start_time, $end_time, '$_POST[inv_price]', '$_POST[status]', '$_POST[part_number]', '$vaule', '$action_user', '$_POST[action_note]')";
				//echo $sql;
			    $db->query($sql);
			
				//库存log
				inventory_log($max_inv_id,get_status_name_by_id($_POST['status']),$_POST['action_note']);		
			}	
		}
	}
	
	$is_only = $exc_inv->is_only('serial_number', $_POST['serial_number']);

    if (!$is_only)
    {
        sys_msg(sprintf($_POST['serial_number'].$_LANG['serial_number_exist'], stripslashes($_POST['serial_number'])), 1);
    }
	
    /*插入数据*/
	$max_inv_id     = $db->getOne("SELECT MAX(inv_id) + 1 FROM ".$ecs->table('inventory'));
	
    $sql = "INSERT INTO ".$ecs->table('inventory')."(inv_id, inv_start_time, inv_end_time, inv_price, status_id, part_number,serial_number, action_user,action_note) ".
           "VALUES ($max_inv_id, $start_time, $end_time, '$_POST[inv_price]', '$_POST[status]', '$_POST[part_number]', '$_POST[serial_number]', '$action_user', '$_POST[action_note]')";
    $db->query($sql);
	
	/* 修改库存和平均成本价 */
	act_goods_number_and_average_price($_POST['part_number']);
	
    admin_log($_POST[''],'add','inventory');
	inventory_log($max_inv_id,get_status_name_by_id($_POST['status']),$_POST['action_note']);
	

    /* 清除缓存*/
    clear_cache_files();
	
	$link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'inventory.php?act=add&part_number='.$_POST['part_number'];

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'inventory.php?act=list';

    sys_msg($_LANG['inventoryadd_succed'], 0, $link);

}

/*------------------------------------------------------ */
//-- 更新库存数据
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == 'update')
{
	/*检查权限*/
    admin_priv('inspect_part_number');

	//$is_only = $exc_inv->is_only('serial_number', $_POST['serial_number']);
	/*
    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['serial_number_exist'], stripslashes($_POST['serial_number'])), 1);
    }*/

	$action_user = $_SESSION['admin_name'];

	$start_time = empty($_REQUEST['start_time']) ? 0 : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
    $end_time   = empty($_REQUEST['end_time']) ? 0 : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

	$part_value = $_POST['attr_part_list'];
	$serial_value = $_POST['attr_serial_list'];
	$inv_id_value = $_POST['attr_id_list'];
	if($part_value && $serial_value)
	{
		//批量操作 不能修改库存价格 不能批量入库 以serial_number 为准 可能会覆盖part_number 
		//print_r($attr_value);
		for($i=0;$i<count($part_value);$i++)
			if($part_value[$i] != '')
			{
				$sql = "UPDATE " . $ecs->table('inventory') .
						" SET inv_end_time = '$end_time', " .
							"status_id 	  = '$_POST[status]', ". 
							"part_number  = '$part_value[$i]', ".
							"serial_number = '$serial_value[$i]', ".
							"action_note = '$_POST[action_note]', ".
							"action_user = '$action_user' ".
			            "WHERE serial_number = '$serial_value[$i]' LIMIT 1";
				//echo $sql;
			    $db->query($sql);
			
				
				/* 修改库存和平均成本价 */
				act_goods_number_and_average_price($part_value[$i]);
				//库存log
				inventory_log(trim($serial_value[$i]),get_status_name_by_id($_POST['status']),$_POST['action_note'],1);	
					
			}	
	}
	else
	{	

    	/*更新数据*/
		$sql = "UPDATE " . $ecs->table('inventory') .
			" SET inv_start_time = '$start_time', " .
				"inv_end_time = '$end_time', ".
				"inv_price 	  = '$_POST[inv_price]', ".
				"status_id 	  = '$_POST[status]', ".
				"part_number  = '$_POST[part_number]', ".
				"serial_number = '$_POST[serial_number]', ".
				"action_note = '$_POST[action_note]', ".
				"action_user = '$action_user' ".
            "WHERE inv_id = '$_POST[inv_id]' LIMIT 1";
		//echo $sql;
    	$db->query($sql);

		/* 修改库存和平均成本价 */
		act_goods_number_and_average_price($_POST['part_number']);
	
    	admin_log($_POST[''],'update','inventory');
	
		inventory_log($_POST['inv_id'],get_status_name_by_id($_POST['status']),$_POST['action_note']);
	}
	
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
	/*检查权限*/
    admin_priv('inspect_part_number');
	
	$inv_id = intval($_REQUEST['id']);
	
	/* 检查权限 */
    check_authz_json('inventory');
	/*记录part_number & serial_number*/
	$sql = "SELECT part_number,serial_number FROM " . $GLOBALS['ecs']->table('inventory') . " WHERE inv_id = '$inv_id'";
    $inv_info = $GLOBALS['db']->getRow($sql);

    /* 记录日志 */
    admin_log('', 'batch_remove', 'inventory');
	$inv_info_text = "part_number: ". $inv_info['part_number'].".  serial_number: ".$inv_info['serial_number'];
	inventory_log($inv_id,'删除',$inv_info_text);
		
    /*删除*/
	
	$sql = "DELETE FROM " . $ecs->table('inventory') . " WHERE inv_id = '$inv_id'";
	$GLOBALS['db']->query($sql);
    
	/* 清除缓存 */
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
    admin_priv('inventory_status');

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['inventory_status']);
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
    admin_priv('inventory_status');

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
    admin_priv('inventory_status');

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
	/*检查权限*/
    admin_priv('inventory_status');
	
	$status_id = intval($_REQUEST['id']);
	
	/* 检查权限 */
    check_authz_json('inventory');

    /*删除*/
	
	$sql = "DELETE FROM " . $ecs->table('inventory_status') . " WHERE status_id = '$status_id'";
	$db->query($sql);

    /* 清除缓存 */
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
	/*检查权限*/
    admin_priv('inventory_status');
	
    $list_status = get_status_list('fullinv');
    $smarty->assign('list_status',   $list_status);

    make_json_result($smarty->fetch('inventory_list_status.htm'));
}

/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch')
{
	/*检查权限*/
    admin_priv('batch_inventory');
	
    $code = empty($_REQUEST['extension_code'])? '' : trim($_REQUEST['extension_code']);

    /* 取得要操作的商品编号 */
	//$checkarray = array('21', '22', '23');
    //$inv_id = !empty($checkarray) ? join(',', $checkarray) : 0;
    $inv_id = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;

    if (isset($_POST['type']))
    {
        /*入库 */
        if ($_POST['type'] == 'inbound')
        {
	
            update_inventory($inv_id, 'status_id', get_status_id_by_desc($_POST['type']));
			update_inventory($inv_id, 'inv_end_time','');
			
            /* 记录日志 */
            admin_log('', 'batch_inbound', 'inventory');
			inventory_log($inv_id,get_status_name_by_desc($_POST['type']),get_status_name_by_desc($_POST['type']));
        }

        /* 出库 */
        if ($_POST['type'] == 'outbound')
        {
            update_inventory($inv_id, 'status_id', get_status_id_by_desc($_POST['type']));
			//出库时间
			update_inventory($inv_id, 'inv_end_time',gmtime());
			
            /* 记录日志 */
            admin_log('', 'batch_outbound', 'inventory');
			inventory_log($inv_id,get_status_name_by_desc($_POST['type']),get_status_name_by_desc($_POST['type']));

        }

        /* 转移到技术 */
        if ($_POST['type'] == 'lendtotech')
        {
            update_inventory($inv_id, 'status_id', get_status_id_by_desc($_POST['type']));
			//出库时间
			update_inventory($inv_id, 'inv_end_time',gmtime());
			
            /* 记录日志 */
            admin_log('', 'batch_lendtotech', 'inventory');
			inventory_log($inv_id,get_status_name_by_desc($_POST['type']),get_status_name_by_desc($_POST['type']));

        }

        /* 转移到市场 */
        if ($_POST['type'] == 'lendtomarket')
        {
            update_inventory($inv_id, 'status_id', get_status_id_by_desc($_POST['type']));
			//出库时间
			update_inventory($inv_id, 'inv_end_time',gmtime());
			
            /* 记录日志 */
            admin_log('', 'batch_lendtomarket', 'inventory');
			inventory_log($inv_id,get_status_name_by_desc($_POST['type']),get_status_name_by_desc($_POST['type']));
			
        }

        /* 采购状态 */
        if ($_POST['type'] == 'purchase')
        {
            update_inventory($inv_id, 'status_id', get_status_id_by_desc($_POST['type']));

            /* 记录日志 */
            admin_log('', 'batch_lendtotech', 'inventory');
			inventory_log($inv_id,get_status_name_by_desc($_POST['type']),get_status_name_by_desc($_POST['type']));

        }
        
		
        /* 还原 */
        elseif ($_POST['type'] == 'other')
        {
            update_inventory($inv_id, 'status_id', get_status_id_by_desc($_POST['type']));

            /* 记录日志 */
            admin_log('', 'batch_other', 'inventory');
			inventory_log($inv_id,get_status_name_by_desc($_POST['type']),get_status_name_by_desc($_POST['type']));

        }
        /* 还原 */
        elseif ($_POST['type'] == 'repair')
        {
            update_inventory($inv_id, 'status_id', get_status_id_by_desc($_POST['type']));
			//出库时间
			update_inventory($inv_id, 'inv_end_time',gmtime());
			
            /* 记录日志 */
            admin_log('', 'batch_repair', 'inventory');
			inventory_log($inv_id,get_status_name_by_desc($_POST['type']),get_status_name_by_desc($_POST['type']));

        }
        /* 删除 */
        elseif ($_POST['type'] == 'drop')
        {
			/*记录part_number & serial_number*/
			$sql = "SELECT part_number,serial_number FROM " . $GLOBALS['ecs']->table('inventory') . " WHERE inv_id = '$inv_id'";
		    $inv_info = $GLOBALS['db']->getRow($sql);
		
            delete_inventory($inv_id);
            /* 记录日志 */
            admin_log('', 'batch_remove', 'inventory');
			$inv_info_text = "part_number: ". $inv_info['part_number'].".  serial_number: ".$inv_info['serial_number'];
			inventory_log($inv_id,'删除',$inv_info_text);
			
        }
    }
	
	/* 批量修改库存和平均成本价 */
	if (!is_array($inv_id))
    {
        $inv_id = explode(',', $inv_id);
    }
    $inv_id = array_unique($inv_id);
	foreach ($inv_id AS $item)
	{
		act_goods_number_and_average_price_inv_id($item);
		
	}
		
    /* 清除缓存 */
    clear_cache_files();

    $link[] = array('href' => 'inventory.php?act=list', 'text' => $_LANG['inventory_list']);
    sys_msg($_LANG['batch_handle_ok'], 0, $link);
}


/*------------------------------------------------------ */
//-- 全手动批量操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch_operate')
{
	/* 检查权限 */
    admin_priv('batch_inventory');	
	
    /* 取得参数 act */
    $act = $_GET['act'];
	if($act == 'add'){
		$part_number = empty($_GET['part_number']) ? '' : trim($_GET['part_number']);
		$smarty->assign('part_number', $part_number);
		$smarty->assign('start_time',local_date($_CFG['time_format'], gmtime() ));
		$smarty->assign('status', 1);	    
	}
    $smarty->assign('ur_here', (($act == 'add') ?
        $_LANG['add_inventory'] : $_LANG['edit_inventory']));
    $smarty->assign('step_act', $act);

	/* 模板赋值 */
    $smarty->assign('action_link', array('href' => 'inventory.php?act=list', 'text' => $_LANG['inventory_list']));

    $smarty->assign('status_list',get_status_list('noinbound'));   // 订单状态

    /* 显示模板 */
    assign_query_info();
    $smarty->display('inventory_batch_operate.htm');
}




/*------------------------------------------------------ */
//-- 库存log列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'list_log')
{
    /* 检查权限 */
    admin_priv('inventory_log');

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['01_inv_list']);
    $smarty->assign('action_link', array('href' => 'inventory.php?act=list', 'text' => $_LANG['inventory_list']));

    $inventory_log = inventory_list_log();
    $smarty->assign('inventory_log',   $inventory_log['orders']);
    $smarty->assign('filter',       $inventory_log['filter']);
    $smarty->assign('record_count', $inventory_log['record_count']);
    $smarty->assign('page_count',   $inventory_log['page_count']);
    $smarty->assign('sql',   $inventory_log['sql']);
    $sort_flag  = sort_flag($inventory_log['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);


    $smarty->assign('full_page',    1);

    /* 显示模板 */
    assign_query_info();
    $smarty->display('inventory_log.htm');
}



/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query_list_log')
{
	/* 检查权限 */
    admin_priv('inventory_log');
    
    $inventory_log = inventory_list_log();

    $smarty->assign('inventory_log',   $inventory_log['orders']);
    $smarty->assign('filter',       $inventory_log['filter']);
    $smarty->assign('record_count', $inventory_log['record_count']);
    $smarty->assign('page_count',   $inventory_log['page_count']);
    $smarty->assign('sql',   $inventory_log['sql']);

    $sort_flag  = sort_flag($inventory_log['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    
	
    make_json_result($smarty->fetch('inventory_log.htm'), '', array('filter' => $inventory_log['filter'], 'page_count' => $inventory_log['page_count']));
}

/*------------------------------------------------------ */
//-- 库存log列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'delete_log')
{
    /*检查权限*/
    admin_priv('inventory_log');
	
	$action_id = intval($_REQUEST['id']);
	
	/* 检查权限 */
    check_authz_json('inventory');

    /*删除*/
	
	$sql = "DELETE FROM " . $ecs->table('inventory_log') . " WHERE action_id = '$action_id'";
	echo $sql;
	$db->query($sql);

    /* 清除缓存 */
    clear_cache_files();

	$url = 'inventory.php?act=query_list_log&' . str_replace('act=delete_log', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}


?>