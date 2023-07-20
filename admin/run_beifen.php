<?php 
require("../include/class_sql.php");
echo "<style type='text/css'>*{ padding:0; margin:0; font-size:12px;color:#1aacda}</style>";
$canshu=$_GET;//传递的参数 （进度、页数）
$res=$c_sql->beifen($canshu['jindu'],$canshu['page']);
if($res===0){
	echo "整站数据备份完成！";
}
else if($res===1){
	$canshu['jindu']=$canshu['jindu']+1;//下一个表
	$canshu['page']=1;//初始化表
	tiao($canshu);
}
else{
	echo $res;
	$canshu['page']=$canshu['page']+1;//下一页
	//<hr />
	tiao($canshu);	
	echo 1;
}
?>