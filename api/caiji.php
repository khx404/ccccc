<style type="text/css">
*{ padding:0; margin:0; font-size:14px; color:#333}
a{ color:#1aacda}
</style>
<?php 
include('../cms/include/class_sql.php');
if(!isset($_GET['run'])){
	exit('参数有误');
}
$run=$_GET['run'];
//采集并自动更新
if($run=='caiji'){
	$json=file_get_contents(base64_decode(URL)."caiji/api.php?web=".$web=web_dq()."&run=caiji&id=".$_GET['id']);
	if($json=='error'){exit('error');}
	else{
		$arr=json_decode($json, true);
		if($arr['biaoti']=='' || $arr['neirong']==''){
			exit('采集节点没有您要更新的内容！');
		}
		
		//是否插入关键词和图片
		$charus=$c_sql->select("select * from info where shuyu='wyc'");
		$cr=array();
		foreach($charus as $charus_arr){
			if($charus_arr['diaoyongbiaoqian']=='开启伪原创'){$cr['kaiqi']=$charus_arr['neirong'];}
			if($charus_arr['diaoyongbiaoqian']=='随机插入图片'){$cr['chatu']=$charus_arr['neirong'];}
			if($charus_arr['diaoyongbiaoqian']=='随机图片库'){$cr['tuku']=$charus_arr['neirong'];}
			if($charus_arr['diaoyongbiaoqian']=='随机插入'){$cr['ciku']=$charus_arr['neirong'];}
		}
		
		if($cr['kaiqi']=='是'){
			$sj_ci=explode("\r\n",$cr['ciku']);
			$sj_ci=$sj_ci[rand(0,count($sj_ci)-1)];
			$sj_cis=explode('|',$sj_ci);
			$sj_ci=$sj_cis[0];
			$sj_a=$sj_cis[1];
			$biaoti[]=$sj_ci.$arr['biaoti'];
			$biaoti[]=$arr['biaoti'].$sj_ci;
			$arr['biaoti']=$biaoti[rand(0,1)];//插入标题
			//插入内容
			$neirong=$arr['neirong'];
			$neirongs=explode('。',$neirong);
			$sj=rand(0,3);
			foreach($neirongs as $k=>$nr){
				if($k<=$sj){
					$neirongs[$k]=$nr."<a href='{$sj_a}' target='_blank'>{$sj_ci}</a>";
				}
			}
			$arr['neirong']=implode('。',$neirongs);
		}
		
		$arr['paixu']=50;
		$arr['fabudao']=0;
		$arr['fabushijian']=time();
		$id=$c_sql->insert('art',$arr);
		if($id<=0){
			exit('无法采集');
		}
	}
}

//审核并自动更新
else if($run=='shenhe'){
	$tid=$_GET['lanmu'];
	$up_arr=array('fabushijian',time());
	$ids=$c_sql->select("select id from art where (fabushijian=1 and tid={$tid}) limit 1");
	if(isset($ids[0]['id'])){$id=$ids[0]['id'];}
	else{exit('待审核文章库没有文章了！');}
	$up_arr=array('fabushijian'=>time());
	$up=$c_sql->update('art',$up_arr,"id={$id}");
	$up=1;
	if($up!=1){
		exit('审核系统出错');
	}
}
else{
	exit("run参数有误");
}
?>
<span class="ts">努力运行中，请勿关闭</span>
<script src="../common/js/jquery.min.js"></script>
<script>
make_dan('make_dan&art=<?php echo $id; ?>');
function make_dan(data){
	$.post('../../search.php?'+data,{},function(res){
		if(res.indexOf("make_dan") != -1){
			$('.ts').html('正在努力生成中，请勿关闭哦...');
			make_dan(res);
		}
		else{
			$('.ts').html("采集并生成html静态完成，<a href='../../<?php echo $id; ?>.html' target='_blank'>浏览</a>");
			return false;
		}
	})
}
</script>