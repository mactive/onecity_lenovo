<?php
require("../../includes1/db_mysql.php");
require("../../includes1/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
$db = new CSmysql;
    //
    $v1=$_POST['newstitle'];
    $v2=$_POST['newstype'];
    $v12=$_POST['tuijian'];
    $v3=$_POST['tituname'];
    $v4=$_POST['ftituname'];
    $v5=$_POST['imgname'];
    $v6=$_POST['jianshu'];
    $v7=reduceHTML($_POST['content']);
    //$v8=$_POST['ys_type'];
    $v9=$_POST['sp_key'];
    $v10=$_POST['wz_key'];
    $v11=$_POST['ys_key'];
    $v13="UNIX_TIMESTAMP()";//set_time();
	$v14 = $_POST["period"];
	$v15 = $_POST["lastimgname"];

	$relidstr = implode(";",$_POST["relid"]);
    //
    $sp_type=$_POST['sp_type1'];
    $sp_type=$sp_type.",".$_POST['sp_type2'];
	$sp_type=$sp_type.",".$_POST['sp_type3'];
    $sp_type=$sp_type.",".$_POST['sp_type4'];
    $sp_type=$sp_type.",".$_POST['sp_type5'];
    $sp_type=$sp_type.",".$_POST['sp_type6'];
    //
    $wz_type=$_POST['wz_type1'];
    $wz_type=$wz_type.",".$_POST['wz_type2'];
	$wz_type=$wz_type.",".$_POST['wz_type3'];
    $wz_type=$wz_type.",".$_POST['wz_type4'];
    $wz_type=$wz_type.",".$_POST['wz_type5'];
    $wz_type=$wz_type.",".$_POST['wz_type6'];
    //
    $ys_type=$_POST['ys_type1'];
    $ys_type=$ys_type.",".$_POST['ys_type2'];
	$ys_type=$ys_type.",".$_POST['ys_type3'];
    $ys_type=$ys_type.",".$_POST['ys_type4'];
    $ys_type=$ys_type.",".$_POST['ys_type5'];
    $ys_type=$ys_type.",".$_POST['ys_type6'];
	//
    $sqlstr="INSERT INTO tb_zt (" ;
    $sqlstr=$sqlstr."title, newstype, tuijian, titu, ftitu, ";
    $sqlstr=$sqlstr."jianshu, img, content, sp_type,wz_type, ys_type, sp_key, wz_key,ys_key,time,period,lastimg,relid) ";
    $sqlstr=$sqlstr."VALUES('$v1','$v2','$v12','$v3','$v4',";
    $sqlstr=$sqlstr."'$v6','$v5','$v7','$sp_type','$wz_type','$ys_type','$v9','$v10','$v11',$v13,'$v14','$v15','$relidstr')";
   // echo $sqlstr;
    
    $db->query($sqlstr);
    

	header("Location: admin_news.php");
?>