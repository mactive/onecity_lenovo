<?php
require("../../includes/db_mysql.php");
require("../../includes/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
   if(strtolower($admindir[1])=="admin"){
	require("../check.php");
   }
$db = new CSmysql;
//���� ����
$newsid = $_GET['newsid'];
$isupdate = $_POST['okupload'];
	if($isupdate!=""){
		//�������ݿ�
		//���� ��
			$newsid=$_POST['newsid'];
			$tuijian=$_POST['tuijian'];
			$titu=$_POST['tituname'];
			$ftitu=$_POST['ftituname'];
			$imgname=$_POST['imgname'];
			$lastimgname=$_POST['lastimgname'];
			if($isupdate=="�ύ")
				$sqlstr="UPDATE tb_zt set tuijian='$tuijian',titu='$titu',ftitu='$ftitu',img='$imgname',lastimg='$lastimgname' where newsid=".$newsid;
			if($isupdate=="ɾ����ͼ")
				$sqlstr="UPDATE tb_zt set titu='0' where newsid=".$newsid;
			if($isupdate=="ɾ������ͼ")
				$sqlstr="UPDATE tb_zt set ftitu='0' where newsid=".$newsid;

			//echo $sqlstr;
			$db->query($sqlstr);
			
	}
	//��ѯ ����
	$db->sql="select * from tb_zt where newsid=".$newsid;
	$db->query();
	$arr=$db->fetch();
	$img_dir="../";

	$tuijian=($arr[tuijian]==1)?"��":"��";
	$titu=($arr[titu]=="0" || $arr[titu]=="")?"��":"<img src=".$img_dir.$IMG_PATH.$arr[titu].">";
	$ftitu=($arr[ftitu]=="0" || $arr[ftitu]=="")?"��":"<img src=".$img_dir.$IMG_PATH.$arr[ftitu].">";
	$img=($arr[img]=="0" || $arr[img]=="")?"��":"<img src=".$img_dir.$IMG_PATH.$arr[img].">";
	$lastimg=($arr[lastimg]=="0" || $arr[lastimg]=="")?"��":"<img src=".$img_dir.$IMG_PATH.$arr[lastimg].">";
	
?>
<html>
<head>
<title>������ͼ</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<script src="../js.js"></script>
<script src="../func.js"></script>
</head>
<body> 
<b>���</b> 
<table width="80%" height="16" border="0" cellpadding="2" cellspacing="1" class="thin"> 
  <tr> 
    <th width="24%" align="right"> �Ƽ��� </th> 
    <td width="76%"> <?php echo($tuijian)?> </td> 
  </tr> 
  <tr> 
    <th align="right">����ͼ��</th> 
    <td><?php echo($ftitu)?> &nbsp;</td> 
  </tr> 
  <tr> 
    <th width="24%" align="right"> ��ͼ�� </th> 
    <td width="76%"> <?php echo($titu)?> </td> 
  </tr> 
  <tr> 
    <th width="24%" align="right" valign="middle">ͼ�꣺</th> 
    <td width="76%"><?php echo($img)?></td> 
  </tr> 
  <tr> 
    <th align="right" valign="middle">��ͼ��</th> 
    <td><?php echo($lastimg)?></td> 
  </tr> 
</table> 
<br> 
<hr> 
<b>����</b> 
<form name="form1" method="post" action="<?php echo $PHP_SELF?>" enctype="multipart/form-data"> 
  <table width="80%" height="16" border="0" cellpadding="0" cellspacing="1" class="thin"> 
    <tr> 
      <th width="24%" align="right" bgcolor="#D9F2FF">�Ƽ��� </th> 
      <td width="76%"> <input type=radio value=1 name=tuijian <?php if($tuijian=="��") echo "checked" ?>> 
        ��
        <input type=radio value=0 name=tuijian <?php if($tuijian=="��") echo "checked" ?> > 
        �� </span></td> 
    </tr> 
    <tr> 
      <th align="right">����ͼ��</th> 
      <td><a href="#" onClick="openwin('ftituname');"><b> 
        <input name="ftituname" type="text" value="<?php echo($arr[ftitu])?>" size="30"> 
        �����ϴ�</b></a>&nbsp;</td> 
    </tr> 
    <tr> 
      <th width="24%" align="right" bgcolor="#D9F2FF"> ��ͼ�� </th> 
      <td width="76%"> <a href="#" onClick="openwin('tituname');"><b> 
        <input name="tituname" type="text" value="<?php echo($arr[titu])?>" size="30"> 
        �����ϴ�</b></a> </td> 
    </tr> 
    <tr> 
      <td width="24%" height="5" align="right" bgcolor="#D9F2FF">ͼ�꣺ </td> 
      <td width="76%" bgcolor="#F2F2F2" height="5"> <input name="imgname" type="text" value="<?php echo($arr[img])?>" size="30"> 
        <a href="#" onClick="openwin('imgname');">�����ϴ�</a></td> 
    </tr> 
    <tr> 
      <td width="24%" height="5" align="right" bgcolor="#D9F2FF">��ͼ��</td> 
      <td width="76%" bgcolor="#F2F2F2" height="5"> <input name="lastimgname" type="text" value="<?php echo($arr[lastimg])?>" size="30"> 
        <a href="#" onClick="openwin('lastimgname');">�����ϴ�</a></td> 
    </tr> 
    <tr> 
      <th width="24%" align="right" bgcolor="#D9F2FF"> </th> 
      <td width="76%"> </td> 
    </tr> 
    <tr> 
      <th width="24%" align="right">&nbsp;</th> 
      <td width="76%"> <input type="submit" name="okupload" value="�ύ"> 
        <?php if(!(($arr[titu]=="0")||($arr[titu]==""))) :?> 
        <input type="submit" name="okupload" value="ɾ����ͼ"> 
        <?php endif ?> 
        <?php if(!(($arr[ftitu]=="0")||($arr[ftitu]==""))) :?> 
        <input type="submit" name="okupload" value="ɾ������ͼ"> 
        <?php endif ?> 
        <input type="hidden" name="newsid" value="<?php echo($newsid)?>"> 
&nbsp; </td> 
    </tr> 
    <tr> 
      <th width="24%" align="right">&nbsp;</th> 
      <td width="76%">*ע�⣺ֻ���ϴ�ͼƬ�ļ���<br> 
        *����ͼƬʱ���ϴ��µ�ͼƬ�����ύ�� </td> 
    </tr> 
  </table> 
</form> 
<p>&nbsp;</p> 
<p>&nbsp;</p> 
</body>
</html>
