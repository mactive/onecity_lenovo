<?php

	//require("..\\func.php");
    require('../func.php');
    $id=$HTTP_GET_VARS['id'];
    $sqlstr="delete from book where id=".$id;
    
    $result=execute($sqlstr);
    
    //����ԭ����ҳ��
    header("Location:admin_book.php");
?>