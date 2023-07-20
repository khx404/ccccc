$('.up').change(function() {
	var tishi=$(this).attr('data-tishi');//上传成功提示
	var path=$(this).attr('data-path');//上传到文件夹
	var filename=$(this).attr('data-filename');//文件名0重命名，1跟原来文件名一致
	var zhanshi=$(this).attr('data-zhanshi');//展示到css
	/*创建FormData对象 并赋值*/
	var formData = new FormData();
    $.each($(this)[0].files,function(i,file){
        formData.append(i,file);
    });
	
	/*上传请求*/
	$.ajax({
		url: '../include/up.php?run=file&path='+path+'&filename='+filename,
		type: 'POST',
		cache: false,
		data: formData,
		processData: false,
		contentType: false,
		success: function(res){
			
			if(tishi==1){
				tanbox();//引入提示
				$(".tan_tit_con").html('温馨提示');//提示内容
				var html="上传成功！";
				$(".tan_con").html(html);//写入内容
				var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>不刷新</a><a class='queren' onclick='shuaxin()'>刷新页面</a>";
				$(".tan_but").html(but);//写入按钮
				tanrun();//定位和弹出
				
			}
			
			var sjc=Date.parse(new Date());
			$.each(res,function(i,v){
				var css='del'+sjc+i;
				var img="<img onclick=\"del('"+v+"','"+css+"','"+zhanshi+"')\" class='"+css+"' src='"+v+"' />";
				$('.'+zhanshi).append(img);
			});
			file(zhanshi);
		},
		dataType:'json'
	})
});
//删除
function del(url,css,id){
	tanbox();//引入提示
	$(".tan_tit_con").html('温馨提示');//提示内容
	var html="亲，您确定删除<b>"+url+"</b>图片吗？删除后不可以还原的哦！";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick=\"del_pic_run('"+url+"','"+css+"','"+id+"')\" class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
	
}

function del_pic_run(url,css,id){
	tanrun(0);//弹窗消失
	var post={url:url};
	$('.'+css).remove();
	file(id);
	$.post('../include/up.php?run=del',post,function(res){});
}

//赋值到表单
function file(id){
	var zhi=$('.'+id).html();
	var post={zhi:zhi};
	$.post('../include/up.php?run=guolv',post,function(res){
		$('#'+id).val(res);
	});
}

function shuaxin(){
	window.location.reload();
}