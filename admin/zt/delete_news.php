<?php

	//require("..\\func.php");
    require('../func.php');
    $newsid=$HTTP_GET_VARS['newsid'];
    $sqlstr="delete from tb_zt where newsid=".$newsid;
    
    $result=execute($sqlstr);
    
    //返回原来的页面
    header("Location:admin_news.php");
?>