<br>
<div class="font20px"><span class="red-block f_left" style="padding:5px 20px;">新增</span>城市上传确认率实时统计表 当前时间:<?php echo $this->_var['date']; ?></div>

	<table width="80%" style="margin:10px auto;background:url(themes/default/images/container_bg.png) #ffffff;" class="table_standard table_border_dark" border="1">
		<tr>
			<td rowspan="1">分区</td>
			<td colspan="6">全部级别</td>
		</tr>
		<tr>
			<td></td>
			<td><span class="red-block f_left">新增</span>城市数量</td>
			<td>4级确认城市数量</td>
			<td>5级确认城市数量</td>
			<td>6级确认城市数量</td>
			<td>百强镇确认城市数量</td>
			<td>总确认率</td>
		</tr>
		<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_78669100_1329921297');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_78669100_1329921297']):
?>
		<tr>
			<td><?php echo $this->_var['item_0_78669100_1329921297']['col_1']; ?></td>
			<td><?php echo $this->_var['item_0_78669100_1329921297']['amount']; ?></td>
			<td><?php echo $this->_var['item_0_78669100_1329921297']['lv_4']['confirm_amount']; ?></td>
			<td><?php echo $this->_var['item_0_78669100_1329921297']['lv_5']['confirm_amount']; ?></td>
			<td><?php echo $this->_var['item_0_78669100_1329921297']['lv_6']['confirm_amount']; ?></td>
			<td><?php echo $this->_var['item_0_78669100_1329921297']['lv_7']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_78669100_1329921297']['percent']; ?>%</td>

		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			
	</table>
	</table>
