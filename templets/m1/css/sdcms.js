$(function(){
	$(".form_comment").validator(
	{
		stopOnError:true,
		theme:'yellow_top',
		ignore:':hidden',
		valid:function(form)
		{
			$.fn.tips({type:'loading',content:'数据提交中'});
			$.ajax(
			{
				url:webroot+"plug/comment.asp?act=add&id="+infoid,
				type:"post",
				data:$(form).serialize(),
				success:function(data){
					data=jQuery.parseJSON(data);
					var type="warn";
					if(data.status=="y"){type="ok";}
					$.fn.tips({type:type,content:data.info});
					if(data.status=="y")
					{
						$(".form_comment")[0].reset();
						var act=data.info.substring(0,1);
						var info=data.info.substring(1);
						$.fn.tips({type:"ok",content:info});
						if(act==2)
						{
							setTimeout(function(){location.href=webroot+'plug/comment.asp?id='+infoid+'';},1500)
						}
					}
				}
			});
		}
	});
})



var startHref;
$(function(){
	
	function item_masonry()
	{
		$('.item_list img').load(function(){$('.list-loop').masonry({itemSelector:'.loop'});});
		$('.list-loop').masonry({itemSelector:'.loop'});
	}
	
	var p=false;
	if($(".item_list").length>0){p=true;item_masonry();}
	$(".imore a").click(function(){
		var href=$(this).attr("href");
		startHref=href;
		if(href!=undefined){
			$.ajax({
				type:"get",
				cache:false,
				url:startHref,
				success:function(data){
					var $result=$(data).find(".loop");
					if(p){$(".list-loop").append($result).masonry('appended',$result);item_masonry();}else{$(".list-loop").append($result);}
					var newHref=$(data).find(".imore a").attr("href");
					if(newHref!=""){$(".imore a").attr("href",newHref);}else{$(".imore").html("已显示全部内容");}
				}
			})
		}
		return false;
	})
	$(window).bind("scroll",function(){
		if($(document).scrollTop()+$(window).height()>$(document).height()-70){
		if($(".imore a").attr('href')!=startHref){$(".imore a").trigger("click");}}
	})
  
});