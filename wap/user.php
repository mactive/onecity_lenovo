<?php

/**
 * SINEMALL 鐢ㄦ埛涓?績
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2008 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛沨ttp://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: testyang $
 * $Id: user.php 14543 2008-05-04 02:18:37Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$act = !empty($_GET['act']) ? $_GET['act'] : 'login';

$smarty->assign('footer', get_footer());
/* 鐢ㄦ埛鐧婚檰 */
if ($act == 'do_login')
{
    $user_name = !empty($_POST['username']) ? $_POST['username'] : '';
    $pwd = !empty($_POST['pwd']) ? $_POST['pwd'] : '';
    if (empty($user_name) || empty($pwd))
    {
        $login_faild = 1;
    }
    else
    {
        if ($user->check_user($user_name, $pwd) > 0)
        {
            $user->set_session($user_name);
            show_user_center();
        }
        else
        {
            $login_faild = 1;
        }
    }

    if (!empty($login_faild))
    {
        $smarty->assign('login_faild', 1);
        $smarty->display('login.wml');
    }
}

elseif ($act == 'order_list')
{
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = {$_SESSION['user_id']}");
    if ($record_count > 0)
    {
        include_once(ROOT_PATH . 'includes/lib_transaction.php');
        $page_num = '10';
        $page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
        $pages = ceil($record_count / $page_num);
        if ($page <= 0)
        {
            $page = 1;
        }
        if ($pages == 0)
        {
            $pages = 1;
        }
        if ($page > $pages)
        {
            $page = $pages;
        }
        $pagebar = get_wap_pager($record_count, $page_num, $page, 'user.php?act=order_list', 'page');
        $smarty->assign('pagebar' , $pagebar);
        /* 璁㈠崟鐘舵€ */
        $_LANG['os'][OS_UNCONFIRMED] = '鏈?‘璁?;
        $_LANG['os'][OS_CONFIRMED] = '宸茬‘璁?;
        $_LANG['os'][OS_CANCELED] = '宸插彇娑?;
        $_LANG['os'][OS_INVALID] = '鏃犳晥';
        $_LANG['os'][OS_RETURNED] = '閫€璐?;
        $_LANG['ss'][SS_UNSHIPPED] = '鏈?彂璐?;
        $_LANG['ss'][SS_SHIPPED] = '宸插彂璐?;
        $_LANG['ss'][SS_RECEIVED] = '鏀惰揣纭??';
        $_LANG['ps'][PS_UNPAYED] = '鏈?粯娆?;
        $_LANG['ps'][PS_PAYING] = '浠樻?涓?;
        $_LANG['ps'][PS_PAYED] = '宸蹭粯娆?;
        $_LANG['confirm_cancel'] = '鎮ㄧ‘璁よ?鍙栨秷璇ヨ?鍗曞悧锛熷彇娑堝悗姝よ?鍗曞皢瑙嗕负鏃犳晥璁㈠崟';
        $_LANG['cancel'] = '鍙栨秷璁㈠崟';
        $orders = get_user_orders($_SESSION['user_id'], $page_num, ($page_num * ($page - 1)));
        //$merge  = get_user_merge($_SESSION['user_id']);
        $smarty->assign('orders', $orders);
    }

    $smarty->display('order_list.wml');
}

/* 鐢ㄦ埛涓?績 */
else
{
    if ($_SESSION['user_id'] > 0)
    {
        show_user_center();
    }
    else
    {
        $smarty->display('login.wml');
    }
}

/**
 * 鐢ㄦ埛涓?績鏄剧ず
 */
function show_user_center()
{
    $best_goods = get_recommend_goods('best');
    if (count($best_goods) > 0)
    {
        foreach  ($best_goods as $key => $best_data)
        {
            $best_goods[$key]['shop_price'] = encode_output($best_data['shop_price']);
            $best_goods[$key]['name'] = encode_output($best_data['name']);
        }
    }
    $GLOBALS['smarty']->assign('best_goods' , $best_goods);
    $GLOBALS['smarty']->display('user.wml');
}

?>