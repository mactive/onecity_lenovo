<?php if ($this->_var['upload_message'] && $this->_var['is_change'] == 0): ?>

<div class="yellow_notice" style="text-align:center;"><?php echo $this->_var['upload_message']; ?></div>

<?php else: ?>

<div class="upload_table">
<div class="upload_board" style="height:auto;float:left;">
<form method="post" action="city_operate.php" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
<table width="70%" id="lesson-table" class="table_border table_standard" border="1">
    <tr>
      <td width="100"><?php echo $this->_var['lang']['county']; ?>:</td>
      <td><?php echo $this->_var['ad_info']['city_name']; ?></td>
    </tr>
	<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
    <tr>
      <td>上传照片:</td>
      <td><span class="green-color">请将单张照片大小控制在1M以内。</span></td>
    </tr>
	
	<tr>
		<td>近景1.</td>
		<td>
			<?php if ($this->_var['photo_info']): ?>
				<a href="<?php echo $this->_var['photo_info']['0']['img_url']; ?>" target="_blank" class="city_photo">
					<img src="<?php echo $this->_var['photo_info']['0']['thumb_url']; ?>">
				</a><?php echo $this->_var['reupload_message']; ?><input type="hidden" name="img_id[]" value="<?php echo $this->_var['photo_info']['0']['img_id']; ?>">
			<?php endif; ?>
			<?php if (! $this->_var['ad_info']['is_aduit_confirm']): ?><input type="file" name="idea_photo[]"><?php endif; ?>
		</td>
	</tr>	
	<tr>
		<td>近景2. </td>
		<td>
			<?php if ($this->_var['photo_info']): ?>
				<a href="<?php echo $this->_var['photo_info']['1']['img_url']; ?>" target="_blank" class="city_photo">
					<img src="<?php echo $this->_var['photo_info']['1']['thumb_url']; ?>">
				</a><?php echo $this->_var['reupload_message']; ?><input type="hidden" name="img_id[]" value="<?php echo $this->_var['photo_info']['1']['img_id']; ?>">
			<?php endif; ?>
			<?php if (! $this->_var['ad_info']['is_aduit_confirm']): ?><input type="file" name="idea_photo[]"><?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>远景1. </td>
		<td>
			<?php if ($this->_var['photo_info']): ?>
				<a href="<?php echo $this->_var['photo_info']['2']['img_url']; ?>" target="_blank" class="city_photo">
					<img src="<?php echo $this->_var['photo_info']['2']['thumb_url']; ?>">
				</a><?php echo $this->_var['reupload_message']; ?><input type="hidden" name="img_id[]" value="<?php echo $this->_var['photo_info']['2']['img_id']; ?>">
			<?php endif; ?>
			<?php if (! $this->_var['ad_info']['is_aduit_confirm']): ?><input type="file" name="idea_photo[]"></td><?php endif; ?>
	</tr>
	<tr>
		<td>远景2. </td>
		<td>
			<?php if ($this->_var['photo_info']): ?>
				<a href="<?php echo $this->_var['photo_info']['3']['img_url']; ?>" target="_blank" class="city_photo">
					<img src="<?php echo $this->_var['photo_info']['3']['thumb_url']; ?>">
				</a><?php echo $this->_var['reupload_message']; ?><input type="hidden" name="img_id[]" value="<?php echo $this->_var['photo_info']['3']['img_id']; ?>">
			<?php endif; ?>
			<?php if (! $this->_var['ad_info']['is_aduit_confirm']): ?><input type="file" name="idea_photo[]"></td><?php endif; ?>
	</tr>
	<tr>
		<td>备注:</td>
		<td class="red-color">如果有灯光,上传两张夜景,远景近景各一张</td>
	</tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="upload_user_id" value="<?php echo $this->_var['sm_session']['user_id']; ?>" />
        <input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_id']; ?>" />
		<?php if ($this->_var['photo_info']): ?><input type="hidden" name="modify" value="1" /><?php endif; ?>
      	<input type="hidden" name="act" value="act_upload_photo" />
    	<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
      </td>
    </tr>
	<?php endif; ?>

  </table>
</form>
</div>
</div>
<?php endif; ?>