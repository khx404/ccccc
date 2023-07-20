<?php 
require("../include/class_sql.php");
echo "<style type='text/css'>*{ padding:0; margin:0; font-size:12px;color:#1aacda}</style>";
$canshu=$_GET;//传递的参数 （进度、页数）
$res=$c_sql->huanyuan($canshu['riqi'],$canshu['jindu']);
if(strstr($res,'文件代码有误')){
	exit($res);
}
if($res=='100'){
	exit('数据还原成功');
}
else{
	echo $res;
	$canshu['jindu']=$canshu['jindu']+1;//下一页
	tiao($canshu);
}
?>