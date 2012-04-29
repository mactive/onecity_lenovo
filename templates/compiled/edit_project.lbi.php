<form method="post" action="city_project.php" name="theForm" enctype="multipart/form-data">
<?php $_from = $this->_var['project_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_45932600_1334456984');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_45932600_1334456984']):
?>
<div class="city_info radius_5px">
	<div class="f_left left_title left_radius_5px"><?php echo $this->_var['lang'][$this->_var['key']]; ?></div>
	<div class="f_left right_content">
			<?php if ($this->_var['key'] == "6A_excel" || $this->_var['key'] == "6B_excel"): ?>
			<input type="file" name="user_upload_file[]"> &nbsp; <?php echo $this->_var['lang']['item_desc'][$this->_var['key']]; ?>
				<?php if ($this->_var['act_step'] == "edit_project"): ?><input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['project_info'][$this->_var['key']]; ?>"><?php endif; ?>
			<?php else: ?>
				<?php if ($this->_var['key'] == project_id): ?>
					<span><?php echo $this->_var['item_0_45932600_1334456984']['project_id']; ?></span>
				<?php else: ?>
				<input type="text" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['project_info'][$this->_var['key']]; ?>" size="30" /> &nbsp; <?php echo $this->_var['lang']['item_desc'][$this->_var['key']]; ?>
				
				<?php endif; ?>
			<?php endif; ?>
	</div>
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

<div style="width:500px;float:left;">
	<input type="hidden" name="act" value="update_project" />
	<input type="hidden" name="project_id" value="<?php echo $this->_var['project_info']['project_id']; ?>" />
	<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
</div>

</form>
