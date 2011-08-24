function submitonce(theform){
	//if IE 4+ or NS 6+
	if (document.all||document.getElementById)
	{
		//screen thru every element in the form, and hunt down "submit" and "reset"
		for (i=0;i<theform.length;i++)
		{
			var tempobj=theform.elements[i]
			if(tempobj.type.toLowerCase()=="submit"||tempobj.type.toLowerCase()=="reset")
			//disable em
			tempobj.disabled=true
		}
	}
}


function openwin(tagname){
  	winid=window.open("../upload_image.php?tagname="+tagname,"test");
}


function openScript(url, width, height){
	var Win = window.open(url,"openScript",'width=' + width + ',height=' + height + ',resizable=1,scrollbars=yes,menubar=no,status=yes' );
}

//***********默认设置定义.*********************


//下拉菜单相关代码
 var h;
 var w;
 var l;
 var t;
 var topMar = 1;
 var leftMar = -2;
 var space = 1;
 var isvisible;
 var MENU_SHADOW_COLOR='#999999';//定义下拉菜单阴影色
 var global = window.document
 global.fo_currentMenu = null
 global.fo_shadows = new Array

function HideMenu() 
{
 var mX;
 var mY;
 var vDiv;
 var mDiv;
	if (isvisible == true)
	{
		vDiv = document.all("menuDiv");
		mX = window.event.clientX + document.body.scrollLeft;
		mY = window.event.clientY + document.body.scrollTop;
		if ((mX < parseInt(vDiv.style.left)) || (mX > parseInt(vDiv.style.left)+vDiv.offsetWidth) || (mY < parseInt(vDiv.style.top)-h) || (mY > parseInt(vDiv.style.top)+vDiv.offsetHeight))
		{
			vDiv.style.visibility = "hidden";
			isvisible = false;
	//add by yanglei
			for(i=0;i<window.document.all.tags("SELECT").length; i++)
			{
				var objTemp = window.document.all.tags("SELECT")[i];
				objTemp.style.visibility="visible";
			}
		}


	}

}

function ShowMenu(vMnuCode,tWidth) {
	vSrc = window.event.srcElement;
	vMnuCode = "<table id='submenu' cellspacing=1 cellpadding=3 style='width:"+tWidth+"' class=tableborder1 onmouseout='HideMenu()'><tr height=23 ><td nowrap align=left class=tablebody1>" + vMnuCode + "</td></tr></table>";

	h = vSrc.offsetHeight;
	w = vSrc.offsetWidth;
	l = vSrc.offsetLeft + leftMar+4;
	t = vSrc.offsetTop + topMar + h + space-2;
	while(vSrc = vSrc.offsetParent){
		l += vSrc.offsetLeft;
		t += vSrc.offsetTop;
	}
	menuDiv.innerHTML = vMnuCode;
	menuDiv.style.top = t;
	menuDiv.style.left = l;
	menuDiv.style.visibility = "visible";
	isvisible = true;
    makeRectangularDropShadow(submenu, MENU_SHADOW_COLOR, 4);
//add by yanglei
	for(i=0;i<window.document.all.tags("SELECT").length; i++)
	{
		var objTemp = window.document.all.tags("SELECT")[i];
		objTemp.style.visibility="hidden";
	}

}

function makeRectangularDropShadow(el, color, size)
{
	var i;
	for (i=size; i>0; i--)
	{
		var rect = document.createElement('div');
		var rs = rect.style
		rs.position = 'absolute';
		rs.left = (el.style.posLeft + i) + 'px';
		rs.top = (el.style.posTop + i) + 'px';
		rs.width = el.offsetWidth + 'px';
		rs.height = el.offsetHeight + 'px';
		rs.zIndex = el.style.zIndex - i;
		rs.backgroundColor = color;
		var opacity = 1 - i / (i + 1);
		rs.filter = 'alpha(opacity=' + (100 * opacity) + ')';
		el.insertAdjacentElement('afterEnd', rect);
		global.fo_shadows[global.fo_shadows.length] = rect;
	}
}
//首页
//var indexlist= '<table cellspacing=0 cellpadding=0><tr><td><a  href="/admin/user/admin_user.php">用户管理</a></td></tr><tr><td><a  href="/admin/order/admin_cart.php">订单管理</a></td></tr><tr><td><a  href="/admin/other/admin_ques.php">调查管理</a></td></tr><tr><td><a  href="/admin/other/admin_adv.php">广告管理</a><hr size=1></td></tr><tr><td><a  href="/admin/user/admin_adminpwd.php">修改密码</a><?if($_COOKIE["cookie_user"]=="admin"){ echo "</td></tr><tr><td><a  href="/admin/user/admin_adminuser.php">后台用户管理</a></td></tr><tr><td><a  href="/admin/other/ftppwd.php">ftp用户管理</a>"; }?></td></tr><tr><td><a  href="/admin/login.php?action=logout">注销</a></td></tr></table>'

//var indexlist= '<table cellspacing=0 cellpadding=0><tr><td><a  href="/admin/order/admin_cart.php">订单管理</a></td></tr><tr><td><a  href="/admin/order/admin_ques.php">调查管理</a></td></tr><tr><td><a  href="/admin/order/admin_adv.php">广告管理</a><hr size=1></td></tr><? if($_COOKIE["cookie_adminuser"]=="admin"){ echo "<tr><td><a  href="/admin/user/admin_adminpwd.php">后台用户密码管理</a> </td></tr><tr><td><a  href="/admin/user/ftppwd.php">ftp用户管理</a>"; }?></td></tr><tr><td><a  href="/admin/user/admin_adminlog.php">管理日志</a></td></tr><tr><td><a  href="/admin/login.php?action=logout">注销</a></td></tr></table>'

//独家专题
var ztlist= '<table cellspacing=0 cellpadding=0><tr><td><a  href="/admin/zt/admin_news.php">修改专题</a></td></tr><tr><td><a  href="/admin/zt/add_newnews.php">添加专题</a></td></tr><tr><td><a  href="/admin/zt/admin_zttype.php" target=_blank>专题类别</a></td></tr><tr><td><a  href="/admin/zt/zt-top.php" >置顶专题管理</a></td></tr><tr><td><a  href="/admin/zt/admin_bbs.php" >评论管理</a></td></tr><tr><td><a  href="/admin/zt/zt_review_admin.php">往期专题管理</a></td></tr></table> '
//产品直销
var splist= '<table cellspacing=0 cellpadding=0><tr><td><a  href="/admin/sp/mid.php" target=_blank>产品置顶</a></td></tr><tr><td><a href="/admin/sp/namm.php" target=_blank>Namm新品</a></td></tr><tr><td><a  href="/admin/sp/admin_sp_fj.php" target="_blank">指定商品为其他商品附件</a></td></tr><td><a href="/admin/sp/admin_sp_type_fj.php" target="_blank">指定商品为其他分类附件</a></td></tr><td><a  href="/admin/sp/sptosp.php" target="_blank">附属商品管理</a></td></tr><tr><td><a  href="/admin/sp/search.php">商品管理</a></td></tr><tr><td><a  href="/admin/sp/prosmgr.php">新商品管理</a></td></tr><tr><td><a href="/admin/sp/sp_fj_discount.php"  target="_blank">附件商品打折率管理</a></td></tr><tr><td><a  href="/admin/sp/admin_sp.php">添加商品</a></td></tr><tr><td><a  href="/admin/sp/tmp.php" target="_blank">为商品分类</a></td></tr><tr><td><a  href="/admin/sp/admin_pack.php">修改商品包</a></td></tr><tr><td><a  href="/admin/sp/admin_type.php" target=_blank>类别管理</a></td></tr><tr><td><a  href="/admin/sp/ltem.php" target=_blank>技术参数</a></td></tr><tr><td><a  href="/admin/sp/admin_bbs.php" >评论管理</a></td></tr><tr><td><a  href="/admin/sp/admin_offer.php" target=_blank>厂商管理</a></td></tr><tr><td><a  href="/admin/sp/admin_sphit.php" target=_blank>查看点击率</a></td></tr><tr><td><a  href="/admin/sp/sp_discount.php" target=_blank>打折活动</a></td></tr><tr><td><a href="/admin/sp/sp_purveymanager.php">供货商管理</a></td></tr><tr><td><a href="/admin/sp/sp_unitmanager.php">单位管理</a></td></tr></table>'
//酷图
var yslist= '<table cellspacing=0 cellpadding=0><tr><td>赛因产品图集</td></tr><tr><td><a  href="/gallery/admin/1.php?tt=赛因产品" target=_blank>添加</a></td></tr><tr><td><a  href="/gallery/admin/1_1.php?cptjlb=赛因产品" target=_blank>图集管理</a></td></tr><tr><td>赛因活动图集</td></tr><tr><td><a  href="/gallery/admin/1.php?tt=赛因活动" target=_blank>添加</a></td></tr><tr><td><a  href="/gallery/admin/1_1.php?cptjlb=赛因活动" target=_blank>图集管理</a></td></tr><tr><td>赛因视角图集</td></tr><tr><td><a  href="/gallery/admin/1.php?tt=赛因视角" target=_blank>添加</a></td></tr><tr><td><a  href="/gallery/admin/1_1.php?cptjlb=赛因视角" target=_blank>图集管理</a></td></tr><tr><td>赛因视频图集</td></tr><tr><td><a  href="/gallery/admin/3.php" target=_blank>添加</a></td></tr><tr><td><a  href="/gallery/admin/3_1.php?cptjlb=赛因视频" target=_blank>图集管理</a></td></tr><tr><td><a  href="/gallery/admin/a1.php" target=_blank>图集分级管理</a></td></tr><tr><td>赛因案例图集</td></tr><tr><td><a  href="/gallery/admin/2.php" target=_blank>添加</a></td></tr><tr><td><a  href="/gallery/admin/2_1.php?cptjlb=赛因案例" target=_blank>图集管理</a></td></tr><tr><td><a  href="/gallery/admin/b1.php" target=_blank>图集分类管理</a></td></tr></table>'
//新闻资讯
var newslist= '<table cellspacing=0 cellpadding=0><tr><td><a  href="/admin/news/admin_news.php">修改新闻</a></td></tr><tr><td><a  href="/admin/news/add_news.php">添加新闻</a></td></tr><tr><td><a  href="/admin/news/admin_type.php" target=_blank>新闻类别</a></td></tr><tr><td><a  href="/admin/news/admin_bbs.php" >评论管理</a></td></tr></table>'
//技术学习
var techlist= '<table cellspacing=0 cellpadding=0><tr><td><a  href="/admin/tech/admin_news.php">修改技术文章</a></td></tr><tr><td><a  href="/admin/tech/add_news.php">添加技术文章</a></td></tr><tr><td><a  href="/admin/tech/admin_type.php" target=_blank>技术学习类别</a></td></tr><tr><td><a  href="/admin/tech/admin_bbs.php" >评论管理</a></td></tr></table>'
//系统集成
var syslist= '<table cellspacing=0 cellpadding=0><tr><td><a  href="/admin/sys/admin_news.php">修改系统集成文章</a></td></tr><tr><td><a  href="/admin/sys/add_news.php">添加系统集成文章</a></td></tr><tr><td><a  href="/admin/sys/admin_type.php" target=_blank>系统集成类别</a></td></tr><tr><td><a  href="/admin/sys/admin_bbs.php" >评论管理</a></td></tr></table>'
//客服中心
var cslist= '<table cellspacing=0 cellpadding=0><tr><td><a  href="/admin/cs/admin_file.php">文件管理</a></td></tr><tr><td><a  href="/draft/admin/index.php" target=_blank>稿件管理</a></td></tr></table>';

//
document.onmousemove=HideMenu;


//===================create from dreamweaver 2004====================================
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
