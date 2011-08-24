<?
include("../../includes1/db_mysql.php");
include("../../includes1/admin_functions.php");
//require("../../news/sitemap.php");
$admindir = explode("/",$_SERVER["PHP_SELF"]);

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
<?
$db->sql = "select tpid ,tpname from tb_zt_type where pid=0 order by tpid";
$db->query();
while($res = $db->fetch())
{
  $tpidA[] = $res['tpid'];
  $tpnameA[] = $res['tpname'];
}
$db->sql ="select pid,tpid,tpname from tb_zt_type where pid>0 order by pid";
$db->query();
while($res = $db->fetch())
{
  $subidA[$res['pid']][] = $res['tpid'];
  $subnameA[$res['pid']][] = $res['tpname'];
 }
 for($i=0;$i<sizeof($tpidA);$i++)
 {
     $dispid[] = $tpidA[$i];
	 $dispname[] = $tpnameA[$i];
	 $pname[] = "";
	 for($j=0;$j<sizeof($subidA[$tpidA[$i]]);$j++)
	 {
	   $dispid[] = $subidA[$tpidA[$i]][$j];
	   $dispname[] = $subnameA[$tpidA[$i]][$j];
	   $pname[] = $tpnameA[$i];
	 }
 }
?>
<script language="JavaScript" type="text/JavaScript">
function delcfm()
{
	if(!confirm("你确定要删除吗？")) window.event.returnValue=false;
}
</SCRIPT>
          <table width="539" border="0" cellpadding="2" cellspacing="1" class="thin">
            <tr>
              <th width="48">ID</th>
              <th width="306">类型名称</th>
              <th width="169">操作</th>
            </tr>
<?
for($i=0;$i<sizeof($dispid);$i++)
{
?>
<form name="form1" action="admin_zttypehdl.php" method="post">
            <tr>
              <td align="center"><?=$dispid[$i]?></td>
              <td><? if($pname[$i]!="") echo $pname[$i]." --->"; ?><input name="tpname" value="<?=$dispname[$i]?>"></td>
              <td><input type="submit" name="Submit" value="修改"> 
               &nbsp;&nbsp; <a href="admin_zttype.php?operation=del&id=<?=$dispid[$i]?>" onClick="delcfm()">删除</a>                  <input name="operation" type="hidden" id="operation" value="change">
                  <input name="tpid" type="hidden" id="tpid" value="<?=$dispid[$i]?>"></td>
            </tr>
</form>
	<?}?>
            <tr>
              <td colspan="3"><span class="bred"><br>
                请慎用删除功能，删除将导致已经关联的相关类别文章失效，如须删除，请通知管理员。<br>
              </span></td>
              </tr>
          </table>
<br>
<br>		  
<form name="form2" method="post" action="admin_zttypehdl.php">
          <table width="500" border="0" cellpadding="2" cellspacing="1" class="thin">
            <tr align="left">
              <th colspan="3">添加主分类：</th>
              </tr>
            <tr>
              <td width="82">主分类名称:</td>
              <td width="274"><input name="tpname" type="text" id="tpname" size="30"></td>
              <td width="128"><input type="submit" name="Submit2" value="添加">
                <input name="operation" type="hidden" id="operation" value="addmain"></td>
            </tr>
          </table>
		  </form><br>
<br>

          <form name="form3" method="post" action="admin_zttypehdl.php">
          <table width="500" border="0" cellpadding="2" cellspacing="1" class="thin">
            <tr align="left">
              <th colspan="3">添加次分类：</th>
            </tr>
            <tr>
              <td>选择主分类：</td>
              <td><select name="pid" id="pid">
<?
$str = "";
for($i=0;$i<sizeof($tpidA);$i++)
{
  $str.= "<option value=$tpidA[$i]>$tpnameA[$i]</option>\n";
}
echo $str;
?>
              </select>			  </td>
              <td>&nbsp;</td>
            </tr>
			<tr>
              <td width="81">名称 :              </td>
              <td width="276"><input name="tpname" type="text" id="tpname" size="30"></td>
              <td width="127"><input type="submit" name="Submit22" value="添加">
                <input name="operation" type="hidden" id="operation" value="addsub"></td>
            </tr>
          </table>
          </form>
          <br>
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
