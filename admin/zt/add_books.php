<?
require("../../includes1/db_mysql.php");
require("../../includes1/admin_functions.php");
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
	if(!confirm("��ȷ��Ҫɾ����")) window.event.returnValue=false;
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
  <form name="form1" method="post" action="add_book.php" enctype="multipart/form-data" style="MARGIN: 0px" onSubmit="return(submitform());">
            <table width="700" border="0" align="center" cellpadding="2" cellspacing="1" class="thin">
              <tr>
                <th width="16%">
                  <div align="right">������</div></th>
                <td width="84%"><font color="#FFFFFF">
                  <input name="name" type="text" size="25">
                </font></td>
              </tr>
         <tr>
                <th width="16%">
                  <div align="right">�γ�id��</div></th>
                <td width="84%"><font color="#FFFFFF">
                  <input name="courseid" type="text" size="25">
                </font></td>
              </tr>
              <tr>
    <tr>
                <th width="16%">
                  <div align="right">���ߣ�</div></th>
                <td width="84%"><font color="#FFFFFF">
                  <input name="artist" type="text" size="25">
                </font></td>
              </tr>
              <tr>
              <tr bgcolor="#ddddCC">
                <th width="16%" align="right"> ��ͼ�� </th>
                <td width="84%"> <input type="text" name="images">
      <a href="#" class="bred" onClick="openwin('images');">�ϴ�</a><span class="bred">*��ͼƬ���ϴ�.</span></td>
              </tr>
              
              <tr>
                <th width="16%">
                  <div align="right">������Ϣ��</div></th>
                <td width="84%"> <font color="#FFFFFF">
                  <input name="publics" type="text" size="50">
                </font> </td>
              </tr>
              <tr>
                <th align="right">�۸�</th>
                <td><input name="price" type="text" size="50"></td>
              </tr>
			   <tr>
                <th align="right">��Ԫ�۸�</th>
                <td><input name="mei" type="text" size="50"></td>
              </tr>
              <tr>
                <th valign="top" width="16%">
                  <div align="right">���ݣ�</div></th>
                <td width="84%">
                    <textarea id="demo" name="demo" cols=50 rows=20 style="width:100%"></textarea></td>
              <tr>
	            <tr>
                <th valign="top" width="16%">
                  <div align="right">������</div></th>
                <td width="84%">
                  <textarea id="description" name="description" cols=50 rows=20 style="width:100%"></textarea>
                </td>
              </tr>
              <tr>
	            <tr>
                <th valign="top" width="16%">
                  <div align="right">���£�</div></th>
                <td width="84%">
                  <textarea id="updates" name="updates" cols=50 rows=20 style="width:100%"></textarea>
                </td>
              </tr>
              <tr>		  
          
              <tr>
                <th height="16" width="16%">
                  <div align="right"></div></th>
                <td width="84%">
                  <input type="submit" name="okupload" value="�ύ">
                  <input type="reset" name="cancel" value="����">
                </td>
              </tr>
            </table>
			</form>
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
