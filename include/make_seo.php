<?php 
//栏目
$type=$c_sql->select("select id,lanmumingcheng,baocunlujing from type");
$types=array();
if(count($type)>0){
	foreach($type as $arr){
		$id=$arr['id'];
		unset($arr['id']);
		$types[$id]=$arr;
	}
}

//文章
$art=$c_sql->select("select id,tid,biaoti from art");
$arts=array();
if(count($art)>0){
	foreach($art as $arr){
		$baocunlujing=$types[$arr['tid']]['baocunlujing'];
		unset($arr['tid']);
		$arr['baocunlujing']=$baocunlujing;
		$arts[]=$arr;
	}
}

$www='http://'.$_SERVER['SERVER_NAME'].'/';

//生成网站地图
$url_txt='';
$type_txt="<b>导航：</b><a href='{$www}'>网站首页</a>";
foreach($types as $type){
	$url=$www.$type['baocunlujing'].'/index.html';
	
	$url_txt.="<url>
<loc>{$url}</loc>
<changefreq>always</changefreq>
</url>\r\n";
	$type_txt.="|<a href='{$url}' target='_blank'>".$type['lanmumingcheng']."</a>";
}
$type_txt.="<hr>";

$art_txt="<b>最新文章：</b><br/>";
foreach($arts as $art){
	$url=$www.$art['baocunlujing']."/".$art['id'].".html";
	$url_txt.="<url>
<loc>{$url}</loc>
<changefreq>always</changefreq>
</url>\r\n";
	$art_txt.="<a href='{$url}' target='_blank'>".$art['biaoti']."</a><br/>";
}

$ditu=$type_txt.$art_txt;
$xml="<?xml version='1.0' encoding='UTF-8'?>
<urlset xmlns=http://www.google.com/schemas/sitemap/0.9>
{$url_txt}
</urlset>";

write("../../../sitemap.html",$ditu);
write("../../../sitemap.xml",$xml);
?>