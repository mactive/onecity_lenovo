<? if(!defined('UC_ROOT')) exit('Access Denied');?>
<? include $this->gettpl('header');?>

<script src="js/common.js" type="text/javascript"></script>
<div class="container">
	<? if($status == 2) { ?>
		<div class="correctmsg"><p>指定备份文件成功删除。</p></div>
	<? } ?>
	<div class="hastabmenu">
		<ul class="tabmenu">
			<li class="tabcurrent"><a href="#" class="tabcurrent">数据备份</a></li>
		</ul>
		<div class="tabcontentcur">
			<form action="admin.php?m=db&a=export" method="post">
			<input type="hidden" name="formhash" value="<?=FORMHASH?>">
			<table>
				<tr>
					<td>备份文件名:</td>
					<td><input type="text" name="filename" value="<?=$filename?>" class="txt" />.sql</td>
					<td> &nbsp; 分卷长度(kb):</td>
					<td><input type="text" name="sizelimit" value="2048" class="txt" /></td>
					<td><input type="submit" value="提 交"  class="btn" /></td>
				</tr>
			</table>
			</form>
		</div>
	</div>
	<h3>数据备份记录</h3>
	<div class="mainbox">
		<? if($volumelist) { ?>
			<form action="admin.php?m=db&a=ls" method="post">
				<input type="hidden" name="formhash" value="<?=FORMHASH?>">
				<table class="datalist fixwidth">
					<tr>
						<th width="10%"><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall">删除</label></th>
						<th>文件名</th>
						<th>版本</th>
						<th>时间</th>
						<th>尺寸</th>
						<th>卷号</th>
						<th>操作</th>
					</tr>
					<? foreach((array)$volumelist as $volume) {?>
					<tr>
						<td><input type="checkbox" name="delete[]" value="<?=$volume['filename']?>" class="checkbox" /></td>
						<td><a href="./data/backup/<?=$volume['filename']?>" target="_blank"><?=$volume['filename']?></a></td>
						<td><?=$volume['version']?></td>
						<td><?=$volume['dateline']?></td>
						<td><?=$volume['size']?></td>
						<td><?=$volume['volume']?></td>
						<td><a href="###" onclick="if(window.confirm('导入备份数据将会覆盖现有用户中心的数据，您确定导入吗？。')) {window.location='admin.php?m=db&a=import&file=<?=$volume['filename']?>';}">导入</a></td>
					</tr>
					<? } ?>
					<tr class="nobg">
						<td><input type="submit" value="提 交" class="btn" /></td>
						<td class="tdpage" colspan="2"><?=$multipage?></td>
					</tr>
				</table>
			</form>
		<? } else { ?>
			<div class="note">
				<p class="i">目前没有相关记录!</p>
			</div>
		<? } ?>
	</div>
</div>

<? include $this->gettpl('footer');?>