<?php
/**
 * SINEMALL 生成显示商品的js代码    $Author: testyang $
 * $Id: gen_goods_script.php 14481 2008-04-18 11:23:01Z testyang $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 生成代码
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'setup')
{
    /* 检查权限 */
    admin_priv('goods_manage');

    /* 编码 */
    $lang_list = array(
        'UTF8'   => $_LANG['charset']['utf8'],
        'GB2312' => $_LANG['charset']['zh_cn'],
        'BIG5'   => $_LANG['charset']['zh_tw'],
    );

    /* 参数赋值 */
    $ur_here = $_LANG['15_goods_script'];
    $smarty->assign('ur_here',    $ur_here);
    $smarty->assign('cat_list',   cat_list());
    $smarty->assign('brand_list', get_brand_list());
    $smarty->assign('intro_list', $_LANG['intro']);
    $smarty->assign('url',        $ecs->url());
    $smarty->assign('lang_list',  $lang_list);

    /* 显示模板 */
    assign_query_info();
    $smarty->display('gen_goods_script.htm');
}

?>