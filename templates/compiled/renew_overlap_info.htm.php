<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<div style="border:5px solid #6AB30F;float:left;padding:10px;background:#D2FC89;">
	<span class="red-block f_left" style="width:120px;">续签广告牌返款计算</span>  
<?php $_from = $this->_var['renew_fee_note']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_93798900_1328796707');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_93798900_1328796707']):
?>
<div class="city_info radius_5px">
	<div class="f_left left_title left_radius_5px" style="width:400px;"><?php echo $this->_var['item_0_93798900_1328796707']; ?></div>
	<div class="f_left right_content" style="width:200px;"><?php echo $this->_var['overlap_info'][$this->_var['k']]; ?> 元</div>	
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

</div>