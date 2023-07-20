<?php 
include('../include/class_sql.php');
if(!isset($_GET['run'])){
	exit("参数有误");
}
$run=$_GET['run'];

$pc_www=$c_sql->select("select neirong from info where diaoyongbiaoqian='电脑站网址'");
$pc_www=$pc_www[0]['neirong'];

//增或改
if($run=='addedit'){
	$table=$_GET['table'];
	$where="id=".$_POST['id'];
	$cha=$c_sql->select("select id from $table where $where");
	if(isset($cha[0]['id'])){
		$res=$c_sql->update($table,$_POST,$where);
	}
	else{
		$res=$c_sql->insert($table,$_POST);
	}
	echo $res;
}

//删
if($run=='del'){
	$table=$_GET['table'];
	$where="id=".$_POST['id'];
	echo $c_sql->delete($table,$where);
}

//图片上传
if($run=='youad_pic'){
	$path='../../upload/';
	$path_res='../../upload/';
	$res='';
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
			$pathurl_res=str_ireplace($path,$path_res,$pathurl);
			
			$res.=$pathurl_res;
		}
	}
	echo $res;
}

//检测是否需要开通
if($run=='addxinzeng'){
	$url=base64_decode(URL)."caiji/api.php?web=".web_dq()."&run=addxinzeng";
	echo file_get_contents($url);
	exit;
}

//新增任务
if($run=='renwu_add'){
	$url=base64_decode(URL)."caiji/api.php?web=".web_dq()."&run=renwu_add&run_renwu=".$_GET['run_renwu'];
	foreach($_POST as $k=>$v){
		$url.="&{$k}={$v}";
	}
	echo file_get_contents($url);
}

//删除任务
if($run=='renwu_del'){
	$url=base64_decode(URL)."caiji/api.php?web=".web_dq()."&run=renwu_del&id=".$_POST['id'];
	echo file_get_contents($url);
}

if($run=='shiyong'){
	echo file_get_contents(base64_decode(URL)."caiji/api.php?web=".web_dq()."&run=add_tongji&shiyong&tongji_run=".$_POST['run']);
}

//预支付订单
if($run=='daizhifu'){
	$url=url($_GET);
	echo file_get_contents(base64_decode(URL)."caiji/api.php".$url);
}


/*********************开始生成html静态页************************/
if($run=='html'){
	//当前更新电脑站还是手机站
	if(isset($_POST['ms'])){$ms=$_POST['ms'];}
	else{$ms='pc';}

	//地方站
	if(isset($_POST['df'])){$df=$_POST['df'];}
	else{$df=0;}
	
	//生成到文件夹判断
	$path='../../..';
	if($df>0){
		$dfs=$c_sql->select("select name from liandong where(lid=1) limit $df,1");
		if(isset($dfs[0]['name'])){
			$path.='/'.pinyin($dfs[0]['name'],'all');
			if(!is_dir($path)){
				mkdir($path);
			} 
		}
		else{
			$data['tishi']='ok1';
			echo json_encode($data);//返回json
			exit;
		}
	}
	
	//表
	if(isset($_POST['table'])){$table=$_POST['table'];}
	else{$table='art';}
	
	//第几条
	if($_POST['limit']){$limit=$_POST['limit'];}
	else{$limit=0;}
	
	if($table!='index'){
		$ziduan='id';
		if($table=='type'){
			$ziduan.=',baocunlujing';
		}
		
		$ids=$c_sql->select("select $ziduan from $table limit $limit,1");
		if(isset($ids[0]['id'])){
			$id=$ids[0]['id'];
		}
		else{
			if($table=='art'){
				$data['table']='type';
				$data['limit']=0;
				$data['url']='';
				echo json_encode($data);//返回json
				exit;
			}
			
			if($table=='type'){
				$data['table']='index';
				$data['limit']=0;
				$data['url']='';
				echo json_encode($data);//返回json
				exit;
			}
			else{
				$data['tishi']='ok';
				echo json_encode($data);//返回json
				exit;
			}
		}
	}
	
	if($table=='art'){
		$res="{$pc_www}show.php?ms=$ms&df=$df&art=$id";
		file_put_contents($path."/{$id}.html",get($res));
		$data['tishi']="正在生成详情ID为{$id}的页面";
	}
	
	else if($table=='type'){
		$res="{$pc_www}show.php?ms=$ms&df=$df&list=$id";
		file_put_contents($path.'/'.$ids[0]['baocunlujing'].'.html',get($res));
		$data['tishi']="正在生成栏目ID为{$id}的页面".$ids[0]['baocunlujing'];
	}
	
	else{
		$res="{$pc_www}show.php?ms=$ms&df=$df";
		file_put_contents("$path/index.html",get($res));
		$data['ms']=$ms;
		$data['df']=$df*1+1;
		$data['table']='art';
		$data['limit']=0;
		echo json_encode($data);//返回json
		exit;
	}
	$data['ms']=$ms;
	$data['df']=$df;
	$data['table']=$table;
	$data['limit']=$limit*1+1;
	echo json_encode($data);//返回json
}

if($run=='install_tj'){
	$www=web_dq();
	if(strstr($www,'127.0.0') || strstr($www,'localhost')){exit;}
	$url=base64_decode(URL)."caiji/api.php?web=".$www."&run=install";
	echo file_get_contents($url);
}








?>