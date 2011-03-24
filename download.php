<?php
$file_name = trim($_GET['file_path']);
if($file_name){
$file_dir = "./images/upfile/";
	if (!file_exists($file_dir.$file_name)){ 
        echo "pls check again";
        return false;
        exit;
	}else{
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".filesize($file_dir."/".$file_name));
        header( 'Content-Transfer-Encoding: binary' );
        header("Content-Disposition: attachment; filename=" .$file_name); 
        header('Pragma: no-cache');
        header('Expires: 0');
		$file = fopen($file_dir."/".$file_name,"r"); 
        echo fread($file,filesize($file_dir."/".$file_name));
        fclose($file);
        exit;
    }

}else{
     
	echo 'ÄãÖ¸¶¨µÄÎÄ¼þ²»´æÔÚ»òÒÑÉ¾³ý';


    }
?>
