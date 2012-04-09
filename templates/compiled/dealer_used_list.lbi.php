<div class="yellow_notice" style="text-align:center;">渠道信息使用详情</div>

<div class="f_left">
	<a class="back_url" href="city_dealer.php"></a>	
</div>
	<?php $_from = $this->_var['dealer_used_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_34062800_1333976419');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_34062800_1333976419']):
?>
	<div class="city_info radius_5px" style="height:60px;">
		<div class="f_left left_title left_radius_5px" style="width:100px;background:#cdcdcd;"><?php echo $this->_var['item_0_34062800_1333976419']['col_1']; ?></div>
		<div class="f_left left_title left_radius_5px" style="width:100px;"><a href="city_operate.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_34062800_1333976419']['ad_id']; ?>#area_col_43" target="_blank"><?php echo $this->_var['item_0_34062800_1333976419']['col_3']; ?></a></div>
		<div class="f_left right_content" style="width:600px;">
			<?php echo $this->_var['item_0_34062800_1333976419']['col_7']; ?>
		</div>	
		<div class="clear"></div>
	</div>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>



