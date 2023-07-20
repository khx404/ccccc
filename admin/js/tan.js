function tanbox(){
	$(".tan").remove();
	var tanc="<div class='tan'>";
	tanc+="<div class='tanbox'>";
    tanc+="<div class='tan_tit'><span class='tan_tit_con'>温馨提示</span><a class='tan_tit_shan' onclick='tanrun(0)'>X</a></div>";
    tanc+="<div class='tan_con'></div>";
    tanc+="<div class='tan_but'></div>";
    tanc+="</div>";
    tanc+="</div>";
	$("body").append(tanc);
}

function tanrun(run){
	//隐藏
	if(run==0){
		$(".tan").remove();
	}
	else{
		$(".tan").fadeIn(100);
	}
	//定位
	var height=$(".tanbox").outerHeight();//获取高度
	var height_window=$(window).height();//浏览器可见区域高度
	var martop=(height_window-height)/2;
	$(".tanbox").css("margin-top",martop+"px")
}

//生成
function html(t){
	tanbox();//引入提示
	$(".tan_tit_con").html('生成html');//提示内容
	var html="<table class='from'>";
	html+="<tr><td class='make'>正在请求更新……</td></tr>";
	html+="</table>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right5 quxiao'>取消</a>";
	but+="<a onclick=\"html_run('../include/make.php?html')\" class='margin_right5 queren'>一键生成</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
	if(t==0){
		html_run('../include/make.php?html');
	}
}

function html_run(url){
	$.post(url,{},function(res){
		if(res.tiao==''){
			$('.make').html(res.tishi);
			return false;
		}
		html_run('../include/make.php?html'+qukong(res.tiao));
		$('.make').html(res.tishi);
	},'json');
}

//去除字符串空格
function qukong(str){
	return str.replace(/\s+/g,"");//去除class name 空格
}

//读取多选框值，用分号隔开（name的值）
function duoxuan(ming){
	var zhi = '';
    $("input:checkbox[name='"+ming+"']:checked").each(function(){
		if(zhi==''){zhi += $(this).val();}
		else{zhi += ";"+$(this).val();}
    });
	return zhi;
}

//弹出升级
function shengji(){
	$.post("shengji.php",{},function(result){
		tanbox();//引入提示
		$(".tan_tit_con").html('在线升级');//提示内容
		var html=result+"<br/><div id='sj'></div>";
		$(".tan_con").html(html);//写入内容
		var but="<a onclick='f5()' class='quxiao'>取消</a>";
		if(result.indexOf("最新版本")==-1){
			but+="<a class='margin_left5 queren' onclick='shengji_but()'>升级</a>";
		}
		$(".tan_but").html(but);//写入按钮
		tanrun();//定位和弹出
	});
}

//点击升级按钮
function shengji_but(){
	var shengji="<iframe id='iframemake' src='shengji.php?sj' frameborder='0' style='padding-top:10px;height:20px;width:100%;'></iframe>";
	$('#sj').html(shengji);
}

//刷新当前页
function f5(){
	window.location.reload();
}

//当前时间
function shijian(){
	var myDate = new Date();
	return myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()
}

//退出登陆
function tui(){
	$.post('run_ajax.php?run=logontui',{},function(res){
		window.location='login.php';
	});
}

//使用此函数编码你的文章
function html_encode(strHTML){   
  var strTem = "";   
  if (strHTML.length == 0) return "";   
  strTem = strHTML.replace(/</g, "&lt;");   
  strTem = strTem.replace(/>/g, "&gt;");   
  return strTem;   
}

function piliang(id,lanmumingcheng){
	tanbox();//引入提示
	$(".tan_tit_con").html('批量上传文章到【'+lanmumingcheng+'】，文件标题为文章标题');//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>插入图片</th><td>";
	html+="<label style='margin-top:0;'><input type='radio' name='chatu' value='1' checked='checked'/>是</label>";
	html+="<label style='margin-top:0;'><input type='radio' name='chatu' value='0'/>否</label>";
	html+="</td></tr>";
	html+="<tr><th class='w100'>发布时间</th><td>";
	html+="<label style='margin-top:0;'><input type='radio' name='fabushijian' value='0' checked='checked'/>待发布</label>";
	html+="<label style='margin-top:0;'><input type='radio' name='fabushijian' value='1'/>当前时间</label>";
	
	html+="</td></tr>";
	
	html+="<tr><th>上传</th><td>";
	html+="<p class='file1'><p class='file2'>TXT文本</p>";
	html+="<input type='file' multiple='multiple' onchange='uptxt("+id+")' class='file_input up' id='up'/></p>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

function uptxt(id){
	var chatu=$('input[name="chatu"]:checked').val();
	var fabushijian=$('input[name="fabushijian"]:checked').val();
	var formData = new FormData();
    $.each($("#up")[0].files,function(i,file){
        formData.append(i,file);
    });

	$.ajax({
		url: 'run_ajax.php?run=uptxt&id='+id+'&chatu='+chatu+'&fabushijian='+fabushijian,
		type: 'POST',
		cache: false,
		data: formData,
		processData: false,
		contentType: false,
		success: function(res){
			window.location='art_list.php?tid='+id;
		},
		dataType:'html'
	})
}

//AJAX同步操作数据的增删改

//新增(表名，带键名的一维数组)
function ajax_add(table,post){
	var fanhui;
	$.ajax({ 
		type : "post", 
        url : "run_ajax.php?run=add&table="+table, 
        data :post, 
        async : false, 
        success : function(res){
			fanhui=res;
        } 
    });
	return fanhui; 
}

//删除函数（表名，条件）
function ajax_del(table,where){
	var fanhui;
	$.ajax({ 
		type : "post", 
        url : "run_ajax.php?run=del&table="+table+"&where="+where, 
        data :{}, 
        async : false, 
        success : function(res){
			fanhui=res;
        } 
    });
	return fanhui; 
}

//改函数（表名，条件）
function ajax_edit(table,post,where){
	var fanhui;
	$.ajax({ 
		type : "post",
        url : "run_ajax.php?run=edit&table="+table+"&where="+where, 
        data :post, 
        async : false, 
        success : function(res){
			fanhui=res;
        } 
    });
	return fanhui; 
}