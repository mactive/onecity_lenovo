<div class="f_left">
	<a class="back_url" href="city_dealer.php"></a>	
</div>
<div class="yellow_notice" style="text-align:center;"><?php echo $this->_var['upload_message']; ?></div>
	<form method="post" action="city_dealer.php" name="theForm" enctype="multipart/form-data">
	<?php $_from = $this->_var['dealer_info_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_86068100_1332489719');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_86068100_1332489719']):
?>
	<div class="city_info radius_5px">
		<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_86068100_1332489719']; ?></div>
		<div class="f_left right_content">
			<?php if ($this->_var['k'] == "region_id" || $this->_var['k'] == "region_name"): ?>
			<?php echo $this->_var['dealer_info'][$this->_var['k']]; ?>
			<?php else: ?>
			<input type="text" name="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['dealer_info'][$this->_var['k']]; ?>" size="50" />
			
			<?php endif; ?>
		</div>	
		<div class="clear"></div>
	</div>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<div style="width:500px;float:left;">
		<input type="hidden" name="act" value="update_dealer" />
		<input type="hidden" name="dealer_id" value="<?php echo $this->_var['dealer_info']['dealer_id']; ?>" />						
		<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
	</div>
	</form>


