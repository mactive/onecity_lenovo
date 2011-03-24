<?php

/**
 * SINEMALL 商品分类 * $Author: testyang $
 * $Id: category.php 14481 2008-04-18 11:23:01Z testyang $
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
require_once(ROOT_PATH . 'includes/lib_solution_purchase.php');// only for purchase

$exc   		 = new exchange($ecs->table("step_db"), $db, 'step_id', 'goods_id');
$exc_order   = new exchange($ecs->table("step_order"), $db, 'order_id', 'order_name');
$exc_supplier  = new exchange($ecs->table("supplier"), $db, 'supplier_id', 'supplier_name');
$exc_supplier_contact  = new exchange($ecs->table("supplier_contact"), $db, 'contact_id', 'contact_name');

$exc_agency  = new exchange($ecs->table("agency"), $db, 'agency_id', 'agency_name');
$exc_agency_contact  = new exchange($ecs->table("agency_contact"), $db, 'contact_id', 'contact_name');
$exc_purchase = new exchange($ecs->table("purchase"), $db, 'purchase_id', 'goods_id');

if($_SESSION['user_rank'] < rank_purchase_staff)
{
	/* 提示信息 无权查看*/
	show_message($_LANG['have_no_privilege'], $_LANG['relogin_lnk'], 'user.php?act=login&back_act='.$back_act, 'info');
}


if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}


/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = "show";
	$smarty->assign('act_step',       $_REQUEST['act']);

}




/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 20;
$purchase_status = isset($_REQUEST['purchase_status']) && intval($_REQUEST['purchase_status']) > 0 ? intval($_REQUEST['purchase_status']) : 0;
$tag_name = empty($_REQUEST['tag_name']) ? '' : trim($_REQUEST['tag_name']);

$filter_attr = empty($_REQUEST['filter_attr']) ? '' : trim($_REQUEST['filter_attr']);

$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
$smarty->assign('lang',  $_LANG);

/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'is_promote' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update','goods_name','sort_order'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;


/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 页面的缓存ID */
//$cache_id = sprintf('%X', crc32($cat_id . '-' . $display . '-' . $sort  .'-' . $order  .'-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' . $_CFG['lang'] .'-'. $brand. '-' . $price_max . '-' .$price_min . '-' . $filter_attr));

//为采购列表信息
if ($_REQUEST['act'] == 'show')
{
    /* 如果页面没有被缓存则重新获取页面的内容 */

    $children = get_children($cat_id);

    $cat = get_cat_info($cat_id);   // 获得分类的相关信息

    $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));
    $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
    $smarty->assign('cat_style',   htmlspecialchars($cat['style']));
	
	
    $position = assign_ur_here($cat_id, $brand_name);
    $smarty->assign('page_title',       $position['title']);    // 页面标题 在线配单
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

    $purchase_list 	= get_purchase_list($purchase_status);
    $smarty->assign('purchase_list',    $purchase_list['purchase_list']);
    $smarty->assign('filter',       	$purchase_list['filter']);
    $smarty->assign('record_count', 	$purchase_list['record_count']);
    $smarty->assign('page_count',   	$purchase_list['page_count']);
    $smarty->assign('sql',   			$purchase_list['sql']);
    $smarty->assign('count_sql',   		$purchase_list['count_sql']);


	$smarty->assign('purchase_status',     $purchase_status);  // 当前位置
	
	$smarty->assign('full_page',        '1');  // 当前位置
		
	$smarty->display('solution_purchase.dwt');
	
}
/* query_show*/
elseif ($_REQUEST['act'] == 'query_show')
{
    
	$brand = isset($_REQUEST['brand']) && intval($_REQUEST['brand']) > 0 ? intval($_REQUEST['brand']) : 0;
	$smarty->assign('brand',         $brand);
	
    $purchase_list 	= get_purchase_list($purchase_status);
    $smarty->assign('purchase_list',    $purchase_list['purchase_list']);
    $smarty->assign('filter',       	$purchase_list['filter']);
    $smarty->assign('record_count', 	$purchase_list['record_count']);
    $smarty->assign('page_count',   	$purchase_list['page_count']);
	$smarty->assign('sql',   			$purchase_list['sql']);
    $smarty->assign('count_sql',   		$purchase_list['count_sql']);
    
	
    make_json_result($smarty->fetch('solution_purchase.dwt'), '', array('filter' => $purchase_list['filter'], 'page_count' => $purchase_list['page_count']));
	
}

/* list order_detail*/
elseif ($_REQUEST['act'] == 'operate_purchase')
{
	$goods_id = isset($_REQUEST['goods_id'])   && intval($_REQUEST['goods_id'])  > 0 ? intval($_REQUEST['goods_id'])  : 0;
	$full_page = empty($_REQUEST['full_page']) ? 0 : intval($_REQUEST['full_page']);
	$smarty->assign('full_page',        $full_page);  // 当前位置

    /* 如果页面没有被缓存则重新获取页面的内容 */
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('purchase') .
			" WHERE goods_id = ". $goods_id ." AND purchase_status = 0 ";
					
	$purchase_info = $GLOBALS['db']->getRow($sql);
	
	$smarty->assign('purchase_info',$purchase_info);

	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
	
    $position = assign_ur_here($cat_id, $brand_name);
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
    $smarty->assign('cfg', $_CFG);
	$smarty->assign('action',     $_REQUEST['act']);
	$smarty->assign('status_list',     $_LANG['status_list']);


	$supplier_list = get_supplier_list($_SESSION['user_id']);
	$smarty->assign('supplier_list',       $supplier_list);

	$smarty->assign('form_action', 'update_step_purchase'); //指定返回处理结果
   	$smarty->display('operate_purchase.dwt');	
}

/**/
elseif ($_REQUEST['act'] == 'update_step_purchase')
{	
	$purchase_id 		= !empty($_POST['purchase_id']) ? $_POST['purchase_id'] : 0 ;
	$goods_id 			= !empty($_POST['goods_id']) ? $_POST['goods_id'] : 0 ;
	$purchase_count		= !empty($_POST['purchase_count']) ? $_POST['purchase_count'] : 0;
	$purchase_price		= !empty($_POST['purchase_price']) ? $_POST['purchase_price'] : 0;
	$order_period		= !empty($_POST['order_period']) ? $_POST['order_period'] : 0;
	$supplier_id		= !empty($_POST['supplier_id']) ? $_POST['supplier_id'] : 0;
	$supplier_contact_id= !empty($_POST['supplier_contact_id']) ? $_POST['supplier_contact_id'] : 0;
	
	// 原始的采购单子
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('purchase') .
			" WHERE goods_id = ". $goods_id ." AND purchase_status = 0 ";
					
	$old_info = $GLOBALS['db']->getRow($sql);
	
	// 更新原来的那个 purchase_count
	$c_1 = $old_info['purchase_count'] - $purchase_count ;
	$where = " WHERE purchase_id = ".$old_info['purchase_id'] ." AND goods_id = ".$old_info['goods_id'] ." AND purchase_status = 0 ";
		
	$sql = "UPDATE " . $GLOBALS['ecs']->table('purchase') .
			" SET purchase_count = '$c_1' ". $where ;
	   
	$GLOBALS['db']->query($sql);
	
	// 更新数据
	$old_info['purchase_id'] 	= '';
	$old_info['purchase_count'] = $purchase_count;
	$old_info['purchase_price'] = $purchase_price;
	$old_info['purchase_time'] =  gmtime();
	$old_info['order_period'] 	= $order_period;
	$old_info['purchase_status']= '1'; //已经采购
	$old_info['supplier_id'] 	= $supplier_id;
	$old_info['supplier_contact_id'] = $supplier_contact_id;
	
	//插入新数据
	$GLOBALS['db']->autoExecute($ecs->table('purchase'), $old_info, 'INSERT');
    

	
	
	if($purchase_count){
		show_message($old_info['goods_name']. '采购成功', $_LANG['go_back'], 'solution_purchase.php?purchase_status=1', 'info', true);
	}
    else
    {
		show_message($old_info['goods_name']. '商品有库存 无需采购', $_LANG['go_back'], 'javascript:history.back(-1)', 'info', true);
    }

	
}


// 修改客户的ID
elseif ($_REQUEST['act'] == 'change_supplier_id')
{   	
	include_once(ROOT_PATH . 'includes/cls_json.php');
	$json = new JSON;
    $filters = $json->decode($_GET['JSON']);
	$supplier_id = $filters->supplier_id;
	/**/
	if($supplier_id){
			//刷新需要显示的值
			
			$supplier_contact_list = getSupplierContactList($_SESSION['user_id'],$supplier_id); //架构 客户 联系人
			$smarty->assign('supplier_contact_list',      $supplier_contact_list);	
			make_json_result($smarty->fetch('supplier_contact_list.htm'));
	}
	
}

// 修改客户的ID
/* 主动采购*/
elseif ($_REQUEST['act'] == 'pre_purchase')
{
	
}


/*------------------------------------------------------ */
//-- 没有order_id 时 获得最新的 order_id
/*------------------------------------------------------ */
function get_newest_order_id(){
	$sql = "SELECT MAX(order_id) FROM " . $GLOBALS['ecs']->table('step_order') .
             " WHERE 1";
    $sn = $GLOBALS['db']->getOne($sql);
	$sn = $sn + 1;
	return $sn;
}



/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */


?>
