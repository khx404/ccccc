var top_height=$(".top").outerHeight();//工具栏高度

var id=self.frameElement.getAttribute('id');
//获取父级textarea的内容
var id_textarea=id.replace('_iframe','');
var str=$('#'+id_textarea,parent.document).text();
if(str!=''){
	$('#edit').html(str);
}

var height=self.frameElement.getAttribute('height')*1-top_height*1-32;
$('#edit').css('height',height+"px");

//鼠标离开编辑框赋值给父级的textarea
$('.zbz_edit_box').mouseleave(function(){
	var edit_box=$('.edit_box').html();
	if(edit_box.indexOf('<textarea class="edit" id="edit"') != -1){var edit=$('.edit').val();}
	else{var edit=$('.edit').html();}
	parent.document.getElementById(id_textarea).value = edit;
})

var editer = document.getElementById('edit');
$('.tijiao').click(function(){
	var type;
	var zhi;
	type=$(this).data("type");
	if(type=='createlink'){
		zhi=$('.link').val();
		//验证链接
		if(zhi==''){
			alert('请先填写链接哦');
			return false;
		}
	}
	else{
		zhi=$(this).data("zhi");
	}
	editer.focus();
	document.execCommand(type,false,zhi);
});

//图片上传
function uploadFile(e){
	var path=self.frameElement.getAttribute('data-path');//图片保存路径
	var data_path_res=self.frameElement.getAttribute('data-path-res');//返回图片保存路径
	var data_pic_name=self.frameElement.getAttribute('data-pic-name');//图片命名
	var formData = new FormData();
    $.each($("#up")[0].files,function(i,file){
        formData.append(i,file);
    });

	$.ajax({
		url: 'php/zbz.php?run=uptxt&path='+path+'&path_res='+data_path_res+'&data_pic_name='+data_pic_name,
		type: 'POST',
		cache: false,
		data: formData,
		processData: false,
		contentType: false,
		success: function(res){
			$.each(res,function(i,url){
				editer.focus();
				document.execCommand('InsertImage',false,url);
			});
		},
		dataType:'json'
	})
}

//源代码
$('.yuanma').click(function(){
	var edit_box=$('.edit_box').html();
	if(edit_box.indexOf("<textarea class=\"edit\" id=\"edit\"") != -1){
		var zhi=$('#edit').val();
		var html="<div class='edit' contenteditable='true' id='edit' style='overflow-x:auto;overflow-y:auto;'>"+zhi+"</div>";
		$('.edit_box').html(html);
		//工具栏恢复
		$('.tijiao').css('color','#333');
	}
	else{
		var zhi=$('#edit').html();
		var html="<textarea class='edit' id='edit' style='overflow-x:auto;overflow-y:auto;'>"+zhi+"</textarea>";
		$('.edit_box').html(html);
		
		//是源代码时，工具栏禁用
		$('.tijiao').css('color','#ccc');
		
	}
	$('#edit').css('height',height+"px");
});