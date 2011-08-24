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

			$v1=$_POST['comname'];
			$v2=$_POST['name'];
			$v3=$_POST['telephone'];
			$v4=$_POST['phone'];
			$v5=$_POST['fax'];
			$v6=$_POST['email'];
			$v7=$_POST['address'];
			$v8=$_POST["other"];
		
			
			//
			$sqlstr="UPDATE edu_group set comname='$v1'";
			$sqlstr=$sqlstr." ,name='$v2'";
			$sqlstr=$sqlstr." ,telephone='$v3'";
			$sqlstr=$sqlstr." ,phone='$v4'";
			$sqlstr=$sqlstr." ,fax='$v5'";
			$sqlstr=$sqlstr." ,email='$v6'";
			$sqlstr=$sqlstr." ,address='$v7'";
			$sqlstr=$sqlstr." ,other='$v8'";
			$sqlstr=$sqlstr." where comid=".$comid;
			//echo $sqlstr;
			
				$db->query($sqlstr);
				
	$sqlstr1="select * from edu_people";
	$sqlstr1=$sqlstr1." where comid=$comid";
	$sqlstr1=$sqlstr1." order by id  ";
	$result1=mysql_query($sqlstr1); 
    $numrows=mysql_num_rows($result); 
    $i=0;
while($arr=mysql_fetch_array($result1))
{
$i++;
    $k=$_POST['k'][$i];
	$ename=$_POST['ename'][$i];
	$sex=$_POST['sex'][$i];
	$position=$_POST['position'][$i];
	$etelephone =$_POST['etelephone'][$i];
	$eemail=$_POST['eemail'][$i];
	$eaddress=$_POST['eaddress'][$i];
	$sql1="UPDATE edu_people set ename='$ename' ";
	$sql1=$sql1." ,sex='$sex'";
	$sql1=$sql1." ,position='$position'";
	$sql1=$sql1." ,etelephone='$etelephone'";
	$sql1=$sql1." ,eemail='$eemail'";
	$sql1=$sql1." ,eaddress='$eaddress'";
	$sql1=$sql1." where id=".$k;
	$db->query($sql1);
	}
	}
	//查询 数据
	$sqlstr="select * from edu_group";
	$sqlstr=$sqlstr." where comid=$comid ";
		
	$db->query($sqlstr);
	$arr=$db->fetch();
	//初始化 显示对象
	$comname=$arr[comname];
	$name=$arr[name];
	$telephone=$arr[telephone];
	$phone=$arr[phone];
	$fax =$arr[fax];
	$email = $arr[email];
	$address=$arr[address];
	$other=$arr[other];
	?>
	
<html>
<head>
<title>团体报名更新</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<script src="../js.js"></script>
<script src="../func.js"></script>
<? require("../edit.htm");?>
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
            <th colspan="4">客户名称：</th>
            <td colspan="2">
              <input name="comname" type="text" class="xiaozi" size="25" value="<?php echo($comname)?>"></td> 
          </tr> 
          <tr> 
            <th colspan="4"> 联系人：</th>
            <td colspan="2">
               <input name="name" type="text" class="xiaozi" size="25" value="<?php echo($name)?>"></td> 
          </tr> 
          <tr> 
            <th colspan="4"> 固定电话：</th>
            <td colspan="2"> <input name="telephone" type="text" class="xiaozi" size="50" value="<?php echo($telephone)?>"></td> 
          </tr> 
          <tr> 
            <th colspan="4">移动电话：</th> 
            <td colspan="2">
              <input name="phone" type="text" class="xiaozi" id="phone" value="<?php echo($phone)?>" size="50"></td> 
          </tr> 
          <tr> 
            <th colspan="4" valign="top"> 传真：</th>
            <td colspan="2">   <input name="fax" type="text" class="xiaozi" size="25" value="<?php echo($fax)?>"> </td> 
          </tr> 
          <tr> 
            <th colspan="4">电子邮件：</th> 
            <td colspan="2">
             <input name="email" type="text" class="xiaozi" size="25" value="<?php echo($email)?>"></td> 
          </tr> 
          <tr> 
            <th height="2" colspan="4"> 通讯地址及邮编：</th>
            <td height="2" colspan="2">
              <input name="address" type="text" class="xiaozi" size="25" value="<?php echo($address)?>"></td> 
          </tr> 
           <tr> 
            <th colspan="4"> 所选课程信息：</th>
            <td colspan="2">
              <textarea name="other" cols="80" rows="10" class="xiaozi"><?php echo($other) ?></textarea></td> 
          </tr> 
       
          <tr> 
            <th height="130" colspan="6">参训人员名单<?
	$sqlstr="select * from edu_people";
	$sqlstr=$sqlstr." where comid=$comid";
	$sqlstr=$sqlstr." order by id  ";
	$result=mysql_query($sqlstr); 
    $numrows=mysql_num_rows($result); 
$i=0;
while($arr=mysql_fetch_array($result))
{
$i++;
?>
    <tr>
                                                                                  <td width="10%" align="right"><div align="left">姓名
                                                                                    <?=$i?>
：                                                                                      
                                                                                  </div>
                                                                                  <td width="11%" align="right"><div align="left">
                                                                                    <input type="text" name="ename[<?=$i?>]" value="<?php echo($arr['ename']);?>" size="20">                                                                                        
                                                                                  </div>
                                                                                  <td width="16%" align="right"><div align="left">性别
                                                                                    <?=$i?>
：                                                                                        
                                                                                  </div>
                                                                                  <td width="15%" align="right"><div align="left">
                                                                                   <input type="text" name="sex[<?=$i?>]" value="<?php echo($arr['sex']);?>" size="8">
                                                                                   0为男，1为女
                                                                                  </div>
      <td width="4%" align="right"><div align="left">职务
          <?=$i?>
：                                                                                        
        </div>
      <td width="44%" align="right"><div align="left">
        <input type="text" name="position[<?=$i?>]" value="<?php echo($arr['position']);?>" size="20">                                                                                        
        </div>
    <tr>
                                                                                        <td align="right"><div align="left">电话
                                                                                         <?=$i?>
：
                                                                                        </div>
        <td align="right"><div align="left">
          <input type="text" name="etelephone[<?=$i?>]" value="<?php echo($arr['etelephone']);?>" size="20">                                                                                        
        </div>
        <td align="right"><div align="left">Email
                                                                                          <?=$i?>
                                                                                          ：                                                                                        
                                                                                        </div>
        <td align="right"><div align="left">
          <input type="text" name="eemail[<?=$i?>]" value="<?php echo($arr['eemail']);?>" size="20">                                                                                        
        </div>
        <td align="right"><div align="left">地址
                                                                                          <?=$i?>
：                                                                                        
                                                                                        </div>
        <td align="right"><div align="left">
          <input type="text" name="eaddress[<?=$i?>]" value="<?php echo($arr['eaddress']);?>" size="20">                                                                                        
            </div>
    <tr>
                                                                                        <td colspan="6" align="right">
                                                                                          <div align="left"></div>
    <tr>
                                                                                        <td height="25" colspan="6" align="left">
                                           <input type="hidden" name="k[<?=$i?>]" value="<?php echo($arr['id']);?>"> </td>                                                <div align="left"></div>
    <tr>
                                                                                        <td colspan="6" align="right"><?

}//for
?>                                                                                        </tr> 
          <tr> 
            <th height="16" colspan="4">&nbsp; </th>
            <td colspan="2"> <input type="submit" name="okupload" value="提交" class="xiaozi"> 
              <input type="reset" name="cancel" value="重置" class="xiaozi"> 
              <input type="hidden" name="comid" value="<?php echo($comid)?>"> </td> 
			  
          </tr> 
   </table> 
</form>
</body>
</html>
