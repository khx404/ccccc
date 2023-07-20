<?php 
session_start();
if(!$_SESSION['id'] or !$_SESSION['guanliyuan']){
	exit("<script>window.location='login.php';</script>");
}
if(isset($_GET['txt'])){
	echo $_GET['txt'];
}
else{
	exit('error');
}

/*中文文件名处理S*/
$ua = $_SERVER["HTTP_USER_AGENT"];  
date_default_timezone_set('PRC'); 
$filename ="网站后台".date("Y-m-d H点i分s秒").'.txt';    
$encoded_filename = urlencode($filename);    
$encoded_filename = str_replace("+", "%20", $encoded_filename); 
/*中文文件名处理E*/
header("Content-Type: application/octet-stream");      
if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) ) {      
	header('Content-Disposition:  attachment; filename="' . $encoded_filename . '"');      
}
elseif (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {      
	header('Content-Disposition: attachment; filename*="' .  $filename . '"');      
}
else {      
	header('Content-Disposition: attachment; filename="' .  $filename . '"');      
}  
ob_end_flush();
?>