<div class="font20px">2011年Q<?php echo $this->_var['project_id']; ?> 换画反馈实时统计表 当前时间:<?php echo $this->_var['date']; ?></div>
<div class="font14px">
<a href="city_operate.php?act=project_querenlv&project_id=1">Q1</a>
<a href="city_operate.php?act=project_querenlv&project_id=2">Q2</a>
<a href="city_operate.php?act=project_querenlv&project_id=3">Q3</a>
<a href="city_operate.php?act=project_querenlv&project_id=4">Q4</a>
</div>
	<table width="1200px" style="margin:10px auto;background:url(themes/default/images/container_bg.png) #ffffff;" class="table_standard table_border_dark" border="1">
		<tr>
			<td rowspan="1">分区</td>
			<td colspan="5">4级</td>
			<td colspan="5">5级</td>
			<td colspan="5">6级(不含百强镇)</td>
			<td colspan="5">百强镇</td>
		</tr>
		<tr>
			<td></td>
			<td>城市数量</td>
			<td>上画数量</td>
			<td>上画率</td>
			<td>上画通过数量</td>
			<td>上画通过率</td>
			<td>城市数量</td>
			<td>上画数量</td>
			<td>上画率</td>
			<td>上画通过数量</td>
			<td>上画通过率</td>
			<td>城市数量</td>
			<td>上画数量</td>
			<td>上画率</td>
			<td>上画通过数量</td>
			<td>上画通过率</td>
			<?php if ($this->_var['sm_session']['user_id'] == 59): ?>
			<td>城市数量</td>
			<td>上画数量</td>
			<td>上画率</td>
			<td>上画通过数量</td>
			<td>上画通过率</td>
			<?php endif; ?>
		</tr>
		<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_34397100_1328442497');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_34397100_1328442497']):
?>
		<tr>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['col_1']; ?></td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_4']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_4']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_34397100_1328442497']['lv_4']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_4']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_34397100_1328442497']['lv_4']['confirm_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_5']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_5']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_34397100_1328442497']['lv_5']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_5']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_34397100_1328442497']['lv_5']['confirm_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_6']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_6']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_34397100_1328442497']['lv_6']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_6']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_34397100_1328442497']['lv_6']['confirm_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_7']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_7']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_34397100_1328442497']['lv_7']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_34397100_1328442497']['lv_7']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_34397100_1328442497']['lv_7']['confirm_percent']; ?>%</td>
		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			
	</table>
