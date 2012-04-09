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


<?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,utils.js,jquery.tools.min.js')); ?>
<script src="admin/js/listtable.js" type="text/javascript"></script>
<script src="admin/js/common.js" type="text/javascript"></script>
<script type="text/javascript"> 	
	$(document).ready(function(){
	//鼠标移到tr上变色  
	            $("table tr").mouseover(function(){  
	                $(this).addClass("hover_tr");  
	            });  
	            $("table tr").mouseout(function(){  
	                $(this).removeClass("hover_tr");  
	            });

	        });  
	
         
</script>

</head>

<body>
<?php if ($this->_var['full_page']): ?>	
<div id="lite_globalWrapper">
<div id="wrapper">
	<?php echo $this->fetch('library/page_header.lbi'); ?>


	<div id="container">
		<div id="page-left" style="width:168px;">
			
			<?php echo $this->fetch('library/mycity.lbi'); ?>
		</div>
		<div id="page-middle">
			<div class="yellow_notice" style="text-align:center;"><?php echo $this->_var['operate_message']; ?></div>
			
			<div class="form-div">
			  <form action="javascript:searchCRM()" name="searchForm">
				<table width="90%" style="margin:10px auto;" class="table_standard table_border" border="1">
					<tr><td>
						<img src="themes/default/images/green_arrow.png" alt=""/>
						</td>
						<td>
						 搜索:
					    	<?php echo $this->_var['lang']['region']; ?> <input name="region_name" type="text" id="region_name" size="10" value="<?php echo $this->_var['filter']['region_name']; ?>" />&nbsp;&nbsp;&nbsp;
					    	<?php echo $this->_var['lang']['dealer_sn']; ?> <input name="dealer_sn" type="text" id="dealer_sn" size="10" value="<?php echo $this->_var['filter']['dealer_sn']; ?>" />&nbsp;&nbsp;&nbsp;
					    	<?php echo $this->_var['lang']['dealer_name']; ?> <input name="dealer_name" type="text" id="dealer_name" size="10" value="<?php echo $this->_var['filter']['dealer_name']; ?>" /> &nbsp;&nbsp;&nbsp;
							是否通过
							<select name="audit_status" id="audit_status">
						      	<option value='9' <?php if ($this->_var['filter']['is_audit'] == 0): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['lang']['select_please']; ?></option>
								<option value='0' ><?php echo $this->_var['lang']['no']; ?></option>
						      	<option value='1' <?php if ($this->_var['filter']['is_audit'] == 1): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['lang']['yes']; ?></option>
							</select>
							
						</td>
					<td style="padding-left:30px;"><input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" /> <input type="reset" value="重置" class="button" /></td>
					</tr>
				</table>
			  </form>
			</div>
			<form method="post" name="listForm"  onsubmit="return confirmSubmit(this)">
			
			<div id="listDiv" class="table_div">
			<?php endif; ?>
				<table width="100%" id="lesson-table" class="table_border table_standard" border="1">
				    <tr>
					  	<th width = "100"><?php echo $this->_var['lang']['region']; ?></th>
				      	<th><?php echo $this->_var['lang']['dealer_summary']; ?></th>
				      	<th><?php echo $this->_var['lang']['dealer_sn']; ?></th>
				      	<th><?php echo $this->_var['lang']['dealer_name']; ?></th>
				      	<th width="70">类型</th>
					  	<th width="50">是否通过</th>
						<th> 查看</th>
					  	<th width="100"><?php echo $this->_var['lang']['handler']; ?></th>
				    </tr>
				<?php $_from = $this->_var['dealer_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['index_idea'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['index_idea']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['index_idea']['iteration']++;
?>
					<tr <?php if ($this->_var['city']['is_upload']): ?>
								class="city_upload"
						<?php endif; ?>>
						<td><?php echo $this->_var['item']['region_name']; ?></td>
						<td><a href="city_dealer.php?act=used_list&dealer_sn=<?php echo $this->_var['item']['dealer_id']; ?>"><?php echo $this->_var['item']['dealer_summary']; ?></a></td>
						<td><?php echo $this->_var['item']['dealer_sn']; ?></td>
						<td><?php echo $this->_var['item']['dealer_name']; ?></td>
						<td><?php echo $this->_var['lang']['is_dealer'][$this->_var['item']['is_dealer']]; ?></td>
						<td><?php if ($this->_var['item']['is_audit']): ?>
							<a class="audit_confirm"></a>
							<?php else: ?>
							<a class="audit_idle"></a>
							<?php endif; ?></td>
						<td>	<a href="city_dealer.php?act=view_dealer&dealer_id=<?php echo $this->_var['item']['dealer_id']; ?>">查看</a> 
						</td>
						<td width=150>
							<a href="city_dealer.php?act=edit_dealer&dealer_id=<?php echo $this->_var['item']['dealer_id']; ?>">修改</a> &nbsp;&nbsp;&nbsp;&nbsp;
							<?php if ($this->_var['item']['is_audit']): ?>
							<a href="city_dealer.php?act=reject_dealer&dealer_id=<?php echo $this->_var['item']['dealer_id']; ?>">否决</a>&nbsp;&nbsp;
							<a href="city_dealer.php?act=confirm_dealer&dealer_id=<?php echo $this->_var['item']['dealer_id']; ?>">重通过</a>
							<?php else: ?>
							<a href="city_dealer.php?act=confirm_dealer&dealer_id=<?php echo $this->_var['item']['dealer_id']; ?>">通过</a>
							<?php endif; ?>
						</td>
					</tr>
			    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</table>
				
				<div id="turn-page" class="area_brand" style="text-indent:50px; margin:10px 0px;background:#ddd;">
			  		<?php echo $this->_var['lang']['total_records']; ?> <span id="totalRecords"><?php echo $this->_var['record_count']; ?></span>
			  		<?php echo $this->_var['lang']['total_pages']; ?> <span id="totalPages"><?php echo $this->_var['page_count']; ?></span>
			  		<?php echo $this->_var['lang']['page_current']; ?> <span id="pageCurrent"><?php echo $this->_var['filter']['page']; ?></span>
			  		<?php echo $this->_var['lang']['page_size']; ?> <?php echo $this->_var['filter']['page_size']; ?>条
			  		<span id="page-link">
			    		<a href="javascript:listTable.gotoPageFirst()"><?php echo $this->_var['lang']['page_first']; ?></a>
			    		<a href="javascript:listTable.gotoPagePrev()"><?php echo $this->_var['lang']['page_prev']; ?></a>
			    		<a href="javascript:listTable.gotoPageNext()"><?php echo $this->_var['lang']['page_next']; ?></a>
			    		<a href="javascript:listTable.gotoPageLast()"><?php echo $this->_var['lang']['page_last']; ?></a>
			    		<select id="gotoPage" onchange="listTable.gotoPage(this.value)">
			      			<?php echo $this->smarty_create_pages(array('count'=>$this->_var['page_count'],'page'=>$this->_var['filter']['page'])); ?>
			    		</select>
			  		</span>
				</div>
				
				
<?php if ($this->_var['full_page']): ?>
			</div>
		 	</form>
		
			
			<script type="text/javascript" language="javascript">
			  <!--
			  listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
			  listTable.pageCount = <?php echo $this->_var['page_count']; ?>;
			  listTable.query = "query_dealer";
			  <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
			  listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
			  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

			  /**
			   * 搜索订单
			   */
			  function searchCRM()
			  {
			      listTable.filter['region_name'] = document.getElementById("region_name").value;
			      listTable.filter['dealer_sn'] = document.getElementById("dealer_sn").value;
			      listTable.filter['dealer_name'] = document.getElementById("dealer_name").value;
			      listTable.filter['audit_status'] = document.getElementById("audit_status").value;
			      listTable.filter['page'] = 1;
			      listTable.loadList();
			  }

			  //-->
			</script>
			
			
		</div>			
		

		<div class="clear"></div>
		
		<?php echo $this->fetch('library/page_footer.lbi'); ?>
	</div>

</div>

</div>
<?php endif; ?>
</body>
</html>
