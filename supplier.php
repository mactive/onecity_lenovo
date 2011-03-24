<?php

/**
 * SINEMALL 商品相册 * $Author: testyang $
 * $Id: gallery.php 14481 2008-04-18 11:23:01Z testyang $
*/
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/solution_operate.php');

require_once(ROOT_PATH . 'includes/lib_goods.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'admin/includes/lib_main.php');
require_once(ROOT_PATH . 'admin/includes/cls_exchange.php');

include_once(ROOT_PATH . 'includes/cls_json.php');

require_once(ROOT_PATH . 'includes/lib_solution_operate.php');
require_once(ROOT_PATH . 'includes/lib_solution_purchase.php');
$exc   		 = new exchange($ecs->table("step_db"), $db, 'step_id', 'goods_id');
$exc_order   = new exchange($ecs->table("step_order"), $db, 'order_id', 'order_name');
$exc_supplier  = new exchange($ecs->table("supplier"), $db, 'supplier_id', 'supplier_name');
$exc_supplier_contact  = new exchange($ecs->table("supplier_contact"), $db, 'contact_id', 'contact_name');


if($_SESSION['user_rank'] < rank_purchase_staff)
{
	/* 提示信息 无权查看*/
    show_message($_LANG['have_no_privilege'], $_LANG['profile_lnk'], 'user.php?act=login', 'info');

}


if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = "panel";
}

$position = assign_ur_here();
$smarty->assign('page_title',       $position['title']);    // 页面标题 在线配单
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
$smarty->assign('action',       	$_REQUEST['act']);
$smarty->assign('lang',  $_LANG);
$smarty->assign('img_path',   'themes/default/images/');     // 图片路径


//注册机构 
if ($_REQUEST['act'] == 'panel')
{	
    
	/* 客户列表 */
	$supplier_list = get_supplier_contact_list($_SESSION['user_id']);
	$smarty->assign('supplier_list',      $supplier_list['supplier_list']);
    $smarty->assign('filter',       	$supplier_list['filter']);
    $smarty->assign('record_count', 	$supplier_list['record_count']);
    $smarty->assign('page_count',   	$supplier_list['page_count']);    
	
	$smarty->assign('full_page',        '1');  // 当前位置
	$smarty->assign('sql',   	$supplier_list['sql']);
	
    $smarty->display('supplier_list.dwt');

}
// 页面内刷新 php
elseif ($_REQUEST['act'] == 'query_panel')
{	
	/* 客户列表 */
	$supplier_list = get_supplier_contact_list($_SESSION['user_id']);
	$smarty->assign('supplier_list',      $supplier_list['supplier_list']);
    $smarty->assign('filter',       	$supplier_list['filter']);
    $smarty->assign('record_count', 	$supplier_list['record_count']);
    $smarty->assign('page_count',   	$supplier_list['page_count']);    
	
	$smarty->assign('full_page',        '0');  // 当前位置
	$smarty->assign('sql',   	$goods_list['sql']);
	
    make_json_result($smarty->fetch('supplier_list.dwt'), '', array('filter' => $supplier_list['filter'], 'page_count' => $supplier_list['page_count']));

}

/*------------------------------------------------------ */
//-- 编辑机构
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_supplier')
{
    $sql = "SELECT * ".
            "FROM " .$ecs->table('supplier'). " WHERE supplier_id='$_REQUEST[supplier_id]'";
    $supplier = $db->GetRow($sql);

    $smarty->assign('ur_here',     $_LANG['supplier_edit']);
    $smarty->assign('supplier',       $supplier);
    $smarty->assign('form_action', 'update_supplier');
    $smarty->assign('cfg', $_CFG);

	$smarty->assign('full_page',      1);
		
    $smarty->display('supplier.dwt');
}
elseif ($_REQUEST['act'] == 'update_supplier')
{


    /*对描述处理*/
    if (!empty($_POST['supplier_desc']))
    {
        $_POST['supplier_desc'] = $_POST['supplier_desc'];
    }


    /* 处理图片 */
    $param = "supplier_name = '$_POST[supplier_name]',  supplier_desc='$_POST[supplier_desc]', supplier_phone='$_POST[supplier_phone]', tax_number = '$_POST[tax_number]', bank_name='$_POST[bank_name]', bank_account='$_POST[bank_account]'  ";


    if ($exc_supplier->edit($param,  $_POST['supplier_id']))
    {

		show_message($_POST['supplier_name']. '编辑成功', $_LANG['profile_lnk'], 'supplier.php', 'info', true);
		
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑机构联系人
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_contact')
{
    $sql = "SELECT * ".
            "FROM " .$ecs->table('supplier_contact'). " WHERE contact_id='$_REQUEST[contact_id]'";
    $contact = $db->GetRow($sql);

    $smarty->assign('contact',       $contact);
    $smarty->assign('form_action', 'update_contact');
    $smarty->assign('cfg', $_CFG);

	$supplier_list = get_supplier_list($_SESSION['user_id']);
	$smarty->assign('supplier_list',       $supplier_list);

	$full_page = empty($_REQUEST['full_page']) ? 1 : intval($_REQUEST['full_page']);
	$smarty->assign('full_page',      $full_page);
			
    $smarty->display('supplier.dwt');
}
elseif ($_REQUEST['act'] == 'update_contact')
{


    /*对描述处理*/
    if (!empty($_POST['contact_desc']))
    {
        $_POST['contact_desc'] = $_POST['contact_desc'];
    }


    /* 处理图片 */
    $param = "supplier_id = '$_POST[supplier_id]', contact_email = '$_POST[contact_email]', contact_name = '$_POST[contact_name]',  contact_desc='$_POST[contact_desc]', contact_sex='$_POST[contact_sex]',contact_office_phone='$_POST[contact_office_phone]', contact_msn='$_POST[contact_msn]', contact_qq='$_POST[contact_qq]', contact_mobile_phone='$_POST[contact_mobile_phone]'  ";


    if ($exc_supplier_contact->edit($param,  $_POST['contact_id']))
    {

		show_message($_POST['contact_name']. '编辑成功', $_LANG['profile_lnk'], 'supplier.php', 'info', true);
		
    }
    else
    {
        die($db->error());
    }
}



//注册机构 
elseif ($_REQUEST['act'] == 'take_supplier_register')
{	
	$smarty->assign('action',       $_REQUEST['act']);
	$full_page = isset($_REQUEST['full_page']) ? $_REQUEST['full_page'] : 1 ;
	$smarty->assign('full_page',      $full_page);
	$smarty->assign('form_action', 'act_supplier_register');
    
    $smarty->display('supplier.dwt');

}
//注册机构  写入数据库
elseif ($_REQUEST['act'] == 'act_supplier_register')
{
    $supplier_name = isset($_POST['supplier_name']) ? trim($_POST['supplier_name']) : '';
    $supplier_desc = isset($_POST['supplier_desc']) ? trim($_POST['supplier_desc']) : '';
    $tax_number = isset($_POST['tax_number']) ? trim($_POST['tax_number']) : '';
    $bank_name = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
    $bank_account = isset($_POST['bank_account']) ? trim($_POST['bank_account']) : '';
    $supplier_phone = isset($_POST['supplier_phone']) ? trim($_POST['supplier_phone']) : '';

	//写入机构数据库	
	$supplier_id 	= $db->getOne("SELECT MAX(supplier_id) + 1 FROM ". $ecs->table('supplier'));
	$sql = 'INSERT INTO '. $ecs->table('supplier').
			'(supplier_id, supplier_name,supplier_desc, user_id, supplier_phone,tax_number, bank_name, bank_account) '.
			"VALUE ('$supplier_id','$supplier_name','$supplier_desc', '$_SESSION[user_id]', '$supplier_phone','$tax_number','$bank_name', '$bank_account')";

    $db->query($sql);
	
	//同时写入联系人数据库
	$contact_id 	= $db->getOne("SELECT MAX(contact_id) + 1 FROM ". $ecs->table('supplier_contact'));
	
	$sql = 'INSERT INTO '. $ecs->table('supplier_contact').
			'(contact_id, supplier_id, user_id, contact_name, contact_desc,  contact_office_phone ) '.
			"VALUE ('$contact_id','$supplier_id','$_SESSION[user_id]', '$supplier_name',  '$supplier_desc', '$supplier_phone')";

	//echo $sql;	
    $db->query($sql);

	show_message($supplier_name. '注册成功', '为其添加联系人', 'supplier.php?act=edit_contact&contact_id='.$contact_id, 'info', true);
}

/* 代为会员注册界面 */
elseif ($_REQUEST['act'] =='take_contact_register')
{
	$supplier_list = get_supplier_list($_SESSION['user_id']);
	$smarty->assign('supplier_list',       $supplier_list);
	$full_page = isset($_REQUEST['full_page']) ? $_REQUEST['full_page'] : 1 ;
	$smarty->assign('full_page',      $full_page);
		
	$smarty->assign('action',       $_REQUEST['act']);
	$smarty->assign('form_action', 'act_take_contract_register');
	
    $smarty->display('supplier.dwt');
}
elseif ($_REQUEST['act'] =='act_take_contract_register'){
	$supplier_id = empty($_REQUEST['supplier_id']) ? 0 : intval($_REQUEST['supplier_id']);
    
	$contact_name = isset($_POST['contact_name']) ? trim($_POST['contact_name']) : '';
	$contact_email = isset($_POST['contact_email']) ? trim($_POST['contact_email']) : '';
	$sex = empty($_REQUEST['sex']) ? 0 : intval($_REQUEST['sex']);
	$contact_mobile_phone = isset($_POST['contact_mobile_phone']) ? trim($_POST['contact_mobile_phone']) : '';
	$contact_office_phone = isset($_POST['contact_office_phone']) ? trim($_POST['contact_office_phone']) : '';
	$contact_msn = isset($_POST['contact_msn']) ? trim($_POST['contact_msn']) : '';
	$contact_qq = isset($_POST['contact_qq']) ? trim($_POST['contact_qq']) : '';
	$contact_desc = isset($_POST['contact_desc']) ? trim($_POST['contact_desc']) : '';

	//写入数据库
	$sql = 'INSERT INTO '. $ecs->table('supplier_contact').
			'(contact_id, supplier_id, user_id, contact_email, contact_name, contact_desc, contact_sex, contact_msn, contact_qq, contact_office_phone, contact_mobile_phone) '.
			"VALUE ('','$supplier_id','$_SESSION[user_id]', '$contact_email', '$contact_name',  '$contact_desc', '$contact_sex', '$contact_msn', '$contact_qq', '$contact_office_phone', '$contact_mobile_phone')";

	//echo $sql;	
    $db->query($sql);
 	show_message($contact_name. '注册成功', $_LANG['profile_lnk'], 'supplier.php', 'info', true);
//	$url = "supplier.php";
//	ecs_header('Location: ' .$url.'?act=show'. "\n");
//  exit;
    
    
}


?>