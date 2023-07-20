<?php
//2019/8/2 23:42
$searchphp='<?php 
header("Content-type: text/html; charset=utf-8");
$url="{url}";
$url=$url."/cms/cms/include/make.php?php&ms={ms}&df={df}".get();
echo getcurl($url);
function get(){
	$res="";
	if(count($_GET)<=0){return $res;}
	foreach($_GET as $k=>$v){
		$zhi="";
		if($v!=""){$zhi="={$v}";}
		$res.="&{$k}{$zhi}";
	}
	return $res;
}

function getcurl($url){
	return file_get_contents($url);
}
?>';
?>