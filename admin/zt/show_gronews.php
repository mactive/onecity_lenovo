<?
	//require("..\\func.php");
    require('../func.php');
	//接受 请求
	$comid=$HTTP_GET_VARS['comid'];
?>	



<html>
<head>
<title>团体报名浏览</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<style type="text/css">
<!--
.style1 {color: #9900FF}
-->
</style>
</head>

<body bgcolor="#FFFFFF" text="#000000">
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center"><font color="#9c00ce"><a href="registration.php">个人报名管理 </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;</font></font><span class="style1"><a href="group.php">团体</a></span><span class="style1"><a href="group.php">报名管理</a></span><font color="#9c00ce"><a href="registration.php"> </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="course.php">课程管理</a>&nbsp;</font></font></p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<table width="62%" height="107" align="center">
  <tr>
    <td width="100%" height="101">
      <table width="800" border="0" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" class="xiaozi">
                 <?
	$sqlstr="select * from edu_group";
	$sqlstr=$sqlstr." where comid=$comid";
	$sqlstr=$sqlstr." order by comid desc ";

	
	$result = execute($sqlstr);
	
	if($arr=mysql_fetch_array($result))
{
?> 
                 <tr>
          <td width="96" height="20">
            <div align="left"><span class="xiaozi">客户名称：</span></div></td>
          <td width="306"><span class="xiaozi">
  
        <?php echo($arr['comname']);?>
            </span></td>
          <td width="79">联系人：</td>
          <td width="322"><?php echo($arr['name']);?> </td>
                 </tr>
        <tr>
          <td width="96" height="21">
            <div align="left">固定电话：</div></td>
          <td width="306"><?php echo($arr['telephone']);?> </td>
          <td width="79">移动电话：</td>
          <td width="322"><?php echo($arr['phone']);?></td>
        </tr>
        <tr>
          <td width="96" height="20" bgcolor="#FFFFFF">
            <div align="left">传真：</div></td>
          <td width="306" bgcolor="#FFFFFF"><?php echo($arr['fax']);?> </td>
          <td width="79" bgcolor="#FFFFFF">电子邮件：</td>
          <td width="322" bgcolor="#FFFFFF"><?php echo($arr['email']);?> </td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="96" height="20" bgcolor="#FFFFFF">
            <div align="left">通讯地址及邮编：</div></td>
          <td width="306" bgcolor="#FFFFFF"><?php echo($arr['address']);?> </td>
          <td width="79" bgcolor="#FFFFFF">课程总费用：</td>
          <td width="322" bgcolor="#FFFFFF"><?php echo($arr['demo']);?> </td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="96" height="18" bgcolor="#FFFFFF">
          
          <div align="left">所选课程信息： </div></td>
          <td colspan="3" bgcolor="#FFFFFF"><?php echo($arr['other']);?> </td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td height="18" colspan="4" bgcolor="#FFFFFF">—————————————————————————————————————————————————————————————————————————</td>
        </tr>
</table>
</table>
		<p align="center">
		  <? }?>参训人员名单：
		  <?
	$sqlstr="select * from edu_people";
	$sqlstr=$sqlstr." where comid=$comid";
	$sqlstr=$sqlstr." order by id desc ";

	
	$result = execute($sqlstr);
	
while($arr=mysql_fetch_array($result))
{
?>
		</p>
		<table width="800" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
          <tr>
            <td width="83" height="25"><span class="xiaozi">姓名：</span></td>
            <td width="144"><span class="xiaozi"><?php echo($arr['ename']);?></span></td>
            <td width="80"><span class="xiaozi">性别：</span></td>
            <td width="166"><span class="xiaozi"><?php echo($arr['sex']);?> </span></td>
            <td width="56"><span class="xiaozi">职务：</span></td>
            <td width="257"><span class="xiaozi"><?php echo($arr['position']);?> </span></td>
          </tr>
          <tr>
            <td height="22"><span class="xiaozi">联系电话：</span></td>
            <td><span class="xiaozi"><?php echo($arr['etelephone']);?></span></td>
            <td><span class="xiaozi">电子邮箱：</span></td>
            <td><span class="xiaozi"><?php echo($arr['eemail']);?></span></td>
            <td><span class="xiaozi">地址：</span></td>
            <td><span class="xiaozi"><?php echo($arr['eaddress']);?></span></td>
          </tr>
          <tr>
            <td height="28">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
        <? }?>
</body>
</html>
