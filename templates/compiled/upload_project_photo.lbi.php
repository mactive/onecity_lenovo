<div class="f_left" style="width:100%;">
	<a class="back_url" href="city_project.php?act=list_city_to_select&project_id=<?php echo $this->_var['project_id']; ?>&region_name=<?php echo $this->_var['city_name']; ?>"></a>
</div>

<?php $_from = $this->_var['old_photo_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'project');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['project']):
?>
	<?php if ($this->_var['project']): ?>
	<div class="radius_5px city_info" style="width:95%;height:200px;padding:0px 10px;">
		<span class="green-color font14px"><?php if ($this->_var['k'] == 0): ?>未换画之前<?php else: ?>2011Q<?php echo $this->_var['k']; ?><?php endif; ?></span><br>
	<?php $_from = $this->_var['project']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_35589400_1314285818');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_35589400_1314285818']):
?>	
	<div style="width:160px;height:160px;text-align:center;float:left;margin:10px 20px;">
	<a href="<?php echo $this->_var['item_0_35589400_1314285818']['img_url']; ?>" target="_blank" class="city_photo"><img src="<?php echo $this->_var['item_0_35589400_1314285818']['thumb_url']; ?>"></a>
	<?php echo $this->_var['lang']['city_photo'][$this->_var['item_0_35589400_1314285818']['img_sort']]; ?>
	</div>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php echo $this->fetch('library/audit_path.htm'); ?>						

<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>

<div class="upload_table">
<div class="upload_board" style="height:auto;float:left;">	
<form method="post" action="city_project.php" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
<table width="70%" id="lesson-table" class="table_border table_standard" border="1">
    <tr>
      <td width="100"><?php echo $this->_var['lang']['project_name']; ?>:</td>
      <td><span class="red_color font16px"><?php echo $this->_var['project_info']['project_name']; ?></span></td>
    </tr>
	<tr>
      <td width="100"><?php echo $this->_var['lang']['county']; ?>:</td>
      <td><?php echo $this->_var['ad_info']['city_name']; ?></td>
    </tr>
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
			<?php if (( ! $this->_var['ad_info']['is_aduit_confirm'] && ! $this->_var['feedback_confirm'] && $this->_var['sm_session']['user_rank'] == 1 ) || $this->_var['is_change'] == 1): ?><input type="file" name="idea_photo[]"><?php endif; ?>
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
			<?php if (( ! $this->_var['ad_info']['is_aduit_confirm'] && ! $this->_var['feedback_confirm'] && $this->_var['sm_session']['user_rank'] == 1 ) || $this->_var['is_change'] == 1): ?><input type="file" name="idea_photo[]"><?php endif; ?>
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
			<?php if (( ! $this->_var['ad_info']['is_aduit_confirm'] && ! $this->_var['feedback_confirm'] && $this->_var['sm_session']['user_rank'] == 1 ) || $this->_var['is_change'] == 1): ?><input type="file" name="idea_photo[]"><?php endif; ?>
	</tr>
	<tr>
		<td>远景2. </td>
		<td>
			<?php if ($this->_var['photo_info']): ?>
				<a href="<?php echo $this->_var['photo_info']['3']['img_url']; ?>" target="_blank" class="city_photo">
					<img src="<?php echo $this->_var['photo_info']['3']['thumb_url']; ?>">
				</a><?php echo $this->_var['reupload_message']; ?><input type="hidden" name="img_id[]" value="<?php echo $this->_var['photo_info']['3']['img_id']; ?>">
			<?php endif; ?>
			<?php if (( ! $this->_var['ad_info']['is_aduit_confirm'] && ! $this->_var['feedback_confirm'] && $this->_var['sm_session']['user_rank'] == 1 ) || $this->_var['is_change'] == 1): ?><input type="file" name="idea_photo[]"><?php endif; ?>
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
        <input type="hidden" name="project_id" value="<?php echo $this->_var['project_id']; ?>" />
		<?php if ($this->_var['photo_info']): ?><input type="hidden" name="modify" value="1" /><?php endif; ?>
      	<input type="hidden" name="act" value="act_upload_photo" />
    	<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
      </td>
    </tr>
</table>
</form>
</div>
</div>
<?php else: ?>
<table width="70%" id="lesson-table" class="table_border table_standard" border="1">
	<tr>
      <td width="100"><?php echo $this->_var['lang']['project_name']; ?>:</td>
      <td><span class="red_color font16px"><?php echo $this->_var['project_info']['project_name']; ?></span></td>
    </tr>
	<tr>
      <td width="100"><?php echo $this->_var['lang']['county']; ?>:</td>
      <td><?php echo $this->_var['ad_info']['city_name']; ?></td>
    </tr>
</table>

<div class="radius_5px city_info" style="width:95%;height:200px;padding:0px 10px;">
	<span class="green-color font14px"><?php echo $this->_var['project_info']['project_name']; ?></span><br>
<?php $_from = $this->_var['photo_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_35702800_1314285818');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_35702800_1314285818']):
?>	
<div style="width:160px;height:160px;text-align:center;float:left;margin:10px 20px;">
<a href="<?php echo $this->_var['item_0_35702800_1314285818']['img_url']; ?>" target="_blank" class="city_photo"><img src="<?php echo $this->_var['item_0_35702800_1314285818']['thumb_url']; ?>"></a>
<?php echo $this->_var['lang']['city_photo'][$this->_var['item_0_35702800_1314285818']['img_sort']]; ?>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>

<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
<div class="clear"></div>
<form method="post" action="city_project.php" name="theForm" id="theForm" enctype="multipart/form-data" onsubmit="return validate()">
  <table width="100%" border="0" cellspacing="0" class="table_standard">
	<tr>
		<td width="130">如果审核不通过,<br>请写下明确的原因:</td>
		<td><br>
			<textarea name="audit_note" id="audit_note" cols="40" rows="4"></textarea></td>
	</tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_id']; ?>" />
        <input type="hidden" name="confirm" id="confirm" value="0" />
        <input type="hidden" name="project_id" value="<?php echo $this->_var['project_id']; ?>" />
      	<input type="hidden" name="act" value="update_audit" />
		<input type="submit" class="input_s3 f_left" value="意见反馈" />
		<a onclick="update_audit()" class="cancel_lite_btn f_left" style="margin-left:20px;">通过审核</a> 
      </td>
    </tr>
  </table>
</form>

<script type="text/javascript">
function update_audit(){
	document.getElementById('confirm').value = 1;
	
	if(<?php echo $this->_var['sm_session']['user_rank']; ?> == 2){
			document.getElementById("theForm").submit();
	}else{
		alert("您无权通过改画面");
	}
}
/**
 * 检查表单输入的数据
 */
function validate()
{
	
	var obj = document.getElementById('audit_note');

    if (obj.value == "")
    {
        alert("请填写不通过原因");
        return false;
    }
	return true;
    //return validator.passed();
}
</script>
<?php endif; ?>

<?php endif; ?>

<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_35722200_1314285818');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_35722200_1314285818']):
?>
<div class="city_info radius_5px">
	<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_35722200_1314285818']; ?></div>
	<div class="f_left right_content" 
	<?php if ($this->_var['k'] == "col_28" || $this->_var['k'] == "col_29" || $this->_var['k'] == "col_42" || $this->_var['k'] == "col_43" || $this->_var['k'] == "col_44"): ?>
	style="background:#fffead;"
	<?php endif; ?>>
		<?php if ($this->_var['k'] == "col_42"): ?>
			<select id="<?php echo $this->_var['k']; ?>" name="col[]">
			      <?php echo $this->html_options(array('options'=>$this->_var['lang']['pic_type_select_lite'],'selected'=>$this->_var['ad_detail'][$this->_var['k']])); ?>
			</select>
		<?php else: ?>
		<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>
		<?php endif; ?>
		</div>	
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>