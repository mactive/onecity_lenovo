<div class="f_left" style="width:100%;">
	<a class="back_url" href="city_base_info.php?act=ad_list&project_id=<?php echo $this->_var['project_id']; ?>&region_name=<?php echo $this->_var['city_name']; ?>"></a>
</div>
<script type="text/javascript" src="js/calendar.php"></script>
<link href="js/calendar/calendar.css" rel="stylesheet" type="text/css" />

<div class="radius_5px city_info" style="width:95%;height:200px;padding:0px 10px;">
	<span class="green-color font14px">未换画之前</span><br>
<?php $_from = $this->_var['old_photo_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_78257600_1315224179');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_78257600_1315224179']):
?>
	<div style="width:160px;height:160px;text-align:center;float:left;margin:10px 20px;">
	<a href="<?php echo $this->_var['item_0_78257600_1315224179']['img_url']; ?>" target="_blank" class="city_photo"><img src="<?php echo $this->_var['item_0_78257600_1315224179']['thumb_url']; ?>"></a>
	<?php echo $this->_var['lang']['city_photo'][$this->_var['item_0_78257600_1315224179']['img_sort']]; ?>
	</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>

<?php echo $this->fetch('library/audit_path.htm'); ?>						


<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
<div class="yellow_notice" style="text-align:center;"><?php echo $this->_var['upload_message']; ?></div>
<?php endif; ?>

<?php if ($this->_var['ad_info']['is_audit_confirm'] == 1 && $this->_var['ad_info']['audit_status'] == 5): ?>
	<form method="post" action="city_base_info.php" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
	<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_78288100_1315224179');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_78288100_1315224179']):
?>
	<div class="city_info radius_5px">
		<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_78288100_1315224179']; ?></div>
		<div class="f_left right_content">		
			<span class="f_right"><?php if ($this->_var['k'] == "col_12"): ?>如是两块牌子请写合计尺寸 &nbsp;<?php endif; ?>
				<a target="_blank"  class="grey666" href="city_operate.php?act=view_log&ad_id=<?php echo $this->_var['ad_detail']['ad_id']; ?>&col_name=<?php echo $this->_var['k']; ?>">
				修改记录</a></span>
				
			<?php if (( $this->_var['ad_detail']['base_info_modify'] == 1 && $this->_var['sm_session']['user_rank'] == 1 ) && ( $this->_var['k'] != "col_1" && $this->_var['k'] != "col_2" && $this->_var['k'] != "col_3" && $this->_var['k'] != "col_4" && $this->_var['k'] != "col_5" && $this->_var['k'] != "col_19" && $this->_var['k'] != "col_20" && $this->_var['k'] != "col_21" && $this->_var['k'] != "col_22" && $this->_var['k'] != "col_23" && $this->_var['k'] != "col_27" && $this->_var['k'] != "col_41" )): ?>
				<?php if ($this->_var['k'] == "col_16" || $this->_var['k'] == "col_17" || $this->_var['k'] == "col_18" || $this->_var['k'] == "col_35" || $this->_var['k'] == "col_37" || $this->_var['k'] == "col_39"): ?>
						<?php if ($this->_var['k'] == "col_18"): ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="45" style="background:#ffffff;" readonly=1/>
						<?php elseif ($this->_var['k'] == "col_16" || $this->_var['k'] == "col_17"): ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="45" style="background:#fffead;" rel="datepicker" onchange=sep_days() readonly=1 />
						<?php else: ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="45" style="background:#fffead;" rel="datepicker" readonly=1 />
						
						<?php endif; ?>
				<?php elseif ($this->_var['k'] == "col_13" || $this->_var['k'] == "col_15"): ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="45" style="background:#ffffff;" readonly=1/>
				
				<?php elseif ($this->_var['k'] == "col_10"): ?>
					<select id="<?php echo $this->_var['k']; ?>" name="col[]">
					      <?php echo $this->html_options(array('options'=>$this->_var['lang']['resource_type'],'selected'=>$this->_var['ad_detail'][$this->_var['k']])); ?>
					</select>
				<?php elseif ($this->_var['k'] == "col_42"): ?>
						<select id="<?php echo $this->_var['k']; ?>" name="col[]">
						      <?php echo $this->html_options(array('options'=>$this->_var['lang']['pic_type_select_lite'],'selected'=>$this->_var['ad_detail'][$this->_var['k']])); ?>
						</select>
				<?php else: ?>
					<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" <?php if ($this->_var['k'] == "col_12"): ?>size="20"<?php else: ?>size="45"<?php endif; ?>  <?php if ($this->_var['k'] == "col_11" || $this->_var['k'] == "col_12" || $this->_var['k'] == "col_14"): ?>onblur=calc_area()<?php endif; ?> style="background:#fffead;"/>
					

				<?php endif; ?>
			<?php else: ?>
				<span><?php echo $this->_var['ad_detail'][$this->_var['k']]; ?></span>
				<input type="hidden" name="col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" />
			<?php endif; ?>
		</div>	
		<div class="clear"></div>
	</div>
	<input type="hidden" name="old_col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" />
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	
	<?php if ($this->_var['ad_detail']['base_info_modify'] == 1 && $this->_var['sm_session']['user_rank'] == 1): ?>
	
	<div style="width:500px;float:left;">
		<input type="hidden" name="act" value="act_update_ad_info" />
		<input type="hidden" name="project_id" value="<?php echo $this->_var['project_id']; ?>" />	
		<input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_detail']['ad_id']; ?>" />	
		<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
	</div>
	<?php endif; ?>
	
	</form>
<?php endif; ?>


<script type="text/javascript">
/**
 * 检查表单输入的数据
 */
function validate()
{
	var col_19 = document.getElementById('col_19');
	var col_20 = document.getElementById('col_20');
	var col_28 = document.getElementById('col_28');
	var col_29 = document.getElementById('col_29');
	var col_42 = document.getElementById('col_42');
    
    if (col_42.value == 0 )
    {
        alert("费用来源必须选择!");
        return false;
    }else{
		return true;
	}

	/*
	var con = confirm("只有一次填写机会，请确认填写和修改的数据无错误");
	if(con == true){
		return true;
	}
	*/
	
    //return validator.passed();
}

function calc_area(){
	var col_11 = document.getElementById('col_11').value;
	var col_12 = document.getElementById('col_12').value;
	var col_14 = document.getElementById('col_14').value;
	var tt = col_11 * col_12;
	document.getElementById('col_13').value = tt;
	document.getElementById('col_15').value = tt * col_14;
}

function sep_days()
{
	var end_date = document.getElementById('col_17').value;
	var start_date = document.getElementById('col_16').value;
 	var temp = strtotime(end_date)-strtotime(start_date);
 	var days = temp/(60*60*24);
 	//alert(days+1);
	document.getElementById('col_18').value = days + 1;

}

function strtotime(time_str) 
{ 
	var time  = (new Date()).getTime(); 

	if (time_str) 
    { 
    	var str = time_str.split('\/'); 

        if (3 === str.length) 
       	{ 
        	var year  = str[2] - 0; 
            var month = str[0] - 0 - 1; 
            var day   = str[1] - 0; 
            
  		} 
 		time = (new Date(year, month, day)).getTime();
    }
	time = time / 1000; 
   	return time; 
}
</script>