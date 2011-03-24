<?php

/**
 * SINEMALL 专题前台
 * ============================================================================
 * @author:     mactive <mactive@gmail.com>
 * @version:    v.1.51
 * ---------------------------------------------
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');
require_once(ROOT_PATH . 'admin/includes/lib_main.php');

require_once(ROOT_PATH . 'includes/lib_solution.php');
//require(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/solution.php');

require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/solution.php');

// 老配单系统
if ($_REQUEST['act'] == 'old_peidan')
{
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
	
	$smarty->display('solution_all.dwt');
	exit;
}


/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */
if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = "show";
	$smarty->assign('act_step',       $_REQUEST['act']);

}

$smarty->assign('act_step', $_REQUEST['act']);
$smarty->assign('lang',       $_LANG);

$user_id = $_SESSION['user_id'];
$solution_id  = empty($_REQUEST['solution_id']) ? 0 : intval($_REQUEST['solution_id']);	
$order_id  = empty($_REQUEST['order_id']) ? 0 : intval($_REQUEST['order_id']);	

//获得 需要 post 的数组列表 
$post_step_list = post_solution_list($solution_id);
$step_number = '';
foreach($post_step_list AS $key=> $val)
{
	$step_number .= $val['solution_id'].",";
}
$smarty->assign('step_number',       $step_number);

$client_rank_name = sine_client;
$client_list = get_users_by_rankname($client_rank_name);
$smarty->assign('client_list',    $client_list);

$smarty->assign('sine_client', sine_client);



/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'show')
{

	assign_template();
	assign_dynamic('solution');
//	$solution_id  = empty($_REQUEST['solution_id']) ? 0 : intval($_REQUEST['solution_id']);	
	//单条solution_order 的信息
	$solution = get_single_solution_info($solution_id);
	
	$first_child = get_first_child($solution_id);
	
	//显示order_id 的详细信息 驱动页面上的ajax 信息显示
	$order_info = get_solution_order_detail($order_id);
	
	$single_order_info = get_solution_order_info($order_id);
	$smarty->assign('order_info', $order_info);
	$smarty->assign('single_order_info', $single_order_info);
	
	/* 获取分类列表 */
	$post_solution_list = post_solution_list($solution_id);

	//print_r($first_child);
	$sort_goods_arr = array();

	/* 模板赋值 */
	$position = assign_ur_here();
	$ur_here  = $position['ur_here'] .' <li> ' .$solution['solution_name']. '</li>';
	$smarty->assign('page_title',       $position['title']);       // 页面标题
	$smarty->assign('ur_here',          $ur_here);     // 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径

	$smarty->assign('solution', $solution);
	$smarty->assign('solution_id', $solution_id);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('first_child', $first_child);
	$smarty->assign('sort_goods_arr',   $sort_goods_arr);          // 商品列表
	$smarty->assign('post_solution_list',       $post_solution_list);                   // 专题信息
	$smarty->assign('is_show',       '0');  	// 专题信息
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{

	assign_template();
	assign_dynamic('solution');
//	$solution_id  = empty($_REQUEST['solution_id']) ? 0 : intval($_REQUEST['solution_id']);	
	//单条solution_order 的信息
	$solution = get_single_solution_info($solution_id);
	
	$first_child = get_first_child($solution_id);
		
	/* 获取分类列表 */
	$post_solution_list = post_solution_list($solution_id);

	//print_r($first_child);
	$sort_goods_arr = array();

	/* 模板赋值 */
	$position = assign_ur_here();
	$ur_here  = $position['ur_here'] .' <li> ' .$solution['solution_name']. '</li>';
	$smarty->assign('page_title',       $position['title']);       // 页面标题
	$smarty->assign('ur_here',          $ur_here);     // 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径

	$smarty->assign('solution', $solution);
	$smarty->assign('solution_id', $solution_id);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('first_child', $first_child);
	$smarty->assign('sort_goods_arr',   $sort_goods_arr);          // 商品列表
	$smarty->assign('post_solution_list',       $post_solution_list);                   // 专题信息
	$smarty->assign('is_show',       '1');  	// 专题信息
	
}


//插入数据
elseif ($_REQUEST['act'] == 'insert')
{
//	$solution_id  = empty($_REQUEST['solution_id']) ? 0 : intval($_REQUEST['solution_id']);	
	
	    /*插入数据*/
		$add_time = gmtime();
	    $sql = "INSERT INTO ".$GLOBALS['ecs']->table('solution_order')."(order_id,user_id, contact_id, add_time, solution_id, order_note) ".
	           "VALUES ('$order_id', '$user_id', '$_POST[contact_id]', '$add_time', '$solution_id', '$_POST[order_note]')";
		//echo $sql;
	    $GLOBALS['db']->query($sql);
	
		$new_order_id = get_solition_order_id();
		$post_solution_list = post_solution_list($solution_id);
		
		foreach($post_solution_list AS $key => $val )
		{
			//$val['step_id']
			$select_id = "s_".$val['solution_id'];
			$order_price = $select_id."_order_price";
			$action_note = $select_id."_action_note";
			$goods_count = $select_id."_goods_count";
			$serial_number = $select_id."_serial_number";
			$sql = 	"INSERT INTO ".$GLOBALS['ecs']->table('solution_order_detail').
					"(order_id,step_id, goods_id, order_price, action_note, goods_count) ".
		           	"VALUES ('$new_order_id', '$val[step_id]', '$_POST[$select_id]','$_POST[$order_price]','$_POST[$action_note]','$_POST[$goods_count]')";
			//echo $sql;
		    $GLOBALS['db']->query($sql);
			
		}
		
		// 计算总价写入order写入数据库
		$sql = "SELECT SUM(`order_price` * `goods_count`) FROM ".$GLOBALS['ecs']->table('solution_order_detail').
				" WHERE order_id  = $new_order_id ";
		echo $sql;
	    $order_amount = $GLOBALS['db']->getOne($sql);

		$sql = "UPDATE ".$GLOBALS['ecs']->table('solution_order').
				" SET `order_amount`  = '$order_amount' ".
				" WHERE order_id = $new_order_id  LIMIT 1";
		//echo $sql;
	    $GLOBALS['db']->query($sql);
	    
	    
		
		/*
		$new_order_id = get_solition_order_id();
		$sql = "INSERT INTO ".$GLOBALS['ecs']->table('solution_order_detail')."(order_id,step_id, goods_id) ".
	           "VALUES ('$new_order_id', '$user_id', '$user_id')";
		//echo $sql;
	    $GLOBALS['db']->query($sql);
	    */		
		
		
		/* 刷新购物车 */
	    ecs_header("Location: user.php?act=solution_list"."\n");
	    exit;

}

elseif ($_REQUEST['act'] == 'edit')
{

	assign_template();
	assign_dynamic('solution');
	
//	$solution_id  = empty($_REQUEST['solution_id']) ? 0 : intval($_REQUEST['solution_id']);	

	//单条solution_order 的信息
	$solution = get_single_solution_info($solution_id);
	
	
	
	$first_child = get_first_child($solution_id);
	
	//显示order_id 的详细信息 驱动页面上的ajax 信息显示
	$order_info = get_solution_order_detail($order_id);
	
	$single_order_info = get_solution_order_info($order_id);
	$smarty->assign('order_info', $order_info);
	$smarty->assign('single_order_info', $single_order_info);

	$client_info = get_user_info_detail($single_order_info['contact_id']);// 通过 contact_id 获得客户详细信息
	$smarty->assign('client_info',       $client_info);       // 页面标题
	
	/* 获取分类列表 */
	$post_solution_list = post_solution_list($solution_id);

	//print_r($first_child);
	$sort_goods_arr = array();

	/* 模板赋值 */
	$position = assign_ur_here();
	$smarty->assign('page_title',       $position['title']);       	// 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);    	// 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     	// 图片路径

	$smarty->assign('solution', $solution);
	$smarty->assign('solution_id', $solution_id);
	$smarty->assign('order_id', $order_id);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('first_child', $first_child);
	$smarty->assign('sort_goods_arr',   $sort_goods_arr);          		// 商品列表
	$smarty->assign('post_solution_list',       $post_solution_list);  	// 专题信息
	$smarty->assign('is_show',       '0');  	// 专题信息
}


//修改数据
elseif ($_REQUEST['act'] == 'update')
{
	$order_id  = intval($_POST['order_id']);
	$solution_id  = intval($_POST['solution_id']);
	
	
	$post_solution_list = post_solution_list($solution_id);

	/*插入数据*/
	
	$sql = "UPDATE " . $ecs->table('solution_order') .
			" SET contact_id = '$_POST[contact_id]', " .			
			"order_note		= '$_POST[order_note]' ".
            "WHERE  order_id= '$order_id' LIMIT 1";

    $GLOBALS['db']->query($sql);

	
	foreach($post_solution_list AS $key => $val )
	{
		//$val['step_id']
		$select_id = "s_".$val['solution_id'];
		$order_price = $select_id."_order_price";
		$action_note = $select_id."_action_note";
		$goods_count = $select_id."_goods_count";
		$serial_number = $select_id."_serial_number";
		
		$sql = "UPDATE ".$GLOBALS['ecs']->table('solution_order_detail').
				" SET `goods_id`  = '$_POST[$select_id]', ".
				"order_price 	  = '$_POST[$order_price]', ".
				"action_note 	  = '$_POST[$action_note]', ".
				"goods_count 	  = '$_POST[$goods_count]' ".
				" WHERE order_id = $order_id AND step_id = $val[step_id]  LIMIT 1";
		echo $sql;
		
	    $GLOBALS['db']->query($sql);
		
	}
	
	
	// 计算总价写入order写入数据库
	$sql = "SELECT SUM(`order_price` * `goods_count`) FROM ".$GLOBALS['ecs']->table('solution_order_detail').
			" WHERE order_id  = $order_id ";
	echo $sql;
    $order_amount = $GLOBALS['db']->getOne($sql);

	$sql = "UPDATE ".$GLOBALS['ecs']->table('solution_order').
			" SET `order_amount`  = '$order_amount' ".
			" WHERE order_id = $order_id  LIMIT 1";
	//echo $sql;
    $GLOBALS['db']->query($sql);
    
    
	
	/*
	$new_order_id = get_solition_order_id();
	$sql = "INSERT INTO ".$GLOBALS['ecs']->table('solution_order_detail')."(order_id,step_id, goods_id) ".
           "VALUES ('$new_order_id', '$user_id', '$user_id')";
	//echo $sql;
    $GLOBALS['db']->query($sql);
    */		
	
	
	/* 刷新购物车 */
    ecs_header("Location: solution.php?act=edit&solution_id=".$solution_id."&order_id=".$order_id."\n");
    exit;

}


//修改数据
elseif ($_REQUEST['act'] == 'remove')
{
	$order_id  = $order_id  = empty($_REQUEST['order_id']) ? 0 : intval($_REQUEST['order_id']);	
	
	
    /*删除*/
	$sql = "UPDATE ".$GLOBALS['ecs']->table('solution_order').
			" SET `is_delete`  = '1' ".
			" WHERE order_id = $order_id  LIMIT 1";
	$GLOBALS['db']->query($sql);
	    
	/* 清除缓存 */
    clear_cache_files();

	$url = 'user.php?act=solution_list';

    ecs_header("Location: $url\n");
    exit;
	
}
//修改数据
elseif ($_REQUEST['act'] == 'delete')
{
	$order_id  = $order_id  = empty($_REQUEST['order_id']) ? 0 : intval($_REQUEST['order_id']);	
	
	
    /*删除*/
	$sql = "DELETE FROM " . $GLOBALS['ecs']->table('solution_order') . " WHERE order_id = '$order_id'";
	$GLOBALS['db']->query($sql);
	
	$sql2 = "DELETE FROM " . $GLOBALS['ecs']->table('solution_order_detail') . " WHERE order_id = '$order_id'";
	$GLOBALS['db']->query($sql2);
    
	/* 清除缓存 */
    clear_cache_files();

	$url = 'user.php?act=solution_list';

    ecs_header("Location: $url\n");
    exit;
	
}

// AJAX 获得商品信息
elseif ($_REQUEST['act'] == 'get_goods_info')
{
	    include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;

	    $filters = $json->decode($_GET['JSON']);

	    $arr = get_json_goods_info($filters);

    make_json_result($arr);
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



//
if(empty($_REQUEST['solution_id']))
{
    include_once(ROOT_PATH . 'includes/lib_solution_operate.php');
	
	assign_template();
	$position = assign_ur_here();
	
	$smarty->assign('page_title',       $position['title']);       // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);     // 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
	$smarty->assign('user_id', $user_id);
	
	//$_CFG['index_solutions_article_cat'] = 14;
	$solution_article = get_articlecat_subcat($_CFG['index_solutions_article_cat']);
	$solution_article = add_order_to_solution_array($solution_article); //增加订单数组  按cat.keywords 搜索 order.tag_name
	$smarty->assign('solution_article', $solution_article);
	
	//$_CFG['index_cases_article_cat'] = 12;//解决方案资料主分类
	$case_article = get_articlecat_subcat($_CFG['index_cases_article_cat'],20,110,'DESC');
	$smarty->assign('case_article', $case_article);



	$smarty->display('solution_and_case.dwt');
	exit;
}

/* 显示模板 */
$smarty->display('solution.dwt');


?>