<?php 
include('../../'.pathcms("../../").'/include/class_sql.php');//引用主程序文件
include('../../'.pathcms("../../").'/include/class_smtp.php');//引用邮箱类库
if(!isset($_GET['run'])){
	exit('参数有误');
}
$run=$_GET['run'];
//电脑站网址
$www=$c_sql->select("select neirong from info where diaoyongbiaoqian in('电脑站网址','手机站网址')");
if(isset($www[0]['neirong'])){
	$pc_www=$www[0]['neirong'];
}

//计算验证码
if($run=='jisuan'){
	$a=rand(0,9);
	$b=rand(0,9);
	$c=$a+$b;
	$arr['a']=$a;
	$arr['b']=$b;
	$arr['c']=$c;
	echo json_encode($arr);
}

//留言
if($run=='liuyan'){
	$post['diaoyongbiaoqian']='客户留言';
	if(isset($_GET['title'])){
		if($_GET['title']!=''){$post['diaoyongbiaoqian']=$_GET['title'];}
	}
	if(count($_POST)>0){
		$neirong='';
		foreach($_POST as $k=>$v){
			if($neirong==''){$neirong=$k.'：'.$v;}
			else{$neirong.="<br/>".$k.'：'.$v;}
		}
	}
	$post['shuyu']=3;//2属于留言
	$post['neirong']=$neirong;//留言内容
	
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
	$urls=explode('/cms/',$url);
	$url=$urls[0];
	$post['neirong'].="<br/>来源网页：".$url;//来源网页
	
	$post['paixu']=0;//0未处理，1已处理
	$post['morenzhi']=date("Y-m-d h:i:sa");//提交时间
	$post['leixing']=0;//ip地址
	$res=$c_sql->insert('info',$post);
	if($res>0){
		echo $post['neirong'];
	}
	else{
		echo 0;
	}
}

if($run=='liuyan_youxiang'){
	echo file_get_contents('http://www.whhjpt.com/mail_api/api.php?toemail='.$_POST['toemail'].'&title='.$_POST['title'].'&content='.$_POST['neirong']);
}

//广告
if($run=='ad' && $_GET['id']){
	$ads=$c_sql->select("select * from youad where id=".$_GET['id']);
	$biaoti=$ads[0]['biaoti'];
	$tupian=$ads[0]['tupian'];
	$width=$ads[0]['width'];
	$height=$ads[0]['height'];
	$lianjie=$ads[0]['lianjie'];
	
	if($tupian==''){
		$res="<a href='$lianjie' rel='nofollow' target='_blank'>$biaoti</a>";
	}
	else{
		$res="<a href='$lianjie' target='_blank' rel='nofollow'><img width='{$width}px' height='{$height}px' src='$tupian' /></a>";
	}
	$res=str_ireplace('../../upload/',"{$pc_www}cms/upload/",$res);//图片路径转为绝对路径
	echo "document.write(\"{$res}\");";
}

//点击量api
if($run=='dj' && $_GET['id']){
	$dianjis=$c_sql->select("select dianji from art where id=".$_GET['id']);
	$dianji=$dianjis[0]['dianji'];
	if($dianji==NULL or $dianji==''){
		echo "document.write('0');";
	}
	else{
		echo "document.write('{$dianji}');";
	}
	$post=array('dianji'=>$dianji*1+1);
	$c_sql->update('art',$post,'id='.$_GET['id']);
}

//主程序目录(相对根目录下cms文件)
function pathcms($path){
	$wenjians=array_diff(scandir($path),array("..","."));//获取模板;
	foreach($wenjians as $wj){
		$path_xin=$path.$wj;
		if(is_dir($path_xin.'/include') && is_dir($path_xin.'/zbzedit')){
			return $wj;
		}
	}
}
?>