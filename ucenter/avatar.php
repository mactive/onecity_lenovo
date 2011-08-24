<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: avatar.php 12126 2008-01-11 09:40:32Z heyond $
*/

//note url��ʽ: http://uc_server/avatar.php?uid=123

error_reporting(0);

$uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
$size = isset($_GET['size']) ? $_GET['size'] : '';
$random = isset($_GET['random']) ? $_GET['random'] : '';

$avatar = './data/avatar/'.get_avatar($uid, $size);

if(file_exists(dirname(__FILE__).'/'.$avatar)) {
	$random = !empty($random) ? rand(1000, 9999) : '';
	empty($_GET['check_file_exists'])?header("Location: $avatar?random=$random"):exit('1');
} else {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
	empty($_GET['check_file_exists'])?header("Location: images/noavatar_$size.gif"):exit('0');
}

/**
 * �����û��� uid �õ� avatar/home Ŀ¼
 *
 * @param int $uid
 * @return string
 */
function get_avatar($uid, $size = 'middle') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2)."_avatar_$size.jpg";
}

?>