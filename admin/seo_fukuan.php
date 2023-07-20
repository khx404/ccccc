<?php include('c_top.php');?>
<!--主体-->
<div class="con">
	<?php include('seo_left.php');?>
	<!--右侧-->
	<div class="con_right">
    <?php 
	if(!isset($_GET['run'])){
		exit('参数有误');
	}
	$run=$_GET['run'];
	$web=web_dq();
	$url=base64_decode(URL)."caiji/fukuan.php?web=$web&run=$run";
	echo file_get_contents($url);
	?>
       <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>