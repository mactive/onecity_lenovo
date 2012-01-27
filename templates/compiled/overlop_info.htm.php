<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<div style="border:5px solid #ccc;float:left;padding:10px;">
	<?php echo $this->_var['overlop_info']['fee_1']; ?>dd
<?php $_from = $this->_var['publish_fee_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_85893000_1327646884');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_85893000_1327646884']):
?>
<div class="city_info radius_5px">
	<div class="f_left left_title left_radius_5px" style="width:400px;"><?php echo $this->_var['item_0_85893000_1327646884']; ?></div>
	<div class="f_left right_content"><?php echo $this->_var['overlop_info'][$this->_var['k']]; ?></div>	
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

</div>