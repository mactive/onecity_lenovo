<?php

	//require("..\\func.php");
    require('../func.php');
	//���� ����
	$newsid=$HTTP_GET_VARS['newsid'];

	$isupdate=$HTTP_POST_VARS['okupload'];
	if($isupdate!=""){
		//�������ݿ�
		//���� ��
			$newsid=$HTTP_POST_VARS['newsid'];
			$imgname=$HTTP_POST_VARS['imgname'];
			$lastimgname=$HTTP_POST_VARS['lastimgname'];
			$sqlstr="UPDATE tb_zt set img='$imgname',lastimg='$lastimgname' where newsid=".$newsid;
			echo $sqlstr;
			
				$result=execute($sqlstr);
			
	}
	//��ѯ ����
	$sqlstr="select img,lastimg from tb_zt where newsid=".$newsid;
	
		$result=execute($sqlstr);
		$arr=mysql_fetch_array($result);
		$img_dir='../';
		$img=($arr[img]=="0" || $arr[img]=="")?"��":"<img src=".$img_dir.$IMG_PATH.$arr[img].">";
		$lastimg=($arr[lastimg]=="0" || $arr[lastimg]=="")?"��":"<img src=".$img_dir.$IMG_PATH.$arr[lastimg].">";
	
?>
<html>
<head>
<title>����Сͼ��</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<script src="../js.js"></script>
<script src="../func.js"></script>
<script language="JavaScript">
	function upload_img() { //v3.0
		fpath = document.all.img.value;
		fname = document.all.imgname.value;

		if(fpath!=""){
			if(fname=="0" || fname=="")
				fname = getFileName(fpath);
			showmode="height=150, width=200, top=200, left=300, toolbar=no, menubar=no, scrollbars=no, resizable=yes,location=no, status=no";
			window.open("../upload_img.php?filename="+fname+"&filepath="+fpath,"���ͼƬ",showmode);
			document.all.imgname.value=fname;
			}
		else
			alert("����ѡ��ͼƬ�����ϴ�!");
	}
</script>
</head>
<body bgcolor="#FFFFFF" text="#000000"> 
<b>���</b> 
<table border="0" width="80%" cellpadding="0" height="16"> 
  <tr> 
    <td width="24%" valign="middle" bgcolor="#D9F2FF"> <p align="right" style="margin-top: 2"><font face="����">ͼ�꣺</font> </td> 
    <td width="76%" bgcolor="#F2F2F2"><?php echo($img)?></td> 
  </tr> 
  <tr> 
    <td align="right" valign="middle" bgcolor="#D9F2FF">��ͼ��</td> 
    <td bgcolor="#F2F2F2"><?php echo($lastimg)?></td> 
  </tr> 
</table> 
<br> 
<hr> 
<form name="form1" method="post" action="<?php echo $PHP_SELF?>" enctype="multipart/form-data"> 
  <b>����</b> 
  <table border="0" width="80%" cellpadding="0" height="16"> 
    <tr> 
      <td width="24%" height="5" align="right" bgcolor="#D9F2FF"><font face="����">��ͼ�꣺</font> </td> 
      <td width="76%" bgcolor="#F2F2F2" height="5"> <input type="text" name="imgname" value="<?php echo($arr[img])?>"> 
        <a href="#" onClick="openwin('imgname');"><b><font face="����"> </font><font color="#FF0000">[�����ϴ�]</font></b></a> </td> 
    </tr> 
    <tr> 
      <td width="24%" height="5" align="right" bgcolor="#D9F2FF"><font face="����">��ͼ��</font> </td> 
      <td width="76%" bgcolor="#F2F2F2" height="5"> <input type="text" name="lastimgname" value="<?php echo($arr[lastimg])?>"> 
        <a href="#" onClick="openwin('lastimgname');"><b><font face="����"> </font><font color="#FF0000">[�����ϴ�]</font></b></a> </td> 
    </tr> 
	    <tr> 
      <td width="24%">&nbsp;</td> 
      <td width="76%" bgcolor="#F2F2F2"> <input type="submit" name="okupload" value="�ύ"> 
        <input type="button" name="cancel" value="ȡ��"> 
        <font face="����"> 
        <input type="hidden" name="newsid" value="<?php echo($newsid)?>"> 
        </font> </td> 
    </tr> 
    <tr> 
      <td width="24%">&nbsp;</td> 
      <td width="76%" bgcolor="#F2F2F2"><font color="#FF0000" face="����">*ע�⣺ֻ���ϴ�ͼƬ�ļ���</font><br> 
        <font color="#FF0000">*����ͼƬʱ���ϴ��µ�ͼƬ�����ύ��</font> </td> 
    </tr> 
  </table> 
</form> 
<p>&nbsp;</p> 
<p>&nbsp;</p> 
</body>
</html>
