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

<?php echo $this->smarty_insert_scripts(array('files'=>'common.js,user.js,transport.js')); ?>

<?php echo $this->smarty_insert_scripts(array('files'=>'jquery-1.2.6.js')); ?>
</head>
<body>
<div id="globalWrapper">
<div id="wrapper">
		<?php echo $this->fetch('library/page_header_index.lbi'); ?>



  <div style="margin-bottom:20px;float:left;width:100%;">
    
    <?php if ($this->_var['action'] == 'login'): ?>
	<script type="text/javascript" language="javascript">
	function activatePlaceholders() {
	var detect = navigator.userAgent.toLowerCase();
	if (detect.indexOf("safari") > 0) return false;
	var inputs = document.getElementsByTagName("input");
	for (var i=0;i<inputs.length;i++) {
	  if (inputs[i].getAttribute("type") == "text") {
	   if (inputs[i].getAttribute("placeholder") && inputs[i].getAttribute("placeholder").length > 0) {
	    inputs[i].value = inputs[i].getAttribute("placeholder");
	    inputs[i].onclick = function() {
	     if (this.value == this.getAttribute("placeholder")) {
	      this.value = "";
	     }
	     return false;
	    }
	    inputs[i].onblur = function() {
	     if (this.value.length < 1) {
	      this.value = this.getAttribute("placeholder");
	     }
	    }
	   }
	  }
	}
	}
	window.onload=function() {
	activatePlaceholders();
	}
	</script>
	
	    
	<div class="">
      <form name="formLogin" action="user.php" method="post" onSubmit="return userLogin()">
		<div style="margin:0px auto;width:362px;height:170px;background:url(<?php echo $this->_var['img_path']; ?>index_login_bg.png) no-repeat;padding-top:55px;">
        <table width="90%" border="0" align="center" class="table_standard" style="margin-left:30px;">
          <tr style="height:35px;">
            <td><input name="username" type="text" size="20" tabindex="1"  placeholder="用户名.." class="input_s1" /></td>
          </tr>
          <tr style="height:50px;">
            <td><input name="password" type="password" size="20" tabindex="2" class="input_s1"/></td>
          </tr>
          <?php if ($this->_var['enabled_captcha']): ?>
          <tr>
            <td align="right"><strong><?php echo $this->_var['lang']['comment_captcha']; ?>:</strong></td>
            <td><input type="text" size="8" name="captcha" />
            <img src="captcha.php?is_login=1&<?php echo $this->_var['rand']; ?>" alt="captcha" style="vertical-align: middle;cursor: pointer;" onClick="this.src='captcha.php?is_login=1&'+Math.random()" /> </td>
          </tr>
          <?php endif; ?>
          <tr>
            <td style="padding:0;"><input type="hidden" name="act" value="act_login" />
              <input type="hidden" name="back_act" value="<?php echo $this->_var['back_act']; ?>" />
              <input type="submit" name="submit" class="index_login_btn" value="" tabindex="3" />
			</td>
          </tr>
        </table>
        </div>
      </form>
	</div>
	
	
  </div>
    <?php endif; ?>
    
    
    <?php if ($this->_var['action'] == 'register'): ?>
    <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
    <div class="title-div">
		<div class="f_left"></div>
		<div class="f_left font14px" style="line-height:22px;padding-left:8px;">会员注册 Sign Up</div>
		<div class="clear"></div>
	</div>
	<br>
	<div class="goods_main_top"></div>
	<div class="goods_main_body" style="width:971px; border:1px solid #e0e0e0;border-top:none;border-bottom:none;">
    <form action="user.php" method="post" name="formUser" onsubmit="return register();">
      <br />
      <table width="70%"  border="0" align="left" class="table_standard">
        <tr>
          <td width="36%" class="right"><strong><?php echo $this->_var['lang']['label_username']; ?>:</strong></td>
          <td width="27%"><input name="username" type="text" id="username" onblur="is_registered(this.value);"/></td>
		  <td width="37%">
            <span id="username_notice" class="label_box"> * 您的会员名 可以是中文</span></td>
        </tr>
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['label_email']; ?>:</strong></td>
          <td><input name="email" type="text" id="email" onblur="checkEmail(this.value);"/></td>
		  <td>
            <span id="email_notice" class="label_box"> * 需要认证邮箱才能查看本站价格 </span></td>
        </tr>
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['label_password']; ?>:</strong></td>
          <td><input name="password" type="password" id="password" onblur="check_password(this.value);" onkeyup="checkIntensity(this.value)" /></td>
		  <td>
            <span id="password_notice" class="label_box"> * 不少于6位的密码</span></td>
        </tr>
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['label_password_intensity']; ?>:</strong></td>
          <td>
            <table width="145" border="0" cellspacing="0" cellpadding="1">
              <tr align="center">
                <td width="33%" id="pwd_lower"><?php echo $this->_var['lang']['pwd_lower']; ?></td>
                <td width="33%" id="pwd_middle"><?php echo $this->_var['lang']['pwd_middle']; ?></td>
                <td width="33%" id="pwd_high"><?php echo $this->_var['lang']['pwd_high']; ?></td>
              </tr>
            </table>
          </td>
			<td>
		  </td>
        </tr>
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['label_confirm_password']; ?>:</strong></td>
          <td><input name="confirm_password" type="password" id="conform_password" onblur="check_conform_password(this.value);" /></td>
		  <td>
            <span id="conform_password_notice" class="label_box"> * 重复输入</span></td>
        </tr>
		<tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_real_name']; ?>:</strong></td>
          <td><input name="other[real_name]" type="text"  /></td>
		  <td>
			<span id="real_name_notice" class="label_box"> 方便发货和更多服务</span></td>
        </tr>
		<tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_office_phone']; ?>:</strong></td>
          <td><input name="other[office_phone]" type="text"  /></td>
		  <td>	<span id="office_phone_notice" class="label_box"> 格式:010-51271062(615)</span></td>
        </tr>
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_mobile_phone']; ?>:</strong></td>
          <td><input name="other[mobile_phone]" type="text"  onblur="check_mobile_phone(this.value);"/></td>
		  <td>
			<span id="mobile_phone_notice" class="label_box"> 方便发货和更多服务</span></td>
        </tr>
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_msn']; ?>:</strong></td>
          <td><input name="other[msn]" type="text" /></td>
		  <td>	<span id="msn_notice" class="label_box"> 网上沟通更及时</span></td>
        </tr>
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_qq']; ?>:</strong></td>
          <td><input name="other[qq]" type="text"  /></td>
		  <td><span id="qq_notice" class="label_box"> 网上沟通更方便</span></td>
        </tr>

      <?php if ($this->_var['enabled_captcha']): ?>
      <tr>
      <td class="right"><strong><?php echo $this->_var['lang']['comment_captcha']; ?>:</strong></td>
      <td><input type="text" size="8" name="captcha" />
      <img src="captcha.php?<?php echo $this->_var['rand']; ?>" alt="captcha" style="vertical-align: middle;cursor: pointer;" onClick="this.src='captcha.php?'+Math.random()" />	</td>
		  <td> </td>
      </tr>
      <?php endif; ?>
        <tr>
          <td>&nbsp;</td>
          <td><label>
            <input name="agreement" type="checkbox" value="1" checked="checked" />
            <b><?php echo $this->_var['lang']['agreement']; ?></b></label></td>
        </tr>
        <tr>
          <td colspan="2" class="center"><input name="act" type="hidden" value="act_register" >
            <input name="Submit" type="submit" value="<?php echo $this->_var['lang']['confirm_register']; ?>"></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><li style="border-bottom: 1px dashed #CECECE;width:150px;"> <a href="user.php?act=login"><?php echo $this->_var['lang']['want_login']; ?></a></li>
            <li style="border-bottom: 1px dashed #CECECE;width:150px;"> <a href="user.php?act=get_password"><?php echo $this->_var['lang']['forgot_password']; ?></a></li></td>
        </tr>
      </table>
      <br />
    </form>
	</div>
	<div class="goods_main_bottom"></div>
	<div style="height:14px;"></div>
	
    <?php endif; ?>
    
	
	
    <?php if ($this->_var['action'] == 'take_register'): ?>
    <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
    <div class="title-div">
		<div class="f_left"></div>
		<div class="f_left font14px" style="line-height:22px;padding-left:8px;">代为客户注册 </div>
		<div class="clear"></div>
	</div>
	<br>
	<div class="goods_main_top"></div>
	<div class="goods_main_body">
    <form action="user.php" method="post" name="formUser" onsubmit="return register();">
      <br />
      <table width="70%"  border="0" align="left" class="table_standard">
		 <tr>
	          <td width="36%" class="right"><strong>所属机构:</strong></td>
	          <td width="27%"><select name="other[agency_id]">
				<option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
							<?php echo $this->html_options(array('options'=>$this->_var['agency_list'])); ?>
			</select></td>
			  <td width="37%">
	            <span id="" class="label_box"> * 联系人的机构</span></td>
	    </tr>
        <tr>
          <td width="36%" class="right"><strong><?php echo $this->_var['lang']['label_username']; ?>:</strong></td>
          <td width="27%"><input name="username" type="text" id="username"  onblur="is_registered(this.value);"/></td>
		  <td width="37%">
            <span id="username_notice" class="label_box"> * 请填写中文全名</span></td>
        </tr>
		<script language='javascript'>
		function getvalue()
		{
		var text1=document.getElementById("username").value;
		document.getElementById("other[real_name]").value=text1;

		}
		</script>
		<tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_contact_name']; ?>:</strong></td>
          <td><input name="other[real_name]"  id="other[real_name]" type="text" onfocus="getvalue();" /></td>
		  <td>
			<span id="real_name_notice" class="label_box"> 方便发货和更多服务</span></td>
        </tr>
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['label_email']; ?>:</strong></td>
          <td><input name="email" type="text" id="email" onblur="checkEmail(this.value);"/></td>
		  <td>
            <span id="email_notice" class="label_box"> * 需要认证邮箱才能查看本站价格 </span></td>
        </tr>
		<tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_mobile_phone']; ?>:</strong></td>
          <td><input name="other[mobile_phone]" type="text"  onblur="check_mobile_phone(this.value);"/></td>
		  <td>
			<span id="mobile_phone_notice" class="label_box"> 方便发货和更多服务</span></td>
        </tr>
		<tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_office_phone']; ?>:</strong></td>
          <td><input name="other[office_phone]" type="text"  /></td>
		  <td>	<span id="office_phone_notice" class="label_box"> 格式:010-51271062(615)</span></td>
        </tr>

        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_msn']; ?>:</strong></td>
          <td><input name="other[msn]" type="text" /></td>
		  <td>	<span id="msn_notice" class="label_box"> 网上沟通更及时</span></td>
        </tr>
		
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_qq']; ?>:</strong></td>
          <td>	<input name="other[qq]" type="text"  />
				<input name="other[user_rank]" type="hidden" value="<?php echo $this->_var['rank_agency']; ?>">
				<input name="other[is_validated]" type="hidden" value="1">
				<input name="take_register" type="hidden" value="1">
				
		  </td>
		  <td><span id="qq_notice" class="label_box"> 网上沟通更方便</span></td>
        </tr>
		
		<tr>
          <td class="right"><strong><?php echo $this->_var['lang']['label_password']; ?>:</strong></td>
          <td><input name="password" type="password" id="password" value="123456" onblur="check_password(this.value);" /></td>
		  <td>
            <span id="password_notice" class="label_box"> * 不少于6位的密码</span></td>
        </tr>
        
        <tr>
          <td class="right"><strong><?php echo $this->_var['lang']['label_confirm_password']; ?>:</strong></td>
          <td><input name="confirm_password" type="password" value="123456" id="conform_password" onblur="check_conform_password(this.value);" /></td>
		  <td>
            <span id="conform_password_notice" class="label_box"> * 重复输入</span></td>
        </tr>

      <?php if ($this->_var['enabled_captcha']): ?>
      <tr>
      <td class="right"><strong><?php echo $this->_var['lang']['comment_captcha']; ?>:</strong></td>
      <td><input type="text" size="8" name="captcha" />
      <img src="captcha.php?<?php echo $this->_var['rand']; ?>" alt="captcha" style="vertical-align: middle;cursor: pointer;" onClick="this.src='captcha.php?'+Math.random()" />	</td>
		  <td> </td>
      </tr>
      <?php endif; ?>
        <tr>
          <td>&nbsp;</td>
          <td><label>
            <input name="agreement" type="checkbox" value="1" checked="checked" />
            <b><?php echo $this->_var['lang']['agreement']; ?></b></label></td>
        </tr>
        <tr>
          <td colspan="2" class="center">
			<input name="act" type="hidden" value="act_register">			
            <input name="Submit" type="submit" value="<?php echo $this->_var['lang']['confirm_register']; ?>"></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
       
      </table>
      <br />
    </form>
	</div>
	<div class="goods_main_bottom"></div>
	<div style="height:14px;"></div>
	
    <?php endif; ?>
    


	
    <?php if ($this->_var['action'] == 'send_register'): ?>
    <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
    <div class="title-div">
		<div class="f_left"></div>
		<div class="f_left font14px" style="line-height:22px;padding-left:8px;"><?php echo $this->_var['register_title']; ?> </div>
		<div class="clear"></div>
	</div>
	<br>
	<div class="goods_main_top"></div>
	<div class="goods_main_body">
    <form action="user.php" method="post" name="formUser" onsubmit="return register();">
      <br />
      <table width="70%"  border="0" align="left" class="table_standard">
        <tr>
          <td width="36%" class="right"><strong>所选课程:</strong></td>
          <td width="27%">	<select name="course">
				<option value="Pro Tools 101">Pro Tools 101</option>
				<option value="Pro Tools 110">Pro Tools 110</option>
				<option value="Pro Tools 201">Pro Tools 201</option>
			</select></td>
		  <td width="37%">
            <span id="username_notice" class="label_box"> * 按照难度从上到下</span></td>
        </tr>
        <tr>
          <td width="36%" class="right"><strong>姓名:</strong></td>
          <td width="27%"><input name="username" type="text" id="username"/></td>
		  <td width="37%">
            <span id="username_notice" class="label_box"> * 请填写中文全名</span></td>
        </tr>
        <tr>
          <td width="36%" class="right"><strong>性别:</strong></td>
          <td width="27%"><input name="sex" type="text" id="sex"/></td>
		  <td width="37%">
            <span id="username_notice" class="label_box"> * 先生/女士</span></td>
        </tr>
		<tr>
          <td class="right"><strong>身份证:</strong></td>
          <td>	<input name="id_card" type="text"  />
		  </td>
		  <td><span id="qq_notice" class="label_box">* 注册考试号需要</span></td>
        </tr>
		
		<tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_mobile_phone']; ?>:</strong></td>
          <td><input name="[mobile_phone]" type="text"  onblur="check_mobile_phone(this.value);"/></td>
		  <td>
			<span id="mobile_phone_notice" class="label_box"> * 方便于您联系</span></td>
        </tr>
		<tr>
          <td class="right"><strong><?php echo $this->_var['lang']['other_office_phone']; ?>:</strong></td>
          <td><input name="office_phone" type="text"  /></td>
		  <td>	<span id="office_phone_notice" class="label_box"> 格式:010-51271062(615)</span></td>
        </tr>
		<tr>
          <td class="right"><strong><?php echo $this->_var['lang']['label_email']; ?>:</strong></td>
          <td><input name="email" type="text" id="email" onblur="checkEmail(this.value);"/></td>
		  <td>
            <span id="email_notice" class="label_box"> * 正确填写邮箱才能收到反馈信息 </span></td>
        </tr>
		<tr>
          <td class="right"><strong>公司/机构名称:</strong></td>
          <td><input name="agency_name"  id="agency_name" type="text" /></td>
		  <td>
			<span id="real_name_notice" class="label_box"> &nbsp;</span></td>
        </tr>
		<tr>
          <td class="right"><strong>职位:</strong></td>
          <td><input name="Position" type="text" /></td>
		  <td>
			<span id="real_name_notice" class="label_box">&nbsp; </span></td>
        </tr>
		<tr>
          <td class="right"><strong>地址:</strong></td>
          <td><input name="address"  type="text" /></td>
		  <td>
			<span id="real_name_notice" class="label_box"> 方便投递纸质信息</span></td>
        </tr>
        
        <tr>
          <td colspan="2" class="center">
			
			<input name="send_register" type="hidden" value="1">			
			<input name="act" type="hidden" value="act_send_register">			
            <input name="Submit" type="submit" value="<?php echo $this->_var['lang']['confirm_register']; ?>"></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
       
      </table>
      <br />
    </form>
	</div>
	<div class="goods_main_bottom"></div>
	<div style="height:14px;"></div>
	
    <?php endif; ?>
    


    
    <?php if ($this->_var['action'] == 'get_password'): ?>
    <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
    <script type="text/javascript">
    <?php $_from = $this->_var['lang']['password_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
      var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </script>
    <div class="title-div"><img src="themes/default/images/forget.gif" width="158" height="39"  alt="" /></div>
    <div class="content-div" style="width:970px;">
      <form action="user.php" method="post" name="getPassword" onsubmit="return submitPwdInfo();">
        <br />
        <table width="70%" border="0" align="center">
          <tr>
            <td colspan="2" align="center"><strong><?php echo $this->_var['lang']['username_and_email']; ?></strong></td>
          </tr>
          <tr>
            <td width="39%" align="right"><?php echo $this->_var['lang']['username']; ?>:</td>
            <td width="61%"><input name="user_name" type="text" size="30" /></td>
          </tr>
          <tr>
            <td align="right"><?php echo $this->_var['lang']['email']; ?>:</td>
            <td><input name="email" type="text" size="30" /></td>
          </tr>
          <tr>
            <td colspan="2" align="center"><input type="hidden" name="act" value="send_pwd_email" />
              <input type="submit" name="submit" value="<?php echo $this->_var['lang']['confirm_submit']; ?>" />
              <input name="button" type="button" onclick="history.back()" value="<?php echo $this->_var['lang']['back_page_up']; ?>" />            </td>
          </tr>
        </table>
        <br />
      </form>
    </div>
    <div class="clear"></div>
	<?php endif; ?>
    <?php if ($this->_var['action'] == 'reset_password'): ?>
    <script type="text/javascript">
    <?php $_from = $this->_var['lang']['password_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
      var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </script>
    <div class="title-div"><img src="themes/default/images/forget.gif" width="158" height="39"  alt="" /></div>
    <form action="user.php" method="post" name="getPassword2" onSubmit="return submitPwd()">
      <br />
      <table width="80%" border="0" align="center">
        <tr>
          <td><?php echo $this->_var['lang']['new_password']; ?></td>
          <td><input name="new_password" type="password" size="25" /></td>
        </tr>
        <tr>
          <td><?php echo $this->_var['lang']['confirm_password']; ?>:</td>
          <td><input name="confirm_password" type="password" size="25" /></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><input type="hidden" name="act" value="act_edit_password" />
            <input type="hidden" name="uid" value="<?php echo $this->_var['uid']; ?>" />
            <input type="hidden" name="code" value="<?php echo $this->_var['code']; ?>" />
            <input type="submit" name="submit" value="<?php echo $this->_var['lang']['confirm_submit']; ?>" />          </td>
        </tr>
      </table>
      <br />
    </form>
    <?php endif; ?>
	
<?php echo $this->fetch('library/page_footer.lbi'); ?></div>

</div>
</div>
<script type="text/javascript">
var process_request = "<?php echo $this->_var['lang']['process_request']; ?>";
<?php $_from = $this->_var['lang']['passport_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var username_exist = "<?php echo $this->_var['lang']['username_exist']; ?>";
</script>
</body>
</html>
