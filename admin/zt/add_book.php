<?php
require("../../includes1/db_mysql.php");
require("../../includes1/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);

$db = new CSmysql;
    //

    $v1=$_POST['name'];
    $v2=$_POST['courseid'];
    $v3=$_POST['artist'];
    $v4=$_POST['images'];
    $v5=$_POST['publics'];
    $v6=$_POST['price'];
	$v66=$_POST['mei'];
    $v7=reduceHTML($_POST['demo']);
	$v8=reduceHTML($_POST['description']);
	$v9=reduceHTML($_POST['updates']);
   
	//
    $sqlstr="INSERT INTO  book (name,courseid,artist,images,publics," ;
    $sqlstr=$sqlstr."price,mei,demo,description,updates) ";
    $sqlstr=$sqlstr."VALUES('$v1','$v2','$v3','$v4','$v5',";
    $sqlstr=$sqlstr."'$v6','$v66','$v7','$v8','$v9')";
	 $db->query($sqlstr);
header("Location: admin_book.php");
	
?>