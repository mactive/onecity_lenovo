<?php

	//require("..\\func.php");
    require('../func.php');
    $comid=$HTTP_GET_VARS['comid'];
    $sqlstr="delete from edu_cominfo where comid=".$comid;
    
    $result=execute($sqlstr);
    
    //����ԭ����ҳ��
    header("Location:registration.php");
?>