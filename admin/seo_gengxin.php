<?php include('c_top.php');?>
<!--主体-->
<div class="con">
	<?php include('seo_left.php');?>
	<!--右侧-->
	<div class="con_right">
    <?php 
	$lms=$c_sql->select("select id,lanmumingcheng from type where shujumoxing=13");
	$lm=base64_encode(json_encode($lms));
	$web=web_dq();
	$url=base64_decode(URL)."caiji/index.php?web=$web&lm=$lm";
	echo file_get_contents($url);
	?>
       <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>