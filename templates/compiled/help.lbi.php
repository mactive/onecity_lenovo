<?php if ($this->_var['helps']): ?>
<table width="801" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#DADADA" class="clear" style="margin:10px auto 5px auto;">
  <tr>
    <?php $_from = $this->_var['helps']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'help_cat');if (count($_from)):
    foreach ($_from AS $this->_var['help_cat']):
?>
    <td class="help-cat"><img src="themes/default/images/grey_arrow.png" alt="" /><?php echo $this->_var['help_cat']['cat_name']; ?></td>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </tr>
  <tr>
    <?php $_from = $this->_var['helps']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'help_cat');if (count($_from)):
    foreach ($_from AS $this->_var['help_cat']):
?>
    <td bgcolor="#FFFFFF" valign="top"><ul>
        <?php $_from = $this->_var['help_cat']['article']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
        <li><A href="<?php echo $this->_var['item']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['item']['title']); ?>"><?php echo $this->_var['item']['short_title']; ?></A></li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      </ul></td>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </tr>
</table>
<?php endif; ?>
