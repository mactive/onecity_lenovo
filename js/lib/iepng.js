// JavaScript Document
function correctPNG4IE6()
{
        //获得全部图片
        var imgs=document.getElementsByTagName("IMG");
        for(var i=0; i<imgs.length; i++)
        {
                var img = imgs[i];
                var imgName = img.src.toUpperCase();
                <!--操作PNG图片-->
                if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
                {
                        var imgID = (img.id) ? "id='" + img.id + "' " : "";
                        var imgClass = (img.className) ? "class='" + img.className + "' " : "";
                        var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' ";
                        var imgStyle = "display:inline-block;" + img.style.cssText;
                        if (img.align == "left") imgStyle = "float:left;" + imgStyle;
                        if (img.align == "right") imgStyle = "float:right;" + imgStyle;
                        if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle;
                        
                        <!--对于隐藏层中的图片，或者其他原因导致图片尺寸无法获得-->
                        <!--此时我们需要读取图片的真实大小-->
                        <!--以免宽高都为0px而导致图片不显示-->
                        var imgTrueWidth=0;
                        var imgTrueHeight=0;
                        if(img.width==0)
                        {
                                var imgPng=new Image();
                                imgPng.src=img.src;
                                imgTrueWidth=imgPng.width;
                                imgTrueHeight=imgPng.height;
                        }
                        
                        <!--用<span>替换<img>标签-->
                        var strNewHTML = "<span " + imgID + imgClass + imgTitle + " style=\"";
                        strNewHTML=strNewHTML+"width:" + (img.width==0?imgTrueWidth:img.width) + "px; height:";
                        strNewHTML=strNewHTML+(img.height==0?imgTrueHeight:img.height) + "px;";
                        strNewHTML=strNewHTML+imgStyle + ";" + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader";
                        strNewHTML=strNewHTML+"(src='" + img.src + "', sizingMethod='scale');\"></span>";
                        
                        <!--执行替换-->
                        img.outerHTML = strNewHTML;
                        
                        i = i-1;
                }
        }
}


function correctPNG()
   {
   for(var i=0; i<document.images.length; i++)
      {
     var img = document.images[i]
     var imgName = img.src.toUpperCase()
     if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
        {
       var imgID = (img.id) ? "id='" + img.id + "' " : ""
       var imgClass = (img.className) ? "class='" + img.className + "' " : ""
       var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
       var imgStyle = "display:inline-block;" + img.style.cssText
       if (img.align == "left") imgStyle = "float:left;" + imgStyle
       if (img.align == "right") imgStyle = "float:right;" + imgStyle
       if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle     
       var strNewHTML = "<span " + imgID + imgClass + imgTitle
       + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
        + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
       + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>"
       img.outerHTML = strNewHTML
       i = i-1
        }
      }
   } 
   
   function alphaBackgrounds(){
   var rslt = navigator.appVersion.match(/MSIE (d+.d+)/, '');
   var itsAllGood = (rslt != null && Number(rslt[1]) >= 6 && Number(rslt[1]) != 7);
   for (i=0; i<document.all.length; i++){
      var bg = document.all[i].currentStyle.backgroundImage;
      if (bg){
         if (bg.match(/.png/i) != null){
            var mypng = bg.substring(5,bg.length-2);
    //alert(mypng);
            document.all[i].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+mypng+"', sizingMethod='crop')";
            document.all[i].style.backgroundImage = "url('')";
    //alert(document.all[i].style.filter);
         }                                               
      }
   }
}
if (navigator.platform == "Win32" && navigator.appName == "Microsoft Internet Explorer" && window.attachEvent) {
window.attachEvent("onload", correctPNG4IE6);
//window.attachEvent("onload", alphaBackgrounds);
} 
