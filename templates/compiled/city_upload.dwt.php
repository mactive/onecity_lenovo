<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="Generator" content="Sinemall v1.5" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />

<title><?php echo $this->_var['page_title']; ?></title>



<link rel="shortcut icon" href="favicon.ico" />
<link rel="icon" href="animated_favicon.gif" type="image/gif" />
<link href="themes/default/css.css" rel="stylesheet" type="text/css" />
<link href="themes/default/css/grade.css" rel="stylesheet" type="text/css" />
<link href="themes/default/css/overlay-apple.css" rel="stylesheet" type="text/css" />


<?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,utils.js,jquery-1.2.6.js')); ?>
<script src="admin/js/listtable.js" type="text/javascript"></script>
<script src="admin/js/common.js" type="text/javascript"></script>


</head>

<body>
<div id="lite_globalWrapper">
<div id="wrapper">
	<?php echo $this->fetch('library/page_header.lbi'); ?>


	<div id="container">
		<div id="page-left" style="width:168px;">
			
			<?php echo $this->fetch('library/mycity.lbi'); ?>
		</div>
		<div id="page-middle">
	      
			
				<?php if ($this->_var['act_step'] == "upload_file"): ?>
					<div class="yellow_notice" style="width:600px;"><?php echo $this->_var['upload_message']; ?></div>
					<br><a href="city_operate.php?act=confirm_insert" class="confirm_btn f_left">确认写入数据库</a>
					<a href="city_operate.php?act=cancel_insert" class="cancel_btn f_left" style="margin-left:100px;">取消写入</a>
					<br>
				<?php endif; ?>
				
				<?php if ($this->_var['act_step'] == "upload_panel"): ?>
				<div class="audit_info radius_5px" style="width:600px;padding:20px;margin:30px 0px 0px 20px;">
					<div class="font14px grey333" style="margin:20px 0px;">请提交 - FY11财年联想“一城一牌”资源位申报表格 <br>
						可以分几次上传全部的县／市,每一个县市限传5条广告位置信息。
					</div>
				<form method="post" action="city_operate.php" name="theForm" enctype="multipart/form-data">
					<input type="file" name="user_upload_file[]" class="input_s2" size="20">
					<input type="hidden" name="act" value="upload_file" /><br><br>
			    	<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
				</form>
				</div>
				<?php endif; ?>
				
				
				
				
				<?php if ($this->_var['act_step'] == "confirm_insert"): ?>
					<a href="city_operate.php" class="confirm_btn">查看城市，查看上传数据</a><br>
					<div class="yellow_notice"><?php echo $this->_var['insert_message']; ?></div>
					<?php if ($this->_var['problem_array']): ?>
					<div class="table_div">
					<table width="100%" id="lesson-table" class="table_border table_standard" border="1">
						<tr>
							<th><?php echo $this->_var['city_title']['col_1']; ?></th>
							<th><?php echo $this->_var['city_title']['col_2']; ?></th>
							<th><?php echo $this->_var['city_title']['col_3']; ?></th>
							<th><?php echo $this->_var['city_title']['col_4']; ?></th>
							<th><?php echo $this->_var['city_title']['col_5']; ?></th>
							<th><?php echo $this->_var['city_title']['col_6']; ?></th>
							<th><?php echo $this->_var['city_title']['col_7']; ?></th>
							<th>问题状态</th>
					    </tr>
					<?php $_from = $this->_var['problem_array']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?>
						<tr>
							<td><?php echo $this->_var['city']['col_1']; ?></td>
							<td><?php echo $this->_var['city']['col_2']; ?></td>
							<td><?php echo $this->_var['city']['col_3']; ?></td>
							<td><?php echo $this->_var['city']['col_4']; ?></td>
							<td><?php echo $this->_var['city']['col_5']; ?></td>
							<td><?php echo $this->_var['city']['col_6']; ?></td>
							<td><?php echo $this->_var['city']['col_7']; ?></td>
							<td><?php echo $this->_var['city']['temp_status']; ?></td>
						</tr>
					<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</table>
					</div>
					<?php endif; ?>
				<?php endif; ?>
				

				
			
		</div>			
		
		
		<div class="clear"></div>
		
		<?php if ($this->_var['act_step'] == "upload_file"): ?>
		<div class="table_div" <?php if ($this->_var['act_step'] == "upload_file"): ?>style="width:4000px;"<?php endif; ?>>
		<table width="100%" id="lesson-table" class="table_border table_standard" border="1">
			<tr>
				<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
			  	<th><?php echo $this->_var['item']; ?></th>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		    </tr>
		<?php $_from = $this->_var['all_city_content']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?>
			<tr>
				<?php $_from = $this->_var['city']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['city_content'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['city_content']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['city_content']['iteration']++;
?>
				<?php if (($this->_foreach['city_content']['iteration'] - 1) < $this->_var['CONTENT_COLS']): ?>
				<td><?php echo $this->_var['item']; ?></td>
				<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</table>
		</div>
		<?php endif; ?>
		
		<?php echo $this->fetch('library/page_footer.lbi'); ?>
	</div>

</div>

</div>
</body>
</html>
