<?php 
error_reporting(0);
header("Content-type: text/html; charset=utf-8");
if(count($_GET)<=0 and file_exists('index.html')){
	echo "<script>window.location='index.html';</script>";
	exit;
}
else if(!file_exists('cms/cms/install/install.txt')){
	echo "<script>window.location='cms/cms/install/index.php';</script>";
	exit;
}
else{
	echo "<script>window.location='search.php?index';</script>";
}
?>