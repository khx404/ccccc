<?php
header('content-type:text/html charset:utf-8');
//上传
if(isset($_FILES) && isset($_GET['path']) && isset($_GET['name']) && isset($_GET['id'])){
	$path = $_GET['path'];//文件上传根目录
	$name = $_GET['name'];//文件命名规则
	$id = $_GET['id'];//上传插件的id
	if (!file_exists($path)) {mkdir($path, 0777, true);}//上传目录不存在则创建
	$res='';//返回的参数
	$i=1;//递增的文件名
	foreach($_FILES as $arr){
		//文件名S
		if($name==1){$file_name=$arr['name'];}
		else{
			if($arr['type']=='image/png'){$file_name=time().$i.'.png';}
			if($arr['type']=='image/jpeg'){$file_name=time().$i.'.jpg';}
			if($arr['type']=='image/gif'){$file_name=time().$i.'.gif';}
		}
		//文件名E
		$tmp_name=$arr['tmp_name'];//临时文件
		$url=$path.$file_name;//新文件路径
		if(move_uploaded_file($tmp_name,$url)){
			$res.="<img id='$id{$i}00' onclick=\"del('$id{$i}00','{$id}')\" src='{$url}' />";
		}
		$i++;
	}
	echo $res;
}

//删除
else if(isset($_GET['del'])){
	$src=$_GET['del'];
	if(!unlink($src)){
		echo 0;
	}
	else{
		echo 1;
	}
	exit;
}

//提取图片
else if(isset($_GET['tiqu'])){
	$src=$_GET['tiqu'];
	preg_match_all("/<img .*?src=['|\"](.*?)['|\"]/i",$src,$imgusr);
	echo implode(";",$imgusr[1]);
}
?>