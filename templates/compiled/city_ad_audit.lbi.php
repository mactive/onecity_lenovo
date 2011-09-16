<div class="font20px">电通工作统计表 当前时间:<?php echo $this->_var['date']; ?></div>
<div style="line-height:30px;">
	<form action="city_base_info.php" method="get" accept-charset="utf-8">
		开始时间: &nbsp;<input type="text" name="start_time" value="" rel="datepicker" value="<?php echo $this->_var['start_time']; ?>">&nbsp;&nbsp;-&nbsp;&nbsp;
		截止时间: &nbsp;<input type="text" name="end_time" value="" rel="datepicker" value="<?php echo $this->_var['end_time']; ?>">
		
		<input type="hidden" name="act" value="city_ad_audit" />
		<input type="submit" value="submit"><br>
		 统计时间: &nbsp; &nbsp;<?php echo $this->_var['start_time']; ?> &nbsp; &nbsp;- &nbsp; &nbsp;<?php echo $this->_var['end_time']; ?>
	</form>
</div>
	<table width="800px" style="margin:10px auto;background:url(themes/default/images/container_bg.png) #ffffff;" class="table_standard table_border_dark" border="1">
		<tr>
			<td rowspan="1">分区</td>
			<td colspan="2">牌子审核</td>
			<td colspan="2">基础信息修改审核</td>
			<td colspan="2">Q1换画审核</td>
			<td colspan="2">Q2换画审核</td>
			<td colspan="2">Q3换画审核</td>
			<td colspan="2">Q4换画审核</td>
		</tr>
		<tr>
			<td></td>
			<td>通过</td>
			<td>不通过</td>
			<td>通过</td>
			<td>不通过</td>
			<td>通过</td>
			<td>不通过</td>
			<td>通过</td>
			<td>不通过</td>
			<td>通过</td>
			<td>不通过</td>
			<td>通过</td>
			<td>不通过</td>
		</tr>
		<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_28769300_1316186284');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_28769300_1316186284']):
?>
		<tr>
			<td><?php echo $this->_var['item_0_28769300_1316186284']['col_1']; ?></td>
			<td><?php echo $this->_var['item_0_28769300_1316186284']['q0']['passed']; ?></td><td><?php echo $this->_var['item_0_28769300_1316186284']['q0']['refused']; ?></td>
			<td><?php echo $this->_var['item_0_28769300_1316186284']['q9']['passed']; ?></td><td><?php echo $this->_var['item_0_28769300_1316186284']['q9']['refused']; ?></td>
			<td><?php echo $this->_var['item_0_28769300_1316186284']['q1']['passed']; ?></td><td><?php echo $this->_var['item_0_28769300_1316186284']['q1']['refused']; ?></td>
			<td><?php echo $this->_var['item_0_28769300_1316186284']['q2']['passed']; ?></td><td><?php echo $this->_var['item_0_28769300_1316186284']['q2']['refused']; ?></td>
			<td><?php echo $this->_var['item_0_28769300_1316186284']['q3']['passed']; ?></td><td><?php echo $this->_var['item_0_28769300_1316186284']['q3']['refused']; ?></td>
			<td><?php echo $this->_var['item_0_28769300_1316186284']['q4']['passed']; ?></td><td><?php echo $this->_var['item_0_28769300_1316186284']['q4']['refused']; ?></td>
		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			
	</table>
