<?php
if(isset($_GET['run'])){
	$run=$_GET['run'];
}
else{
	exit('参数有误');
}
//上传
if($run=='file'){
	$res=array();
	$path=$_GET['path'];//上传的路径
	$filename=$_GET['filename'];//1不更改，0更改
	is_dir($path) OR mkdir($path, 0777, true);//文件夹不存在创建文件夹
	foreach($_FILES as $k=>$arr){
		$path=$_GET['path'];//上传的路径
		$name=$arr['name'];//文件名
		$type=$arr['type'];//文件类型
		$tmp_name=$arr['tmp_name'];//临时文件
		$size=$arr['size'];//文件大小
		
		/*目标地址*/
		if($filename==1){
			$path.=$name;
		}
		else{
			$path.=time().$k.hz($name);
		}
		
		/*上传*/
		if(move_uploaded_file($tmp_name,$path)){
			$res[]=$path;
		}
	}
	echo json_encode($res);
}

//删除文件
if($run=='del'){
	$url=$_POST['url'];
	if(!unlink($url)){
		echo 0;
	}
	else{
		echo 1;
	}
}

if($run=='guolv'){
	$zhi=xiegang($_POST['zhi']);
	preg_match_all("/src=\"(.*?)\"/ism",$zhi,$src);
	echo implode(';',$src[1]);
}

//文件后缀
function hz($name){
	$names=explode('.',$name);
	return '.'.$names[count($names)-1];
}

//反斜杠处理
function xiegang($str){
	if(get_magic_quotes_gpc()){
		return stripslashes($str);//将字符串进行处理
	}
	else{
		return $str;
	}
}
?>