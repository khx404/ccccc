<?php 
error_reporting(E_ALL^E_NOTICE^E_WARNING);
require("../include/class_sql.php");
echo "<style type='text/css'>
*{ padding:0; margin:0; font-size:12px;color:#1aacda}
a{ padding:5px 20px; border:1px solid #1aacda;border-radius:3px; text-decoration:none}
p{ line-height:35px;}
span{ color:#FF0000}
</style>";
$canshu=$_GET;//传递的参数 （进度、页数）
$res=$c_sql->huanyuan($canshu['riqi'],$canshu['jindu']);
if($res=='100'){
	$path_dq=path_dq();//网站安装的目录
	$path_dq0=$path_dq;
	if($path_dq==''){$path_dq='/';}
	else{$path_dq="/{$path_dq}/";}
	$array['neirong']=$path_dq;
	$c_sql->update('info',$array,"diaoyongbiaoqian='电脑站网址'");
	$array['neirong']=$path_dq.'m/';
	$c_sql->update('info',$array,"diaoyongbiaoqian='手机站网址'");
	$_SESSION['install']=time();
	echo "<p>数据还原OK，默认用户名密码均是：<b>admin</b><br/>";
	echo "前往，<a href='../admin' target='_blank'>后台</a> <a href='../../../' target='_blank'>前台</a></p>";
	exit;
}
else{
	echo $res;
	$canshu['jindu']=$canshu['jindu']+1;//下一页
	tiao($canshu);
}

//获取当前文件所在的文件夹
function path_dq(){
	$url=$_SERVER['REQUEST_URI'];
	$urls=explode('/',$url);
	return $urls[count($urls)-5];
}
?>