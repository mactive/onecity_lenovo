<?php

	//require("..\\func.php");
    require('../func.php');
    $tpid=$HTTP_GET_VARS['tpid'];
    $sqlstr="delete from tb_zt_type where tpid=".$tpid;
    
    $result=execute($sqlstr);
    
    //����ԭ����ҳ��
    header("Location:admin_type.php");
?>