<?php

	//require("..\\func.php");
    require('../func.php');
    $event_id=$HTTP_GET_VARS['event_id'];
    $sqlstr="delete from calendar_events where event_id=".$event_id;
    
    $result=execute($sqlstr);
    
    //����ԭ����ҳ��
    header("Location:event_show.php");
?>