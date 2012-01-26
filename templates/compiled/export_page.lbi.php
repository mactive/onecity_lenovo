
<script type="text/javascript" src="js/calendar.php"></script>
<link href="js/calendar/calendar.css" rel="stylesheet" type="text/css" />

<div class="form-div">
<form method="post" action="city_operate.php" name="theForm" enctype="multipart/form-data">
	<table width="80%" style="margin:10px auto;" class="table_standard table_border" border="1">
		<tr><td>
			<img src="themes/default/images/green_arrow.png" alt=""/>
			</td>
			<td>
			 搜索:<?php if ($this->_var['sm_session']['user_rank'] != 1 && $this->_var['sm_session']['user_rank'] != 3): ?>
		    	<?php echo $this->_var['lang']['region']; ?> 
					<select name="region">
					      <?php echo $this->html_options(array('options'=>$this->_var['region_array'],'selected'=>$this->_var['item']['region'])); ?>
					</select>
				&nbsp;&nbsp;&nbsp;
				<?php endif; ?>
		    	<?php echo $this->_var['lang']['market_level']; ?>
		 			<select name="market_level">
						<option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
					      <?php echo $this->html_options(array('options'=>$this->_var['market_level_array'],'selected'=>$this->_var['item']['market_level'])); ?>
					</select>
				&nbsp;&nbsp;&nbsp;
				

				<?php echo $this->_var['lang']['audit_status']; ?>
		 			<select name="audit_status">
						<option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
					      <?php echo $this->html_options(array('options'=>$this->_var['audit_status_array'],'selected'=>$this->_var['item']['audit_status'])); ?>
					</select>
				&nbsp;&nbsp;&nbsp;
				是否包含<?php echo $this->_var['lang']['is_xz']; ?> 
				<select name="has_new">
			      	<option value='0'><?php echo $this->_var['lang']['select_please']; ?></option>
			      	<option value='1'><?php echo $this->_var['lang']['is_xz']; ?></option>
				</select>
				&nbsp;&nbsp;&nbsp;
				
				<br><br>
				<?php echo $this->_var['lang']['start_time']; ?>				
				<input name="start_time" value="<?php echo $this->_var['start_date']; ?>" style="width:80px;" onclick="return showCalendar(this, '%Y-%m-%d', false, false, this);" />
			      ~ 	<?php echo $this->_var['lang']['end_time']; ?>
				<input name="end_time" value="<?php echo $this->_var['start_date']; ?>" style="width:80px;" onclick="return showCalendar(this, '%Y-%m-%d', false, false, this);" />


				<?php echo $this->_var['lang']['resource_title']; ?>		
				<select name="resource">
					<option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
				      <?php echo $this->html_options(array('options'=>$this->_var['lang']['resource'],'selected'=>$this->_var['item']['resource'])); ?>
				</select>
				
			    
			</td>
		<td style="padding-left:30px;">
			<input type="hidden" name="act" value="export_db" />
			<input type="submit" value="导出" class="button" /> <input type="reset" value="重置" class="button" /></td>
		</tr>
	</table>
  </form>
</div>


<script type="text/javascript" language="javascript">


</script>