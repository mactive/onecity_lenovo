<?php

	//require("..\\func.php");
    require('../func.php');
    $couid=$HTTP_GET_VARS['couid'];
    $sqlstr="delete from edu_coursemgr where couid=".$couid;
    
    $result=execute($sqlstr);
    
    //����ԭ����ҳ��
    header("Location:course.php");
?>