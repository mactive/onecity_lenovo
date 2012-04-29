	<div class="f_left">
		<a class="back_url" href="city_renew.php?region_name=<?php echo $this->_var['city_name']; ?>&has_new=<?php echo $this->_var['has_new']; ?>"></a>
	</div>
	<div class="facebox f_left">
		<table width="100%" class="table_border table_standard" border="1">
		    <tr>
		      	<th width="35"><?php echo $this->_var['lang']['is_xz']; ?></th>
		      	<th><?php echo $this->_var['lang']['county']; ?></th>
		      	<th width="150">具体位置描述</th>
			  	<th>检查时间</th>
		      	<th>照片数量</th>
			  	<th>审核状态</th>
			  	<th width="50">续,改,删</th>
			  	<th width="200"><?php echo $this->_var['lang']['handler']; ?></th>
		    </tr>
		<?php $_from = $this->_var['ad_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_10268900_1335702004');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_10268900_1335702004']):
?>
			<tr <?php if ($this->_var['item_0_10268900_1335702004']['is_upload']): ?>
					<?php if ($this->_var['item_0_10268900_1335702004']['audit_status'] > 1): ?>
						<?php if ($this->_var['item_0_10268900_1335702004']['is_audit_confirm']): ?>
							class="city_audit_confirm"
						<?php else: ?>
							class="city_audit_cancel"
						<?php endif; ?>
					<?php else: ?>
						class="city_upload"
					<?php endif; ?>
				<?php endif; ?>>
				<td><?php if ($this->_var['item_0_10268900_1335702004']['is_new']): ?><span class="red-block">新增</span><?php else: ?><span class="grey999">已有</span><?php endif; ?></td>
				<td><?php echo $this->_var['item_0_10268900_1335702004']['city_name']; ?></td>
				<td><a href="city_renew.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>"><?php echo $this->_var['item_0_10268900_1335702004']['col_7']; ?></a></td>
				<td><?php if ($this->_var['item_0_10268900_1335702004']['is_upload']): ?><?php echo $this->_var['item_0_10268900_1335702004']['user_time']; ?><?php else: ?><?php echo $this->_var['lang']['upload_pending']; ?><?php endif; ?></td>
				<td>
					<span <?php if ($this->_var['item_0_10268900_1335702004']['photo_num']): ?>class="red-color font14px"<?php endif; ?>><?php echo $this->_var['item_0_10268900_1335702004']['photo_num']; ?></span></td>
				<td>
					<?php if ($this->_var['item_0_10268900_1335702004']['audit_status'] > 1): ?>
							<?php echo $this->_var['audit_title'][$this->_var['item_0_10268900_1335702004']['audit_status']]; ?>-
							<?php if ($this->_var['item_0_10268900_1335702004']['is_audit_confirm']): ?>
								<span class="green-color"><?php echo $this->_var['lang']['audit_confirm']; ?></span>
							<?php else: ?>
								<span class="red-color"><?php echo $this->_var['lang']['audit_cancel']; ?></span>
							<?php endif; ?>
						<?php else: ?>
							<?php echo $this->_var['lang']['audit_pending']; ?>
						<?php endif; ?>
				</td>
				<td>
					<?php if ($this->_var['item_0_10268900_1335702004']['is_change']): ?>已修改
					<?php endif; ?>

					<?php if ($this->_var['item_0_10268900_1335702004']['is_renew']): ?>已续签
					<?php endif; ?>

				</td>
				<td>
					<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
						<?php if ($this->_var['item_0_10268900_1335702004']['is_audit_confirm'] == 0): ?>
							<?php if ($this->_var['item_0_10268900_1335702004']['audit_status'] == 1): ?>
							<a href="city_renew.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">查看</a> &nbsp; | &nbsp;
							<a href="city_renew.php?act=edit_ad&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">修改</a> &nbsp; | &nbsp;
							<a href="city_renew.php?act=upload_photo&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">传照片</a>							
							<?php else: ?>
							<a href="city_renew.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">查看</a>
							<?php endif; ?>
						<?php else: ?>
							<?php if ($this->_var['item_0_10268900_1335702004']['audit_status'] == 5): ?>

								<?php if ($this->_var['item_0_10268900_1335702004']['is_renew'] || $this->_var['item_0_10268900_1335702004']['is_change']): ?>
								<a class="btn disabled">续签</a>
								<?php else: ?>
								<a href="city_renew.php?act=renew_ad&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>" class="btn success">续签</a>
								<?php endif; ?>
								 |
								<?php if ($this->_var['item_0_10268900_1335702004']['is_change'] || $this->_var['item_0_10268900_1335702004']['is_renew']): ?>
								<a 	class="btn disabled">修改</a>
								<?php else: ?>
								<a href="city_renew.php?act=edit_ad&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>" class="btn">修改</a>
								<?php endif; ?>
								 |
								<a onclick="confrim_delete(<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>)" class="btn danger">删除</a>
						
							<?php endif; ?>
						<?php endif; ?>
						
					<?php else: ?>
					
						<?php if ($this->_var['item_0_10268900_1335702004']['is_upload'] && $this->_var['item_0_10268900_1335702004']['photo_num']): ?>
							<?php if ($this->_var['item_0_10268900_1335702004']['is_audit_confirm'] == 0): ?>
								<?php if ($this->_var['item_0_10268900_1335702004']['audit_status'] == 1): ?>
									<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
										<a href="city_renew.php?act=audit&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">审核</a>
									<?php else: ?>
										<a href="city_renew.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">查看</a>
									<?php endif; ?>
								<?php else: ?>
								<a href="city_renew.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">查看</a>
									<?php if ($this->_var['sm_session']['user_rank'] >= 2): ?>
									<a href="city_renew.php?act=audit&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">审核</a>
									<?php endif; ?>
								<?php endif; ?>
							<?php else: ?>
								<?php if (( $this->_var['sm_session']['user_rank'] - $this->_var['item_0_10268900_1335702004']['audit_status'] == 1 )): ?>
								<a href="city_renew.php?act=audit&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">重
									审</a>
								<?php else: ?>
								<a href="city_renew.php?act=view_ad&ad_id=<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>">查看</a>
								<?php endif; ?>
							<?php endif; ?>							
						<?php else: ?>
						<span class="grey999">资料不全</a>
						<?php endif; ?>
						<?php if ($this->_var['sm_session']['user_rank'] >= 4): ?><a onclick="confrim_delete(<?php echo $this->_var['item_0_10268900_1335702004']['ad_id']; ?>)" class="red_color">删除</a><?php endif; ?>
						
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</table>
	</div>

