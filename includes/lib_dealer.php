<?php 
/* function about dealer some*/

/**/
function get_dealer_list($limit = 0){


    $filter['dealer_sn'] = empty($_REQUEST['dealer_sn']) ? '' : trim($_REQUEST['dealer_sn']);
    $filter['dealer_name'] = empty($_REQUEST['dealer_name']) ? '' : trim($_REQUEST['dealer_name']);
    $filter['region_name'] = empty($_REQUEST['region_name']) ? '' : trim($_REQUEST['region_name']);
    $filter['audit_status'] = $_REQUEST['audit_status'];
	
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'inv_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = ' WHERE  1 ';
	
    if ($filter['dealer_sn'])
    {
        $where .= " AND a.dealer_sn LIKE '%" . mysql_like_quote($filter['dealer_sn']) . "%'";
    }
	if ($filter['dealer_name'])
    {
        $where .= " AND a.dealer_name LIKE '%" . mysql_like_quote($filter['dealer_name']) . "%'";
    }
	if (isset($filter['audit_status']) && intval($filter['audit_status']) != 9)
    {
        $where .= " AND a.is_audit  = " . $filter['audit_status'] ;
    }

    if ($filter['region_name'])
    {
        $where .= " AND cat.cat_name LIKE '%" . mysql_like_quote($filter['region_name']) . "%'";
    }




	/* 分页大小 */
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);


	/**/
    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    }
    else
    {
        $filter['page_size'] = 50;
    }
	
	

    if ($filter['region_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('city_dealer') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS cat ON cat.cat_id = a.region_id ". 
                $where;
    }
    else
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('city_dealer') ." AS a " . 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS cat ON cat.cat_id = a.region_id ". 
				$where;
		
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($count_sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$request_title = "re.lv_".$_SESSION['user_rank'];
	$limit_sql = $limit > 0 ? " LIMIT 0,$limit ": " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
	
	$sql = "SELECT a.*,cat.cat_name AS region_name ". //
			" FROM ".$GLOBALS['ecs']->table('city_dealer') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS cat ON cat.cat_id = a.region_id ". 
			"$where ORDER BY a.dealer_id ASC ".
			$limit_sql;
	//echo $sql;	 GROUP BY ad.ad_id
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{
		$res[$key]['dealer_summary'] = get_dealer_used_summary($val['dealer_id']);//用做弹窗 
		

	}
	$arr = array('dealers' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql' => $sql,'count_sql' => $count_sql, 'page_size' => $filter['page_size']);
    return $arr;
}


function get_dealer_used_summary($dealer_id){
	$dealer_id = intval($dealer_id);
	$sql = "SELECT count(*) ". //
			" FROM ".$GLOBALS['ecs']->table('city') .
			" WHERE dealer_id = $dealer_id ";
	$count = $GLOBALS['db']->getOne($sql);
	return $count;
}

function get_dealer_used_list($dealer_id){
	$dealer_id = intval($dealer_id);
	$sql = "SELECT * ". //
			" FROM ".$GLOBALS['ecs']->table('city') .
				" WHERE dealer_id = $dealer_id ";
	$res  = $GLOBALS['db']->getAll($sql);
	return $res;
}


function get_dealer_info($dealer_id){
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('city_dealer') .
			" WHERE dealer_id = $dealer_id";
		
		$tmp = $GLOBALS['db']->getRow($sql);
		$tmp['region_name'] = get_region_name($tmp['region_id']);
		return $tmp;
}


function insert_dealer($sn,$name,$region_name,$ad_id){
	$sn = trim($sn);
	$name = trim($name);
	$region_name = trim($region_name);
	
	if(!empty($sn) && !empty($name)){
		
		$tmp = get_dealer_id($sn,$name,1);

		if($tmp > 0){
			$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET dealer_id = '$tmp'  WHERE ad_id = '$ad_id'";
		    $GLOBALS['db']->query($sql);
			echo $sql."<br>";
		}else{

			if (!get_dealer_id($sn,$name,0)) {
				
				$is_dealer = stripos($sn,'K') === 0 ? 1 : 0;
				$region_id = get_region_id($region_name);
				$sql = "INSERT INTO " . $GLOBALS['ecs']->table('city_dealer') .
						" (`dealer_sn`,`dealer_name`,`region_id`,`is_dealer` ) ".
						"VALUES ('$sn', '$name', '$region_id','$is_dealer')";
				//echo $sql."<br>";

				$GLOBALS['db']->query($sql);
				echo $sql."<br>";
				
			}
			

		}
	}	
}

function get_dealer_id($sn,$name,$is_qudit = 0){
	$sql_plus = $is_qudit > 0 ? " AND is_audit = $is_qudit ": "" ;
	
	$sql = "SELECT dealer_id FROM " . $GLOBALS['ecs']->table('city_dealer') .
			" WHERE (dealer_sn LIKE '$sn' OR `dealer_name` LIKE  '$name' ) ".
			$sql_plus;
			
	$tmp = $GLOBALS['db']->getOne($sql);
	return $tmp;
}


function get_region_id($cat_name){
	$sql = 'SELECT cat_id FROM ' .$GLOBALS['ecs']->table('category').
           " WHERE cat_name LIKE '%$cat_name%' AND  parent_id = 1 ";
	
    return $GLOBALS['db']->getOne($sql);
}

function get_region_name($cat_id){
	$sql = 'SELECT cat_name FROM ' .$GLOBALS['ecs']->table('category').
           " WHERE cat_id  = $cat_id ";
	
    return $GLOBALS['db']->getOne($sql);
}


function act_city_dealer($dealer_info,$is_confirm,$dealer_id = 0){
	$sql = 'SELECT ad_id,dealer_id, col_1,col_2,col_3,col_7 FROM ' .$GLOBALS['ecs']->table('city').
           " WHERE ( col_43 LIKE '$dealer_info[dealer_sn]' OR col_45 LIKE '$dealer_info[dealer_sn]' ) "; // AND dealer_id < 1 
	$res = $GLOBALS['db']->getAll($sql);
	if($is_confirm){
		//通过的数据

		foreach($res AS $val){
			$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET dealer_id = '$dealer_info[dealer_id]'  WHERE ad_id = '$val[ad_id]'";
		    $GLOBALS['db']->query($sql);
		}
	   
		//更新 city.dealer_id
	}else{
		// 不通过的数据 已经通过的去掉
		update_city_dealer_id($dealer_id,0);
		// foreach($res AS $val){
		// 	$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET dealer_id = '0'  WHERE ad_id = '$val[ad_id]'";
		//     $GLOBALS['db']->query($sql);
		// }
	}	// 
		// echo $sql;
		// print_r($res);
	return $res;
}

function update_city_dealer_id($_old_id,$_new_id){
	$old_id = intval(trim($_old_id));
	$new_id = $_new_id == 0 ? 0 : intval(trim($_new_id));
	if($old_id){
		$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET dealer_id = '$new_id'  WHERE dealer_id = '$old_id'";
		$GLOBALS['db']->query($sql);
	}
    

}

?>