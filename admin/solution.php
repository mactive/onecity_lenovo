<?php

/**
 * SINEMALL 管理中心品牌管理    $Author: testyang $
 * $Id: brand.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_solution.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc_step = new exchange($ecs->table("solution_step"), $db, 'step_id', 'step_name');
$exc = new exchange($ecs->table("solution"), $db, 'solution_id', 'solution_name');

/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      $_LANG['list_solution']);
    $smarty->assign('action_link',  array('text' => $_LANG['add_solution'], 'href' => 'solution.php?act=add'));
    $smarty->assign('action_link2',  array('text' => $_LANG['all_solution'], 'href' => 'solution.php?act=list_one_solution'));
    $smarty->assign('full_page',    1);

    $solution_list = get_solutionlist(0);

    $smarty->assign('solution_list',    $solution_list['solution']);
    $smarty->assign('filter',       $solution_list['filter']);
    $smarty->assign('record_count', $solution_list['record_count']);
    $smarty->assign('page_count',   $solution_list['page_count']);
    


    assign_query_info();
    $smarty->display('solution_list.htm');
}

/*------------------------------------------------------ */
//-- 商品分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list_one_solution')
{
	$id = intval($_GET['id']);
	
    /* 获取分类列表 */
    $cat_list = solution_list($id, 0, false);

    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['list_one_solution']);
    $smarty->assign('action_link',  array('href' => 'solution.php?act=list', 'text' => $_LANG['list_solution']));
    $smarty->assign('full_page',    1);

    $smarty->assign('cat_list',     $cat_list);
	$smarty->assign('clusters_id',   intval($_GET['id']) );
	

    /* 列表页面 */
    assign_query_info();
    $smarty->display('solution_list_view.htm');
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $solution_list = get_solutionlist(0);

    $smarty->assign('solution_list',    $solution_list['solution']);
    $smarty->assign('filter',       $solution_list['filter']);
    $smarty->assign('record_count', $solution_list['record_count']);
    $smarty->assign('page_count',   $solution_list['page_count']);

    make_json_result($smarty->fetch('solution_list.htm'), '',
        array('filter' => $solution_list['filter'], 'page_count' => $solution_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_child')
{
	$id = intval($_GET['id']);
	$clusters_id = intval($_GET['clusters_id']);
    
    /* 权限判断 */
    admin_priv('slolution_manage');

    $smarty->assign('ur_here',     $_LANG['add_child_step']);
    $smarty->assign('action_link', array('text' => $_LANG['list_solution'], 'href' => 'solution.php?act=list'));
    $smarty->assign('form_action', 'insert_child');

    assign_query_info();

    $smarty->assign('solution_step_list',   get_solution_step_list());
	$smarty->assign('solution_list',   get_solution_list($id));    
	$smarty->assign('solution_id',   $id);    
	$smarty->assign('clusters_id',   $clusters_id);    
    
    $smarty->display('solution_info.htm');
}

elseif ($_REQUEST['act'] == 'insert_child')
{
	$id = intval($_POST['id']);
	$clusters_id = intval($_POST['clusters_id']);
    /*检查品牌名是否重复*/
    admin_priv('slolution_manage');

    $is_only = $exc->is_only_step('solution_name', $_POST['solution_name'],$id);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['solutionname_exist'], stripslashes($_POST['solution_name'])), 1);
    }


     /*处理图片*/
	
    $img_name = basename($image->upload_image($_FILES['solution_logo'],'steplogo'));
	if($img_name == ''){
		$img_name = $_POST['step_logo'];
	}
     /*处理扩展分类*/
	//$json       = new JSON;
    //$tmp_data   = $json->decode($_POST['other_cat']);
    //$other_cat       = serialize($tmp_data);

    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('solution')."( solution_name, solution_desc, solution_logo, parent_id, step_id, sort_step,step_type,clusters_id) ".
           "VALUES ('$_POST[solution_name]', '$_POST[solution_desc]', '$img_name', '$_POST[parent_id]','$_POST[step_id]','$_POST[sort_step]','$_POST[step_type]',$clusters_id)";
    $db->query($sql);

    admin_log($_POST['step_name'],'add','solution');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'solution.php?act=add_child&id='.$id.'&clusters_id='.$clusters_id;

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'solution.php?act=list_one_solution&id='.$clusters_id;

    sys_msg($_LANG['stepadd_succed'], 0, $link);
}


/*------------------------------------------------------ */
//-- 添加品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('slolution_manage');

    $smarty->assign('ur_here',     $_LANG['add_solution']);
    $smarty->assign('action_link', array('text' => $_LANG['list_solution'], 'href' => 'solution.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();

    $smarty->assign('solution_step_list',   get_solution_step_list());
	//$smarty->assign('solution_list',   get_solution_list());
	$smarty->assign('intro_list',   get_intro_list());
    
    
    $smarty->display('solution_info.htm');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查品牌名是否重复*/
    admin_priv('slolution_manage');


    $is_only = $exc->is_only('solution_name', $_POST['solution_name']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['solutionname_exist'], stripslashes($_POST['solution_name'])), 1);
    }


     /*处理图片*/
    $img_name = basename($image->upload_image($_FILES['solution_logo'],'steplogo'));

     /*处理扩展分类*/
	//$json       = new JSON;
    //$tmp_data   = $json->decode($_POST['other_cat']);
    //$other_cat       = serialize($tmp_data);
    $sql = "SELECT MAX(solution_id)  FROM " . $GLOBALS['ecs']->table('solution') ."WHERE 1";
    $res = $GLOBALS['db']->getOne($sql);
	$solution_id = $res + 1;

    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('solution')."(solution_id, solution_name, solution_desc, solution_logo, parent_id, sort_step) ".
           "VALUES ('$solution_id','$_POST[solution_name]', '$_POST[solution_desc]', '$img_name', '$_POST[parent_id]','$_POST[sort_step]')";
    $db->query($sql);

    admin_log($_POST['step_name'],'add','solution');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'solution.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'solution.php?act=list';

    sys_msg($_LANG['stepadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('slolution_manage');
    $sql = "SELECT  * "	.
            "FROM " .$ecs->table('solution'). " WHERE solution_id='$_REQUEST[id]'";
    $solution = $db->GetRow($sql);
	//print_r($solution);

    $smarty->assign('ur_here',     $_LANG['edit_solution']);
    $smarty->assign('action_link', array('text' => $_LANG['solution_list'], 'href' => 'solution.php?act=list'));
    
	$smarty->assign('solution',       $solution);
    $smarty->assign('form_action', 'update');
	
	
    assign_query_info();
    $smarty->display('solution_add.htm');
}
elseif ($_REQUEST['act'] == 'update')
{
    admin_priv('slolution_manage');
    if ($_POST['solution_name'] != $_POST['old_solutionname'])
    {
        /*检查品牌名是否相同*/
        $is_only = $exc->is_only_step('solution_name', $_POST['solution_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['solutionname_exist'], stripslashes($_POST['solution_name'])), 1);
        }
    }


    /* 处理图片 */
    $img_name = basename($image->upload_image($_FILES['solution_logo'],'steplogo'));

    $param = "solution_name = '$_POST[solution_name]',  solution_desc='$_POST[solution_desc]' ";//, other_cat='$_POST[other_cat]'
    if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,solution_logo = '$img_name' ";
    }

    if ($exc->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['solution_name'], 'edit', 'solution');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'solution.php?act=list';
        $note = vsprintf($_LANG['solutionedit_succeed'], $_POST['solution_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 删除品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('slolution_manage');

    $id = intval($_GET['id']);

    /* 删除该品牌的图标 */
    $sql = "SELECT solution_logo FROM " .$ecs->table('solution'). " WHERE solution_id = '$id'";
    $logo_name = $db->getOne($sql);
    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . 'data/steplogo/' .$logo_name);
    }
	
	$exc->drop($id);
	
	$sql = "SELECT solution_id FROM " .$ecs->table('solution'). " WHERE parent_id = '$id'";
    $arr = $db->GetAll($sql);
	foreach($arr AS $key){
		$exc->drop($key);
	}
	
	/* 清除缓存 */
    clear_cache_files();
	
    $url = 'solution.php?act=query&'. str_replace('act=remove', '', $_SERVER['QUERY_STRING']);;

    ecs_header("Location: $url\n");
    exit;
}


/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list_step')
{
    $smarty->assign('ur_here',      $_LANG['list_step']);
    $smarty->assign('action_link',  array('text' => $_LANG['add_step'], 'href' => 'solution.php?act=add_step'));
    $smarty->assign('full_page',    1);

    $step_list = get_steplist();

    $smarty->assign('step_list',    $step_list['step']);
    $smarty->assign('filter',       $step_list['filter']);
    $smarty->assign('record_count', $step_list['record_count']);
    $smarty->assign('page_count',   $step_list['page_count']);

    assign_query_info();
    $smarty->display('solution_step_list.htm');
}

/*------------------------------------------------------ */
//-- 添加品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_step')
{
    /* 权限判断 */
    admin_priv('slolution_manage');

    $smarty->assign('ur_here',     $_LANG['solution_add_step']);
    $smarty->assign('action_link', array('text' => $_LANG['solution_list_step'], 'href' => 'solution.php?act=list_step'));
    $smarty->assign('form_action', 'insert_step');

    assign_query_info();

	$smarty->assign('cat_list',     cat_list(0));
    $smarty->assign('brand_list',   get_brand_list());
	$smarty->assign('intro_list',   get_intro_list());
    
    
    $smarty->display('solution_step_info.htm');
}
elseif ($_REQUEST['act'] == 'insert_step')
{
    /*检查品牌名是否重复*/
    admin_priv('slolution_manage');

    $cat_id = isset($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;
    $is_real = isset($_REQUEST['is_real']) ? intval($_REQUEST['is_real']) : 1;
    $brand_id = isset($_REQUEST['brand_id']) ? intval($_REQUEST['brand_id']) : 0;
    $intro_type = isset($_REQUEST['intro_type']) ? trim($_REQUEST['intro_type']) : 0;
	
	$array = array($cat_id, $brand_id, $intro_type);
	$step_goods = join(",", $array);
	

    $is_only = $exc_step->is_only('step_name', $_POST['step_name']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['brandname_exist'], stripslashes($_POST['brand_name'])), 1);
    }

    /*对描述处理*/
    if (!empty($_POST['brand_desc']))
    {
        $_POST['brand_desc'] = $_POST['brand_desc'];
    }

     /*处理图片*/
    $img_name = basename($image->upload_image($_FILES['step_logo'],'steplogo'));

     /*处理扩展分类*/
	//$json       = new JSON;
    //$tmp_data   = $json->decode($_POST['other_cat']);
    //$other_cat       = serialize($tmp_data);
    

    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('solution_step')."(step_name, step_desc, step_logo, step_goods,other_cat,is_real) ".
           "VALUES ('$_POST[step_name]', '$_POST[step_desc]', '$img_name', '$step_goods', '$other_cat','$is_real')";
    $db->query($sql);

    admin_log($_POST['step_name'],'add','solution_step');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'solution.php?act=add_step';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'solution.php?act=list_step';

    sys_msg($_LANG['stepadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_step')
{
    /* 权限判断 */
    admin_priv('slolution_manage');
    $sql = "SELECT  * "	.
            "FROM " .$ecs->table('solution_step'). " WHERE step_id='$_REQUEST[id]'";
    $step = $db->GetRow($sql);

    $smarty->assign('ur_here',     $_LANG['edit_step']);
    $smarty->assign('action_link', array('text' => $_LANG['list_step'], 'href' => 'solution.php?act=list_step'));
    
	list($cat_id, $brand_id, $intro_type) = explode(",", $step['step_goods']);
	$smarty->assign('cat_id',   $cat_id);
	$smarty->assign('brand_id', $brand_id);
	$smarty->assign('intro_type',   $intro_type);
	
	$smarty->assign('step',       $step);
    $smarty->assign('form_action', 'update_step');
	$smarty->assign('cat_list',   cat_list(0,$cat_id));
    $smarty->assign('brand_list', get_brand_list());
	$smarty->assign('intro_list',   get_intro_list());
	
	
    assign_query_info();
    $smarty->display('solution_step_info.htm');
}
elseif ($_REQUEST['act'] == 'update_step')
{
    admin_priv('slolution_manage');
    if ($_POST['step_name'] != $_POST['old_stepname'])
    {
        /*检查品牌名是否相同*/
        $is_only = $exc_step->is_only('step_name', $_POST['step_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['stepname_exist'], stripslashes($_POST['step_name'])), 1);
        }
    }

    /*对描述处理*/
    if (!empty($_POST['step_desc']))
    {
        $_POST['step_desc'] = $_POST['step_desc'];
    }
	
	$cat_id = isset($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;
    $brand_id = isset($_REQUEST['brand_id']) ? intval($_REQUEST['brand_id']) : 0;
    $intro_type = isset($_REQUEST['intro_type']) ? trim($_REQUEST['intro_type']) : 0;
	
	$array = array($cat_id, $brand_id, $intro_type);
	$step_goods = join(",", $array);
    
	

    /* 处理图片 */
    $img_name = basename($image->upload_image($_FILES['step_logo'],'steplogo'));

    $param = "step_name = '$_POST[step_name]',  step_goods='$step_goods', step_desc='$_POST[step_desc]',is_real='$_POST[is_real]' ";//, other_cat='$_POST[other_cat]'
    if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,step_logo = '$img_name' ";
    }

    if ($exc_step->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['step_name'], 'edit', 'solution_step');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'solution.php?act=list_step';
        $note = vsprintf($_LANG['stepedit_succed'], $_POST['step_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑品牌名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_solution_name')
{
    check_authz_json('slolution_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc->num("solution_name",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['solutionname_exist'], $name));
    }
    else
    {
        if ($exc->edit("solution_name = '$name'", $id))
        {
            admin_log($name,'edit','step');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['solutionedit_fail'], $name));
        }
    }
}





/*------------------------------------------------------ */
//-- 编辑品牌名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_step_name')
{
    check_authz_json('slolution_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc_step->num("step_name",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['stepname_exist'], $name));
    }
    else
    {
        if ($exc_step->edit("step_name = '$name'", $id))
        {
            admin_log($name,'edit','step');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['stepedit_fail'], $name));
        }
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_step')
{
    check_authz_json('slolution_manage');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc->get_name($id);

    if ($exc->edit("sort_step = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','solution');

        make_json_result($order);
    }
    else
    {
        make_json_error(sprintf($_LANG['solutionedit_fail'], $name));
    }
}

	/*------------------------------------------------------ */
	//-- 切换是否显示
	/*------------------------------------------------------ */
	elseif ($_REQUEST['act'] == 'toggle_show')
	{
	    check_authz_json('slolution_manage');

	    $id     = intval($_POST['id']);
	    $val    = intval($_POST['val']);

	    $exc->edit("is_show='$val'", $id);

	    make_json_result($val);
	}


	/*------------------------------------------------------ */
	//-- 切换是否显示
	/*------------------------------------------------------ */
	elseif ($_REQUEST['act'] == 'toggle_real')
	{
	    check_authz_json('slolution_manage');

	    $id     = intval($_POST['id']);
	    $val    = intval($_POST['val']);

	    $exc_step->edit("is_real='$val'", $id);

	    make_json_result($val);
	}


/*------------------------------------------------------ */
//-- 切换是否推荐
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_commend')
{
    check_authz_json('slolution_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_commend='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 删除品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_step')
{
    check_authz_json('slolution_manage');

    $id = intval($_GET['id']);

    /* 删除该品牌的图标 */
    $sql = "SELECT step_logo FROM " .$ecs->table('solution_step'). " WHERE step_id = '$id'";
    $logo_name = $db->getOne($sql);
    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . 'data/steplogo/' .$logo_name);
    }

    $exc_step->drop($id);

	/* 清除缓存 */
    clear_cache_files();
    
    $url = 'solution.php?act=query_step';

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除品牌图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_logo')
{
    /* 权限判断 */
    admin_priv('slolution_manage');
    $brand_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 取得logo名称 */
    $sql = "SELECT brand_logo FROM " .$ecs->table('brand'). " WHERE brand_id = '$brand_id'";
    $logo_name = $db->getOne($sql);

    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . 'data/steplogo/' .$logo_name);
        $sql = "UPDATE " .$ecs->table('brand'). " SET brand_logo = '' WHERE brand_id = '$brand_id'";
        $db->query($sql);
    }
    $link= array(array('text' => $_LANG['brand_edit_lnk'], 'href' => 'brand.php?act=edit&id=' . $brand_id), array('text' => $_LANG['brand_list_lnk'], 'href' => 'brand.php?act=list'));
    sys_msg($_LANG['drop_brand_logo_success'], 0, $link);
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query_step')
{
    $step_list = get_steplist();

    $smarty->assign('step_list',   $step_list['step']);
    $smarty->assign('filter',       $step_list['filter']);
    $smarty->assign('record_count', $step_list['record_count']);
    $smarty->assign('page_count',   $step_list['page_count']);

    make_json_result($smarty->fetch('solution_step_list.htm'), '',
        array('filter' => $step_list['filter'], 'page_count' => $step_list['page_count']));
}
/*------------------------------------------------------ */
//-- 搜索商品，仅返回名称及ID
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_goods_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    
$json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $arr = get_goods_list($filters);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value' => $val['goods_id'],
                        'text' => $val['goods_name'],
                        'data' => $val['shop_price']);
    }

    make_json_result($opt);
}

elseif ($_REQUEST['act'] == 'get_step_info')
{
	    include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;

	    $filters = $json->decode($_GET['JSON']);

	    $arr = get_solution_step_info($filters);

    make_json_result($arr);
}



?>
