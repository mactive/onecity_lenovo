<div class="font20px"><span class="red-block f_left" style="padding:5px 20px;">新增</span>基础信息实时统计表 当前时间:<?php echo $this->_var['date']; ?></div>

	<table width="80%" style="margin:10px auto;background:url(themes/default/images/container_bg.png) #ffffff;" class="table_standard table_border_dark" border="1">
		<tr>
			<td rowspan="1">分区</td>
			<td colspan="8">全部级别</td>
		</tr>
		<tr>
			<td></td>
			<td><span class="red-block f_left">新增</span>城市数量</td>
			<td>已修改数量</td>
			<td>修改率</td>
			<td>审核数量<br>(电通工作量)</td>
			<td>通过数量</td>
			<td>通过率</td>
			<td><span class="red-block">寄出</span></td>
			<td><span class="red-block">收到</span></td>
		</tr>
		<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_23473800_1329649252');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_23473800_1329649252']):
?>
		<tr>
			<td><?php echo $this->_var['item_0_23473800_1329649252']['col_1']; ?></td>
			<td><?php echo $this->_var['item_0_23473800_1329649252']['lv_6']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_23473800_1329649252']['lv_6']['upload_amount']; ?></td><td><?php echo $this->_var['item_0_23473800_1329649252']['lv_6']['upload_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_23473800_1329649252']['lv_6']['audit_amount']; ?><td><?php echo $this->_var['item_0_23473800_1329649252']['lv_6']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_23473800_1329649252']['lv_6']['confirm_percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_23473800_1329649252']['lv_6']['send_amount']; ?><td><?php echo $this->_var['item_0_23473800_1329649252']['lv_6']['receive_amount']; ?></td>
			
		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			
	</table>
