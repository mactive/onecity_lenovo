<?php

	//require("..\\func.php");
    require('../func.php');
    $comid=$HTTP_GET_VARS['comid'];
    $sqlstr="delete from edu_group where comid=".$comid;
    
    $result=execute($sqlstr);
    
    //����ԭ����ҳ��
    header("Location:group.php");
?>