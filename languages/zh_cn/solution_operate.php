<?php

/**
 * SINEMALL 步骤管理语言项 * $Author: testyang $
 * $Id: step.php 14481 2008-04-18 11:23:01Z testyang $
*/

$_LANG['add_step'] = '添加步骤';
$_LANG['step_name'] = '步骤名称';
$_LANG['step_desc'] = '步骤描述';
$_LANG['step_id'] = '步骤ID';
$_LANG['step_logo'] = '步骤图标';
$_LANG['step_goods'] = '步骤商品';
$_LANG['other_cat'] = '扩展分类';
$_LANG['is_real'] = '有否内容';
$_LANG['goods_name'] = '商品名称';
$_LANG['part_number'] = 'Part_number';
$_LANG['serial_number'] = 'Serial_number';
$_LANG['serial_number_exist'] = '您输入的Serial Number已经存在请重新输入';
$_LANG['serial_number_not_exist'] = '您输入的Serial Number 在库存中不存在';

$_LANG[''] = '';

$_LANG['add_solution'] = '添加方案';
$_LANG['solution_name'] = '方案名称';
$_LANG['solution_desc'] = '方案描述';
$_LANG['solution_id'] = '方案ID';
$_LANG['solution_logo'] = '方案图标';
$_LANG['parent_id'] = '父亲分类';
$_LANG['sort_step'] = '步骤排序';
$_LANG['step_type'] = '步骤类型';
$_LANG['sort_step_notice'] = '输入排序序号';
$_LANG[''] = '';
$_LANG[''] = '';
$_LANG['solution_list_step'] = '步骤列表';
$_LANG['solution_add_step'] = '添加步骤信息';
$_LANG['solution_update_step'] = '修改步骤信息';
$_LANG['solution_delete_step'] = '删除步骤信息';
$_LANG['remove_step_yn'] = '是否删除步骤信息？';

/*配单 语句*/
$_LANG['order_id'] = '配单号:';
$_LANG['order_name'] = '配单名:';
$_LANG['order_public'] = '公布情况:';
$_LANG['order_model'] = '发布标准情况:';
$_LANG['order_exe'] = '执行情况:';
$_LANG['order_count'] = '配单产品数:';
$_LANG['order_amount'] = '配单总金额:';
$_LANG['order_sub_total'] = '单项合计:';
$_LANG['order_note'] = '备注:';
$_LANG['order_user_name'] = '操作人:';
$_LANG['order_desc'] = '描述:';
$_LANG['order_tag'] = '标签:';
$_LANG['personal'] = '个人';
$_LANG['agency'] = '机构';
$_LANG['agency_id'] = '机构ID';
$_LANG['agency_name'] = '机构名称:';
$_LANG['agency_desc'] = '机构描述:';
$_LANG['bank_account'] = '银行帐号:';
$_LANG['bank_name'] = '开户行';
$_LANG['tax_number'] = '税号';
$_LANG['website'] = '网址';
$_LANG['contact_name'] = '联系人名称';
$_LANG['address'] = '地址邮编:';
$_LANG['copy_succeed'] = '复制成功.';
$_LANG['preview_order'] = '预览';
$_LANG['have_no_privilege_operate'] = '您无权操作此页面';
$_LANG['order_is_model'] = '已发布为标准配单';
$_LANG['order_isnot_model'] = '未发布为标准';
$_LANG['order_is_public'] = '已公布';
$_LANG['order_isnot_public'] = '已公布';
$_LANG['order_is_exe'] = '已执行';
$_LANG['order_isnot_exe'] = '未执行';
$_LANG['agency_info'] = '机构信息';
$_LANG['contact_info'] = '联系人信息';
$_LANG['contact_desc'] = '联系人描述';
$_LANG['relogin_lnk'] = '重新登录';

/* solution operate 配单操作*/
$_LANG['step_price'] = '单项价格：';
$_LANG['step_count'] = '单项数量：';
$_LANG['remove_step_yn'] = '是否删除步骤信息？';
$_LANG['trash_order_yn'] = '是否将订单信息放入回收站？';
$_LANG['model_order_yn'] = '是否将订单信息发布？发布之后为标配断,普通用户可见';
$_LANG['remove_order_yn'] = '是否彻底删除订单信息？';
$_LANG['model_order_yn'] = '将进行价格验证应提交，是否继续？';

/* supplier 供应商 */
$_LANG['supplier'] = '供应商';
$_LANG['supplier_id'] = '供应商ID';
$_LANG['supplier_name'] = '供应商名称';
$_LANG['supplier_info'] = '供应商信息';
$_LANG['supplier_desc'] = '供应商描述';
$_LANG['supplier_phone'] = '供应商电话';
$_LANG['supplier_address'] = '供应商地址';
$_LANG['supplier_contact_name'] = '联系人';

$_LANG['purchase_price'] = '采购价格:';
$_LANG['purchase'] = '采购';
$_LANG['purchase_count'] = '采购数量:';
$_LANG['need_purchase_count'] = '需要采购:';
$_LANG['can_use_count'] = '可用库存:';
$_LANG['purchase_time'] = '采购时间:';
$_LANG['purchase_status'] = '采购状态:';
$_LANG['pay_status'] = '付款状态:';
$_LANG['shipping_status'] = '运输状态:';
$_LANG['order_period'] = '货期:'; 

/* 采购状态*/
//$status_list.purchase_status
$_LANG['status_list']['purchase_status']['0'] = '未采购';
$_LANG['status_list']['purchase_status']['1'] = '已采购';
$_LANG['status_list']['purchase_status']['2'] = '已采购入库';
$_LANG['status_list']['purchase_status']['5'] = '部分采购';

//$status_list.shipping_status
$_LANG['status_list']['shipping_status']['0'] = '未发货';
$_LANG['status_list']['shipping_status']['1'] = '已发货';
$_LANG['status_list']['shipping_status']['2'] = '已收货';
$_LANG['status_list']['shipping_status']['5'] = '部分发货';
//$_LANG['status_list']['shipping_status']['3'] = '';

//$status_list.pay_status
$_LANG['status_list']['pay_status']['0'] = '未付款';
$_LANG['status_list']['pay_status']['1'] = '付款中';
$_LANG['status_list']['pay_status']['2'] = '已付款';
$_LANG['status_list']['pay_status']['5'] = '部分付款';
//$_LANG['status_list']['pay_status']['3'] = '';





$_LANG['is_best'] = '精品推荐';
$_LANG['is_new'] = '新品';
$_LANG['is_hot'] = '热销';
$_LANG['is_promote'] = '特价';
$_LANG['intro_type'] = '类型';
$_LANG['please_select'] = '请选择';
$_LANG['parent_cat'] = '顶级分类';
$_LANG['add_child'] = '添加子步骤';
$_LANG['drop_logo'] = '删除图标';

/*提示信息*/
$_LANG['solution_edit'] = '编辑方案记录';
$_LANG['upload_failure'] = '图片上传失败！';
$_LANG['solutionedit_fail'] = '方案 %s 修改失败！';
$_LANG['solutionadd_succeed'] = '新方案添加成功！';
$_LANG['solutionedit_succed'] = '方案 %s 修改成功！';
$_LANG['solutionname_exist'] = '方案 %s 已经存在！';
$_LANG[''] = '';
$_LANG[''] = '';


$_LANG['list_step'] = '步骤列表';
$_LANG['all_cat'] = '所有分类';
$_LANG['now_order_amount'] = '目前配单总金额:';
$_LANG['order_note'] = '备注：';
$_LANG['action_note'] = '说明：';
$_LANG['calc'] = '计算';

$_LANG['wire_fee'] = '线材费:';
$_LANG['tax_fee'] = '税点:';
$_LANG['travel_fee'] = '差旅费:';
$_LANG['training_fee'] = '培训费:';
$_LANG[''] = '';
$_LANG[''] = '';
$_LANG[''] = '';
$_LANG[''] = '';
$_LANG[''] = '';

$_LANG['js_languages']['step_name_empty'] = '请输入步骤名称!';
$_LANG['js_languages']['start_time_empty'] = '请选择开始时间!';
$_LANG['js_languages']['end_time_empty'] = '请选择结束时间!';
$_LANG['js_languages']['delete_topic_confirm'] = '确定删除选中项吗?';
$_LANG['js_languages']['sort_name_exist'] = '该分类已经存在';
$_LANG['js_languages']['sort_name_empty'] = '请输入分类名称';
$_LANG['js_languages']['confirm_shipping_status'] = '您是否确认发货？将会修改库存和库存记录';
$_LANG['js_languages']['not_assign_serial_number'] = '您没有指定serial_number';
$_LANG['js_languages']['move_item_confirm'] = '已选商品已经转移到\"className\"分类下';
$_LANG['js_languages']['item_upper_limit'] = '每个分类下的商品不能超过50个';
$_LANG['js_languages']['start_lt_end'] = '开始时间不能大于结束时间';
$_LANG['solutionedit_fail'] = '编辑失败';
$_LANG['all_solution'] = '全部展开';
$_LANG['list_solution'] = '方案列表';
$_LANG['add_solution'] = '添加方案';
$_LANG['edit_solution'] = '编辑方案';
$_LANG['list_one_solution'] = '方案详细';
$_LANG['add_child_step'] = '添加子步骤';
$_LANG['list_step'] = '步骤列表';



$_LANG['js_languages']['solution_name_empty'] = '请输入方案名称!';
$_LANG['detail'] = '详细';
$_LANG['count'] = '数量';
$_LANG['add_time'] = '添加时间:';
$_LANG['order_detail'] = '配单详细';
$_LANG[''] = '';
$_LANG[''] = '';



$_LANG['solution_id'] = '配单ID';
$_LANG['solution_name'] = '配单名称';
$_LANG['solution_logo'] = '配单logo';
$_LANG['solution_desc'] = '配单描述';
$_LANG['solution_list'] = '配单列表';
$_LANG['add_solution'] = '添加方案';
$_LANG['back_list'] = '返回列表';



$_LANG['sort_order'] = '排序';
$_LANG['is_show'] = '是否显示';
$_LANG['is_commend'] = '是否推荐';
$_LANG['drop_step_logo'] = '删除图标';
$_LANG['confirm_drop_logo'] = '你确认要删除该图标吗？';
$_LANG['drop_step_logo_success'] = '删除步骤logo成功';

$_LANG['step_edit_lnk'] = '重新编辑该步骤';
$_LANG['step_list_lnk'] = '返回列表页面';

/*帮助信息*/
$_LANG['up_steplogo'] = '请上传图片，做为步骤的LOGO！';
$_LANG['warn_steplogo'] = '你已经上传过图片。再次上传时将覆盖原图片！';

/*提示信息*/
$_LANG['step_edit'] = '编辑步骤记录';
$_LANG['upload_failure'] = '图片上传失败！';
$_LANG['stepedit_fail'] = '步骤 %s 修改失败！';
$_LANG['stepadd_succed'] = '新步骤添加成功！';
$_LANG['stepedit_succed'] = '步骤 %s 修改成功！';
$_LANG['stepname_exist'] = '步骤 %s 已经存在！';
$_LANG['drop_confirm'] = '你确认要删除选定的商品步骤吗？';
$_LANG['drop_succeed'] = '已成功删除！';
$_LANG['drop_fail'] = '删除失败！';

$_LANG['no_stepname'] = '您必须输入步骤名称！';
$_LANG['enter_int'] = '请输入一个整数！';

$_LANG['back_list'] = '返回步骤列表';
$_LANG['continue_add'] = '继续添加新步骤';

$_LANG['upfile_type_error'] = "只能上传jpg，gif，png类型的图片";
$_LANG['upfile_error'] = "图片无法上传，请确保data目录下所有子目录的可写性！";

$_LANG['visibility_notes'] = '选否表示改步骤下还没有商品,只是分类。';
$_LANG['commend_notes'] = '推荐的步骤会优先显示在首页。';

/*JS 语言项*/
$_LANG['js_languages']['no_stepname'] = '您必须输入步骤名称！';
$_LANG['js_languages']['require_num'] =  '排序序号必须是一个数字';

?>
