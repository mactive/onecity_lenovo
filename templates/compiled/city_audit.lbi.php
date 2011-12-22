
<div class="f_left">
	<a class="back_url" href="city_operate.php?act=city_ad_list&city_id=<?php echo $this->_var['ad_info']['city_id']; ?>"></a>
</div>

<?php if ($this->_var['is_xz']): ?>
<?php echo $this->fetch('library/passed_audit_path.htm'); ?>	
<?php endif; ?>

<?php echo $this->fetch('library/audit_path.htm'); ?>					

<div class="upload_board" style="height:auto;float:left;">
	
<form method="post" action="city_operate.php" name="theForm" id="theForm" enctype="multipart/form-data" onsubmit="return validate()">
  <table width="100%" border="0" cellspacing="0" class="table_standard">
	<tr>
		<td>已经上传照片</td>
		<td>
			<?php $_from = $this->_var['photo_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_45146700_1324552205');$this->_foreach['photo_info'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['photo_info']['total'] > 0):
    foreach ($_from AS $this->_var['item_0_45146700_1324552205']):
        $this->_foreach['photo_info']['iteration']++;
?>
				<div style="width:160px;height:160px;text-align:center;float:left;margin:10px;">
				<a href="<?php echo $this->_var['item_0_45146700_1324552205']['img_url']; ?>" target="_blank" class="city_photo"><img src="<?php echo $this->_var['item_0_45146700_1324552205']['thumb_url']; ?>"></a>
				<?php echo $this->_var['lang']['city_photo'][$this->_var['item_0_45146700_1324552205']['img_sort']]; ?>
				</div>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</td>
	</tr>
	
	<?php if (( $this->_var['highest_audit_level'] <= $this->_var['sm_session']['user_rank'] && $this->_var['sm_session']['user_rank'] > 1 && $this->_var['sm_session']['user_rank'] - $this->_var['ad_info']['audit_status'] == 1 ) || ( $this->_var['ad_info']['is_audit_confirm'] == 0 && $this->_var['ad_info']['audit_status'] > 1 )): ?>
	<tr>
		<td width="130">如果审核不通过,<br>请写下明确的原因:</td>
		<td><br>
			<textarea name="audit_note" id="audit_note" cols="40" rows="4"></textarea></td>
	</tr>
	

    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_info']['ad_id']; ?>" />
        <input type="hidden" name="confirm" id="confirm" value="0" />
        <input type="hidden" name="city_id" value="<?php echo $this->_var['ad_info']['city_id']; ?>" />
      	<input type="hidden" name="act" value="update_audit" />
		<input type="submit" class="input_s3 f_left" value="不通过" />
		<a onclick="update_audit()" class="cancel_lite_btn f_left" style="margin-left:20px;">通过审核</a> 
      </td>
    </tr>
	<?php endif; ?>
  </table>
</form>
</div>


<script src="admin/js/validator.js" type="text/javascript"></script>
<script type="text/javascript">
function update_audit(){
	document.getElementById('confirm').value = 1;
	
	if(<?php echo $this->_var['sm_session']['user_rank']; ?> == 2){
		var obj_2 = document.getElementById('col_23');
		
		if (obj_2.value == "")
	    {
	        alert("请填写媒体评分");
	    }else{
			document.getElementById("theForm").submit();
			
		}
	}	
	else{
		document.getElementById("theForm").submit();
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
function validate2()
{
	
	var obj_2 = document.getElementById('col_23');
    if (obj_2.value == "")
    {
        alert("请填写媒体评分");
        return false;
    }
	return true;
    //return validator.passed();
}
</script>


<?php if ($this->_var['sm_session']['user_rank'] > 1): ?>
	<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
		<form method="post" action="city_operate.php" name="theForm" enctype="multipart/form-data" onsubmit="return validate2()">
		<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_45217300_1324552205');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_45217300_1324552205']):
?>
		<div class="city_info radius_5px">
			<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_45217300_1324552205']; ?></div>
			<div class="f_left right_content">
				<?php if ($this->_var['k'] == "col_1" || $this->_var['k'] == "col_2" || $this->_var['k'] == "col_3" || $this->_var['k'] == "col_4" || $this->_var['k'] == "col_5"): ?>
					<span><?php echo $this->_var['ad_detail'][$this->_var['k']]; ?></span>
					<input type="hidden" name="col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="50" />
				<?php else: ?>
					<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="50" />
					<span class="f_right"><a target="_blank"  class="grey666" href="city_operate.php?act=view_log&ad_id=<?php echo $this->_var['ad_detail']['ad_id']; ?>&col_name=<?php echo $this->_var['k']; ?>">
						修改记录</a></span>
					
				<?php endif; ?>
			</div>
			<div class="clear"></div>
		</div>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		<div style="width:500px;float:left;">
			<input type="hidden" name="act" value="update_ad" />
			<input type="hidden" name="form_audit" value="1" />
			<input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_detail']['ad_id']; ?>" />
			<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_45268400_1324552205');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_45268400_1324552205']):
?>
			<input type="hidden" name="old_col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" />
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
		</div>
		</form>
	<?php else: ?>
		<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_45286400_1324552205');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_45286400_1324552205']):
?>
		<div class="city_info radius_5px">
			<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_45286400_1324552205']; ?></div>
			<div class="f_left right_content"><?php echo $this->_var['ad_detail'][$this->_var['k']]; ?></div>	
			<div class="clear"></div>
		</div>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<?php endif; ?>
<?php endif; ?>



