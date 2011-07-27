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
	
	$sql = "SELECT ad_id FROM ".$GLOBALS['ecs']->table('city_gallery')." WHERE feedback  = $project_id GROUP BY ad_id LIMIT 0,50";
	echo $sql."<br>";
	
	$res = $GLOBALS['db']->getCol($sql);
	
	

	foreach($res AS $val){
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('city_gallery')." WHERE feedback  = $project_id AND ad_id = $val ORDER BY img_id DESC";
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
elseif($_REQUEST['act'] == 'transform')
{
	$project_id =  !empty($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 1;
	$market_level =  !empty($_REQUEST['market_level']) ? intval($_REQUEST['market_level']) : 0;
	$wanted_level = 4; // 4 5 6
	// 	4级城市 / 各大分区 / 城市名称  
	//	所有都是包含画面的
	//	8月中旬要数据
	
	$sql = "SELECT ad_id FROM ".$GLOBALS['ecs']->table('city_gallery')." WHERE feedback  = $project_id GROUP BY ad_id "; //LIMIT 0,50
	
	echo $sql."<br>";
	
	$res = $GLOBALS['db']->getCol($sql);
	$count = 0 ;
	$root_folder = "export/level_".$wanted_level;
	if (!file_exists($root_folder))
	{
	    @mkdir($root_folder, 0777);
	    @chmod($root_folder, 0777);
	}

	foreach($res AS $val){
		$city_id = get_city_id($val);
		$market_level = get_market_level_by_ad_id($val);
			
		if($market_level == $wanted_level){

			$base_info = get_base_info($city_id);

			$city_name	= $base_info['city_name'];
			$region_name = $base_info['region_name'];
			$region_folder = $root_folder."/".$region_name;
			$city_folder = $region_folder."/".$city_name;
			//echo $city_name. $region_name ;

			if (!file_exists($region_folder))
			{
			    @mkdir($region_folder, 0777);
			    @chmod($region_folder, 0777);
			}

			if (!file_exists($city_folder))
			{
			    @mkdir($city_folder, 0777);
			    @chmod($city_folder, 0777);
			}
			
			$count += 1;
			
		}
		/*
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('city_gallery')." WHERE feedback  = $project_id AND ad_id = $val ORDER BY img_id DESC";
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
		*/
	}
	echo "all 4 level ".$count;	
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



?>