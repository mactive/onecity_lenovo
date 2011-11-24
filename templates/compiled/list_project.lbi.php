	<?php if ($this->_var['sm_session']['user_rank'] == 4): ?>
	<div style="height:25px;margin-top:10px;"><a href="city_project.php?act=add_project" class="add_project"></a></div>
	<?php endif; ?>
	<div class="facebox">
		<table width="100%" class="table_border table_standard" border="1">
		    <tr>
		      	<th><?php echo $this->_var['lang']['project_name']; ?></th>
		      	<th><?php echo $this->_var['lang']['project_status']; ?></th>
			  	<th><?php echo $this->_var['lang']['project_note']; ?></th>
		      	<th><?php echo $this->_var['lang']['start_time']; ?></th>
			  	<th><?php echo $this->_var['lang']['duration_time']; ?></th>
			  	<th><?php echo $this->_var['lang']['city_list']; ?></th>
			  	<th><?php echo $this->_var['lang']['handler']; ?></th>
		    </tr>
		<?php $_from = $this->_var['project_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_93309400_1322111788');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_93309400_1322111788']):
?>
			<tr>
				<td><?php echo $this->_var['item_0_93309400_1322111788']['project_name']; ?></td>
				<td><?php echo $this->_var['item_0_93309400_1322111788']['project_status']; ?>
					总计<span class="underline"> <?php echo $this->_var['item_0_93309400_1322111788']['summary']['city_count']; ?> </span>块牌子<br>
					<?php if ($this->_var['item_0_93309400_1322111788']['project_id'] == 1): ?><span class="underline"> <?php echo $this->_var['item_0_93309400_1322111788']['summary']['write_complete']; ?> </span>块牌子已填写合同信息<br><?php endif; ?>
					<span class="underline"> <?php echo $this->_var['item_0_93309400_1322111788']['summary']['upload']; ?> </span>块牌子已反馈效果<br>
					<span class="underline"> <?php echo $this->_var['item_0_93309400_1322111788']['summary']['confirm']; ?> </span>块牌子换画已经通过审核
				</td>
				<td><?php echo $this->_var['item_0_93309400_1322111788']['project_note']; ?></td>
				<td><?php echo $this->_var['item_0_93309400_1322111788']['start_time']; ?></td>
				<td>
					<?php echo $this->_var['item_0_93309400_1322111788']['duration_time']; ?>天
				</td>
				<td><?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
						<a href="city_project.php?act=list_city_to_select&project_id=<?php echo $this->_var['item_0_93309400_1322111788']['project_id']; ?>" class="list_city_to_select"></a>
					<?php else: ?>
						<a href="city_project.php?act=list_city_to_select&project_id=<?php echo $this->_var['item_0_93309400_1322111788']['project_id']; ?>" class="view_city"></a>
					<?php endif; ?>
				</td>				
				<td>
					<?php if ($this->_var['sm_session']['user_rank'] == 4): ?>
						<a href="city_project.php?act=edit_project&project_id=<?php echo $this->_var['item_0_93309400_1322111788']['project_id']; ?>">修改</a> 
					<?php endif; ?>

					<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
						<a href="city_project.php?act=list_picture&project_id=<?php echo $this->_var['item_0_93309400_1322111788']['project_id']; ?>">广告列表</a>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</table>
	</div>

