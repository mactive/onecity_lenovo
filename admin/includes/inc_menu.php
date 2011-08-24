<?php

/**
 * SINEMALL 管理中心菜单数组    $Author: testyang $
 * $Id: inc_menu.php 14481 2008-04-18 11:23:01Z testyang $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
/*得到不同管理员的左侧菜单
* function admin_menu from lib_main.php
* db admin_user where menu_list
* db admin_action where parent_id = 0
*/
/*商品管理*/
if(admin_menu('goods'))
{
//	$modules['02_cat_and_goods']['06_goods_brand_list'] = 'brand.php?act=list';
	$modules['02_cat_and_goods']['03_category_list']    = 'category.php?act=list';
//	$modules['02_cat_and_goods']['05_comment_manage']   = 'comment_manage.php?act=list';
//	$modules['02_cat_and_goods']['01_goods_list']       = 'goods.php?act=list';         // 商品列表
//	$modules['02_cat_and_goods']['02_goods_add']        = 'goods.php?act=add';          // 添加商品
//	$modules['02_cat_and_goods']['16_goods_script']     = 'gen_goods_script.php?act=setup';
	
//	$modules['02_cat_and_goods']['09_topic']                = 'topic.php?act=list';
	
	//$modules['02_cat_and_goods']['11_goods_trash']      = 'goods.php?act=trash';        // 商品回收站
	//$modules['02_cat_and_goods']['11_goods_trash_tmp']      = 'goods.php?act=trash_tmp';        // 商品回收站
	//$modules['02_cat_and_goods']['13_batch_add']        = 'goods_batch.php?act=add';    // 商品批量上传
	//$modules['02_cat_and_goods']['15_batch_edit']       = 'goods_batch.php?act=select'; // 商品批量修改
	//$modules['02_cat_and_goods']['08_goods_type']       = 'goods_type.php?act=manage';
	//$modules['02_cat_and_goods']['12_batch_pic']        = 'picture_batch.php';
	//$modules['02_cat_and_goods']['17_tag_manage']       = 'tag_manage.php?act=list';

	//$modules['02_cat_and_goods']['50_virtual_card_list']   ='goods.php?act=list&extension_code=virtual_card';
	//$modules['02_cat_and_goods']['51_virtual_card_add']    ='goods.php?act=add&extension_code=virtual_card';
	//$modules['02_cat_and_goods']['52_virtual_card_change'] ='virtual_card.php?act=change';
	//$modules['02_cat_and_goods']['goods_auto'] ='goods_auto.php?act=list';

	//$modules['02_cat_and_goods']['14_goods_export'] = 'goods_export.php?act=goods_export';
	
}

/*资料管理*/
if(admin_menu('cms_manage'))
{
//	$modules['07_content']['03_article_list']           = 'article.php?act=list';
//	$modules['07_content']['02_articlecat_list']        = 'articlecat.php?act=list';
//	$modules['07_content']['05_idea_list']        		= 'idea.php?act=list';
//	$modules['07_content']['06_ideacat_list']        	= 'ideacat.php?act=list';
	
//	$modules['08_members']['08_unreply_msg']            = 'user_msg.php?act=list_all';
	
//	$modules['07_content']['04_success_cases']	        = 'article.php?act=list&cat_id=12';
//	$modules['07_content']['shop_help']                 = 'shophelp.php?act=list_cat';
//	$modules['07_content']['shop_info']                 = 'shopinfo.php?act=list';
//	$modules['07_content']['vote_list']                 = 'vote.php?act=list';
//	$modules['07_content']['article_auto'] ='article_auto.php?act=list';
}

/*会员管理*/
if(admin_menu('users_manage'))
{
//	$modules['08_members']['06_list_integrate']         = 'integrate.php?act=list';
//	$modules['08_members']['08_unreply_msg']            = 'user_msg.php?act=list_all';
	$modules['08_members']['05_user_rank_list']         = 'user_rank.php?act=list';
	$modules['08_members']['03_users_list']             = 'users.php?act=list';
	$modules['08_members']['04_users_add']              = 'users.php?act=add';
//	$modules['08_members']['09_user_account']           = 'user_account.php?act=list';
	
}

/*权限管理*/
if(admin_menu('priv_manage'))
{
	//$modules['10_priv_admin']['admin_logs']             = 'admin_logs.php?act=list';
	//$modules['10_priv_admin']['admin_list']             = 'privilege.php?act=list';
	//$modules['10_priv_admin']['agency_list']            = 'agency.php?act=list';
}


if(admin_menu('sys_manage')){
	/*促销管理
	$modules['05_banner']['ad_position']                = 'ad_position.php?act=list';
	$modules['05_banner']['ad_list']                    = 'ads.php?act=list';

	$modules['06_stats']['flow_stats']                  = 'flow_stats.php?act=view';
	$modules['06_stats']['searchengine_stats']          = 'searchengine_stats.php?act=view';
	$modules['06_stats']['z_clicks_stats']              = 'adsense.php?act=list';
	$modules['06_stats']['report_guest']                = 'guest_stats.php?act=list';
	$modules['06_stats']['report_order']                = 'order_stats.php?act=list';
	$modules['06_stats']['report_sell']                 = 'sale_general.php?act=list';
	$modules['06_stats']['sale_list']                   = 'sale_list.php?act=list';
	$modules['06_stats']['sell_stats']                  = 'sale_order.php?act=goods_num';
	$modules['06_stats']['report_users']                = 'users_order.php?act=order_num';
	$modules['06_stats']['visit_buy_per']               = 'visit_sold.php?act=list';

	$modules['11_system']['01_shop_config']             = 'shop_config.php?act=list_edit';
	
	$modules['11_system']['02_payment_list']            = 'payment.php?act=list';
	$modules['11_system']['03_shipping_list']           = 'shipping.php?act=list';
	*/	
	//$modules['12_template']['02_template_select']       = 'template.php?act=list';
	//$modules['12_template']['03_template_setup']        = 'template.php?act=setup';
	//$modules['12_template']['04_template_library']      = 'template.php?act=library';
}
/*	$modules['11_system']['01_index_config']            = 'index_config.php?act=edit';
	$modules['11_system']['04_mail_settings']           = 'shop_config.php?act=mail_settings';
	$modules['11_system']['05_area_list']               = 'area_manage.php?act=list';
	$modules['11_system']['06_plugins']                 = 'plugins.php?act=list';
	$modules['11_system']['07_cron_schcron']            = 'cron.php?act=list';
	$modules['11_system']['08_friendlink_list']         = 'friend_link.php?act=list';
	$modules['11_system']['sitemap']                    = 'sitemap.php';
	$modules['11_system']['check_file_priv']            = 'check_file_priv.php?act=check';
	$modules['11_system']['captcha_manage']             = 'captcha_manage.php?act=main';

	$modules['11_system']['flashplay']              = 'flashplay.php?act=list';
	$modules['11_system']['navigator']                    = 'navigator.php?act=list';



	$modules['12_template']['mail_template_manage']     = 'mail_template.php?act=list';
	$modules['12_template']['05_edit_languages']        = 'edit_languages.php?act=list';
	$modules['12_template']['06_template_backup']       = 'template.php?act=backup_setting';


	$modules['13_backup']['convert']                    = 'convert.php?act=main';
	$modules['13_backup']['02_db_manage']               = 'database.php?act=backup';
	$modules['13_backup']['03_db_optimize']             = 'database.php?act=optimize';
	$modules['13_backup']['05_synchronous']             = 'integrate.php?act=sync';
	$modules['13_backup']['04_sql_query']               = 'sql.php?act=main';

	
	//$modules['14_sms']['02_sms_my_info']                = 'sms.php?act=display_my_info';
	//$modules['14_sms']['03_sms_send']                   = 'sms.php?act=display_send_ui';
	//$modules['14_sms']['04_sms_charge']                 = 'sms.php?act=display_charge_ui';
	//$modules['14_sms']['05_sms_send_history']           = 'sms.php?act=display_send_history_ui';
	//$modules['14_sms']['06_sms_charge_history']         = 'sms.php?act=display_charge_history_ui';
	
	$modules['15_rec']['affiliate']         = 'affiliate.php?act=list';
	$modules['15_rec']['affiliate_ck']     = 'affiliate_ck.php?act=list';

}
*/
/*订单管理*/
if(admin_menu('order_manage')){
	//$modules['04_order']['06_undispose_booking']        = 'goods_booking.php?act=list_all';
	//$modules['04_order']['03_order_query']              = 'order.php?act=order_query';
	//$modules['04_order']['02_order_list']               = 'order.php?act=list';
	//$modules['04_order']['04_merge_order']              = 'order.php?act=merge';
	//$modules['04_order']['05_edit_order_print']         = 'order.php?act=templates';
	//$modules['04_order']['07_repay_application']        = 'repay.php?act=list_all';
	//$modules['04_order']['08_add_order']                = 'order.php?act=add';
}

/*促销管理
if(admin_menu('promotion')){
	//$modules['03_promotion']['04_bonustype_list']       = 'bonus.php?act=list';
	//$modules['03_promotion']['07_card_list']            = 'card.php?act=list';
	//$modules['03_promotion']['06_pack_list']            = 'pack.php?act=list';
	//$modules['03_promotion']['02_snatch_list']          = 'snatch.php?act=list';
	//$modules['03_promotion']['08_group_buy']            = 'group_buy.php?act=list';

	$modules['03_promotion']['10_auction']              = 'auction.php?act=list';
	$modules['03_promotion']['12_favourable']           = 'favourable.php?act=list';
	$modules['03_promotion']['13_wholesale']            = 'wholesale.php?act=list';
	$modules['03_promotion']['ebao_commend']            = 'ebao_commend.php?act=list';
	
}
*/
/*邮件管理
if(admin_menu('email')){
	$modules['16_email_manage']['email_list']         = 'email_list.php?act=list';
	//$modules['16_email_manage']['magazine_list']      = 'magazine_list.php?act=list';
	$modules['16_email_manage']['attention_list']     = 'attention_list.php?act=list';
	//$modules['16_email_manage']['view_sendlist']     = 'view_sendlist.php?act=list';
	
}
*/
/*库存管理
if(admin_menu('inventory')){
	$modules['17_inventory']['01_inventory_list']             	= 'inventory.php?act=list';
	$modules['17_inventory']['02_inspect_part_number']          = 'inventory.php?act=inspect_part_number';
	$modules['17_inventory']['03_batch_inventory']           	= 'inventory.php?act=batch_operate';
	$modules['17_inventory']['04_out_of_date_inventory']        = 'inventory.php?act=list';
	$modules['17_inventory']['05_inventory_status']           	= 'inventory.php?act=list_status';
	$modules['17_inventory']['06_inventory_log']            	= 'inventory.php?act=list_log';
	$modules['17_inventory']['07_inventory_status_accounting'] 	= 'inventory.php?act=status_accounting';
	
}
*/
/*库存管理
if(admin_menu('inventory')){
	$modules['18_solution_order']['01_solution_step_list']    = 'solution.php?act=list_step';
	$modules['18_solution_order']['02_solution_list']         = 'solution.php?act=list';
	$modules['18_solution_order']['03_congif_order_list']     = 'solution_order.php?act=list';
//	$modules['18_solution_order']['04_saler_list']            = 'solution_order.php?act=sale_list';
//	$modules['18_solution_order']['05_customer_list']         = 'solution_order.php?act=custom_list';
	$modules['18_solution_order']['06_config_order_log']      = 'solution_order.php?act=list_log';
	
}
*/
/*培训管理
if(admin_menu('training')){
	$modules['19_training']['01_event_list']    		= 'training.php?act=list_event';
	$modules['19_training']['02_course_list']         	= 'training.php?act=list_course';
	$modules['19_training']['03_course_cat_list']    	= 'training.php?act=list_course_cat'; //课程分类
	
	$modules['19_training']['05_lesson_list']    	 	= 'training.php?act=list_lesson';
	
	$modules['19_training']['06_video_list']          	= 'training_o.php?act=list_video';
	$modules['19_training']['07_certificate_list']      = 'training.php?act=list_certificate';
	$modules['19_training']['08_level_list']    		= 'training.php?act=list_level'; //course or event level
	
	
	$modules['19_training']['10_teacher_list']          = 'training.php?act=list_teacher';
	$modules['19_training']['11_student_list']          = 'training.php?act=list_student';
	$modules['19_training']['12_message_list']         	= 'training.php?act=list_message';
	$modules['19_training']['13_rank_list']         	= 'training.php?act=list_rank';
	$modules['19_training']['14_location_list']      	= 'training.php?act=list_location';
//	$modules['19_training']['06_config_order_log']      = 'solution_order.php?act=list_log';
	
}
*/


?>