<?php
require("../../includes/db_mysql.php");
require("../../includes/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
   if(strtolower($admindir[1])=="admin"){
	require("../check.php");
   }
$db = new CSmysql;
	//���� ����
	$event_id=$HTTP_GET_VARS['event_id'];

	$isupdate=$_POST['okupload'];
	if($isupdate<>""){
		//�������ݿ�
		//���� ��
			$comid=$_POST['event_id'];

			$v1=$_POST['event_year'];
			$v2=$_POST['event_month'];
			$v3=$_POST['event_day'];
			$v4=$_POST['event_time'];
			$v5=$_POST['event_title'];
			$v6=$_POST['event_link'];
			$v7=$_POST['event_desc'];
				
			//
			$sqlstr="UPDATE calendar_events set event_year='$v1'";
			$sqlstr=$sqlstr." ,event_month='$v2'";
			$sqlstr=$sqlstr." ,event_day='$v3'";
			$sqlstr=$sqlstr." ,event_time='$v4'";
			$sqlstr=$sqlstr." ,event_title='$v5'";
			$sqlstr=$sqlstr." ,event_link='$v6'";
			$sqlstr=$sqlstr." ,event_desc='$v7'";
			$sqlstr=$sqlstr." where event_id=".$event_id;
			//echo $sqlstr;
			
				$db->query($sqlstr);
			
	}
	//��ѯ ����
	$sqlstr="select * from calendar_events";
	$sqlstr=$sqlstr." where event_id=$event_id ";
		
	$db->query($sqlstr);
	$arr=$db->fetch();
	//��ʼ�� ��ʾ����
	$event_year=$arr[event_year];
	$event_month=$arr[event_month];
	$event_day=$arr[event_day];
	$event_time=$arr[event_time];
	$event_title = $arr[event_title];
	$event_link = $arr[event_link];
	$event_desc=$arr[event_desc];
?>
<html>
<head>
<title>�γ̷�������</title>
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
   <p><font color="#9c00ce"><a href="../../AATC/event_add.php">�γ����<font color="#FF0000">&nbsp;</font></a><font color="#FF0000">&nbsp;&nbsp;<a href="event_show.php">�γ̹���&nbsp;</a></font></font></p>
</div>
 <form name="form1" method="post" action="<?php echo $PHP_SELF?>"> 
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" class="thin"> 
          <tr> 
            <th width="18%">�꣺</th>
            <td width="82%">
              <input name="event_year" type="text" class="xiaozi" size="25" value="<?php echo($event_year)?>"></td> 
          </tr> 
          <tr> 
            <th> �£�</th>
            <td>
               <input name="event_month" type="text" class="xiaozi" size="25" value="<?php echo($event_month)?>">               </td> 
          </tr> 
          <tr> 
            <th> �գ�</th>
            <td> <input name="event_day" type="text" class="xiaozi" size="50" value="<?php echo($event_day)?>"></td> 
          </tr> 
          <tr> 
            <th>ʱ�䣺</th> 
            <td>
              <input name="event_time" type="text" class="xiaozi" id="status" value="<?php echo($event_time)?>" size="50"></td> 
          </tr> 
          <tr> 
            <th valign="top"> ���⣺</th>
            <td>   <input name="event_title" type="text" class="xiaozi" size="25" value="<?php echo($event_title)?>"> </td> 
          </tr> 
           <tr> 
            <th valign="top"> �γ����ӣ�</th>
            <td>   <input name="event_link" type="text" class="xiaozi" size="25" value="<?php echo($event_link)?>"> </td> 
          </tr> 
           <th>��ϸ���ݣ�</th>
            <td>
              <textarea name="event_desc" cols="80" rows="10" class="xiaozi"><?php echo($event_desc) ?></textarea></td> 
          </tr> 
       
          <tr> 
            <th>&nbsp; </th>
            <td>&nbsp;</td> 
          </tr> 
          <tr> 
            <th height="16">&nbsp; </th>
            <td> <input type="submit" name="okupload" value="�ύ" class="xiaozi"> 
              <input type="reset" name="cancel" value="����" class="xiaozi"> 
              <input type="hidden" name="comid" value="<?php echo($event_id)?>"> </td> 
          </tr> 
   </table> 
</form>
</body>
</html>
