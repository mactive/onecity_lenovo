<?php
/**
 * 获取课程列表 配和 list_event 和 query_event
 *
 * @access  public
 * @return  array
 */
function get_eventlist($limit = 0,$event_cat = 0 )
{
	$filter['event_name'] = empty($_REQUEST['event_name']) ? '' : trim($_REQUEST['event_name']);
    $filter['is_commend'] = empty($_REQUEST['is_commend']) ? '' : intval($_REQUEST['is_commend']);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'event_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);


	$where = 'WHERE 1 ';
    if ($filter['event_name'])
    {
        $where .= " AND o.event_name LIKE '%" . mysql_like_quote($filter['event_name']) . "%'";
    }
    if ($filter['is_commend'])
    {
        $where .= " AND o.is_commend = '$filter[is_commend]'";
    }
	if($event_cat)
	{
		$where .= " AND o.event_cat =  " . $event_cat; 
	}
	if($limit)
	{
		$where .= " LIMIT " . $limit; 
	}



	$event_sql = "SELECT o.*  ".
        	"FROM " .$GLOBALS['ecs']->table('events'). " AS o " .
			" $where ";
			//echo $event_sql;
	
    //$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

	$row = $GLOBALS['db']->getAll($event_sql);
 	/* 格式话数据 */

	//print_r($row);
	$arr = array('event' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;

}

/* 取得时间关联课程 */
function get_event_courses($event_id,$event_duration)
{
	$list = array();
	$sql = 	' SELECT o.id,o.event_id,o.sort_order,o.course_id, '.
			' c.course_name, c.course_duration, c.course_logo, c.course_brief'.
			' FROM ' .$GLOBALS['ecs']->table('event_course') . ' AS o' .
			' LEFT JOIN ' . $GLOBALS['ecs']->table('courses') . ' AS c ON c.course_id = o.course_id '.
			" WHERE o.event_id = '$event_id' AND o.course_id > 0 ".
			" ORDER BY o.sort_order ASC ";
			
	$list = $GLOBALS['db']->getAll($sql);
	//print_r($list);

   	return $list;
}

/**
 * 获取课程列表 配和 list_video 和 query_video
 *
 * @access  public
 * @return  array
 */
function get_videolist($limit = 0,$course_id = 0 )
{
	$filter['video_name'] = empty($_REQUEST['video_name']) ? '' : trim($_REQUEST['video_name']);
    $filter['is_commend'] = empty($_REQUEST['is_commend']) ? '' : intval($_REQUEST['is_commend']);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'video_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);


	$where = 'WHERE 1 ';
    if ($filter['video_name'])
    {
        $where .= " AND o.video_name LIKE '%" . mysql_like_quote($filter['video_name']) . "%'";
    }
    if ($filter['is_commend'])
    {
        $where .= " AND o.is_commend = '$filter[is_commend]'";
    }
	if($course_id)
	{
		$where .= " AND o.course_id =  " . $course_id; 
	}
	if($limit)
	{
		$where .= " LIMIT " . $limit; 
	}



	$video_sql = "SELECT o.*  ".
        	"FROM " .$GLOBALS['ecs']->table('videos'). " AS o " .
			" $where ";
			//echo $video_sql;
	
    //$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

	$row = $GLOBALS['db']->getAll($video_sql);
 	/* 格式话数据 */

	//print_r($row);
	$arr = array('video' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;

}

/**
 * 获取课程列表 配和 list_course 和 query_course
 *
 * @access  public
 * @return  array
 */
function get_courselist($limit = 0,$course_cat = 0 )
{
	$filter['course_name'] = empty($_REQUEST['course_name']) ? '' : trim($_REQUEST['course_name']);
    $filter['is_commend'] = empty($_REQUEST['is_commend']) ? '' : intval($_REQUEST['is_commend']);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'course_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);


	$where = 'WHERE 1 ';
    if ($filter['course_name'])
    {
        $where .= " AND o.course_name LIKE '%" . mysql_like_quote($filter['course_name']) . "%'";
    }
    if ($filter['is_commend'])
    {
        $where .= " AND o.is_commend = '$filter[is_commend]'";
    }
	if($course_cat)
	{
		$where .= " AND o.course_cat =  " . $course_cat; 
	}
	if($limit)
	{
		$where .= " LIMIT " . $limit; 
	}



	$course_sql = "SELECT o.* , c.course_cat_name ".
        	"FROM " .$GLOBALS['ecs']->table('courses'). " AS o " .
        	"LEFT JOIN " .$GLOBALS['ecs']->table('courses_cat'). " AS c ON o.course_cat = c.course_cat_id ".
			" $where ";
			//echo $course_sql;
	
    //$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

	$row = $GLOBALS['db']->getAll($course_sql);
 	/* 格式话数据 */

	//print_r($row);
	$arr = array('course' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;

}

/**
 * 获取课程分类列表 配和 list_course_cat 和 query_course_cat
 *
 * @access  public
 * @return  array
 */
function get_course_catlist($limit = 0)
{

	$where = 'WHERE 1 ';
	if($limit)
	{
		$where .= " LIMIT " . $limit; 
	}
	
	$course_cat_sql = 'SELECT o.*'.				
            " FROM " . $GLOBALS['ecs']->table('courses_cat') . " AS o ".
			" $where ";
	
	$row = $GLOBALS['db']->getAll($course_cat_sql);

	//print_r($row);
    return $row;

}

/**
 * 取得状态列表
 * @param   string  $type   类型：all | order | shipping | payment
 */
function get_course_cat_list()
{
    $list = array();

	$sql = 'SELECT course_cat_id, course_cat_name'." FROM " . $GLOBALS['ecs']->table('courses_cat') .
	 	   'WHERE 1 ORDER BY course_cat_id ASC';
	$temp_list = array();
	$temp_list = $GLOBALS['db']->getAll($sql);
	foreach ($temp_list AS $key => $value)
    {
		$key = $value['course_cat_id'];
        $list[$key] = $value['course_cat_name'];
    }

	//print_r($list);
    return $list;
}

/**
 * 取得状态列表
 * @param   string  $type   类型：all | order | shipping | payment
 */
function get_course_list()
{
    $list = array();

	$sql = 'SELECT course_id, course_name'." FROM " . $GLOBALS['ecs']->table('courses') .
	 	   'WHERE 1 ORDER BY course_id ASC';
	$temp_list = array();
	$temp_list = $GLOBALS['db']->getAll($sql);
	foreach ($temp_list AS $key => $value)
    {
		$key = $value['course_id'];
        $list[$key] = $value['course_name'];
    }

	//print_r($list);
    return $list;
}


/**
 * 获取课时列表 配和 list_lesson 和 query_lesson
 *
 * @access  public
 * @return  array
 */
function get_lessonlist($course_id = 0)
{
	$filter['lesson_name'] = empty($_REQUEST['lesson_name']) ? '' : trim($_REQUEST['lesson_name']);
    $filter['is_commend'] = empty($_REQUEST['is_commend']) ? '' : intval($_REQUEST['is_commend']);
    $filter['course_id'] = empty($_REQUEST['course_id']) ? '' : intval($_REQUEST['course_id']);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'lesson_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);


	$where = 'WHERE 1 ';
    if ($filter['lesson_name'])
    {
        $where .= " AND o.lesson_name LIKE '%" . mysql_like_quote($filter['lesson_name']) . "%'";
    }
    if ($filter['is_commend'])
    {
        $where .= " AND o.is_commend = '$filter[is_commend]'";
    }
    if ($filter['course_id'])
    {
        $where .= " AND o.course_id = '$filter[course_id]'";
    }
    if ($course_id)
    {
        $where .= " AND o.course_id = '$course_id'";
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
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('lessons') ." AS o " . $where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

	$lesson_sql = 'SELECT o.*'.				
            " FROM " . $GLOBALS['ecs']->table('lessons') . " AS o ".
			"$where ORDER BY $filter[sort_by] $filter[sort_order] ".
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
			//echo $lesson_sql;
	
    //$res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

	$row = $GLOBALS['db']->getAll($lesson_sql);
	//print_r($row);
	$arr = array('lesson' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;

}



/* 把商品删除关联 */
function drop_link_goods($goods_id, $course_id)
{
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_course') .
            " WHERE goods_id = '$goods_id' AND course_id = '$course_id' LIMIT 1";
    $GLOBALS['db']->query($sql);
    create_result(true, '', $goods_id);
}
/* 取得课程关联课时 */
function get_course_lessons($course_id,$course_duration)
{
	$list = array();
	$sql = 	' SELECT l.*,c.course_name,c.course_duration'.
			' FROM ' .$GLOBALS['ecs']->table('courses') . ' AS c' .
			' LEFT JOIN ' . $GLOBALS['ecs']->table('lessons') . ' AS l ON l.course_id = c.course_id '.
			" WHERE c.course_id = '$course_id' ".
			" ORDER BY l.sort_order ASC ";
			
	$list = $GLOBALS['db']->getAll($sql);
    
	$list_day = array();
	foreach($list AS $key => $val){
		$j = $val['course_day'];
		$k = $val['sort_order'];
		$list_day[$j]['course_name'] = $val['course_name'];
		$list_day[$j]['course_duration'] = $val['course_duration'];
		$list_day[$j]['course_day'] = $j;
		$list_day[$j]['lessons'][$k] = $val;
		//array_push($list_day[$j]['lessons'],$val);
	}
//	print_r($list_day);
	return $list_day;
}

/* 取得文课程关联商品 */
function get_course_goods($course_id)
{
    $list = array();
    $sql  = 'SELECT g.goods_id, g.goods_name,g.goods_thumb'.
            ' FROM ' . $GLOBALS['ecs']->table('goods_course') . ' AS ga'.
            ' LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = ga.goods_id'.
            " WHERE ga.course_id = '$course_id'";
    $list = $GLOBALS['db']->getAll($sql);
    return $list;
}



?>