<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<div class="f_left" style="width:100%;">
<?php $_from = $this->_var['audit_path']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'level');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['level']):
?>
<div class="audit_board radius_5px">
	<div class="audit_level_title"><?php echo $this->_var['lang']['audit_level'][$this->_var['key']]; ?><span class="f_right">->> &nbsp;&nbsp;</span></div>
	<div></div>
	<div class="audit_level_content">
		<?php $_from = $this->_var['level']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_48930600_1336280229');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_48930600_1336280229']):
?>
		<div class="audit_record">
		<span class="grey999"><?php echo $this->_var['item_0_48930600_1336280229']['time']; ?></span><br />
		<span class="grey666"><?php echo $this->_var['item_0_48930600_1336280229']['real_name']; ?></span><br />
		<div style="padding:0px 5px;line-height:20px;" <?php if ($this->_var['item_0_48930600_1336280229']['audit_note'] == "续签审核通过"): ?>class="bg_green radius_5px"<?php else: ?>class="bg_red radius_5px"<?php endif; ?>>
			<?php echo $this->_var['item_0_48930600_1336280229']['audit_note']; ?>
		</div>
		</div>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>
		
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>