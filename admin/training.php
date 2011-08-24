<?php

/**
 * SINEMALL 管理中心事件管理    $Author: testyang $
 * $Id: brand.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/admin/includes/lib_training.php');
require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

$exc_events = new exchange($ecs->table("events"), $db, 'event_id', 'event_name');
$exc_courses = new exchange($ecs->table("courses"), $db, 'course_id', 'course_name');
$exc_courses_cat = new exchange($ecs->table("courses_cat"), $db, 'course_cat_id', 'course_cat_name');
$exc_lessons = new exchange($ecs->table("lessons"), $db, 'lesson_id', 'lesson_name');

/*----------------------------------------------------------------------------------------- */
//-- 和事件有关的操作 开始
/*------------------------------------------------------------------------------------------ */

/*------------------------------------------------------ */
//-- 事件列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list_event')
{
    $smarty->assign('ur_here',      $_LANG['01_event_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['02_event_add'], 'href' => 'training.php?act=add_event'));
    $smarty->assign('full_page',    1);

    $event_list = get_eventlist();

    $smarty->assign('event_list',   $event_list['event']);
    $smarty->assign('filter',       $event_list['filter']);
    $smarty->assign('record_count', $event_list['record_count']);
    $smarty->assign('page_count',   $event_list['page_count']);

    assign_query_info();
    $smarty->display('training_event_list.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query_event')
{
    $event_list = get_eventlist();

    $smarty->assign('event_list',   $event_list['event']);
    $smarty->assign('filter',       $event_list['filter']);
    $smarty->assign('record_count', $event_list['record_count']);
    $smarty->assign('page_count',   $event_list['page_count']);

    make_json_result($smarty->fetch('training_event_list.htm'), '',
        array('filter' => $event_list['filter'], 'page_count' => $event_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加事件
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_event')
{
    /* 权限判断 */
    admin_priv('training_manage');

    /* 创建 html editor */
    create_html_editor('FCKeditor_desc');

    $smarty->assign('ur_here',     $_LANG['02_event_add']);
    $smarty->assign('action_link', array('text' => $_LANG['01_event_list'], 'href' => 'training.php?act=list_event'));
    $smarty->assign('form_action', 'insert_event');

	/* 取得事件的课程列表 */
	$event_courses = get_event_courses($event['event_id'],$event['event_duration'],0);
	$smarty->assign('event_courses',$event_courses);   //课程分类
	$smarty->assign('course_list',get_course_list());   // <select> 课程分类

    assign_query_info();
    $smarty->assign('event', array('sort_order'=>0, 'is_show'=>1,'is_commend'=>1));
    $smarty->assign('cfg',     $_CFG);

    $smarty->display('training_event_info.htm');

}
elseif ($_REQUEST['act'] == 'insert_event')
{
    /*检查事件名是否重复*/
    admin_priv('training_manage');


    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;
	$event_start_time = local_strtotime($_POST['event_start_time']);
    
    $is_only = $exc_events->is_only('event_name', $_POST['event_name']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['eventname_exist'], stripslashes($_POST['event_name'])), 1);
    }

    /*对简述处理*/
    if (!empty($_POST['event_brief']))
    {
        $_POST['event_brief'] = $_POST['event_brief'];
    }

     /*处理图片*/
    $img_name = $image->upload_image($_FILES['event_logo'],'traininglogo');

     /*处理URL*/
    //$site_url = sanitize_url($_POST['site_url'] );

    /*插入数据*/
	$event_id = $db->getOne('SELECT MAX(event_id)+1 AS event_id FROM ' .$ecs->table('events'));
    
    $sql = "INSERT INTO ".$ecs->table('events')."(event_id,event_name, event_brief, event_desc, event_logo, is_show,is_commend, sort_order,event_start_time,event_duration,event_gallery,event_teacher,level) ".
           "VALUES ($event_id,'$_POST[event_name]', '$_POST[event_brief]','$_POST[FCKeditor_desc]', '$img_name', '$is_show', '$is_commend','$_POST[sort_order]','$event_start_time','$_POST[event_duration]','$_POST[event_gallery]','$_POST[event_teacher]','$_POST[level]')";
    $db->query($sql);
	
	/*插入事件课程列表*/
	$sort_order_value = $_POST['sort_order_list'];
	$course_id_value = $_POST['course_id_list'];
	$id_list_value = $_POST['id_list'];
	
	if($sort_order_value && $course_id_value)
	{
		for($i=0;$i<count($course_id_value);$i++)
		{
			if($course_id_value[$i]){
				$sql = "INSERT INTO ".$ecs->table('event_course')."(id,event_id,course_id,sort_order) ".
			           "VALUES ('','$event_id', '$course_id_value[$i]','$sort_order_value[$i]')";
				//echo $sql;
			    $db->query($sql);
			}
			
		}	
	}
	

    admin_log($_POST['event_name'],'add','training');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'training.php?act=add_event';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'training.php?act=list_event';

    sys_msg($_LANG['eventadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑事件
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_event')
{
    /* 权限判断 */
    admin_priv('training_manage');

    $sql = "SELECT event_id,event_name, event_brief, event_desc, event_logo, is_show,is_commend, sort_order,event_start_time,event_duration,event_gallery,event_teacher,level ".
            "FROM " .$ecs->table('events'). " WHERE event_id='$_REQUEST[id]'";
    $event = $db->GetRow($sql);

    $event['event_start_time']  = local_date($GLOBALS['_CFG']['time_format'], $event['event_start_time']);
	
    /* 创建 html editor */
    create_html_editor('FCKeditor_desc',$event['event_desc']);


    $smarty->assign('ur_here',     $_LANG['event_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['01_event_list'], 'href' => 'training.php?act=list_event&' . list_link_postfix()));
    $smarty->assign('event',       $event);
    $smarty->assign('form_action', 'update_event');
    $smarty->assign('cfg', $_CFG);

	/* 取得事件的课程列表 */
	$event_courses = get_event_courses($event['event_id'],$event['event_duration'],0);
	$smarty->assign('event_courses',$event_courses);   //课程分类
	$smarty->assign('course_list',get_course_list());   // <select> 课程分类
	
    assign_query_info();
    $smarty->display('training_event_info.htm');
}


elseif ($_REQUEST['act'] == 'update_event')
{
    admin_priv('training_manage');

    if ($_POST['event_name'] != $_POST['old_eventname'])
    {
        /*检查事件名是否相同*/
        $is_only = $exc_events->is_only('event_name', $_POST['event_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['eventname_exist'], stripslashes($_POST['event_name'])), 1);
        }
    }

    /*对描述处理*/
    if (!empty($_POST['event_brief']))
    {
        $_POST['event_brief'] = $_POST['event_brief'];
    }

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;
	$event_start_time = local_strtotime($_POST['event_start_time']);

    /* 处理图片 */
    $img_name = $image->upload_image($_FILES['event_logo'],'traininglogo');
    $param = "event_name = '$_POST[event_name]',  event_brief='$_POST[event_brief]', event_desc='$_POST[FCKeditor_desc]', is_show='$is_show', is_commend='$is_commend',sort_order='$_POST[sort_order]',event_start_time = '$event_start_time', event_duration='$_POST[event_duration]', event_gallery='$_POST[event_gallery]', event_teacher='$_POST[event_teacher]' , level='$_POST[level]'  ";

    if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,event_logo = '$img_name' ";
    }

	/*更新课程列表*/
	$sort_order_value = $_POST['sort_order_list'];
	$course_id_value = $_POST['course_id_list'];
	$id_list_value = $_POST['id_list'];
	
	if($sort_order_value && $course_id_value)
	{
		//批量操作 不能修改库存价格 不能批量入库 以serial_number 为准 可能会覆盖part_number 
		//print_r($attr_value);
		for($i=0;$i<count($course_id_value);$i++)
		{
			//
			if($id_list_value[$i] != '')
			{
				$sql = "UPDATE " . $ecs->table('event_course') .
						" SET course_id  = '$course_id_value[$i]', ".
							"sort_order = '$sort_order_value[$i]' ".
			            "WHERE id = '$id_list_value[$i]' LIMIT 1";
				//echo $sql;
			    $db->query($sql);
			}else{
				if($course_id_value[$i]){
					$sql = "INSERT INTO ".$ecs->table('event_course')."(id,event_id,course_id,sort_order) ".
				           "VALUES ('','$_POST[id]', '$course_id_value[$i]','$sort_order_value[$i]')";
					//echo $sql;
				    $db->query($sql);
				}
				
			}	
		}
	}
	
    if ($exc_events->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['event_name'], 'edit', 'event');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'training.php?act=list_event&' . list_link_postfix();
        $note = vsprintf($_LANG['eventedit_succed'], $_POST['event_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑事件名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_event_name')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc_events->num("event_name",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['eventname_exist'], $name));
    }
    else
    {
        if ($exc_events->edit("event_name = '$name'", $id))
        {
            admin_log($name,'edit','training');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['eventedit_fail'], $name));
        }
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_event_sort_order')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc_events->get_name($id);

    if ($exc_events->edit("sort_order = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','event');

        make_json_result($order);
    }
    else
    {
        make_json_error(sprintf($_LANG['eventedit_fail'], $name));
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_event_show')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc_events->edit("is_show='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 切换是否推荐
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_event_commend')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc_events->edit("is_commend='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 删除事件
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_event')
{
    check_authz_json('training_manage');

    $id = intval($_GET['id']);

    /* 删除该事件的图标 */
    $sql = "SELECT event_logo FROM " .$ecs->table('events'). " WHERE event_id = '$id'";
    $logo_name = $db->getOne($sql);
    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . $logo_name);
    }

    $exc_events->drop($id);


    $url = 'training.php?act=query_event&' . str_replace('act=remove_event', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除事件图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_event_logo')
{
    /* 权限判断 */
    admin_priv('training_manage');
    $event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 取得logo名称 */
    $sql = "SELECT event_logo FROM " .$ecs->table('events'). " WHERE event_id = '$event_id'";
    $logo_name = $db->getOne($sql);

    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH .$logo_name);
        $sql = "UPDATE " .$ecs->table('events'). " SET event_logo = '' WHERE event_id = '$event_id'";
        $db->query($sql);
    }
    $link= array(array('text' => $_LANG['event_edit_lnk'], 'href' => 'training.php?act=edit_event&id=' . $event_id), array('text' => $_LANG['event_list_lnk'], 'href' => 'training.php?act=list_event'));
    sys_msg($_LANG['drop_logo_success'], 0, $link);
}
/*----------------------------------------------------------------------------------------- */
//-- 和事件有关的操作  结束
/*------------------------------------------------------------------------------------------ */



/*----------------------------------------------------------------------------------------- */
//-- 和课程有关的操作 开始
/*------------------------------------------------------------------------------------------ */

/*------------------------------------------------------ */
//-- 课程列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list_course')
{
    $smarty->assign('ur_here',      $_LANG['02_course_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['02_course_add'], 'href' => 'training.php?act=add_course'));
    $smarty->assign('full_page',    1);
    $smarty->assign('course_cat_list',get_course_cat_list());   //课程分类

    $course_list = get_courselist();

    $smarty->assign('course_list',   $course_list['course']);
    $smarty->assign('filter',       $course_list['filter']);
    $smarty->assign('record_count', $course_list['record_count']);
    $smarty->assign('page_count',   $course_list['page_count']);

    assign_query_info();
    $smarty->display('training_course_list.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query_course')
{
    $course_list = get_courselist();

    $smarty->assign('course_list',   $course_list['course']);
    $smarty->assign('filter',       $course_list['filter']);
    $smarty->assign('record_count', $course_list['record_count']);
    $smarty->assign('page_count',   $course_list['page_count']);

    make_json_result($smarty->fetch('training_course_list.htm'), '',
        array('filter' => $course_list['filter'], 'page_count' => $course_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加课程
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_course')
{
    /* 权限判断 */
    admin_priv('training_manage');

    /* 创建 html editor */
    create_html_editor('FCKeditor_desc');

    $smarty->assign('ur_here',     $_LANG['02_course_add']);
    $smarty->assign('action_link', array('text' => $_LANG['02_course_list'], 'href' => 'training.php?act=list_course'));
    $smarty->assign('form_action', 'insert_course');
    $smarty->assign('course_cat_list',get_course_cat_list());   //课程分类

    assign_query_info();
    $smarty->assign('course', array('sort_order'=>0, 'is_show'=>1,'is_commend'=>1));
    $smarty->display('training_course_info.htm');
    $smarty->assign('cfg',     $_CFG);

}
elseif ($_REQUEST['act'] == 'insert_course')
{
    /*检查课程名是否重复*/
    admin_priv('training_manage');


    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;
	//$course_start_time = local_strtotime($_POST['course_start_time']);
    
    $is_only = $exc_courses->is_only('course_name', $_POST['course_name']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['coursename_exist'], stripslashes($_POST['course_name'])), 1);
    }

    /*对简述处理*/
    if (!empty($_POST['course_brief']))
    {
        $_POST['course_brief'] = $_POST['course_brief'];
    }

     /*处理图片*/
    $img_name = $image->upload_image($_FILES['course_logo'],'traininglogo');


    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('courses')."(course_id,course_name, course_brief, course_desc, course_logo, is_show,is_commend, sort_order,course_cat,course_duration,course_tag,level) ".
           "VALUES ('','$_POST[course_name]', '$_POST[course_brief]','$_POST[FCKeditor_desc]', '$img_name', '$is_show', '$is_commend','$_POST[sort_order]','$_POSt[course_cat]','$_POST[course_duration]','$_POST[course_tag]','$_POST[level]')";
    $db->query($sql);

    admin_log($_POST['course_name'],'add','training');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'training.php?act=add_course';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'training.php?act=list_course';

    sys_msg($_LANG['courseadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑课程
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_course')
{
    /* 权限判断 */
    admin_priv('training_manage');

    $sql = "SELECT course_id,course_name, course_brief, course_desc, course_logo, is_show,is_commend, sort_order,course_cat,course_duration,course_tag,level ".
            "FROM " .$ecs->table('courses'). " WHERE course_id='$_REQUEST[id]'";
    $course = $db->GetRow($sql);
	
    /* 创建 html editor */
    create_html_editor('FCKeditor_desc',$course['course_desc']);


    $smarty->assign('ur_here',     $_LANG['course_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['02_course_list'], 'href' => 'training.php?act=list_course&' . list_link_postfix()));
    $smarty->assign('course_cat_list',get_course_cat_list());   //课程分类
    
	/* 取得关联商品 */
    $goods_list = get_course_goods($_REQUEST['id']);
    $smarty->assign('goods_list', $goods_list);
	/* 取得关联课时 */
	$lessons_list = get_course_lessons($_REQUEST['id'],$course['course_duration']);
    $smarty->assign('lessons_list', $lessons_list);
    


    $smarty->assign('course',       $course);
    $smarty->assign('form_action', 'update_course');
    $smarty->assign('cfg', $_CFG);

    assign_query_info();
    $smarty->display('training_course_info.htm');
}
elseif ($_REQUEST['act'] == 'update_course')
{
    admin_priv('training_manage');

    if ($_POST['course_name'] != $_POST['old_coursename'])
    {
        /*检查课程名是否相同*/
        $is_only = $exc_courses->is_only('course_name', $_POST['course_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['coursename_exist'], stripslashes($_POST['course_name'])), 1);
        }
    }

    /*对描述处理*/
    if (!empty($_POST['course_brief']))
    {
        $_POST['course_brief'] = $_POST['course_brief'];
    }

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;
	$course_start_time = local_strtotime($_POST['course_start_time']);

    /* 处理图片 */
    $img_name = $image->upload_image($_FILES['course_logo'],'traininglogo');
    $param = "course_name = '$_POST[course_name]',  course_brief='$_POST[course_brief]', course_desc='$_POST[FCKeditor_desc]', is_show='$is_show', is_commend='$is_commend',sort_order='$_POST[sort_order]',course_cat = '$_POST[course_cat]', course_duration='$_POST[course_duration]',  course_tag='$_POST[course_tag]' , level='$_POST[level]'  ";

    if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,course_logo = '$img_name' ";
    }

    if ($exc_courses->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['course_name'], 'edit', 'course');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'training.php?act=list_course&' . list_link_postfix();
        $note = vsprintf($_LANG['courseedit_succed'], $_POST['course_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑课程名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_course_name')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc_courses->num("course_name",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['coursename_exist'], $name));
    }
    else
    {
        if ($exc_courses->edit("course_name = '$name'", $id))
        {
            admin_log($name,'edit','training');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['courseedit_fail'], $name));
        }
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_course_sort_order')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc_courses->get_name($id);

    if ($exc_courses->edit("sort_order = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','course');

        make_json_result($order);
    }
    else
    {
        make_json_error(sprintf($_LANG['courseedit_fail'], $name));
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_course_show')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc_courses->edit("is_show='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 切换是否推荐
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_course_commend')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc_courses->edit("is_commend='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 删除课程
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_course')
{
    check_authz_json('training_manage');

    $id = intval($_GET['id']);

    /* 删除该课程的图标 */
    $sql = "SELECT course_logo FROM " .$ecs->table('courses'). " WHERE course_id = '$id'";
    $logo_name = $db->getOne($sql);
    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . $logo_name);
    }

    $exc_courses->drop($id);


    $url = 'training.php?act=query_course&' . str_replace('act=remove_course', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除课程图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_course_logo')
{
    /* 权限判断 */
    admin_priv('training_manage');
    $course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 取得logo名称 */
    $sql = "SELECT course_logo FROM " .$ecs->table('courses'). " WHERE course_id = '$course_id'";
    $logo_name = $db->getOne($sql);

    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH .$logo_name);
        $sql = "UPDATE " .$ecs->table('courses'). " SET course_logo = '' WHERE course_id = '$course_id'";
        $db->query($sql);
    }
    $link= array(array('text' => $_LANG['course_edit_lnk'], 'href' => 'training.php?act=edit_course&id=' . $course_id), array('text' => $_LANG['course_list_lnk'], 'href' => 'training.php?act=list_course'));
    sys_msg($_LANG['drop_logo_success'], 0, $link);
}
/*----------------------------------------------------------------------------------------- */
//-- 和课程有关的操作  结束
/*------------------------------------------------------------------------------------------ */


/*----------------------------------------------------------------------------------------- */
//-- 和课时有关的操作 开始
/*------------------------------------------------------------------------------------------ */

/*------------------------------------------------------ */
//-- 课时列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list_lesson')
{
    $smarty->assign('ur_here',      $_LANG['05_lesson_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['05_lesson_add'], 'href' => 'training.php?act=add_lesson'));
    $smarty->assign('full_page',    1);

    $lesson_list = get_lessonlist();
	$smarty->assign('course_list',get_course_list());   //课程分类
    

    $smarty->assign('lesson_list',   $lesson_list['lesson']);
    $smarty->assign('filter',       $lesson_list['filter']);
    $smarty->assign('record_count', $lesson_list['record_count']);
    $smarty->assign('page_count',   $lesson_list['page_count']);

    assign_query_info();
    $smarty->display('training_lesson_list.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query_lesson')
{
    $lesson_list = get_lessonlist();

    $smarty->assign('lesson_list',   $lesson_list['lesson']);
    $smarty->assign('filter',       $lesson_list['filter']);
    $smarty->assign('record_count', $lesson_list['record_count']);
    $smarty->assign('page_count',   $lesson_list['page_count']);

    make_json_result($smarty->fetch('training_lesson_list.htm'), '',
        array('filter' => $lesson_list['filter'], 'page_count' => $lesson_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加课时
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_lesson')
{
    /* 权限判断 */
    admin_priv('training_manage');
	/*连续添加*/
    $course_id = isset($_REQUEST['course_id']) ? intval($_REQUEST['course_id']) : 1;
    $course_day = isset($_REQUEST['course_day']) ? intval($_REQUEST['course_day']) : 1;


    $smarty->assign('ur_here',     $_LANG['05_lesson_add']);
    $smarty->assign('action_link', array('text' => $_LANG['05_lesson_list'], 'href' => 'training.php?act=list_lesson'));
    $smarty->assign('form_action', 'insert_lesson');
	$smarty->assign('course_list',get_course_list());   //课程分类

    assign_query_info();
    $smarty->assign('lesson', array('course_day'=>1, 'is_show'=>1,'course_id'=>$course_id));

    $smarty->display('training_lesson_info.htm');
    $smarty->assign('cfg',     $_CFG);

}
elseif ($_REQUEST['act'] == 'insert_lesson')
{
    /*检查课时名是否重复*/
    admin_priv('training_manage');


    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;
	//$lesson_start_time = local_strtotime($_POST['lesson_start_time']);
    
    $is_only = $exc_lessons->is_only('lesson_name', $_POST['lesson_name']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['lessonname_exist'], stripslashes($_POST['lesson_name'])), 1);
    }

    /*对描述处理*/
    if (!empty($_POST['lesson_brief']))
    {
        $_POST['lesson_brief'] = $_POST['lesson_brief'];
    }


    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('lessons')."(lesson_id,course_id,lesson_name, lesson_brief, is_show, sort_order,course_day,lesson_duration,level) ".
           "VALUES ('','$_POST[course_id]','$_POST[lesson_name]', '$_POST[lesson_brief]', '$is_show', '$_POST[sort_order]','$_POST[course_day]','$_POST[lesson_duration]','$_POST[level]')";
    $db->query($sql);

    admin_log($_POST['lesson_name'],'add','training');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'training.php?act=add_lesson&course_id='.$_POST['course_id'];

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'training.php?act=list_lesson';

    sys_msg($_LANG['lessonadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑课时
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_lesson')
{
    /* 权限判断 */
    admin_priv('training_manage');

    $sql = "SELECT lesson_id, course_id, lesson_name, lesson_brief, is_show, sort_order,course_day,lesson_duration,level ".
            "FROM " .$ecs->table('lessons'). " WHERE lesson_id='$_REQUEST[id]'";
    $lesson = $db->GetRow($sql);

	$smarty->assign('course_list',get_course_list());   //课程分类

    $smarty->assign('ur_here',     $_LANG['lesson_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['05_lesson_list'], 'href' => 'training.php?act=list_lesson&' . list_link_postfix()));
    $smarty->assign('lesson',       $lesson);
    $smarty->assign('form_action', 'update_lesson');
    $smarty->assign('cfg', $_CFG);

    assign_query_info();
    $smarty->display('training_lesson_info.htm');
}
elseif ($_REQUEST['act'] == 'update_lesson')
{
    admin_priv('training_manage');

    if ($_POST['lesson_name'] != $_POST['old_lessonname'])
    {
        /*检查课时名是否相同*/
        $is_only = $exc_lessons->is_only('lesson_name', $_POST['lesson_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['lessonname_exist'], stripslashes($_POST['lesson_name'])), 1);
        }
    }

    /*对描述处理*/
    if (!empty($_POST['lesson_brief']))
    {
        $_POST['lesson_brief'] = $_POST['lesson_brief'];
    }

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;

    /* 处理图片 */
    $param = "course_id='$_POST[course_id]' ,lesson_name = '$_POST[lesson_name]',  lesson_brief='$_POST[lesson_brief]', is_show='$is_show',sort_order='$_POST[sort_order]',course_day = '$_POST[course_day]', lesson_duration='$_POST[lesson_duration]', level='$_POST[level]'  ";


    if ($exc_lessons->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['lesson_name'], 'edit', 'lesson');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'training.php?act=list_lesson&' . list_link_postfix();
        $note = vsprintf($_LANG['lessonedit_succed'], $_POST['lesson_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑课时名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_lesson_name')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc_lessons->num("lesson_name",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['lessonname_exist'], $name));
    }
    else
    {
        if ($exc_lessons->edit("lesson_name = '$name'", $id))
        {
            admin_log($name,'edit','training');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['lessonedit_fail'], $name));
        }
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_lesson_sort_order')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc_lessons->get_name($id);

    if ($exc_lessons->edit("sort_order = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','lesson');

        make_json_result($order);
    }
    else
    {
        make_json_error(sprintf($_LANG['lessonedit_fail'], $name));
    }
}
/*------------------------------------------------------ */
//-- 编辑lesson 所在的天
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_lesson_course_day')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc_lessons->get_name($id);

    if ($exc_lessons->edit("course_day = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','lesson');

        make_json_result($order);
    }
    else
    {
        make_json_error(sprintf($_LANG['lessonedit_fail'], $name));
    }
}



/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_lesson_show')
{
    check_authz_json('training_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc_lessons->edit("is_show='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 删除课时
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_lesson')
{
    check_authz_json('training_manage');

    $id = intval($_GET['id']);

    /* 删除该课时的图标 */
    $sql = "SELECT lesson_logo FROM " .$ecs->table('lessons'). " WHERE lesson_id = '$id'";
    $logo_name = $db->getOne($sql);
    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . $logo_name);
    }

    $exc_lessons->drop($id);


    $url = 'training.php?act=query_lesson&' . str_replace('act=remove_lesson', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*----------------------------------------------------------------------------------------- */
//-- 和课时有关的操作  结束
/*------------------------------------------------------------------------------------------ */


/*----------------------------------------------------------------------------------------- */
//-- 和课程分类有关的操作 开始
/*------------------------------------------------------------------------------------------ */

/*------------------------------------------------------ */
//-- 课程分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list_course_cat')
{
    $smarty->assign('ur_here',      $_LANG['03_course_cat_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['03_course_cat_add'], 'href' => 'training.php?act=add_course_cat'));
    $smarty->assign('full_page',    1);

    $course_cat_list = get_course_catlist();

    $smarty->assign('course_cat_list',   $course_cat_list['course_cat']);
    $smarty->assign('filter',       $course_cat_list['filter']);
    $smarty->assign('record_count', $course_cat_list['record_count']);
    $smarty->assign('page_count',   $course_cat_list['page_count']);

    assign_query_info();
    $smarty->display('training_course_cat_list.htm');
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query_course_cat')
{
    $course_cat_list = get_course_catlist();

    $smarty->assign('course_cat_list',   $course_cat_list['course_cat']);
    $smarty->assign('filter',       $course_cat_list['filter']);
    $smarty->assign('record_count', $course_cat_list['record_count']);
    $smarty->assign('page_count',   $course_cat_list['page_count']);

    make_json_result($smarty->fetch('training_course_cat_list.htm'), '',
        array('filter' => $course_cat_list['filter'], 'page_count' => $course_cat_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加课程分类
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_course_cat')
{
    /* 权限判断 */
    admin_priv('training_manage');
	/* 创建 html editor */
    create_html_editor('FCKeditor_desc');

    $smarty->assign('ur_here',     $_LANG['03_course_cat_add']);
    $smarty->assign('action_link', array('text' => $_LANG['03_course_cat_list'], 'href' => 'training.php?act=list_course_cat'));
    $smarty->assign('form_action', 'insert_course_cat');

    assign_query_info();
    $smarty->assign('course_cat', array('sort_order'=>0, 'is_show'=>1,'is_commend'=>1));
    $smarty->display('training_course_cat_info.htm');
    $smarty->assign('cfg',     $_CFG);

}
elseif ($_REQUEST['act'] == 'insert_course_cat')
{
    /*检查课程分类名是否重复*/
    admin_priv('training_manage');


    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;
	//$course_cat_start_time = local_strtotime($_POST['course_cat_start_time']);
    
    $is_only = $exc_courses_cat->is_only('course_cat_name', $_POST['course_cat_name']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['course_catname_exist'], stripslashes($_POST['course_cat_name'])), 1);
    }

    /*对简述处理*/
    if (!empty($_POST['course_cat_brief']))
    {
        $_POST['course_cat_brief'] = $_POST['course_cat_brief'];
    }

     /*处理图片*/
    $img_name = $image->upload_image($_FILES['course_cat_logo'],'traininglogo');


    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('courses_cat')."(course_cat_id,course_cat_name, course_cat_brief, course_cat_desc, course_cat_logo) ".
           "VALUES ('','$_POST[course_cat_name]', '$_POST[course_cat_brief]','$_POST[FCKeditor_desc]', '$img_name' )";
    $db->query($sql);

    admin_log($_POST['course_cat_name'],'add','training');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'training.php?act=add_course_cat';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'training.php?act=list_course_cat';

    sys_msg($_LANG['course_catadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑课程分类
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_course_cat')
{
    /* 权限判断 */
    admin_priv('training_manage');

    $sql = "SELECT course_cat_id,course_cat_name, course_cat_brief, course_cat_desc, course_cat_logo ".
            "FROM " .$ecs->table('courses_cat'). " WHERE course_cat_id='$_REQUEST[id]'";
    $course_cat = $db->GetRow($sql);

	
    /* 创建 html editor */
    create_html_editor('FCKeditor_desc',$course_cat['course_cat_desc']);

    $smarty->assign('ur_here',     $_LANG['course_cat_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['03_course_cat_list'], 'href' => 'training.php?act=list_course_cat&' . list_link_postfix()));
    $smarty->assign('course_cat',       $course_cat);
    $smarty->assign('form_action', 'update_course_cat');
    $smarty->assign('cfg', $_CFG);

    assign_query_info();
    $smarty->display('training_course_cat_info.htm');
}
elseif ($_REQUEST['act'] == 'update_course_cat')
{
    admin_priv('training_manage');

    if ($_POST['course_cat_name'] != $_POST['old_course_catname'])
    {
        /*检查课程分类名是否相同*/
        $is_only = $exc_courses_cat->is_only('course_cat_name', $_POST['course_cat_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['course_catname_exist'], stripslashes($_POST['course_cat_name'])), 1);
        }
    }

    /*对描述处理*/
    if (!empty($_POST['course_cat_brief']))
    {
        $_POST['course_cat_brief'] = $_POST['course_cat_brief'];
    }

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $is_commend = isset($_REQUEST['is_commend']) ? intval($_REQUEST['is_commend']) : 0;
	$course_cat_start_time = local_strtotime($_POST['course_cat_start_time']);

    /* 处理图片 */
    $img_name = $image->upload_image($_FILES['course_cat_logo'],'traininglogo');
    $param = "course_cat_name = '$_POST[course_cat_name]',  course_cat_brief='$_POST[course_cat_brief]', course_cat_desc='$_POST[FCKeditor_desc]' ";

    if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,course_cat_logo = '$img_name' ";
    }

    if ($exc_courses_cat->edit($param,  $_POST['id']))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['course_cat_name'], 'edit', 'course_cat');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'training.php?act=list_course_cat&' . list_link_postfix();
        $note = vsprintf($_LANG['course_catedit_succed'], $_POST['course_cat_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}


/*------------------------------------------------------ */
//-- 删除课程分类
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_course_cat')
{
    check_authz_json('training_manage');

    $id = intval($_GET['id']);

    /* 删除该课程分类的图标 */
    $sql = "SELECT course_cat_logo FROM " .$ecs->table('courses_cat'). " WHERE course_cat_id = '$id'";
    $logo_name = $db->getOne($sql);
    if (!empty($logo_name))
    {
        @unlink(ROOT_PATH . $logo_name);
    }

    $exc_courses_cat->drop($id);


    $url = 'training.php?act=query_course_cat&' . str_replace('act=remove_course_cat', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*----------------------------------------------------------------------------------------- */
//-- 和课程分类有关的操作 结束
/*------------------------------------------------------------------------------------------ */

/*------------------------------------------------------ */
//-- 将商品加入关联
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_link_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('training_manage');

    $add_ids = $json->decode($_GET['add_ids']);
    $args = $json->decode($_GET['JSON']);
    $course_id = $args[0];

    if ($course_id == 0)
    {
        $course_id = $db->getOne('SELECT MAX(course_id)+1 AS course_id FROM ' .$ecs->table('courses'));
    }

    foreach ($add_ids AS $key => $val)
    {
        $sql = 'INSERT INTO ' . $ecs->table('goods_course') . ' (goods_id, course_id) '.
               "VALUES ('$val', '$course_id')";
        $db->query($sql, 'SILENT') or make_json_error($db->error());
    }

    /* 重新载入 */
    $arr = get_course_goods($course_id);
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

    check_authz_json('training_manage');

    $drop_goods     = $json->decode($_GET['drop_ids']);
    $arguments      = $json->decode($_GET['JSON']);
    $course_id     = $arguments[0];

    if ($course_id == 0)
    {
        $course_id = $db->getOne('SELECT MAX(course_id)+1 AS course_id FROM ' .$ecs->table('courses'));
    }

    $sql = "DELETE FROM " . $ecs->table('goods_course').
            " WHERE course_id = '$course_id' AND goods_id " .db_create_in($drop_goods);
    $db->query($sql, 'SILENT') or make_json_error($db->error());

    /* 重新载入 */
    $arr = get_course_goods($course_id);
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
//-- 搜索商品
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



?>
