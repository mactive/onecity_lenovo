<?php
	//require("..\\func.php");
    require('../func.php');
	//接受 请求
	$couid=$HTTP_GET_VARS['couid'];
	//查询 数据
	$sqlstr="select * from edu_coursemgr";
	$sqlstr=$sqlstr." where couid=$couid";
	$sqlstr=$sqlstr." order by couid desc ";

	
	$result = execute($sqlstr);
	
	if($arr=mysql_fetch_array($result))
{
?>

<html>
<head>
<title>个人报名浏览</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<style type="text/css">
<!--
.style1 {color: #9900FF}
-->
</style>
</head>

<body bgcolor="#FFFFFF" text="#000000">
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center"><font color="#9c00ce"><a href="registration.php">个人报名管理 </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;</font></font><span class="style1"><a href="group.php">团体报名管理</a></span><font color="#9c00ce"><a href="registration.php"> </a><font color="#FF0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="course.php">课程管理</a>&nbsp;</font></font></p>
<table width="90%" height="289" align="center">
  <tr>
    <td height="283">
      <table width="100%" border="1" cellpadding="1" cellspacing="1" class="xiaozi">
        <tr>
          <td width="25%" height="33">
          <div align="right"><span class="xiaozi">课程名称：</span></div>          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo($arr['couname']);?>
            </span></td>
        </tr>
        <tr>
          <td width="25%" height="35">
          <div align="right"><span class="xiaozi">培训教师：</span></div>          </td>
          <td width="75%"><span class="xiaozi">
           <?php echo($arr['teacher']);?>
			
            </span></td>
        </tr>
        <tr>
          <td width="25%" height="32">
          <div align="right"><span class="xiaozi">开始时间： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
     <?php echo($arr['begdate']);?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%" height="36">
          <div align="right"><span class="xiaozi">结束时间： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
          <?php echo($arr['enddate']);?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%" height="38">
          <div align="right"><span class="xiaozi">培训费用： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
           <?php echo($arr['price']);?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%" height="34">
          <div align="right"><span class="xiaozi">培训地点： </span></div>          </td>
          <td width="75%" height="34"><span class="xiaozi">
            <?php echo($arr['iwhere']);?>
            </span></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
<?php
	}else{
		echo "数据库中没有这个记录！";
	}
	//
?>