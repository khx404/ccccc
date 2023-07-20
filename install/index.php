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
	<div class="tit">安装<span>站帮主建站系统</span></div>
    <div class="boxcon install">
    <p>很抱歉，该源码已安装！<br/>如果需要重新安装，请把路径：<br /><b>根目录/cms/cms/install/install.txt</b><br />文件删除</p>
    </div>
    
    <div class="boxcon quanxian"></div>
    
    <div class="boxcon anzhuang">
    <form id="post">
        <table class="from">
            <tr><th>数据库地址<span>*</span></th>
            <td><input id="host" name="host" onchange="yanzheng()" value="127.0.0.1" /></td></tr>
            <tr><th>数据库用户名<span>*</span></th>
            <td><input id="username" name="username" onchange="yanzheng()" value="root" /></td></tr>
            <tr><th>数据库密码</th>
            <td><input id="password" name="password" onchange="yanzheng()" value="root" /></td></tr>
            <tr><th>数据库名称</th><td><input id="dbname" name="dbname" /></td></tr>
            
            
            <tr><th>数据包</th><td>
				<?php 
                $datas=array_diff(scandir("../data"),array("..","."));
                $datas=array_values($datas);
                ?>
            	<label><input name="riqi" checked="checked" value="<?php echo $datas[(count($datas)-1)]; ?>" type="radio">最新数据</label>
              	<label><input name="riqi" type="radio" value="0">不带数据</label>
            </td></tr>
            <tr><th class="jisuan">X+X=</th><td><input id="jisuan" /></td></tr>
        </table>
        <div class="cle20"></div>
        <a class="queren">一键安装</a><span class="ts"></span>
    </form>
    </div>
    
    <div class="boxcon runinstall"></div>
    
</div>
<script>
<?php 
//判断文件权限
$paths=array('../../../');
$quanxian=1;
foreach($paths as $v){
	if(!is_writable($v)){
		$quanxian=$v;
		break;
	}
}
	
//检测是否已安装
if(is_file('install.txt')){
	echo "$('.install').show();\r\n";
}
else if($quanxian!=1){
	echo "$('.quanxian').html('<p>请设置路径<b>{$quanxian}</b>为可写权限！</p>');";
	echo "$('.quanxian').show();\r\n";
}
else{
	echo "$('.anzhuang').show();\r\n";
}

//设置默认数据库名称
$dbname='db_'.path_dq();
$dbname= str_ireplace('.','_',$dbname);
echo "$('#dbname').val('{$dbname}');\r\n";

//获取当前文件所在的文件夹
function path_dq(){
	$url=$_SERVER['REQUEST_URI'];
	$urls=explode('/',$url);
	if($urls[count($urls)-5]==''){
		return $_SERVER['HTTP_HOST'];
	}
	else{
		return $urls[count($urls)-5];
	}
	
}
?>
//验证码
jisuan();
dingwei();//居中定位

//点击安装按钮
$('.queren').click(function(){
	if(jisuan_jg()==0){
		$('.ts').html('验证码有误');
		return false;
	}
	var riqi=$('input[name="riqi"]:checked').val(); 
	var host=$('#host').val();
	var username=$('#username').val();
	if(host!='' && username!=''){
		var post = $('#post').serializeArray();
		$.post('run_ajax.php?run=install',post,function(res){
			if(res==1){
				$('.boxcon').hide();
				var url="<iframe frameborder='0' scrolling='no' src='huanyuan.php?jindu=2&riqi="+riqi+"'></iframe>";
				$('.runinstall').html(url);
				$('.runinstall').show();
				dingwei();//居中定位
			}
			else{
				$('.ts').html(res);
			}
		});
	}
});

//验证数据库链接是否正确
function yanzheng(){
	var host=$('#host').val();
	var username=$('#username').val();
	var password=$('#password').val();
	if(host!='' && username!=''){
		var post={host:host,username:username,password:password}
		$.post('run_ajax.php?run=link',post,function(res){
			if(res==1){
				$('.ts').html("<font color='#1aacda'>数据库链接成功</font>");
			}
			else{
				$('.ts').html('数据库链接失败');
			}
		});
	}
}
</script>
</body>
</html>