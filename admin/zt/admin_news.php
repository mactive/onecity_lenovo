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
              <table width="100%" height="192" border="0" align="center" cellpadding="0" cellspacing="0"> 
              <!--DWLayoutTable--> 
              <tr> 
                <td width="10" height="10" valign="top"><img src="../../images/7.gif" width="10" height="10"></td> 
                <td width="850" background="../../images/7-5.gif"></td> 
                <td width="10" valign="top"><img src="../../images/7-7.gif" width="10" height="10"></td> 
              </tr> 
              <tr> 
                <td height="172" background="../../images/7-1.gif">&nbsp;</td> 
                <td align="center" valign="top" bgcolor="#FFFFFF"> <p><font color="#9c00ce"><a href="registration.php">报名管理         </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;</font></font><span class="style1"><a href="event_show.php">课程发布</a></span></p> 
                  <?php

	//接受 请求
	$page=$_GET['page'];
	$sqlstr="select * from tb_zt,tb_zt_type";
	$sqlstr=$sqlstr." where tb_zt.newstype=tb_zt_type.tpid ";
	$sqlstr=$sqlstr." order by tb_zt.newsid desc ";
    $page_size = 20;
    mkpage($sqlstr);
	$db->query($sqlstr);
?> 
                  <form name="form2" method="post" action="<? echo $PHP_SELF?>"  style="MARGIN: 0px"> 
                    <table width="100%" border="0" class="thin"  bordercolorlight="#CEEFFF" cellpadding="1" cellspacing="1"> 
                      <tr> 
                        <th width="6%" height="22" nowrap> 序号</th> 
                        <th width="38%" nowrap> 题目</th> 
                        <th width="12%" nowrap> 类型</th> 
                        <th width="4%" nowrap> 推荐</th> 
                        <th width="5%" nowrap> 题图</th> 
                        <th width="6%" nowrap> 副题图</th> 
                        <th width="5%" nowrap> 小标图</th> 
                        <th width="7%" nowrap> 大标图</th> 
                        <th width="7%" nowrap> 修改图</th> 
                        <th width="5%" nowrap> 修改</th> 
                        <th width="5%" nowrap> 删除</th> 
                      </tr> 
                      <?php
		while($arr=$db->fetch()){
			//设置 id ,title
			$newsid=$arr[newsid];
			$title=$arr[title];
	?> 
                      <tr> 
                        <td width="6%" height="20"><?=$arr['newsid']?></td> 
                        <td width="38%"><a href="show_news.php?newsid=<?=$arr['newsid']?>" target=_blank><?=$arr['title']?></a></td> 
                        <td width="12%" align="center"> <?php echo $arr[tpname]?></td> 
                        <td width="4%" align="center" bgcolor="#CCCCCC"> <?php echo($arr[tuijian]==1?"是":"否")?></td> 
                        <td width="5%" align="center" bgcolor="#CCCCCC">  <?php echo(($arr[titu]=="0" || $arr[titu]=="")?"无":"有")?> </td> 
                        <td width="6%" align="center" bgcolor="#CCCCCC">  <?php echo(($arr[ftitu]=="0" || $arr[ftitu]=="")?"无":"有")?></td> 
                        <td width="5%" align="center" bgcolor="#CCCCCC"> <?php echo(($arr[img]=="0" || $arr[img]=="")?"无":"有")?> </td> 
                        <td width="7%" align="center" bgcolor="#CCCCCC"><?php echo(($arr['lastimg']=="0" || $arr['lastimg']=="")?"无":"有")?> </td> 
                        <td width="7%" align="center" bgcolor="#CCCCCC"><a href="update_titu.php?newsid=<?=$arr['newsid']?>" target=_blank>更新</a></td> 
                        <td width="5%" align="center"><a href="update_news.php?newsid=<?=$arr['newsid']?>" target=_blank  title="修改"><img src="../images/b_edit.png" width="16" height="16" border="0"></a></td> 
                        <td width="5%" align="center"><a href="delete_news.php?newsid=<?=$arr['newsid']?>" onClick="delcfm()"  title="删除"><img src="../images/b_drop.png" width="16" height="16" border="0"></a></td> 
                      </tr> 
<?php }?> 
                      <tr> 
                        <td colspan="11" align="right"> <? showpage(); ?> </td> 
                      </tr> 
                    </table> 
                </form></td> 
                <td background="../../images/7-4.gif"></td> 
              </tr> 
              <tr> 
                <td height="10" valign="top"><img src="../../images/7-2.gif" width="10" height="10"></td> 
                <td background="../../images/7-6.gif"></td> 
                <td valign="top"><img src="../../images/7-8.gif" width="10" height="10"></td> 
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
