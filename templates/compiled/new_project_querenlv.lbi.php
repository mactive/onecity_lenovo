<div class="font20px"><span class="red-block f_left" style="padding:5px 20px;">新增</span>2011年Q<?php echo $this->_var['project_id']; ?> 换画反馈实时统计表 当前时间:<?php echo $this->_var['date']; ?></div>
<div class="font14px">
<a href="city_operate.php?act=new_project_querenlv&project_id=2">Q2</a>
<a href="city_operate.php?act=new_project_querenlv&project_id=3">Q3</a>
<a href="city_operate.php?act=new_project_querenlv&project_id=4">Q4</a>
</div>
	<table width="80%" style="margin:10px auto;background:url(themes/default/images/container_bg.png) #ffffff;" class="table_standard table_border_dark" border="1">
		<tr>
			<td rowspan="1">分区</td>
			<td colspan="5">全部城市</td>
		</tr>
		<tr>
			<td></td>
			<td><span class="red-block f_left">新增</span>城市数量</td>
			<td>上画数量</td>
			<td>上画率</td>
			<td>上画通过数量</td>
			<td>上画通过率</td>
		</tr>
		<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_36505300_1328442675');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_36505300_1328442675']):
?>
		<tr>
			<td><?php echo $this->_var['item_0_36505300_1328442675']['col_1']; ?></td>
			<td><?php echo $this->_var['item_0_36505300_1328442675']['lv_6']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_36505300_1328442675']['lv_6']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_36505300_1328442675']['lv_6']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_36505300_1328442675']['lv_6']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_36505300_1328442675']['lv_6']['confirm_percent']; ?>%</td>
		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			
	</table>
