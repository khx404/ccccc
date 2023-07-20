<?php 
//批量上传txt文章
if(isset($_GET['run'])){$run=$_GET['run'];}
else{exit('error');}

//图片上传路径
if(isset($_GET['path'])){$path=$_GET['path'];}
else{$path='../up';}

//图片回调路径
if(isset($_GET['path_res'])){$path_res=$_GET['path_res'];}
else{$path_res='../up';}

//图片命名，0改名，1原名
if(isset($_GET['data_pic_name'])){$data_pic_name=$_GET['data_pic_name'];}
else{$data_pic_name='0';}

if($run=='uptxt'){
	$res=array();
	foreach($_FILES as $i=> $arr){
		$tmp_name=$arr['tmp_name'];//临时文件
		
		if($data_pic_name==0){
			//后缀
			$houzhuis=explode('.',$arr['name']);
			$houzhui=$houzhuis[count($houzhuis)-1];
			$pathurl=$path.time().'_'.$i.'.'.$houzhui;
		}
		else{
			$pathurl=$path.$arr['name'];
		}
		
		is_dir($path) OR mkdir($path, 0777, true);//文件夹不存在创建文件夹
		$pathurl=iconv("UTF-8","gb2312",$pathurl);//目标路径
		if(move_uploaded_file($tmp_name,$pathurl)){
			$res[]=str_ireplace($path,$path_res,$pathurl);
		}
	}
	echo json_encode($res);//返回json
}
?>