<?php
require("../../includes/db_mysql.php");
require("../../includes/admin_functions.php");
  $admindir = explode("/",$_SERVER["PHP_SELF"]);
   if(strtolower($admindir[1])=="admin"){
	require("../check.php");
   }
$db = new CSmysql;
	//接受 请求
	$newsid=$HTTP_GET_VARS['newsid'];

	$isupdate=$_POST['okupload'];
	if($isupdate<>""){
		//更新数据库
		//接受 表单
			$newsid=$_POST['newsid'];

			$v1=$_POST['newstitle'];
			$v2=$_POST['newstype'];
			$v3=$_POST['jianshu'];
			$v4=reduceHTML($_POST['content']);
			$v5=$_POST['sp_key'];
			$v6=$_POST['wz_key'];
			$v7=$_POST['ys_key'];
			$v8=$_POST["period"];
			$prelid = $_POST["relid"];
			for($i=0;$i<sizeof($prelid);$i++)	
			{
				$prelid[$i] = str_replace("，",",",$prelid[$i]);
				$prelid[$i] = trim($prelid[$i],',');
			}
			$relidstr=implode(";",$prelid);
			//
			$sp_type=$_POST['sp_type1'];
			$sp_type=$sp_type.",".$_POST['sp_type2'];
			$sp_type=$sp_type.",".$_POST['sp_type3'];
			$sp_type=$sp_type.",".$_POST['sp_type4'];
			$sp_type=$sp_type.",".$_POST['sp_type5'];
			$sp_type=$sp_type.",".$_POST['sp_type6'];
			//
			$wz_type=$_POST['wz_type1'];
			$wz_type=$wz_type.",".$_POST['wz_type2'];
			$wz_type=$wz_type.",".$_POST['wz_type3'];
			$wz_type=$wz_type.",".$_POST['wz_type4'];
			$wz_type=$wz_type.",".$_POST['wz_type5'];
			$wz_type=$wz_type.",".$_POST['wz_type6'];
			//
			$ys_type=$_POST['ys_type1'];
			$ys_type=$ys_type.",".$_POST['ys_type2'];
			$ys_type=$ys_type.",".$_POST['ys_type3'];
			$ys_type=$ys_type.",".$_POST['ys_type4'];
			$ys_type=$ys_type.",".$_POST['ys_type5'];
			$ys_type=$ys_type.",".$_POST['ys_type6'];
			//
			$sqlstr="UPDATE tb_zt set title='$v1'";
			$sqlstr=$sqlstr." ,newstype='$v2'";
			$sqlstr=$sqlstr." ,jianshu='$v3'";
			$sqlstr=$sqlstr." ,content='$v4'";
			$sqlstr=$sqlstr." ,sp_key='$v5'";
			$sqlstr=$sqlstr." ,wz_key='$v6'";
			$sqlstr=$sqlstr." ,ys_key='$v7'";
			$sqlstr.=" ,period='$v8'";
			$sqlstr.=" ,relid='$relidstr'";
			$sqlstr=$sqlstr." ,sp_type='$sp_type'";
			$sqlstr=$sqlstr." ,wz_type='$wz_type'";
			$sqlstr=$sqlstr." ,ys_type='$ys_type'";
			$sqlstr=$sqlstr." where newsid=".$newsid;
			//echo $sqlstr;
			
				$db->query($sqlstr);
			
	}
	//查询 数据
	$sqlstr="select * from tb_zt,tb_zt_type";
	$sqlstr=$sqlstr." where tb_zt.newstype=tb_zt_type.tpid ";
	$sqlstr=$sqlstr." and tb_zt.newsid=$newsid";

	
	$db->query($sqlstr);
	$arr=$db->fetch();
	//初始化 显示对象
	$title=$arr[title];
	$newstype=$arr[newstype];
	$jianshu=$arr[jianshu];
	$content=$arr[content];
	$period = $arr[period];
	$relid = explode(";",$arr[relid]);
	$arr_sp=get_type_str($arr[sp_type]);
	$arr_wz=get_type_str($arr[wz_type]);
	$arr_ys=get_type_str($arr[ys_type]);
	$relid = explode(";",$arr['relid']);
	$sp_key=$arr[sp_key]==""?"空":$arr[sp_key];
	$wz_key=$arr[wz_key]==""?"空":$arr[wz_key];
	$ys_key=$arr[ys_key]==""?"空":$arr[ys_key];
?>
<html>
<head>
<title>专题更新</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link rel="stylesheet" href="../site.css" type="text/css">
<script src="../js.js"></script>
<script src="../func.js"></script>
<?require("../edit.htm");?>
</head>
<body bgcolor="#FFFFFF" text="#000000"  onload="HTMLArea.replaceAll(config);">
 <div align="center" class="bblue">更新专题
</div>
 <form name="form1" method="post" action="<?php echo $PHP_SELF?>"> 
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" class="thin"> 
          <tr> 
            <th width="18%"> 标题：</th>
            <td width="82%">
              <input name="newstitle" type="text" class="xiaozi" size="25" value="<?php echo($title)?>"></td> 
          </tr> 
          <tr> 
            <th> 专题类型：</th>
            <td>
              <select name="newstype" size="1" class="xiaozi">
                <?php echo get_zt_type12($newstype);?>
              </select></td> 
          </tr> 
          <tr> 
            <th> 简述：</th>
            <td> <input name="jianshu" type="text" class="xiaozi" size="50" value="<?php echo($jianshu)?>"></td> 
          </tr> 
          <tr> 
            <th>年月期：</th> 
            <td>
              <input name="period" type="text" class="xiaozi" id="period" value="<?php echo($period)?>" size="50"></td> 
          </tr> 
          <tr> 
            <th valign="top"> 内容：</th>
            <td> <textarea id="content" name="content" rows=20 cols=50  style="width:100%"><?php echo($content); ?></textarea> </td> 
          </tr> 
          <tr> 
            <th>相关链接ID</th> 
            <td>新闻：
              <input name="relid[0]" type="text" id="relid[0]" size="20" value="<?=$relid[0]?>"> 
              技术：
              <input name="relid[1]" type="text" id="relid[1]" size="20" value="<?=$relid[1]?>">  
              <br> 
              音色：
              <input name="relid[2]" type="text" id="relid[2]" size="20" value="<?=$relid[2]?>">  
              产品：
              <input name="relid[3]" type="text" id="relid[3]" size="20" value="<?=$relid[3]?>"> </td> 
          </tr> 
          <tr> 
            <th height="2"> 商品分类：</th>
            <td height="2">
              <select name="sp_type1" size="1" class="xiaozi">
                <?php echo get_sp_type($arr_sp[0]);?>
              </select>
              <select name="sp_type2" size="1" class="xiaozi">
                <?php echo get_sp_type($arr_sp[1]);?>
              </select>
              <select name="sp_type3" size="1" class="xiaozi">
                <?php echo get_sp_type($arr_sp[2]);?>
              </select>
              <br>
              <select name="sp_type4" size="1" class="xiaozi">
                <?php echo get_sp_type($arr_sp[3]);?>
              </select>
              <select name="sp_type5" size="1" class="xiaozi">
                <?php echo get_sp_type($arr_sp[4]);?>
              </select>
              <select name="sp_type6" size="1" class="xiaozi">
                <?php echo get_sp_type($arr_sp[5]);?>
              </select></td> 
          </tr> 
          <tr> 
            <th height="51"> 文章分类： </th>
            <td height="51">
              <select name="wz_type1" size="1" class="xiaozi">
                <?php echo get_wz_type($arr_wz[0]);?>
              </select></td> 
          </tr> 
          <tr> 
            <th> 音色盘分类： </th>
            <td>
              <select name="ys_type1" size="1" class="xiaozi">
                <?php echo get_ys_type($arr_ys[0]);?>
              </select>
              <select name="ys_type2" size="1" class="xiaozi">
                <?php echo get_ys_type($arr_ys[1]);?>
              </select>
              <select name="ys_type3" size="1" class="xiaozi">
                <?php echo get_ys_type($arr_ys[2]);?>
              </select>
              <br>
              <select name="ys_type4" size="1" class="xiaozi">
                <?php echo get_ys_type($arr_ys[3]);?>
              </select>
              <select name="ys_type5" size="1" class="xiaozi">
                <?php echo get_ys_type($arr_ys[4]);?>
              </select>
              <select name="ys_type6" size="1" class="xiaozi">
                <?php echo get_ys_type($arr_ys[5]);?>
              </select></td> 
          </tr> 
          <tr> 
            <th> 商品关键字搜索：</th>
            <td>
              <input name="sp_key" type="text" class="xiaozi" size="15" value="<?php echo($sp_key) ?>"></td> 
          </tr> 
          <tr> 
            <th> 文章关键字搜索：</th>
            <td>
              <input name="wz_key" type="text" class="xiaozi" size="15"  value="<?php echo($wz_key) ?>"></td> 
          </tr> 
          <tr> 
            <th> 音色盘关键字搜索：</th>
            <td>
              <input name="ys_key" type="text" class="xiaozi" size="15"  value="<?php echo($ys_key) ?>"></td> 
          </tr> 
          <tr> 
            <th>&nbsp; </th>
            <td>&nbsp;</td> 
          </tr> 
          <tr> 
            <th height="16">&nbsp; </th>
            <td> <input type="submit" name="okupload" value="提交" class="xiaozi"> 
              <input type="reset" name="cancel" value="重置" class="xiaozi"> 
              <input type="hidden" name="newsid" value="<?php echo($newsid)?>"> </td> 
          </tr> 
      </table> 
</form>
</body>
</html>
