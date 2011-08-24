<?
include("../../includes/db_mysql.php");
include("../../includes/admin_functions.php");
//require("../../news/sitemap.php");
$admindir = explode("/",$_SERVER["PHP_SELF"]);
if(strtolower($admindir[1])=="admin"){
   require("../check.php");
}
$db = new CSmysql;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="file:///Mac OS 2/var/www/html/Templates/adminnewtpl.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<!-- InstanceBeginEditable name="doctitle" -->
<title>www.sinemedia.com</title>
<!-- InstanceEndEditable --><!-- InstanceBeginEditable name="head" -->
<script language="JavaScript" type="text/JavaScript">
function delcfm()
{
	if(!confirm("你确定要删除吗？")) window.event.returnValue=false;
}
</SCRIPT>
<!-- InstanceEndEditable -->
<link href="../site.css" rel="stylesheet" type="text/css">
<script src="../nav.js"></script>
<!-- InstanceParam name="onload" type="text" value="" -->
</head>

<body onLoad="">
<?php include("../includes/adminheader.php"); ?>
<!-- main table start -->
<table width="932" border="0" align="center" cellpadding="0" cellspacing="0"> 
  <tr> 
    <td width="16" background="../../images/1-1.gif"><img src="../../images/1-8.gif" width="16" height="6"></td> 
    <td width="900" valign="top" background="../../images/1.gif"> <table width="890" border="0" align="center" cellpadding="0" cellspacing="0"> 
        <tr> 
          <td width="4" height="10" background="../../images/3-1.gif"><img src="../../images/3-1.gif" width="10" height="4"></td> 
          <td background="/images/3.gif" ><!-- InstanceBeginEditable name="text" -->
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#3399FF">
  <tr align="center" bgcolor="#FFFFFF">
    <td height="20" style="color:#FF0000;">标题和链接不能为空！</td>
  </tr>
</table>
<br>
<?php
		}
	}
	elseif ($_POST[act]=="FIX_NEWS")
	{	$title=$_POST[title];	$linkurl=$_POST[linkurl];	$isopen=0;
		if ($title!="" and $linkurl!="")
		{	if ($_POST[isopen])
			{	$isopen=1;	}
			$db->query("UPDATE zt_review SET is_open=$isopen,title='$title',linkurl='$linkurl' WHERE id='{$_POST[newsid]}' LIMIT 1;");
			echo "<script>self.location.replace('{$_SERVER[PHP_SELF]}');</script>";
		}
		else
		{
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#3399FF">
  <tr align="center" bgcolor="#FFFFFF">
    <td height="20" style="color:#FF0000;">标题和链接不能为空！</td>
  </tr>
</table>
<br>
<?php
		}
	}
}
else
{	if ($_GET[openclose])
	{	$db->query("UPDATE zt_review SET is_open=(is_open+1)%2 WHERE id='{$_GET[openclose]}' LIMIT 1;");
		echo "<script>self.location.replace('{$_SERVER[PHP_SELF]}');</script>";
	}
	elseif ($_GET[delete])
	{	$db->query("DELETE FROM zt_review WHERE id='{$_GET[delete]}' LIMIT 1;");
		echo "<script>self.location.replace('{$_SERVER[PHP_SELF]}');</script>";
	}
}
$querynewslink=$db->query("SELECT * FROM zt_review ORDER BY id;");
$newslinkcount=$db->num_rows($querynewslink);
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#3399FF">
  <tr align="center" bgcolor="#FFFFFF">
    <td width="24">ID</td>
    <td width="32">启用</td>
    <td>标题</td>
    <td>链接</td>
    <td width="94">添加日期</td>
    <td width="177" nowrap>操作</td>
  </tr>
<?php
if ($newslinkcount)
{	for ($i=0;$i<$newslinkcount;$i++)
	{	$thisnewslink=$db->assoc($querynewslink);
?>
  <tr bgcolor="#FFFFFF">
    <td align="center"><?=($i+1)?></td>
    <td align="center"><?=$thisnewslink[is_open]?"<span style=\"color:blue;\">是</span>":"<span style=\"color:red;\">否</span>"?></td>
    <td style="word-break:break-all;"><?=$thisnewslink[title]?></td>
    <td style="word-break:break-all;"><?=$thisnewslink[linkurl]?></td>
    <td align="center"><?=date("Y-m-d",$thisnewslink[adddate])?></td>
    <td align="center"><a href="<?=$thisnewslink[linkurl]?>" target="_blank">预览</a> <a href="<?=$_SERVER[PHP_SELF]?>?openclose=<?=$thisnewslink[id]?>">开启/关闭</a> <a href="<?=$_SERVER[PHP_SELF]?>?fixit=<?=$thisnewslink[id]?>">修改</a> <a href="<?=$_SERVER[PHP_SELF]?>?delete=<?=$thisnewslink[id]?>" onClick="if (confirm('您确定要删除它么？')) { return true;} else { return false;}">删除</a></td>
  </tr>
<?php
	}
}
else
{
?>
  <tr align="center">
    <td height="80" colspan="6" bgcolor="#FFFFFF">现有专题栏目为空，请添加！</td>
  </tr>
<?php
}
?>
</table>
<br>
<?php
if ($_GET[fixit])
{	$query=$db->query("SELECT * FROM zt_review WHERE id='{$_GET[fixit]}' LIMIT 1;");
	$fixnewslink=$db->assoc($query);
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#3399FF">
<form method="post">
<input type="hidden" name="act" value="FIX_NEWS" />
<input type="hidden" name="newsid" value="<?=$_GET[fixit]?>" />
  <tr align="center" bgcolor="#FFFFFF">
    <td colspan="4">添加新内容</td>
  </tr>
  <tr align="center" bgcolor="#FFFFFF">
    <td width="42%">标题</td>
    <td width="42%">链接</td>
    <td width="10%">是否开启</td>
    <td width="6%">操作</td>
  </tr>
  <tr align="center" bgcolor="#FFFFFF">
    <td><input name="title" type="text" style="width:100%" maxlength="250" value="<?=$fixnewslink[title]?>" /></td>
    <td><input name="linkurl" type="text" style="width:100%" maxlength="250" value="<?=$fixnewslink[linkurl]?>" /></td>
    <td><input name="isopen" type="checkbox" value="1">
    开启</td>
    <td><input type="submit" value="修改"></td>
  </tr>
</form>
</table>
<?php
}
else
{
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#3399FF">
<form method="post">
<input type="hidden" name="act" value="ADD_NEWS" />
  <tr align="center" bgcolor="#FFFFFF">
    <td colspan="4">添加新内容</td>
  </tr>
  <tr align="center" bgcolor="#FFFFFF">
    <td width="42%">标题</td>
    <td width="42%">链接</td>
    <td width="10%">是否开启</td>
    <td width="6%">操作</td>
  </tr>
  <tr align="center" bgcolor="#FFFFFF">
    <td><input name="title" type="text" style="width:100%" maxlength="250" /></td>
    <td><input name="linkurl" type="text" style="width:100%" maxlength="250" /></td>
    <td><input name="isopen" type="checkbox" value="1">
    开启</td>
    <td><input type="submit" value="添加"></td>
  </tr>
</form>
</table>
<?php
}
?>
<br>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="10" valign="top"><img src="/images/7.gif" width="10" height="10"></td>
    <td width="189" background="/images/7-5.gif"></td>
    <td width="10" valign="top"><img src="/images/7-7.gif" width="10" height="10"></td>
  </tr>
  <tr>
    <td background="/images/7-1.gif"></td>
    <td bgcolor="#FFFFFF" valign="top"><table width="100%" height="39" border="0" cellpadding="0" cellspacing="1">
      <tr>
        <td height="20" colspan="2"><div align="center"><img src="/images/btl-zt-5.gif" width="130" height="20"></div></td>
      </tr>
<?php
$querynewslink=$db->query("SELECT * FROM zt_review WHERE is_open=1 ORDER BY id;");
$newslinkcount=$db->num_rows($querynewslink);
for($i=0;$i<$newslinkcount;$i++)
{	$listnews=$db->assoc($querynewslink);
?>
      <tr>
        <td width="9%" height="16"><img src="/images/hdd.gif" width="18" height="16"></td>
        <td width="91%"><a href="<?=$listnews[linkurl]?>" target="_blank"><? echo wordscut($listnews[title],26);?></a></td>
      </tr>
<?php
}
?>
    </table></td>
    <td background="/images/7-4.gif"></td>
  </tr>
  <tr>
    <td valign="top"><img src="/images/7-2.gif" width="10" height="10"></td>
    <td background="/images/7-6.gif"></td>
    <td valign="top"><img src="/images/7-8.gif" width="10" height="10"></td>
  </tr>
</table>
          <!-- InstanceEndEditable -->            <!--CONTENT --></td> 
          <td width="4" background="../../images/3-2.gif"><img src="../../images/3-2.gif" width="10" height="4"></td> 
        </tr> 
      </table></td> 
    <td width="16" background="../../images/1-2.gif"><img src="../../images/1-9.gif" width="16" height="6"></td> 
  </tr> 
</table> 
<!--main table end -->

<?php include("../includes/adminfooter.php"); ?>

</body>
<!-- InstanceEnd --></html>
