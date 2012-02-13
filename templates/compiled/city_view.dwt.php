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


<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,transport.js')); ?>

<script src="admin/js/listtable.js" type="text/javascript"></script>
<script src="admin/js/common.js" type="text/javascript"></script>
<script type="text/javascript">   
	function confrim_delete(ad_ID){
		var con = confirm("确认是否删除这条记录?");
		
		if(<?php echo $this->_var['sm_session']['user_rank']; ?>  >= 4 && con == true){
			var filters = new Object;
			filters.ad_id = ad_ID;
			
				Ajax.call("city_operate.php?act=delete_ad", filters, function(result)
			  	{
					if (result.content)
				      {
						alert(result.message);
						//var new_location = "city_operate.php";
						window.location.assign(result.content);
						  //document.getElementById('city_div_'+cityID).innerHTML = result.content;
				      }
			  }, "GET", "JSON");
		}
	}
</script>
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
			
			<?php if ($this->_var['act_step'] == "city_ad_list"): ?>
			
				<?php echo $this->fetch('library/city_ad_list.lbi'); ?>
				
			<?php endif; ?>
			
			<?php if ($this->_var['act_step'] == "view_log"): ?>
			
				<?php echo $this->fetch('library/view_log.lbi'); ?>
				
			<?php endif; ?>
			
	      
			
				<?php if ($this->_var['act_step'] == "edit_ad"): ?>
				
					<?php echo $this->fetch('library/edit_ad.lbi'); ?>
					
				<?php endif; ?>
				
				
				
				<?php if ($this->_var['act_step'] == "view_ad"): ?>
									
					<div class="f_left">
						<a class="back_url" href="city_operate.php?act=city_ad_list&city_id=<?php echo $this->_var['ad_info']['city_id']; ?>"></a>
					</div>
					
					
					<?php if ($this->_var['ad_info']['is_new']): ?>
					<?php echo $this->fetch('library/overlap_info.htm'); ?>
					<?php else: ?>
					<?php echo $this->fetch('library/renew_overlap_info.htm'); ?>
					<?php endif; ?>
					
					<?php echo $this->fetch('library/audit_path.htm'); ?>
					
					<?php $_from = $this->_var['photo_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
						<div style="width:160px;height:160px;text-align:center;float:left;margin:10px;">
						<a href="<?php echo $this->_var['item']['img_url']; ?>" target="_blank" class="city_photo"><img src="<?php echo $this->_var['item']['thumb_url']; ?>"></a>
						<?php echo $this->_var['lang']['city_photo'][$this->_var['item']['img_sort']]; ?>
						</div>
					<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>					
					
					<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item']):
?>
					<div class="city_info radius_5px">
						<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item']; ?></div>
						<div class="f_left right_content"><?php echo $this->_var['ad_detail'][$this->_var['k']]; ?></div>	
						<div class="clear"></div>
					</div>
					<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					
				<?php endif; ?>
				
				<?php if ($this->_var['act_step'] == "upload_photo"): ?>
					<?php echo $this->fetch('library/upload_city_photo.lbi'); ?>
				<?php endif; ?>
				
				<?php if ($this->_var['act_step'] == "audit"): ?>
				<?php echo $this->fetch('library/city_audit.lbi'); ?>
				<?php endif; ?>
				
				<?php if ($this->_var['act_step'] == "querenlv"): ?>
				<?php echo $this->fetch('library/city_querenlv.lbi'); ?>
				<?php endif; ?>
				
				
				<?php if ($this->_var['act_step'] == "new_querenlv"): ?>
				<?php echo $this->fetch('library/new_city_querenlv.lbi'); ?>
				<?php endif; ?>
				
				
				<?php if ($this->_var['act_step'] == "project_querenlv"): ?>
				<?php echo $this->fetch('library/project_querenlv.lbi'); ?>
				<?php endif; ?>
				
				
				<?php if ($this->_var['act_step'] == "new_project_querenlv"): ?>
				<?php echo $this->fetch('library/new_project_querenlv.lbi'); ?>
				<?php endif; ?>
				
				<?php if ($this->_var['act_step'] == "export_page"): ?>
				<?php echo $this->fetch('library/export_page.lbi'); ?>
				<?php endif; ?>
				
				
				

				
			
		</div>			
		

		<div class="clear"></div>
		
		<?php echo $this->fetch('library/page_footer.lbi'); ?>
	</div>

</div>

</div>
</body>
</html>
