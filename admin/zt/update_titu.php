<?php
require("../../includes/db_mysql.php");
require("../../includes/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
   if(strtolower($admindir[1])=="admin"){
	require("../check.php");
   }
$db = new CSmysql;
//接受 请求
$newsid = $_GET['newsid'];
$isupdate = $_POST['okupload'];
	if($isupdate!=""){
		//更新数据库
		//接受 表单
			$newsid=$_POST['newsid'];
			$tuijian=$_POST['tuijian'];
			$titu=$_POST['tituname'];
			$ftitu=$_POST['ftituname'];
			$imgname=$_POST['imgname'];
			$lastimgname=$_POST['lastimgname'];
			if($isupdate=="提交")
				$sqlstr="UPDATE tb_zt set tuijian='$tuijian',titu='$titu',ftitu='$ftitu',img='$imgname',lastimg='$lastimgname' where newsid=".$newsid;
			if($isupdate=="删除题图")
				$sqlstr="UPDATE tb_zt set titu='0' where newsid=".$newsid;
			if($isupdate=="删除副题图")
				$sqlstr="UPDATE tb_zt set ftitu='0' where newsid=".$newsid;

			//echo $sqlstr;
			$db->query($sqlstr);
			
	}
	//查询 数据
	$db->sql="select * from tb_zt where newsid=".$newsid;
	$db->query();
	$arr=$db->fetch();
	$img_dir="../";

	$tuijian=($arr[tuijian]==1)?"是":"否";
	$titu=($arr[titu]=="0" || $arr[titu]=="")?"无":"<img src=".$img_dir.$IMG_PATH.$arr[titu].">";
	$ftitu=($arr[ftitu]=="0" || $arr[ftitu]=="")?"无":"<img src=".$img_dir.$IMG_PATH.$arr[ftitu].">";
	$img=($arr[img]=="0" || $arr[img]=="")?"无":"<img src=".$img_dir.$IMG_PATH.$arr[img].">";
	$lastimg=($arr[lastimg]=="0" || $arr[lastimg]=="")?"无":"<img src=".$img_dir.$IMG_PATH.$arr[lastimg].">";
	
?>
<html>
<head>
<title>更新题图</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<script src="../js.js"></script>
<script src="../func.js"></script>
</head>
<body> 
<b>浏览</b> 
<table width="80%" height="16" border="0" cellpadding="2" cellspacing="1" class="thin"> 
  <tr> 
    <th width="24%" align="right"> 推荐： </th> 
    <td width="76%"> <?php echo($tuijian)?> </td> 
  </tr> 
  <tr> 
    <th align="right">副题图：</th> 
    <td><?php echo($ftitu)?> &nbsp;</td> 
  </tr> 
  <tr> 
    <th width="24%" align="right"> 题图： </th> 
    <td width="76%"> <?php echo($titu)?> </td> 
  </tr> 
  <tr> 
    <th width="24%" align="right" valign="middle">图标：</th> 
    <td width="76%"><?php echo($img)?></td> 
  </tr> 
  <tr> 
    <th align="right" valign="middle">大图：</th> 
    <td><?php echo($lastimg)?></td> 
  </tr> 
</table> 
<br> 
<hr> 
<b>更改</b> 
<form name="form1" method="post" action="<?php echo $PHP_SELF?>" enctype="multipart/form-data"> 
  <table width="80%" height="16" border="0" cellpadding="0" cellspacing="1" class="thin"> 
    <tr> 
      <th width="24%" align="right" bgcolor="#D9F2FF">推荐： </th> 
      <td width="76%"> <input type=radio value=1 name=tuijian <?php if($tuijian=="是") echo "checked" ?>> 
        是
        <input type=radio value=0 name=tuijian <?php if($tuijian=="否") echo "checked" ?> > 
        否 </span></td> 
    </tr> 
    <tr> 
      <th align="right">副题图：</th> 
      <td><a href="#" onClick="openwin('ftituname');"><b> 
        <input name="ftituname" type="text" value="<?php echo($arr[ftitu])?>" size="30"> 
        重新上传</b></a>&nbsp;</td> 
    </tr> 
    <tr> 
      <th width="24%" align="right" bgcolor="#D9F2FF"> 题图： </th> 
      <td width="76%"> <a href="#" onClick="openwin('tituname');"><b> 
        <input name="tituname" type="text" value="<?php echo($arr[titu])?>" size="30"> 
        重新上传</b></a> </td> 
    </tr> 
    <tr> 
      <td width="24%" height="5" align="right" bgcolor="#D9F2FF">图标： </td> 
      <td width="76%" bgcolor="#F2F2F2" height="5"> <input name="imgname" type="text" value="<?php echo($arr[img])?>" size="30"> 
        <a href="#" onClick="openwin('imgname');">重新上传</a></td> 
    </tr> 
    <tr> 
      <td width="24%" height="5" align="right" bgcolor="#D9F2FF">大图：</td> 
      <td width="76%" bgcolor="#F2F2F2" height="5"> <input name="lastimgname" type="text" value="<?php echo($arr[lastimg])?>" size="30"> 
        <a href="#" onClick="openwin('lastimgname');">重新上传</a></td> 
    </tr> 
    <tr> 
      <th width="24%" align="right" bgcolor="#D9F2FF"> </th> 
      <td width="76%"> </td> 
    </tr> 
    <tr> 
      <th width="24%" align="right">&nbsp;</th> 
      <td width="76%"> <input type="submit" name="okupload" value="提交"> 
        <?php if(!(($arr[titu]=="0")||($arr[titu]==""))) :?> 
        <input type="submit" name="okupload" value="删除题图"> 
        <?php endif ?> 
        <?php if(!(($arr[ftitu]=="0")||($arr[ftitu]==""))) :?> 
        <input type="submit" name="okupload" value="删除副题图"> 
        <?php endif ?> 
        <input type="hidden" name="newsid" value="<?php echo($newsid)?>"> 
&nbsp; </td> 
    </tr> 
    <tr> 
      <th width="24%" align="right">&nbsp;</th> 
      <td width="76%">*注意：只能上传图片文件。<br> 
        *更新图片时先上传新的图片后再提交。 </td> 
    </tr> 
  </table> 
</form> 
<p>&nbsp;</p> 
<p>&nbsp;</p> 
</body>
</html>
