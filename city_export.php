<?php
/**
 * lenovo-one 资料分类 * $Author: mactive $
 * 此文件用来执行一些批量操作
 * $Id: city_sql.php 14481 2011-05-18 11:23:01Z mactive $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_city.php');
require(dirname(__FILE__) . '/includes/lib_clips.php');



/*更新分区上传错误的数据*/
if($_REQUEST['act'] == 'fankui_pic')
{
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 1;
	
	$sql = "SELECT ad_id FROM ".$GLOBALS['ecs']->table($GLOBALS['year']."_".'city_gallery')." WHERE feedback  = $project_id GROUP BY ad_id LIMIT 0,50";
	echo $sql."<br>";
	
	$res = $GLOBALS['db']->getCol($sql);
	
	

	foreach($res AS $val){
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table($GLOBALS['year']."_".'city_gallery')." WHERE feedback  = $project_id AND ad_id = $val ORDER BY img_id DESC";
		echo $sql."<br>";
		$row = $GLOBALS['db']->getAll($sql);
		
		echo "<div>";
		echo $val;
		foreach($row AS $v){
//			echo '<img src="'.$v['thumb_url'].'" width="100" height="75" /> ';
			echo '<a href="city_export.php?act=rename&ad_id='.$val.'&img_sort='.$v['img_sort'].'&path='.$v['img_url'].'">'.
			$v['img_id']."_".$v['img_sort'].'<a/> &nbsp;';
		}
		echo "</div>";
		
	}	
}
elseif($_REQUEST['act'] == 'rename')
{
	$ad_id =  !empty($_REQUEST['ad_id']) ? intval($_REQUEST['ad_id']) : '';
	$img_sort =  !empty($_REQUEST['img_sort']) ? intval($_REQUEST['img_sort']) : 0;
	$path =  !empty($_REQUEST['path']) ? trim($_REQUEST['path']) : '';
	//echo $path;
	$img_sort = $img_sort + 1;
	$ad_info = get_ad_info($ad_id);
	$city_name  = $ad_info['city_name'];
	
	$info = array();
	$info['path'] = $path;
	$info['filename'] = $city_name."_".$img_sort.".jpg";
	
	pic_download($info);

}
elseif($_REQUEST['act'] == 'transform_page')
{
	$data = array();
	for($i=2;$i<24;$i++){
		$tt = array();
		$tt['id'] = $i; 
		$tt['name'] = get_cat_name($i);
		array_push($data,$tt);
	}
	$smarty->assign('data',    $data);	
	
	$smarty->display('city_export.dwt');
	
}
elseif($_REQUEST['act'] == 'transform')
{
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 2;
	$quarter = "Q".$project_id;
	
	$wanted_market_level =  !empty($_REQUEST['market_level']) ? trim($_REQUEST['market_level']) : 0;
	$wanted_region = !empty($_REQUEST['wanted_region']) ? intval($_REQUEST['wanted_region']) : 2; //2-23
	//$wanted_level = 4; // 4 5 6
	// 	4级城市 / 各大分区 / 城市名称  
	//	所有都是包含画面的
	//	8月中旬要数据
	
	$sql = "SELECT g.ad_id FROM ".$GLOBALS['ecs']->table($GLOBALS['year']."_".'city_gallery'). " AS g " .
		" LEFT JOIN " .$GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad_audit') . " AS au ON au.ad_id = g.ad_id ". 
		" LEFT JOIN " .$GLOBALS['ecs']->table($GLOBALS['year']."_".'city_ad') . " AS ad ON ad.ad_id = g.ad_id ". 
		" LEFT JOIN " .$GLOBALS['ecs']->table($GLOBALS['year']."_".'city_resource') . " AS re ON re.city_id = ad.city_id ". 
			" WHERE g.feedback  = $project_id "." AND re.$quarter = 6 ".
			" AND au.audit_note LIKE '审核通过' AND au.feedback_audit = $project_id  GROUP BY g.ad_id "; //LIMIT 0,50
	
	echo $sql."<br>";
	
	$res = $GLOBALS['db']->getCol($sql);
	$count = 0 ;
	$wanted_region_name = get_cat_name($wanted_region);
//	$root_folder = "export/level_".$wanted_level;
	$root_folder = "export/".$wanted_region_name;
	
	
	if (!file_exists($root_folder))
	{
	    @mkdir($root_folder, 0777);
	    @chmod($root_folder, 0777);
	}


	foreach($res AS $val){
		$city_id = get_city_id($val);
		
		$base_info 	= get_region_info($city_id);
		$region_name= $base_info['region_name'];
		$region_id 	= $base_info['region_id'];
		$market_level = get_market_level("",$city_id);
		
		if($region_id == $wanted_region && ($market_level == "6A" || $market_level == "6B" || $market_level == "6C" || $market_level == "百强镇" )){
			
			//$market_level = get_market_level("",$city_id);
		
			$base_info = get_base_info($city_id);

			$city_name	= $base_info['city_name'];
			//$region_name = $base_info['region_name'];
			$level_folder = $root_folder."/level_".$market_level;
			$city_folder = $level_folder."/".$city_name;
			//echo $city_name. $region_name ;
			
			if (!file_exists($level_folder))
			{
			    //@mkdir(iconv("UTF-8","GBK",$level_folder), 0777);
			    //@chmod(iconv("UTF-8","GBK",$level_folder), 0777);
			    @mkdir(rawurldecode($level_folder), 0777);
			    @chmod(rawurldecode($level_folder), 0777);
			}

			if (!file_exists($city_folder))
			{
			    @mkdir(rawurldecode($city_folder), 0777);
			    @chmod(rawurldecode($city_folder), 0777);
			
			//	@mkdir(iconv("UTF-8","GBK",$city_folder), 0777);
		    //	@chmod(iconv("UTF-8","GBK",$city_folder), 0777);
		    
			
				$pic_list = get_pic_list($val,$city_name,$project_id);
				foreach($pic_list AS $row){
					@copy($row['img_url'],   $city_folder."/".$row['img_name']);
				}
			}
			
			$count += 1;
			
		}

	}
	
	
	echo "all $wanted_region region ".$count;	
}

function get_market_level_by_ad_id($ad_id)
{
	$city_id = get_city_id($ad_id);
	$market_level = get_market_level("",$city_id);
	if($market_level == "6A" || $market_level == "6B"  || $market_level == "6C"){
		$market_level = 6; 
	}
	return $market_level;
}


function get_cat_name($cat_id){
	$sql = "SELECT cat_name FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'category') . 
            " WHERE cat_id = $cat_id ";
	$res =  $GLOBALS['db']->getOne($sql); 
	return $res;
}


function get_pic_list($ad_id,$city_name,$project_id){
	$sql = "SELECT img_id,img_url  FROM " . $GLOBALS['ecs']->table($GLOBALS['year']."_".'city_gallery') .
            " WHERE ad_id = $ad_id AND feedback  = $project_id limit 4 ";
	$res =  $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $row){
		$res[$key]['img_url'] = $_SERVER['DOCUMENT_ROOT']."/".$row['img_url'];
		$res[$key]['img_name'] = $city_name."_$key".".jpg";
	}
	//print_r($res);
	return $res;
}
?>