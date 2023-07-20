<?php 
$path="../../../index.html";
$index=file_get_contents($path);
preg_match_all("/<title>(.*)<\/title>/ism",$index,$titles);//获取标题

//怀疑挂马则重新生成html到idnex.html
if(strstr($titles[1][0],'&#')){
	
	
	
	
}
?>