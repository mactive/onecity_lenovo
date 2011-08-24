<?php

/**
 * SINEMALL 管理中心會員數據整合插件管理程序語言文件   * $Author: dolphin $
 * $Id: integrate.php 14547 2008-05-04 03:09:19Z dolphin $
*/

$_LANG['integrate_name'] = '名稱';
$_LANG['integrate_version'] = '版本';
$_LANG['integrate_author'] = '作者';

/* 插件列表 */
$_LANG['update_success'] = '設置會員數據整合插件已經成功。';
$_LANG['install_confirm'] = '您確定要安裝該會員數據整合插件嗎？';
$_LANG['need_not_setup'] = '當您採用ECSHOP會員系統時，無須進行設置。';
$_LANG['different_domain'] = '您設置的整合對像和 ECSHOP 不在同一域下。<br />您將只能共享該系統的會員數據，但無法實現同時登錄。';
$_LANG['points_set'] = '積分兌換設置';
$_LANG['view_user_list'] = '查看論壇用戶';
$_LANG['view_install_log'] = '查看安裝日誌';

$_LANG['integrate_setup'] = '設置會員數據整合插件';
$_LANG['continue_sync'] = '繼續同步會員數據';
$_LANG['go_userslist'] = '返回會員帳號列表';

/* 查看安裝日誌 */
$_LANG['lost_install_log'] = '未找到安裝日誌';
$_LANG['empty_install_log'] = '安裝日誌為空';

/* 表單相關語言項 */
$_LANG['db_notice'] = '點擊「<font color="#000000">下一步</font>」將引導你到將商城用戶數據同步到整合論壇。如果不需同步數據請點擊「<font color="#000000">直接保存配置信息</font>」';

$_LANG['lable_db_host'] = '數據庫服務器主機名：';
$_LANG['lable_db_name'] = '數據庫名：';
$_LANG['lable_db_chartset'] = '數據庫字符集：';
$_LANG['lable_is_latin1'] = '是否為latin1編碼';
$_LANG['lable_db_user'] = '數據庫帳號：';
$_LANG['lable_db_pass'] = '數據庫密碼：';
$_LANG['lable_prefix'] = '數據表前綴：';
$_LANG['lable_url'] = '被整合系統的完整 URL：';
/* 表單相關語言項(discus5x) */
$_LANG['cookie_prefix']          = 'COOKIE前綴：';
$_LANG['cookie_salt']          = 'COOKIE加密串：';
$_LANG['button_next'] = '下一步';
$_LANG['button_force_save_config'] = '直接保存配置信息';
$_LANG['save_confirm'] = '您確定要直接保存配置信息嗎？';
$_LANG['button_save_config'] = '保存配置信息';

$_LANG['error_db_msg'] = '數據庫地址、用戶或密碼不正確';
$_LANG['error_db_exist'] = '數據庫不存在';
$_LANG['error_table_exist'] = '整合論壇關鍵數據表不存在，你填寫的信息有誤';

$_LANG['notice_latin1'] = '該選項填寫錯誤時將可能到導致中文用戶名無法使用';
$_LANG['error_not_latin1'] = '整合數據庫檢測到不是latin1編碼！請重新選擇';
$_LANG['error_is_latin1'] = '整合數據庫檢測到是lantin1編碼！請重新選擇';
$_LANG['invalid_db_charset'] = '整合數據庫檢測到是%s 字符集，而非%s 字符集';
$_LANG['error_latin1'] = '你填寫的整合信息會導致嚴重錯誤，無法完成整合';

/* 檢查同名用戶 */
$_LANG['conflict_username_check'] = '檢查商城用戶是否和整合論壇用戶有重名';
$_LANG['check_notice'] = '本頁將檢測商城已有用戶和論壇用戶是否有重名，點擊「開始檢查前」，請為商城重名用戶選擇一個默認處理方法';
$_LANG['default_method'] = '如果檢測出商城有重名用戶，請為這些用戶選擇一個默認處理方法';
$_LANG['shop_user_total'] = '商城共有 %s 個用戶待檢查';
$_LANG['lable_size'] = '每次檢查用戶個數';
$_LANG['start_check'] = '開始檢查';
$_LANG['next'] = '下一步';
$_LANG['checking'] = '正在檢查...(請不要關閉瀏覽器)';
$_LANG['notice'] = '已經檢查 %s / %s ';
$_LANG['check_complete'] = '檢查完成';

/* 同名用戶處理 */
$_LANG['conflict_username_modify'] = '商城重名用戶列表';
$_LANG['modify_notice'] = '以下列出了所有商城與論壇的重名用戶及處理方法。如果您已確認所有操作，請點擊「開始整合」；您對重名用戶的操作的更改需要點擊按鈕「保存本頁更改」才能生效。';
$_LANG['page_default_method'] = '本頁面中重名用戶默認處理方法';
$_LANG['lable_rename'] = '商城重名用戶加後綴';
$_LANG['lable_delete'] = '刪除商城的重名用戶及相關數據';
$_LANG['lable_ignore'] = '保留商城重名用戶，論壇同名用戶視為同一用戶';
$_LANG['short_rename'] = '商城用戶改名為';
$_LANG['short_delete'] = '刪除商城用戶';
$_LANG['short_ignore'] = '保留商城用戶';
$_LANG['user_name'] = '商城用戶名';
$_LANG['email'] = 'email';
$_LANG['reg_date'] = '註冊日期';
$_LANG['all_user'] = '所有商城重名用戶';
$_LANG['error_user'] = '需要重新選擇操作的商城用戶';
$_LANG['rename_user'] = '需要改名的商城用戶';
$_LANG['delete_user'] = '需要刪除的商城用戶';
$_LANG['ignore_user'] = '需要保留的商城用戶';

$_LANG['submit_modify'] = '保存本頁變更';
$_LANG['button_confirm_next'] = '開始整合';


/* 用戶同步 */
$_LANG['user_sync'] = '同步商城數據到論壇，並完成整合';
$_LANG['button_pre'] = '上一步';
$_LANG['task_name'] = '任務名';
$_LANG['task_status'] = '任務狀態';
$_LANG['task_del'] = '%s 個商城用戶數待刪除';
$_LANG['task_rename'] = '%s 個商城用戶需要改名';
$_LANG['task_sync'] = '%s 個商城用戶需要同步到論壇';
$_LANG['task_save'] = '保存配置信息，並完成整合';
$_LANG['task_uncomplete'] = '未完成';
$_LANG['task_run'] = '執行中 (%s / %s)';
$_LANG['task_complete'] = '已完成';
$_LANG['start_task'] = '開始任務';
$_LANG['sync_status'] = '已經同步 %s / %s';
$_LANG['sync_size'] = '每次處理用戶數量';
$_LANG['sync_ok'] = '恭喜您。整合成功';


$_LANG['save_ok'] = '保存成功';

/* 積分設置 */
$_LANG['no_points'] = '沒有檢測到論壇有可以兌換的積分';
$_LANG['bbs'] = '論壇';
$_LANG['shop_pay_points'] = '商城消費積分';
$_LANG['shop_rank_points'] = '商城等級積分';
$_LANG['add_rule'] = '新增規則';
$_LANG['modify'] = '修改';
$_LANG['rule_name'] = '兌換規則';
$_LANG['rule_rate'] = '兌換比例';

/* JS語言項 */
$_LANG['js_languages']['no_host'] = '數據庫服務器主機名不能為空。';
$_LANG['js_languages']['no_user'] = '數據庫帳號不能為空。';
$_LANG['js_languages']['no_name'] = '數據庫名不能為空。';
$_LANG['js_languages']['no_integrate_url'] = '請輸入整合對象的完整 URL';
$_LANG['js_languages']['install_confirm'] = '請不要在系統運行中隨意的更換整合對象。\r\n您確定要安裝該會員數據整合插件嗎？';
$_LANG['js_languages']['num_invalid'] = '同步數據的記錄數不是一個整數';
$_LANG['js_languages']['start_invalid'] = '同步數據的起始位置不是一個整數';
$_LANG['js_languages']['sync_confirm'] = '同步會員數據會將目標數據表重建。請在執行同步之前備份好您的數據。\r\n您確定要開始同步會員數據嗎？';

$_LANG['cookie_prefix_notice'] = 'UTF8版本的cookie前綴默認為xnW_，GB2312/GBK版本的cookie前綴默認為KD9_。';

$_LANG['js_languages']['no_method'] = '請選擇一種默認處理方法';

$_LANG['js_languages']['rate_not_null'] = '比例不能為空';
$_LANG['js_languages']['rate_not_int'] = '比例只能填整數';
$_LANG['js_languages']['rate_invailed'] = '你填寫了一個無效的比例';

?>