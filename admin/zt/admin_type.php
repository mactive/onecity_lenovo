<?
  $admindir = explode("/",$_SERVER["PHP_SELF"]);

?>
<html>
<head>
<title>新闻类别管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<script language="javascript">
<!--
function checkform()
{
  if (form1.tpname.value=="")
  {
	form1.tpname.focus();
	alert("类别题目不能为空。");return false;
  }
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
</head>

<body bgcolor="#FFFFFF" text="#000000" onLoad="MM_preloadImages('../../images/dht-index.gif','../../images/dht-store.gif','../../images/dht-sample.gif','../../images/dht-news.gif','../../images/dht-tech.gif','../../images/dht-cs1.gif','../../images/dht-vod-111.gif','../../images/dht-forum.gif')">
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
                <td><div align="center"><img src="../../images/logo-sinemedia.gif" width="186" height="71"></div></td>
              </tr>
            </table></td>
          <td width="689" rowspan="2" valign="top" bgcolor="#D3001F"> 
            <?php
	require("../header.php");
?>
          </td>
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
    <td width="16" height="409" background="../../images/1-1.gif"><img src="../../images/1-8.gif" width="16" height="6"></td>
    <td colspan="3" valign="top" background="../../images/1.gif"> <table width="880" border="0" align="center" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr> 
          <td width="10" height="10">&nbsp;</td>
          <td width="860" rowspan="3"><table width="873" border="0" align="center" cellpadding="0" cellspacing="0">
              <!--DWLayoutTable-->
              <tr> 
                <td height="16" colspan="11" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
              </tr>
              <tr> 
                <td width="77" height="22"><img src="../../images/5-2.gif" width="83" height="22"></td>
                <td width="79"><a href="../index.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('index','','../../images/dht-index.gif',1)"><img src="../../images/4.gif" alt="赛因首页" name="index" width="79" height="22" border="0"></a></td>
                <td width="78"><img src="../../images/dht-zt.gif" alt="独家专题" name="dht-zt" width="78" height="22" border="0"></td>
                <td width="78"><a href="../sp/admin_sp.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('产品直销','','../../images/dht-store.gif',1)"><img src="../../images/dht-store-1.gif" alt="产品直销" name="产品直销" width="78" height="22" border="0"></a></td>
                <td width="78"><a href="../ys/admin_sp.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('音色在线','','../../images/dht-sample.gif></a></td>
                <td width="78"><a href="../xwzx/admin_news.php" onMouseOver="MM_swapImage('新闻资讯','','../../images/dht-news.gif',1)" onMouseOut="MM_swapImgRestore()"><img src="../../images/4-5.gif" alt="新闻资讯" name="新闻资讯" width="78" height="22" border="0"></a></td>
                <td width="78"><a href="../jsxx/admin_news.php" onMouseOver="MM_swapImage('tech','','../../images/dht-tech.gif',1)" onMouseOut="MM_swapImgRestore()"><img src="../../images/4-6.gif" alt="技术学习" name="tech" width="78" height="22" border="0"></a></td>
                <td width="78"><a href="../kfzx/admin_news.php" onMouseOver="MM_swapImage('客服中心','','../../images/dht-cs1.gif',1)" onMouseOut="MM_swapImgRestore()"><img src="../../images/dht-cs-1.gif" alt="客服中心" name="客服中心" width="78" height="22" border="0"></a></td>
                <td width="78"><a href="../sys/admin_news.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('宽带平台','','../../images/dht-vod-111.gif',1)"><img src="../../images/dht-vod-1.gif" alt="宽带平台" name="宽带平台" width="78" height="22" border="0"></a></td>
                <td width="80"><a href="#" target="_blank" onMouseOver="MM_swapImage('forum','','../../images/dht-forum.gif',1)" onMouseOut="MM_swapImgRestore()"><img src="../../images/4-2.gif" alt="赛因社区" name="forum" width="80" height="22" border="0"></a></td>
                <td width="91"><img src="../../images/5-1.gif" width="85" height="22"></td>
              </tr>
              <tr> 
                <td colspan="11" valign="top"> <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <!--DWLayoutTable-->
                    <tr> 
                      <td width="163" height="16" background="../../images/3.gif">&nbsp;</td>
                      <td width="362" height="16" bgcolor="#ce8ae7"><span class="heizi"><a href="admin_news.php">专题管理</a></span> 
                        <span class="xiaozi"> <a href="admin_ques.php">调查管理</a></span> 
                        <span class="heizi"><a href="admin_adv.php">广告管理</a></span> 
                        <span class="heizi"><a href="admin_top.php" target="_blank">排行榜管理</a><a href="admin_type.php" target="_blank"> 
                        专题类别管理</a> </span></td>
                      <td width="348" height="16" background="../../images/3.gif">&nbsp;</td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
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
          <td valign="top" background="../../images/3.gif"><table width="100%">
              <tr> 
                <td height="311" align="center"><table width="283" height="192" border="0" align="center" cellpadding="0" cellspacing="0">
                    <!--DWLayoutTable-->
                    <tr> 
                      <td width="10" height="10" valign="top"><img src="../../images/7.gif" width="10" height="10"></td>
                      <td width="270" background="../../images/7-5.gif"></td>
                      <td width="10" valign="top"><img src="../../images/7-7.gif" width="10" height="10"></td>
                    </tr>
                    <tr> 
                      <td height="172" background="../../images/7-1.gif">&nbsp;</td>
                      <td align="center" valign="top" bgcolor="#FFFFFF" class="heizi"> 
                        <p>
                          <?php
	//require("..\\func.php");
    require('../func.php');

	//接受 请求
	$operate=$HTTP_POST_VARS['okupload'];
	if($operate<>"")
	{
			//提交后
			$tpname=$HTTP_POST_VARS['tpname'];
				$sqlstr="INSERT INTO tb_zt_type(tpname) values('$tpname')";
			
				$result=execute($sqlstr);
			
	}

	//查询 数据
	$sqlstr = "select * from tb_zt_type order by tpid ";
		
		$result=execute($sqlstr);
		$count=mysql_num_rows($result);
		if($count>0)
?>
                        </p>
                        <table border="1" width="300" bordercolorlight="#CEEFFF" cellspacing="0" cellpadding="0" bordercolordark="#C0C0C0" height="50">
                          <tr> 
                            <td width="24%" align="center" height="20">序号</td>
                            <td width="65%" align="center" height="20">类别题目</td>
                            <td width="11%" align="center" height="20">选择</td>
                          </tr>
                          <?php
		while($arr=mysql_fetch_array($result)){
			$tpid=$arr[tpid];
			$tpname=$arr[tpname];
			$del_href="<a href=delete_news_type.php?upload=del&tpid=".$tpid."><font color=#FF0000><b>删除</b></font></a>"
	?>
                          <tr> 
                            <td width="24%" height="22"> <?php echo($tpid) ?> 
                            </td>
                            <td width="65%" height="22"> <div align="center"> 
                                <?php echo($tpname) ?> </div></td>
                            <td width="11%" align="center" height="22"><?php echo($del_href) ?></td>
                          </tr>
                          <?php
		}
	?>
                        </table>
                        <p> 
                          <?php
 	
	//========================
?>
                        </p>
                        <form name="form1" method="post" action="<?php echo $PHP_SELF?>"  style="MARGIN: 0px"   onsubmit="return(checkform());">
                          <table border="1" width="300" bordercolorlight="#CEEFFF" cellspacing="0" cellpadding="0" bordercolordark="#C0C0C0" height="50">
                            <tr> 
                              <td width="24%" align="center" height="20">类别题目</td>
                              <td width="65%" height="20"> <input type="text" name="tpname"> 
                              </td>
                            </tr>
                            <tr> 
                              <td width="24%" height="22">&nbsp; </td>
                              <td width="65%" height="22"> <input type="submit" name="okupload" value="添加"> 
                              </td>
                            </tr>
                          </table>
                        </form> </td>
                      <td background="../../images/7-4.gif"></td>
                    </tr>
                    <tr> 
                      <td height="10" valign="top"><img src="../../images/7-2.gif" width="10" height="10"></td>
                      <td background="../../images/7-6.gif"></td>
                      <td valign="top"><img src="../../images/7-8.gif" width="10" height="10"></td>
                    </tr>
                  </table> 
                  <form name="form1" method="post" action="<?php echo $PHP_SELF?>"  style="MARGIN: 0px"   onsubmit="return(checkform());">
                  </form> </td>
              </tr>
            </table></td>
          <td background="../../images/3-2.gif"></td>
        </tr>
        <tr> 
          <td height="10"><img src="../../images/3-4.gif" width="10" height="10"></td>
          <td background="../../images/3-3.gif"></td>
          <td><img src="../../images/3-5.gif" width="10" height="10"></td>
        </tr>
      </table>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td height="28"><img src="../../images/copyright.gif" width="296" height="23"></td>
        </tr>
      </table></td>
    <td width="16" background="../../images/1-2.gif"><!--DWLayoutEmptyCell-->&nbsp;</td>
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
</html>