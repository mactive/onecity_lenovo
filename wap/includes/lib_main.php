<?php

/**
 * SINEMALL wap鍓嶅彴鍏?叡鍑芥暟
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2008 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛沨ttp://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: testyang $
 * $Id: lib_main.php 14545 2008-05-04 02:54:30Z testyang $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 瀵硅緭鍑虹紪鐮
 *
 * @access  public
 * @param   string   $str
 * @return  string
 */
function encode_output($str)
{
    if (EC_CHARSET != 'utf-8')
    {
        $str = ecs_iconv(EC_CHARSET, 'utf-8', $str);
    }
    return htmlspecialchars($str);
}

/**
 * wap鍒嗛〉鍑芥暟
 *
 * @access      public
 * @param       int     $num        鎬昏?褰曟暟
 * @param       int     $perpage    姣忛〉璁板綍鏁
 * @param       int     $curr_page  褰撳墠椤垫暟
 * @param       string  $mpurl      浼犲叆鐨勮繛鎺ュ湴鍧€
 * @param       string  $pvar       鍒嗛〉鍙橀噺
 */
function get_wap_pager($num, $perpage, $curr_page, $mpurl,$pvar)
{
    $multipage = '';
    if($num > $perpage)
    {
        $page = 2;
        $offset = 1;
        $pages = ceil($num / $perpage);
        $all_pages = $pages;
        $tmp_page = $curr_page;
        $setp = strpos($mpurl, '?') === false ? "?" : '&amp;';
        if($curr_page > 1)
        {
            $multipage .= "<a href=\"$mpurl${setp}${pvar}=".($curr_page-1)."\">涓婁竴椤袋/a>";
        }
        $multipage .= $curr_page."/".$pages;
        if(($curr_page++) < $pages)
        {
            $multipage .= "<a href=\"$mpurl${setp}${pvar}=".$curr_page++."\">涓嬩竴椤袋/a><br/>";
        }
        //$multipage .= $pages > $page ? " ... <a href=\"$mpurl&amp;$pvar=$pages\"> [$pages] &gt;&gt;</a>" : " 椤?".$all_pages."椤?;
        $url_array = explode("?" , $mpurl);
        $field_str = "";
        if (isset($url_array[1]))
        {
            $filed_array = explode("&amp;" , $url_array[1]);
            if (count($filed_array) > 0)
            {
                foreach ($filed_array AS $data)
                {
                    $value_array = explode("=" , $data);
                    $field_str .= "<postfield name='".$value_array[0]."' value='".encode_output($value_array[1])."'/>\n";
                }
            }
        }
        $multipage .= "璺宠浆鍒扮?<input type='text' name='pageno' format='*N' size='4' value='' maxlength='2' emptyok='true' />椤袋anchor>[GO]<go href='{$url_array[0]}' method='get'>{$field_str}<postfield name='".$pvar."' value='$(pageno)'/></go></anchor>";
        //<postfield name='snid' value='".session_id()."'/>
    }
    return $multipage;
}

/**
 * 杩斿洖灏炬枃浠
 *
 * @return  string
 */
function get_footer()
{
    if (substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')) == '/index.php')
    {
        $footer = "<br/>Powered by ECShop[".local_date('H:i')."]";
    }
    else
    {
        $footer = "<br/><select><option onpick='index.php'>蹇?€熼€氶亾</option><option onpick='goods_list.php?type=best'>绮惧搧鎺ㄨ崘</option><option onpick='goods_list.php?type=promote'>鍟嗗?淇冮攢</option><option onpick='goods_list.php?type=hot'>鐑?棬鍟嗗搧</option><option onpick='goods_list.php?type=new'>鏈€鏂颁骇鍝?/option></select>";
    }

    return $footer;
}

?>