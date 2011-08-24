<div style="margin-top:20px;" class="font20px"><?php echo $this->_var['lang']['city_title'][$this->_var['col_name']]; ?></div>

<div class="city_info radius_5px">
	<div class="f_left left_title left_radius_5px">原值</div>
	<div class="f_left right_content">修改值</div>
</div>
<?php $_from = $this->_var['log_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_42824000_1314189891');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_42824000_1314189891']):
?>
<div class="city_info radius_5px">
	<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_42824000_1314189891']['old_value']; ?></div>
	<div class="f_left right_content">
		<?php echo $this->_var['item_0_42824000_1314189891']['value']; ?>
		<span class="f_right grey999" style="margin-right:20px;"><?php echo $this->_var['item_0_42824000_1314189891']['time']; ?></span>
		
	</div>	
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>


