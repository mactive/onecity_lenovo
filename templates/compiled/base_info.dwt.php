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
<link href="themes/default/css/overlay-apple.css" rel="stylesheet" type="text/css" />


<?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,utils.js,jquery.tools.min.js')); ?>
<script src="admin/js/listtable.js" type="text/javascript"></script>
<script src="admin/js/common.js" type="text/javascript"></script>
<script type="text/javascript">   

		//点击客户名称 指定客户
		function select_request_city(projectID,adID,is_ADD)
		{
			
			var filters = new Object;
			filters.project_id = projectID;
			filters.ad_id = adID;
			filters.is_add = is_ADD;
			Ajax.call("city_project.php?act=select_request_city", filters, function(result)
		  	{
				if (result.content)
			      {
					  document.getElementById('ad_div'+adID).innerHTML = result.content;
			      }
		  }, "GET", "JSON");
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
				<table width="100%" style="margin:10px auto;" class="table_standard table_border" border="1">
					<tr><td>
						<img src="themes/default/images/green_arrow.png" alt=""/>
						</td>
						<td>
						 搜索:
					    	<?php echo $this->_var['lang']['region']; ?> <input name="region_name" type="text" id="region_name" size="10" value="<?php echo $this->_var['filter']['region_name']; ?>" />&nbsp;&nbsp;&nbsp;
					    	<?php echo $this->_var['lang']['county']; ?> <input name="county_name" type="text" id="county_name" size="10" value="<?php echo $this->_var['filter']['county_name']; ?>" />&nbsp;&nbsp;&nbsp;
					    	<?php echo $this->_var['lang']['market_level']; ?> <input name="market_level" type="text" id="market_level" size="4" /> &nbsp;&nbsp;&nbsp;
							审核情况
							<select id="audit_status">
						      	<option value='0'><?php echo $this->_var['lang']['select_please']; ?></option>
								<?php echo $this->html_options(array('options'=>$this->_var['lang']['audit_status_select'],'selected'=>$this->_var['filter']['audit_status'])); ?>
							</select>
							&nbsp;&nbsp;&nbsp;
							<span <?php if ($this->_var['filter']['has_new'] == 1): ?>style="display:none;"<?php endif; ?>>
							<br>
							是否包含<?php echo $this->_var['lang']['is_xz']; ?> 
							<select name="has_new" id="has_new">
								<option value='0'><?php echo $this->_var['lang']['select_please']; ?></option>
						      	<option value='1' <?php if ($this->_var['filter']['has_new'] == 1): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['lang']['is_xz']; ?></option>					    
								<option value='2' <?php if ($this->_var['filter']['has_new'] == 2): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['lang']['is_all']; ?></option>
						      	<option value='3' <?php if ($this->_var['filter']['has_new'] == 3): ?>selected="selected"<?php endif; ?>><?php echo $this->_var['lang']['is_no_xz']; ?></option>					    
							</select>
							</span>
							&nbsp;&nbsp;&nbsp;
						</td>

					<td style="padding-left:30px;"><input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="btn" /> <input type="reset" value="重置" class="btn" /></td>
					</tr>

				</table>
			  </form>
			</div>
			<div class="yellow_notice" style="font-size:14px;color:#ff0000;">以下的广告牌都是通过审核的。请按照合同内容需要重新检查的广告牌的基础信息</div>
			
			<form method="post" name="listForm"  onsubmit="return confirmSubmit(this)">
			
			<div id="listDiv" class="table_div">
			<?php endif; ?>
				<table width="100%" id="lesson-table" class="table_border table_standard" border="1">
				    <tr>
					  	<th width="35"><?php echo $this->_var['lang']['is_xz']; ?></th>
					  	<th><?php echo $this->_var['lang']['region']; ?></th>
				      	<th width="50"><?php echo $this->_var['lang']['province']; ?></th>
				      	<th><?php echo $this->_var['lang']['county']; ?></th>
				      	<th><?php echo $this->_var['lang']['market_level']; ?></th>
					  	<th>是否检查过</th>
						<th>寄出材料</th>
						<th>收到材料</th>
					  	<th><?php echo $this->_var['lang']['audit_2']; ?>审核</th>
						<th>操作</th>
						
				    </tr>
				<?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');$this->_foreach['index_idea'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['index_idea']['total'] > 0):
    foreach ($_from AS $this->_var['city']):
        $this->_foreach['index_idea']['iteration']++;
?>
					<tr <?php if ($this->_var['city']['base_info_changed'] == 1): ?>class="city_upload"<?php endif; ?>>
						<td><?php if ($this->_var['city']['has_new']): ?><span class="red-block">新增</span><?php endif; ?></td>
						<td><?php echo $this->_var['city']['region']; ?></td>
						<td><?php echo $this->_var['city']['province']; ?></td>
						<td><?php echo $this->_var['city']['county']; ?></td>
						<td><?php echo $this->_var['city']['market_level']; ?></td>
						<td>
								<?php if ($this->_var['city']['base_info_changed'] == 1): ?>
									已检查
								<?php else: ?>
									未检查									
								<?php endif; ?>						
						</td>
						<td>
							<?php $_from = $this->_var['city']['send_time']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['send'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['send']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['send']['iteration']++;
?>
								<?php echo $this->_var['item']['time']; ?>[<?php echo $this->_foreach['send']['iteration']; ?>]<br>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							
							<?php $_from = $this->_var['city']['receive_time']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['receive'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['receive']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['receive']['iteration']++;
?>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							
							<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
								<?php if ($this->_var['city']['base_info_modify'] == 2 || ( $this->_var['city']['base_info_modify'] == 0 && $this->_var['city']['audit_note'] != "" )): ?>
									<?php if ($this->_foreach['receive']['total'] == $this->_foreach['send']['total']): ?>
									<a href="city_base_info.php?act=re_send_material&ad_id=<?php echo $this->_var['city']['ad_id']; ?>" target="_blank">重新寄出</a>
									<?php endif; ?>
								<?php else: ?>
									<?php if (! $this->_var['city']['send_time']): ?>
									<a href="city_base_info.php?act=send_material&ad_id=<?php echo $this->_var['city']['ad_id']; ?>" target="_blank">寄出</a>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>

							
							
							
						</td>
						<td>
							<?php $_from = $this->_var['city']['receive_time']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['receive'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['receive']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['receive']['iteration']++;
?>
								<?php echo $this->_var['item']['time']; ?>[<?php echo $this->_foreach['receive']['iteration']; ?>]<br>
							<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							
							<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
								<?php if ($this->_var['city']['base_info_modify'] == 2 || ( $this->_var['city']['base_info_modify'] == 0 && $this->_var['city']['audit_note'] != "" )): ?>
									<?php if ($this->_foreach['receive']['total'] < $this->_foreach['send']['total']): ?>
										<a href="city_base_info.php?act=re_receive_material&ad_id=<?php echo $this->_var['city']['ad_id']; ?>" target="_blank">重新收到</a>
									<?php endif; ?>
								<?php else: ?>
									<?php if (! $this->_var['city']['receive_time']): ?>
									<a href="city_base_info.php?act=receive_material&ad_id=<?php echo $this->_var['city']['ad_id']; ?>" target="_blank">收到</a>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
						
							
						</td>
						
						
						<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
						<?php else: ?>
						<?php endif; ?>
						
						
						<td>
							<?php if ($this->_var['city']['audit_note']): ?>
								<?php if ($this->_var['city']['audit_note'] == "审核通过"): ?>
								<a class="audit_confirm" href="city_base_info.php?act=update_ad_info&ad_id=<?php echo $this->_var['city']['ad_id']; ?>&project_id=9"></a>
								<?php else: ?>
								
									<?php if ($this->_var['city']['base_info_modify'] == 1 || $this->_var['city']['base_info_modify'] == 2): ?>
									<a class="audit_cancel" href="city_base_info.php?act=update_ad_info&ad_id=<?php echo $this->_var['city']['ad_id']; ?>&project_id=9"></a>
									<?php else: ?>
									<a class="audit_idle" href="city_base_info.php?act=update_ad_info&ad_id=<?php echo $this->_var['city']['ad_id']; ?>&project_id=9"></a>
									<?php endif; ?>
									
								<?php endif; ?>
								
							<?php else: ?>
								<?php if ($this->_var['city']['send_time']): ?>
									<?php if ($this->_var['city']['receive_time']): ?>
									已收到<a class="audit_idle" href="city_base_info.php?act=update_ad_info&ad_id=<?php echo $this->_var['city']['ad_id']; ?>&project_id=9"></a>
									<?php else: ?>
									等待收件
									<?php endif; ?>
								<?php else: ?>
									<?php if ($this->_var['city']['receive_time']): ?>
									已收到 <a class="audit_idle" href="city_base_info.php?act=update_ad_info&ad_id=<?php echo $this->_var['city']['ad_id']; ?>&project_id=9"></a>
									<?php else: ?>
									等待寄出
									<?php endif; ?>
									
								<?php endif; ?>
								
							<?php endif; ?>

						</td>
						<td>
							<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
								<?php if ($this->_var['city']['base_info_modify'] == 1 || $this->_var['city']['base_info_modify'] == 2): ?>
									<a href="city_base_info.php?act=update_ad_info&ad_id=<?php echo $this->_var['city']['ad_id']; ?>&project_id=9">检查,修改</a>
								<?php else: ?>
									<a href="city_base_info.php?act=update_ad_info&ad_id=<?php echo $this->_var['city']['ad_id']; ?>&project_id=9">查看</a>
								<?php endif; ?>
							<?php endif; ?>
							<?php if ($this->_var['sm_session']['user_rank'] == 2 && $this->_var['city']['base_info_changed'] == 1): ?>
							<a href="city_base_info.php?act=base_info_audit&ad_id=<?php echo $this->_var['city']['ad_id']; ?>&project_id=9">审核</a>
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
			  listTable.query = "query_ad_list";
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
			      listTable.filter['project_id'] = 9;
			      listTable.filter['audit_status'] = document.getElementById("audit_status").value;
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
