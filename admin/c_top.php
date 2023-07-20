<?php 
$ym='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
if(strstr($ym,'//localhost')){
	$tiao=str_replace("//localhost","//127.0.0.1",$ym);
	exit("<script>window.location='{$tiao}';</script>");
}

include("../include/class_sql.php");
if(!$_SESSION['id'] or !$_SESSION['guanliyuan']){
	exit("<script>window.location='login.php';</script>");
}
else{
	$admin_id=$_SESSION['id'];
	$admins=$c_sql->select("select * from admin where id={$admin_id} limit 1");
	$admins=$admins[0];
	$guanliyuan=$admins['guanliyuan'];
	$dengji=$admins['dengji'];
}

//权限设置S
$quanxian='';
if($dengji==2){
	$quanxian=" style='display:none'";
	$wenjian=wenjian_dq();
	if($wenjian=='art_list.php' || $wenjian=='art.php' || $wenjian=='art_list.php'){}
	else{echo "<script>window.location='art.php';</script>";}
}
//权限设置E

//演示站
$demo='';
if(file_exists("../../../demo.txt")){
	$demo="alert('很抱歉，为了保证数据的完整性，演示后台无法启动该操作')"."\r\n";
	$demo.="return false;";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>站帮主管理系统</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
<script src="../../common/js/jquery.min.js"></script>
<script src="../../common/js/common.js"></script>
<script src="js/tan.js"></script>
</head>
<body>
<!--头部-->
<div class="top">
	<div class="topbox">
	<a class="logo float_l" href="index.php"><i class="float_l">Z</i><span class="float">站帮主</span></a>
	<ul class="nav float_l">
    	<li class="nav_fu float_l"<?php echo $quanxian; ?>><a href="info.php">系统设置</a>
        	<ul class="nav_zi">
            	<li><a href="info.php">基本设置</a></li>
                <li><a href="info_moxing.php">模型管理</a></li>
                <li><a href="info_shuju.php">数据维护</a></li>
                <li><a href="info_admin.php">管理员</a></li>
            </ul>
        </li>
        
        <li class="nav_fu float_l"><a href="type_list.php">栏目发布</a>
        	<ul class="nav_zi">
            	<li<?php echo $quanxian; ?>><a href="type_list.php">栏目中心</a></li>
                <li><a href="art_list.php">内容列表</a></li>
                <li><a href="type_list.php?fabu">发布</a></li>
            </ul>
        </li>
        
        <li class="nav_fu float_l"<?php echo $quanxian; ?>><a>模板中心</a>
        	<ul class="nav_zi">
            	<li><a href="wenjian.php?path=../../templets/<?php echo ii('电脑模板文件'); ?>">电脑模板</a></li>
                <li><a href="wenjian.php?path=../../templets/<?php echo ii('手机模板文件'); ?>">手机模板</a></li>
                <li><a href="wenjian.php?path=../../..">根目录</a></li>
            </ul>
        </li>
        
        <li class="nav_fu float_l"<?php echo $quanxian; ?>><a href="liuyan.php" class="zong">用户留言</a>
        	<ul class="nav_zi ly"></ul>
        </li>
        
        <li class="nav_fu float_l"<?php echo $quanxian; ?>><a href="youlian.php">友链广告</a>
        	<ul class="nav_zi">
            	<li><a href="youlian.php">友情链接</a></li>
        		<li><a href="ad.php">广告管理</a></li>
            </ul>
        </li>
        
        <li class="nav_fu float_l"<?php echo $quanxian; ?> style="width:160px;"><a style="width:100%;">SEO优化<!--<i>hot</i>--></a>
        	<ul class="nav_zi">
            	<li><a href="seo_duozhan.php">霸屏多地方站</a></li>
                <li><a href="seo_shenhe.php" title="自动审核并发布到前台">自动审核发布</a></li>
                <li><a href="seo_gengxin.php" title="自动采集伪原创并发布到前台">自动采集发布</a></li>
                <li><a href="seo_wyc.php">伪原创设置</a></li>
                <li><a href="seo_tuisong.php">搜索引擎推送</a></li>
                <li><a href="seo_paiming.php">排名监控</a></li>
            
            </ul>
        </li>
        
    </ul>
    <ul class="nav float_r">
    	<li class="nav_fu float_l nav_more"><a onClick="html(0)">发布</a>
        	<ul class="nav_zi">
            	<li<?php echo $quanxian; ?>><a onClick="html(0)">更新发布</a></li>
            	<li><a target="_blank" href="../../../search.php?index">预览电脑版</a></li>
                <li><a target="_blank" href="../../../m/search.php?index">预览手机版</a></li>
                <li><a target="_blank" href="../../../">电脑版</a></li>
                <li><a target="_blank" href="../../../m/">手机版</a></li>
                <li<?php echo $quanxian; ?>><a href="tag.php" target="_blank">建站Tag</a></li>
                <li<?php echo $quanxian; ?>><a onClick="shengji()" id="shengji">在线升级</a></li>
                <li><a onClick="tui()">退出</a></li>
            </ul>
        </li>
    </ul>
    </div>
</div>
<div class="cle70"></div>
<div class="anquan"></div>
<audio controls id="audio" style="display:none">
	<source src="./mp3/tx1.mp3" type="audio/mp3" />
	<source src="./mp3/song.ogg" type="audio/ogg" />
	<embed height="100" width="100" src="../../common/style/mp3/tx1.mp3" />
</audio>
<script>
//改变title滚动s
var text=document.title;
var timerID;
function newtext(){
	clearTimeout(timerID)
	document.title=text.substring(1,text.length)+text.substring(0,1);
	text=document.title.substring(0,text.length);
	timerID = setTimeout("newtext()",100);
}
//改变title滚动e

//提示
function ts(tishi){
	tanbox();//引入提示
	$(".tan_tit_con").html('温馨提示');//提示内容
	$(".tan_con").html(tishi);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

function x(){
	$('.anquan p').hide();
}
function shaohou(){
	$.post('run_ajax.php?run=shaohou',{},function(res){
		if(res==1){
			$('.anquan p').hide();
		}
	})
}

$(function(){
	liuyan_tx();
	liuyan_duqu();
});

//留言提醒
function liuyan_tx(){
	$.post('run_ajax.php?run=liuyan_tx',{},function(res){
		if(res==1){
			newtext();
			var audio=document.getElementById("audio");
			audio.play();
		}
		setTimeout(liuyan_tx,30000);
		setTimeout(liuyan_duqu,30000);
	});
}
</script>