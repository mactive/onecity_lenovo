<?php
	require("../func.php");
	
#===============get request==========================
$tb="tb_zt_comment";
$tb2 = "tb_zt";
	$sel = "1";
	import_request_variables(gP);
	if($order!="") $orderby = " cid,$order desc";
	else $orderby = " cid desc";
#==================删除============================
	if($delbtn!=""&&sizeof($delid)>0)
	{
	   $delstr = implode(",",$delid);
	   $sql = "delete from $tb where cid in($delstr)";
	  // echo $sql;
	   execute($sql);
	}
#===================分页===========================
  $page = $_GET["page"]+0;
  $eachpage = $adminbbs_size;
  $sql ="select count(*) as ids from $tb where $sel";
  $result = execute($sql);
  if($rs = mysql_fetch_array($result)) $sums = $rs[0];
  $pages = ceil(($sums-0.5)/$eachpage)-1;
  $prepage = ($page>0)?$page-1:0;
  $nextpage = ($page<$pages)?$page+1:$pages;
  $start = $page*$eachpage;
#=====================详情=========================
	$sql = "select username,cid,from_unixtime(a.time,'%Y-%m-%d %h:%m:%s') as time,a.content,b.title from $tb as a,$tb2 as b where a.newsid=b.newsid  and $sel order by $orderby limit $start,$eachpage";
	//echo $sql;
	$result =execute($sql);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>赛因网-专题评论</title>
<style type="text/css">
<!--
.xiaozi,td {
	font-family: Tahoma, Verdana;
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
}
.lianjie {
	font-family: Tahoma, Verdana;
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: 400;
	font-variant: normal;
	text-transform: none;
}
a {
	text-decoration: none;
}
a:hover {
	color: #FF0000;
}
.heizi {
	font-family: Tahoma, Verdana;
	font-size: 12px;
	font-style: normal;
	line-height: normal;
	font-weight: bold;
	font-variant: normal;
	text-transform: none;
}
.x13 {
	font-family: Tahoma, Verdana;
	font-size: 13px;
	font-style: normal;
	line-height: normal;
	font-weight: bold;
	font-variant: normal;
	text-transform: none;
}
.14 {
	font-family: Tahoma, Verdana;
	font-size: 14px;
	font-style: normal;
	line-height: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
}
.14h {
	font-family: Tahoma, Verdana;
	font-size: 14px;
	font-style: normal;
	line-height: normal;
	font-weight: bold;
	font-variant: normal;
	text-transform: none;
}
-->
</style>
<link rel="stylesheet" type="text/css" href="../../admin/cs/pro.css" />
<script src="../../admin/cs/stuHover.js" type="text/javascript"></script>
</head>

<body>
<table width="932" height="82" border="0" align="center" cellpadding="0" cellspacing="0">
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
          <td height="66" valign="top" bgcolor="#D3001F"><!--DWLayoutEmptyCell-->&nbsp; </td>
        </tr>
      </table></td>
    <td width="16" background="../../images/1-2.gif"><img src="../../images/2-2.gif" width="16" height="26"></td>
  </tr>
  <tr>
    <td height="39" background="../../images/1-1.gif">&nbsp;</td>
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
          <td width="10">&nbsp;</td>
          <td width="860" rowspan="3">
		  <? require("../navbar.php");?></td>
          <td width="10">&nbsp;</td>
        </tr>
        <tr>
          <td><img src="../../images/5-3.gif" width="10" height="22"></td>
          <td width="10"><img src="../../images/5-4.gif" width="10" height="22"></td>
        </tr>
        <tr>
          <td background="../../images/3-1.gif">&nbsp;</td>
          <td width="10" background="../../images/3-2.gif">&nbsp;</td>
        </tr>
        <tr>
          <td background="../../images/3-1.gif"></td>
          <td valign="top" background="../../images/3.gif"><table border="0" cellpadding="0" cellspacing="0" background="../../images/3.gif" class="xiaozi">
              <!--DWLayoutTable-->
              <tr class="xiaozi">
                <td width="871" rowspan="2" align="center" valign="top">
				  
				  <table width="99%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #000000 ">
                    <tr>
                      <th height="22" scope="col">专题评论管理</th>
                    </tr>
                    <tr>
                      <td>
					  <form name="bbsform" action="" method="post" onSubmit="return confirm('你确定要删除选择的？');">
					  <table width="100%"  border="1" align="center" cellpadding="0" cellspacing="0"   bordercolor="#6595d6" bordercolordark="#EEEEEE" class="xiaozi">
                        <tr bgcolor="#EBEFF7">
                          <th width="2%" height="23" scope="col"><a href="<? echo $PHP_SELF."?order=";?>">ID</a></th>
                          <th width="10%" height="23" scope="col"><a href="<? echo $PHP_SELF."?order=username";?>">用户名</a></th>
                          <th width="18%" height="23" scope="col"><a href="<? echo $PHP_SELF."?order=title";?>">专题标题</a></th>
                          <th width="58%" height="23" scope="col">评论内容</th>
                          <th width="8%" height="23" scope="col">时间</th>
                          <th width="4%" height="23" scope="col">删除</th>
                        </tr>
<?

#===============构造查询字符串=====================
$queryString ="order=$order&bd=$bd"; 


while($rs = mysql_fetch_array($result))
{
?>						
                        <tr>
                          <td><?=$rs[cid]?></td>
                          <td align="center"><?=$rs[username]?>&nbsp;</td>
                          <td title="<?=$rs[title]?>"><?=wordscut($rs[title],22);?>&nbsp;</td>
                          <td><?=$rs[content]?>&nbsp;</td>
                          <td><?=$rs[time]?>&nbsp;</td>
                          <td>&nbsp;
                            <input name="delid[]" type="checkbox" id="delid[]" value="<?=$rs[cid]?>" ></td>
                        </tr>
<? }?>	
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td align="center"><input name="delbtn" type="submit" class="button" id="delbtn" value="删除选择的评论">
&nbsp;&nbsp;
<input name="Submit2" type="reset" class="button" value="重置"></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td colspan="6">
<div align="right"> 
                                <?
							  $shownum =10/2;
							  $startpage = ($page>=$shownum)?$page-$shownum:0;
							  $endpage = ($page+$shownum<=$pages)?$page+$shownum:$pages;
							   
							 echo "共".($pages+1)."页&nbsp;&nbsp;"; 
							  echo "<a href=$PHP_SELF?page=0&$queryString>第一页</a>";
							  if($startpage>0)
							    echo " ... <b><a href=$PHP_SELF?page=".($page-$shownum*2)."&$queryString>&laquo;</a></b>";
							  for($i=$startpage;$i<=$endpage;$i++)
							  {
							    if($i==$page)
								  echo "&nbsp;<b><font color=#0000ff>".($i+1)."</font></b>&nbsp;";
								else
								  echo "&nbsp;<a href=$PHP_SELF?page=$i&$queryString>".($i+1)."</a>&nbsp;";
							   }
							   if($endpage<$pages)
							    echo "<b><a href=$PHP_SELF?page=".($page+$shownum*2)."&$queryString>&raquo;</a></b> ... ";
                               echo "<a href=$PHP_SELF?page=$pages&$queryString>最后页</a>";
							 ?>
                              </div>						  
						 </td>
                          </tr>
					
                      </table></form></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
                <td width="11"></td>
              </tr>
              <tr class="xiaozi">
                <td></td>
              </tr>
              <tr class="xiaozi">
                <td colspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
              </tr>
            </table></td>
          <td background="../../images/3-2.gif"></td>
        </tr>
        <tr>
          <td><img src="../../images/3-4.gif" width="10" height="10"></td>
          <td background="../../images/3-3.gif"></td>
          <td><img src="../../images/3-5.gif" width="10" height="10"></td>
        </tr>
      </table>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td height="28" valign="middle"><img src="../../images/copyright.gif" width="296" height="23"></td>
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
</html>