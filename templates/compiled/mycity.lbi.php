<div class="nav_top_red">
    <span>FY12续签修改</span>
    <div class="bgr"> </div>
</div>
<div>
  <ul class="mycity_ul">
    <li <?php if ($this->_var['PHP_SELF'] == "city_renew"): ?>class="selected"<?php endif; ?>>
<a href="city_renew.php" style="background:url(<?php echo $this->_var['img_path']; ?>ico/flagged.png) no-repeat 10px center;">FY12修改续签牌子</a></li>
  <?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
    <li <?php if ($this->_var['act_step'] == "upload_panel"): ?>class="selected"<?php endif; ?>>
    <a href="city_operate.php?act=upload_panel" style="background:url(<?php echo $this->_var['img_path']; ?>ico/inbox.png) no-repeat 10px center;">上传数据</a></li>
  <?php endif; ?>
    <li <?php if ($this->_var['PHP_SELF'] == "city_operate" && $this->_var['act_step'] == "show"): ?>class="selected"<?php endif; ?>>
    <a href="city_operate.php" style="background:url(<?php echo $this->_var['img_path']; ?>audit_status_bg.png) no-repeat 8px 4px;">FY12新上传牌子</a></li>
  <li><a href="download/FY12_model.rar" style="background:url(<?php echo $this->_var['img_path']; ?>ico/project.png) no-repeat 10px center;">模版下载</a></li>
  </ul>
</div>
<div class="height_5px" style="background:#cb5a42;"></div>
<div class="left_menu" style="height:<?php if ($this->_var['act_step'] == "upload_file"): ?>200<?php else: ?>600<?php endif; ?>px;">

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
	<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
	<div class="height_5px" style="background:#cb5a42;"></div>	
	<li><a href="city_dealer.php" style="background:url(<?php echo $this->_var['img_path']; ?>green_arrow.png) no-repeat 10px center;">渠道管理</a></li>
	<?php endif; ?>
	<li><a href="download/help.rar" style="background:url(<?php echo $this->_var['img_path']; ?>ico/my_task.png) no-repeat 10px center;">系统使用说明</a></li>	
	<li><a href="user.php?act=profile" style="background:url(<?php echo $this->_var['img_path']; ?>ico/delegate.png) no-repeat 10px center;">个人密码资料</a></li>
    <li><a href="user.php?act=logout" style="background:url(<?php echo $this->_var['img_path']; ?>ico/delegated.png) no-repeat 10px center;">退出</a></li>
  </ul>
</div>

<div class="nav_bot"></div>
</div>




