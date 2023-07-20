<?php 
include("function_make.php");
$res['tishi']='';//提示
$res['tiao']='';//跳转
//传递的GET,?ms=pc&df=0&t=0
//$path_make为生成的文件夹
//$path_muban当前模板文件夹

//开启模式
$kaiqi=$c_sql->select("select neirong from info where diaoyongbiaoqian='启动模式'");
$kaiqi=$kaiqi[0]['neirong'];

//当前模式ms电脑站pc,手机站m
if(isset($_GET['ms'])){$ms=$_GET['ms'];}
else{
	if(strstr($kaiqi,'电脑')){$ms='pc';}
	else{$ms='m';}
}

//电脑站网址
$www=$c_sql->select("select neirong from info where diaoyongbiaoqian in('电脑站网址','手机站网址')");
$pc_www=$www[0]['neirong'];
$m_www=$www[1]['neirong'];

//当前地方，当前文件夹
if(isset($_GET['df'])){$df=$_GET['df'];}
else{$df=0;}
$difangs_dq=$c_sql->select("select id,name,pinyin from liandong where (lid=1 and run=1) order by paixu,id limit $df,1");
if(count($difangs_dq)==0){
	//没有地方站情况下S
	if($df==0){
		$df_1_id=0;
		$df_1_name='';
		
		$m='';
		if($ms=='m'){$m='/m';}
		$path_make='../../..'.$m;
		
		//文件夹不存在创建文件夹
		is_dir($path_make) OR mkdir($path_make, 0777, true);
		
		//创建search.php文件
		$path_s=$path_make.'/search.php';
		$cj=0;
		if(file_exists($path_s)){
			if(filemtime($path_s)<filemtime('./search.php')){$cj=1;}
		}
		else{$cj=1;}
		
		if($cj==1){
			
			$url_th='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
			$urls_th=explode('/cms/',$url_th);
			$url_th=$urls_th[0];
			$s_txt=$searchphp;
			$bth_arr=array('{url}','{ms}','{df}');
			$th_arr=array($url_th,$ms,$df);
			$s_txt=str_ireplace($bth_arr,$th_arr,$s_txt);
			write($path_s,$s_txt);
		}
	}
	else{
		$res['tishi']="OK，生成HTML静态页完成！";
		if(strstr($kaiqi,'电脑')){
			$res['tishi'].="<a href='$pc_www' target='_blank'>电脑站</a>";
		}
		if(strstr($kaiqi,'手机')){
			$res['tishi'].="<a href='$m_www' target='_blank'>手机站</a>";
		}
		echo json_encode($res);//返回json
		
		//生成地图
		include("make_seo.php");
		exit;
	}
}
else{
	$df_1_id=$difangs_dq[0]['id'];
	$df_1_name=$difangs_dq[0]['name'];
	
	$m='';
	if($ms=='m'){$m='/m';}
	if($df==0){
		$path_make='../../..'.$m;
	}
	else{
		$path_make='../../../'.$difangs_dq[0]['pinyin'].$m;
	}
	//文件夹不存在创建文件夹
	is_dir($path_make) OR mkdir($path_make, 0777, true);
	
	//创建search.php文件
	$path_s=$path_make.'/search.php';
	$cj=0;
	if(file_exists($path_s)){
		if(filemtime($path_s)<filemtime('./search.php')){$cj=1;}
	}
	else{$cj=1;}
	
	if($cj==1){
		$url_th='http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		//$url_th='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
		$urls_th=explode('/cms/',$url_th);
		$url_th=$urls_th[0];
		$s_txt=$searchphp;
		$bth_arr=array('{url}','{ms}','{df}');
		$th_arr=array($url_th,$ms,$df);
		$s_txt=str_ireplace($bth_arr,$th_arr,$s_txt);
		write($path_s,$s_txt);
	}
	
	
	
}

//当前模板文件夹
if($ms=='pc'){
	$diaoyongbiaoqian='电脑模板文件';
}
else{
	$diaoyongbiaoqian='手机模板文件';
}

$path_muban=$c_sql->select("select neirong from info where diaoyongbiaoqian='$diaoyongbiaoqian'");
$muban=$path_muban[0]['neirong'];//模板文件名
$path_muban='../../templets/'.$muban;
//栏目处理S
$types=$c_sql->select("select id,tid,lanmumingcheng,baocunlujing,lanmutupian,shujumoxing,lanmumoban,xiangqingmoban,fulanmumingcheng,youhuazhaiyao,lanmutupian,run from type order by paixu asc");
$type_q=array();//全部栏目
$type_1=array();//一级栏目
$type_2=array();//二级栏目
foreach($types as $type){
	$id=$type['id'];
	$tid=$type['tid'];
	$type_q[$id]=$type;
	if($tid==0){
		$type_1[$id]=$type;
	}
	else{
		$type_2[$tid][]=$type;
	}
}
$mx_ziduans_news='';
$mx_ziduans_news_b='';
//模型所有字段
$mx_ziduans=$c_sql->select("select diaoyongmingcheng from moxing where mid!=0");
foreach($mx_ziduans as $arr){
	$mx_ziduans_news.='{'.$arr['diaoyongmingcheng'].'}';
	$mx_ziduans_news_b.='['.$arr['diaoyongmingcheng'].']';
}

/********************生成栏目页和详情页********************/
if(isset($_GET['t'])){$t=$_GET['t'];}
else{$t=0;}

if(isset($_GET['php']) && isset($_GET['art'])){
	$tid=$c_sql->select("select tid from art where id=".$_GET['art']);
	$type_dq=$c_sql->select("select id,tid,lanmumoban,lanmumingcheng,xiangqingmoban,baocunlujing,shujumoxing from type where id=".$tid[0]['tid']);
	$_GET['art_p']=2;
}
else if(isset($_GET['list'])){
	$type_dq=$c_sql->select("select id,tid,lanmumoban,lanmumingcheng,xiangqingmoban,baocunlujing,shujumoxing from type where id=".$_GET['list']);
}
else{
	$type_dq=$c_sql->select("select id,tid,lanmumoban,lanmumingcheng,xiangqingmoban,baocunlujing,shujumoxing from type where baocunlujing not like 'http%' limit $t,1");
}
if(count($type_dq)<=0 && !isset($_GET['index'])){
	if($difangs_dq[0]['name']==''){
		$res['tishi']='生成[<b>'.$ms.'版</b>]首页';
	}
	else{
		$res['tishi']='生成[<b>'.$difangs_dq[0]['name'].'站'.$ms.'版</b>]首页';
	}
	$res['tiao']="&ms=$ms&df=$df&index";
	echo json_encode($res);//返回json
	exit;
}
if(count($type_dq)>0){
	$type_id=$type_dq[0]['id'];//栏目id
	if(isset($_GET['art_p'])){$art_p=$_GET['art_p'];}
	else{$art_p=1;}
}

/**********************************开始生成****************************************/
if(isset($_GET['index'])){	
	$index_html=get($path_muban.'/index.html');//首页
	$index_htmls=baohan($index_html);
	$index_html=$index_htmls['str'];
	$sx_id=$index_htmls['sx_id'];
	$index_htmls=baohan_qita($index_html);
	$index_html=$index_htmls['html'];
	
	$info_seo=$c_sql->select("select neirong from info where diaoyongbiaoqian in('网站SEO标题','网站SEO关键词','网站SEO描述')");
	$seo_arr=array($info_seo[0]['neirong'],$info_seo[1]['neirong'],$info_seo[2]['neirong']);
	$index_html=seo($index_html,$seo_arr);
	
	$index_html=str_ireplace('../../upload/',"{$pc_www}cms/upload/",$index_html);//图片路径转为绝对路径
	//动态浏览首页
	if(isset($_GET['php']) && !isset($_GET['make_dan'])){
		echo $index_html;
		exit;
	}
	//单个生成首页
	if(isset($_GET['make_dan'])){
		write($path_make.'/index.html',$index_html);
		
		if($ms=='m'){
			exit('index_ok');
		}
		else if(strstr($kaiqi,'手机')){
			exit('make_dan&php&art='.$_GET['art_id'].'&ms=m');
		}
		else{
			exit('index_ok');
		}
	}	
	write($path_make.'/index.html',$index_html);
	if($ms=='pc' && strstr($kaiqi,'手机')){
		if($difangs_dq[0]['name']==''){
			$res['tishi']='生成[<b>'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目';
		}
		else{
			$res['tishi']='生成[<b>'.$difangs_dq[0]['name'].'站'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目';
		}
		
		$res['tiao']="&ms=m&df=$df";
		echo json_encode($res);//返回json
		exit;
	}
	else if(!strstr($kaiqi,'手机') || $ms=='m'){
		if($difangs_dq[0]['name']==''){
			$res['tishi']='生成[<b>'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目';
		}
		else{
			$res['tishi']='生成[<b>'.$difangs_dq[0]['name'].'站'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目';
		}
		$res['tiao']="&df=".($df+1);
		echo json_encode($res);//返回json
		exit;
	}
}

else{
	//生成栏目页
	if($art_p==1){
		$type_html=get($path_muban.'/'.$type_dq[0]['lanmumoban']);//列表模板
		
		$type_htmls=baohan($type_html);
		$type_html=$type_htmls['str'];
		$sx_id=$type_htmls['sx_id'];
		$type_htmls=baohan_qita($type_html);
		$type_html=$type_htmls['html'];
		$title_sx=$type_htmls['title_sx'];
		$type_html=make_type($type_html,$type_id);
		if($type_html=='type_ok'){
			if($type_dq[0]['shujumoxing']==''){
				if($difangs_dq[0]['name']==''){
					$res['tishi']='生成[<b>'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目页';
				}
				else{
					$res['tishi']='生成[<b>'.$difangs_dq[0]['name'].'站'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目页';
				}
				$res['tiao']="&ms=$ms&df=$df&t=".($t+1)."";
				echo json_encode($res);//返回json
				exit;
			}
		}
	}
	//是否需要更新详情页
	$art_html=get($path_muban.'/'.$type_dq[0]['xiangqingmoban']);//详情模板
	
	$art_htmls=baohan($art_html);
	$art_html=$art_htmls['str'];
	$sx_id=$art_htmls['sx_id'];
	$art_htmls=baohan_qita($art_html);
	$art_html=$art_htmls['html'];	
	$art_html=make_art($art_html,$type_id);//文章
	if($art_html=='art_ok'){
		if($difangs_dq[0]['name']==''){
			$res['tishi']='生成[<b>'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目文章页';
		}
		else{
			$res['tishi']='生成[<b>'.$difangs_dq[0]['name'].'站'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目文章页';
		}
		$res['tiao']="&ms=$ms&df=$df&t=".($t+1);
		echo json_encode($res);//返回json
		exit;
	}
	else{
		if($difangs_dq[0]['name']==''){
			$res['tishi']='生成[<b>'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目文章页完成';
		}
		else{
			$res['tishi']='生成[<b>'.$difangs_dq[0]['name'].'站'.$ms.'版</b>][<b>'.$type_dq[0]['lanmumingcheng'].'</b>]栏目文章页完成';
		}
		
		if(!isset($_GET['art_p'])){$_GET['art_p']=0;}
		$res['tiao']="&ms=$ms&df=$df&t=$t&art_p=".($_GET['art_p']+1);
		echo json_encode($res);//返回json
		exit;
	}
}
?>