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
			
		
				<?php if ($this->_var['act_step'] == "edit_dealer"): ?>
				
					<?php echo $this->fetch('library/edit_dealer.lbi'); ?>
					
				<?php endif; ?>
				
				
				
				<?php if ($this->_var['act_step'] == "view_dealer"): ?>
					<?php echo $this->fetch('library/view_dealer.lbi'); ?>
				<?php endif; ?>
				
			
				
				<?php if ($this->_var['act_step'] == "used_list"): ?>
					<?php echo $this->fetch('library/dealer_used_list.lbi'); ?>
				<?php endif; ?>
				
			
		</div>			
		

		<div class="clear"></div>
		
		<?php echo $this->fetch('library/page_footer.lbi'); ?>
	</div>

</div>

</div>
</body>
</html>
