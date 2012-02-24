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
	function batch_audit(){
		var con = confirm("是否确认批量审核通过");
		
		if(<?php echo $this->_var['sm_session']['user_rank']; ?>  == 5 && con == true){
			
			var filters = new Object;
			filters.confirm = 1;
			
				Ajax.call("city_operate.php?act=batch_audit", filters, function(result)
			  	{
					if (result.content)
				      {
						alert(result.content);
						var new_location = "city_operate.php";
						window.location.assign(new_location);
						  //document.getElementById('city_div_'+cityID).innerHTML = result.content;
				      }
			  }, "GET", "JSON");
		}
	}
	
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

			<div class="form-div">
			  <form action="javascript:searchCRM()" name="searchForm">
				<table width="90%" style="margin:10px auto;" class="table_standard table_border" border="1">
					<tr><td>
						<img src="themes/default/images/green_arrow.png" alt=""/>
						</td>
						<td>
						 搜索:
					    	<?php echo $this->_var['lang']['region']; ?> <input name="region_name" type="text" id="region_name" size="10" value="<?php echo $this->_var['filter']['region_name']; ?>" />&nbsp;&nbsp;&nbsp;
					    	<?php echo $this->_var['lang']['county']; ?> <input name="county_name" type="text" id="county_name" size="10" value="<?php echo $this->_var['filter']['county_name']; ?>" />&nbsp;&nbsp;&nbsp;
					    	<?php echo $this->_var['lang']['market_level']; ?> <input name="market_level" type="text" id="market_level" size="5" value="<?php echo $this->_var['filter']['market_level']; ?>" /> &nbsp;&nbsp;&nbsp;
							<span <?php if ($this->_var['filter']['has_new'] == 1): ?>style="display:none;"<?php endif; ?>>
							是否包含<?php echo $this->_var['lang']['is_xz']; ?>
							<select name="has_new" id="has_new">
						      	<option value='0'><?php echo $this->_var['lang']['select_please']; ?></option>
						      	<option value='1' <?php if ($this->_var['filter']['has_new'] == 1): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['lang']['is_xz']; ?></option>					    
								<option value='2' <?php if ($this->_var['filter']['has_new'] == 2): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['lang']['is_all']; ?></option>
						      	<option value='3' <?php if ($this->_var['filter']['has_new'] == 3): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['lang']['is_no_xz']; ?></option>
							</select>
							
					      	</span>
						</td>
					<td style="padding-left:30px;"><input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" /> <input type="reset" value="重置" class="button" /></td>
					</tr>
				</table>
			  </form>
			</div>
			<?php if ($this->_var['sm_session']['user_rank'] == 5): ?>
			<div><a onclick="batch_audit()" class="batch_audit"></a></div>
			<?php endif; ?>
			<form method="post" name="listForm"  onsubmit="return confirmSubmit(this)">
			
			<div id="listDiv" class="table_div">
			<?php endif; ?>
				<table width="100%" id="lesson-table" class="table_border table_standard" border="1">
				    <tr>
					  	<th width="35"><?php echo $this->_var['lang']['is_xz']; ?></th>
					  	<th><?php echo $this->_var['lang']['region']; ?></th>
				      	<th><?php echo $this->_var['lang']['province']; ?></th>
				      	<th><?php echo $this->_var['lang']['county']; ?></th>
				      	<th><?php echo $this->_var['lang']['market_level']; ?></th>
						<?php if ($this->_var['sm_session']['user_rank'] > 1): ?>
						<th>审核请求</th>
						<?php endif; ?>
					  	<th>上传时间</th>
					  	<th>上传数</th>
					  	<th>照片数</th>
					  	<th>审核数</th>
					  	<th>通过数</th>
					  	<th><?php echo $this->_var['lang']['handler']; ?></th>
				    </tr>
				<?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');$this->_foreach['index_idea'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['index_idea']['total'] > 0):
    foreach ($_from AS $this->_var['city']):
        $this->_foreach['index_idea']['iteration']++;
?>
					<tr <?php if ($this->_var['city']['is_upload']): ?>
								class="city_upload"
						<?php endif; ?>>
						<td><?php if ($this->_var['city']['has_new']): ?><span class="red-block">新增</span><?php endif; ?></td>
						<td><?php echo $this->_var['city']['region']; ?></td>
						<td><?php echo $this->_var['city']['province']; ?></td>
						<td><?php echo $this->_var['city']['county']; ?></td>
						<td><?php echo $this->_var['city']['market_level']; ?></td>
						<?php if ($this->_var['sm_session']['user_rank'] > 1): ?>
						<td>
							<?php if ($this->_var['city']['city_request'] > 0): ?>
							<a href="city_operate.php?act=city_ad_list&city_id=<?php echo $this->_var['city']['cat_id']; ?>">								
								<span class="red-color"><?php echo $this->_var['city']['city_request']; ?>条</span>待审
							</a><?php else: ?>无待审核<?php endif; ?>
							
							<?php $_from = $this->_var['city']['status_summary']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
								<?php if ($this->_var['key'] > 1 && $this->_var['key'] == $this->_var['sm_session']['user_rank']): ?>
									
								<?php endif; ?>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</td>
						<?php endif; ?>
						<td><?php if ($this->_var['city']['is_upload']): ?><?php echo $this->_var['city']['time_summary']; ?><?php else: ?><?php echo $this->_var['lang']['upload_pending']; ?><?php endif; ?></td>
						<td><?php echo $this->_var['city']['ad_count']; ?></td>
						<td><?php echo $this->_var['city']['photo_summary']; ?></td>
						<td><?php echo $this->_var['city']['audit_status_summary']; ?></td>
						<td><?php echo $this->_var['city']['audit_confirm_summary']; ?></td>
						<td><a href="city_operate.php?act=city_ad_list&city_id=<?php echo $this->_var['city']['cat_id']; ?>">查看详情</a></td>
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
			  listTable.query = "query_show";
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
			      listTable.filter['county_name'] = document.getElementById("county_name").value;
			      listTable.filter['market_level'] = document.getElementById("market_level").value;
			      listTable.filter['has_new'] = document.getElementById("has_new").value;
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
