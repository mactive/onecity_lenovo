<div class="font20px">审核状态汇总表 当前时间:<?php echo $this->_var['date']; ?></div> 城市级别，，城市数量，待审核，审核通过，提交数量，审核未通过

	<table width="100%" style="margin:10px auto;background:url(themes/default/images/container_bg.png) #ffffff;" class="table_standard table_border_dark" border="1">
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
			<td>提交数量</td>
			<td>待电通审核</td>
			<td>审核通过</td>
			<td>审核未通过</td>
			<td>城市数量</td>
			<td>提交数量</td>
			<td>待电通审核</td>
			<td>审核通过</td>
			<td>审核未通过</td>
			<td>城市数量</td>
			<td>提交数量</td>
			<td>待电通审核</td>
			<td>审核通过</td>
			<td>审核未通过</td>
			<td>城市数量</td>
			<td>提交数量</td>
			<td>待电通审核</td>
			<td>审核通过</td>
			<td>审核未通过</td>
		</tr>
		<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_96871600_1329577271');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_96871600_1329577271']):
?>
		<tr>
			<td><?php echo $this->_var['item_0_96871600_1329577271']['col_1']; ?></td>
			<td><?php echo $this->_var['item_0_96871600_1329577271']['lv_4']['amount']; ?></td><td><?php echo $this->_var['item_0_96871600_1329577271']['lv_4']['upload_amount']; ?></td>
			<td><span <?php if ($this->_var['item_0_96871600_1329577271']['lv_4']['wait_amount'] > 0): ?>class="red-block"<?php endif; ?>><?php echo $this->_var['item_0_96871600_1329577271']['lv_4']['wait_amount']; ?></span></td>
			
			<td><?php echo $this->_var['item_0_96871600_1329577271']['lv_4']['pass_amount']; ?></td><td><?php echo $this->_var['item_0_96871600_1329577271']['lv_4']['unpass_amount']; ?></td>
			<td><?php echo $this->_var['item_0_96871600_1329577271']['lv_5']['amount']; ?></td><td><?php echo $this->_var['item_0_96871600_1329577271']['lv_5']['upload_amount']; ?></td>
			<td><span <?php if ($this->_var['item_0_96871600_1329577271']['lv_5']['wait_amount'] > 0): ?>class="red-block"<?php endif; ?>><?php echo $this->_var['item_0_96871600_1329577271']['lv_5']['wait_amount']; ?></span></td>
			<td><?php echo $this->_var['item_0_96871600_1329577271']['lv_5']['pass_amount']; ?></td><td><?php echo $this->_var['item_0_96871600_1329577271']['lv_5']['unpass_amount']; ?></td>
			
			<td><?php echo $this->_var['item_0_96871600_1329577271']['lv_6']['amount']; ?></td><td><?php echo $this->_var['item_0_96871600_1329577271']['lv_6']['upload_amount']; ?></td>
			<td><span <?php if ($this->_var['item_0_96871600_1329577271']['lv_6']['wait_amount'] > 0): ?>class="red-block"<?php endif; ?>><?php echo $this->_var['item_0_96871600_1329577271']['lv_6']['wait_amount']; ?></span></td>
			<td><?php echo $this->_var['item_0_96871600_1329577271']['lv_6']['pass_amount']; ?></td><td><?php echo $this->_var['item_0_96871600_1329577271']['lv_6']['unpass_amount']; ?></td>
			
			<td><?php echo $this->_var['item_0_96871600_1329577271']['lv_7']['amount']; ?></td><td><?php echo $this->_var['item_0_96871600_1329577271']['lv_7']['upload_amount']; ?></td>
			<td><span <?php if ($this->_var['item_0_96871600_1329577271']['lv_7']['wait_amount'] > 0): ?>class="red-block"<?php endif; ?>><?php echo $this->_var['item_0_96871600_1329577271']['lv_7']['wait_amount']; ?></span></td>
			<td><?php echo $this->_var['item_0_96871600_1329577271']['lv_7']['pass_amount']; ?></td><td><?php echo $this->_var['item_0_96871600_1329577271']['lv_7']['unpass_amount']; ?></td>		
		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			
	</table>
