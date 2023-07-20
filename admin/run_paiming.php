<?php 
header("Content-type: text/html; charset=utf-8");
include "../include/class_sql.php";
$curl = new Curl();
if(isset($_SESSION['i'])){$i=$_SESSION['i'];}
else{$i=0;}

if(isset($_SESSION['ms'])){$ms=$_SESSION['ms'];}
else{$ms='pc';}

if(isset($_SESSION['pm'])){$paiming_txt=$_SESSION['pm'];}
else{$paiming_txt='';}

if(isset($_POST['keys'])){
	$www=$_SERVER['SERVER_NAME'];
	$www=str_replace(array('www.','m.'),'',$www);
	paiming($www,$_POST['keys'],$i,$ms);
}
else{
	exit('error');
}
function paiming($www,$keys,$i,$ms){
	global $paiming_txt;
	global $curl;
	global $c_sql;
	$keys=str_replace(array(',','，','；','|'),';',$keys);
	$keys=explode(';',$keys);
	if(isset($keys[$i])){
		$key=$keys[$i];
		$pm='>50';
		for($p=0;$p<5;$p++){
			if($ms=='pc'){$baidu_url="http://www.baidu.com/s?wd={$key}&pn=".$p*10;}
			else{$baidu_url="http://m.baidu.com/s?wd={$key}&pn=".$p*10;}
			$html=$curl->get($baidu_url);
			if(strstr($html,$www)){
				if($ms=='pc'){preg_match_all("/result c-container \" id=\"(.*?)\"(.*?)result c-container/ism",$html,$pms);}
				else{preg_match_all("/c-result result.*?order=\"(.*?)\"(.*?)c-result result/ism",$html,$pms);}
				foreach($pms[0] as $k=>$v){
					if(strstr($v,$www)){
						$pm=$pms[1][$k];
						break;
					}
				}
				break;
			}
		}
		if($paiming_txt==''){
			$_SESSION['pm']="{$key}|{$pm}|".time();
		}
		else{
			$_SESSION['pm']=$paiming_txt.";{$key}|{$pm}|".time();
		}
		$_SESSION['i']=$i*1+1;
	}
	else{
		if($ms=='pc'){
			$_SESSION['pm']=$paiming_txt."&";
			$_SESSION['ms']='m';
			$_SESSION['i']=0;
		}
		else{
			//入库
			$post['neirong']=$paiming_txt;
			$_SESSION['paiming']=time();
			$c_sql->update('info',$post,"diaoyongbiaoqian='百度排名'");
			unset($_SESSION["i"]);
			unset($_SESSION["ms"]);
			unset($_SESSION["pm"]);
			echo '1';
		}
	}
}
?>