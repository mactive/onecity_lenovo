<?php

	//require("..\\func.php");
    require('../func.php');
    $comid=$HTTP_GET_VARS['comid'];
    $sqlstr="delete from edu_cominfo where comid=".$comid;
    
    $result=execute($sqlstr);
    
    //返回原来的页面
    header("Location:registration.php");
?>