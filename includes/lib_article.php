<?php

/**
 * SINEMALL 资料及资料分类相关函数库 * $Author: testyang $
 * $Id: lib_article.php 14481 2008-04-18 11:23:01Z testyang $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 获得文章分类下的文章列表
 *
 * @access  public
 * @param   integer     $cat_id
 * @param   integer     $page
 * @param   integer     $size
 *
 * @return  array
 */
function get_cat_articles($cat_id, $page = 1, $size = 20,$brief_limit = 45,$sort_by='DESC',$keywords='',$article_type = '')
{
    //取出所有非0的文章
    if ($cat_id == '-1')
    {
        $cat_str = 'a.cat_id > 0';
    }
    else
    {
        $cat_str = get_article_children_a($cat_id);
    }
	$keywords_sql = empty($keywords) ? '' : " AND a.title LIKE '%" . mysql_like_quote($keywords) . "%' " . " OR a.content LIKE '%" . mysql_like_quote($keywords) . "%' ";
	$article_type_sql = empty($article_type) ? '' : " AND a.article_type =  ' $article_type ' ";
    
    $sql = 'SELECT a.*,ac.cat_user_rank ' .
           ' FROM ' .$GLOBALS['ecs']->table('article') . ' AS a ' .
			' LEFT JOIN ' .$GLOBALS['ecs']->table('article_cat'). ' AS ac ON a.cat_id = ac.cat_id '.
           " WHERE a.is_open = 1 AND " . $cat_str . $keywords_sql . $article_type_sql.
           ' ORDER BY a.article_id '.$sort_by;
	//echo $sql."<br>";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page-1) * $size);

    $arr = array();

    if ($res)
    {
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
			if($row['cat_user_rank'] <= $_SESSION['user_rank'])
			{
				$article_id = $row['article_id'];

	            $arr[$article_id]['id']          = $article_id;
	            $arr[$article_id]['title']       = $row['title'];
	            $arr[$article_id]['keywords']    = $row['keywords'];
	            $arr[$article_id]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ? sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
	            $arr[$article_id]['author']      = empty($row['author']) || $row['author'] == '_SHOPHELP' ? $GLOBALS['_CFG']['shop_name'] : $row['author'];
	            $arr[$article_id]['url']         = $row['open_type'] != 1 ? build_uri('article', array('aid'=>$article_id), $row['title']) : trim($row['file_url']);
	            $arr[$article_id]['file_url']    = $row['file_url'];
	            $arr[$article_id]['add_time']    = date($GLOBALS['_CFG']['date_format'], $row['add_time']);			
				$arr[$article_id]['logo']    	 = "data/articlelogo/".$row['logo'];

	            //$arr[$article_id]['brief'] 		 = empty($row['brief']) ? str_replace("\<p\>","",sub_str($row['content'], $brief_limit) ) : $row['brief'];
	            //$arr[$article_id]['brief'] 		 = empty($row['brief']) ? sub_str(html2txt($row['content']), $brief_limit) : $row['brief'];
	            $arr[$article_id]['brief'] 		 = empty($row['brief']) ? sub_str(_strip_tags($row['content']), $brief_limit) : $row['brief'];
	            $arr[$article_id]['short_brief'] = $row['brief'];
	            $arr[$article_id]['content'] 	 = _strip_tags($row['content']);
				//上级分类的信息
				$arr_tem = get_article_catinfo($article_id);
				$arr[$article_id]['cat_id'] 	 = $arr_tem['cat_id'];
				$arr[$article_id]['cat_name']    = $arr_tem['cat_name'];
			}	
        }
    }

    return $arr;
}

/**
 * 获得文章分类下的小分类及其文章列表
 *
 * @access  public
 * @param   integer     $cat_id
 * @param   integer     $page
 * @param   integer     $size
 *
 * @return  array
 */
/**/
function get_articlecat_subcat($cat_id = 0,$limit = 20,$brief_limit = 45,$sort='ASC',$keywords='',$need_subarticles = 1)
{
	//取出所有非0的文章
    if ($cat_id < 0)
    {
         return array();
    }
	if($limit){
		$where = " LIMIT 0,$limit";
	}

    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('article_cat') . " WHERE parent_id = '$cat_id'" ;
    if ($GLOBALS['db']->getOne($sql))
	{
		$sql = 'SELECT cat_id ,cat_name,cat_logo,cat_article_id,cat_desc,keywords,cat_user_rank  FROM ' . $GLOBALS['ecs']->table('article_cat') . 
				" WHERE parent_id = '$cat_id' ORDER BY cat_id $sort";
		$arr = $GLOBALS['db']->GetAll($sql);

    	if (empty($arr))
    	{
        	return array();
    	}
		$index = 0;
    	$cats  = array();
        	foreach ($arr AS $row)
        	{	
				if($row['cat_user_rank'] <= $_SESSION['user_rank'])
				{
					$cats[$index]['cat_id']   = $row['cat_id'];
					$cats[$index]['cat_user_rank']   = $row['cat_user_rank'];
	                $cats[$index]['cat_name'] = $row['cat_name'];
	                $cats[$index]['cat_logo'] = 'data/articlelogo/'.$row['cat_logo'];
	                $cats[$index]['cat_desc'] = $row['cat_desc'];
	                $cats[$index]['keywords'] = $row['keywords'];
					$cats[$index]['cat_nav']  = $row['cat_nav'];
	                $cats[$index]['cat_article_id'] = $row['cat_article_id'];

					$cat_article = get_article_info($row['cat_article_id']);
					$cats[$index]['cat_article_desc'] = sub_str(_strip_tags($cat_article['content']), $brief_limit);
					$cats[$index]['subarticles'] = $need_subarticles ?  get_articlecat_subcat($row['cat_id']) : ''; //get_cat_articles
					$index++;
				}

        	}
			//print_r($cats);
    		return $cats;
		
		}
	else{
		$cats = array();
		$cats = get_cat_articles($cat_id,1,$limit,$brief_limit,$sort,$keywords); //$brief_limit 简介的字符长度
		return $cats;
	}
}

function _strip_tags($str)
{
	$tags_a = array("a","img","div","p","strong","br","table","script");
	foreach ($tags_a as $tag)
	{
		$p[]="/(<(?:\/".$tag."|".$tag.")[^>]*>)/i";
	}
	$return_str = preg_replace($p,"",$str);
	return $return_str;
}


/**
 * 获得指定分类下的资料总数
 *
 * @param   integer     $cat_id
 *
 * @return  integer
 */
function get_article_count($cat_id)
{
    global $db, $ecs;

    $count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('article') . " WHERE " . get_article_children($cat_id) . " AND is_open = 1");

    return $count;
}

/**
 * 获得指定资料的所有上级分类
 *
 * @access  public
 * @param   integer $cat    分类编号
 * @return  array
 */
function get_article_catinfo($article_id)
{
    if ($article_id == 0)
    {
        return array();
    }

	$sql_pre = 'SELECT cat_id FROM ' . $GLOBALS['ecs']->table('article') . " WHERE article_id = '" . $article_id ."'";
	$cat_id = $GLOBALS['db']->getOne($sql_pre);
	//echo $cat_id;
	$sql = 'SELECT cat_id, cat_name,cat_logo,sort_order FROM ' . $GLOBALS['ecs']->table('article_cat') . " WHERE cat_id = '" . $cat_id ."'";
	//echo $sql;
    $arr = $GLOBALS['db']->GetRow($sql);

    if (empty($arr))
    {
        return array();
    }
	
	$arr['cat_logo'] = 'data/articlelogo/'.$arr['cat_logo'];
    return $arr;
}

/**
 * 获得产品系列的列表
 *
 * @access  public
 * @param   integer     $cat_id
 * @param   integer     $page
 * @param   integer     $size
 *
 * @return  array
 */
function get_goods_series($size = 20,$brief_limit = 45,$is_index = 0 ,$page = 1,$tag_name='')
{
	$where = $is_index > 0 ? " WHERE is_index = 1  "  :  " WHERE 1  " ;
	if($tag_name){
		$where.=" AND topic_tag LIKE '%" . mysql_like_quote($tag_name) . "%' ";
	}
    $sql = 'SELECT topic_id, title, brief, intro, logo, start_time, end_time ,topic_tag' .
           ' FROM ' .$GLOBALS['ecs']->table('topic') .
            $where .
           ' ORDER BY topic_id DESC';
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page-1) * $size);

    $arr = array();
    if ($res)
    {
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $topic_id = $row['topic_id'];

            $arr[$topic_id]['topic_id']     = $topic_id;
            $arr[$topic_id]['title']       	= $row['title'];
            $arr[$topic_id]['short_title'] 	= $GLOBALS['_CFG']['article_title_length'] > 0 ? sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
            $arr[$topic_id]['url']         	= 'topic.php?topic_id='.$topic_id;
            $arr[$topic_id]['start_time'] 	= date($GLOBALS['_CFG']['start_time'], $row['start_time']);			
            $arr[$topic_id]['end_time']    	= date($GLOBALS['_CFG']['end_time'], $row['end_time']);			
			$arr[$topic_id]['logo']    	 	= "data/topiclogo/".$row['logo'];
			$arr[$topic_id]['full_brief'] 	= $row['brief'];
            $arr[$topic_id]['brief'] 		= sub_str($row['brief'], $brief_limit);
			
        }
    }

    return $arr;
}

/**
 * 获得指定的资料的详细信息
 *
 * @access  private
 * @param   integer     $article_id
 * @return  array
 */
function get_article_info($article_id)
{
    /* 获得资料的信息 */
    $sql = "SELECT a.* ".
            "FROM " .$GLOBALS['ecs']->table('article'). " AS a ".
        //    "LEFT JOIN " .$GLOBALS['ecs']->table('comment'). " AS r ON r.id_value = a.article_id AND r.comment_type = 1 ".
            "WHERE a.is_open = 1 AND a.article_id = '$article_id' ";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        $row['comment_rank'] = ceil($row['comment_rank']);                              // 用户评论级别取整
        $row['add_time']     = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']); // 修正添加时间显示
        $row['logo']     	= 'data/articlelogo/'.$row['logo']; // 修正添加时间显示
        /* 作者信息如果为空，则用网站名称替换 */
        if (empty($row['author']) || $row['author'] == '_SHOPHELP')
        {
            $row['author'] = $GLOBALS['_CFG']['shop_name'];
        }
    }
    return $row;
}
//相册图片
function get_article_gallery($article_id)
{
    global $db, $ecs;
    $arr = $db->getAll("SELECT * FROM " . $ecs->table('articles_gallery') . " WHERE article_id = $article_id  ORDER BY img_sort ASC , img_id DESC");

    return $arr;
}
//flickr 图库信息
function get_photosets_flickr($set_id){
	include_once('phpFlickr.php');
	
	//require_once("phpFlickr.php");
	// Create new phpFlickr object
	$f = new phpFlickr("636958e206940f536fc87e9e9e3549a9");
	//$set_id=$_POST['set_id'];
	$i = 0;
	if ($set_id) {
		$arr = array();
		
	    // Get the friendly URL of the user's photos
	    //$photos_url = $f->urls_getUserPhotos($person['id']);
	    $photosets = $f->photosets_getPhotos($set_id);


	    // Get the user's first 36 public photos
	    //$photos = $f->people_getPublicPhotos($person['id'], NULL, NULL, 36);
		$result ='';
	    // Loop through the photos and output the html
	    foreach ((array)$photosets['photoset']['photo'] as $photo) {
			$id = $photo['id'];
			$arr[$id] = $photo;
			$arr[$id]['thumb'] = $f->buildPhotoURL($photo, "Square");
			$arr[$id]['img'] = $f->buildPhotoURL($photo, "medium");
	        $i++;
	    }
	}
	//echo $result;	
	return $arr;
}
/* filckr html 信息*/
function get_photosets_flickr_html($set_id){
	include_once('phpFlickr.php');
	
	//require_once("phpFlickr.php");
	// Create new phpFlickr object
	$f = new phpFlickr("636958e206940f536fc87e9e9e3549a9");
	//$set_id=$_POST['set_id'];

	$i = 0;
	if ($set_id) {

	    // Get the friendly URL of the user's photos
	    //$photos_url = $f->urls_getUserPhotos($person['id']);
	    $photosets = $f->photosets_getPhotos($set_id);


	    // Get the user's first 36 public photos
	    //$photos = $f->people_getPublicPhotos($person['id'], NULL, NULL, 36);
		$result ='';
	    // Loop through the photos and output the html
	    foreach ((array)$photosets['photoset']['photo'] as $photo) {
			$result.= "<a href=$photos_url$photo[id]>";
	        $result.= "<img border='0' alt='$photo[title]' ".
	            "src=" . $f->buildPhotoURL($photo, "Square") . ">";
	        $result.= "</a>";
	        $i++;
	        // If it reaches the sixth photo, insert a line break
	        if ($i % 3 == 0) {
	            $result.= "<br>\n";
	        }
	    }
	}
	//echo $result;	
	return $result;
}


/**
 * 取得资料列表
 * @param   string  $type   类型：all | order | shipping | payment
 */
function get_articleLIST($cat_id)
{
    $list = array();

	$sql = 'SELECT article_id, title'." FROM " . $GLOBALS['ecs']->table('article') .
	 	   'WHERE cat_id ='.$cat_id;
	$temp_list = array();
	$temp_list = $GLOBALS['db']->getAll($sql);
	foreach ($temp_list AS $key => $value)
    {
		$key = $value['article_id'];
        $list[$key] = $value['title'];
    }


	
	//print_r($list);
    return $list;
}

/**
 * 取得资料列表
 * @param   string  $type   类型：all | order | shipping | payment
 */
function get_article_list_new($cat_id = 0)
{
	$cat_sql = $cat_id > 0 ? 'WHERE a.cat_id ='.$cat_id : ""; 

	$sql = 'SELECT a.article_id, a.title,ac.cat_user_rank'." FROM " . $GLOBALS['ecs']->table('article') . ' AS a ' . 
			' LEFT JOIN ' .$GLOBALS['ecs']->table('article_cat'). ' AS ac ON a.cat_id = ac.cat_id '.
			$cat_sql . 
			" ORDER BY a.add_time DESC LIMIT 0,15 " ;
	 	   
	$temp_list = array();
	$temp_list = $GLOBALS['db']->getAll($sql);
	$arr = array();
	$index = 0;
	foreach ($temp_list AS $row)
	{	
		if($row['cat_user_rank'] <= $_SESSION['user_rank'])
		{
			$arr[$index]['cat_user_rank']   =  $row['cat_user_rank'];
			$arr[$index]['article_id']   =  $row['article_id'];
			$arr[$index]['title']   =  $row['title'];
		}
		
		$index++;

	}
    return $arr;
}
function get_article_catinfo_by_cat($cat_id)
{
	$sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('article_cat') . " WHERE cat_id = '" . $cat_id ."'";
	//echo $sql;
    $arr = $GLOBALS['db']->GetRow($sql);
	return $arr;
}



?>