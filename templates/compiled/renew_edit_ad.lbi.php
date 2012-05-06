<div class="f_left">
	<a class="back_url" href="city_renew.php?act=city_ad_list&city_id=<?php echo $this->_var['ad_info']['city_id']; ?>"></a>
</div>

<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
<div class="yellow_notice" style="text-align:center;"><?php echo $this->_var['upload_message']; ?></div>
<?php endif; ?>
<?php if ($this->_var['ad_info']['is_delete'] == 0): ?>
	<form method="post" action="city_renew.php" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
	<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_47646700_1336281605');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_47646700_1336281605']):
?>
	<div class="city_info radius_5px">
		<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_47646700_1336281605']; ?></div>
		<div class="f_left right_content">		
			<span class="f_right"><?php if ($this->_var['k'] == "col_12"): ?>如是两块牌子请写合计尺寸 &nbsp;<?php endif; ?>
				<a target="_blank"  class="grey666" href="city_operate.php?act=view_log&ad_id=<?php echo $this->_var['ad_detail']['ad_id']; ?>&col_name=<?php echo $this->_var['k']; ?>">
				修改记录</a></span>
				
			<?php if (( $this->_var['ad_info']['is_change'] == 0 || ( $this->_var['ad_info']['is_audit_confirm'] == 0 && $this->_var['ad_info']['audit_status'] == 2 ) ) && ( $this->_var['k'] != "col_1" && $this->_var['k'] != "col_2" && $this->_var['k'] != "col_3" && $this->_var['k'] != "col_4" && $this->_var['k'] != "col_5" && $this->_var['k'] != "col_6" && $this->_var['k'] != "col_7" && $this->_var['k'] != "col_8" && $this->_var['k'] != "col_9" && $this->_var['k'] != "col_10" && $this->_var['k'] != "col_11" && $this->_var['k'] != "col_12" && $this->_var['k'] != "col_13" && $this->_var['k'] != "col_14" && $this->_var['k'] != "col_15" && $this->_var['k'] != "col_23" && $this->_var['k'] != "col_27" && $this->_var['k'] != "col_41" )): ?>
				<?php if ($this->_var['k'] == "col_16" || $this->_var['k'] == "col_17" || $this->_var['k'] == "col_18" || $this->_var['k'] == "col_35" || $this->_var['k'] == "col_37" || $this->_var['k'] == "col_39"): ?>
						<?php if ($this->_var['k'] == "col_18"): ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="37" style="background:#ffffff;" readonly=1/>
						<?php elseif ($this->_var['k'] == "col_16" || $this->_var['k'] == "col_17"): ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="37" style="background:#fffead;" rel="datepicker" onchange=sep_days() readonly=1 />
						<?php else: ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="37" style="background:#fffead;" rel="datepicker" readonly=1 />
						
						<?php endif; ?>
				<?php elseif ($this->_var['k'] == "col_13" || $this->_var['k'] == "col_15" || $this->_var['k'] == "col_22"): ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="37" style="background:#ffffff;" readonly=1/>
				
				<?php elseif ($this->_var['k'] == "col_10"): ?>
					<select id="<?php echo $this->_var['k']; ?>" name="col[]">
					      <?php echo $this->html_options(array('options'=>$this->_var['lang']['resource_type'],'selected'=>$this->_var['ad_detail'][$this->_var['k']])); ?>
					</select>
				<?php elseif ($this->_var['k'] == "col_42"): ?>
						<select id="<?php echo $this->_var['k']; ?>" name="col[]">
						      <?php echo $this->html_options(array('options'=>$this->_var['lang']['pic_type_select_lite'],'selected'=>$this->_var['ad_detail'][$this->_var['k']])); ?>
						</select>
				<?php elseif ($this->_var['k'] == "col_47"): ?>
						<select id="<?php echo $this->_var['k']; ?>" name="col[]" onChange="hide_items()">
						      <?php echo $this->html_options(array('options'=>$this->_var['lang']['promotion_select'],'selected'=>$this->_var['ad_detail'][$this->_var['k']])); ?>
						</select>
				<?php elseif ($this->_var['k'] == "col_19" || $this->_var['k'] == "col_20" || $this->_var['k'] == "col_21"): ?>
					<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" onblur=calc_price() style="background:#fffead;"/>
				<?php else: ?>
					<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" <?php if ($this->_var['k'] == "col_12"): ?>size="20"<?php else: ?>size="37"<?php endif; ?>  <?php if ($this->_var['k'] == "col_11" || $this->_var['k'] == "col_12" || $this->_var['k'] == "col_14"): ?>onblur=calc_area()<?php endif; ?> style="background:#fffead;"/>
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
	
	<?php if (( $this->_var['ad_info']['is_change'] == 0 && $this->_var['sm_session']['user_rank'] == 1 ) || ( $this->_var['ad_info']['is_audit_confirm'] == 0 && $this->_var['ad_info']['audit_status'] == 2 )): ?>
	
	<div style="width:500px;float:left;">
		<input type="hidden" name="act" value="act_update_renew_info" />
		<input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_detail']['ad_id']; ?>" />	
		<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
	</div>
	<?php endif; ?>
	
	</form>
<?php endif; ?>


<script type="text/javascript">
hide_items();
/**
 * 检查表单输入的数据
 */
function validate()
{
	// var col_19 = document.getElementById('col_19');
	// var col_20 = document.getElementById('col_20');
	// var col_28 = document.getElementById('col_28');
	// var col_29 = document.getElementById('col_29');
	var col_42 = document.getElementById('col_42');
	var col_47 = document.getElementById('col_47');

	    if (col_42.value == 0 )
	    {
	        alert("费用来源必须选择!");
	        return false;
	    }
	
		else if(col_47.value == 0 ){
			alert("是否使用推广费必须选择!");
			return false;
		}
		
		else if(col_47.value != 2){
			if($("input#col_43").val() =="" || $("input#col_44").val() =="" || $("input#col_45").val() =="" || $("input#col_46").val() =="" || $("input#col_48").val() ==""  || $("input#col_49").val() =="" ){
				alert("甲方，上级，推广费，营销折扣金额等6项必须填写");
				return false;
			}
		}
		else{
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

function hide_items(){
	var _value = $("#col_47").val();
	
	if(_value == 2){
		$("input#col_43").parentsUntil(".city_info").parent().hide();
		$("input#col_44").parentsUntil(".city_info").parent().hide();
		$("input#col_45").parentsUntil(".city_info").parent().hide();
		$("input#col_46").parentsUntil(".city_info").parent().hide();
		$("input#col_48").parentsUntil(".city_info").parent().hide();
		$("input#col_49").parentsUntil(".city_info").parent().hide();
	}
	else{
		$("input#col_43").parentsUntil(".city_info").parent().show();
		$("input#col_44").parentsUntil(".city_info").parent().show();
		$("input#col_45").parentsUntil(".city_info").parent().show();
		$("input#col_46").parentsUntil(".city_info").parent().show();
		$("input#col_48").parentsUntil(".city_info").parent().show();
		$("input#col_49").parentsUntil(".city_info").parent().show();

	}
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

function calc_price()
{
	var price_1 = document.getElementById('col_19').value;
	var price_2 = document.getElementById('col_20').value;
	var price_3 = document.getElementById('col_21').value;
	var tmp = parseInt(price_1) + parseInt(price_2) + parseInt(price_3) ;
 	//alert(days+1);
	document.getElementById('col_22').value = tmp;

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