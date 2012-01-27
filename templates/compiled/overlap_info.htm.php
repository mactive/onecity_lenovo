<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<div style="border:5px solid #ff9ccb;float:left;padding:10px;background:#fbd9e9;">
	<span class="red-block f_left" style="width:100px;">新牌子返款计算</span>  
	<a href="city_operate.php?act=view_ad&ad_id=<?php echo $this->_var['another_ad_id']; ?>" target="_blank" class="f_right" style="margin-right:100px;"><span>查看另一块牌子</span></a>
<?php $_from = $this->_var['publish_fee_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_23889500_1327647826');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_23889500_1327647826']):
?>
<div class="city_info radius_5px">
	<div class="f_left left_title left_radius_5px" style="width:400px;"><?php echo $this->_var['item_0_23889500_1327647826']; ?></div>
	<div class="f_left right_content" style="width:200px;"><?php echo $this->_var['overlap_info'][$this->_var['k']]; ?></div>	
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

</div>