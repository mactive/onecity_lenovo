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
  <form name="form1" method="post" action="add_news.php" enctype="multipart/form-data" style="MARGIN: 0px" onSubmit="return(submitform());">
            <table width="700" border="0" cellpadding="2" cellspacing="1" class="thin">
              <tr>
                <th width="16%">
                  <div align="right">���⣺</div></th>
                <td width="84%"><font color="#FFFFFF">
                  <input name="newstitle" type="text" size="25">
                </font></td>
              </tr>
              <tr>
                <th width="16%">
                  <div align="right">ר�����ͣ�</div></th>
                <td width="84%"><font color="#FFFFFF">
                  <select name="newstype" >
                    <?php echo get_zt_type12();?>
                  </select>
                </font></td>
              </tr>
              <tr>
                <th width="16%">
                  <div align="right">�Ƽ�ר�⣺ </div></th>
                <td width="84%">
                  <input type=radio value=1 name=tuijian>
      ��
      <input type=radio value=0 name=tuijian checked >
      �� </td>
              </tr>
              <tr bgcolor="#ddddCC">
                <th width="16%" align="right"> ��ͼ�� </th>
                <td width="84%"> <input type="text" name="tituname">
      <a href="#" class="bred" onClick="openwin('tituname');">�ϴ�</a><span class="bred">*��ͼƬ���ϴ�.</span></td>
              </tr>
               <tr bgcolor="#ddddCC">
                <th width="16%" align="right"> ����ͼ�� </th>
                <td width="84%"> <input type="text" name="ftituname">
      <a href="#" class="bred" onClick="openwin('ftituname');">�ϴ�</a><span class="bred">*��ͼƬ���ϴ�.</span></td>
              </tr>
			   <tr bgcolor="#ddddCC">
                <th width="16%" align="right"> С��ͼ�� </th>
                <td width="84%"> <input type="text" name="imgname">
      <a href="#" class="bred" onClick="openwin('imgname');">�ϴ�</a><span class="bred">*��ͼƬ���ϴ�.</span></td>
              </tr>
			   <tr bgcolor="#ddddCC">
                <th width="16%" align="right"> ��ͼ�� </th>
                <td width="84%"> <input type="text" name="lastimgname">
      <a href="#" class="bred" onClick="openwin('lastimgname');">�ϴ�</a><span class="bred">*��ͼƬ���ϴ�.</span>detailҳ</td>
              </tr>
              <tr>
                <th width="16%">
                  <div align="right">������</div></th>
                <td width="84%"> <font color="#FFFFFF">
                  <input name="jianshu" type="text" size="50">
                </font> </td>
              </tr>
              <tr>
                <th align="right">�����ڣ�</th>
                <td><input name="period" type="text" size="50"></td>
              </tr>
              <tr>
                <th valign="top" width="16%">
                  <div align="right">���ݣ�</div></th>
                <td width="84%">
                  <textarea id="content" name="content" cols=50 rows=20 style="width:100%"></textarea>
                </td>
              </tr>
              <tr>
                <th>�������ID</th>
                <td>���ţ�
                    <input name="relid[0]" type="text" id="relid1" size="20">
      ������
      <input name="relid[1]" type="text" id="relid2" size="20">
      <br>
      ��ɫ��
      <input name="relid[2]" type="text" id="relid3" size="20">
      ��Ʒ��
      <input name="relid[3]" type="text" id="relid4" size="20"></td>
              </tr>
              <tr>
                <th width="16%" height="56">
                  <div align="right">��Ʒ���ࣺ</div></th>
                <td width="84%" height="56"><font color="#FFFFFF">
                  <select name="sp_type1" size="1">
                    <?php echo get_sp_type(0);?>
                  </select>
                  <select name="sp_type2" size="1">
                    <?php echo get_sp_type(0);?>
                  </select>
                  <select name="sp_type3" size="1">
                    <?php echo get_sp_type(0);?>
                  </select>
                  <br>
                  <select name="sp_type4" size="1">
                    <?php echo get_sp_type(0);?>
                  </select>
                  <select name="sp_type5" size="1">
                    <?php echo get_sp_type(0);?>
                  </select>
                  <select name="sp_type6" size="1">
                    <?php echo get_sp_type(0);?>
                  </select>
                </font></td>
              </tr>
              <tr>
                <th width="16%" height="51">
                  <div align="right">���·��ࣺ<font color="#FFFFFF"> </font></div></th>
                <td width="84%" height="51"><font color="#FFFFFF">
                  <select name="wz_type1" size="1">
                    <?php echo get_wz_type(0);?>
                  </select>
                </font></td>
              </tr>
              <tr>
                <th width="16%">
                  <div align="right">��ɫ�̷��ࣺ<font color="#FFFFFF"> </font></div></th>
                <td width="84%"><font color="#FFFFFF">
                  <select name="ys_type1" size="1">
                    <?php echo get_ys_type(0);?>
                  </select>
                  <select name="ys_type2" size="1">
                    <?php echo get_ys_type(0);?>
                  </select>
                  <select name="ys_type3" size="1">
                    <?php echo get_ys_type(0);?>
                  </select>
                  <br>
                  <select name="ys_type4" size="1">
                    <?php echo get_ys_type(0);?>
                  </select>
                  <select name="ys_type5" size="1">
                    <?php echo get_ys_type(0);?>
                  </select>
                  <select name="ys_type6" size="1">
                    <?php echo get_ys_type(0);?>
                  </select>
                </font></td>
              </tr>
              <tr>
                <th width="16%">
                  <div align="right">��Ʒ�ؼ���������</div></th>
                <td width="84%">
                  <p><font color="#FFFFFF"> <font color="#FF0000">
                    <input name="sp_key" type="text" size="15">
        *�ؼ������� ������ �ֿ�</font></font></p></td>
              </tr>
              <tr>
                <th width="16%">
                  <div align="right">���¹ؼ���������</div></th>
                <td width="84%"><font color="#FFFFFF">
                  <input name="wz_key" type="text" size="15">
                  <font color="#FF0000">*�ؼ������� ������ �ֿ�</font></font></td>
              </tr>
              <tr>
                <th width="16%">
                  <div align="right">��ɫ�̹ؼ���������</div></th>
                <td width="84%"><font color="#FFFFFF">
                  <input name="ys_key" type="text" size="15">
                  <font color="#FF0000">*�ؼ������á������ֿ�</font></font></td>
              </tr>
              <tr>
                <th width="16%">
                  <div align="right"></div></th>
                <td width="84%">&nbsp;</td>
              </tr>
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
