<div class="font20px">基础信息实时统计表 当前时间:<?php echo $this->_var['date']; ?></div>

	<table width="1900px" style="margin:10px auto;background:url(themes/default/images/container_bg.png) #ffffff;" class="table_standard table_border_dark" border="1">
		<tr>
			<td rowspan="1">分区</td>
			<td colspan="8">4级</td>
			<td colspan="8">5级</td>
			<td colspan="8">6级(不含百强镇)</td>
			<td colspan="8">百强镇</td>
		</tr>
		<tr>
			<td></td>
			<td>城市数量</td>
			<td>已修改数量</td>
			<td>修改率</td>
			<td>审核数量<br>(电通工作量)</td>
			<td>通过数量</td>
			<td>通过率</td>
			<td><span class="red-block">寄出</span></td>
			<td><span class="red-block">收到</span></td>
			<td>城市数量</td>
			<td>已修改数量</td>
			<td>修改率</td>
			<td>审核数量<br>(电通工作量)</td>
			<td>通过数量</td>
			<td>通过率</td>
			<td><span class="red-block">寄出</span></td>
			<td><span class="red-block">收到</span></td>
			<td>城市数量</td>
			<td>已修改数量</td>
			<td>修改率</td>
			<td>审核数量<br>(电通工作量)</td>
			<td>通过数量</td>
			<td>通过率</td>
			<td><span class="red-block">寄出</span></td>
			<td><span class="red-block">收到</span></td>
			<td>城市数量</td>
			<td>已修改数量</td>
			<td>修改率</td>
			<td>审核数量<br>(电通工作量)</td>
			<td>通过数量</td>
			<td>通过率</td>
			<td><span class="red-block">寄出</span></td>
			<td><span class="red-block">收到</span></td>
		</tr>
		<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_30372600_1329552429');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_30372600_1329552429']):
?>
		<tr>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['col_1']; ?></td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_4']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_4']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_4']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_4']['audit_amount']; ?></td><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_4']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_4']['confirm_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_4']['send_amount']; ?><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_4']['receive_amount']; ?></td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_5']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_5']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_5']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_5']['audit_amount']; ?><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_5']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_5']['confirm_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_5']['send_amount']; ?><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_5']['receive_amount']; ?></td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_6']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_6']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_6']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_6']['audit_amount']; ?><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_6']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_6']['confirm_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_6']['send_amount']; ?><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_6']['receive_amount']; ?></td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_7']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_7']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_7']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_7']['audit_amount']; ?><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_7']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_7']['confirm_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_30372600_1329552429']['lv_7']['send_amount']; ?><td><?php echo $this->_var['item_0_30372600_1329552429']['lv_7']['receive_amount']; ?></td>
		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			
	</table>
