<?php
	//require("..\\func.php");
    require('../func.php');
	//接受 请求
	$comid=$HTTP_GET_VARS['comid'];
	//查询 数据
	$sqlstr="select * from edu_cominfo";
	$sqlstr=$sqlstr." where comid=$comid";
	$sqlstr=$sqlstr." order by comid desc ";

	
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
<table width="90%" height="308" align="center">
  <tr>
    <td height="15">
      <table width="100%" border="1" cellpadding="1" cellspacing="1" class="xiaozi">
        <tr>
          <td width="25%" height="33">
          <div align="right"><span class="xiaozi">姓名：</span></div>          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo($arr['name']);?>
            </span></td>
        </tr>
        <tr>
          <td width="25%" height="35">
          <div align="right"><span class="xiaozi">性别：</span></div>          </td>
          <td width="75%"><span class="xiaozi">
           <? $k=$arr['sex'];
		   if($k=0){
		   echo "男";
		   }
		   else
		   {
		   echo "女";
		   }
		 
		   ?>
			
            </span></td>
        </tr>
        <tr>
          <td width="25%" height="32">
          <div align="right"><span class="xiaozi">公司机构名称： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
     <?php echo($arr['comname']);?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%" height="36">
          <div align="right"><span class="xiaozi">身份证号码： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
          <?php echo($arr['status']);?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%" height="38">
          <div align="right"><span class="xiaozi">职位： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
           <?php echo($arr['position']);?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%" height="34">
          <div align="right"><span class="xiaozi">电子邮件： </span></div>          </td>
          <td width="75%" height="34"><span class="xiaozi">
            <?php echo($arr['email']);?>
            </span></td>
        </tr>
        <tr>
          <td width="25%" height="39">
          <div align="right"><span class="xiaozi">固定电话：</span></div>          </td>
          <td width="75%" height="39">
          <?php echo($arr['telephone']);?>      </td>
        </tr>
        <tr>
          <td width="25%" height="39" valign="top">
          <div align="right"><span class="xiaozi">移动电话：</span></div>          </td>
          <td width="75%">
            <?php echo ($arr['phone'])?>          </td>
        </tr>
        <tr>
          <td width="25%" height="46">
          <div align="right">地址：</div>          </td>
          <td width="75%" height="46"><span class="xiaozi"> 
            <?php echo($arr['address']);?>
           </span></td>
        </tr>
		 <tr>
          <td width="25%" height="51">
            <div align="right">课程总费用：<span class="xiaozi"></span></div>          </td>
          <td width="75%" height="51"> <span class="xiaozi">
            <?php echo($arr['demo']);?>
            </span> </td>
        </tr>
        <tr>
          <td width="25%" height="51">
            <div align="right">所选课程信息：<span class="xiaozi">
            </span></div>          </td>
          <td width="75%" height="51"> <span class="xiaozi">
            <?php echo($arr['other']);?>
         </span> </td>
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