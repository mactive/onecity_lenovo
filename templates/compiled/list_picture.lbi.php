	<div style="height:25px;margin-top:10px;"><a href="city_project.php?act=add_picture&project_id=<?php echo $this->_var['project_info']['project_id']; ?>" class="add_picture"></a></div>
	<div class="facebox">
		<table width="100%" class="table_border table_standard" border="1">
		    <tr>
		      	<th><?php echo $this->_var['lang']['picture_id']; ?></th>
		      	<th><?php echo $this->_var['lang']['pic_name']; ?></th>
		      	<th><?php echo $this->_var['lang']['pic_note']; ?></th>
				<th><?php echo $this->_var['lang']['pic_thumb']; ?></th>
			  	<th><?php echo $this->_var['lang']['pic_type']; ?></th>
			  	<th><?php echo $this->_var['lang']['file_url']; ?></th>
		      	<th><?php echo $this->_var['lang']['upload_time']; ?></th>
			  	<th><?php echo $this->_var['lang']['handler']; ?></th>
		    </tr>
		<?php $_from = $this->_var['picture_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_33837400_1314285838');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_33837400_1314285838']):
?>
			<tr>
				<td><?php echo $this->_var['item_0_33837400_1314285838']['picture_id']; ?></td>
				<td><?php echo $this->_var['item_0_33837400_1314285838']['pic_name']; ?></td>
				<td><?php echo $this->_var['item_0_33837400_1314285838']['pic_note']; ?></td>
				<td><img src="<?php echo $this->_var['item_0_33837400_1314285838']['pic_thumb']; ?>" height="100" ></td>
				<td><?php echo $this->_var['lang']['pic_type_select'][$this->_var['item_0_33837400_1314285838']['pic_type']]; ?></td>
				<td><a href="<?php echo $this->_var['item_0_33837400_1314285838']['file_url']; ?>">下载地址</a></td>
				<td><?php echo $this->_var['item_0_33837400_1314285838']['upload_time']; ?></td>
				<td>
					<a href="city_project.php?act=view_picture&picture_id=<?php echo $this->_var['item_0_33837400_1314285838']['picture_id']; ?>">查看</a> &nbsp;&nbsp;|&nbsp;&nbsp;
					<a href="city_project.php?act=edit_picture&picture_id=<?php echo $this->_var['item_0_33837400_1314285838']['picture_id']; ?>">修改</a> &nbsp;&nbsp;|&nbsp;&nbsp; 
					<a href="city_project.php?act=delete_picture&picture_id=<?php echo $this->_var['item_0_33837400_1314285838']['picture_id']; ?>">删除</a>
				</td>
			</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</table>
	</div>

