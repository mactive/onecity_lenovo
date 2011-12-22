<!-- $Id: category_list.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>

<form method="post" action="" name="listForm">
<!-- start ad position list -->
<div class="list-div" id="listDiv">
<?php endif; ?>

<table width="100%" cellspacing="1" cellpadding="2" id="list-table">
  <tr>
    <th><?php echo $this->_var['lang']['cat_name']; ?></th>
    <th>城市级别</th>
    <th><?php echo $this->_var['lang']['measure_unit']; ?></th>
	<th><?php echo $this->_var['lang']['cat_logo']; ?></th>
    <th><?php echo $this->_var['lang']['is_show']; ?></th>
    <th><?php echo $this->_var['lang']['sort_order']; ?></th>
    <th><?php echo $this->_var['lang']['handler']; ?></th>
  </tr>
  <?php $_from = $this->_var['cat_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');if (count($_from)):
    foreach ($_from AS $this->_var['cat']):
?>
  <tr align="center" class="<?php echo $this->_var['cat']['level']; ?>">
    <td align="left" class="first-cell" >
      <?php if ($this->_var['cat']['is_leaf'] != 1): ?>
      <img src="images/menu_minus.gif" width="9" height="9" border="0" style="margin-left:<?php echo $this->_var['cat']['level']; ?>em" onclick="rowClicked(this)" />
      <?php else: ?>
      <img src="images/menu_arrow.gif" width="9" height="9" border="0" style="margin-left:<?php echo $this->_var['cat']['level']; ?>em" />
      <?php endif; ?>
      <span><a href="category.php?act=list&amp;cat_id=<?php echo $this->_var['cat']['cat_id']; ?>"><?php echo $this->_var['cat']['cat_name']; ?></a></span>
    </td>
    <td width="7%"><?php echo $this->_var['cat']['market_level']; ?></td>
    <td width="7%"><span onclick="listTable.edit(this, 'edit_measure_unit', <?php echo $this->_var['cat']['cat_id']; ?>)"><!-- <?php if ($this->_var['cat']['measure_unit']): ?> --><?php echo $this->_var['cat']['measure_unit']; ?><!-- <?php else: ?> -->&nbsp;&nbsp;&nbsp;&nbsp;<!-- <?php endif; ?> --></span></td>
	<td width="6%"><a href="../data/categorylogo/<?php echo $this->_var['cat']['cat_logo']; ?>" target="_brank"><img src="images/picflag.gif" width="16" height="16" border="0" /></a></span></td>	  
    <td width="10%"><img src="images/<?php if ($this->_var['cat']['is_show'] == '1'): ?>yes<?php else: ?>no<?php endif; ?>.gif" onclick="listTable.toggle(this, 'toggle_is_show', <?php echo $this->_var['cat']['cat_id']; ?>)" /></td>
    	<td width="10%" align="right"><span onclick="listTable.edit(this, 'edit_sort_order', <?php echo $this->_var['cat']['cat_id']; ?>)"><?php echo $this->_var['cat']['sort_order']; ?></span></td>
    <td width="24%" align="center">
      <a href="category.php?act=edit&amp;cat_id=<?php echo $this->_var['cat']['cat_id']; ?>"><?php echo $this->_var['lang']['edit']; ?></a> |
      <a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['cat']['cat_id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="<?php echo $this->_var['lang']['remove']; ?>"><?php echo $this->_var['lang']['remove']; ?></a>
    </td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>

<?php if ($this->_var['full_page']): ?>
</div>
</form>


<script language="JavaScript">
<!--

onload = function()
{
  // 开始检查订单
  startCheckOrder();
}

var imgPlus = new Image();
imgPlus.src = "images/menu_plus.gif";

/**
 * 折叠分类列表
 */
function rowClicked(obj)
{
  obj = obj.parentNode.parentNode;

  var tbl = document.getElementById("list-table");
  var lvl = parseInt(obj.className);
  var fnd = false;

  for (i = 0; i < tbl.rows.length; i++)
  {
      var row = tbl.rows[i];

      if (tbl.rows[i] == obj)
      {
          fnd = true;
      }
      else
      {
          if (fnd == true)
          {
              var cur = parseInt(row.className);
              if (cur > lvl)
              {
                  row.style.display = (row.style.display != 'none') ? 'none' : (Browser.isIE) ? 'block' : 'table-row';
              }
              else
              {
                  fnd = false;
                  break;
              }
          }
      }
  }

  for (i = 0; i < obj.cells[0].childNodes.length; i++)
  {
      var imgObj = obj.cells[0].childNodes[i];
      if (imgObj.tagName == "IMG" && imgObj.src != 'images/menu_arrow.gif')
      {
          imgObj.src = (imgObj.src == imgPlus.src) ? 'images/menu_minus.gif' : imgPlus.src;
      }
  }
}
//-->
</script>


<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>