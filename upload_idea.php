<?php

/**
 * SINEMALL 提交用户评论 * $Author: testyang $
 * $Id: comment.php 14481 2008-04-18 11:23:01Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/cls_image.php');

$image = new cls_image($_CFG['bgcolor']);


$_REQUEST['act'] = empty($_REQUEST['act']) ? 'upload_idea': trim($_REQUEST['act']);

if($_REQUEST['act'] == "upload_idea")
{		
	$user_id = empty($_REQUEST['upload_user_id']) ? $_SESSION['user_id'] :  $_REQUEST['upload_user_id'] ;
	$cat_id  = empty($_REQUEST['cat_id']) ? 30 : $_REQUEST['cat_id'] ;
	$content = $_REQUEST['upload_content'];
	$title = trim($_REQUEST['title']);
	$add_time = gmtime();
	$is_open = 0;
	
    /* 保存评论内容 */
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('idea') . 
			" (idea_id, cat_id, title, author, content, add_time,is_open) " .
            "VALUES ('NULL','$cat_id', '$title', '$user_id', '$content','$add_time','$is_open')";
	//echo $sql;
	
	$result = $GLOBALS['db']->query($sql);
	
	log_account_change($user_id, GOLD_10, 0, 0, 0, $_LANG['MONEY_GOLD_10']);
}

if($_REQUEST['act'] == "upload_comment")
{
	$user_id  = empty($_REQUEST['upload_user_id']) ? $_SESSION['user_id'] :  $_REQUEST['upload_user_id'] ;
	$user_info = get_user_info($user_id);
	$idea_id  = empty($_REQUEST['idea_id']) ? 30 : $_REQUEST['idea_id'] ;
	$content  = $_REQUEST['content'];
	$comment_rank = $_REQUEST['comment_rank'];
	$add_time = gmtime();
	
	//echo $comment_rank."-".$idea_id ."-". $content;
	
	/* 保存评论内容 */
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('comment') . 
			" (comment_id, comment_rank, idea_id, user_id, user_name, content, add_time,is_show) " .
            "VALUES ('NULL','$comment_rank', '$idea_id', '$user_id', '$user_info[real_name]', '$content', '$add_time', '1')";
	//echo $sql;
	
	$result = $GLOBALS['db']->query($sql);
	
	log_account_change($user_id, GOLD_1, 0, 0, 0, $_LANG['MONEY_GOLD_1']);
}

if($_REQUEST['act'] == "upload_photo")
{
	$user_id  = empty($_REQUEST['upload_user_id']) ? $_SESSION['user_id'] :  $_REQUEST['upload_user_id'] ;
	$idea_id  = empty($_REQUEST['idea_id']) ? 30 : $_REQUEST['idea_id'] ;
	$cat_id  = empty($_REQUEST['cat_id']) ? 30 : $_REQUEST['cat_id'] ;
	$title  = empty($_REQUEST['title']) ? '' : $_REQUEST['title'] ;
	$photo  = $_FILES['idea_photo'];
	$desc  = $_REQUEST['idea_desc'];
	
	$sort_array = array();
	$desc_array = array();
	
	foreach($photo['size'] AS $key => $val){
		if($val > 0){
			$desc_array[$key] = $desc[$key];
			$sort_array[$key] = $key;
			log_account_change($user_id, GOLD_10, 0, 0, 0, $_LANG['MONEY_GOLD_10']);
			
		}		
	}
	
	$add_time = gmtime();
	
	
    $idea_id = $GLOBALS['db']->getOne('SELECT MAX(idea_id)+1 AS idea_id FROM ' .$ecs->table('idea'));

	
    /* 保存评论内容 */
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('idea') . 
			" (idea_id, cat_id, title, author, content, add_time,is_open) " .
            "VALUES ($idea_id,'$cat_id', '$title', '$user_id', '$content','$add_time','$is_open')";
	//echo $sql;
	$result = $GLOBALS['db']->query($sql);
	
    
	
	handle_idea_gallery_image($idea_id, $photo, $desc_array, $sort_array);
	
	
	show_message("恭喜您,照片上传成功。", $_LANG['back_home_lnk'], 'city_operate.php', 'info', true);
	
	
}

/* 上传文件 */
function upload_idea_file($upload)
{
	require(ROOT_PATH . 'includes/cls_image.php');
	
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