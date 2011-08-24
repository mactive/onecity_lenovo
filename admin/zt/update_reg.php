<?php
require("../../includes/db_mysql.php");
require("../../includes/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
   if(strtolower($admindir[1])=="admin"){
	require("../check.php");
   }
$db = new CSmysql;
	//接受 请求
	$comid=$HTTP_GET_VARS['comid'];

	$isupdate=$_POST['okupload'];
	if($isupdate<>""){
		//更新数据库
		//接受 表单
			$comid=$_POST['comid'];

			$v1=$_POST['name'];
			$v2=$_POST['sex'];
			$v3=$_POST['comname'];
			$v4=$_POST['status'];
			$v5=$_POST['position'];
			$v6=$_POST['email'];
			$v7=$_POST['telephone'];
			$v8=$_POST["phone"];
			$v9=$_POST["address"];
			$v10=$_POST["other"];
			
			//
			$sqlstr="UPDATE edu_cominfo set name='$v1'";
			$sqlstr=$sqlstr." ,sex='$v2'";
			$sqlstr=$sqlstr." ,comname='$v3'";
			$sqlstr=$sqlstr." ,status='$v4'";
			$sqlstr=$sqlstr." ,position='$v5'";
			$sqlstr=$sqlstr." ,email='$v6'";
			$sqlstr=$sqlstr." ,telephone='$v7'";
			$sqlstr=$sqlstr." ,phone='$v8'";
			$sqlstr=$sqlstr." ,address='$v9'";
			$sqlstr=$sqlstr." ,other='$v10'";
			$sqlstr=$sqlstr." where comid=".$comid;
			//echo $sqlstr;
			
				$db->query($sqlstr);
			
	}
	//查询 数据
	$sqlstr="select * from edu_cominfo";
	$sqlstr=$sqlstr." where comid=$comid ";
		
	$db->query($sqlstr);
	$arr=$db->fetch();
	//初始化 显示对象
	$name=$arr[name];
	$sex=$arr[sex];
	$comname=$arr[comname];
	$status=$arr[status];
	$position = $arr[position];
	$email=$arr[email];
	$telephone=$arr[telephone];
	$phone=$arr[phone];
	$address=$arr[address];
	$other= $arr[other];
?>
<html>
<head>
<title>个人报名更新</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<script src="../js.js"></script>
<script src="../func.js"></script>
<?require("../edit.htm");?>
<style type="text/css">
<!--
.style1 {color: #9900FF}
-->
</style>
</head>
<body bgcolor="#FFFFFF" text="#000000"  onload="HTMLArea.replaceAll(config);">
 <div align="center" class="bblue">
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p><font color="#9c00ce"><a href="registration.php">个人报名管理 </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;</font></font><span class="style1"><a href="group.php">团体报名管理</a></span><font color="#9c00ce"><a href="registration.php"> </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="course.php">课程管理</a>&nbsp;</font></font></p>
</div>
 <form name="form1" method="post" action="<?php echo $PHP_SELF?>"> 
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" class="thin"> 
          <tr> 
            <th width="18%">姓名：</th>
            <td width="82%">
              <input name="name" type="text" class="xiaozi" size="25" value="<?php echo($name)?>"></td> 
          </tr> 
          <tr> 
            <th> 性别：</th>
            <td>
               <input name="sex" type="text" class="xiaozi" size="25" value="<?php echo($sex)?>">
               0为男性，1为女性</td> 
          </tr> 
          <tr> 
            <th> 公司机构名称：</th>
            <td> <input name="comname" type="text" class="xiaozi" size="50" value="<?php echo($comname)?>"></td> 
          </tr> 
          <tr> 
            <th>身份证号码：</th> 
            <td>
              <input name="status" type="text" class="xiaozi" id="status" value="<?php echo($status)?>" size="50"></td> 
          </tr> 
          <tr> 
            <th valign="top"> 职位：</th>
            <td>   <input name="position" type="text" class="xiaozi" size="25" value="<?php echo($position)?>"> </td> 
          </tr> 
          <tr> 
            <th>电子邮件：</th> 
            <td>
             <input name="email" type="text" class="xiaozi" size="25" value="<?php echo($email)?>"></td> 
          </tr> 
          <tr> 
            <th height="2"> 固定电话：</th>
            <td height="2">
              <input name="telephone" type="text" class="xiaozi" size="25" value="<?php echo($telephone)?>"></td> 
          </tr> 
          <tr> 
            <th height="51"> 移动电话： </th>
            <td height="51">
              <input name="phone" type="text" class="xiaozi" size="25" value="<?php echo($phone)?>"></td> 
          </tr> 
          <tr> 
            <th> 地址： </th>
            <td>
               <input name="address" type="text" class="xiaozi" size="25" value="<?php echo($address)?>"></td> 
          </tr> 
           <tr> 
            <th> 所选课程信息：</th>
            <td>
              <textarea name="other" cols="80" rows="10" class="xiaozi"><?php echo($other) ?></textarea></td> 
          </tr> 
       
          <tr> 
            <th>&nbsp; </th>
            <td>&nbsp;</td> 
          </tr> 
          <tr> 
            <th height="16">&nbsp; </th>
            <td> <input type="submit" name="okupload" value="提交" class="xiaozi"> 
              <input type="reset" name="cancel" value="重置" class="xiaozi"> 
              <input type="hidden" name="comid" value="<?php echo($comid)?>"> </td> 
          </tr> 
   </table> 
</form>
</body>
</html>
