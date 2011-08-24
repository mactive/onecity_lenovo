<!-- $Id: user_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<div class="main-div">
<form method="post" action="users.php" name="theForm" onsubmit="return validate()">
<table width="100%" >
  <tr>
    <td class="label"><?php echo $this->_var['lang']['username']; ?>:</td>
    <td><?php if ($this->_var['form_action'] == "update"): ?><?php echo $this->_var['user']['user_name']; ?><input type="hidden" name="username" value="<?php echo $this->_var['user']['user_name']; ?>" /><?php else: ?><input type="text" name="username" maxlength="60" value="<?php echo $this->_var['user']['user_name']; ?>" /><?php echo $this->_var['lang']['require_field']; ?><?php endif; ?></td>
  </tr>
  <?php if ($this->_var['form_action'] == "update"): ?>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['user_money']; ?>:</td>
    <td><?php echo $this->_var['user']['formated_user_money']; ?> <a href="account_log.php?act=list&user_id=<?php echo $this->_var['user']['user_id']; ?>&account_type=user_money">[ <?php echo $this->_var['lang']['view_detail_account']; ?> ]</a> </td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['frozen_money']; ?>:</td>
    <td><?php echo $this->_var['user']['formated_frozen_money']; ?> <a href="account_log.php?act=list&user_id=<?php echo $this->_var['user']['user_id']; ?>&account_type=frozen_money">[ <?php echo $this->_var['lang']['view_detail_account']; ?> ]</a> </td>
  </tr>
  <tr>
    <td class="label"><a href="javascript:showNotice('noticeRankPoints');" title="<?php echo $this->_var['lang']['form_notice']; ?>"><img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>"></a> <?php echo $this->_var['lang']['rank_points']; ?>:</td>
    <td><?php echo $this->_var['user']['rank_points']; ?> <a href="account_log.php?act=list&user_id=<?php echo $this->_var['user']['user_id']; ?>&account_type=rank_points">[ <?php echo $this->_var['lang']['view_detail_account']; ?> ]</a> <br /><span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="noticeRankPoints"><?php echo $this->_var['lang']['notice_rank_points']; ?></span></td>
  </tr>
  <tr>
    <td class="label"><a href="javascript:showNotice('noticePayPoints');" title="<?php echo $this->_var['lang']['form_notice']; ?>"><img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>" /></a> <?php echo $this->_var['lang']['pay_points']; ?>:</td>
    <td><?php echo $this->_var['user']['pay_points']; ?> <a href="account_log.php?act=list&user_id=<?php echo $this->_var['user']['user_id']; ?>&account_type=pay_points">[ <?php echo $this->_var['lang']['view_detail_account']; ?> ]</a> <br />
        <span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="noticePayPoints"><?php echo $this->_var['lang']['notice_pay_points']; ?></span></td>
  </tr>
  <?php endif; ?>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['email']; ?>:</td>
    <td><input type="text" name="email" maxlength="60" size="40" value="<?php echo $this->_var['user']['email']; ?>" /><?php echo $this->_var['lang']['require_field']; ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['real_name']; ?>:</td>
    <td><input type="text" name="real_name" maxlength="60" size="40" value="<?php echo $this->_var['user']['real_name']; ?>" /></td>
  </tr>
  <?php if ($this->_var['form_action'] == "insert"): ?>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['password']; ?>:</td>
    <td><input type="password" name="password" maxlength="20" size="20" /><?php echo $this->_var['lang']['require_field']; ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['confirm_password']; ?>:</td>
    <td><input type="password" name="confirm_password" maxlength="20" size="20" /><?php echo $this->_var['lang']['require_field']; ?></td>
  </tr>
  <?php endif; ?>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['region']; ?>:</td>
    <td><input type="text" name="other[msn]" maxlength="60" size="40" value="<?php echo $this->_var['user']['msn']; ?>" /></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['user_rank']; ?>:</td>
    <td><select name="user_rank">
      <option value="0"><?php echo $this->_var['lang']['not_special_rank']; ?></option>
      <?php echo $this->html_options(array('options'=>$this->_var['special_ranks'],'selected'=>$this->_var['user']['user_rank'])); ?>
    </select></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['gender']; ?>:</td>
    <td><?php echo $this->html_radios(array('name'=>'sex','options'=>$this->_var['lang']['sex'],'checked'=>$this->_var['user']['sex'])); ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['birthday']; ?>:</td>
    <td><?php echo $this->html_select_date(array('field_order'=>'YMD','prefix'=>'birthday','time'=>$this->_var['user']['birthday'],'start_year'=>'-100','end_year'=>'+1','display_days'=>'true','month_format'=>'%m')); ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['credit_line']; ?>:</td>
    <td><input name="credit_line" type="text" id="credit_line" value="<?php echo $this->_var['user']['credit_line']; ?>" size="10" /></td>
  </tr>
  <?php if ($this->_var['user']['parent_id']): ?>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['parent_user']; ?>:</td>
    <td><a href="users.php?act=edit&id=<?php echo $this->_var['user']['parent_id']; ?>"><?php echo $this->_var['user']['parent_username']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="users.php?act=remove_parent&id=<?php echo $this->_var['user']['user_id']; ?>"><?php echo $this->_var['lang']['parent_remove']; ?></a></td>
  </tr>
  <?php endif; ?>
    </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['qq']; ?>:</td>
    <td><input type="text" name="other[qq]" maxlength="60" size="40" value="<?php echo $this->_var['user']['qq']; ?>" /></td>
  </tr>
    </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['office_phone']; ?>:</td>
    <td><input type="text" name="other[office_phone]" maxlength="60" size="40" value="<?php echo $this->_var['user']['office_phone']; ?>" /></td>
  </tr>
    </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['home_phone']; ?>:</td>
    <td><input type="text" name="other[home_phone]" maxlength="60" size="40" value="<?php echo $this->_var['user']['home_phone']; ?>" /></td>
  </tr>
    </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['mobile_phone']; ?>:</td>
    <td><input type="text" name="other[mobile_phone]" maxlength="60" size="40" value="<?php echo $this->_var['user']['mobile_phone']; ?>" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" />
      <input type="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" class="button" />
      <input type="hidden" name="act" value="<?php echo $this->_var['form_action']; ?>" />
      <input type="hidden" name="id" value="<?php echo $this->_var['user']['user_id']; ?>" />    </td>
  </tr>
</table>

</form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>

<script language="JavaScript">
<!--

if (document.forms['theForm'].elements['act'] == "insert")
{
  document.forms['theForm'].elements['username'].focus();
}
else
{
  document.forms['theForm'].elements['email'].focus();
}

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}

/**
 * 检查表单输入的数据
 */
function validate()
{
    validator = new Validator("theForm");
    validator.isEmail("email", invalid_email, true);
 
    if (document.forms['theForm'].elements['act'].value == "insert")
    {
        validator.required("username",  no_username);
        validator.required("password", no_password);
        validator.required("confirm_password", no_confirm_password);
        validator.eqaul("password", "confirm_password", password_not_same);
    }

    return validator.passed();
}
//-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>
