<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="Generator" content="Sinemall v1.5" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<?php if ($this->_var['auto_redirect']): ?>
<meta http-equiv="refresh" content="3;URL=<?php echo $this->_var['message']['href']; ?>" />
<?php endif; ?>

<title><?php echo $this->_var['page_title']; ?></title>

<link rel="shortcut icon" href="favicon.ico" />
<link rel="icon" href="animated_favicon.gif" type="image/gif" />
<link href="themes/default/css.css" rel="stylesheet" type="text/css" />

<?php echo $this->smarty_insert_scripts(array('files'=>'common.js,jquery-1.2.6.js')); ?>
</head>
<body>
<div id="globalWrapper">  <div style="float:left;"></div>
<div id="wrapper">

	<?php echo $this->fetch('library/page_header.lbi'); ?>


    <div id="container2" style="padding: 20px 20px 20px 180px;">
      <p style="font-size: 14px; font-weight:bold; color: red; width:500px"><?php echo $this->_var['message']['content']; ?></p>
	<p>
		<?php echo $this->_var['message']['detail']; ?>
	</p>
      <p><a href="<?php echo $this->_var['message']['href']; ?>">&gt;&gt; <?php echo $this->_var['message']['link']; ?></a></p>
    </div>
    <?php echo $this->fetch('library/help.lbi'); ?><?php echo $this->fetch('library/page_footer.lbi'); ?></div>
  
  <div style="float:left;"></div></div>

</body>
</html>
