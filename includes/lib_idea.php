<?php

/**
 * SINEMALL 资料及资料分类相关函数库 * $Author: testyang $
 * $Id: lib_idea.php 14481 2008-04-18 11:23:01Z testyang $
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
function get_cat_ideas($cat_id, $page = 1, $size = 20,$brief_limit = 45,$sort_by='DESC',$keywords='',$idea_type = '')
{
    //取出所有非0的文章
    if ($cat_id == '-1')
    {
        $cat_str = 'a.cat_id > 0';
    }
    else
    {
        $cat_str = get_idea_children_a($cat_id);
    }
	$keywords_sql = empty($keywords) ? '' : " AND a.title LIKE '%" . mysql_like_quote($keywords) . "%' " . " OR a.content LIKE '%" . mysql_like_quote($keywords) . "%' ";
	$idea_type_sql = empty($idea_type) ? '' : " AND a.idea_type =  ' $idea_type ' ";
    
    $sql = 'SELECT a.*,ac.cat_user_rank , u.real_name' .
           ' FROM ' .$GLOBALS['ecs']->table('idea') . ' AS a ' .
			' LEFT JOIN ' .$GLOBALS['ecs']->table('idea_cat'). ' AS ac ON a.cat_id = ac.cat_id '.
			' LEFT JOIN ' .$GLOBALS['ecs']->table('users'). ' AS u ON a.author = u.user_id '.
           " WHERE a.is_open = 1 AND " . $cat_str . $keywords_sql . $idea_type_sql.
           ' ORDER BY a.idea_id '.$sort_by;
	//echo $sql."<br>";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page-1) * $size);

    $arr = array();

    if ($res)
    {
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
				$idea_id = $row['idea_id'];

	            $arr[$idea_id]['id']          = $idea_id;
	            $arr[$idea_id]['title']       = $row['title'];
	            $arr[$idea_id]['real_name']   = $row['real_name'];
	            $arr[$idea_id]['keywords']    = $row['keywords'];
	            $arr[$idea_id]['short_title'] = $GLOBALS['_CFG']['idea_title_length'] > 0 ? sub_str($row['title'], $GLOBALS['_CFG']['idea_title_length']) : $row['title'];
	            $arr[$idea_id]['author']      = empty($row['author']) || $row['author'] == '_SHOPHELP' ? $GLOBALS['_CFG']['shop_name'] : $row['author'];
	            $arr[$idea_id]['url']         = $row['open_type'] != 1 ? build_uri('idea', array('aid'=>$idea_id), $row['title']) : trim($row['file_url']);
	            $arr[$idea_id]['file_url']    = $row['file_url'];
	            $arr[$idea_id]['add_time']    = date($GLOBALS['_CFG']['date_format'], $row['add_time']);			
				$arr[$idea_id]['logo']    	 = "data/idealogo/".$row['logo'];

	            //$arr[$idea_id]['brief'] 		 = empty($row['brief']) ? str_replace("\<p\>","",sub_str($row['content'], $brief_limit) ) : $row['brief'];
	            //$arr[$idea_id]['brief'] 		 = empty($row['brief']) ? sub_str(html2txt($row['content']), $brief_limit) : $row['brief'];
	            $arr[$idea_id]['brief'] 		 = empty($row['brief']) ? sub_str(idea_strip_tags($row['content']), $brief_limit) : $row['brief'];
	            $arr[$idea_id]['short_brief'] = $row['brief'];
	            $arr[$idea_id]['content'] 	  = ($row['content']);//idea_strip_tags
				//上级分类的信息
				$arr_tem = get_idea_catinfo($idea_id);
				$arr[$idea_id]['cat_id'] 	 = $arr_tem['cat_id'];
				$arr[$idea_id]['cat_name']    = $arr_tem['cat_name'];
				$arr[$idea_id]['comment_list'] = get_idea_comment_list($idea_id);
				$arr[$idea_id]['comment_count'] = count($arr[$idea_id]['comment_list']);
				$arr[$idea_id]['gallery'] = get_idea_gallery($idea_id);
				
        }
    }

    return $arr;
}

function  get_idea_comment_list($idea_id)
{
	$sql = 'SELECT *  FROM ' . $GLOBALS['ecs']->table('comment') . 
			" WHERE idea_id = '$idea_id' ORDER BY add_time DESC";
	$arr = $GLOBALS['db']->GetAll($sql);
	foreach($arr AS $key => $row)
	{
		$arr[$key]['add_time'] = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']); // 修正添加时间显示
		$tt = str_split(strval(round($row['comment_rank'] *10,-1)));
		$arr[$key]['rank']  =  $tt[0]."_".$tt[1];
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
function get_ideacat_subcat($cat_id = 0,$limit = 20,$brief_limit = 45,$sort='ASC',$keywords='',$need_subideas = 1)
{
	//取出所有非0的文章
    if ($cat_id < 0)
    {
         return array();
    }
	if($limit){
		$where = " LIMIT 0,$limit";
	}

    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('idea_cat') . " WHERE parent_id = '$cat_id'" ;
    if ($GLOBALS['db']->getOne($sql))
	{
		$sql = 'SELECT cat_id ,cat_name,cat_logo,cat_idea_id,cat_desc,keywords,cat_user_rank  FROM ' . $GLOBALS['ecs']->table('idea_cat') . 
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
					$cats[$index]['cat_id']   = $row['cat_id'];
					$cats[$index]['cat_user_rank']   = $row['cat_user_rank'];
	                $cats[$index]['cat_name'] = $row['cat_name'];
	                $cats[$index]['cat_logo'] = 'data/idealogo/'.$row['cat_logo'];
	                $cats[$index]['cat_desc'] = $row['cat_desc'];
	                $cats[$index]['keywords'] = $row['keywords'];
					$cats[$index]['cat_nav']  = $row['cat_nav'];
	                $cats[$index]['cat_idea_id'] = $row['cat_idea_id'];

					$cat_idea = get_idea_info($row['cat_idea_id']);
					$cats[$index]['cat_idea_desc'] = sub_str(idea_strip_tags($cat_idea['content']), $brief_limit);
					$cats[$index]['subideas'] = $need_subideas ?  get_ideacat_subcat($row['cat_id']) : ''; //get_cat_ideas
					$index++;
			

        	}
			//print_r($cats);
    		return $cats;
		
		}
	else{
		$cats = array();
		$cats = get_cat_ideas($cat_id,1,$limit,$brief_limit,$sort,$keywords); //$brief_limit 简介的字符长度
		return $cats;
	}
}

function idea_strip_tags($str)
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
function get_idea_count($cat_id)
{
    global $db, $ecs;

    $count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('idea') . " WHERE " . get_idea_children($cat_id) . " AND is_open = 1");

    return $count;
}

/**
 * 获得指定资料的所有上级分类
 *
 * @access  public
 * @param   integer $cat    分类编号
 * @return  array
 */
function get_idea_catinfo($idea_id)
{
    if ($idea_id == 0)
    {
        return array();
    }

	$sql_pre = 'SELECT cat_id FROM ' . $GLOBALS['ecs']->table('idea') . " WHERE idea_id = '" . $idea_id ."'";
	$cat_id = $GLOBALS['db']->getOne($sql_pre);
	//echo $cat_id;
	$sql = 'SELECT cat_id, cat_name,cat_logo,sort_order FROM ' . $GLOBALS['ecs']->table('idea_cat') . " WHERE cat_id = '" . $cat_id ."'";
	//echo $sql;
    $arr = $GLOBALS['db']->GetRow($sql);

    if (empty($arr))
    {
        return array();
    }
	
	$arr['cat_logo'] = 'data/idealogo/'.$arr['cat_logo'];
    return $arr;
}

/**
 * 获得指定的资料的详细信息
 *
 * @access  private
 * @param   integer     $idea_id
 * @return  array
 */
function get_idea_info($idea_id)
{
    /* 获得资料的信息 */
    $sql = "SELECT a.* ".
            "FROM " .$GLOBALS['ecs']->table('idea'). " AS a ".
            "LEFT JOIN " .$GLOBALS['ecs']->table('comment'). " AS r ON r.id_value = a.idea_id AND r.comment_type = 1 ".
            "WHERE a.is_open = 1 AND a.idea_id = '$idea_id' ";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        $row['comment_rank'] = ceil($row['comment_rank']);                              // 用户评论级别取整
        $row['add_time']     = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']); // 修正添加时间显示
        $row['logo']     	= 'data/idealogo/'.$row['logo']; // 修正添加时间显示
        /* 作者信息如果为空，则用网站名称替换 */
        if (empty($row['author']) || $row['author'] == '_SHOPHELP')
        {
            $row['author'] = $GLOBALS['_CFG']['shop_name'];
        }
    }
    return $row;
}
//相册图片
function get_idea_gallery($idea_id)
{
    global $db, $ecs;
    $arr = $db->getAll("SELECT * FROM " . $ecs->table('ideas_gallery') . " WHERE idea_id = $idea_id  ORDER BY img_sort ASC , img_id DESC");

    return $arr;
}

/**
 * 取得资料列表
 * @param   string  $type   类型：all | order | shipping | payment
 */
function get_ideaLIST($cat_id)
{
    $list = array();

	$sql = 'SELECT idea_id, title'." FROM " . $GLOBALS['ecs']->table('idea') .
	 	   'WHERE cat_id ='.$cat_id;
	$temp_list = array();
	$temp_list = $GLOBALS['db']->getAll($sql);
	foreach ($temp_list AS $key => $value)
    {
		$key = $value['idea_id'];
        $list[$key] = $value['title'];
    }


	
	//print_r($list);
    return $list;
}

/**
 * 取得资料列表
 * @param   string  $type   类型：all | order | shipping | payment
 */
function get_idea_list_new($cat_id = 0)
{
	$cat_sql = $cat_id > 0 ? 'WHERE a.cat_id ='.$cat_id : ""; 

	$sql = 'SELECT a.idea_id, a.title,ac.cat_user_rank'." FROM " . $GLOBALS['ecs']->table('idea') . ' AS a ' . 
			' LEFT JOIN ' .$GLOBALS['ecs']->table('idea_cat'). ' AS ac ON a.cat_id = ac.cat_id '.
			$cat_sql . 
			" ORDER BY a.add_time DESC LIMIT 0,15 " ;
	 	   
	$temp_list = array();
	$temp_list = $GLOBALS['db']->getAll($sql);
	$arr = array();
	$index = 0;
	foreach ($temp_list AS $row)
	{	

			$arr[$index]['cat_user_rank']   =  $row['cat_user_rank'];
			$arr[$index]['idea_id']   =  $row['idea_id'];
			$arr[$index]['title']   =  $row['title'];
		
		$index++;

	}
    return $arr;
}
function get_idea_catinfo_by_cat($cat_id)
{
	$sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('idea_cat') . " WHERE cat_id = '" . $cat_id ."'";
	//echo $sql;
    $arr = $GLOBALS['db']->GetRow($sql);
	return $arr;
}

/* 首页的ideas*/
function get_index_idea($cat_id,$limit = 5)
{
	$sql = 'SELECT i.*,u.user_name,u.real_name,u.user_id  FROM ' . $GLOBALS['ecs']->table('idea') . " AS i " .
	 		' LEFT JOIN ' .$GLOBALS['ecs']->table('users'). ' AS u ON i.author = u.user_id '.
			" WHERE i.cat_id = '$cat_id' ORDER BY i.add_time DESC " ;
    
	//取出所有非0的文章
    if ($cat_id < 0)
    {
         return array();
    }
	if($limit){
		$sql .= " LIMIT 0,$limit";
	}

    $res = $GLOBALS['db']->getAll($sql);
	
	foreach ($res AS $key => $row)
	{
		$res[$key]['add_time'] 	= local_date($GLOBALS['_CFG']['date_format'], $row['add_time']); // 修正添加时间显示
		$res[$key]['brief']  	= empty($row['brief']) ? sub_str(idea_strip_tags($row['content']), 50) : $row['brief'];
        $res[$key]['short_brief'] = $row['brief'];
        $res[$key]['content'] 	= idea_strip_tags($row['content']);
	}
    
	return $res;

}


?>