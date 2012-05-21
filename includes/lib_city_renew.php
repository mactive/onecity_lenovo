<?php
/*区域航班*/
function get_city_children($arr)
{
	$all_array = array();
	foreach($arr AS $val){
		//$tt = array_merge(array($val), array_keys(cat_list($val, 0, false)));
		$tt =  array_keys(cat_list($val, 0, false));
		$all_array  = array_merge($all_array,$tt);

	}

	return 'a.cat_id ' . db_create_in(array_unique($all_array));		
	
}

function get_city_children_a($arr)
{
	$all_array = array();
	foreach($arr AS $val){
		$tt =  array_keys(cat_list($val, 0, false));
		$all_array  = array_merge($all_array,$tt);
	}
	return 'a.city_id ' . db_create_in(array_unique($all_array));		
	
}


function get_user_region()
{
	/* 检查有没有缺货 */
    $sql = "SELECT msn FROM ".$GLOBALS['ecs']->table('users')." WHERE user_id = '$_SESSION[user_id]' AND user_rank > 0 ";
    $res = $GLOBALS['db']->getOne($sql);
	return explode(",",$res);
}

function get_user_permission($user_region)
{
	$user_permission = array();
	foreach($user_region AS $key => $val){
		if(!empty($val)){
			$cat_name = $GLOBALS['db']->getOne("SELECT cat_name FROM ".$GLOBALS['ecs']->table('category')." WHERE cat_id = $val ");
		    array_push($user_permission,$cat_name);	
		}	
	}
	return $user_permission;
}


/**/
function get_city_list($children,$limit = 0){

	$filter['start_price'] = empty($_REQUEST['start_price']) ? 0 : $_REQUEST['start_price'];
    $filter['end_price'] = empty($_REQUEST['end_price']) ? 1000000 : $_REQUEST['end_price'];

    $filter['county_name'] = empty($_REQUEST['county_name']) ? '' : trim($_REQUEST['county_name']);
    $filter['city_name'] = empty($_REQUEST['city_name']) ? '' : trim($_REQUEST['city_name']);
    $filter['region_name'] = empty($_REQUEST['region_name']) ? '' : trim($_REQUEST['region_name']);
    $filter['market_level'] = empty($_REQUEST['market_level']) ? '' : trim($_REQUEST['market_level']);
    $filter['audit_status'] = empty($_REQUEST['audit_status']) ? 0 : $_REQUEST['audit_status'];
    $filter['has_new'] = empty($_REQUEST['has_new']) ? 0 : $_REQUEST['has_new'];
	
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
	if ($filter['market_level'])
    {
        $where .= " AND a.market_level LIKE '%" . mysql_like_quote($filter['market_level']) . "%'";
    }
    if ($filter['audit_status'])
    {
        $where .= " AND a.audit_status = $filter[audit_status] ";
    }

    if ($filter['has_new'])
    {
		if($filter['has_new'] == 1){
	        $where .= " AND a.has_new = 1 ";
		}elseif($filter['has_new'] == 3){
			$where .= " AND a.has_new = 0 ";
		}else{
			$where .= "";
		}
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
	
	
	/* 记录总数 */
    if ($filter['city_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id "
               . $where;
    }
    elseif ($filter['region_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
                $where;
    }
    else
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') ." AS a " . 
				$where;
		
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($count_sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$request_title = "re.lv_".$_SESSION['user_rank'];
	$limit_sql = $limit > 0 ? " LIMIT 0,$limit ": " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
	
	$sql = "SELECT a.cat_name AS county, a.market_level, a.cat_id ,a.is_upload, a.audit_status, a.is_audit_confirm, a.renew_audit,
			a.has_new,re.renew_num,re.change_num,re.is_audit_confirm ,re.audit_status ,". 
			"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region ".
			",$request_title AS city_request " . 

			" FROM ".$GLOBALS['ecs']->table('category') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ". 
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_request').   " AS re ON re.city_id = a.cat_id ".
			
			//" LEFT JOIN " .$GLOBALS['ecs']->table('city'). 		' AS c  ON c.city_id = a.cat_id '.
			//" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad'). 	' AS ad ON ad.city_id = a.cat_id '.
			"$where ORDER BY re.renew_num DESC, re.change_num DESC, a.is_upload DESC, a.audit_status DESC ". 
			$limit_sql;
	//echo $sql;	 GROUP BY ad.ad_id
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{

		$res[$key]['is_checked'] = get_city_ad_is_checked($val['cat_id']);
		$res[$key]['ad_count'] = get_city_ad_num($val['cat_id']);		//上传条数
		$res[$key]['time_summary'] = get_city_ad_is_checked($val['cat_id'],1); 	//最新检查时间
		$res[$key]['renew_audit_request'] = intval($res[$key]['ad_count'] - $val['renew_audit']);

/*
		$ad_list = get_ad_list_by_cityid($val['cat_id']);//用做弹窗 
		$ad_summary = get_ad_summary($ad_list);
		$res[$key]['status_summary'] = get_ad_status_summary($ad_list);
		$res[$key]['photo_summary'] = $ad_summary['photo_summary']/4; //照片总量
		$res[$key]['time_summary'] = $ad_summary['time_summary']; 	//最新上传时间
		$res[$key]['audit_status_summary'] = $ad_summary['audit_status_summary']; //已经审核数量
		$res[$key]['audit_confirm_summary'] = $ad_summary['audit_confirm_summary']; //审核通过数量

*/
	}
	$arr = array('citys' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql' => $sql,'count_sql' => $count_sql, 'page_size' => $filter['page_size']);
    return $arr;
}

function get_city_ad_num($city_id)
{
	$sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('city_ad') .
				" WHERE city_id = $city_id AND is_delete = 0 AND renew_upload = 0 ";

	return $GLOBALS['db']->getOne($sql);
}

function act_renew_plus($city_id,$is_plus = 1){
	$data['renew_num'] = $GLOBALS['db']->getOne("SELECT renew_num FROM " . $GLOBALS['ecs']->table('city_request') .
				" WHERE city_id = $city_id ");//已经修改过
	if ($is_plus == 0) {
		$data['renew_num'] -= 1;
	}else{
		$data['renew_num'] += 1;
	}
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_request'), $data, 'update', "city_id='$city_id'");	
}


function act_change_plus($city_id,$is_plus = 1){
	$data['change_num'] = $GLOBALS['db']->getOne("SELECT change_num FROM " . $GLOBALS['ecs']->table('city_request') .
				" WHERE city_id = $city_id ");//已经修改过
	if ($is_plus == 0) {
		$data['change_num'] -= 1;
	}else{
		$data['change_num'] += 1;
	}
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_request'), $data, 'update', "city_id='$city_id'");	
}


function act_renew_audit($city_id,$is_plus = 1){
	$data['renew_audit'] = $GLOBALS['db']->getOne("SELECT renew_audit FROM " . $GLOBALS['ecs']->table('category') .
				" WHERE cat_id = $city_id ");//已经修改过
	if ($is_plus == 0) {
		$data['renew_audit'] -= 1;
	}else{
		$data['renew_audit'] += 1;
	}
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category'), $data, 'update', "cat_id='$city_id'");	

}



// 检查城市下的牌子是不是都检查过了
function get_city_ad_is_checked($city_id,$time = 0 ){
	if($city_id){
		$sql = "SELECT checked_time FROM " . $GLOBALS['ecs']->table('city_ad') .
				" WHERE city_id = $city_id AND is_delete = 0 AND renew_upload = 0 ";
		$res = $GLOBALS['db']->getCol($sql);
		$count = 0;
		foreach ($res as $var) { 
			if ($var > 0) {
				$count += 1;
			}
		}
		if ($time && count($res)) {
			return local_date('Y-m-d', max($res));
		}else{
			if ($count < count($res)) {
				return 0;
			}else{
				return 1;
				}
		}


	}else{
		return 0;
	}
}

// 检查城市下的牌子是不是都检查过了
function get_city_ad_is_modified($city_id){
	if($city_id){
		$sql = "SELECT is_change,checked_time FROM " . $GLOBALS['ecs']->table('city_ad') .
				" WHERE city_id = $city_id AND is_delete = 0  ";
		$res = $GLOBALS['db']->getAll($sql);
		$count = 0;
		foreach ($res as $key => $var) { 
			if ($var['is_change']) {
				$count += 1;
			}
		}
		return $count;


	}else{
		return 0;
	}
}

//修改过的项目
function get_changed_detail($ad_id){
	$sql = "SELECT col_name,value,old_value  FROM " . $GLOBALS['ecs']->table('city_ad_log') .
			" WHERE ad_id = $ad_id ";
	$res = $GLOBALS['db']->getAll($sql);
	$data = array();
	foreach ($res as $key => $value) {
		$data[$value['col_name']] =  $value['old_value'];
	}
	return $data;
}


function get_ad_status_summary($ad_list){
	$audit_level_array = array("1"=>"0","2"=>"0","3"=>"0","4"=>"0","5"=>"0");
	foreach($audit_level_array AS $k => $v){
		foreach($ad_list AS $key => $val){
			if($k == $val['audit_status'] && $val['is_upload']  && $val['photo_num'] && $val['is_audit_confirm']){
					$audit_level_array[$k+1] += 1;
			}else{
				if($val['audit_status'] == 1 && $_SESSION['user_rank'] == 2 && $val['is_audit_confirm'] == 0 && $val['photo_num']){
					$audit_level_array[$k+1] += 1;
				}				
			}
		}
	}
	return $audit_level_array;
	
}
function get_ad_summary($ad_list)
{
	$res = array();
	$res['time_summary'] = 	$res['audit_status_summary'] = 	$res['audit_confirm_summary'] = 0;
	foreach($ad_list AS $key=>$val)
	{
		$res['photo_summary'] += $val['photo_num'];
		$res['time_summary'] =  $res['time_summary'] < $val['time_original'] ? $val['time_original'] : $res['time_summary'] ;
		if($val['audit_status'] > 1){
			$res['audit_status_summary'] += 1;
			if($val['is_audit_confirm'] && $val['audit_status'] == 5){
				$res['audit_confirm_summary'] += 1;
			}
		}
	}
	
	$res['time_summary'] = local_date('Y-m-d', $res['time_summary']);
	
	return $res;
}
//一个城市的左右广告列表
function get_ad_list_by_cityid($city_id)
{
	$sql = "SELECT a.*,c.col_7,c.ad_sn,COUNT(g.img_id) AS photo_num ".
			//", c.city_id, c.user_time FROM " . 
			" FROM ".$GLOBALS['ecs']->table('city_ad') . " AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city'). 		' AS c ON c.ad_id = a.ad_id '.
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_gallery'). ' AS g ON g.ad_id = a.ad_id '.
			" WHERE a.city_id = $city_id AND a.is_delete = 0  AND a.renew_upload =  0 GROUP BY c.record_id ORDER BY a.ad_id ASC ";
		//echo $sql."<br>";	
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{
		$res[$key]['time_original'] = $val['checked_time'];
		$res[$key]['user_time'] = local_date('Y-m-d', $val['checked_time']);
	}
	return $res;
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
			/*
			if($key == 19 || $key == 22 || $key == 30 || $key == 31 || $key == 32 || $key == 34 || $key == 36 || $key == 38)
			{
				$city_content["col_".$key] = substr($val,0,strlen(trim($val))-2);
			}
			*/
			
			if(strripos($val,"_)")){
				$city_content["col_".$key] = substr($val,0,strlen(trim($val))-2);
			}elseif(strripos($val,",")){
				$city_content["col_".$key] = str_replace(",","",$val);
			}
			else{
				$city_content["col_".$key] = trim($val);
			}
		}	
	}
	$city_content['city_id'] = get_cat_id_by_name($xls_array[3]);
	$city_content['user_id'] = $_SESSION['user_id'];
	$city_content['user_time'] = gmtime();
	return $city_content;
}

function get_market_level($cat_name,$is_city_id = 0)
{
	if($is_city_id){
		$sql = "SELECT market_level  FROM " . $GLOBALS['ecs']->table('category') .
				" WHERE cat_id = $is_city_id ";
	}else{
		$sql = "SELECT market_level  FROM " .$GLOBALS['ecs']->table('category') .
				" WHERE cat_name LIKE '". $cat_name ."' ";
		//echo $sql;
	}	
	
	$res = $GLOBALS['db']->getOne($sql);
	return $res;
}


function get_audit_time($ad_id,$user_rank){
	$sql = "SELECT time FROM " .$GLOBALS['ecs']->table('city_ad_audit') .
			" WHERE ad_id = $ad_id AND user_rank = $user_rank AND audit_note  LIKE '审核通过' ";
	//echo $sql;	
	
	$res = $GLOBALS['db']->getOne($sql);
	//$tt = date("l, F d, Y @ g:i A",strtotime($res));
	$tmp = empty($res) ? "未完成" : date("Y-m-d H:i",(strtotime($res)));
	//echo $res."-".$tt."<br>";
	return $tmp;
}


function get_cat_id_by_name($cat_name)
{
	$sql = "SELECT cat_id  FROM " .$GLOBALS['ecs']->table('category') .
			" WHERE cat_name LIKE '". $cat_name ."' ";
//	echo $sql;	
	
	$res = $GLOBALS['db']->getOne($sql);
	return $res;
}

function get_cat_name_by_id($cat_id)
{
	$sql = "SELECT cat_name  FROM " .$GLOBALS['ecs']->table('category') .
			" WHERE cat_id = $cat_id ";
//	echo $sql;	
	
	$res = $GLOBALS['db']->getOne($sql);
	return $res;
}

function get_city_id($ad_id)
{
	$sql = "SELECT city_id  FROM " .$GLOBALS['ecs']->table('city_ad') .
			" WHERE ad_id = $ad_id ";
	//echo $sql;	
	
	$res = $GLOBALS['db']->getOne($sql);
	return $res;
}

function get_city_info($ad_id = 0)
{
	if($ad_id){
		$sql = 	" SELECT * FROM " . $GLOBALS['ecs']->table('city') . " WHERE ad_id = $ad_id";
		
		$city_info = $GLOBALS['db']->getRow($sql);
		return $city_info;
	}
}

function get_ad_info($ad_id = 0)
{
	
	if($ad_id){
		$ad_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('city_ad') . " WHERE ad_id = $ad_id");
		return $ad_info;
	}
}






function get_ad_photo_info($ad_id = 0,$project = 0 ){
	if($ad_id){
		$photo_info = $GLOBALS['db']->getAll("SELECT * FROM " . $GLOBALS['ecs']->table('city_gallery') . " WHERE ad_id = $ad_id AND feedback = $project ");
		foreach ($photo_info as $key => $data) {
			if ($data['img_id'] <= 26952) {
				$photo_info[$key]['img_url'] = '../'.$data['img_url'];
				$photo_info[$key]['thumb_url'] = '../'.$data['thumb_url'];
				$photo_info[$key]['img_original'] = '../'.$data['img_original'];			
			}
		}

		return $photo_info;
	}
}
//最新的审核记录
function get_last_audit_note($ad_id,$project){
	if($ad_id){
		$photo_info = $GLOBALS['db']->getOne("SELECT audit_note FROM " . $GLOBALS['ecs']->table('city_ad_audit') . " WHERE ad_id = $ad_id AND feedback_audit = $project  ORDER BY record_id DESC limit 1 ");
		return $photo_info;
	}
}

function get_project_ad_photo_info($ad_id = 0,$project = 0){
	$sql = "SELECT g.*,p.project_name FROM " . $GLOBALS['ecs']->table('city_gallery') ." AS g ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('project') . " AS p ON p.project_id = g.feedback ". 
			 " WHERE g.ad_id = $ad_id AND g.feedback != $project ";
	
	
	$res = $GLOBALS['db']->getAll($sql);
	
	$photo_info = array("0"=>array(),"1"=>array(),"2"=>array(),"3"=>array(),"4"=>array());
	foreach($res AS $key => $val){
		foreach($photo_info AS $k=>$v){
			if($val['feedback'] == $k){
				array_push($photo_info[$k],$val);
			}
		}
		
	}
	return $photo_info;
}

/* 获得审核路径 */
function get_audit_path($ad_id = 0,$audit_level_array,$project_id = 0)
{
	$project_sql = $project_id == 0 ? "" : " AND feedback_audit  = $project_id " ;
	switch($type)
    {
        case 'audit':
			$type_sql = " AND price_audit = 0 AND feedback_audit = 0 ";
			break;
        case 'price':
			$type_sql = " AND price_audit = 1 AND feedback_audit = 0 ";
			break;
        case 'feedback':
			$type_sql = " AND price_audit = 0 AND feedback_audit = 1 ";
			break;
    }

	
	$audit_path = array(); //"2","3","4","5"级别
	
	$sql = "SELECT c.*, u.user_name ,u.real_name  FROM " . $GLOBALS['ecs']->table('city_ad_audit') . " AS c ". // , r.rank_name
 			" LEFT JOIN " .$GLOBALS['ecs']->table('users') . " AS u ON u.user_id = c.user_id ". 
 			//" LEFT JOIN " .$GLOBALS['ecs']->table('user_rank') . " AS r ON r.rank_id = c.user_rank ". 
			" WHERE c.ad_id = $ad_id ".$project_sql.$type_sql."ORDER BY time DESC ";
	//echo $sql;
	$res = $GLOBALS['db']->getAll($sql);
	
	foreach($audit_level_array AS $v){
		$unit = array();
		foreach($res AS $val){
			if($val['user_rank'] == $v){
				array_push($unit,$val);
			}
		}
		$audit_path[$v] = $unit;
	}
	
	//print_r($audit_path);
	return $audit_path;
}


function is_exist_city_ad($city_id,$col_7)
{
	$sql = "SELECT a.audit_status ,c.record_id FROM " . $GLOBALS['ecs']->table('city') . " AS c ". // , r.rank_name
 			" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS a ON a.ad_id = c.ad_id ". 
 			//" LEFT JOIN " .$GLOBALS['ecs']->table('user_rank') . " AS r ON r.rank_id = c.user_rank ". 
			" WHERE c.city_id = $city_id AND  c.col_7 LIKE '$col_7' ";
	$res = $GLOBALS['db']->getRow($sql);
	return $res;
}
//城市级别不对
function get_sys_level($city_id)
{
	if($city_id){
		$sql = "SELECT sys_level  FROM " . $GLOBALS['ecs']->table('category') .
				" WHERE cat_id = $city_id ";
		$res = $GLOBALS['db']->getOne($sql);
		//echo $sql."<br>";
		return $res;
	}else{
		return false;
	}
	
}

/*删除之后更新数据*/
function act_city_request_delete($city_id,$level,$is_delete = 0){
	if($is_delete){
		$next_level = $level + 1;
		$next_level_col = "lv_".$next_level;
		$sql = "SELECT $next_level_col FROM ".$GLOBALS['ecs']->table('city_request')." WHERE city_id = '$city_id' ";
		$res = $GLOBALS['db']->getOne($sql);
		//echo $sql."<br>";
		
		$next_num  = $res - 1;
	    	$sql_2 = "UPDATE " . $GLOBALS['ecs']->table('city_request') . 
					 " SET $next_level_col = '$next_num' WHERE city_id = '$city_id' ";
		//echo $sql_2."<br>";
		$GLOBALS['db']->query($sql_2);
	}
}

function act_city_request($city_id,$level,$is_cancel = 0)
{
	$now_level_col = "lv_".$level;
	
	$prev_level = $level - 1;
	$prev_level_col = "lv_".$prev_level;
	
	$next_level = $level + 1;
	$next_level_col = "lv_".$next_level;
	
	if($is_cancel){
		$sql = "SELECT $now_level_col,$prev_level_col FROM ".$GLOBALS['ecs']->table('city_request')." WHERE city_id = '$city_id' ";
	}else{
		$sql = "SELECT $now_level_col,$next_level_col FROM ".$GLOBALS['ecs']->table('city_request')." WHERE city_id = '$city_id' ";
	}
	//echo $sql;
    $res = $GLOBALS['db']->getRow($sql);

	//print_r($res);
	$now_num  = $res[$now_level_col] - 1; //当前等级减1
	$next_num = $res[$next_level_col] + 1; //下一等级加1
	$prev_num = $res[$prev_level_col] + 1; //上一等级加1
	
	
	//更新city_request
	if($is_cancel){ //不通过操作		
		$sql_2 = "UPDATE " . $GLOBALS['ecs']->table('city_request') . 
				 " SET $now_level_col = '$now_num',$prev_level_col = '$prev_num'  WHERE city_id = '$city_id' ";
	}else{
		$sql_2 = "UPDATE " . $GLOBALS['ecs']->table('city_request') . 
				 " SET $now_level_col = '$now_num',$next_level_col = '$next_num'  WHERE city_id = '$city_id' ";
	}
	//echo $sql_2."<br>";
	$GLOBALS['db']->query($sql_2);
	
	// echo $now_level_col."-".$now_num."-".$next_level_col."-".$next_num."-"."<br>".$sql."<br>".$sql_2;
	
}

/**/
function getFull_ad_list($children,$market_level,$audit_status,$resource,$start_time,$end_time,$r_title,$has_new,$limit = 0){
	
	$where = ' WHERE '. $children ;
	
    if ($resource)
    {
        $where .= " AND c.resource = $resource ";
    }
    if ($has_new)
    {
		if($has_new == 1){
	        $where .= " AND ad.is_new = 1 ";
		}elseif($has_new == 3){
			$where .= " AND ad.is_new = 0 ";
		}else{
			$where .= "";
		}
    }
	if ($market_level)
    {
        $where .= " AND c.market_level LIKE '%" . mysql_like_quote($market_level) . "%'";
    }

    if ($audit_status)
    {
    
		switch($audit_status)
	    {
	        case 'leader未审核':
				$where .= " AND ad.audit_status = 4 AND ad.is_audit_confirm = 1 ";
	        	$where .= " AND au.audit_note LENGTH($audit_status) = 0 ";
	            break;
			case '审核通过':
				$where .= " AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 AND au.user_rank = 5 ";
        		$where .= " AND au.audit_note LIKE '" . mysql_like_quote($audit_status) . "'";
	            break;
			case '审核不通过':
				$where .= " AND ad.audit_status = 5 AND ad.is_audit_confirm = 0 AND au.user_rank = 5 ";
        		$where .= " AND au.audit_note NOT LIKE '审核通过'  AND  au.audit_note NOT LIKE '已被其他客户采购' ";
	            break;
			case '已被其他客户采购':
				$where .= " AND ad.audit_status = 5 AND ad.is_audit_confirm = 0 AND au.user_rank = 5 ";
        		$where .= " AND au.audit_note LIKE '" . mysql_like_quote($audit_status) . "'";
	            break;
	    }
	
    }

    if ($start_time && $end_time)
    {
        $where .= " AND au.time > '$start_time' AND au.time < '$end_time' ";
    }

	// $project_id = 2;
	// $where .= " AND au.feedback_audit = $project_id ";
	
	$sql = "SELECT a.*, ad.*, c.resource ".
			" FROM ".$GLOBALS['ecs']->table('city') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = a.ad_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS c ON c.cat_id = a.city_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad_audit') . " AS au ON au.ad_id = a.ad_id ". 
			
			"$where GROUP BY a.ad_id ORDER BY a.ad_id DESC ";
	 // echo $sql."<br>";	 //
	$col_42_array = array('0'=>"未指定",'1'=>"SMB",'2'=>"IDEA");
	$col_47_array = array('0'=>"未指定",'2'=>"使用推广费",'3'=>"使用营销折扣费",'4'=>"推广费&营销折扣费");
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{
		
		if($val['is_upload'] && $val['audit_status'])
		{
			$res[$key]['col_4'] = get_market_level($val['col_3']); //防治分区的人填写错误  从category库中取得
			// $res[$key]['quarter'] = get_quarter_audit_note($val['ad_id'],$project_id);
			$res[$key]['lv_2'] = get_audit_note($val['ad_id'],2);
			$res[$key]['lv_2'] = get_audit_note($val['ad_id'],2);
			$res[$key]['lv_3'] = get_audit_note($val['ad_id'],3);
			$res[$key]['lv_4'] = get_audit_note($val['ad_id'],4);
			$res[$key]['lv_5'] = get_audit_note($val['ad_id'],5);
			
			$res[$key]['start_date'] = intval(sep_days($val['col_16'],"01/01/1900") + 2);
			$res[$key]['end_date'] = intval(sep_days($val['col_17'],"01/01/1900") + 2);
			
			$res[$key]['col_42'] = $col_42_array[$val['col_42']];
			$res[$key]['col_47'] = $col_47_array[$val['col_47']];
			$res[$key]['resource_type'] = $r_title[$val['resource']];
			$res[$key]['last_audit_time'] = get_audit_time($val['ad_id'],5);

			if($val['is_new'] == 1){
				$tmp_arr = get_overlap_info(get_another_ad_id($val['city_id'],$val['ad_id']),$val['ad_id']);
				$res[$key]['fee_1'] = $tmp_arr['fee_1'];
				$res[$key]['fee_2'] = $tmp_arr['fee_2'];
				$res[$key]['fee_3'] = $tmp_arr['fee_3'];
				$res[$key]['fee_4'] = $tmp_arr['fee_4'];
				$res[$key]['fee_5'] = $tmp_arr['fee_5'];
				$res[$key]['fee_6'] = $tmp_arr['fee_6'];
			}else{
				$res[$key]['fee_1'] = $res[$key]['fee_2'] = $res[$key]['fee_3'] = $res[$key]['fee_4'] = $res[$key]['fee_5'] = "老牌子不计算费用";
			}

			
			//echo $res[$key]['ad_id']."-".$val['resource']."-".$res[$key]['resource_type']."<br>";
		}
	}
    return $res;
}

function get_quarter_audit_note($ad_id,$project_id){
	$sql = "SELECT audit_note FROM ".$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = '$ad_id' AND feedback_audit = $project_id ORDER BY record_id DESC limit 1";
	$res = $GLOBALS['db']->getOne($sql);
	return $res;
}

function get_audit_note($ad_id,$user_rank){
	$sql = "SELECT audit_note FROM ".$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = '$ad_id' AND user_rank = $user_rank ORDER BY record_id DESC";
	$res = $GLOBALS['db']->getAll($sql);
	$tt = array_shift($res); //返回第一个
	return $tt['audit_note'];
}



function excel_write_with_sub_array($_name,$title = array(),$data = array(),$sub_folder='city'){
	include_once(ROOT_PATH . 'includes/excelwriter.inc.php');
	
	$name = ROOT_PATH.'xls/'.$sub_folder.'/'.$_name.".xls";

	$excel=new ExcelWriter($name);

	if($excel==false)	
		echo $excel->error;

	$excel->GetHeader(); //头部 用来定义编码
	
	//写 标题title
	$excel->writeRow();
	foreach($title AS $v){
		if(is_array($v)){
			foreach($v AS $item){
				$excel->writeCol($item);
			}	
		}else{
			$excel->writeCol($v);
			//echo $v;
		}
	}
	
	/*按照 title 组织内容*/
	
	foreach($data AS $key => $val){
		$excel->writeRow();
		foreach($title AS $k => $v){
			if(is_array($v)){
				foreach($v AS $m => $n){
					$excel->writeCol($val[$k][$m]);
				}
			}else{
				$excel->writeCol($val[$k]);
			}
		}
	}
	
	/*
	$myArr=array("名字","姓氏","地址","年龄");
	$excel->writeLine($myArr);

	$myArr=array("Sriram","Pandit","23 mayur vihar",24);
	$excel->writeLine($myArr);

	$excel->writeRow();
	$excel->writeCol("Manoj");
	$excel->writeCol("Tiwari");
	$excel->writeCol("80 Preet Vihar");
	$excel->writeCol(24);

	$excel->writeRow();
	$excel->writeCol("Harish");
	$excel->writeCol("Chauhan");
	$excel->writeCol("115 Shyam Park Main");
	$excel->writeCol(22);

	$myArr=array("Tapan","Chauhan","1st Floor Vasundhra",25);
	$excel->writeLine($myArr);
	*/
	$excel->GetFooter(); //头部 用来定义编码

	$excel->close();
	return true;
}
/**********************************/
//
//
/**********************************/


/**/
function get_project_list($children){	
	$sql = "SELECT p.*  ".
			" FROM ".$GLOBALS['ecs']->table('project') . " AS p ".			
			"WHERE 1 ORDER BY p.project_id DESC ";
	//echo $sql;	 GROUP BY ad.ad_id
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val){
		$res[$key]['end_time'] 	= date( 'Y-m-d',(strtotime($val['start_time']) + $val['duration_time'] * 86400 ));
		$res[$key]['pic_count']	= $GLOBALS['db']->getOne("SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('project_picture')." WHERE project_id  =  $val[project_id]");
		$res[$key]['summary']	= get_project_summary($val['project_id'],$children);
		
		
	}
	return $res;
}

/**/
function get_new_project_list($children,$user_region,$based_new_nums){
	$sql = "SELECT p.*  ".
			" FROM ".$GLOBALS['ecs']->table('project') . " AS p ".			
			"WHERE 1 ORDER BY p.project_id DESC LIMIT 2";
	//echo $sql;	 GROUP BY ad.ad_id
	
	$res = $GLOBALS['db']->getAll($sql);
	
	$pic_count = 0;
	foreach($based_new_nums AS $v){
		$pic_count = $pic_count + intval($v);
	}
	
	
	foreach($res AS $key => $val){
		$res[$key]['end_time'] 	= date( 'Y-m-d',(strtotime($val['start_time']) + $val['duration_time'] * 86400 ));
		$res[$key]['pic_count']	= $pic_count;
		$res[$key]['summary']	= get_new_project_summary($val['project_id'],$user_region,$children,$based_new_nums);
		
		
	}
	return $res;
}

function get_new_project_summary($project_id,$user_region,$children,$based_new_nums){
	$res = array();
	$quarter = " AND a.Q".$project_id;
	
	$amount = 0;
	foreach($based_new_nums AS $v){
		$amount = $amount + intval($v);
	}
	
	$res['city_count'] = $user_region[0] == 1 ? $amount : $based_new_nums[$user_region[0]]; 
	
	$quarter_1 = " AND re.Q".$project_id;
	$sql_1 = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('city') ." AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') ." AS re ON re.city_id = a.city_id ". 
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') ." AS ad ON ad.city_id = a.city_id ". 
			 " WHERE $quarter_1 > 0  AND a.col_43 > 0 AND ad.is_new = 1";
	
	
	$sql_2 = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('city_ad') ." AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_gallery') ." AS g ON g.ad_id = a.ad_id ". 
			 	" WHERE $children AND g.feedback = $project_id AND a.is_new = 1 GROUP BY a.city_id ";

	$sql_3 = "SELECT count(au.ad_id) FROM ".$GLOBALS['ecs']->table('city_ad'). " AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad_audit') ." AS au ON au.ad_id = a.ad_id ". 
			 " WHERE $children AND a.is_new =1 AND au.feedback_audit = $project_id AND au.audit_note LIKE '审核通过' GROUP BY au.ad_id";
	
	// echo $sql_3;
	
	$res['write_complete'] = $project_id == 1 ? $GLOBALS['db']->getOne($sql_1) : 0 ;
	$tmp_1 = $GLOBALS['db']->getOne($sql_2);
	$res['upload'] = $tmp_1 > 0 ? $tmp_1 : 0 ;
	$tmp_2 = $GLOBALS['db']->getAll($sql_3);
	$res['confirm'] = count($tmp_2 );
	return $res;
}

function get_project_summary($project_id,$children){
	$res = array();
	$quarter = " AND a.Q".$project_id;
	
	$sql_0 = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('city_resource'). " AS a " .
				" WHERE $children $quarter > 0 ";
	
	$res['city_count'] = $GLOBALS['db']->getOne($sql_0);
	
	
	$quarter_1 = " AND re.Q".$project_id;
	$sql_1 = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('city') ." AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource') ." AS re ON re.city_id = a.city_id ". 
			 " WHERE $children $quarter_1 > 0  AND a.col_43 > 0";
	
	
	$sql_2 = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('city') ." AS a ".
			 " LEFT JOIN " .$GLOBALS['ecs']->table('city_gallery') ." AS g ON g.city_id = a.city_id ". 
			 " WHERE $children AND g.feedback = $project_id GROUP BY a.city_id ";
	
	$sql_3 = "SELECT au.ad_id FROM ".$GLOBALS['ecs']->table('city_ad'). " AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad_audit') ." AS au ON au.ad_id = a.ad_id ". 
			 " WHERE $children AND au.feedback_audit = $project_id AND au.audit_note LIKE '审核通过' GROUP BY au.ad_id";
	
	//echo $sql_2;
	
	$res['write_complete'] = $project_id == 1 ? $GLOBALS['db']->getOne($sql_1) : 0 ;
	$res['upload'] = $GLOBALS['db']->getAll($sql_2);
	$res['upload'] = count($res['upload']);
	$tmp = $GLOBALS['db']->getAll($sql_3);
	$res['confirm'] = count($tmp);
	return $res;
}

function get_project_info($project_id){
	if($project_id){
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('project') .	" WHERE project_id =  $project_id ";
		$res =  $GLOBALS['db']->getRow($sql);
		return $res;
	}else{
		return false;
	}
}

/**/
function get_project_city($children,$limit = 0){

    $filter['county_name'] = empty($_REQUEST['county_name']) ? '' : trim($_REQUEST['county_name']);
    $filter['city_name'] = empty($_REQUEST['city_name']) ? '' : trim($_REQUEST['city_name']);
    $filter['region_name'] = empty($_REQUEST['region_name']) ? '' : trim($_REQUEST['region_name']);
    $filter['project_id'] = empty($_REQUEST['project_id']) ? 0 : $_REQUEST['project_id'];
    $filter['resource'] = empty($_REQUEST['resource']) ? 0 : $_REQUEST['resource'];
    $filter['market_level'] = empty($_REQUEST['market_level']) ? '' : trim($_REQUEST['market_level']);
    $filter['audit_status'] = empty($_REQUEST['audit_status']) ? '' : trim($_REQUEST['audit_status']);
    $filter['has_new'] = empty($_REQUEST['has_new']) ? '' : trim($_REQUEST['has_new']);

	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'inv_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
	$quarter = "Q".$filter['project_id'];
	$can_modify_quarter = "can_modify_q".$filter['project_id'];
	$update_time_quarter = "update_time_q".$filter['project_id'];
	
	$where = ' WHERE '. $children ." AND ad.is_audit_confirm = 1 AND ad.audit_status = 5 AND re.sys_level = 5 ";
	$where .= " AND re.$quarter > 0 ";
	//$where.= $_SESSION['user_rank'] > 1 ? " AND re.req_id > 0 " : "" ;
	// 最终通过的权限要求 ID
    if ($filter['county_name'])
    {
        $where .= " AND a.cat_name LIKE '%" . mysql_like_quote($filter['county_name']) . "%'";
    }
    if ($filter['resource'])
    {
        $where .= " AND re.$quarter = $filter[resource] ";
    }
	if ($filter['city_name'])
    {
        $where .= " AND a1.cat_name LIKE '%" . mysql_like_quote($filter['city_name']) . "%'";
    }
    if ($filter['region_name'])
    {
        $where .= " AND a3.cat_name LIKE '%" . mysql_like_quote($filter['region_name']) . "%'";
    }
    if ($filter['audit_status'] == "audit_confirm")
    {
        $where .= " AND au.audit_note LIKE '审核通过'  ";
    }
    if ($filter['audit_status'] == "audit_cancel")
    {
        $where .= " AND au.audit_note is NOT NULL AND au.audit_note NOT LIKE '审核通过'  ";
    }
    if ($filter['audit_status'] == "audit_idle")
    {
        $where .= " AND au.audit_note is NULL  ";
    }
	if ($filter['market_level'])
    {
        $where .= " AND a.market_level LIKE '%" . mysql_like_quote($filter['market_level']) . "%'";
    }


    if ($filter['has_new'])
    {
		if($filter['has_new'] == 1){
	        $where .= " AND ad.is_new = 1 AND a.has_new = 1 ";
		}elseif($filter['has_new'] == 3){
			$where .= " AND a.has_new = 0 ";
		}else{
			$where .= "";
		}
    }



	//  最大值列表 db_create_in
	$max_record_array = $GLOBALS['db']->getCol("SELECT MAX(record_id) FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " WHERE feedback_audit = $filter[project_id]  GROUP BY ad_id ");
	$sql_max = "SELECT ad_id,audit_note FROM " . $GLOBALS['ecs']->table('city_ad_audit') .
            " WHERE record_id " . db_create_in($max_record_array);
	//echo $sql_max;
	
	

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
	
	
	/* 记录总数 */
    if ($filter['city_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').  " AS re ON re.city_id = a.cat_id ".
                $where;
    }
    elseif ($filter['region_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').  " AS re ON re.city_id = a.cat_id ".
				" LEFT JOIN (" . $sql_max . ') AS au ON au.ad_id = ad.ad_id '.
                $where;
    }
	elseif ($filter['audit_status'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').  " AS re ON re.city_id = a.cat_id ".
				" LEFT JOIN (" . $sql_max . ') AS au ON au.ad_id = ad.ad_id '.
                $where;
    }

    else
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') ." AS a " . 
					" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
					" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').   " AS re ON re.city_id = a.cat_id ".					
					" $where " ;
		
    }
	
    $filter['record_count']   = $GLOBALS['db']->getOne($count_sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	

	$request_title = "re.lv_".$_SESSION['user_rank'];
	$limit_sql = $limit > 0 ? " LIMIT 0,$limit ": " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
	$order_sql = $_SESSION['user_rank'] == 1 ? " ORDER BY city.$update_time_quarter DESC " : " ORDER BY city.$update_time_quarter DESC " ;
	
	$sql = "SELECT a.cat_name AS county, a.market_level, a.cat_id ,a.is_upload, a.audit_status, a.is_audit_confirm, a.is_microsoft, a.has_new, ". //
			"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region , ad.ad_id, ".
			//" pr.req_id, pr.price, pr.price_amount, pr.request_price, pr.request_price_amount,  (ad.price_status - $_SESSION[user_rank]) AS t1 ".
			" city.col_19,city.col_20  ,city.$can_modify_quarter AS can_modify, re.resource, re.$quarter AS nowQ , au.audit_note ".
			" FROM ".$GLOBALS['ecs']->table('category') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ". 
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city').   " AS city ON city.ad_id = ad.ad_id ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').  " AS re ON re.city_id = a.cat_id ".
			
			//" LEFT JOIN " .$GLOBALS['ecs']->table('project_request').   " AS pr ON pr.city_id = a.cat_id  AND pr.ad_id  = ad.ad_id ".
			//" LEFT JOIN " .$GLOBALS['ecs']->table('city'). 		' AS c  ON c.city_id = a.cat_id '.
			" LEFT JOIN (" . $sql_max . ') AS au ON au.ad_id = ad.ad_id '.
			"$where "." GROUP BY a.cat_id  "." $order_sql ".
			$limit_sql;
	//echo $sql;	 //GROUP BY ad.ad_id 
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{

		$res[$key]['pic_view'] = get_pic_view($val['nowQ'],$filter['project_id']);		
		//$res[$key]['audit_note'] =  $GLOBALS['db']->getOne("SELECT audit_note FROM ".$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = $val[ad_id] AND feedback_audit = $filter[project_id] ORDER BY record_id DESC LIMIT 1");
		$res[$key]['upload_picture'] =  $GLOBALS['db']->getOne("SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('city_gallery')." WHERE ad_id = $val[ad_id] AND feedback = $filter[project_id]");
		
		
		
	}
	$arr = array('citys' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql' => $sql,'count_sql' => $count_sql, 'page_size' => $filter['page_size']);
    return $arr;
}

function get_pic_view($type,$project_id){

    $sql = "SELECT picture_id FROM ".$GLOBALS['ecs']->table('project_picture')." WHERE pic_type = $type AND project_id = $project_id ";
    $res = $GLOBALS['db']->getOne($sql);
	if($res){
		return "city_project.php?act=view_picture&picture_id=".$res;
	}else{
		return 0;
	}
}
/*
function act_select_request_city($project_id,$ad_id,$is_add){
	$final_ad =  get_final_ad($ad_id);
	if(count($final_ad)){
		if($is_add){
			$request_info['project_id'] = $project_id;
			$request_info['user_id'] = $_SESSION['user_id'];
			$request_info['price'] = $final_ad['col_30'] ; //['col_30'] = "每平米制作费"
			$request_info['price_amount'] = $final_ad['col_32'] ; //['col_30'] = "每平米制作费"
			$request_info['city_id'] = $final_ad['city_id'] ;
			$request_info['ad_id'] = $final_ad['ad_id'];
			
			$request_info['request_price'] = ""; 	//以后需要填写 第一次不用填写
			$request_info['request_price_amount'] = "";		//以后需要填写 第一次不用填写
			$request_info['request_note'] = "";			//前两项有变化则填写
			
			
			//$final_ad['col_31'] ; //['col_31'] = "安装费"
			//$final_ad['col_32'] ; //['col_32'] = "制作费合计"
		
			
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('project_request'), $request_info, 'INSERT');
			
		}else{
			$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('project_request') . " WHERE city_id = $final_ad[city_id] AND ad_id = $final_ad[ad_id]");
			$sql_2 = "UPDATE " . $GLOBALS['ecs']->table('city_ad') . 
					 " SET is_price_change = 0,price_status = 0,is_price_confirm = 0 WHERE ad_id = '$final_ad[ad_id]' ";
			$GLOBALS['db']->query($sql_2);
			
			
		}
		
	}
	
	
}
*/
function get_final_ad($ad_id){
	$sql = "SELECT ad.*,c.* FROM ".$GLOBALS['ecs']->table('city_ad') . " AS ad ".
	 		" LEFT JOIN " .$GLOBALS['ecs']->table('city') . " AS c ON c.ad_id = ad.ad_id ". 
			" WHERE ad.ad_id =  $ad_id AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 ";
	$res =  $GLOBALS['db']->getRow($sql);
	return $res;
}

/*

function get_req_info($project_id,$ad_id){
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('project_request') .
			" WHERE ad_id =  $ad_id AND project_id = $project_id ";
	$res =  $GLOBALS['db']->getRow($sql);
	return $res;
}
function get_price_info($ad_id = 0)
{
	if($ad_id){
		$price_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('project_request') . " WHERE ad_id = $ad_id");
		return $price_info;
	}
}

*/
/**/
function get_picture_list($project_id){
	$ext_sql = $project_id ? " pic.project_id = $project_id " : " 1 " ;
	
	$sql = "SELECT p.*, pic.* ".
			" FROM ".$GLOBALS['ecs']->table('project_picture') . " AS pic ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('project') . " AS p ON p.project_id = pic.project_id ". 
			"WHERE $ext_sql ORDER BY pic.upload_time DESC ";
	//echo $sql;//	 GROUP BY ad.ad_id
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val){
		$res[$key]['upload_time'] = date('Y-m-d',$val['upload_time']);
		
	}
	return $res;
}

function get_picture_info($picture_id){
	if($picture_id){
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('project_picture') .	" WHERE picture_id =  $picture_id ";
		$res =  $GLOBALS['db']->getRow($sql);
		return $res;
	}else{
		return false;
	}
}

//获得城市的广告牌通过数量
function get_city_confirm_ad_num($city_id){
	$sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('city_ad') .	" WHERE audit_status =  5 AND is_audit_confirm = 1 AND city_id = $city_id ";
	$res =  $GLOBALS['db']->getOne($sql);
	return $res;
}
//获得城市的大区名字
function get_base_info($city_id){
	$sql = "SELECT a.cat_name AS city_name,a1.cat_name AS county_name, a2.cat_name AS province_name,a3.cat_name AS region_name,a.has_new  FROM " . 
			$GLOBALS['ecs']->table('category') . " AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
            " WHERE a.cat_id = $city_id limit 1 ";
	$base_info =  $GLOBALS['db']->getRow($sql); 
	return $base_info;
}
/**
 * 为了支持报表导出所做的 获取select 数据 
 */
function get_region_array(){
	$sql = "SELECT cat_id,cat_name FROM ".$GLOBALS['ecs']->table('category') .	" WHERE parent_id <= 1 ";
	$res =  $GLOBALS['db']->getAll($sql);
	$array = array();
	foreach($res AS $key => $val){
		$array[$val['cat_id']] = $val['cat_name'];
	}
	return $array;
}

function get_market_level_array(){
	$sql = "SELECT market_level FROM ".$GLOBALS['ecs']->table('category') .	" WHERE LENGTH(market_level) > 0 GROUP BY market_level ORDER BY market_level";
	$res =  $GLOBALS['db']->getCol($sql);
	$data = array();
	foreach($res AS $val){
		$data[$val] = $val;
	}
	return $data;
}

function get_audit_status_array(){
	$res = array(
		"未审核"=>"未审核",
		"审核通过"=>"审核通过",
		"审核不通过"=>"审核不通过",
		"已被其他客户采购"=>"已被其他客户采购");
	return $res;
}

function get_audit_status($ad_id,$project_id){
	$sql = "SELECT audit_note FROM ".$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = $ad_id AND feedback_audit = $project_id ORDER BY record_id DESC LIMIT 1";
	$audit_note = trim($GLOBALS['db']->getOne($sql));
	//echo $sql.$audit_note;
	if(empty($audit_note)){
		$audit_status = "audit_idle";
	}else{
		if($audit_note == "审核通过"){
			$audit_status = "audit_confirm";
		}else{
			$audit_status = "audit_cancel";
		}
	}
	return $audit_status;
}



function act_level_4_city_upload($city_id,$ad_id){
	$sql_1 = "UPDATE " . $GLOBALS['ecs']->table('city_ad') . " SET audit_status = '5', is_audit_confirm = '1' WHERE ad_id = '$ad_id' ";
	//echo $sql_1;
	$GLOBALS['db']->query($sql_1);
	
	$sql_2 = "UPDATE " . $GLOBALS['ecs']->table('city_request') . " SET lv_0 = '0',lv_1 = '0',lv_2 = '0',lv_3 = '0',lv_4 = '0',lv_5 = '0',lv_6 = '1' WHERE city_id = '$city_id'";
	//echo $sql_2;
	$GLOBALS['db']->query($sql_2);
	
	$audit = array();
	$audit['ad_id'] = $ad_id;
	$audit['user_id'] = 63;
	$audit['user_rank'] = 5;
	$audit['audit_note'] = "审核通过";			
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad_audit'), $audit, 'INSERT');
}

function is_ie() {
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if ((strpos($useragent, 'opera') !== false) ||
        (strpos($useragent, 'konqueror') !== false)) return false;
    if (strpos($useragent, 'msie ') !== false) return true;
     return false;
}

function pic_download($attachment) {
    //$realpath = ABSPATH . UPLOAD_DIR . '/' . $attachment['path'] . '/' . $attachment['savename'];
    $realpath =  "http://www.lenovo-one.com/".$attachment['path'];
    $content_len = sprintf("%u", filesize($realpath));
    if (is_ie()) {
        // leave $filename alone so it can be accessed via the hook below as expected.
        $filename = rawurlencode($attachment['filename']);
    }
    else {
        $filename = &$attachment['filename'];
    }

    while(ob_get_length() !== false) @ob_end_clean(); 
    header('Pragma: public');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: pre-check=0, post-check=0, max-age=0');
    header('Content-Transfer-Encoding: binary'); 
    header('Content-Encoding: none');
    header('Content-type: ' . $attachment['type']);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    //header("Content-length: $content_len");
    echo file_get_contents($realpath);
    exit();
}
/* for base info*/
/**/
function get_base_info_list($children,$limit = 0){

    $filter['county_name'] = empty($_REQUEST['county_name']) ? '' : trim($_REQUEST['county_name']);
    $filter['city_name'] = empty($_REQUEST['city_name']) ? '' : trim($_REQUEST['city_name']);
    $filter['region_name'] = empty($_REQUEST['region_name']) ? '' : trim($_REQUEST['region_name']);
    $filter['audit_status'] = empty($_REQUEST['audit_status']) ? 0 : $_REQUEST['audit_status'];
    $filter['market_level'] = empty($_REQUEST['market_level']) ? '' : trim($_REQUEST['market_level']);
    $filter['project_id'] = empty($_REQUEST['project_id']) ? 0 : $_REQUEST['project_id'];
    $filter['audit_status'] = empty($_REQUEST['audit_status']) ? '' : trim($_REQUEST['audit_status']);
    $filter['has_new'] = empty($_REQUEST['has_new']) ? '' : intval($_REQUEST['has_new']);

	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'inv_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	
	$where = ' WHERE '. $children . " AND ad.is_audit_confirm = 1 AND ad.audit_status = 5 AND re.sys_level = 5 ";
	//$where.= $_SESSION['user_rank'] > 1 ? " AND re.req_id > 0 " : "" ;
	// 最终通过的权限要求 ID
    if ($filter['county_name'])
    {
        $where .= " AND a.cat_name LIKE '%" . mysql_like_quote($filter['county_name']) . "%'";
    }
    if ($filter['resource'])
    {
        $where .= " AND re.$quarter = $filter[resource] ";
    }
	if ($filter['city_name'])
    {
        $where .= " AND a1.cat_name LIKE '%" . mysql_like_quote($filter['city_name']) . "%'";
    }
    if ($filter['region_name'])
    {
        $where .= " AND a3.cat_name LIKE '%" . mysql_like_quote($filter['region_name']) . "%'";
    }
    if ($filter['audit_status'] == "audit_confirm")
    {
        $where .= " AND au.audit_note LIKE '审核通过'  ";
    }
    if ($filter['audit_status'] == "audit_cancel")
    {
        $where .= " AND au.audit_note is NOT NULL AND au.audit_note NOT LIKE '审核通过'  ";
    }
    if ($filter['audit_status'] == "audit_idle")
    {
        $where .= " AND au.audit_note is NULL  ";
    }
	if ($filter['market_level'])
    {
        $where .= " AND a.market_level LIKE '%" . mysql_like_quote($filter['market_level']) . "%'";
    }
    if ($filter['has_new'])
    {
		if($filter['has_new'] == 1){
	        $where .= " AND a.has_new = 1 AND ad.is_new = 1";
		}elseif($filter['has_new'] == 3){
			$where .= " AND a.has_new = 0 ";
		}else{
			$where .= "";
		}
    }


	//  最大值列表 db_create_in
	$max_record_array = $GLOBALS['db']->getCol("SELECT MAX(record_id) FROM ".$GLOBALS['ecs']->table('city_ad_audit'). " WHERE feedback_audit = $filter[project_id]  GROUP BY ad_id ");
	$sql_max = "SELECT ad_id,audit_note FROM " . $GLOBALS['ecs']->table('city_ad_audit') .
            " WHERE record_id " . db_create_in($max_record_array);
	//echo $sql_max;



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
	
	
	/* 记录总数 */
    if ($filter['city_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').  " AS re ON re.city_id = a.cat_id ".
                $where;
    }
    elseif ($filter['region_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').  " AS re ON re.city_id = a.cat_id ".
				" LEFT JOIN (" . $sql_max . ') AS au ON au.ad_id = ad.ad_id '.
				
                $where;
    }
	elseif ($filter['audit_status'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').  " AS re ON re.city_id = a.cat_id ".
				" LEFT JOIN (" . $sql_max . ') AS au ON au.ad_id = ad.ad_id '.
                $where;
    }
    else
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') ." AS a " . 
					" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
					" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').   " AS re ON re.city_id = a.cat_id ".					
					" $where " ;
		
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($count_sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$request_title = "re.lv_".$_SESSION['user_rank'];
	$limit_sql = $limit > 0 ? " LIMIT 0,$limit ": " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
	$order_sql = $_SESSION['user_rank'] == 1 ? " ORDER BY ad.city_id DESC " : " ORDER BY ad.city_id DESC " ;
	
	$sql = "SELECT a.cat_name AS county, a.market_level, a.cat_id ,a.is_upload, a.audit_status, a.is_audit_confirm, a.is_microsoft,a.has_new, ". //
			"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region , ad.ad_id, ad.base_info_changed , ad.base_info_modify,au.audit_note ".
			//" pr.req_id, pr.price, pr.price_amount, pr.request_price, pr.request_price_amount,  (ad.price_status - $_SESSION[user_rank]) AS t1 ".
			" FROM ".$GLOBALS['ecs']->table('category') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ". 
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_resource').  " AS re ON re.city_id = a.cat_id ".
			
			" LEFT JOIN (" . $sql_max . ') AS au ON au.ad_id = ad.ad_id '.
			"$where "." GROUP BY a.cat_id  "." $order_sql ".
			$limit_sql;

	//echo $sql;	 //GROUP BY ad.ad_id 
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{
		//$res[$key]['audit_note'] =  $GLOBALS['db']->getOne("SELECT audit_note FROM ".$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = $val[ad_id] AND feedback_audit = $filter[project_id] ORDER BY record_id DESC LIMIT 1");	
		$res[$key]['send_time'] =  $GLOBALS['db']->getAll("SELECT time FROM ".$GLOBALS['ecs']->table('city_material')." WHERE ad_id = $val[ad_id] AND is_send > 0 ORDER BY time ASC");	
		$res[$key]['receive_time'] =  $GLOBALS['db']->getAll("SELECT time FROM ".$GLOBALS['ecs']->table('city_material')." WHERE ad_id = $val[ad_id] AND is_receive > 0  ORDER BY time ASC");	
	
	}
	$arr = array('citys' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql' => $sql,'count_sql' => $count_sql, 'page_size' => $filter['page_size']);
    return $arr;
}

/* 生成城市编号*/
function make_ad_sn($ad_id, $city_id){
	
	if($ad_id > 0  && $city_id > 0){
		
		$has_new  = 0;
		$sql = "SELECT count(*)  FROM " . $GLOBALS['ecs']->table('city_ad') . 
		    " WHERE city_id = $city_id AND audit_status = 5 AND is_upload = 1 AND is_audit_confirm = 1 ";
	    $passed_count =  $GLOBALS['db']->getOne($sql);

		// 已经有一块牌子通过 或者 新增的城市[ID>2779] 那么都算新城市
		if($passed_count >= 1 || $city_id > NEWCITYAFTERID){
			$has_new = 1;

			//更新分类信息 for 如果是新城市那么就更新 caegory
			$sql = "UPDATE " . $GLOBALS['ecs']->table('category') . " SET has_new = '1'  WHERE cat_id = '$city_id'";
	       	$GLOBALS['db']->query($sql);

			$sql = "UPDATE " . $GLOBALS['ecs']->table('city_ad') . " SET is_new = '1'  WHERE ad_id = '$ad_id'";
	       	$GLOBALS['db']->query($sql);

		}


		$add_word = $has_new == 1 ? "_XZ_" : "_ZC_" ;
		
		
		$region_array = array();
		$level_array = array();
		$region_array[2] = "SD";
		$region_array[3] = "JS";
		$region_array[4] = "ZJ";
		$region_array[5] = "JJ";
		$region_array[6] = "HJ";
		$region_array[7] = "LN";
		$region_array[8] = "JM";
		$region_array[9] = "FJ";
		$region_array[10] = "JX";
		$region_array[11] = "AH";
		$region_array[12] = "HN";
		$region_array[13] = "HB";
		$region_array[14] = "HU";
		$region_array[15] = "GD";
		$region_array[16] = "SZ";
		$region_array[17] = "GX";
		$region_array[18] = "SC";
		$region_array[19] = "YG";
		$region_array[20] = "CY";
		$region_array[21] = "SX";
		$region_array[22] = "GQ";
		$region_array[23] = "XJ";


		$level_array['百强镇'] = "0";
		$level_array['4'] 	= "4";
		$level_array['5'] 	= "5";
		$level_array['6A'] 	= "7";
		$level_array['6B'] 	= "8";
		$level_array['6C'] 	= "9";

		$year_short = substr(date("Y") ,2,2) ;

		$region_info = get_region_info($city_id);
		$region_id = $region_info['region_id'];
		$region_sname = $region_array[$region_id];

		$market_level_tmp = get_market_level("",$city_id);
		$market_level = $level_array[$market_level_tmp];

		$ad_number = substr(strval($ad_id+10000),1,4); 
		
		if(isset($region_sname) && isset($market_level) && isset($ad_number) ){
			$ad_sn = "FY".$year_short.$add_word.$region_sname."_".$market_level."_".$ad_number;
			$sql = "UPDATE " . $GLOBALS['ecs']->table('city') . " SET ad_sn = '$ad_sn'  WHERE ad_id = $ad_id ";
	    	return $ad_sn;
		}else{
			$ad_sn = "FY".$year_short.$add_word.$region_sname."_".$market_level."_".$ad_number;
			echo $city_id.",".$ad_sn."<br>";
			return 0;
		}
		
	}else{
		return 0;
	}
	
}

function get_region_info($city_id){
	$sql = "SELECT a3.cat_name AS region_name, a3.cat_id AS region_id FROM " . 
			$GLOBALS['ecs']->table('category') . " AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
            " WHERE a.cat_id = $city_id limit 1 ";
	$base_info =  $GLOBALS['db']->getRow($sql); 
	return $base_info;
}

//获得通过的牌子Id
function get_passed_ad_id($city_id){
	$sql = "SELECT ad_id  FROM " . $GLOBALS['ecs']->table('city_ad') . 
	    " WHERE city_id = $city_id AND audit_status = 5 AND is_upload = 1 AND is_audit_confirm = 1 ORDER BY ad_id ASC limit 1 ";
    $res =  $GLOBALS['db']->getOne($sql); 
	// echo $sql;
	return $res;
}


// 07/01/2011 -> 2011-07-01
function sep_days($end_date,$start_date)
{
 	$temp = strtotime(date( 'Y-m-d ',strtotime($end_date)))-strtotime(date( 'Y-m-d ',strtotime($start_date)));
 	$days = $temp/(60*60*24);
 	return $days+1;
}

function get_overlap_info($another_ad_id,$ad_id){
	$res = array();	
	$ad_info = get_city_info($ad_id);
	
	if($another_ad_id){
		$old_ad_info = get_city_info($another_ad_id);
		$res['fee_1'] = sep_days($old_ad_info['col_17'],$ad_info['col_16']);
		
	}else{
		$res['fee_1'] = 0; //重叠发布天数
		
	}

	
	$res['fee_2'] = intval($ad_info['col_19'] / $ad_info['col_18'] *  $res['fee_1']);
	$tt = $ad_info['col_19'] - $res['fee_2'];
	$res['fee_3'] = intval($tt * 0.50 );
	$res['fee_4'] = intval($tt * 0.15 );
	$res['fee_5'] = intval($tt * 0.65 );
	$res['fee_6'] = $ad_info['col_20'] + $res['fee_2']  + $res['fee_5'];	
	return $res;
}


function get_another_ad_id($city_id,$ad_id){
	if($city_id && $ad_id){
		$sql = 	" SELECT ad_id FROM " . $GLOBALS['ecs']->table('city_ad') . 
		" WHERE city_id = $city_id AND audit_status = 5 AND is_upload = 1 AND is_audit_confirm = 1 ";
		$tmp = $GLOBALS['db']->getCol($sql);
		foreach($tmp AS $v){
			if($v != $ad_id ){
				return $v;
			}
		}
	}	
}

function insert_ad_log($ad_id,$name,$old_value,$value){
	$log = array();
	$log['ad_id'] 	= $ad_id;
	$log['user_id'] = $_SESSION['user_id'];
	$log['col_name']= $name;
	$log['value'] 	= $value;
	$log['old_value'] = $old_value;
	$log['time'] 	= gmtime();
	//print_r($log);
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('city_ad_log'), $log, 'INSERT');	
}



?>