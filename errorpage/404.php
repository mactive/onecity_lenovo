<?php

/**
 * ECSHOP 资料内容
 * ============================================================================
 * 版权所有 (C) 2005-2008 康盛创想（北京）科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；http://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: testyang $
 * $Id: article.php 14481 2008-04-18 11:23:01Z testyang $
*/


define('IN_ECS', true);


require(dirname(dirname(__FILE__)) . '/includes/init.php');


if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}




/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

//$cache_id = $article_id . '-' . $_SESSION['user_rank'].'-'.$_CFG['lang'];
//$cache_id = sprintf('%X', crc32($cache_id));

//$cache_id = sprintf('%X', crc32($_REQUEST['id'] . '-' . $_CFG['lang']));
//$smarty->is_cached('article.dwt', $cache_id);

if (true)
{
	$article = array();
	$article['title'] = "您要的页面没有找到 -- 试试搜索！";
	$article['content']= "或者<a href='index.php' class='font14px'>回到首页</a><br/><br/><br/>";
	
	
	$smarty->assign('article',      $article);
	
	

	$smarty->display('article_solution.dwt');

	
}


?>