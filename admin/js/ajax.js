//弹出新增框
function add(){
	tmp=qutmp();
	var id_max=$('#id_max').val();//最大id
	id_max++;
	$('#id_max').val(id_max);
	tmp=tmp.replace(/\[id\]/g,id_max);
	$('.list').append(tmp);
}

//提交
function tijiao(id,table){
	//取值
	var post=new Object();
	$(".tr"+id+" input").each(function(){
		var v = $(this).val();
		var n = $(this).attr("class");
		//过滤name为空并且name包含_的值
		if(n!=undefined && n.search("_")== -1){
			post[n]=v;
		}
	});
	
	//异步提交
	$.post('ajax.php?run=addedit&table='+table,post,function(res){
		ok('ok')
	});
}

//上传(当前input样式名，当前tr id，提交数据库)
function uppic(name,id,table){
	var formData = new FormData();
    $.each($(".tr"+id+" .up_"+name)[0].files,function(i,file){
        formData.append(i,file);
    });

	$.ajax({
		url: 'ajax.php?run=youad_pic',
		type: 'POST',
		cache: false,
		data: formData,
		processData: false,
		contentType: false,
		success: function(res){
			$(".tr"+id+" ."+name).val(res);
			$(".tr"+id+" .img_"+name).attr('src',res);
			tijiao(id,table);
		},
		dataType:'html'
	})
}

//删除
function del(id,table,queren){
	
	//确认弹窗
	if(queren!=1){
		tanbox();//引入提示
		$(".tan_tit_con").html('温馨提示');//提示内容
		var html="您确定删除ID<b>"+id+"</b>的内容吗？";
		$(".tan_con").html(html);//写入内容
		var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a>";
		but+="<a onclick=\"del("+id+",'"+table+"',1)\" class='queren'>确认</a>";
		$(".tan_but").html(but);//写入按钮
		tanrun();//定位和弹出
		return false;
	}
	
	$.post('ajax.php?run=del&table='+table,{id:id},function(res){
		tanrun(0);
		if(res==1){
			$('.tr'+id).remove();
			ok('ok')
		}
		else{
			error('删除失败');
		}
	});
}

//成功提示
function ok(tishi){
	$('.ts').html("<span class='tishi'><font color='#1aacda'>"+tishi+"</font></span>");
	$(".ts span").fadeOut(1000);
}

//失败提示
function error(tishi){
	$('.ts').html("<span class='tishi'><font color='#FF0000'>"+tishi+"</font></span>");
	$(".ts span").fadeOut(3000);
}