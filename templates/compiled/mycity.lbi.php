<div class="nav_top_red">
    <span>新牌子管理和操作</span>
    <div class="bgr"> </div>
</div>
<div>
  <ul class="mycity_ul">
    <li><a href="city_operate.php?has_new=1" style="background:url(<?php echo $this->_var['img_path']; ?>ico/flagged.png) no-repeat 10px center;">查看城市</a></li>
	<li><a href="city_base_info.php?act=ad_list&project_id=9&has_new=1" style="background:url(<?php echo $this->_var['img_path']; ?>ico/my_task.png) no-repeat 10px center;">基础信息修改</a></li>
	<li><a href="city_project.php?act=new_adbrand&has_new=1" style="background:url(<?php echo $this->_var['img_path']; ?>red_arrow.png) no-repeat 10px center;">项目管理</a></li>
	<?php if ($this->_var['sm_session']['user_rank'] >= 4 || $this->_var['sm_session']['user_rank'] == 2): ?>
	<li><a href="city_querenlv.php?act=new_querenlv" style="background:url(<?php echo $this->_var['img_path']; ?>blue_arrow.png) no-repeat 10px center;">确认率实时统计</a></li>
	<li><a href="city_querenlv.php?act=new_base_info_querenlv" style="background:url(<?php echo $this->_var['img_path']; ?>blue_arrow.png) no-repeat 10px center;">基础信息确认率</a></li>
	<li><a href="city_querenlv.php?act=new_project_querenlv" style="background:url(<?php echo $this->_var['img_path']; ?>blue_arrow.png) no-repeat 10px center;">换画反馈确认率</a></li>
	<?php endif; ?>
  </ul>
</div>
<div class="height_5px" style="background:#cb5a42;"></div>
<div class="left_menu" style="height:<?php if ($this->_var['act_step'] == "upload_file"): ?>200<?php else: ?>600<?php endif; ?>px;">
<div class="nav_top_lite_grey">
    <span>广告位管理</span>
    <div class="bgr"> </div>
</div>
<div>
  <ul class="mycity_ul">
	<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
    <li <?php if ($this->_var['act_step'] == "upload_panel"): ?>class="selected"<?php endif; ?>>
		<a href="city_operate.php?act=upload_panel" style="background:url(<?php echo $this->_var['img_path']; ?>ico/inbox.png) no-repeat 10px center;">上传数据</a></li>
	<?php endif; ?>
    <li <?php if ($this->_var['act_step'] == "show"): ?>class="selected"<?php endif; ?>>
		<a href="city_operate.php?has_new=3" style="background:url(<?php echo $this->_var['img_path']; ?>ico/flagged.png) no-repeat 10px center;">查看城市</a></li>
	<li><a href="city_operate.php?act=export_page" style="background:url(<?php echo $this->_var['img_path']; ?>green_arrow.png) no-repeat 10px center;">导出报表</a></li>
	<li><a href="download/FY12_model.rar" style="background:url(<?php echo $this->_var['img_path']; ?>ico/project.png) no-repeat 10px center;">模版下载</a></li>
	<?php if ($this->_var['sm_session']['user_rank'] >= 4): ?>
	<li><a href="city_querenlv.php?act=querenlv" style="background:url(<?php echo $this->_var['img_path']; ?>green_arrow.png) no-repeat 10px center;">确认率实时统计</a></li>
	<li><a href="city_querenlv.php?act=audit_status_summary" style="background:url(<?php echo $this->_var['img_path']; ?>green_arrow.png) no-repeat 10px center;">审核状态汇总表</a></li>
	
	<?php endif; ?>
  </ul>
</div>
<div class="nav_top_lite_grey">
    <span>基础信息</span>
    <div class="bgr"> </div>
</div>

<ul class="mycity_ul">
	<li><a href="city_base_info.php?act=ad_list&project_id=9" style="background:url(<?php echo $this->_var['img_path']; ?>ico/my_task.png) no-repeat 10px center;">基础信息修改</a></li>
	<?php if ($this->_var['sm_session']['user_rank'] >= 4 || $this->_var['sm_session']['user_rank'] == 2): ?>
	<li><a href="city_querenlv.php?act=base_info_querenlv" style="background:url(<?php echo $this->_var['img_path']; ?>blue_arrow.png) no-repeat 10px center;">基础信息确认率</a></li>
	<?php endif; ?>
	<?php if ($this->_var['sm_session']['user_rank'] >= 4 || $this->_var['sm_session']['user_id'] == 54): ?>
	<li><a href="city_base_info.php?act=city_ad_audit" style="background:url(<?php echo $this->_var['img_path']; ?>green_arrow.png) no-repeat 10px center;">电通工作统计表</a></li>
	<?php endif; ?>
</ul>
<div class="nav_top_lite_grey">
    <span>换画管理</span>
    <div class="bgr"> </div>
</div>
<ul class="mycity_ul">
	<li><a href="city_project.php" style="background:url(<?php echo $this->_var['img_path']; ?>red_arrow.png) no-repeat 10px center;">项目管理</a></li>
	<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
	<li><a href="city_project.php?act=project_picture" style="background:url(<?php echo $this->_var['img_path']; ?>green_arrow.png) no-repeat 10px center;">广告大图</a></li>
	<?php endif; ?>
	<li><a href="download/project_help.rar" style="background:url(<?php echo $this->_var['img_path']; ?>green_arrow.png) no-repeat 10px center;">项目管理使用说明</a></li>
	<?php if ($this->_var['sm_session']['user_rank'] >= 4): ?>
	<li><a href="city_querenlv.php?act=project_querenlv" style="background:url(<?php echo $this->_var['img_path']; ?>blue_arrow.png) no-repeat 10px center;">换画反馈确认率</a></li>
	<?php endif; ?>
</ul>

<div class="nav_top_lite_grey">
    <span>操作权限</span>
    <div class="bgr"> </div>
</div>
<ul class="mycity_ul">
	<?php $_from = $this->_var['user_permission']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
	<li><?php echo $this->_var['item']; ?></li>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</ul>

<div class="nav_top_lite_grey">
    <span>系统管理</span>
    <div class="bgr"> </div>
</div>
<div>
  <ul class="mycity_ul">
	<li><a href="download/help.rar" style="background:url(<?php echo $this->_var['img_path']; ?>ico/my_task.png) no-repeat 10px center;">系统使用说明</a></li>	
	<li><a href="user.php?act=profile" style="background:url(<?php echo $this->_var['img_path']; ?>ico/delegate.png) no-repeat 10px center;">个人密码资料</a></li>
    <li><a href="user.php?act=logout" style="background:url(<?php echo $this->_var['img_path']; ?>ico/delegated.png) no-repeat 10px center;">退出</a></li>
  </ul>
</div>

<div class="nav_bot"></div>
</div>




