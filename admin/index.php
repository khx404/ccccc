<?php 
include('c_top.php');
$zong_fb=$c_sql->select("select count(id) from art where fabushijian!=1");
$zong_wfb=$c_sql->select("select count(id) from art where fabushijian=1");
?>
<!--主体-->
<div class="con">
	<!--左侧-->
	<div class="con_left">
    	<ul class="con_nav">
        	<li class="tit"><span>快捷导航</span></li>
            <li><a href="info.php" class="lminfo">基本设置</a></li>
            <li><a href="info_moxing.php" class="lminfo_moxing">模型管理</a></li>
            <li><a href="info_shuju.php" class="lminfo_shuju">数据维护</a></li>
            <li><a href="info_admin.php" class="lminfo_admin">管理员</a></li>
        </ul>
    </div>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
        	<span>后台主页</span>
        </div>

        <ul class="houzhu">
			<li style="background:#eee;">总共<b><?php echo $zong_fb[0]['count(id)']; ?></b>条记录，<a href="type_list.php?fabu">去发布</a></li>
			<li style="background:#ddd;"><b><?php echo $zong_wfb[0]['count(id)']; ?></b>条库存待发布文章</li>
			<li style="background:#eee;"><a href="type_list.php?fabu">发布</a></li>
            <li style="background:#ddd;"><a href="seo_paiming.php">看排名</a></li>
        </ul>
        
        
        <table class='from'>
        	<tr class='incs'><th>当前版本</th><td>V2.1</td></tr>
            <tr class='incs'><th>Web服务器</th><td><?php echo 'PHP '.PHP_VERSION;?></td></tr>
            <tr class='incs'><th>服务器时间</th><td><?php date_default_timezone_set("Etc/GMT-8");echo date("Y-m-d H:i:s",time()); ?></td></tr>
            <tr class='incs'><th>登录状态</th><td><?php echo $admins['guanliyuan']; ?><span class="tui">（<a onclick="tui()">退出</a>）</span></td></tr>
            <tr class='incs'><th>管理级别</th><td><?php if($admins['dengji']==1){echo "超级管理员";}else{echo "发布员";} ?></td></tr>
        </table>
        
        <div class="cle70"></div>
    </div>
</div>
<?php 
include('c_foot.php');
?>