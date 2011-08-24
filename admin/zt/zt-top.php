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
     <table width="80%"  border="0" cellpadding="1" cellspacing="1" class="thin">
              <tr>
                <th width="14%" scope="row">ID</th>
                <th width="64%" height="20" scope="row"> 标题</th>
                <th colspan="2" scope="row">固定置顶</th>
              </tr>
<?php while($res = mysql_fetch_array($result)) {?>			  
		  <form name="form<?=$res['newsid']?>" action="" method="post">
              <tr>
                <td align="center" scope="row"><?=$res['newsid']?>&nbsp;</td>
                <td scope="row"><a href="/zt/<?=$res['newsid']?>.html" target="_blank"><?=$res['title']?></a>&nbsp; <?=$res['time']?></td>
                <td width="10%" align="center" scope="row"><input name="attop" type="checkbox" id="attop" value="1" <? if($res['attop']=='1') echo "checked";?>>
                  置顶</td>
                <td width="12%" align="center" scope="row"><input type="submit" name="Submit" value="修改">
                  <input name="id" type="hidden" id="id" value="<?=$res['newsid']?>"></td>
              </tr>
</form>			
<?php }?>			  
               <tr align="center">
                <td colspan="4" scope="row"><? showpage();?>&nbsp;</td>
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
