<?php
require("../../includes/db_mysql.php");
require("../../includes/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
   if(strtolower($admindir[1])=="admin"){
	require("../check.php");
   }
$db = new CSmysql;

	$isupdate=$_POST['okupload'];
	if($isupdate<>""){
	//接受 请求
		//更新数据库
		//接受 表单
			$v1=$_POST['couname'];
			$v2=$_POST['teacher'];
			$v3=$_POST['begdate'];
			$v4=$_POST['enddate'];
			$v5=$_POST['price'];
			$v6=$_POST['iwhere'];
		

	$sqlstr="INSERT INTO edu_coursemgr (" ;
    $sqlstr=$sqlstr."couname,teacher,begdate,enddate,price,iwhere) ";
    $sqlstr=$sqlstr."VALUES('$v1','$v2','$v3','$v4','$v5','$v6')";
  				$db->query($sqlstr);
		header("Location: course.php");		
	}    
	//查询 数据

?>
<html>
<head>
<title>培训课程更新</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<script src="../js.js"></script>
<script src="../func.js"></script>
<?require("../edit.htm");?>
<style type="text/css">
<!--
.style1 {color: #9900FF}
-->
</style>
</head>
<body bgcolor="#FFFFFF" text="#000000"  onload="HTMLArea.replaceAll(config);">
 <div align="center" class="bblue">
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p><font color="#9c00ce"><a href="registration.php">个人报名管理 </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;</font></font><span class="style1"><a href="group.php">团体报名管理</a></span><font color="#9c00ce"><a href="registration.php"> </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="course.php">课程管理</a>&nbsp;</font></font></p>
</div>
 <form name="form1" method="post" action="<?php echo $PHP_SELF?>" enctype="multipart/form-data" style="MARGIN: 0px" onSubmit="return(submitform());">
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" class="thin"> 
          <tr> 
            <th width="18%">课程名称：</th>
            <td width="82%">
              <input name="couname" type="text" class="xiaozi" size="25"></td> 
          </tr> 
          <tr> 
            <th> 培训教师：</th>
            <td>
               <input name="teacher" type="text" class="xiaozi" size="25" ></td> 
          </tr> 
          <tr> 
            <th> 开始时间：</th>
            <td> <input name="begdate" type="text" class="xiaozi" size="50"> </td> 
          </tr> 
          <tr> 
            <th>结束时间：</th> 
            <td>
              <input name="enddate" type="text" class="xiaozi" id="status"></td> 
          </tr> 
          <tr> 
            <th valign="top"> 培训费用：</th>
            <td>   <input name="price" type="text" class="xiaozi" size="25"> </td> 
          </tr> 
          <tr> 
            <th>培训地点：</th> 
            <td>
             <input name="iwhere" type="text" class="xiaozi" size="25"></td> 
          </tr> 
              
          <tr> 
            <th>&nbsp; </th>
            <td>&nbsp;</td> 
          </tr> 
          <tr> 
            <th height="16">&nbsp; </th>
            <td> <input type="submit" name="okupload" value="提交" class="xiaozi"> 
              <input type="reset" name="cancel" value="重置" class="xiaozi"> 
          </tr> 
   </table> 
</form>
</body>
</html>
