<?
include("../../includes1/db_mysql.php");
include("../../includes1/admin_functions.php");
//require("../../news/sitemap.php");
$admindir = explode("/",$_SERVER["PHP_SELF"]);
$db = new CSmysql;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<title>www.sinemedia.com</title>
<script language="JavaScript" type="text/JavaScript">
function delcfm()
{
	if(!confirm("��ȷ��Ҫɾ����")) window.event.returnValue=false;
}
</SCRIPT>
  <table width="100%" height="192" border="0" align="center" cellpadding="0" cellspacing="0"> 
              <tr> 
                <td width="10" height="10" valign="top"><img src="../../images/7.gif" width="10" height="10"></td> 
                <td width="850" background="../../images/7-5.gif"></td> 
                <td width="10" valign="top"><img src="../../images/7-7.gif" width="10" height="10"></td> 
              </tr> 
              <tr> 
                <td height="172" background="../../images/7-1.gif">&nbsp;</td> 
                <td align="center" valign="top" bgcolor="#FFFFFF"> <p><font color="#9c00ce"><a href="registration.php">��������         </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;</font></font><span class="style1"><a href="event_show.php">�γ̷���</a></span></p> 
                  <?php

	//���� ����
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
                        <th width="6%" height="22" nowrap> ���</th> 
                        <th width="38%" nowrap> ��Ŀ</th> 
                        <th width="12%" nowrap> ����</th> 
                        <th width="4%" nowrap> �Ƽ�</th> 
                        <th width="5%" nowrap> ��ͼ</th> 
                        <th width="6%" nowrap> ����ͼ</th> 
                        <th width="5%" nowrap> С��ͼ</th> 
                        <th width="7%" nowrap> ���ͼ</th> 
                        <th width="7%" nowrap> �޸�ͼ</th> 
                        <th width="5%" nowrap> �޸�</th> 
                        <th width="5%" nowrap> ɾ��</th> 
                      </tr> 
                      <?php
		while($arr=$db->fetch()){
			//���� id ,title
			$newsid=$arr[newsid];
			$title=$arr[title];
	?> 
                      <tr> 
                        <td width="6%" height="20"><?=$arr['newsid']?></td> 
                        <td width="38%"><a href="show_news.php?newsid=<?=$arr['newsid']?>" target=_blank><?=$arr['title']?></a></td> 
                        <td width="12%" align="center"> <?php echo $arr[tpname]?></td> 
                        <td width="4%" align="center" bgcolor="#CCCCCC"> <?php echo($arr[tuijian]==1?"��":"��")?></td> 
                        <td width="5%" align="center" bgcolor="#CCCCCC">  <?php echo(($arr[titu]=="0" || $arr[titu]=="")?"��":"��")?> </td> 
                        <td width="6%" align="center" bgcolor="#CCCCCC">  <?php echo(($arr[ftitu]=="0" || $arr[ftitu]=="")?"��":"��")?></td> 
                        <td width="5%" align="center" bgcolor="#CCCCCC"> <?php echo(($arr[img]=="0" || $arr[img]=="")?"��":"��")?> </td> 
                        <td width="7%" align="center" bgcolor="#CCCCCC"><?php echo(($arr['lastimg']=="0" || $arr['lastimg']=="")?"��":"��")?> </td> 
                        <td width="7%" align="center" bgcolor="#CCCCCC"><a href="update_titu.php?newsid=<?=$arr['newsid']?>" target=_blank>����</a></td> 
                        <td width="5%" align="center"><a href="update_news.php?newsid=<?=$arr['newsid']?>" target=_blank  title="�޸�"><img src="../images/b_edit.png" width="16" height="16" border="0"></a></td> 
                        <td width="5%" align="center"><a href="delete_news.php?newsid=<?=$arr['newsid']?>" onClick="delcfm()"  title="ɾ��"><img src="../images/b_drop.png" width="16" height="16" border="0"></a></td> 
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
			</html>
