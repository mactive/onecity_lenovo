<?php
require("../../includes/db_mysql.php");
require("../../includes/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
   if(strtolower($admindir[1])=="admin"){
	require("../check.php");
   }
$db = new CSmysql;
 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<!-- InstanceBegin template="/Templates/admin2level.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<!-- InstanceBeginEditable name="doctitle" -->
<title>赛因网-培训课程管理</title>
<!-- InstanceEndEditable -->
<link href="/admin/site.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../nav.js"></script>
<? require("../edit.htm");?>
<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
<!--
.heizi {	font-family: "Arial", "Helvetica", "sans-serif";
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: bold;
	font-variant: normal;
	text-transform: none;
}
.style1 {color: #9900FF}
-->
</style>
<link rel="stylesheet" type="text/css" href="../../admin/cs/pro.css" />
<script src="../../admin/cs/stuHover.js" type="text/javascript"></script>
<script language="javascript">
function delcfm()
{
	if(!confirm("你确定要删除吗？")) window.event.returnValue=false;
}

</script>
<!-- InstanceEndEditable -->
</head>
<body   onLoad="HTMLArea.replace('content',config); MM_preloadImages('/images/dht-tech.gif','/images/dht-forum.gif','/images/dht-index.gif','/images/dht-cs1.gif','/images/dht-vod-111.gif','/images/dht-zt.gif','/images/dht-news.gif','/images/dht-store.gif','/images/dht-sample.gif')" > 
<table width="932" height="78" border="0" align="center" cellpadding="0" cellspacing="0"> 
  <!--DWLayoutTable--> 
  <tr> 
    <td width="16" height="26" background="../../images/1-1.gif"><img src="../../images/2-1.gif" width="16" height="26"></td> 
    <td width="900" rowspan="3"><table width="900" height="80" border="0" align="center" cellpadding="0" cellspacing="0"> 
        <!--DWLayoutTable--> 
        <tr> 
          <td width="200" rowspan="2" valign="top" bgcolor="#D3001F"> <table width="200" height="65" border="0" cellpadding="0" cellspacing="0"> 
              <!--DWLayoutTable--> 
              <tr> 
                <td width="11" height="11" valign="top"><img src="../../images/6-1-1.gif" width="11" height="11"></td> 
                <td width="189"></td> 
              </tr> 
              <tr> 
                <td height="45">&nbsp;</td> 
                <td><div align="center"><img src="../../images/LOGOsinemedia-48.gif" width="185" height="45"></div></td> 
              </tr> 
            </table></td> 
          <td width="689" rowspan="2" valign="top" bgcolor="#D3001F"> <?php	require("../header.php");?> </td> 
          <td width="11" height="11" valign="top" bgcolor="#D3001F"><img src="../../images/6-2.gif" width="11" height="11"></td> 
        </tr> 
        <tr> 
          <td height="55" valign="top" bgcolor="#D3001F"><!--DWLayoutEmptyCell-->&nbsp;</td> 
        </tr> 
      </table></td> 
    <td width="16" background="../../images/1-2.gif"><img src="../../images/2-2.gif" width="16" height="26"></td> 
  </tr> 
  <tr> 
    <td height="35" background="../../images/1-1.gif">&nbsp;</td> 
    <td background="../../images/1-2.gif">&nbsp;</td> 
  </tr> 
  <tr> 
    <td height="16" valign="top" background="../../images/1-1.gif"><!--DWLayoutEmptyCell-->&nbsp;</td> 
    <td valign="top" background="../../images/1-2.gif"><!--DWLayoutEmptyCell-->&nbsp;</td> 
  </tr> 
</table> 
<table width="932" border="0" align="center" cellpadding="0" cellspacing="0"> 
  <!--DWLayoutTable--> 
  <tr> 
    <td width="16" background="../../images/1-1.gif"><img src="../../images/1-8.gif" width="16" height="6"></td> 
    <td colspan="3" valign="top" background="../../images/1.gif"> <table width="880" border="0" align="center" cellpadding="0" cellspacing="0"> 
        <!--DWLayoutTable--> 
        <tr> 
          <td width="10" height="10">&nbsp;</td> 
          <td width="860" rowspan="3">&nbsp; 
            <?php require("../navbar.php"); ?></td> 
          <td width="10">&nbsp;</td> 
        </tr> 
        <tr> 
          <td height="22"><img src="../../images/5-3.gif" width="10" height="22"></td> 
          <td width="10"><img src="../../images/5-4.gif" width="10" height="22"></td> 
        </tr> 
        <tr> 
          <td height="16" background="../../images/3-1.gif">&nbsp;</td> 
          <td width="10" background="../../images/3-2.gif">&nbsp;</td> 
        </tr> 
        <tr> 
          <td height="220" background="../../images/3-1.gif"></td> 
          <td align="center" valign="top" background="../../images/3.gif"><!-- InstanceBeginEditable name="Text" --> 
            <table width="100%" height="192" border="0" align="center" cellpadding="0" cellspacing="0"> 
              <!--DWLayoutTable--> 
              <tr> 
                <td width="10" height="10" valign="top"><img src="../../images/7.gif" width="10" height="10"></td> 
                <td width="850" background="../../images/7-5.gif"></td> 
                <td width="10" valign="top"><img src="../../images/7-7.gif" width="10" height="10"></td> 
              </tr> 
              <tr> 
                <td height="172" background="../../images/7-1.gif">&nbsp;</td> 
                <td align="center" valign="top" bgcolor="#FFFFFF"> <p><font color="#9c00ce"><a href="registration.php">个人报名管理 </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;</font></font><span class="style1"><a href="group.php">团体报名管理</a></span><font color="#9c00ce"> <font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="course.php">课程管理</a>&nbsp;</font></font><font color="#9c00ce"><font color="#9c00ce"> <font color="#FF0000">&nbsp;&nbsp;&nbsp; <a href="add_cou.php">添加课程</a></font></font></font></p> 
                  <?php

	//接受 请求
	$page=$_GET['page'];
	$sqlstr="select * from edu_coursemgr";
	$sqlstr=$sqlstr." order by couid desc ";
    $page_size = 20;
    mkpage($sqlstr);
	$db->query($sqlstr);
?> 
                  <form name="form2" method="post" action="<? echo $PHP_SELF?>"  style="MARGIN: 0px"> 
                    <table width="100%" border="0" class="thin"  bordercolorlight="#CEEFFF" cellpadding="1" cellspacing="1"> 
                      <tr> 
                        <th width="10%" height="22" nowrap> 序号</th> 
                        <th width="11%" nowrap> 课程名称</th> 
                        <th width="20%" nowrap> 代课老师</th> 
                        <th width="18%" nowrap> 开课时间 </th> 
                        <th width="23%" nowrap> 课程费用</th> 
                        <th width="9%" nowrap> 修改</th> 
                        <th width="9%" nowrap> 删除</th> 
                      </tr> 
                      <?php
		while($arr=$db->fetch()){
			//设置 id ,title
			?> 
                      <tr> 
                        <td width="10%" height="20"><?=$arr['couid']?></td> 
                        <td width="11%"><a href="show_counews.php?couid=<?=$arr['couid']?>" target=_blank><?=$arr['couname']?></a></td> 
                        <td width="20%" align="center"> <?php echo $arr['teacher']?></td> 
                        <td width="18%" align="center" bgcolor="#CCCCCC"> <?php echo $arr['begdate']?></td> 
                        <td width="23%" align="center" bgcolor="#CCCCCC">  <?php echo $arr['price']?> </td> 
                        <td width="9%" align="center"><a href="update_cou.php?couid=<?=$arr['couid']?>" target=_blank  title="修改"><img src="../images/b_edit.png" width="16" height="16" border="0"></a></td> 
                        <td width="9%" align="center"><a href="delete_cou.php?couid=<?=$arr['couid']?>" onClick="delcfm()"  title="删除"><img src="../images/b_drop.png" width="16" height="16" border="0"></a></td> 
                      </tr> 
<?php }?> 
                      <tr> 
                        <td colspan="7" align="right"> <? showpage(); ?> </td> 
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
            <!-- InstanceEndEditable --></td> 
          <td background="../../images/3-2.gif"></td> 
        </tr> 
        <tr> 
          <td height="10"><img src="../../images/3-4.gif" width="10" height="10"></td> 
          <td background="../../images/3-3.gif"></td> 
          <td><img src="../../images/3-5.gif" width="10" height="10"></td> 
        </tr> 
      </table> 
      <table width="153" height="34" border="0" align="center" cellpadding="0" cellspacing="0"> 
        <tr> 
          <td width="153" height="23"><img src="../../images/Untitled-6.gif" width="153" height="16"></td> 
        </tr> 
      </table></td> 
    <td width="16" background="../../images/1-2.gif"><img src="../../images/1-9.gif" width="16" height="6"></td> 
  </tr> 
  <tr> 
    <td height="16"><img src="../../images/1-4.gif" width="16" height="26"></td> 
    <td width="16" background="../../images/1-3.gif"><img src="../../images/1-6.gif" width="16" height="26"></td> 
    <td width="868" background="../../images/1-3.gif">&nbsp;</td> 
    <td width="16" background="../../images/1-3.gif"><img src="../../images/1-7.gif" width="16" height="26"></td> 
    <td><img src="../../images/1-5.gif" width="16" height="26"></td> 
  </tr> 
</table> 
</body>
<!-- InstanceEnd -->
</html>
