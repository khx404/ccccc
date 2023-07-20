//计算验证
function jisuan(){
	$.post('../../common/php/ajax.php?run=jisuan',{},function(data){
		$('.jisuan').html(data['a']+'+'+data['b']+'=');
		$('.jisuan').attr('data',data['c']);
	},'json');
}
//验证计算结果
function jisuan_jg(){
	var jisuan=$('#jisuan').val();
	var jisuan_jg=$('.jisuan').attr("data");
	if(jisuan!=jisuan_jg){
		$('#jisuan').focus();
		return 0;
	}
	else{
		return 1;
	}
}

//居中定位
function dingwei(){
	var height=$(".box").outerHeight();//获取高度
	var height_window=$(window).height();//浏览器可见区域高度
	var martop=(height_window-height)/2;
	$(".box").css("margin-top",martop+"px");
}