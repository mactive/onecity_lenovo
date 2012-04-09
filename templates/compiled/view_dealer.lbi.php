<div class="f_left">
	<a class="back_url" href="city_dealer.php?region_name=<?php echo $this->_var['dealer_info']['region_name']; ?>"></a>	
</div>
<div class="yellow_notice" style="text-align:center;"><?php echo $this->_var['upload_message']; ?>
	<a class="font20px" href="city_dealer.php?act=edit_dealer&dealer_id=<?php echo $this->_var['dealer_info']['dealer_id']; ?>">修改渠道信息</a>
	</div>
	
	<form method="post" action="city_operate.php" name="theForm" enctype="multipart/form-data">
	<?php $_from = $this->_var['dealer_info_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_29951400_1332489144');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_29951400_1332489144']):
?>
	<div class="city_info radius_5px">
		<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_29951400_1332489144']; ?></div>
		<div class="f_left right_content">
			<?php if ($this->_var['k'] == "is_dealer"): ?>
				<?php $_from = $this->_var['lang']['is_dealer']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'i');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['i']):
?><?php if ($this->_var['key'] == $this->_var['dealer_info'][$this->_var['k']]): ?><?php echo $this->_var['i']; ?><?php endif; ?><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			<?php elseif ($this->_var['k'] == "is_audit"): ?>
				<?php if ($this->_var['dealer_info'][$this->_var['k']]): ?>是<?php else: ?>否<?php endif; ?>
			<?php else: ?>
				<?php echo $this->_var['dealer_info'][$this->_var['k']]; ?>
			<?php endif; ?>
		</div>	
		<div class="clear"></div>
	</div>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

	</form>
	


