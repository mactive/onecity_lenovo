<?php
	//require("..\\func.php");
    require('../func.php');
	//���� ����
	$newsid=$HTTP_GET_VARS['newsid'];
	//��ѯ ����
	$sqlstr="select * from tb_zt,tb_zt_type";
	$sqlstr=$sqlstr." where tb_zt.newstype=tb_zt_type.tpid ";
	$sqlstr=$sqlstr." and tb_zt.newsid=$newsid";

	
	$result = execute($sqlstr);
	
	if($arr=mysql_fetch_array($result))
{
	//��ʼ�� ��ʾ����
	$img_dir='../';
	$titu=$arr[titu]==0?"��":"<img src=".$img_dir.$IMG_PATH.$arr[titu].">";
	$ftitu=$arr[ftitu]==0?"��":"<img src=".$img_dir.$IMG_PATH.$arr[ftitu].">";
	$img=$arr[img]==0?"��":"<img src=".$img_dir.$IMG_PATH.$arr[img].">";

	$arr_sp=get_type_str($arr[sp_type]);
	$arr_wz=get_type_str($arr[wz_type]);
	$arr_ys=get_type_str($arr[ys_type]);

	$sp_key=$arr[sp_key]==""?"��":$arr[sp_key];
	$wz_key=$arr[wz_key]==""?"��":$arr[wz_key];
	$ys_key=$arr[ys_key]==""?"��":$arr[ys_key];
?>

<html>
<head>
<title>ר�����</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<table width="90%" height="308">
  <tr>
    <td height="15">
      <table width="102%" border="1" cellpadding="1" cellspacing="1" class="xiaozi">
        <tr>
          <td width="25%">
            <div align="right"><span class="xiaozi">���⣺</span></div>
          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo($arr[title]);?>
            </span></td>
        </tr>
        <tr>
          <td width="25%">
            <div align="right"><span class="xiaozi">ר�����ͣ�</span></div>
          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo $arr[tpname]?>
            </span></td>
        </tr>
        <tr>
          <td width="25%">
            <div align="right"><span class="xiaozi">�Ƽ�ר�⣺ </span></div>
          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo($arr[tuijian]?"��":"��")?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%">
            <div align="right"><span class="xiaozi">��ͼ�� </span></div>
          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo $titu?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%">
            <div align="right"><span class="xiaozi">����ͼ�� </span></div>
          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo $ftitu?>
            </span></td>
        </tr>
        <tr bgcolor="#ddddCC">
          <td width="25%" height="11">
            <div align="right"><span class="xiaozi">С��ͼ�� </span></div>
          </td>
          <td width="75%" height="11"><span class="xiaozi">
            <?php echo $img?>
            </span></td>
        </tr>
        <tr>
          <td width="25%" height="30">
            <div align="right"><span class="xiaozi">������</span></div>
          </td>
          <td width="75%" height="30">
            <?php echo ($arr[jinshu]==""?"��":$arr[jinshu])?>
          </td>
        </tr>
        <tr>
          <td valign="top" width="25%">
            <div align="right"><span class="xiaozi">���ݣ�</span></div>
          </td>
          <td width="75%">
            <?php echo ($arr[content])?>
          </td>
        </tr>
        <tr>
          <td width="25%" height="56">
            <div align="right">��Ʒ���ࣺ</div>
          </td>
          <td width="75%" height="56"><span class="xiaozi"> <font color="#FFFFFF">
            <select name="sp_type1" size="1" class="xiaozi">
              <?php echo get_sp_type($arr_sp[0]);?>
            </select>
            <select name="sp_type2" size="1" class="xiaozi">
              <?php echo get_sp_type($arr_sp[1]);?>
            </select>
            <select name="sp_type3" size="1" class="xiaozi">
              <?php echo get_sp_type($arr_sp[2]);?>
            </select>
            <select name="sp_type4" size="1" class="xiaozi">
              <?php echo get_sp_type($arr_sp[3]);?>
            </select>
            <select name="sp_type5" size="1" class="xiaozi">
              <?php echo get_sp_type($arr_sp[4]);?>
            </select>
            <select name="sp_type6" size="1" class="xiaozi">
              <?php echo get_sp_type($arr_sp[5]);?>
            </select>
            </font> </span></td>
        </tr>
        <tr>
          <td width="25%" height="51">
            <div align="right">���·��ࣺ<span class="xiaozi"><font color="#FFFFFF">
              </font></span></div>
          </td>
          <td width="75%" height="51"> <span class="xiaozi"><font color="#FFFFFF">
            <select name="select" size="1" class="xiaozi">
              <?php echo get_wz_type($arr_wz[0]);?>
            </select>
            <select name="select" size="1" class="xiaozi">
              <?php echo get_wz_type($arr_wz[1]);?>
            </select>
            <select name="select" size="1" class="xiaozi">
              <?php echo get_wz_type($arr_wz[2]);?>
            </select>
            <select name="select" size="1" class="xiaozi">
              <?php echo get_wz_type($arr_wz[3]);?>
            </select>
            <select name="select" size="1" class="xiaozi">
              <?php echo get_wz_type($arr_wz[4]);?>
            </select>
            <select name="select" size="1" class="xiaozi">
              <?php echo get_wz_type($arr_wz[5]);?>
            </select>
            </font></span> </td>
        </tr>
        <tr>
          <td width="25%">
            <div align="right">��ɫ�̷��ࣺ<span class="xiaozi"><font color="#FFFFFF">
              </font></span></div>
          </td>
          <td width="75%"><span class="xiaozi"> <font color="#FFFFFF">
            <select name="select2" size="1" class="xiaozi">
              <?php echo get_ys_type($arr_ys[0]);?>
            </select>
            <select name="select2" size="1" class="xiaozi">
              <?php echo get_ys_type($arr_ys[1]);?>
            </select>
            <select name="select2" size="1" class="xiaozi">
              <?php echo get_ys_type($arr_ys[2]);?>
            </select>
            <select name="select2" size="1" class="xiaozi">
              <?php echo get_ys_type($arr_ys[3]);?>
            </select>
            <select name="select2" size="1" class="xiaozi">
              <?php echo get_wz_type($arr_ys[4]);?>
            </select>
            <select name="select2" size="1" class="xiaozi">
              <?php echo get_ys_type($arr_ys[5]);?>
            </select>
            </font></span></td>
        </tr>
        <tr>
          <td width="25%">
            <div align="right">��Ʒ�ؼ���������</div>
          </td>
          <td width="75%">
            <p><span class="xiaozi">
              <?php echo ($sp_key)?>
              </span></p>
          </td>
        </tr>
        <tr>
          <td width="25%">
            <div align="right">���¹ؼ���������</div>
          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo ($wz_key)?>
            </span></td>
        </tr>
        <tr>
          <td width="25%">
            <div align="right">��ɫ�̹ؼ���������</div>
          </td>
          <td width="75%"><span class="xiaozi">
            <?php echo ($ys_key)?>
            </span></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
<?php
	}else{
		echo "���ݿ���û�������¼��";
	}
	//
?>