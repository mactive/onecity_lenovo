<?php
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
$operation = $_POST['operation'];
if($operation=="") $operation =$_GET['operation'];
if($operation=="") die("�޲���ѡ��");

require("../../includes1/db_mysql.php");
require("../../includes1/admin_functions.php");
$db = new CSmysql;

switch($operation){
case "change":
	$tpid = $_POST['tpid']+0;
	$tpname = $_POST['tpname'];
	$db->sql = "update tb_zt_type set tpname='$tpname' where tpid='$tpid'";
	$ok = $db->query();
	if($ok) $msg = "�޸ĳɹ�����ȷ�����ء�";
	else $msg = "�޸�ʧ�ܣ���ȷ�����ء�";
	break;
case "addmain":
case "addsub":
	$tpname = $_POST['tpname'];
	$pid = $_POST['pid']+0;
	$db->sql = "insert into tb_zt_type (tpname,pid) values('$tpname','$pid')";
	$ok = $db->query();
	if($ok) $msg = "��ӳɹ�����ȷ�����ء�";
	else $msg = "���ʧ�ܣ���ȷ�����ء�";
	break;
case "del":

	break;
default:
	$msg = "û�в�������ȷ�����ء�";
}
 ?>
 <script language="javascript">
     <!--
     alert("<?=$msg?>");
	 location.href="admin_zttype.php";
     // -->
 </script>