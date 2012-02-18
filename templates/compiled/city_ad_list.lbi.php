	<div class="f_left">
		<a class="back_url" href="city_operate.php?region_name=<?php echo $this->_var['city_name']; ?>&has_new=<?php echo $this->_var['has_new']; ?>"></a>
	</div>
	<div class="facebox f_left">
		<table width="100%" class="table_border table_standard" border="1">
		    <tr>
		      	<th width="35"><?php echo $this->_var['lang']['is_xz']; ?></th>
		      	<th><?php echo $this->_var['lang']['county']; ?></th>
		      	<th width="200">具体位置描述</th>
			  	<th>上传时间</th>
		      	<th>照片数量</th>
			  	<th>审核状态</th>
				<?php $_from = $this->_var['audit_level_array']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'level');if (count($_from)):
    foreach ($_from AS $this->_var['level']):
?>
			  	<th><?php echo $this->_var['lang']['audit_level'][$this->_var['level']]; ?></th>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			  	<th><?php echo $this->_var['lang']['handler']; ?></th>
		    </tr>
		<?php $_from = $this->_var['ad_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_55620500_1329217874');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_55620500_1329217874']):
?>
			<tr <?php if ($this->_var['item_0_55620500_1329217874']['is_upload']): ?>
					<?php if ($this->_var['item_0_55620500_1329217874']['audit_status'] > 1): ?>
						<?php if ($this->_var['item_0_55620500_1329217874']['is_audit_confirm']): ?>
							class="city_audit_confirm"
						<?php else: ?>
							class="city_audit_cancel"
						<?php endif; ?>
					<?php else: ?>
						class="city_upload"
					<?php endif; ?>
				<?php endif; ?>>
				<td><?php if ($this->_var['item_0_55620500_1329217874']['is_new']): ?><span class="red-block">新增</span><?php else: ?><span class="grey999">已有</span><?php endif; ?></td>
				<td><?php echo $this->_var['item_0_55620500_1329217874']['city_name']; ?></td>
				<td><?php echo $this->_var['item_0_55620500_1329217874']['col_7']; ?></td>
				<td><?php if ($this->_var['item_0_55620500_1329217874']['is_upload']): ?><?php echo $this->_var['item_0_55620500_1329217874']['user_time']; ?><?php else: ?><?php echo $this->_var['lang']['upload_pending']; ?><?php endif; ?></td>
				<td>
					<span <?php if ($this->_var['item_0_55620500_1329217874']['photo_num']): ?>class="red-color font14px"<?php endif; ?>><?php echo $this->_var['item_0_55620500_1329217874']['photo_num']; ?></span></td>
				<td>
					<?php if ($this->_var['item_0_55620500_1329217874']['audit_status'] > 1): ?>
							<?php echo $this->_var['audit_title'][$this->_var['item_0_55620500_1329217874']['audit_status']]; ?>-
							<?php if ($this->_var['item_0_55620500_1329217874']['is_audit_confirm']): ?>
								<span class="green-color"><?php echo $this->_var['lang']['audit_confirm']; ?></span>
							<?php else: ?>
								<span class="red-color"><?php echo $this->_var['lang']['audit_cancel']; ?></span>
							<?php endif; ?>
						<?php else: ?>
							<?php echo $this->_var['lang']['audit_pending']; ?>
						<?php endif; ?>
				</td>
				
				<?php $_from = $this->_var['audit_level_array']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'level');if (count($_from)):
    foreach ($_from AS $this->_var['level']):
?>
				<td>

					<?php if ($this->_var['item_0_55620500_1329217874']['audit_status'] == $this->_var['level']): ?>
						<?php if ($this->_var['item_0_55620500_1329217874']['is_audit_confirm']): ?><a class="audit_confirm"></a>
						<?php else: ?><a class="audit_cancel"></a>
						<?php endif; ?>
					<?php else: ?>
						<?php if ($this->_var['item_0_55620500_1329217874']['audit_status'] > $this->_var['level']): ?><a class="audit_confirm">
						<?php else: ?>
							<?php if ($this->_var['level'] - $this->_var['item_0_55620500_1329217874']['audit_status'] == 1): ?>
								<?php if ($this->_var['item_0_55620500_1329217874']['audit_status'] == 1 && $this->_var['item_0_55620500_1329217874']['photo_num']): ?><a class="audit_idle"></a><?php endif; ?>
								<?php if ($this->_var['item_0_55620500_1329217874']['is_audit_confirm']): ?><a class="audit_idle"></a><?php else: ?><?php endif; ?>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				</td>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				<td>
					<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
						<?php if ($this->_var['item_0_55620500_1329217874']['is_audit_confirm'] == 0): ?>
							<?php if ($this->_var['item_0_55620500_1329217874']['audit_status'] == 1): ?>
							<a href="city_operate.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">查看</a> &nbsp; | &nbsp;
							<a href="city_operate.php?act=edit_ad&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">修改</a> &nbsp; | &nbsp;
							<a href="city_operate.php?act=upload_photo&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">传照片</a>							
							<?php else: ?>
							<a href="city_operate.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">查看</a>
							<?php endif; ?>
						<?php else: ?>
							<a href="city_operate.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">查看</a>
							<?php if ($this->_var['item_0_55620500_1329217874']['audit_status'] < 3): ?>
							 &nbsp; | &nbsp; <a href="city_operate.php?act=edit_ad&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">修改</a>
							<?php endif; ?>
						<?php endif; ?>
						
					<?php else: ?>
					
						<?php if ($this->_var['item_0_55620500_1329217874']['is_upload'] && $this->_var['item_0_55620500_1329217874']['photo_num']): ?>
							<?php if ($this->_var['item_0_55620500_1329217874']['is_audit_confirm'] == 0): ?>
								<?php if ($this->_var['item_0_55620500_1329217874']['audit_status'] == 1): ?>
									<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
										<a href="city_operate.php?act=audit&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">审核</a>
									<?php else: ?>
										<a href="city_operate.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">查看</a>
									<?php endif; ?>
								<?php else: ?>
								<a href="city_operate.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">查看</a>
									<?php if ($this->_var['sm_session']['user_rank'] >= 2): ?>
									<a href="city_operate.php?act=audit&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">审核</a>
									<?php endif; ?>
								<?php endif; ?>
							<?php else: ?>
								<?php if (( $this->_var['sm_session']['user_rank'] - $this->_var['item_0_55620500_1329217874']['audit_status'] == 1 )): ?>
								<a href="city_operate.php?act=audit&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">重
									审</a>
								<?php else: ?>
								<a href="city_operate.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>">查看</a>
								<?php endif; ?>
							<?php endif; ?>							
						<?php else: ?>
						<span class="grey999">资料不全</a>
						<?php endif; ?>
						<?php if ($this->_var['sm_session']['user_rank'] >= 4): ?><a onclick="confrim_delete(<?php echo $this->_var['item_0_55620500_1329217874']['ad_id']; ?>)" class="red_color">删除</a><?php endif; ?>
						
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</table>
	</div>

