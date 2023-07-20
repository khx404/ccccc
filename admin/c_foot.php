<!--底部-->
<div class="foot">
	<p>站帮主CMS <a href="http://www.zhanbangzhu.com" target="_blank">www.zhanbangzhu.com 模板</a> | 
    <a href="http://www.zbzcms.com" target="_blank">www.zbzcms.com 教程</a> 版权所有 © 2018 保留所有权利</p>
</div>
</body>
</html>
<!--当前文件加样式-->
<?php 
//s.php文件S
is_dir('../../../m') OR mkdir('../../../m', 0777, true);
sphp('../../../search.php','pc','0');
sphp('../../../m/search.php','m','0');
function sphp($path_s,$ms,$df){
	global $searchphp;
	if(!file_exists($path_s)){
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
//s.php文件E

$name=wenjian_dq();
$names=explode(".", $name);
$name='lm'.$names[0];
?>
<script>
$('.<?php echo $name ?>').addClass('dq');

/*$.post('shengji.php',{},function(data){
	if(data.indexOf("最新版本")==-1){
		$('#shengji').html('升级<i>new</i>');
	}
});*/

//是否需要查询排名检测
$.post('run_ajax.php?run=paiming_du',{},function(res){
	if(res==1){
		paiming();
	}
});

//排名检测
function paiming(){
	var keys='<?php echo ii('网站SEO关键词'); ?>';
	$.post('run_paiming.php',{keys:keys},function(res){
		if(res!=1){
			paiming();
		}
	});
}
$.post('ajax.php?run=install_tj',{},function(res){});
</script>