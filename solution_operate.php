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
$exc   		 = new exchange($ecs->table("step_db"), $db, 'step_id', 'goods_id');
$exc_order   = new exchange($ecs->table("step_order"), $db, 'order_id', 'order_name');
$exc_contract= new exchange($ecs->table("order_contract"), $db, 'contract_id', 'contract_name');
$exc_address = new exchange($ecs->table("user_address"), $db, 'address_id', 'address');
$exc_agency  = new exchange($ecs->table("agency"), $db, 'agency_id', 'agency_name');
$exc_agency_contact  = new exchange($ecs->table("agency_contact"), $db, 'contact_id', 'contact_name');
$exc_purchase = new exchange($ecs->table("purchase"), $db, 'purchase_id', 'goods_id');


if($_SESSION['user_rank'] < rank_sale_staff && $_REQUEST['act'] != 'preview_order')
{
	/* 提示信息 无权查看*/
    show_message($_LANG['have_no_privilege'], $_LANG['relogin_lnk'], 'user.php?act=login&back_act='.$back_act, 'info');

}
if($_SESSION['user_rank'] == rank_purchase_staff)
{
	/* 提示信息 无权查看*/
	$url = 'solution_purchase.php';
    ecs_header("Location: $url\n");

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


/* 获得请求的分类 ID 

if (isset($_REQUEST['category']))
{
    $cat_id = intval($_REQUEST['category']);
}
else
{
	$cat_id = 0;
    //ecs_header("Location: ./\n");
    //exit;
}
*/



/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 20;
$brand = isset($_REQUEST['brand']) && intval($_REQUEST['brand']) > 0 ? intval($_REQUEST['brand']) : 0;

$is_exe = isset($_REQUEST['is_exe'])   && intval($_REQUEST['is_exe'])  > 0 ? intval($_REQUEST['is_exe'])  : 0;
$tag_name = empty($_REQUEST['tag_name']) ? '' : trim($_REQUEST['tag_name']);
$cat_id = isset($_REQUEST['category']) && intval($_REQUEST['category']) > 0 ? intval($_REQUEST['category']) : 0;
$price_max = isset($_REQUEST['price_max']) && intval($_REQUEST['price_max']) > 0 ? intval($_REQUEST['price_max']) : 0;
$price_min = isset($_REQUEST['price_min']) && intval($_REQUEST['price_min']) > 0 ? intval($_REQUEST['price_min']) : 0;
$filter_attr = empty($_REQUEST['filter_attr']) ? '' : trim($_REQUEST['filter_attr']);
$order_id = isset($_REQUEST['order_id'])   && intval($_REQUEST['order_id'])  > 0 ? intval($_REQUEST['order_id'])  : 0;
$smarty->assign('order_id',       $order_id);
$smarty->assign('img_path',   'themes/default/images/');     // 图片路径

/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'is_promote' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update','goods_name','sort_order'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')))                              ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_SESSION['display']) ? $_SESSION['display'] : $default_display_type);

$_SESSION['display'] = $display;
$smarty->assign('lang',  $_LANG);

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 页面的缓存ID */
//$cache_id = sprintf('%X', crc32($cat_id . '-' . $display . '-' . $sort  .'-' . $order  .'-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' . $_CFG['lang'] .'-'. $brand. '-' . $price_max . '-' .$price_min . '-' . $filter_attr));

//为配单操作信息
if ($_REQUEST['act'] == 'show')
{
    /* 如果页面没有被缓存则重新获取页面的内容 */

    $children = get_children($cat_id);

    $cat = get_cat_info($cat_id);   // 获得分类的相关信息

    $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));
    $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
    $smarty->assign('cat_style',   htmlspecialchars($cat['style']));
	
//	$smarty->assign('session',  	$_SESSION);     			// all user info
//	$smarty->assign('user_name',  	$_SESSION['user_name']);    // 图片路径
//	$smarty->assign('user_rank',  	$_SESSION['user_rank']);    // 图片路径
	
	
	

    /* 赋值固定内容 */
    if ($brand > 0)
    {
        $sql = "SELECT brand_name FROM " .$GLOBALS['ecs']->table('brand'). " WHERE brand_id = '$brand'";
        $brand_name = $db->getOne($sql);
    }
    else
    {
        $brand_name = '';
    }

    
    //assign_template('c', array($cat_id));

    $position = assign_ur_here($cat_id, $brand_name);
    $smarty->assign('page_title',       $position['title']);    // 页面标题 在线配单
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

    //$smarty->assign('categories',       get_categories_tree($cat_id)); // 同级分类树
	$smarty->assign('categories',     	get_categories_tree()); // 分类树
    $smarty->assign('sub_categories',   get_sub_categories_tree($cat_id,$order_id)); // 子分类树
//    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('category',         $cat_id);
//	$smarty->assign('category_name',    get_name_form_ID($cat_id));
    $smarty->assign('brand',         $brand);
//    $smarty->assign('price_max',        $price_max);
//    $smarty->assign('price_min',        $price_min);
//    $smarty->assign('filter_attr',      $filter_attr);
//    $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-c$cat_id.xml" : 'feed.php?cat=' . $cat_id); // RSS URL


    $brand_list = get_brands_for_solution($cat_id, $order_id, 'solution_operate','1','0'); // cat order_id app 是否推荐 limitnum
    $smarty->assign('brand_list',      $brand_list);

	$goods_list 	= category_get_goods($cat_id,$brand);
    $smarty->assign('goods_list',    	$goods_list['goods']);
    $smarty->assign('filter',       	$goods_list['filter']);
    $smarty->assign('record_count', 	$goods_list['record_count']);
    $smarty->assign('page_count',   	$goods_list['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');
	$smarty->assign('full_page',        '1');  // 当前位置
	$smarty->assign('sql',   	$goods_list['sql']);
	
    //$smarty->assign('goods_list',       $goods_list);
    $smarty->assign('category',         $cat_id);

	$my_order_list = get_my_order_list($_SESSION['user_id']);	//正在配置的的order
  	$smarty->assign('my_order_list',       $my_order_list); //for operate_my_solution.lbi 文件

	$my_public_list = get_my_order_list($_SESSION['user_id'],1); //通过价格审核的order
  	$smarty->assign('my_public_list',       $my_public_list); //for operate_my_solution.lbi 文件

	$my_exe_list = get_my_order_list($_SESSION['user_id'],1,1); //通过执行的
  	$smarty->assign('my_exe_list',       $my_exe_list); //for operate_my_solution.lbi 文件

	$order_info = get_order_info($order_id);
	$smarty->assign('order_info',	$order_info);    
	
	
	$agency_list = get_agency_list();
	$smarty->assign('agency_list',       $agency_list); 
	
    
  	
//    assign_pager('solution_operate',            $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr); // 分页

//	$smarty->display('solution_operate.dwt', $cache_id);
	if($order_id){
		$order_detail 	= get_step_list($order_id); //获得一个订单下的详细产品
		//重新整理 $order_detail['orders'] 加入分类信息
		$cat_array = get_cat_array($order_detail['orders']);
	    $smarty->assign('order_detail',    $cat_array);
	
	}
	if($_SESSION['user_id'] == $_CFG['default_purchase_mid']){
		$smarty->assign('default_purchase_mid',     $_CFG['default_purchase_mid']);
	}
	$smarty->display('solution_operate.dwt');
	
}
/* query_show*/
elseif ($_REQUEST['act'] == 'query_show')
{
	$order_id = isset($_REQUEST['order_id'])   && intval($_REQUEST['order_id'])  > 0 ? intval($_REQUEST['order_id'])  : 0;   
	$brand = isset($_REQUEST['brand']) && intval($_REQUEST['brand']) > 0 ? intval($_REQUEST['brand']) : 0;
	$cat_id = isset($_REQUEST['category']) && intval($_REQUEST['category']) > 0 ? intval($_REQUEST['category']) : 0;
	$smarty->assign('category',         $cat_id);
	$smarty->assign('brand',         $brand);
	
    $goods_list 	= category_get_goods($cat_id,$brand);
    $smarty->assign('goods_list',    	$goods_list['goods']);
    $smarty->assign('filter',       	$goods_list['filter']);
    $smarty->assign('record_count', 	$goods_list['record_count']);
    $smarty->assign('page_count',   	$goods_list['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

	$smarty->assign('sql',   	$goods_list['sql']);
	
    $order_id = isset($_REQUEST['order_id'])   && intval($_REQUEST['order_id'])  > 0 ? intval($_REQUEST['order_id'])  : 0;
//	$smarty->assign('order_id',       $order_id);
	
    make_json_result($smarty->fetch('solution_operate.dwt'), '', array('filter' => $goods_list['filter'], 'order_id' => $order_id , 'page_count' => $goods_list['page_count']));
	
}

/* list order_detail*/
elseif ($_REQUEST['act'] == 'order_detail')
{
    /* 如果页面没有被缓存则重新获取页面的内容 */
	$order_info = get_order_info($order_id);
    $smarty->assign('order_info', 	$order_info);

	//判断权限
	if($_SESSION['user_rank'] != rank_sine_company){
		if($_SESSION['user_id'] != $order_info['user_id'] && $order_info['is_public'] )
		{
			show_message($_LANG['have_no_privilege_operate'], $_LANG['preview_order'], 'solution_operate.php?act=preview_order&order_id='.$order_id, 'info');
		}
		
	}

    $children = get_children($cat_id);

    $cat = get_cat_info($cat_id);   // 获得分类的相关信息

    $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));
    $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
    $smarty->assign('cat_style',   htmlspecialchars($cat['style']));
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
    
    //assign_template('c', array($cat_id));
	
    $position = assign_ur_here($cat_id, $brand_name);
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

    //$smarty->assign('categories',       get_categories_tree($cat_id)); // 同级分类树
	$smarty->assign('categories',     	get_categories_tree()); // 分类树
    $smarty->assign('sub_categories',   get_sub_categories_tree($cat_id,$order_id)); // 子分类树
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('category',         $cat_id);
	$smarty->assign('category_name',    get_name_form_ID($cat_id));
    $smarty->assign('brand',	         $brand);
    $smarty->assign('price_max',        $price_max);
    $smarty->assign('price_min',        $price_min);
    $smarty->assign('filter_attr',      $filter_attr);
    $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-c$cat_id.xml" : 'feed.php?cat=' . $cat_id); // RSS URL


    $brand_list = get_brands_for_solution($cat_id, $order_id, 'solution_operate','1','0'); // all 60 推荐 brands
    $smarty->assign('brand_list',      $brand_list);

    $count = get_cagtegory_goods_count($children, $brand, $price_min, $price_max, $ext);
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    if ($page > $max_page)
    {
        $page = $max_page;
    }

    $smarty->assign('category',         $cat_id);
	$smarty->assign('action',     $_REQUEST['act']);
	
	$my_order_list = get_my_order_list($_SESSION['user_id']);	//正在配置的的order
  	$smarty->assign('my_order_list',       $my_order_list); //for operate_my_solution.lbi 文件

	$my_public_list = get_my_order_list($_SESSION['user_id'],1); //通过价格审核的order
  	$smarty->assign('my_public_list',       $my_public_list); //for operate_my_solution.lbi 文件
	
	$my_exe_list = get_my_order_list($_SESSION['user_id'],1,1); //通过执行的
  	$smarty->assign('my_exe_list',       $my_exe_list); //for operate_my_solution.lbi 文件
	
	$agency_list = get_agency_list();//机构列表
	$smarty->assign('agency_list',       $agency_list);
	
	$smarty->assign('full_page',        '1');  // 当前位置
	
    
	
	$custom_list = get_contact_list($order_info['agency_id']); //架构 客户 联系人	
	$smarty->assign('custom_list',       $custom_list);
	
    $order_detail 	= get_step_list($order_id); //获得一个订单下的详细产品

	
	//重新整理 $order_detail['orders'] 加入分类信息
	$cat_array = get_cat_array($order_detail['orders']);
//	print_r($cat_array);
    $smarty->assign('order_detail',    $cat_array);

//    $smarty->assign('order_detail',    $order_detail['orders']);
    $smarty->assign('filter',       	$order_detail['filter']);
    $smarty->assign('record_count', 	$order_detail['record_count']);
    $smarty->assign('page_count',   	$order_detail['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

	$smarty->display('solution_order_detail.dwt');	
}


/* query_order_detail*/
elseif ($_REQUEST['act'] == 'query_order_detail')
{
    $smarty->assign('order_info',     	get_order_info($order_id));
    $smarty->assign('action',     	'order_detail');

    $order_detail 	= get_step_list($order_id); //获得一个订单下的详细产品

	//重新整理 $order_detail['orders'] 加入分类信息
	$cat_array = get_cat_array($order_detail['orders']);
    $smarty->assign('order_detail',    $cat_array); //整理过的order_detail
//    $smarty->assign('order_detail',    $order_detail['orders']);
    $smarty->assign('filter',       	$order_detail['filter']);
    $smarty->assign('record_count', 	$order_detail['record_count']);
    $smarty->assign('page_count',   	$order_detail['page_count']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('solution_order_detail.dwt'), '', array('filter' => $order_detail['filter'], 'page_count' => $order_detail['page_count']));
	
}

//为配单操作信息
if ($_REQUEST['act'] == 'search_order')
{
	    /* 如果页面没有被缓存则重新获取页面的内容 */

	    $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));
	    $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
	    $smarty->assign('cat_style',   htmlspecialchars($cat['style']));		
		
	    //assign_template('c', array($cat_id));

	    $position = assign_ur_here($cat_id, $brand_name);
	    $smarty->assign('page_title',       $position['title']);    // 页面标题
	    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

		$tag_list = get_s_order_tag_list('solution_operate',$is_exe);
	    $smarty->assign('tag_list',      $tag_list);
	    $smarty->assign('tag_name',      $tag_name);
		
		
		/*		*/
		$order_list 	= get_order_list($tag_name,$is_exe);
	    $smarty->assign('order_list',    	$order_list['orders']);
	    $smarty->assign('filter',       	$order_list['filter']);
	    $smarty->assign('record_count', 	$order_list['record_count']);
	    $smarty->assign('page_count',   	$order_list['page_count']);
	    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');
		$smarty->assign('sql',   	$order_list['sql']);

		
		$smarty->assign('full_page',        '1');  // 当前位置

		$my_order_list = get_my_order_list($_SESSION['user_id']);	//正在配置的的order
	  	$smarty->assign('my_order_list',       $my_order_list); //for operate_my_solution.lbi 文件

		$my_public_list = get_my_order_list($_SESSION['user_id'],1); //通过价格审核的order
	  	$smarty->assign('my_public_list',       $my_public_list); //for operate_my_solution.lbi 文件

		$my_exe_list = get_my_order_list($_SESSION['user_id'],1,1); //通过执行的
	  	$smarty->assign('my_exe_list',       $my_exe_list); //for operate_my_solution.lbi 文件

		$order_info = get_order_info($order_id);
		$smarty->assign('order_info',	$order_info);    


		$ur_array = array(rank_agency);//架构 正式客户 inc_constant.php
		$custom_list = get_contact_list($order_info['agency_id']); //架构 客户 联系人
		$smarty->assign('custom_list',       $custom_list); 

		$agency_list = get_agency_list();
		$smarty->assign('agency_list',       $agency_list);
	
	$smarty->display('solution_order_search.dwt');
	
}

//为配单操作信息
if ($_REQUEST['act'] == 'query_search_order')
{
	
	$tag_name = empty($_REQUEST['tag_name']) ? '' : trim($_REQUEST['tag_name']);
	$tag_list = get_s_order_tag_list('solution_operate',$is_exe);
    $smarty->assign('tag_list',      $tag_list);
    $smarty->assign('tag_name',      $tag_name);
		
	/*		*/
	$order_list 	= get_order_list($tag_name);
    $smarty->assign('order_list',    	$order_list['orders']);
    $smarty->assign('filter',       	$order_list['filter']);
    $smarty->assign('record_count', 	$order_list['record_count']);
    $smarty->assign('page_count',   	$order_list['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');
	$smarty->assign('sql',   	$order_list['sql']);
	
    make_json_result($smarty->fetch('solution_order_search.dwt'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
	
}
// AJAX 获得商品信息
elseif ($_REQUEST['act'] == 'add_solution_order')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
	$filters = $json->decode($_GET['JSON']);
	$agency_id 	= $filters->agency_id;
	$contact_id 	= $filters->contact_id;
	$order_name = $filters->order_name;

	
	$newest_order_id = get_newest_order_id();
	$add_time = gmtime();
	$contract_sn = get_contract_sn($_SESSION['user_id']);
	$temp_order_name = $order_name."-".$newest_order_id;//local_date($GLOBALS['_CFG']['time_format'], $add_time);
	$order_id = $newest_order_id;
	$order_tag = '新增配单'; //默认的配单tag
	$sql2 = 'INSERT INTO '. $ecs->table('step_order').
			'(order_id, user_id, agency_id, contact_id, add_time, order_name,contract_id,order_tag) '.
			"VALUE ('$order_id','$_SESSION[user_id]', '$agency_id', '$contact_id','$add_time','$temp_order_name','$order_id','$order_tag')";

    $db->query($sql2);

	$sql3 = 'INSERT INTO '. $ecs->table('order_contract').
			'(contract_id, user_id, agency_id, contact_id, order_id, contract_sn) '.
			"VALUE ('$order_id','$_SESSION[user_id]','$agency_id', '$contact_id','$order_id','$contract_sn')";

    $db->query($sql3);

	$l_array = get_value_form_location($filters->location_array);
	$added_location = make_location_for_add_step($l_array,$order_id);//为了切换order添加step
	$arr = array('added_location' => $added_location);
    make_json_result($arr);

//	$url = "solution_operate.php?act=show&order_id=".$order_id;
//	ecs_header("Location: $url\n");
//    exit;
	
}

// AJAX 获得商品信息
elseif ($_REQUEST['act'] == 'add_step_to_order')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
	$filters = $json->decode($_GET['JSON']);
	
	$newest_order_id = get_newest_order_id();
	$get_order_id = get_value_form_location($filters->location_array,'order_id'); 
	$order_id = $get_order_id;
	
	$add_time = gmtime();
	$goods_id 	= $filters->goods_id;
	if($goods_id){
		$part_number= $filters->part_number;
		$step_price = $filters->step_price;
		$step_count = $filters->step_count;
		$order_amount = $step_count * $step_price;
	}
	
	$contact_id 	= $filters->contact_id; 
	$agency_id 	= $filters->agency_id; 
	$contact_name = $filters->contact_name; 
	$contract_sn = get_contract_sn($_SESSION['user_id']);
	
	//如果有 goods_id 那么写入step库
	if($goods_id){
		$sql = 'SELECT cat_id FROM' .$ecs->table('goods'). " WHERE goods_id = $goods_id";
		$tmp_cat_id = $db->getOne($sql);	
		$cat_id = get_father_cat_id($tmp_cat_id);
	}
	
	
	//$location_href = make_added_location($filters->location_href,$order_id); 
	
	$temp_order_name = $contact_name ."-". $newest_order_id;//local_date($GLOBALS['_CFG']['time_format'], $add_time);
	$order_tag = 's'; //默认的配单tag
	
	if($order_id){
		$where = " order_id = '$order_id'";
		
		//配单中没有这个商品	
		if ($exc->is_only('goods_id', $goods_id,'',$where)){
			$sql = 'INSERT INTO '. $ecs->table('step_db').
					'(step_id, order_id,user_id, goods_id, cat_id, part_number, step_price, step_count, add_time) '.
					"VALUE ('','$order_id','$_SESSION[user_id]','$goods_id', '$cat_id','$part_number', '$step_price', '$step_count','$add_time')";

			//echo $sql;	
		    $db->query($sql);
			clear_cache_files();	
			

			$order_detail 	= get_step_list($order_id); //获得一个订单下的详细产品
			//重新整理 $order_detail['orders'] 加入分类信息
			$cat_array = get_cat_array($order_detail['orders']);
		    $smarty->assign('order_detail',    $cat_array);


			$smarty->assign('goods_id',     $goods_id);
			$smarty->assign('order_info',     	get_order_info($order_id));    


			make_json_result($smarty->fetch('library/operate_interaction.lbi'));			
		}
		//配单中已经有这个商品	
		else{
			//更新数量和价格
			$sql_have = "UPDATE " . $ecs->table('step_db') .
					" SET step_count = '$step_count' , step_price = '$step_price' ". 
					" WHERE order_id = '$order_id' AND goods_id = '$goods_id'";
			//echo $sql_have;	
			$db->query($sql_have);
		    
			clear_cache_files();
			
			
			$order_detail 	= get_step_list($order_id); //获得一个订单下的详细产品
			//重新整理 $order_detail['orders'] 加入分类信息
			$cat_array = get_cat_array($order_detail['orders']);
		    $smarty->assign('order_detail',    $cat_array);


			$smarty->assign('goods_id',     $goods_id);
			$smarty->assign('order_info',     	get_order_info($order_id));    


			make_json_result($smarty->fetch('library/operate_interaction.lbi'));			

		}
		
	
		
		
	}
	/**/
	else
	{	
		$order_id = $newest_order_id;
		//如果有 goods_id 那么写入step库
		if($goods_id)
		{
			$sql = 'INSERT INTO '. $ecs->table('step_db').
					'(step_id, order_id,user_id, goods_id, cat_id, part_number, step_price, step_count, add_time) '.
					"VALUE ('','$order_id','$_SESSION[user_id]','$goods_id', '$cat_id','$part_number', '$step_price', '$step_count','$add_time')";

		    $db->query($sql);
		}
		
		$sql2 = 'INSERT INTO '. $ecs->table('step_order').
				'(order_id, user_id, contact_id, agency_id, add_time, order_name,order_amount,contract_id,order_tag) '.
				"VALUE ('$order_id','$_SESSION[user_id]','$contact_id','$agency_id','$add_time','$temp_order_name','$order_amount','$order_id','$order_tag')";

	    $db->query($sql2);
	
		$sql3 = 'INSERT INTO '. $ecs->table('order_contract').
				'(contract_id, user_id, contact_id, agency_id, order_id, contract_sn) '.
				"VALUE ('$order_id','$_SESSION[user_id]','$contact_id', '$agency_id','$order_id','$contract_sn')";

	    $db->query($sql3);
	
		$added_location = make_added_location( get_value_form_location($filters->location_array), $order_id); 
		$arr = array('order_id' => $order_id,'user_id' => $_SESSION['user_id'],'added_location' => $added_location);

	    make_json_result($arr);
	}
	

}


// AJAX 获得商品信息
elseif ($_REQUEST['act'] == 'new_location')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
	$filters = $json->decode($_GET['JSON']);
	$order_id = intval($filters->order_id);
	
	$l_array = get_value_form_location($filters->location_array);
	
	$added_location = make_location_for_add_step($l_array,$order_id);//为了切换order添加step
	
	$arr = array('added_location' => $added_location);

    make_json_result($arr);
	

}

// AJAX 删除 step
elseif ($_REQUEST['act'] == 'remove_step')
{
	$step_id = intval($_REQUEST['id']);

	/*删除*/
	
	$sql = "DELETE FROM " . $ecs->table('step_db') . " WHERE step_id = '$step_id'";
	$GLOBALS['db']->query($sql);
	
	/* 清除缓存 */
    clear_cache_files();
	
	//$url = 'solution_operate?act=query_order_detail&' . str_replace('act=remove_step', '', $_SERVER['QUERY_STRING']); 	
	/*str_replace  将$_SERVER['QUERY_STRING'] 中的 act=remove_step 替换为''(空) 在这里是一大堆 参数和值*/
	
    //ecs_header("Location: $url\n");
    exit;
}

/* AJAX 验证订单的价格规则 */
elseif ($_REQUEST['act'] == 'check_order')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
	$filters = $json->decode($_GET['JSON']);
	$order_id = intval($filters->order_id);
	
	/*删除*/
	
	$sql = 	"SELECT s.*,g.shop_price ,g.salebase_price FROM " . $ecs->table('step_db') . 'AS s '. 
			"LEFT JOIN ".$ecs->table('goods') .'AS g ON g.goods_id = s.goods_id '.
			" WHERE s.order_id = '$order_id'";
	$arr = $GLOBALS['db']->getAll($sql);
	
	$too_low_arr 	= array(); // 低于销售底价的
	$too_high_arr	= array(); // 高于本店售价的
	$step_arr 		= array(); //所有的
	foreach($arr AS $key =>$val){
		array_push($step_arr,$val['step_id']);
		if($val['step_price'] < $val['salebase_price']){
			array_push($too_low_arr,$val['step_id']);
		}
		elseif($val['step_price'] > $val['shop_price'])
		{
			array_push($too_high_arr,$val['step_id']);
		}
	}
	
	if(count($too_low_arr)){//如果有过低的价格
		$result = array('low' => $too_low_arr,'result' => 'fall');		
		make_json_result($result);
	}elseif(count($too_high_arr) ){ // 只要要有高于商店售价的 那么返回需要也高于商店售价的
		$diff_array = array_diff($step_arr,$too_high_arr); 
		$tmp2 = array();
		if(count($diff_array)){//有不高于售价的
			foreach($diff_array AS $key =>$val){
				array_push($tmp2,$val);
			}
			$result = array('high' => $tmp2, 'result' => 'fall');		
			make_json_result($result);
		}else{//全是高于售价
			$result = array('result' => 'pass' );		
			make_json_result($result);
		}
		
	}else{
		$result = array('result' => 'pass' );		
		make_json_result($result);
	}

    //make_json_result($result);
	
}

/* AJAX 将订单通过 */
elseif ($_REQUEST['act'] == 'confirm_pass')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
	$filters = $json->decode($_GET['JSON']);
	$order_id = intval($filters->order_id);
	$act = $filters->act;
	/*删除*/
	
	$sql = "UPDATE " . $ecs->table('step_order') ." SET is_public = 1". " WHERE order_id = '$order_id' LIMIT 1";
	$GLOBALS['db']->query($sql);
	
	$l_array = get_value_form_location($filters->location_array);
	
	//$added_location = "?act=show";
	
	$added_location = make_location_for_add_step($l_array,$order_id,$act);//为了切换order添加step
	
	$arr = array('added_location' => $added_location);

    make_json_result($arr);
	
}

/* AJAX 将订单通过 */
elseif ($_REQUEST['act'] == 'exe_order')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
	$filters = $json->decode($_GET['JSON']);
	$order_id = intval($filters->order_id);
	$act = $filters->act;
	
	$sql = "UPDATE " . $ecs->table('step_order') ." SET is_exe = 1". " WHERE order_id = '$order_id' LIMIT 1";
	$GLOBALS['db']->query($sql);
	
	//从配单到采购单
	make_purchase($order_id);
	
	$l_array = get_value_form_location($filters->location_array);
		
	$added_location = make_location_for_add_step($l_array,$order_id,$act);//为了切换order添加step
	
	$arr = array('added_location' => $added_location);

    make_json_result($arr);
	
}
/* AJAX 将订单设置为标准配单 */
elseif ($_REQUEST['act'] == 'model_order')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
	$filters = $json->decode($_GET['JSON']);
	$order_id = intval($filters->order_id);
	$act = $filters->act ;
	
	/*删除*/
	if($act == 'done'){
	    $exc_order->edit("is_model = '1'", $order_id);
		$arr = array('is_model' => $act);
	}elseif($act == 'cancel'){
		$exc_order->edit("is_model = '0'", $order_id);
		$arr = array('is_model' => $act);
	}
    make_json_result($arr);
}

/* AJAX 将订单放入回收站 */
elseif ($_REQUEST['act'] == 'trash_order')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
	$filters = $json->decode($_GET['JSON']);
	$order_id = intval($filters->order_id);
	$act = intval($filters->act);
	
	/*删除*/
	
	$sql = "UPDATE " . $ecs->table('step_order') ." SET is_show = 0". " WHERE order_id = '$order_id' LIMIT 1";
	$GLOBALS['db']->query($sql);
	
	//$l_array = get_value_form_location($filters->location_array);
	
	$added_location = "?act=show";
	
	//$added_location = make_location_for_add_step($l_array,$order_id,$act);//为了切换order添加step
	
	$arr = array('added_location' => $added_location);

    make_json_result($arr);
	
}

/***********************************
 *AJAX 页面内修改 即点即改
 ************************************
*/
// 修改订单tag
elseif ($_REQUEST['act'] == 'edit_order_tag')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $order_id = intval($_POST['id']);
    $order_tag = json_str_iconv(trim($_POST['val']));

	
    if ($exc_order->edit("order_tag = '$order_tag'", $order_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($order_tag));

    }
	
}
// 修改订单名字
elseif ($_REQUEST['act'] == 'edit_order_name')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $order_id = intval($_POST['id']);
    $order_name = json_str_iconv(trim($_POST['val']));

	
    if ($exc_order->edit("order_name = '$order_name'", $order_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($order_name));

    }
	
}
// 修改订单 wire_fee
elseif ($_REQUEST['act'] == 'edit_wire_fee')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $order_id = intval($_POST['id']);
    $wire_fee = intval(trim($_POST['val']));

	
    if ($exc_order->edit("wire_fee = '$wire_fee'", $order_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($wire_fee));

    }
	
}
// 修改订单 tax_fee
elseif ($_REQUEST['act'] == 'edit_tax_fee')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $order_id = intval($_POST['id']);
    $tax_fee = trim($_POST['val']);

	
    if ($exc_order->edit("tax_fee = '$tax_fee'", $order_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($tax_fee));

    }
	
}

// 修改订单 travel_fee
elseif ($_REQUEST['act'] == 'edit_travel_fee')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $order_id = intval($_POST['id']);
    $travel_fee = intval(trim($_POST['val']));

	
    if ($exc_order->edit("travel_fee = '$travel_fee'", $order_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($travel_fee));

    }
	
}

// 修改订单 training_fee
elseif ($_REQUEST['act'] == 'edit_training_fee')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $order_id = intval($_POST['id']);
    $training_fee = intval(trim($_POST['val']));

	
    if ($exc_order->edit("training_fee = '$training_fee'", $order_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($training_fee));

    }
	
}

// 修改单项价格
elseif ($_REQUEST['act'] == 'edit_step_price')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $step_id = intval($_POST['id']);
    $step_price = json_str_iconv(trim($_POST['val']));
	
    if ($exc->edit("step_price = '$step_price'", $step_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($step_price));

    }
	
}

// 修改单项数量
elseif ($_REQUEST['act'] == 'edit_step_count')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $step_id = intval($_POST['id']);
    $step_count = json_str_iconv(trim($_POST['val']));
	
    if ($exc->edit("step_count = '$step_count'", $step_id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($step_count));

    }
	
}


/*------------------------------------------------------ */
//-- 开始为 配单头部操作部分 searchCRM
/*------------------------------------------------------ */


// AJAX 搜索CRM 的 客户信息
elseif ($_REQUEST['act'] == 'searchCRM')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;
    $filters = $json->decode($_GET['JSON']);
	$agency_name = $filters->agency_name;
	$contact_name = $filters->contact_name;
	$order_id = $filters->order_id;

	if($agency_name){
		$sql = "SELECT agency_id,agency_name FROM " .$GLOBALS['ecs']->table('agency'). " WHERE agency_name LIKE '%" . mysql_like_quote($agency_name) . "%'".
				" AND user_id = $_SESSION[user_id]" ;
	    $agency_name_list = $db->getAll($sql);
		$smarty->assign('agency_name_list',       $agency_name_list);
	}
	
	if($contact_name){
		$sql_c = "SELECT user_id,user_name FROM " .$GLOBALS['ecs']->table('agency_contact'). " WHERE user_rank = ".rank_agency." AND user_name LIKE '%" . mysql_like_quote($contact_name) . "%'" . " AND user_id = $_SESSION[user_id]" ;
	    $contact_name_list = $db->getAll($sql_c);
		$smarty->assign('contact_name_list',       $contact_name_list);
	}
	
	
	clear_cache_files();
	
	$smarty->assign('order_info',     	get_order_info($order_id));    
	make_json_result($smarty->fetch('library/so_page_header.lbi'));

    //make_json_result($agency_name_list);
	

}

// AJAX 获得客户信息
elseif ($_REQUEST['act'] == 'get_custom_info')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;

    $filters = $json->decode($_GET['JSON']);

	$arr = get_json_custom_info($filters);

    make_json_result($arr);
}

// 指定客户的ID
elseif ($_REQUEST['act'] == 'assign_contact_id')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
	$json = new JSON;
    $filters = $json->decode($_GET['JSON']);
	$contact_id = $filters->contact_id;
	$contact_name = $filters->contact_name;
	$order_id = $filters->order_id;

    	
	if($order_id){
		if ($exc_order->edit("contact_id = '$contact_id'", $order_id) && $order_id)
	    {
			$exc_order->edit("agency_id = '0'", $order_id);
			$exc_contract->edit("contact_id = '$contact_id'", $order_id);
			clear_cache_files();	
			//刷新需要显示的值
			$smarty->assign('order_info',     	get_order_info($order_id));    
			make_json_result($smarty->fetch('library/so_page_header.lbi'));
		}
	}else{
		$order_info = array("agency_name"=>"个人","agency_id"=>"9","contact_name"=>$contact_name,"contact_id"=>$contact_id,"order_name"=>$contact_name."项目方案");
		$smarty->assign('order_info',     	$order_info);
		make_json_result($smarty->fetch('library/so_page_header.lbi'));
	}
	
}

// 修改客户的ID
elseif ($_REQUEST['act'] == 'edit_contact_id')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $order_id = intval($_POST['id']);
    $contact_id = json_str_iconv(trim($_POST['val']));
	$agency_id = json_str_iconv(trim($_POST['agency_val']));
	
	$ur_array = array(rank_agency);//架构 正式客户 inc_constant.php
	$custom_list = get_contact_list($agency_id); //架构 客户 联系人
	$smarty->assign('custom_list',      $custom_list);
	
	if($order_id){
		if ($exc_order->edit("contact_id = '$contact_id'", $order_id) && $order_id)
	    {
			$exc_contract->edit("contact_id = '$contact_id'", $order_id);
			clear_cache_files();	
			//刷新需要显示的值
			$smarty->assign('order_info',     	get_order_info($order_id));    
			make_json_result($smarty->fetch('library/so_page_header.lbi'));
		}
	}
	
}


/* 指定机构 并且刷新联系人 */
elseif ($_REQUEST['act'] == 'refresh_agency_contact')
{
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
	$json = new JSON;
    $filters = $json->decode($_GET['JSON']);
	$agency_id = $filters->agency_id;
	$agency_name = $filters->agency_name;
	$order_id = $filters->order_id;
	
	$ur_array = array(rank_agency);//架构 正式客户 inc_constant.php
	$custom_list = get_contact_list($agency_id); //架构 客户 联系人
	$smarty->assign('custom_list',      $custom_list);	
    	
	if($order_id){
		if ($exc_order->edit("agency_id = '$agency_id'", $order_id))
	    {
			$exc_contract->edit("agency_id = '$agency_id'", $order_id);
			clear_cache_files();	
			//刷新需要显示的值
			$smarty->assign('order_info',     	get_order_info($order_id));    
			make_json_result($smarty->fetch('library/so_page_header.lbi'));
		}
	}else{
		$order_info = array("agency_name"=>$agency_name,"agency_id"=>$agency_id,"order_name"=>$agency_name."项目方案");
		$smarty->assign('order_info',     	$order_info);
		make_json_result($smarty->fetch('library/so_page_header.lbi'));
		
	}
}

/*------------------------------------------------------ */
//-- 结束为 配单头部操作部分 searchCRM
/*------------------------------------------------------ */


/* 复制配单*/
elseif($_REQUEST['act'] == 'copy_order')
{
	/* 如果页面没有被缓存则重新获取页面的内容 */
	$order_sql = "SELECT * FROM".$ecs->table('step_order')."WHERE order_id = $order_id ";
	$order_info = $GLOBALS['db']->getRow($order_sql);
	//获得最新的 order_id
	$newest_order_id = get_newest_order_id();
	
	$order_info['order_id'] = $newest_order_id; 
	$order_info['user_id'] = $_SESSION['user_id'];//替换user_id
	$order_info['add_time'] = gmtime();
	$order_info['is_public'] = 0;
	$order_info['is_model'] = 0;
	
    $GLOBALS['db']->autoExecute($ecs->table('step_order'), $order_info, 'INSERT');
    

	$step_sql = "SELECT * FROM".$ecs->table('step_db')."WHERE order_id = $order_id ";
	$step_info = $GLOBALS['db']->getAll($step_sql);
	foreach($step_info AS $val){
		$val['step_id'] = ''; //清空 step_id
		$val['order_id'] =  $newest_order_id; //
		$val['user_id'] = $_SESSION['user_id'];//替换user_id
		$val['add_time'] = gmtime();
		$GLOBALS['db']->autoExecute($ecs->table('step_db'), $val, 'INSERT');	    
	}

	show_message($_LANG['copy_succeed'], $_LANG['profile_lnk'], 'solution_operate.php?act=show', 'info');
	

	
}



/* 预览邮件 */
elseif ($_REQUEST['act'] == 'preview_order')
{
	$position = assign_ur_here($cat_id, $brand_name);
    $smarty->assign('page_title',       $position['title']);    // 页面标题 在线配单
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

	$smarty->assign('action',     $_REQUEST['act']);
	
    $smarty->assign('order_info',     	get_order_info($order_id)); //订单信息
    $order_detail 	= get_step_list($order_id); //获得一个订单下的详细产品
	//重新整理 $order_detail['orders'] 加入分类信息
	$cat_array = get_cat_array($order_detail['orders']);
    $smarty->assign('order_detail',    $cat_array);
//    $smarty->assign('order_detail',    $order_detail['orders']);
	
	$smarty->display('preview_order.dwt');
	
}

/* 发送邮件*/
elseif ($_REQUEST['act'] == 'send_mail')
{
	$order_id = $_POST['order_id'];

	$smarty->assign('action',     $_REQUEST['act']);
	
	$order_info = get_order_info($order_id);
    $smarty->assign('order_info',     	$order_info);

    $order_detail 	= get_step_list($order_id); //获得一个订单下的详细产品
	//重新整理 $order_detail['orders'] 加入分类信息
	$cat_array = get_cat_array($order_detail['orders']);
    $smarty->assign('order_detail',    $cat_array);
    $smarty->assign('cfg', $_CFG);
    $smarty->assign('mail_remark', $_POST['mail_remark']);



	// capture the output
	$show_price = isset($_REQUEST['show_price']) && intval($_REQUEST['show_price']) > 0 ? intval($_REQUEST['show_price']) : 0;
	
	if($show_price){
		$order_list = $smarty->fetch('solution_mail_templete.htm');
	}else{
		$order_list = $smarty->fetch('solution_mail_templete_no_price.htm');
	}
	
	//联系人信息
	$contact_info = get_contact_info_detail($order_info['contact_id']);
	
	$subject = $order_info['agency_name']."-".$order_info['contact_name']."-".$order_info['order_name']."-配单信息";
	
	$mail_from = $_SESSION['email'];
	
	//echo $customer_name.$customer_email.$subject.$mail_from;
    
	if (send_solution_order_email($contact_info['contact_name'],$contact_info['contact_email'],$subject,$order_list,$mail_from))
    {
        // 用户没有登录
		//同时给自己回复一封邮件
		if(send_solution_order_email($contact_info['contact_name'],$mail_from,$subject,$order_list,$mail_from)){
			show_message("发送成功");
		}
		
    }else{
	    show_message("发送失败");
	}
	

}

/* 合同 */
elseif ($_REQUEST['act'] == 'order_contract')
{
    /* 如果页面没有被缓存则重新获取页面的内容 */

    $children = get_children($cat_id);

    $cat = get_cat_info($cat_id);   // 获得分类的相关信息

    $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));
    $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
    $smarty->assign('cat_style',   htmlspecialchars($cat['style']));
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
    
    //assign_template('c', array($cat_id));
	
    $position = assign_ur_here($cat_id, $brand_name);
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

    //$smarty->assign('categories',       get_categories_tree($cat_id)); // 同级分类树
	$smarty->assign('categories',     	get_categories_tree()); // 分类树
    $smarty->assign('sub_categories',   get_sub_categories_tree($cat_id,$order_id)); // 子分类树
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('category',         $cat_id);
	$smarty->assign('category_name',    get_name_form_ID($cat_id));
    $smarty->assign('brand',         $brand);
    $smarty->assign('price_max',        $price_max);
    $smarty->assign('price_min',        $price_min);
    $smarty->assign('filter_attr',      $filter_attr);
    $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-c$cat_id.xml" : 'feed.php?cat=' . $cat_id); // RSS URL


    $brand_list = get_brands_for_solution($cat_id, $order_id, 'solution_operate','1','0'); // all 60 推荐 brands
    $smarty->assign('brand_list',      $brand_list);

    $count = get_cagtegory_goods_count($children, $brand, $price_min, $price_max, $ext);
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    if ($page > $max_page)
    {
        $page = $max_page;
    }

    $smarty->assign('category',         $cat_id);

	$smarty->assign('action',     $_REQUEST['act']);
	
	$zh_today = date('Y \年 m \月 j \日',gmtime());     
	$smarty->assign('zh_today',   $zh_today   );
	$exc_contract->edit("order_sign_day = '$zh_today'", $order_id);

	$my_order_list = get_my_order_list($_SESSION['user_id']);	//正在配置的的order
  	$smarty->assign('my_order_list',       $my_order_list); //for operate_my_solution.lbi 文件

	$my_public_list = get_my_order_list($_SESSION['user_id'],1); //通过价格审核的order
  	$smarty->assign('my_public_list',       $my_public_list); //for operate_my_solution.lbi 文件
	
	$my_exe_list = get_my_order_list($_SESSION['user_id'],1,1); //通过执行的
  	$smarty->assign('my_exe_list',       $my_exe_list); //for operate_my_solution.lbi 文件
	
	
	$smarty->assign('full_page',        '1');  // 当前位置
	
	
	$smarty->assign('contract',     	get_contract_info($order_id));

    $consignee = get_consignee($_SESSION['user_id']);
    $smarty->assign('consignee',    $consignee);

    $order_detail 	= get_step_list($order_id); //获得一个订单下的详细产品
	//重新整理 $order_detail['orders'] 加入分类信息
	$cat_array = get_cat_array($order_detail['orders']);
    $smarty->assign('order_detail',    $cat_array);
//  $smarty->assign('order_detail',    $order_detail['orders']);

    $smarty->assign('filter',       	$order_detail['filter']);
    $smarty->assign('record_count', 	$order_detail['record_count']);
    $smarty->assign('page_count',   	$order_detail['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

	$smarty->display('solution_order_detail.dwt');
	
}



/*------------------------------------------------------ */
//-- 开始为合同 AJAX 修改的部分
/*------------------------------------------------------ */

// 修改合同编号
elseif ($_REQUEST['act'] == 'edit_contract_sn')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));
	
    if ($exc_contract->edit("contract_sn = '$val'", $id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($val));

    }
	
}
// 修改合同名字
elseif ($_REQUEST['act'] == 'edit_contact_name')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));
	
    if ($exc_contract->edit("contact_name = '$val'", $id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($val));

    }
	
}

// 修改合同客户头衔
elseif ($_REQUEST['act'] == 'edit_contact_title')
{   
	
    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));
	
    if ($exc_contract->edit("contact_title = '$val'", $id))
    {
        clear_cache_files();
		
        make_json_result(stripslashes($val));

    }
	
}
// 修改合同名字头衔
elseif ($_REQUEST['act'] == 'edit_contact_address')
{   
	
    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));
	
    if ($exc_address->edit("address = '$val'", $id))
    {
        clear_cache_files();
        make_json_result(stripslashes($val));
    }
	
}

// 修改合同业务头衔
elseif ($_REQUEST['act'] == 'edit_user_title')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));
	
    if ($exc_contract->edit("user_title = '$val'", $id))
    {
        clear_cache_files();
        make_json_result(stripslashes($val));
    }
	
}
// 修改合同名
elseif ($_REQUEST['act'] == 'edit_contract_name')
{   
	include_once(ROOT_PATH . 'includes/cls_json.php');
	
    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));
	
    if ($exc_contract->edit("contract_name = '$val'", $id))
    {
        clear_cache_files();
        make_json_result(stripslashes($val));
    }
	
}


// 修改合同总金额
elseif ($_REQUEST['act'] == 'edit_order_amount')
{   
	
    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));
	
    if ($exc_contract->edit("order_amount = '$val'", $id))
    {
        clear_cache_files();
        make_json_result(stripslashes($val));
    }
	
}

// 修改合同签订日期
elseif ($_REQUEST['act'] == 'edit_order_sign_day')
{   
	
    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));
	
    if ($exc_contract->edit("order_sign_day = '$val'", $id))
    {
        clear_cache_files();
        make_json_result(stripslashes($val));
    }
	
}



/*------------------------------------------------------ */
//-- 结束为合同 AJAX 修改的部分
/*------------------------------------------------------ */


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



/**
 * 获得分类下的商品
 *
 * @access  public
 * @param   string  $children
 * @return  array
 */
function category_get_goods($cat_id, $brand)
{	
    $filter['brand'] = empty($_REQUEST['brand']) ? 0 : intval($_REQUEST['brand']);
	$filter['category'] = empty($_REQUEST['category']) ? 0 : intval($_REQUEST['category']);
	
    $filter['goods_name'] = empty($_REQUEST['goods_name']) ? '' : trim($_REQUEST['goods_name']);
//    $filter['min'] = empty($_REQUEST['min']) ? 0 : intval($_REQUEST['min']);
//    $filter['min'] = empty($_REQUEST['min']) ? 0 : intval($_REQUEST['min']);

	$filter['start_price'] = empty($_REQUEST['start_price']) ? 0 : intval($_REQUEST['start_price']);
	$filter['start_price'] = empty($_REQUEST['start_price']) ? 0 : intval($_REQUEST['start_price']);
    $filter['end_price'] = empty($_REQUEST['end_price']) ? 1000000 : intval($_REQUEST['end_price']);
    
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	if($filter['category']){
		$children = get_children($filter['category']);
		$where = "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND "."g.is_delete = 0 AND $children ";
	}else{
		$where = "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND "."g.is_delete = 0 ";
	    
	}
//    $where = "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND "."g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';
	
    if ($filter['brand'])
    {
        $where .=  "AND g.brand_id=$brand ";
    }
	
	if ($filter['goods_name'])
    {
        $where .= " AND g.goods_name LIKE '%" . mysql_like_quote($filter['goods_name']) . "%'";
    }

	if ($filter['start_price'] || $filter['end_price'] )
    {
        $where .= " AND g.shop_price >= '$filter[start_price]'";
		$where .= " AND g.shop_price <= '$filter[end_price]'";
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
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('goods') ." AS g " . $where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
    
    /* 获得商品列表 */
    $sql = 'SELECT g.goods_id, g.part_number, g.goods_name, g.goods_name_style, g.market_price, g.salebase_price, g.is_new, g.is_best, g.is_hot, g.goods_number, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
	        "$where ORDER BY g.is_best DESC,g.goods_number DESC, $filter[sort_by] $filter[sort_order] ".
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";	
	//	echo $sql;
	
	$row = $GLOBALS['db']->getAll($sql);
	//print_r($row);

    $arr = array();
	foreach ($row AS $key => $value)
    {
        if ($value['promote_price'] > 0)
        {
            $promote_price = bargain_price($value['promote_price'], $value['promote_start_date'], $value['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        /* 处理商品水印图片 
        $watermark_img = '';

        if ($promote_price != 0){
            $watermark_img = "watermark_promote_small";
        }else{
			if($row['is_best'] != 0){
				$watermark_img = "watermark_best_small";
			}else{
				if($row['is_hot'] != 0){
					$watermark_img = 'watermark_hot_small';
				}else{
					if($row['is_new'] != 0){
						$watermark_img = "watermark_new_small";
					}
				}
			}
		}

        if ($watermark_img != '')
        {
            $arr[$value['goods_id']]['watermark_img'] =  $watermark_img;
        }
		*/
		
		if ($value['is_best'] != 0)
        {
			$arr[$value['goods_id']]['skpi'] =  "skpi";
        }

        $arr[$value['goods_id']]['goods_id']         = $value['goods_id'];
        $arr[$value['goods_id']]['part_number']      = $value['part_number'];

        $arr[$value['goods_id']]['goods_name']       = $value['goods_name'];

        $arr[$value['goods_id']]['goods_number']     = $value['goods_number'];
        $arr[$value['goods_id']]['goods_brief']      = $value['goods_brief'];
        $arr[$value['goods_id']]['goods_style_name'] = add_style($value['goods_name'],$value['goods_name_style']);
        $arr[$value['goods_id']]['market_price']     = price_format($value['market_price']);
        $arr[$value['goods_id']]['salebase_price']   = price_format($value['salebase_price']);
        $arr[$value['goods_id']]['salebase_price_num']= $value['salebase_price'];
        $arr[$value['goods_id']]['shop_price']       = price_format($value['shop_price']);
        $arr[$value['goods_id']]['shop_price_num']   = $value['shop_price'];
        $arr[$value['goods_id']]['type']             = $value['goods_type'];
        $arr[$value['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$value['goods_id']]['goods_thumb']      = empty($value['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $value['goods_thumb'];
        $arr[$value['goods_id']]['goods_img']        = empty($value['goods_img'])   ? $GLOBALS['_CFG']['no_picture'] : $value['goods_img'];
		$arr[$value['goods_id']]['comment_rank']     = get_goods_rank_common($value['goods_id']);
        $arr[$value['goods_id']]['url']              = build_uri('goods', array('gid'=>$value['goods_id']), $value['goods_name']);
    }
	$result = array('goods' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql'=>'cat'.$_REQUEST['category'].$sql);
    return $result;
}

/**
 * 获得分类下的商品总数
 *
 * @access  public
 * @param   string     $cat_id
 * @return  integer
 */
function get_cagtegory_goods_count($children, $brand = 0, $min = 0, $max = 0, $ext='')
{
    $where  = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';

    if ($brand > 0)
    {
        $where .=  " AND g.brand_id = $brand ";
    }

    if ($min > 0)
    {
        $where .= " AND g.shop_price >= $min ";
    }

    if ($max > 0)
    {
        $where .= " AND g.shop_price <= $max ";
    }

    /* 返回商品总数 */
    return $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g WHERE $where $ext");
}

/**
 * 取得最近的上级分类的grade值
 *
 * @access  public
 * @param   int     $cat_id    //当前的cat_id
 *
 * @return int
 */
function get_parent_grade($cat_id)
{
    static $res = NULL;

    if ($res === NULL)
    {
        $sql = "SELECT parent_id, cat_id, grade ".
               " FROM " . $GLOBALS['ecs']->table('category');
        $res = $GLOBALS['db']->getAllCached($sql);
    }

    if (!$res)
    {
        return 0;
    }

    $parent_arr = array();
    $grade_arr = array();

    foreach ($res as $val)
    {
        $parent_arr[$val['cat_id']] = $val['parent_id'];
        $grade_arr[$val['cat_id']] = $val['grade'];
    }

    while ($parent_arr[$cat_id] >0 && $grade_arr[$cat_id] == 0)
    {
        $cat_id = $parent_arr[$cat_id];
    }

    return $grade_arr[$cat_id];

}

?>
