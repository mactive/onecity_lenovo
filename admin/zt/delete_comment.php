
<html>
<head>
<title>评论管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<?php
	//require("..\\func.php");
    require('../func.php');

	//接受 请求
	$newsid=$HTTP_GET_VARS['newsid'];
	$title=$HTTP_GET_VARS['title'];
	$operate=$HTTP_POST_VARS['del'];

	if($operate<>"")
	{
			//提交后
			$newsid=$HTTP_POST_VARS['newsid'];
			$title=$HTTP_POST_VARS['title'];
			if($operate=="全部删除"){
				
					$sqlstr="delete from tb_zt_comment where newsid=".$newsid;
					//echo $sqlstr;
					$result=execute($sqlstr);
				
			}else{
				$ckarr=$HTTP_POST_VARS[ck];
				
				while(list($k,$v)=each($ckarr)){
					$sqlstr="delete from tb_zt_comment where cid=".$v;
					$result=execute($sqlstr);
				}
				
			}

	}

	//查询 数据
	$sqlstr = "select * from tb_zt_comment where newsid=".$newsid." order by cid desc ";
	//$sqlstr = "select * from tb_zt_comment order by cid desc ";
	
		$result=execute($sqlstr);
	
		$count=mysql_num_rows($result);
		if($count<1)
			echo "没有评论!";
		else{
?>

<form name="form2" method="post" action="<?php echo $PHP_SELF?>"  style="MARGIN: 0px">
  <table border="1" width="80%" bordercolorlight="#CEEFFF" cellspacing="0" cellpadding="0" bordercolordark="#C0C0C0" height="50">
    <tr>
      <td width="10%" align="center" height="20">新闻题目</td>
      <td width="49%" align="center" height="20">评论内容</td>
      <td width="11%" align="center" height="20">时间</td>
      <td width="17%" align="center" height="20"><font face="宋体">作者</font></td>
      <td width="13%" align="center" height="20">选择</td>
    </tr>
	<?php
		while($arr=mysql_fetch_array($result)){
			$cid=$arr[cid];
			$content=$arr[content]==""?"空":$arr[content];
			$time=$arr[time];
			$username=get_user_name($arr[cuserid]);

	?>
    <tr>
      <td width="10%" height="22">
        <?php echo($title) ?>
      </td>
      <td width="49%" height="22">
        <?php echo($content) ?>
      </td>
      <td width="11%" height="22" align="center">
        <?php echo($time) ?>
      </td>
      <td width="17%" align="center" height="22">
        <?php echo($username) ?>
      </td>
      <td width="13%" align="center" height="22">
        <input type="checkbox" name="ck[]" value="<?php echo($cid)?>">
      </td>
    </tr>
	<?php
		}
	?>
    <tr>
      <td colspan="5" height="22">
        <div align="right">
          <input type="submit" name="del" value="全部删除">
          <input type="submit" name="del" value="删除所选项">
          <font face="宋体">
          <input type="hidden" name="newsid" value="<?php echo($newsid)?>">
          <input type="hidden" name="title" value="<?php echo($title)?>">
          </font> </div>
      </td>
    </tr>
  </table>

  </form>
   <?php
 	}
	
	//========================
?>
</body>
</html>