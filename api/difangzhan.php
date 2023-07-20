<?php
session_start();
include('../cms/include/function.php');

if(isset($_SESSION['ip'])){
	echo $_SESSION['ip'];
	exit;
}
//获取ip
$ip_txt=file_get_contents('http://pv.sohu.com/cityjson?ie=utf-8');
preg_match_all('/"cip": "(.*?)"/ism',$ip_txt,$ips);
$ip=$ips[1][0];

//获取地方
$url='http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
$difang_json=file_get_contents($url);
if(strstr($difang_json,'city')){
	$difangs=json_decode($difang_json,true);
	$difangs['data']['city_pinyin']=pinyin($difangs['data']['city'],$lx='all');
	$difang_json=json_encode($difangs);
	echo $difang_json;
	$_SESSION['ip']=$difang_json;
}
else{
	exit(0);
}
?>