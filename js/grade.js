$(function(){
var txt1=["(垃圾)","(很不满意)","(不满意)","(不够好)","(一般)","(还可以)","(还好)","(很不错)","(超赞)","(极品)"];
$(".doPointHandler span small").each(function(index){
  $(this).mouseover(function(){
    id=index+1;
	var obj=$(this).parent().parent().next().children("em");
	if(id<=10){
		obj.html(txt1[id-1]);
	}
    $(this).parent().removeClass();
    $(this).parent().addClass("star"+id);
	$(this).parent().parent().next().children("strong").html(id/2);
  });
  var Point1=4;
  $(this).click(function(){
    id=index+1;
	if(id<=10){
	  $("#pointV1").val(id);
	}
	$(this).parent().attr("v",id);
	
	var v1=parseInt($("#item1").attr("v"));
	var temp_v= v1 / 2;
	var temp_v=Math.round(temp_v*Math.pow(10,1))/Math.pow(10,1);

	$(".doPointHandler input[name='comment_rank']").val(temp_v);	

  });
  $(this).parent().mouseout(function(){
    var ids=$(this).attr("v");
	id=index+1;
	var obj=$(this).parent().next().children("em");
	if(id<=10){
		obj.html(txt1[ids-1]);
	}
	$(this).parent().next().children("strong").html(ids/2);
    $(this).removeClass();
    $(this).addClass("star"+ids);
  });
});

});