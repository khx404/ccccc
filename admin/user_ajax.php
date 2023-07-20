<?php 
include('../include/class_sql.php');
if(!isset($_GET['run'])){
	exit("参数有误");
}
$run=$_GET['run'];

//提货
if($run=='tihuo'){
	$id=$_POST['id'];
	$tihuoma=$_POST['tihuoma'];
	$uorder_arr['run']=time();
	echo $c_sql->update('uorder',$uorder_arr,"id=$id and tihuoma='{$tihuoma}'");
}

?>