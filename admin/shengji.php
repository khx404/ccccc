<?php 
include('../include/class_sql.php');
error_reporting(E_ALL^E_NOTICE^E_WARNING);
header("Content-type: text/html; charset=utf-8");
echo "<style type='text/css'>
*{ padding:0; margin:0;}
p{ font-size:13px; color:#FF0000;}
</style>";
$url_sj=base64_decode(URL).'update/utf8/';//升级目标网址
$json_sj=file_get_contents($url_sj.'update.php');
$arr_sj=json_decode($json_sj,true);
$panduan="<p style='color:#1aacda'>经检测您的程序已经属于最新版本</p>";
foreach($arr_sj as $arr){
	$path=$arr[0];//升级文件路径
	$shijian=$arr[1];//升级编辑时间
	
	//后台相对根目录的位置
	$admin_gen="../../../";
	//判断当前路径所属S
	if(stristr($path,'/cms/admin/')){
		$ht=explode('/admin/',$path);
		$path_dq='./'.$ht[1];
		$path_dq."<br>";
	}
	else if(stristr($path,'/cms/cms/') && !stristr($path,'/cms/cms/admin')){
		$path_dq=str_ireplace('./','../../../',$path);
		
		$cms_dq='cms';
		$cmss=array_diff(scandir("../../"),array("..","."));//获取模板;
		foreach($cmss as $v){
			if(is_dir("../../{$v}/include") && is_dir("../../{$v}/templets")){
				$cms_dq=$v;
				break;
			}
		}
		$path_dq=str_ireplace('/cms/cms/',"/cms/{$cms_dq}/",$path_dq);
	}
	
	else{
		$path_dq=str_ireplace('./','../../../',$path);
	}
	//过滤txt后缀
	if($path_dq!='s.txt'){
		$path_dq=substr($path_dq, 0, -4);
	}
	//判断是否存在
	if(filemtime($path_dq)){
		$shijian_dq=filemtime($path_dq);
		if($shijian_dq<$shijian){
			if(isset($_GET['sj'])){fugai($path,$path_dq);break;}
			else{
				$panduan="<p>急需升级，系统最近更新时间为：".date("Y-m-d H:i",$arr[1]) ."</p>";
				break;
			}
		}
	}
	else{
		if(isset($_GET['sj'])){
			fugai($path,$path_dq);
			break;
		}
		else{
			$panduan="$path_dq<p>急需升级，系统最近更新时间为：".date("Y-m-d H:i",$arr[1]) ."</p>";
			break;
		}
	}
}

echo $panduan;

//覆盖
function fugai($path,$path_xin){
	global $url_sj;
	$path=$url_sj.str_ireplace('./','',$path);
	$wjj=dirname($path_xin);//文件夹
	mkdir($wjj,0777,true);
	$txt=file_get_contents($path);
	file_put_contents($path_xin,$txt);
	echo "<p>正在升级{$path_xin}文件</p>";
	echo "<script>window.location='shengji.php?sj';</script>";
}
?>