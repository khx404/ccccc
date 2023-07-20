<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登陆界面</title>
<script src="../../common/js/jquery.min.js"></script>
<script src="../../common/js/common.js"></script>
<link href="css/qita.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div class="box">
	<div class="tit">欢迎登陆<span>站帮主</span></div>
    <div class="boxcon">
        <table class="from">
            <tr><th>管理员</th><td><input id="guanliyuan" /></td></tr>
            <tr><th>密码</th><td><input id='pwd' type="password" /></td></tr>
            <tr><th class="jisuan">X+X=</th><td><input id="jisuan" /></td></tr>
        </table>
        <div class="cle20"></div>
        <a class="queren" onclick="login()">登陆</a><span class="ts"></span>
    </div>
</div>
<script>
dingwei();//居中定位
jisuan();//输出计算验证

//按enter键登陆
$(document).keyup(function(event){
	if(event.keyCode ==13){login();}
});

//登陆
function login(){
	var guanliyuan=$('#guanliyuan').val();
	var pwd=$('#pwd').val();
	
	if(guanliyuan.length<2 || pwd.length<5){
		$('.ts').html('管理员或密码输入有误');
		jisuan();//输出计算验证
		return false;
	}
	
	if(jisuan_jg()==0){
		$('.ts').html('验证码有误');
		return false;
	}
	var post={guanliyuan:guanliyuan,pwd:pwd};
	$.post('run_ajax.php?run=login',post,function(res){
		if(res==1){
			window.location="index.php";
		}
		else if(res==2){
			window.location="art_list.php";
		}
		else{
			jisuan();//输出计算验证
			$('.ts').html('管理员或密码有误');
		}
	});
}
</script>
</body>
</html>