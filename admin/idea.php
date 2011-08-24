<?php

/**
 * SINEMALL 管理中心资料处理程序文件    $Author: testyang $
 * $Id: idea.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");
require_once(ROOT_PATH . 'includes/cls_image.php');
require_once(ROOT_PATH . 'admin/includes/lib_goods.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("idea"), $db, 'idea_id', 'title');
$image = new cls_image($_CFG['bgcolor']);
/* 允许上传的文件类型 */
$allow_file_types = '|GIF|JPG|PNG|BMP|SWF|DOC|XLS|PPT|MID|WAV|ZIP|RAR|PDF|CHM|RM|TXT|';

/*------------------------------------------------------ */
//-- 资料列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('cat_select',  idea_cat_list(0));
    $smarty->assign('ur_here',      $_LANG['05_idea_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['idea_add'], 'href' => 'idea.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $idea_list = get_ideaslist();

    $smarty->assign('idea_list',    $idea_list['arr']);
    $smarty->assign('filter',          $idea_list['filter']);
    $smarty->assign('record_count',    $idea_list['record_count']);
    $smarty->assign('page_count',      $idea_list['page_count']);

    $sort_flag  = sort_flag($idea_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('idea_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('idea_manage');

    $idea_list = get_ideaslist();

    $smarty->assign('idea_list',    $idea_list['arr']);
    $smarty->assign('filter',          $idea_list['filter']);
    $smarty->assign('record_count',    $idea_list['record_count']);
    $smarty->assign('page_count',      $idea_list['page_count']);

    $sort_flag  = sort_flag($idea_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('idea_list.htm'), '',
        array('filter' => $idea_list['filter'], 'page_count' => $idea_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加资料
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('idea_manage');

    /* 创建 html editor */
    create_html_editor('FCKeditor1');

    /*初始化*/
    $idea = array();
    $idea['is_open'] = 1;
    $idea['templete'] = 4;
    $idea['other_cat'] = array();
    

	// 扩展分类
    $other_cat_list = idea_cat_list(0, 0);
    $smarty->assign('other_cat_list', $other_cat_list);

	
    $userList = get_userList();
    $smarty->assign('userList', $userList);
	
    /* 取得分类、品牌 */
    $smarty->assign('goods_cat_list', cat_list());
    $smarty->assign('brand_list',     get_brand_list());
	

    if (isset($_GET['id']))
    {
        $smarty->assign('cur_id',  $_GET['id']);
    }
    $smarty->assign('idea',     $idea);
    $smarty->assign('cat_select',  idea_cat_list(0));
    $smarty->assign('ur_here',     $_LANG['idea_add']);
    $smarty->assign('action_link', array('text' => $_LANG['05_idea_list'], 'href' => 'idea.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->display('idea_info.htm');
}

/*------------------------------------------------------ */
//-- 添加资料
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('idea_manage');

    /*检查是否重复*/
    $is_only = $exc->is_only('title', $_POST['title'],0, " cat_id ='$_POST[idea_cat]'");

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['title_exist'], stripslashes($_POST['title'])), 1);
    }

    /* 取得文件地址 */
    $file_url = '';
    if ((isset($_FILES['file']['error']) && $_FILES['file']['error'] == 0) || (!isset($_FILES['file']['error']) && isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != 'none'))
    {
        // 检查文件格式
        if (!check_file_type($_FILES['file']['tmp_name'], $_FILES['file']['name'], $allow_file_types))
        {
            sys_msg($_LANG['invalid_file']);
        }

        // 复制文件
        $res = upload_idea_file($_FILES['file']);
        if ($res != false)
        {
            $file_url = $res;
        }
    }

    if ($file_url == '')
    {
        $file_url = $_POST['file_url'];
    }

    /* 计算资料打开方式 */
    if ($file_url == '')
    {
        $open_type = 0;
    }
    else
    {
        $open_type = $_POST['FCKeditor1'] == '' ? 1 : 2;
    }

	 /*处理图片*/
    $img_name = basename($image->upload_image($_FILES['idealogo'],'idealogo'));

    /*插入数据*/
    $add_time = gmtime();
    if (empty($_POST['cat_id']))
    {
        $_POST['cat_id'] = 0;
    }
    $sql = "INSERT INTO ".$ecs->table('idea')."(title, logo, cat_id, idea_type, is_open, author, ".
                "author_email, keywords, flickr_rss, brief, content, add_time, templete,file_url, open_type, link) ".
            "VALUES ('$_POST[title]', '$img_name', '$_POST[idea_cat]', '$_POST[idea_type]', '$_POST[is_open]', ".
                "'$_POST[author]', '$_POST[author_email]', '$_POST[keywords]', '$_POST[flickr_rss]', '$_POST[brief]','$_POST[FCKeditor1]', ".
                "'$add_time', '$_POST[templete]','$file_url', '$open_type', '$_POST[link_url]')";
    $db->query($sql);


	/* 处理相册图片 */
    handle_idea_gallery_image($idea_id, $_FILES['img_url'], $_POST['img_desc'],$_POST['img_sort']);
       
    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'idea.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'idea.php?act=list';

    admin_log($_POST['title'],'add','idea');

    clear_cache_files(); // 清除相关的缓存文件

    sys_msg($_LANG['ideaadd_succeed'],0, $link);
}

/*------------------------------------------------------ */
//-- 编辑
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('idea_manage');

    /* 取资料数据 */
    $sql = "SELECT * FROM " .$ecs->table('idea'). " WHERE idea_id='$_REQUEST[id]'";
    $idea = $db->GetRow($sql);

    /* 创建 html editor */
    create_html_editor('FCKeditor1',$idea['content']);

    $userList = get_userList();
    $smarty->assign('userList', $userList);

	/* 图片列表 */
    $sql = "SELECT * FROM " . $ecs->table('ideas_gallery') . " WHERE idea_id = '$_REQUEST[id]' ORDER BY img_sort ASC";
    $img_list = $db->getAll($sql);
    $smarty->assign('img_list', $img_list);



	
    $smarty->assign('idea',     $idea);
    $smarty->assign('cat_select',  idea_cat_list(0, $idea['cat_id']));
    $smarty->assign('ur_here',     $_LANG['idea_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['05_idea_list'], 'href' => 'idea.php?act=list&' . list_link_postfix()));
    $smarty->assign('form_action', 'update');


    assign_query_info();
    $smarty->display('idea_info.htm');
}

if ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('idea_manage');

    /*检查资料名是否相同*/
    $is_only = $exc->is_only('title', $_POST['title'], $_POST['id'], "cat_id = '$_POST[idea_cat]'");

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['title_exist'], stripslashes($_POST['title'])), 1);
    }


    if (empty($_POST['cat_id']))
    {
        $_POST['cat_id'] = 0;
    }

    /* 取得文件地址 
    $file_url = '';
    if (empty($_FILES['file']['error']) || (!isset($_FILES['file']['error']) && isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != 'none'))
    {
        // 检查文件格式
        if (!check_file_type($_FILES['file']['tmp_name'], $_FILES['file']['name'], $allow_file_types))
        {
            sys_msg($_LANG['invalid_file']);
        }

        // 复制文件
        $res = upload_idea_file($_FILES['file']);
        if ($res != false)
        {
            $file_url = $res;
        }
    }

    if ($file_url == '')
    {
        $file_url = $_POST['file_url'];
    }
	*/

    /* 计算资料打开方式 */
    if ($file_url == '')
    {
        $open_type = 0;
    }
    else
    {
        $open_type = $_POST['FCKeditor1'] == '' ? 1 : 2;
    }


	
    /* 如果 file_url 跟以前不一样，且原来的文件是本地文件，删除原来的文件 */
    $sql = "SELECT file_url FROM " . $ecs->table('idea') . " WHERE idea_id = '$_POST[id]'";

    $old_url = $db->getOne($sql);
    if ($old_url != '' && $old_url != $file_url && strpos($old_url, 'http://') === false && strpos($old_url, 'https://') === false)
    {
        @unlink(ROOT_PATH . $old_url);
    }
	
	/* 处理相册图片 */    
	handle_idea_gallery_image($_POST['id'], $_FILES['img_url'], $_POST['img_desc'],$_POST['img_sort']);
    
    /* 编辑时处理相册图片描述 */
    if (isset($_POST['old_img_desc']))
    {
        foreach ($_POST['old_img_desc'] AS $img_id => $img_desc)
        {
            $sql = "UPDATE " . $ecs->table('ideas_gallery') . " SET img_desc = '$img_desc' WHERE img_id = '$img_id' LIMIT 1";
            $db->query($sql);
        }
    }

    /* 编辑时处理相册图片描述 */
    if (isset($_POST['old_img_sort']))
    {
        foreach ($_POST['old_img_sort'] AS $img_id => $img_sort)
        {
            $sql = "UPDATE " . $ecs->table('ideas_gallery') . " SET img_sort = '$img_sort' WHERE img_id = '$img_id' LIMIT 1";
            $db->query($sql);
        }
    }

	/*处理图片*/
    $img_name = basename($image->upload_image($_FILES['idealogo'],'idealogo'));
	
	$param = " title='$_POST[title]', cat_id='$_POST[idea_cat]', idea_type='$_POST[idea_type]', is_open='$_POST[is_open]', author='$_POST[author]', author_email='$_POST[author_email]', keywords ='$_POST[keywords]', flickr_rss ='$_POST[flickr_rss]',  brief ='$_POST[brief]', templete = '$_POST[templete]', file_url ='$file_url', open_type='$open_type', content='$_POST[FCKeditor1]', link='$_POST[link_url]'  ";
    if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,logo = '$img_name' ";
    }
	
    if ($exc->edit("$param", $_POST['id']))
    {
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'idea.php?act=list&' . list_link_postfix();
        $note = sprintf($_LANG['ideaedit_succeed'], stripslashes($_POST['title']));
        admin_log($_POST['title'], 'edit', 'idea');

        clear_cache_files();

        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑资料主题
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_title')
{
    check_authz_json('idea_manage');

    $id    = intval($_POST['id']);
    $title = json_str_iconv(trim($_POST['val']));

    /* 检查资料标题是否重复 */
    if ($exc->num("title", $title, $id) != 0)
    {
        make_json_error(sprintf($_LANG['title_exist'], $title));
    }
    else
    {
        if ($exc->edit("title = '$title'", $id))
        {
            clear_cache_files();
            admin_log($title, 'edit', 'idea');
            make_json_result(stripslashes($title));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show')
{
    check_authz_json('idea_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_open = '$val'", $id);
    clear_cache_files();

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 切换资料重要性
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_type')
{
    check_authz_json('idea_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("idea_type = '$val'", $id);
    clear_cache_files();

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 批量删除资料
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch_remove')
{
    admin_priv('idea_manage');

    if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
    {
        sys_msg($_LANG['no_select_idea'], 1);
    }

    /* 删除原来的文件 */
    $sql = "SELECT file_url FROM " . $ecs->table('idea') .
            " WHERE idea_id " . db_create_in(join(',', $_POST['checkboxes'])) .
            " AND file_url <> ''";
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $old_url = $row['file_url'];
        if (strpos($old_url, 'http://') === false && strpos($old_url, 'https://') === false)
        {
            @unlink(ROOT_PATH . $old_url);
        }
    }

    $count = 0;
    foreach ($_POST['checkboxes'] AS $key => $id)
    {
        if ($id)
        {
			$sql = "SELECT author  FROM " . $ecs->table('idea') . " WHERE idea_id = '$id'";
			$user_id = $db->getOne($sql);
			log_account_change($user_id, -10, 0, 0, 0, $_LANG['REMOVE_GOLD_10']);
			
			$exc->drop($id);
			
			
            $name = $exc->get_name($id);
            admin_log(addslashes($name),'remove','idea');

            $count++;
        }
    }

    $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'idea.php?act=list');
    sys_msg(sprintf($_LANG['batch_remove_succeed'], $count), 0, $lnk);
}

/*------------------------------------------------------ */
//-- 删除资料主题
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('idea_manage');

    $id = intval($_GET['id']);

    /* 删除原来的文件 */
    $sql = "SELECT author ,file_url FROM " . $ecs->table('idea') . " WHERE idea_id = '$id'";
//echo $sql;	
	$res = $db->getRow($sql);
    $old_url = $res['file_url'];
    if ($old_url != '' && strpos($old_url, 'http://') === false && strpos($old_url, 'https://') === false)
    {
        @unlink(ROOT_PATH . $old_url);
    }

    $name = $exc->get_name($id);
	log_account_change($res['author'], -10, 0, 0, 0, $_LANG['REMOVE_GOLD_10']);

    if ($exc->drop($id))
    {
        admin_log(addslashes($name),'remove','idea');
        clear_cache_files();
		
    }

    $url = 'idea.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    //ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 搜索资料
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'get_idea_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters =(array) $json->decode($_GET['JSON']);

    $where = " WHERE cat_id > 0 ";
    if (!empty($filters['title']))
    {
        $keyword  = trim($filters['title']);
        $where   .=  " AND title LIKE '%" . mysql_like_quote($keyword) . "%' ";
    }

    $sql        = 'SELECT idea_id, title FROM ' .$ecs->table('idea'). $where.
                  'ORDER BY idea_id DESC LIMIT 50';
    $res        = $db->query($sql);
    $arr        = array();

    while ($row = $db->fetchRow($res))
    {
        $arr[]  = array('value' => $row['idea_id'], 'text' => $row['title'], 'data'=>'');
    }

    make_json_result($arr);
}

/*------------------------------------------------------ */
//-- 添加关联资料
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_goods_idea')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $ideas   = $json->decode($_GET['add_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];

    foreach ($ideas AS $val)
    {
        $sql = "INSERT INTO " . $ecs->table('goods_idea') . " (goods_id, idea_id, admin_id) " .
                "VALUES ('$goods_id', '$val', '$_SESSION[admin_id]')";
        $db->query($sql);
    }

    $arr = get_goods_ideas($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['idea_id'],
                        'text'      => $val['title'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 删除关联资料
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_goods_idea')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $ideas   = $json->decode($_GET['drop_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];

    $sql = "DELETE FROM " .$ecs->table('goods_idea') . " WHERE " . db_create_in($ideas, "idea_id");
    $db->query($sql);

    $arr = get_goods_ideas($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['idea_id'],
                        'text'      => $val['title'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}


/*------------------------------------------------------ */
//-- 将商品加入关联
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_link_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('idea_manage');

    $add_ids = $json->decode($_GET['add_ids']);
    $args = $json->decode($_GET['JSON']);
    $idea_id = $args[0];

    if ($idea_id == 0)
    {
        $idea_id = $db->getOne('SELECT MAX(idea_id)+1 AS idea_id FROM ' .$ecs->table('idea'));
    }

    foreach ($add_ids AS $key => $val)
    {
        $sql = 'INSERT INTO ' . $ecs->table('goods_idea') . ' (goods_id, idea_id) '.
               "VALUES ('$val', '$idea_id')";
        $db->query($sql, 'SILENT') or make_json_error($db->error());
    }

    /* 重新载入 */
    $arr = get_idea_goods($idea_id);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value'  => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => '');
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 将商品删除关联
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_link_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('idea_manage');

    $drop_goods     = $json->decode($_GET['drop_ids']);
    $arguments      = $json->decode($_GET['JSON']);
    $idea_id     = $arguments[0];

    if ($idea_id == 0)
    {
        $idea_id = $db->getOne('SELECT MAX(idea_id)+1 AS idea_id FROM ' .$ecs->table('idea'));
    }

    $sql = "DELETE FROM " . $ecs->table('goods_idea').
            " WHERE idea_id = '$idea_id' AND goods_id " .db_create_in($drop_goods);
    $db->query($sql, 'SILENT') or make_json_error($db->error());

    /* 重新载入 */
    $arr = get_idea_goods($idea_id);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value'  => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => '');
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 删除图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_image')
{
    check_authz_json('idea_manage');

    $img_id = empty($_REQUEST['img_id']) ? 0 : intval($_REQUEST['img_id']);

    /* 删除图片文件 */
    $sql = "SELECT img_url, thumb_url, img_original " .
            " FROM " . $GLOBALS['ecs']->table('ideas_gallery') .
            " WHERE img_id = '$img_id'";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row['img_url'] != '' && is_file('../' . $row['img_url']))
    {
        @unlink('../' . $row['img_url']);
    }
    if ($row['thumb_url'] != '' && is_file('../' . $row['thumb_url']))
    {
        @unlink('../' . $row['thumb_url']);
    }
    if ($row['img_original'] != '' && is_file('../' . $row['img_original']))
    {
        @unlink('../' . $row['img_original']);
    }

    /* 删除数据 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('ideas_gallery') . " WHERE img_id = '$img_id' LIMIT 1";
    $GLOBALS['db']->query($sql);

    clear_cache_files();
    make_json_result($img_id);
}


/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'get_goods_list')
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

/* 把商品删除关联 */
function drop_link_goods($goods_id, $idea_id)
{
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_idea') .
            " WHERE goods_id = '$goods_id' AND idea_id = '$idea_id' LIMIT 1";
    $GLOBALS['db']->query($sql);
    create_result(true, '', $goods_id);
}

/* 取得资料关联商品 */
function get_idea_goods($idea_id)
{
    $list = array();
    $sql  = 'SELECT g.goods_id, g.goods_name'.
            ' FROM ' . $GLOBALS['ecs']->table('goods_idea') . ' AS ga'.
            ' LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = ga.goods_id'.
            " WHERE ga.idea_id = '$idea_id'";
    $list = $GLOBALS['db']->getAll($sql);

    return $list;
}

/* 获得资料列表 */
function get_ideaslist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if ($_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['cat_id'] = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.idea_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if ($filter['cat_id'])
        {
            $where .= " AND a." . get_idea_children($filter['cat_id']);
        }

        /* 资料总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('idea'). ' AS a '.
               'LEFT JOIN ' .$GLOBALS['ecs']->table('idea_cat'). ' AS ac ON ac.cat_id = a.cat_id '.
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取资料数据 */
        $sql = 'SELECT a.* , ac.cat_name,u.real_name, COUNT("com.comment_id") AS comment_count '.
               'FROM ' .$GLOBALS['ecs']->table('idea'). ' AS a '.
               'LEFT JOIN ' .$GLOBALS['ecs']->table('idea_cat'). ' AS ac ON ac.cat_id = a.cat_id '.
               'LEFT JOIN ' .$GLOBALS['ecs']->table('users'). ' AS u ON a.author = u.user_id '.
               'LEFT JOIN ' .$GLOBALS['ecs']->table('comment'). ' AS com ON com.idea_id = a.idea_id '.
               'WHERE 1 ' .$where. ' GROUP BY a.idea_id ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['date'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);
		//$sql = "SELECT COUNT(comment_id) FROM " . $GLOBALS['ecs']->table('comment') . " WHERE idea_id='$rows[idea_id]' ";
        //$rows['comment_count'] = $GLOBALS['db']->getOne($sql);

        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/* 上传文件 */
function upload_idea_file($upload)
{
    if (!make_dir("../data/idea"))
    {
        /* 创建目录失败 */
        return false;
    }
    //$filename = cls_image::random_filename() . substr($upload['name'], strpos($upload['name'], '.'));

	$t_name = substr($upload['name'], 0, strpos($upload['name'], '.'));
	
	$filename = $t_name ."_". date("Ymd") . substr($upload['name'], strpos($upload['name'], '.'));
	
    $path     = ROOT_PATH."data/idea/" . $filename;

    if (move_upload_file($upload['tmp_name'], $path))
    {
        return "data/idea/" . $filename;
    }
    else
    {
        return false;
    }
}

?>