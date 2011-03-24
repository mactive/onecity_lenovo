<?php
/*区域航班*/
function get_city_children($arr)
{
	$all_array = array();
	foreach($arr AS $val){
		//$tt = array_merge(array($val), array_keys(cat_list($val, 0, false)));
		$tt =  array_keys(cat_list($val, 0, false));
		$all_array  = array_merge($all_array,$tt);
		/*
		echo "<br>".count($tt)."<br>";
		print_r($tt);
		echo "<br><br>==============<br><br>";
		*/
	}
	/*
	echo "<br><br>==============<br><br>";
	echo "<br>".count($all_array)."<br>";
	print_r($all_array);
	*/
	return 'a.cat_id ' . db_create_in(array_unique($all_array));		
	
}

function get_user_region()
{
	/* 检查有没有缺货 */
    $sql = "SELECT msn FROM ".$GLOBALS['ecs']->table('users')." WHERE user_id = '$_SESSION[user_id]' AND user_rank > 0 ";
    $res = $GLOBALS['db']->GetOne($sql);
	return explode(",",$res);
}




/**/
function get_city_list($children){
	
	$filter['start_price'] = empty($_REQUEST['start_price']) ? 0 : $_REQUEST['start_price'];
    $filter['end_price'] = empty($_REQUEST['end_price']) ? 1000000 : $_REQUEST['end_price'];

    $filter['county_name'] = empty($_REQUEST['county_name']) ? '' : trim($_REQUEST['county_name']);
    $filter['city_name'] = empty($_REQUEST['city_name']) ? '' : trim($_REQUEST['city_name']);
    $filter['region_name'] = empty($_REQUEST['region_name']) ? '' : trim($_REQUEST['region_name']);
    $filter['audit_status'] = $_REQUEST['audit_status'];
	
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'inv_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = ' WHERE '. $children ." AND a.sys_level = 5 ";
	
    if ($filter['county_name'])
    {
        $where .= " AND a.cat_name LIKE '%" . mysql_like_quote($filter['county_name']) . "%'";
    }
	if ($filter['city_name'])
    {
        $where .= " AND a1.cat_name LIKE '%" . mysql_like_quote($filter['city_name']) . "%'";
    }
    if ($filter['region_name'])
    {
        $where .= " AND a3.cat_name LIKE '%" . mysql_like_quote($filter['region_name']) . "%'";
    }
    if ($filter['audit_status'] !='')
    {
        $where .= " AND a.audit_status > 0  AND a.is_audit_confirm = $filter[audit_status] ";
    }



	/* 分页大小 */
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

    $filter['page_size'] = 100;

	/*
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
        $filter['page_size'] = 100;
    }
	*/
	
	/* 记录总数 */
    if ($filter['city_name'])
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id "
               . $where;
    }
    elseif ($filter['region_name'])
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
                $where;
    }
    else
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') ." AS a " . 
				$where;
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	
	$sql = "SELECT a.cat_name AS county, a.market_level, a.cat_id, a.is_upload, a.audit_status, ".
			"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region, ".
			"c.cat_id AS city_id , c.user_time, a.is_audit_confirm FROM " . 
			$GLOBALS['ecs']->table('category') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ". 
			" LEFT JOIN " .$GLOBALS['ecs']->table('city'). ' AS c ON c.cat_id = a.cat_id '.
			"$where ORDER BY a.is_upload DESC, audit_status DESC ".
			" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
	//echo $sql;	
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{
		$sql_1 = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('ideas_gallery') ." WHERE idea_id = $val[cat_id]";
		$res[$key]['photo_num'] = $GLOBALS['db']->getOne($sql_1);
		$res[$key]['user_time'] = local_date('m－d', $val['user_time']);
	}
	$arr = array('citys' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql' => $sql);
    return $arr;
}

/* 生成空数据 */
function make_city_content($count = CONTENT_COLS)
{
	$city_content = array();
	
	for($i=1;$i<=$count;$i++){
		$key = "col_".$i;
		$city_content[$key]= "";
	}
	return $city_content;
}

/* 将 excel数据 填充进入 空数组 */
function full_city_content($xls_array,$city_content)
{	
	if(count($xls_array)){
		foreach($xls_array AS $key => $val){
			//为7列数字列 清除 _)
			if($key == 19 || $key == 30 || $key == 31 || $key == 32 || $key == 34 || $key == 36 || $key == 38)
			{
				$city_content["col_".$key] = substr($val,0,strlen(trim($val))-2);
			}else{
				$city_content["col_".$key] = trim($val);
			}
		}	
	}
	$city_content['cat_id'] = get_cat_id_by_name($xls_array[3]);
	$city_content['user_id'] = $_SESSION['user_id'];
	$city_content['user_time'] = gmtime();
	return $city_content;
}

function get_cat_id_by_name($cat_name)
{
	$sql = "SELECT cat_id  FROM " .$GLOBALS['ecs']->table('category') .
			" WHERE cat_name LIKE '%". $cat_name ."%' ";
	//echo $sql;	
	
	$res = $GLOBALS['db']->getOne($sql);
	return $res;
}

?>