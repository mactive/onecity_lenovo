<?php
require("../../includes/db_mysql.php");
require("../../includes/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
   if(strtolower($admindir[1])=="admin"){
	require("../check.php");
   }
$db = new CSmysql;
	//接受 请求
	$id=$HTTP_GET_VARS['id'];

	$isupdate=$_POST['okupload'];
	if($isupdate<>""){
		//更新数据库
		//接受 表单
			$id=$_POST['id'];

			$v1=$_POST['name'];
			$v2=$_POST['courseid'];
			$v3=$_POST['artist'];
			$images=$HTTP_POST_VARS['imgname'];
			$v4=$_POST['publics'];
			$v5=$_POST['price'];
			$v55=$_POST['mei'];
			$v6=reduceHTML($_POST['demo']);
			$v7=reduceHTML($_POST['description']);
		    $v8=reduceHTML($_POST['updates']);
	
			$sqlstr="UPDATE book set name='$v1'";
			$sqlstr=$sqlstr." ,courseid='$v2'";
			$sqlstr=$sqlstr." ,artist='$v3'";
			$sqlstr=$sqlstr." ,images='$images'";
			$sqlstr=$sqlstr." ,publics='$v4'";
			$sqlstr=$sqlstr." ,price='$v5'";
			$sqlstr=$sqlstr." ,mei='$v55'";
			$sqlstr=$sqlstr." ,demo='$v6'";
			$sqlstr.=" ,description='$v7'";
			$sqlstr.=" ,updates='$v8'";
			$sqlstr=$sqlstr." where id=".$id;
			//echo $sqlstr;
			
				$db->query($sqlstr);
			
	}
	//查询 数据
	$sqlstr="select * from book";
	$sqlstr=$sqlstr." where id=$id";
	
	$db->query($sqlstr);
	$arr=$db->fetch();
	$img_dir='../';
		$images=($arr[images]=="0" || $arr[images]=="")?"无":"<img src=".$img_dir.$IMG_PATH.$arr[images].">";
	
	//初始化 显示对象
	$name=$arr[name];
	$courseid=$arr[courseid];
	$artist=$arr[artist];
	$publics=$arr[publics];
	$price = $arr[price];
	$mei = $arr[mei];
    $demo = $arr[demo];
	$description = $arr[description];
	$updates = $arr[updates];
?>
<html>
<head>

<script language="JavaScript">
	function upload_img() { //v3.0
		fpath = document.all.img.value;
		fname = document.all.imgname.value;

		if(fpath!=""){
			if(fname=="0" || fname=="")
				fname = getFileName(fpath);
			showmode="height=150, width=200, top=200, left=300, toolbar=no, menubar=no, scrollbars=no, resizable=yes,location=no, status=no";
			window.open("../upload_img.php?filename="+fname+"&filepath="+fpath,"添加图片",showmode);
			document.all.imgname.value=fname;
			}
		else
			alert("请先选择图片，再上传!");
	}
</script>
</head
><title>专题更新</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<script src="../js.js"></script>
<script src="../func.js"></script>
<?require("../edit.htm");?>
<body bgcolor="#FFFFFF" text="#000000"  onload="HTMLArea.replaceAll(config);">
 <div align="center" class="bblue">更新专题
</div>
 <form name="form1" method="post" action="<?php echo $PHP_SELF?>"> 
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" class="thin"> 
          <tr> 
            <th width="18%"> 书名：</th>
            <td width="82%">
              <input name="name" type="text" class="xiaozi" size="25" value="<?php echo($name)?>"></td> 
          </tr> 
           <tr> 
            <th width="18%"> 课程id：</th>
            <td width="82%">
              <input name="courseid" type="text" class="xiaozi" size="25" value="<?php echo($courseid)?>"></td> 
          </tr> 
		             <tr> 
            <th width="18%"> 作者：</th>
            <td width="82%">
              <input name="artist" type="text" class="xiaozi" size="25" value="<?php echo($artist)?>"></td> 
          </tr> 
		   <tr> 
    <td width="24%" valign="middle" bgcolor="#D9F2FF"> <p align="right" style="margin-top: 2"><font face="宋体">图标：</font> </td> 
    <td width="76%" bgcolor="#F2F2F2"><?php echo($images)?></td> 
    </tr> 
		 <tr> 
      <td width="24%" height="5" align="right" bgcolor="#D9F2FF"><font face="宋体">新图标：</font> </td> 
      <td width="76%" bgcolor="#F2F2F2" height="5"> <input type="text" name="imgname" value="<?php echo($arr[images])?>"> 
        <a href="#" onClick="openwin('imgname');"><b><font face="宋体"> </font><font color="#FF0000">[重新上传]</font></b></a> </td> 
    </tr>  
          <tr> 
            <th> 价格：</th>
            <td> <input name="price" type="text" class="xiaozi" size="50" value="<?php echo($price)?>"></td> 
          </tr> 
          <tr>
		  <tr> 
            <th>美元价格：</th>
            <td> <input name="mei" type="text" class="xiaozi" size="50" value="<?php echo($mei)?>"></td> 
          </tr> 
          <tr>
		           <th>出版信息：</th> 
            <td>
              <input name="publics" type="text" class="xiaozi" id="publics" value="<?php echo($publics)?>" size="50"></td> 
          </tr> 
            <th>简介：</th> 
            <td>
              <input name="demo" type="text" class="xiaozi" id="demo" value="<?php echo($demo)?>" size="50"></td> 
          </tr> 
          <tr> 
            <th valign="top"> 描述：</th>
            <td> <textarea id="description" name="description" rows=20 cols=50  style="width:100%"><?php echo($description); ?></textarea> </td> 
          </tr> 
		    <tr> 
            <th valign="top"> 更新：</th>
            <td> <textarea id="updates" name="updates" rows=20 cols=50  style="width:100%"><?php echo($updates); ?></textarea> </td> 
          </tr> 
       
          <tr> 
            <th height="16">&nbsp; </th>
            <td> <input type="submit" name="okupload" value="提交" class="xiaozi"> 
              <input type="reset" name="cancel" value="重置" class="xiaozi"> 
              <input type="hidden" name="id" value="<?php echo($id)?>"> </td> 
          </tr> 
      </table> 
</form>
</body>
</html>
