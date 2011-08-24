<?php

	//require("..\\func.php");
    require('../func.php');
    $id=$HTTP_GET_VARS['id'];
    $sqlstr="delete from book where id=".$id;
    
    $result=execute($sqlstr);
    
    //返回原来的页面
    header("Location:admin_book.php");
?>