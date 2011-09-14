<head>
</head>

<div class="header_city">
	<div class="login_title">
	</div>
	<div class="menu_title">
		<?php if ($this->_var['sm_session']['user_rank'] > 0): ?>
			<div class="f_right"><a class="login_top_btn" href="user.php?act=logout">退出</a></div>
		<?php else: ?>
			<div class="f_right"><a class="" href="user.php?act=login">登录</a></div>
		<?php endif; ?>
		<div class="f_right" style="line-height:28px;color:#fff;margin-right:20px;margin-top:5px;">你好 <?php echo $this->_var['real_name']; ?> , 你的权限是 <?php echo $this->_var['your_user_rank']; ?> </div>
	</div>
	<div class="clear"></div>
</div>	
