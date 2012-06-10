<head>
</head>

<div class="header_city">
	<div class="login_title"><a href="index.php" style="width:250px;height:40px;display:block;"></a>
	</div>
	<div class="year_title">
		<a href="city_intro.php?act=switch_year&year=2011&url=<?php echo $this->_var['PHP_URL']; ?>" class="btn <?php if ($this->_var['year'] == 2011): ?>disabled<?php else: ?>btn<?php endif; ?>">
			<?php if ($this->_var['year'] == 2011): ?>当前<?php endif; ?>[FY2011 财年] </a>
		<a href="city_intro.php?act=switch_year&year=2012&url=<?php echo $this->_var['PHP_URL']; ?>" class="btn <?php if ($this->_var['year'] == 2012): ?>disabled<?php else: ?>btn<?php endif; ?>">
			<?php if ($this->_var['year'] == 2012): ?>当前<?php endif; ?>[FY2012 财年] </a>
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
