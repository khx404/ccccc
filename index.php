<?php
if(!isset($_GET['path']) || !isset($_GET['baohan'])){
	exit('110');
}

jiance();
//页面安全检测
function jiance(){
	$path=$_GET['path'];//相对路径
	$baohan=$_GET['baohan'];//包含的代码
	chmod($path,0777);//给目标文件设置权限
	$index=file_get_contents($path);//获取首页文档
	//第一步先判断文件最后编辑时间是否大于缓存文件的时间
	preg_match_all("/<script.*?src=['|\"](.*)['|\"]/i",$index,$jss);//获取标题
	$js_src=$jss[1][0];
	$js_xdsrc="../../cms/templets/pc/js/jquery-1.7.2.min.js";
	chmod($js_xdsrc,0777);//给目标文件设置权
	$jstxt=file_get_contents($js_xdsrc);//获取首页文档
	
	$baohans=explode('|',$baohan);
	
	foreach($baohans as $bh){
		if(strstr($jstxt,$bh)){
			echo '1';
		}
	}
	
	//print_r($baohans);
	
	
	//echo $jstxt;
	
	//echo $js_src;
	exit;
	
	
	//如果大于则进行下面操作
	
	
	
	
	
	
	
	
	preg_match_all("/<title>(.*)<\/title>/ism",$index,$titles);//获取标题
	//怀疑挂马则重新生成html到idnex.html
	if(isset($titles[1][0])){
		//生成存储路径
		$path_ok=str_ireplace('.','0',$path);
		$path_ok=str_ireplace('/','1',$path_ok);
		$path_ok='./html/'.$path_ok.'.html';
		
		//有挂马，将安全文件还原
		if(strstr($titles[1][0],'&#')){
			$index_ok=file_get_contents($path_ok);
			file_put_contents($path,$index_ok);
			echo '//error';
		}
		else{
			//安全，将页面存入html
			file_put_contents($path_ok,$index);
			echo '//ok';
		}
	}
}
?>