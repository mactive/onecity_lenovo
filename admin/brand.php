<?php

/**
 * SINEMALL 管理中心品牌管理    $Author: testyang $
 * $Id: brand.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc = new exchange($ecs->table("brand"), $db, 'brand_id', 'brand_name');

/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      $_LANG['06_goods_brand_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['07_brand_add'], 'href' => 'brand.php?act=add'));
    $smarty->assign('full_page',    1);

    $brand_list = get_brandlist();

    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    assign_query_info();
    $smarty->display('brand_list.htm');
}

/*------------------------------------------------------ */
//-- 添加品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('brand_manage');

    $smarty->assign('ur_here',     $_LANG['07_brand_add']);
    $smarty->assign('action_link', array('text' => $_LANG['06_goods_brand_list'], 'href' => 'brand.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->assign('brand', array('sort_order'=>0, 'is_show'=>1,'is_commend'=>1));
    $smarty->display('brand_info.htm');
    $smarty->assign('cfg',     $_CFG);

}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查品牌名是否重复*/
    admin_priv('brand_manage');

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;

    $is_only = $exc->is_only('brand_name', $_POST['brand_name']);

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
    //$img_name = basename($image->upload_image($_FILES['brand_logo'],'brandlogo'));
    $img_name = $image->upload_image($_FILES['brand_logo'],'brandlogo');

     /*处理URL*/
    $site_url = sanitize_url($_POST['site_url'] );

    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('brand')."(brand_name, site_url, brand_desc, brand_logo, is_show,is_commend, sort_order,market_price_rate, shop_price_rate, salebase_price_rate, agency_price_rate) ".
           "VALUES ('$_POST[brand_name]', '$site_url', '$_POST[brand_desc]', '$img_name', '$is_show', '$is_commend','$_POST[sort_order]','$_POST[market_price_rate]','$_POST[shop_price_rate]','$_POST[salebase_price_rate]','$_POST[agency_price_rate]')";
    $db->query($sql);

    admin_log($_POST['brand_name'],'add','brand');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'brand.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'brand.php?act=list';

    sys_msg($_LANG['brandadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $sql = "SELECT brand_id, brand_name, site_url, brand_logo, brand_desc, brand_logo, is_show, is_commend,sort_order,market_price_rate, shop_price_rate, salebase_price_rate, agency_price_rate ".
            "FROM " .$ecs->table('brand'). " WHERE brand_id='$_REQUEST[id]'";
    $brand = $db->GetRow($sql);

    $smarty->assign('ur_here',     $_LANG['brand_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['06_goods_brand_list'], 'href' => 'brand.php?act=list&' . list_link_postfix()));
    $smarty->assign('brand',       $brand);
    $smarty->assign('form_action', 'updata');
    $smarty->assign('cfg', $_CFG);

    assign_query_info();
    $smarty->display('brand_info.htm');
}
elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('brand_manage');
    if ($_POST['brand_name'] != $_POST['old_brandname'])
    {
        /*检查品牌名是否相同*/
        $is_only = $exc->is_only('brand_name', $_POST['brand_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['brandname_exist'], stripslashes($_POST['brand_name'])), 1);
        }
    }

    /*对描述处理*/
    if (!empty($_POST['brand_desc']))
    {
        $_POST['brand_desc'] = $_POST['brand_desc'];
    }

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;
     /*处理URL*/
    $site_url = sanitize_url( $_POST['site_url'] );

    /* 处理图片 */
    $img_name = $image->upload_image($_FILES['brand_logo'],'brandlogo');
    $param = "brand_name = '$_POST[brand_name]',  site_url='$site_url', brand_desc='$_POST[brand_desc]', is_show='$is_show', is_commend='$is_commend',sort_order='$_POST[sort_order]',market_price_rate = '$_POST[market_price_rate]', shop_price_rate='$_POST[shop_price_rate]', salebase_price_rate='$_POST[salebase_price_rate]', agency_price_rate='$_POST[agency_price_rate]'  ";
    if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,brand_logo = '$img_name' ";
    }

    if ($exc->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['brand_name'], 'edit', 'brand');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'brand.php?act=list&' . list_link_postfix();
        $note = vsprintf($_LANG['brandedit_succed'], $_POST['brand_name']);
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
elseif ($_REQUEST['act'] == 'edit_brand_name')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc->num("brand_name",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['brandname_exist'], $name));
    }
    else
    {
        if ($exc->edit("brand_name = '$name'", $id))
        {
            admin_log($name,'edit','brand');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['brandedit_fail'], $name));
        }
    }
}

elseif($_REQUEST['act'] == 'add_brand')
{
    $brand = empty($_REQUEST['brand']) ? '' : json_str_iconv(trim($_REQUEST['brand']));

    if(brand_exists($brand))
    {
        make_json_error($_LANG['brand_name_exist']);
    }
    else
    {
        $sql = "INSERT INTO " . $ecs->table('brand') . "(brand_name)" .
               "VALUES ( '$brand')";

        $db->query($sql);
        $brand_id = $db->insert_id();

        $arr = array("id"=>$brand_id, "brand"=>$brand);

        make_json_result($arr);
    }
}
/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc->get_name($id);

    if ($exc->edit("sort_order = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','brand');

        make_json_result($order);
    }
    else
    {
        make_json_error(sprintf($_LANG['brandedit_fail'], $name));
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_show='$val'", $id);

    make_json_result($val);
}


/*------------------------------------------------------ */
//-- 切换是否推荐
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_commend')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_commend='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 删除品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('brand_manage');

    $id = intval($_GET['id']);

    /* 删除该品牌的图标 */
    $sql = "SELECT brand_logo FROM " .$ecs->table('brand'). " WHERE brand_id = '$id'";
    $logo_name = $db->getOne($sql);
    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . $logo_name);
    }

    $exc->drop($id);

    /* 更新商品的品牌编号 */
    $sql = "UPDATE " .$ecs->table('goods'). " SET brand_id=0 WHERE brand_id='$id'";
    $db->query($sql);

    $url = 'brand.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除品牌图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_logo')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $brand_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 取得logo名称 */
    $sql = "SELECT brand_logo FROM " .$ecs->table('brand'). " WHERE brand_id = '$brand_id'";
    $logo_name = $db->getOne($sql);

    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH .$logo_name);
        $sql = "UPDATE " .$ecs->table('brand'). " SET brand_logo = '' WHERE brand_id = '$brand_id'";
        $db->query($sql);
    }
    $link= array(array('text' => $_LANG['brand_edit_lnk'], 'href' => 'brand.php?act=edit&id=' . $brand_id), array('text' => $_LANG['brand_list_lnk'], 'href' => 'brand.php?act=list'));
    sys_msg($_LANG['drop_brand_logo_success'], 0, $link);
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $brand_list = get_brandlist();

    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    make_json_result($smarty->fetch('brand_list.htm'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}

/**
 * 获取品牌列表
 *
 * @access  public
 * @return  array
 */
function get_brandlist()
{
	$filter['brand_name'] = empty($_REQUEST['brand_name']) ? '' : trim($_REQUEST['brand_name']);
    $filter['is_commend'] = empty($_REQUEST['is_commend']) ? '' : intval($_REQUEST['is_commend']);
	$where = 'WHERE 1 ';
    if ($filter['brand_name'])
    {
        $where .= " AND o.brand_name LIKE '%" . mysql_like_quote($filter['brand_name']) . "%'";
    }
    if ($filter['is_commend'])
    {
        $where .= " AND o.is_commend = '$filter[is_commend]'";
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
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('brand') ." AS o " . $where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

	$brand_sql = 'SELECT o.*'.				
            " FROM " . $GLOBALS['ecs']->table('brand') . " AS o ".
             $where . 
			" ORDER BY o.brand_name ASC ".
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
			//echo $brand_sql;
	
    //$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

	$row = $GLOBALS['db']->getAll($brand_sql);
 	/* 格式话数据 */
    foreach ($row AS $key => $value)
    {
		$brand_logo = empty($value['brand_logo']) ? '' :
		            '<a href="../'.$value['brand_logo'].'" target="_brank"><img src="images/picflag.gif" width="16" height="16" border="0" alt='.$GLOBALS['_LANG']['brand_logo'].' /></a>';
		$site_url   = empty($value['site_url']) ? 'N/A' : '<a href="'.$value['site_url'].'" target="_brank">'.$value['site_url'].'</a>';
		$row[$key]['brand_logo'] = $brand_logo;
	 	$row[$key]['site_url']   = $site_url;
    }
	//print_r($row);
	$arr = array('brand' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;

}

?>
