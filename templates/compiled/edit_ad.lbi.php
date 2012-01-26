<div class="f_left">
	<a class="back_url" href="city_operate.php?act=city_ad_list&city_id=<?php echo $this->_var['ad_info']['city_id']; ?>"></a>
</div>
<div class="yellow_notice" style="text-align:center;"><?php echo $this->_var['upload_message']; ?></div>
<?php if ($this->_var['ad_info']['is_audit_confirm'] == 0): ?>
	<form method="post" action="city_operate.php" name="theForm" enctype="multipart/form-data">
	<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_22187100_1327560291');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_22187100_1327560291']):
?>
	<div class="city_info radius_5px">
		<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_22187100_1327560291']; ?></div>
		<div class="f_left right_content">
			<?php if ($this->_var['k'] == "col_1" || $this->_var['k'] == "col_2" || $this->_var['k'] == "col_3" || $this->_var['k'] == "col_4" || $this->_var['k'] == "col_5"): ?>
				<span><?php echo $this->_var['ad_detail'][$this->_var['k']]; ?></span>
				<input type="hidden" name="col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" />
			<?php else: ?>
				<input type="text" name="col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="50" />
			<?php endif; ?>
		</div>	
		<div class="clear"></div>
	</div>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<div style="width:500px;float:left;">
		<input type="hidden" name="act" value="update_ad" />
		<input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_detail']['ad_id']; ?>" />						
		<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
	</div>
	</form>
<?php endif; ?>

<?php if ($this->_var['ad_info']['is_audit_confirm'] == 1 && $this->_var['ad_info']['audit_status'] < 3 && $this->_var['sm_session']['user_rank'] == 1): ?>
<form method="post" action="city_operate.php" name="theForm" enctype="multipart/form-data">
<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_22252100_1327560291');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_22252100_1327560291']):
?>
<div class="city_info radius_5px">
	<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_22252100_1327560291']; ?></div>
	<div class="f_left right_content">
		<?php if ($this->_var['k'] == "col_19" || $this->_var['k'] == "col_20" || $this->_var['k'] == "col_21" || $this->_var['k'] == "col_22" || $this->_var['k'] == "col_24" || $this->_var['k'] == "col_25" || $this->_var['k'] == "col_26" || $this->_var['k'] == "col_27" || $this->_var['k'] == "col_28" || $this->_var['k'] == "col_29" || $this->_var['k'] == "col_30" || $this->_var['k'] == "col_31" || $this->_var['k'] == "col_32" || $this->_var['k'] == "col_33" || $this->_var['k'] == "col_34" || $this->_var['k'] == "col_35" || $this->_var['k'] == "col_36" || $this->_var['k'] == "col_37" || $this->_var['k'] == "col_38" || $this->_var['k'] == "col_39" || $this->_var['k'] == "col_40" || $this->_var['k'] == "col_41"): ?>
			<input type="text" name="col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="50" />
		<?php else: ?>
			<span><?php echo $this->_var['ad_detail'][$this->_var['k']]; ?></span>
			<input type="hidden" name="col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" />
		<?php endif; ?>
	</div>	
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<div style="width:500px;float:left;">
	<input type="hidden" name="act" value="update_ad" />
	<input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_detail']['ad_id']; ?>" />						
	<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
</div>
</form>
<?php endif; ?>

