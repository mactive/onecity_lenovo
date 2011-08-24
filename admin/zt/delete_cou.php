<?php

	//require("..\\func.php");
    require('../func.php');
    $couid=$HTTP_GET_VARS['couid'];
    $sqlstr="delete from edu_coursemgr where couid=".$couid;
    
    $result=execute($sqlstr);
    
    //返回原来的页面
    header("Location:course.php");
?>