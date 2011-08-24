<?php
	//require("..\\func.php");
    require('../func.php');
	//接受 请求
	$event_id=$HTTP_GET_VARS['event_id'];
	//查询 数据
	$sqlstr="select * from calendar_events";
	$sqlstr=$sqlstr." where event_id=$event_id";
	$sqlstr=$sqlstr." order by event_id desc ";

	
	$result = execute($sqlstr);
	
	if($arr=mysql_fetch_array($result))
{
?>

<html>
<head>
<title>课程发布浏览</title>
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
<p align="center"><font color="#9c00ce"><a href="../../AATC/event_add.php">课程添加<font color="#FF0000">&nbsp;</font></a><font color="#FF0000">&nbsp;&nbsp;<a href="event_show.php">课程管理&nbsp;</a></font></font></p>
<table width="90%" height="308" align="center">
  <tr>
    <td height="15">
      <table width="100%" border="1" cellpadding="1" cellspacing="1" class="xiaozi">
        <tr>
          <td width="25%" height="33">
          <div align="right"><span class="xiaozi">年：</span></div>          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo($arr['event_year']);?>
            </span></td>
        </tr>
        <tr>
          <td width="25%" height="35">
          <div align="right"><span class="xiaozi">月：</span></div>          </td>
          <td width="75%"><span class="xiaozi">
           <?php echo($arr['event_month']);?>
			
            </span></td>
        </tr>
        <tr>
          <td width="25%" height="32">
          <div align="right"><span class="xiaozi">日： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
     <?php echo($arr['event_day']);?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%" height="36">
          <div align="right"><span class="xiaozi">时间： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
          <?php echo($arr['event_time']);?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%" height="38">
          <div align="right"><span class="xiaozi">标题： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
           <?php echo($arr['event_title']);?>
            </span></td>
        </tr>
		 <tr bgcolor="#ddddCC">
          <td width="25%" height="38">
          <div align="right"><span class="xiaozi">课程链接： </span></div>          </td>
          <td width="75%"><span class="xiaozi">
           <?php echo($arr['event_link']);?>
            </span></td>
        </tr>
        <tr>
          <td width="25%" height="51">
            <div align="right">具体内容：<span class="xiaozi">
            </span></div>          </td>
          <td width="75%" height="51"> <span class="xiaozi">
            <?php echo($arr['event_desc']);?>
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