<?php
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
$operation = $_POST['operation'];
if($operation=="") $operation =$_GET['operation'];
if($operation=="") die("无操作选择。");

require("../../includes1/db_mysql.php");
require("../../includes1/admin_functions.php");
$db = new CSmysql;

switch($operation){
case "change":
	$tpid = $_POST['tpid']+0;
	$tpname = $_POST['tpname'];
	$db->sql = "update tb_zt_type set tpname='$tpname' where tpid='$tpid'";
	$ok = $db->query();
	if($ok) $msg = "修改成功！按确定返回。";
	else $msg = "修改失败！按确定返回。";
	break;
case "addmain":
case "addsub":
	$tpname = $_POST['tpname'];
	$pid = $_POST['pid']+0;
	$db->sql = "insert into tb_zt_type (tpname,pid) values('$tpname','$pid')";
	$ok = $db->query();
	if($ok) $msg = "添加成功！按确定返回。";
	else $msg = "添加失败！按确定返回。";
	break;
case "del":

	break;
default:
	$msg = "没有操作，按确定返回。";
}
 ?>
 <script language="javascript">
     <!--
     alert("<?=$msg?>");
	 location.href="admin_zttype.php";
     // -->
 </script>