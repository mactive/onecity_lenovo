<?php

/**
 * SINEMALL 管理中心资料处理程序文件    $Author: testyang $
 * $Id: idea.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/idea.php');
$smarty->assign('lang', $_LANG);


/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("comment"), $db, 'comment_id', 'content');
/* 允许上传的文件类型 */
$allow_file_types = '|GIF|JPG|PNG|BMP|SWF|DOC|XLS|PPT|MID|WAV|ZIP|RAR|PDF|CHM|RM|TXT|';

/*------------------------------------------------------ */
//-- 资料列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
	$idea_id = empty($_REQUEST['idea_id']) ? '':  $_REQUEST['idea_id'];
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('cat_select',  idea_cat_list(0));
    $smarty->assign('ur_here',      "评论列表");
    $smarty->assign('action_link',  array('text' => $_LANG['idea_add'], 'href' => ''));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $idea_list = get_idea_comments_list($idea_id);
	$smarty->assign('idea_id',    $idea_id);
    
    $smarty->assign('idea_list',    $idea_list['arr']);
    $smarty->assign('filter',          $idea_list['filter']);
    $smarty->assign('record_count',    $idea_list['record_count']);
    $smarty->assign('page_count',      $idea_list['page_count']);
    $smarty->assign('sql',      		$idea_list['sql']);

    $sort_flag  = sort_flag($idea_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('idea_comment_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{	
    $idea_list = get_idea_comments_list();

    $smarty->assign('idea_list',    $idea_list['arr']);
    $smarty->assign('filter',          $idea_list['filter']);
    $smarty->assign('record_count',    $idea_list['record_count']);
    $smarty->assign('page_count',      $idea_list['page_count']);
    $smarty->assign('sql',      		$idea_list['sql']);

    $sort_flag  = sort_flag($idea_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('idea_list.htm'), '',
        array('filter' => $idea_list['filter'], 'page_count' => $idea_list['page_count']));
}

/*------------------------------------------------------ */
//-- 批量删除资料
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch_remove')
{
    if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
    {
        sys_msg($_LANG['no_select_idea'], 1);
    }

    $count = 0;
    foreach ($_POST['checkboxes'] AS $key => $id)
    {	
			$sql = "SELECT user_id  FROM " . $ecs->table('comment') . " WHERE comment_id = '$id'";
			$user_id = $db->getOne($sql);
			
			$exc->drop($id);
			
			log_account_change($user_id, -1, 0, 0, 0, $_LANG['REMOVE_GOLD_1']);
       		$count++;
		
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

    $sql = "SELECT user_id ,user_name FROM " . $ecs->table('comment') . " WHERE comment_id = '$id'";
	$res = $db->getRow($sql);

    $name = $exc->get_name($id);
	log_account_change($res['user_id'], -1, 0, 0, 0, $_LANG['REMOVE_GOLD_1']);

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
//-- 搜索商品
/*------------------------------------------------------ */

/* 获得资料列表 */
function get_idea_comments_list($idea_id)
{
	$filter['idea_id'] 		= empty($_REQUEST['idea_id']) ? '' : intval($_REQUEST['idea_id']);
	
	$idea_id = empty($_REQUEST['idea_id']) ? $filter['idea_id'] : $idea_id;
    
	if(empty($idea_id)){
		exit;
	}
	
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if ($_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['cat_id'] 		= empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['sort_by']    	= empty($_REQUEST['sort_by']) ? 'a.comment_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] 	= empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if ($idea_id)
        {
            $where .= " AND a.idea_id = $idea_id ";
        }

        /* 资料总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('comment'). ' AS a '.
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取资料数据 */
        $sql = 'SELECT a.* '.
               'FROM ' .$GLOBALS['ecs']->table('comment'). ' AS a '.
               'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

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
        $rows['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);
        $arr[] = $rows;
    }
    return array('arr' => $arr, 'sql' => $sql, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}




?>