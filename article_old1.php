<?php

/**
 * SINEMALL 资料内容 * $Author: testyang $
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
$article_id     = $_REQUEST['id'];
if(isset($_REQUEST['cat_id']) && $_REQUEST['cat_id'] < 0)
{
    $article_id = $db->getOne("SELECT article_id FROM " . $ecs->table('article') . " WHERE cat_id = '".intval($_REQUEST['cat_id'])."' ");
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

$cache_id = sprintf('%X', crc32($_REQUEST['id'] . '-' . $_CFG['lang']));

if (!$smarty->is_cached('article.dwt', $cache_id))
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
    $smarty->assign('top_goods',        get_top10());    // 销售排行
    //$smarty->assign('best_goods',      get_recommend_goods('best'));    //最新的推荐商品
	$smarty->assign('best_goods',      	get_index_goods(10) );    // 后台指定的推荐商品
    $smarty->assign('new_goods',        get_recommend_goods('new'));        // 最新商品
    $smarty->assign('hot_goods',        get_recommend_goods('hot'));        // 热点资料
    $smarty->assign('promotion_goods',  get_recommend_goods('promote'));    // 特价商品
    $smarty->assign('related_goods',    article_related_goods($_REQUEST['id']));  // 特价商品
    $smarty->assign('id',               $article_id);
//    $smarty->assign('username',         $_SESSION['user_name']);
//    $smarty->assign('email',            $_SESSION['email']);

    $smarty->assign('type',            '1');
    $smarty->assign('promotion_info', get_promotion_info());
	$cc = 6;
	$smarty->assign('customer_center_art',  get_articlecat_subcat($cc)); //客服中心

    /* 验证码相关设置 */
    if ((intval($_CFG['captcha']) & CAPTCHA_COMMENT) && gd_version() > 0)
    {
        $smarty->assign('enabled_captcha', 1);
        $smarty->assign('rand',            mt_rand());
    }

    $smarty->assign('article',      $article);
    $smarty->assign('keywords',     htmlspecialchars($article['keywords']));
    $smarty->assign('descriptions', htmlspecialchars($article['title']));

	/*
    $catlist = array();
    foreach(get_article_parent_cats($article['cat_id']) as $k=>$v)
    {
        $catlist[] = $v['cat_id'];
    }

    assign_template('a', $catlist);
	*/
	
    $position = assign_ur_here($article['cat_id'], $article['title']);
    $smarty->assign('page_title',   $position['title']);    // 页面标题
    $smarty->assign('ur_here',      $position['ur_here']);  // 当前位置
    $smarty->assign('comment_type', 1);

	/* Flickr 相册*/
	$article_gallery = get_photosets_flickr($article['flickr_rss']);
	$smarty->assign('article_gallery', $article_gallery);
    
	
    /* 相关商品 */
    $sql = "SELECT a.goods_id, g.goods_name,g.goods_thumb " .
            "FROM " . $ecs->table('goods_article') . " AS a, " . $ecs->table('goods') . " AS g " .
            "WHERE a.goods_id = g.goods_id " .
            "AND a.article_id = '$_REQUEST[id]' ";
	$row = $db->getAll($sql);
	foreach($row AS $key => $val){
		$row[$key]['url'] 	= build_uri('goods', array('gid' => $val['goods_id']), $val['goods_name']);
		$row[$key]['thumb'] 	= empty($val['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $val['goods_thumb'];	
	}
    $smarty->assign('goods_list', $row);

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

    assign_dynamic('article');
}

/* solution_cat 解决方案 下属的4个资料分类 ID=>Name */
$solution_cat = array();
$index_solution_cat = get_articlecat_subcat($_CFG['index_solutions_article_cat']);
foreach($index_solution_cat AS $key=> $val)
{
	$solution_cat[$val['cat_id']] = $val['cat_name'];
}


/* 按照 cat_ID 的不同显示不同的资料模板 */
if($article['cat_id'] > 2 && $article['cat_id'] < 12)
{
    $smarty->display('article.dwt', $cache_id);
}
elseif($article['cat_id'] >= 12 && $article['cat_id'] < 14)
{
    $smarty->display('article_solution.dwt', $cache_id);
}
elseif(array_key_exists($article['cat_id'],$solution_cat) )
{
	$smarty->assign('article_cat', get_cat_articles($article['cat_id']) );
    
	$smarty->display('article_solution.dwt', $cache_id);//如果在首页类里那么 用这个模板
}
else
{
    $smarty->display('article_solution.dwt', $cache_id);
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