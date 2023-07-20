<?php 
include('c_top.php');
?>
<!--主体-->
<div class="con">
	<?php
	include('seo_left.php');
	$www=$_SERVER['SERVER_NAME'];
	$www=str_replace(array('www.','m.'),'',$www);
	?>
	<div class="con_right">
    	<div class="tit"><b><?php echo $www; ?></b>排名实时监控</div>
        <table class="list">
        <tr><th width="30%">关键词</th><th width="120">百度PC排名</th><th width="120">百度手机排名</th><th width="120">查询时间</th><tr>
        <?php 
		$paiming=$c_sql->select("select neirong from info where diaoyongbiaoqian='百度排名'");
		$paiming=str_ireplace('&;','&',$paiming[0]['neirong']);
		$paiming_ms=explode('&',$paiming);
		$paiming_pcs=explode(';',$paiming_ms[0]);
		$paiming_waps=explode(';',$paiming_ms[1]);
		foreach($paiming_pcs as $k=>$pc){
			$pcs=explode('|',$pc);
			$key=$pcs[0];
			$waps=explode('|',$paiming_waps[$k]);
			$pm_pc=$pcs[1];
			if($pm_pc!='>50'){$pm_pc="<font style='font-weight:bold' color='#1aacda'>{$pm_pc}</font>";}
			$pm_wap=$waps[1];
			if($pm_wap!='>50'){$pm_wap="<font style='font-weight:bold' color='#1aacda'>{$pm_wap}</font>";}
			
			$pm_pc="<a rel='nofollow' target='_blank' href='https://www.baidu.com/baidu?wd={$key}'>{$pm_pc}</a>";
			$pm_wap="<a rel='nofollow' target='_blank' href='https://m.baidu.com/s?word={$key}'>{$pm_wap}</a>";
			
			echo "<tr><td>{$key}</td>";
			echo "<td>".$pm_pc."</td>";
			echo "<td>".$pm_wap."</td>";
			echo "<td>".date("Y-m-d H:i",$pcs[2])."</td><tr>";
		}
		?>
        </table>
        <div class="fenye">
        <a class="dq" rel="nofollow" target="_blank" href="https://www.baidu.com/baidu?wd=site:<?php echo $www; ?>">查看收录情况</a>
        </div>
        
        <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>