<meta http-equiv="Content-Type" content="text/html; charset=utf-8">


<div class="f_left" style="width:95%;background:#fafafa;border:2px solid #666;padding:20px;margin:10px 0px;">
	<div>之前的牌子审核纪录 牌子编号:<?php echo $this->_var['passed_ad_detail']['ad_sn']; ?></div>
	<div style="width:85%;" class="f_left">
	<?php $_from = $this->_var['passed_photo_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_47189900_1324614106');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_47189900_1324614106']):
?>
		<div style="width:160px;height:160px;text-align:center;float:left;margin:10px;">
		<a href="<?php echo $this->_var['item_0_47189900_1324614106']['img_url']; ?>" target="_blank" class="city_photo"><img src="<?php echo $this->_var['item_0_47189900_1324614106']['thumb_url']; ?>"></a>
		<?php echo $this->_var['lang']['city_photo'][$this->_var['item_0_47189900_1324614106']['img_sort']]; ?>
		</div>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>
	<div><hr></div>
	
	<div style="width:85%;" class="f_left">
	
	<?php if ($this->_var['hace_a_day']): ?>
<?php $_from = $this->_var['passed_audit_path']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'level');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['level']):
?>
<div class="audit_board radius_5px">
	<div class="audit_level_title"><?php echo $this->_var['lang']['audit_level'][$this->_var['key']]; ?><span class="f_right">->> &nbsp;&nbsp;</span></div>
	<div></div>
	<div class="audit_level_content">
		<?php $_from = $this->_var['level']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_47237700_1324614106');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_47237700_1324614106']):
?>
		<div class="audit_record">
		<span class="grey999"><?php echo $this->_var['item_0_47237700_1324614106']['time']; ?></span><br />
		<span class="grey666"><?php echo $this->_var['item_0_47237700_1324614106']['real_name']; ?></span><br />
		<div style="padding:0px 5px;line-height:20px;" <?php if ($this->_var['item_0_47237700_1324614106']['audit_note'] == "审核通过"): ?>class="bg_green radius_5px"<?php else: ?>class="bg_red radius_5px"<?php endif; ?>>
			<?php echo $this->_var['item_0_47237700_1324614106']['audit_note']; ?>
		</div>
		</div>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>
		
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php endif; ?>


</div>
</div>