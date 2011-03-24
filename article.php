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

require(dirname(__FILE__) . '/includes/init.php');


if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}


/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

$_REQUEST['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$_REQUEST['act'] = empty($_REQUEST['act']) ? 'article': trim($_REQUEST['act']);
$article_id     = $_REQUEST['id'];



/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

//$cache_id = $article_id . '-' . $_SESSION['user_rank'].'-'.$_CFG['lang'];
//$cache_id = sprintf('%X', crc32($cache_id));

//$cache_id = sprintf('%X', crc32($_REQUEST['id'] . '-' . $_CFG['lang']));
//$smarty->is_cached('article.dwt', $cache_id);

if ($_REQUEST['act'] == 'article')
{
    /* 资料详情 */
    $article = get_article_info($article_id);
    if (empty($article))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    if (!empty($article['link']) && $article['link'] != 'http://' && $article['link'] != 'https://')
    {
        ecs_header("location:$article[link]\n");
        exit;
    }

    $smarty->assign('categories',       get_categories_tree());  // 分类树
    $smarty->assign('helps',            get_shop_help()); // 网店帮助

	/*
    $smarty->assign('top_goods',        get_top10());    // 销售排行
    $smarty->assign('best_goods',       get_recommend_goods('best'));       // 推荐商品
    $smarty->assign('new_goods',        get_recommend_goods('new'));        // 最新商品
    $smarty->assign('hot_goods',        get_recommend_goods('hot'));        // 热点资料
    $smarty->assign('promotion_goods',  get_recommend_goods('promote'));    // 特价商品
	*/
	
    //$smarty->assign('related_goods',    article_related_goods($_REQUEST['id']));  // 特价商品
    $smarty->assign('id',               $article_id);
    $smarty->assign('username',         $_SESSION['user_name']);
    $smarty->assign('email',            $_SESSION['email']);

	

    $smarty->assign('article',      $article);
    $smarty->assign('keywords',     htmlspecialchars($article['keywords']));
    $smarty->assign('descriptions', htmlspecialchars($article['title']));



    $position = assign_ur_here($article['cat_id'], $article['title']);
    $smarty->assign('page_title',   $position['title']);    // 页面标题
    $smarty->assign('ur_here',      $position['ur_here']);  // 当前位置
    $smarty->assign('comment_type', 1);	

	
	$article_gallery = get_article_gallery($article['article_id']);
	$smarty->assign('article_gallery', $article_gallery);


	/* 相关商品 */
    $sql = "SELECT a.goods_id, g.article_id,g.logo ,g.title " .
            "FROM " . $ecs->table('goods_article') . " AS a, " . $ecs->table('article') . " AS g " .
            "WHERE a.article_id  = g.article_id " .
            "AND a.goods_id = '$_REQUEST[id]' ";
	$row = $db->getAll($sql);
	foreach($row AS $key => $val){
		$row[$key]['logo'] 		= !empty($val['logo']) ? 'data/articlelogo/'.$val['logo']:"";
		$row[$key]['title_num'] = strlen($val['title']);
	}
    $smarty->assign('relate_article', $row);

    /* 上一篇下一篇资料 */
    $next_article = $db->getRow("SELECT article_id, title FROM " .$ecs->table('article'). " WHERE article_id > $article_id AND cat_id=$article[cat_id] AND is_open=1 LIMIT 1");
    $next_article['url'] = build_uri('article', array('aid'=>$next_article['article_id']), $next_article['title']);
    $smarty->assign('next_article', $next_article);

    $prev_aid = $db->getOne("SELECT max(article_id) FROM " . $ecs->table('article') . " WHERE article_id < $article_id AND cat_id=$article[cat_id] AND is_open=1");
    if (!empty($prev_aid))
    {
        $prev_article = $db->getRow("SELECT article_id, title FROM " .$ecs->table('article'). " WHERE article_id = $prev_aid");
        $prev_article['url'] = build_uri('article', array('aid'=>$prev_article['article_id']), $prev_article['title']);
        $smarty->assign('prev_article', $prev_article);
    }

    //assign_dynamic('article');

	/* solution_cat 解决方案 下属的4个资料分类 ID=>Name */
	$solution_cat = array();
	$index_solution_cat = get_articlecat_subcat($_CFG['index_solutions_article_cat']);
	foreach($index_solution_cat AS $key=> $val)
	{
		$solution_cat[$val['cat_id']] = $val['cat_name'];
	}
	
	$same_article_cat = get_cat_articles($article['cat_id'],1,20,45,'ASC'); // 同一个路径里的 资料分类 在 assign 里不能传参数
	$smarty->assign('article_cat', $same_article_cat);
	
	
	if($article['cat_id'] == 13 || $article['cat_id'] == 12){
		$same_article_cat = get_cat_articles($article['cat_id'],1,20,45,'DESC'); // 同一个路径里的 资料分类 在 assign 里不能传参数
		$smarty->assign('article_cat', $same_article_cat);
	}
	
	//资料分类的信息
	$article_cat_info = get_article_catinfo($article_id);
	$smarty->assign('article_cat_info', $article_cat_info);
	$smarty->assign('nav_index', $article_cat_info['sort_order']);
	
	

	//$smarty->display('article.dwt');
	/* 按照 templete 的不同显示不同的资料模板 */
	if($article['templete'] == 1){
		$smarty->display('article.dwt');
	}
	if($article['templete'] == 11){
		$smarty->display('article_11.dwt');
	}
	if($article['templete'] == 2){
		$smarty->display('article_b.dwt');
	}
	if($article['templete'] == 3){
		$smarty->display('article_c.dwt');
	}
	if($article['templete'] == 4){
		$smarty->display('article_d.dwt');
	}
	/*
	if($article['cat_id'] > 2 && $article['cat_id'] < 12 && $article['cat_id'] != 6)
	{
		$smarty->display('article.dwt');
	}
	elseif($article['cat_id'] == 6 || $article['cat_id'] == 29)
	{
		$tmp_cat_id = $article['cat_id'];
	    
		$cat_info = $db->getRow("SELECT * FROM " . $ecs->table('article_cat') . " WHERE cat_id = '$tmp_cat_id'");
		$smarty->assign('cat_info',    $cat_info);
		
		$smarty->assign('articleLIST',  get_articleLIST($tmp_cat_id)); //下拉菜单
		$smarty->assign('cc_article',  get_article_info($article['article_id'])); //下拉菜单

	    $smarty->display('customer_center.dwt', $cache_id);
	}
	elseif($article['cat_id'] >= 12 && $article['cat_id'] < 14)
	{
	    $smarty->display('article_solution.dwt');
	}
	elseif(array_key_exists($article['cat_id'],$solution_cat) )
	{

		$smarty->display('article_solution.dwt');//如果在首页类里那么 用这个模板
	}
	else
	{
	    $smarty->display('article_solution.dwt');
	}
	*/
	
	
}
elseif($_REQUEST['act'] == 'content')
{
	$article = get_article_info($article_id);
	$smarty->assign('content', $article['content']);
	$smarty->display('article_content.dwt');    
}
elseif($_REQUEST['act'] == 'download')
{
	$article = get_article_info($article_id);
    
	$file_name = trim($_GET['file_url']);
	if (!file_exists($file_name)){ 
		show_message("文件不存在", $_LANG['profile_lnk'], 'article_cat.php', 'info', true);        
        return false;
        exit;
	}else{
		$newclick = $article['download_click']+1;
		$sql = "UPDATE " . $GLOBALS['ecs']->table('article') . " SET download_click = '$newclick' WHERE article_id = $article[article_id]";
	    $GLOBALS['db']->query($sql);
	
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".filesize($file_name));
        header( 'Content-Transfer-Encoding: binary' );
        header("Content-Disposition: attachment; filename=" .$file_name); 
        header('Pragma: no-cache');
        header('Expires: 0');
		$file = fopen($file_name,"r"); 
        echo fread($file,filesize($file_name));
        fclose($file);
        exit;
    }    
}


/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */


/**
 * 获得资料关联的商品
 *
 * @access  public
 * @param   integer $id
 * @return  array
 */
function article_related_goods($id)
{
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'g.market_price, g.promote_price, g.promote_start_date, g.promote_end_date ' .
            'FROM ' . $GLOBALS['ecs']->table('goods_article') . ' ga ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = ga.goods_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE ga.article_id = '$id' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0";
    $res = $GLOBALS['db']->query($sql);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$row['goods_id']]['goods_id']      = $row['goods_id'];
        $arr[$row['goods_id']]['goods_name']    = $row['goods_name'];
        $arr[$row['goods_id']]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $arr[$row['goods_id']]['goods_thumb']   = (empty($row['goods_thumb'])) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
        $arr[$row['goods_id']]['goods_img']     = (empty($row['goods_img'])) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];
        $arr[$row['goods_id']]['market_price']  = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
        $arr[$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        if ($row['promote_price'] > 0)
        {
            $arr[$row['goods_id']]['promote_price'] = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $arr[$row['goods_id']]['formated_promote_price'] = price_format($arr[$row['goods_id']]['promote_price']);
        }
        else
        {
            $arr[$row['goods_id']]['promote_price'] = 0;
        }
    }

    return $arr;
}

?>