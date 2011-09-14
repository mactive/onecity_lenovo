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
require_once(ROOT_PATH . 'includes/lib_training.php');

require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/training.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';


if($action){
	//$tag_name = empty($_REQUEST['tag_name']) ? '' : trim($_REQUEST['tag_name']);
	$user_id = $_SESSION['user_id'];

	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径//
	$smarty->assign('action',     $action);
	$smarty->assign('lang',       $_LANG);

	assign_template();
	$position = assign_ur_here();

	$smarty->assign('page_title',       $position['title']);       // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);     // 当前位置
	$smarty->assign('img_path',   'themes/default/images/');     // 图片路径
	/* 页面的缓存ID */
	$cache_id = sprintf('%X', crc32($action . '-' . $_SESSION['user_id'] . '-' . $_CFG['lang']));
}



if ($action == 'default')
{

	
	// 课程列表
	$course_list = get_courselist(4);
	$smarty->assign('course_list',$course_list['course']);
	// 课程分类
	$course_cat_list = get_course_catlist(3);
	$smarty->assign('course_cat_list',$course_cat_list);

	$smarty->display('training.dwt');
}
/* 详细课程 */

elseif($action == 'course')
{        
	$course_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
	$smarty->assign('course_id', $course_id);
	
//	if (!$smarty->is_cached('training.dwt', $cache_id))
//	{
		$sql = "SELECT o.* , c.course_cat_name ".
	        	"FROM " .$ecs->table('courses'). " AS o " .
	        	"LEFT JOIN " .$ecs->table('courses_cat'). " AS c ON o.course_cat = c.course_cat_id ".
				" WHERE o.course_id='$course_id'";
	    $course = $db->GetRow($sql);
		//print_r($course);

		// 课程列表
		$course_list = get_courselist(8);
		$smarty->assign('course_list',$course_list['course']);


		/* 取得关联商品 */
	    $goods_list = get_course_goods($course_id);
	    $smarty->assign('goods_list', $goods_list);

		/* 取得关联课时 */
		$lessons_list = get_course_lessons($course_id,$course['course_duration']);
	    $smarty->assign('lessons_list', $lessons_list);

	    $smarty->assign('course',       $course);
	    
//	}
    $smarty->display('training_course.dwt');
}

/* 详细课程 */

elseif($action == 'course_cat')
{
//    include_once(ROOT_PATH . 'includes/.php');
        
	$course_cat_id  = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
	$smarty->assign('course_id', $course_id);
	
    $sql = "SELECT o.*  ".
        	"FROM " .$ecs->table('courses_cat'). " AS o " .
			" WHERE o.course_cat_id='$course_cat_id'";
    $course_cat = $db->GetRow($sql);

	// 课程列表
	$course_list = get_courselist(8,$course_cat_id);
	$smarty->assign('course_list',$course_list['course']);
	
	// 课程分类
	$course_cat_list = get_course_catlist(3);
	$smarty->assign('course_cat_list',$course_cat_list);
	
	$smarty->assign('course_cat',$course_cat);


    $smarty->display('training.dwt');

}
/* 课程列表 */
elseif($action == 'course_list')
{
//    include_once(ROOT_PATH . 'includes/.php');
        	

	// 课程列表
	$course_list = get_courselist(8);
	$smarty->assign('course_list',$course_list['course']);

    $smarty->display('training_list.dwt');

}

/* 课程列表 */
elseif($action == 'event')
{        
	$event_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
	$smarty->assign('event_id', $event_id);

	if (!$smarty->is_cached('training.dwt', $cache_id))
	{
		$sql = "SELECT o.*  ".
	        	"FROM " .$ecs->table('events'). " AS o " .
				" WHERE o.event_id='$event_id'";
	    $event = $db->GetRow($sql);
		//print_r($event);

		// 课程列表
		$event_list = get_eventlist(8);
		$smarty->assign('event_list',$event_list['event']);

		/* 取得事件的课程列表 */
		$event_courses = get_event_courses($event['event_id'],$event['event_duration'],0);
		$smarty->assign('event_courses',$event_courses);   //课程分类

	    $smarty->assign('event',       $event);
	}
	    
    $smarty->display('training.dwt');
}

/* 课程列表 */
elseif($action == 'event_list')
{
//    include_once(ROOT_PATH . 'includes/.php');
        	
	// 课程列表
	$event_list = get_eventlist();
	$smarty->assign('event_list',$event_list['event']);

    $smarty->display('training_list.dwt');

}


/* 课程列表 */
elseif($action == 'video_list')
{
//    include_once(ROOT_PATH . 'includes/.php');
	$course_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
	// 课程列表
	$video_list = get_videolist(0,$course_id);
	$smarty->assign('video_list',$video_list['video']);

    $smarty->display('training_list.dwt');

}

/* 显示模板 */


?>