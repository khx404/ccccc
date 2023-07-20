<?php 
error_reporting(E_ALL^E_NOTICE^E_WARNING);
if(is_file('install.txt')){exit('error');}
else if(isset($_GET['run'])){$run=$_GET['run'];}
else{exit('error');}

//数据库链接检测(链接成功返回1失败返回0)
if($run=='link'){
	$conn = mysqli_connect($_POST['host'],$_POST['username'],$_POST['password']);
	if(!$conn){exit('0');}
	else{exit('1');}
}

//点击安装按钮
if($run=='install'){
	$host=$_POST['host'];
	$username=$_POST['username'];
	$password=$_POST['password'];
	$dbname=$_POST['dbname'];
	$guanliyuan=$_POST['guanliyuan'];
	$mima=$_POST['mima'];
	
	$conn = mysqli_connect($host,$username,$password);
	if(!$conn){exit("数据库链接失败");}
	else{
		//选择数据库
		if(!mysqli_select_db($conn,$dbname)){
			$sql="CREATE DATABASE {$dbname} DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";//创建数据库
			if(!mysqli_query($conn,$sql)){exit("数据库《{$dbname}》不存在，且无法创建");}
		}
		mysqli_set_charset($conn,'utf8') or die('设置编码失败');//指定数据库连接字符集
		$config="<?php
define('HOST', '$host');//数据库地址
define('USERNAME', '$username');//数据库用户名
define('PASSWORD', '$password');//数据库密码
define('DBNAME', '$dbname');//数据库名称
define('CHARSET', 'utf8');//编码
define('URL', 'aHR0cDovL2FwaS56aGFuYmFuZ3podS5jb20v');
?>";
		if(file_put_contents('../include/config.php',$config)){
			date_default_timezone_set("Asia/Shanghai");
			file_put_contents('install.txt','该源码安装于：'.date("Y/m/d h:i:sa"));
			setcookie('install','1');
			echo 1;
		}
		else{
			exit('无法创建config.php文件');
		}
	}
}
?>