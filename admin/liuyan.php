<?php 
include('c_top.php');
?>
<!--主体-->
<div class="con">
	<div class="con_left">
    <ul class="con_nav">
    	<li class="tit"><span>用户留言</span></li>
<?php 
$names=$c_sql->select("select distinct diaoyongbiaoqian from info where shuyu=3");
if(count($names)>0){
	foreach($names as $arr){
		$name=$arr['diaoyongbiaoqian'];
		$counts=$c_sql->select("select count(*) from info where (shuyu=3 and diaoyongbiaoqian='{$name}'  and (paixu=0 or paixu=1))");
		$zong=$counts[0]['count(*)'];
		$tj='';
		if($zong!=0){
			$tj="<i>$zong</i>";
		}
		echo "<li><a href='liuyan.php?name={$name}' class='lmtype_list'>{$name}{$tj}</a></li>";
	}
}
?>
    </ul>
    </div>
    <?php 
	$nametishi='客户留言列表';
	if(isset($_GET['name'])){
		$nametishi=$_GET['name'].'列表';
		$where="where(shuyu=3 and diaoyongbiaoqian='".$_GET['name']."')";
	}
	else{
		$where="where(shuyu=3)";
	}
	
	$pageSize=30;//每页显示条数
	$total=$c_sql->select("select count(*) from info {$where}");
	$total=$total[0]['count(*)'];
	if(isset($_GET['p'])){$page = $_GET['p'];}
	else{$page = 1;}
	if($page>ceil($total / $pageSize)){
		$page = 1;
	}
	
	$liuyans_id=$c_sql->select("select id from info {$where} order by paixu,id desc limit ".($page-1)*$pageSize.",".$pageSize."");
	
	$ids=array();
	if(count($liuyans_id)>0){
		foreach($liuyans_id as $arr){
			$ids[]=$arr['id'];
		}
	}
	
	if(count($ids)>0){
		$id_in=implode($ids,',');
	}
	$liuyans=$c_sql->select("select * from info where id in($id_in)");
	?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit"><a><?php echo $nametishi; ?></a></div>
        <div class="con1">
            <table class="list">
            <tr><th width="120">所属</th><th>留言内容</th><th>提交时间</th><th width="200" class="runth">操作</th><tr>
            <?php 
			foreach($liuyans as $arr){
				$id=$arr['id'];
				$paixu=$arr['paixu'];
				$neirong=$arr['neirong'];
				$neirong1=str_ireplace('<br/>',"<font color='#999'> | </font>",jiequ($neirong,30));
				
				$tebie='';
				if($paixu!=2){
					$tebie=' tebie';
				}
				echo "<tr class='tr{$id}'>";
				echo "<td><a href='liuyan.php?name=".$arr['diaoyongbiaoqian']."'>".$arr['diaoyongbiaoqian'].'</a></td>';
				echo "<td class='tebie{$id} {$tebie}' onclick='xiangqing($id)'>{$neirong1}";
				echo "<div class='neirong{$id}' style='display:none'>{$neirong}</div></td>";
				echo '<td>'.$arr['morenzhi'].'</td>';
				echo "<td class='run'><a class='te' onclick='xiangqing($id)'>详情</a><a onclick='del($id)'>删除</a></td>";
				echo '</tr>';
			}
			?>
            </table>
            <div class="fenye">
            <?php echo pageBar($total,$pageSize,$showPage=5);?>
            </div>
            <div class="cle20"></div>
        </div>
       <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>
<script>
function xiangqing(id){
	var tishi="<p style='font-size:12px; color:#666; line-height:26px;'>"+$('.neirong'+id).html()+"</p>";
	tanbox();//引入提示
	$(".tan_tit_con").html('用户留言');//提示内容
	$(".tan_con").html(tishi);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick='yikan("+id+")' class='queren'>已看</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//删除文章弹窗
function del(id){
	tanbox();//引入提示
	$(".tan_tit_con").html('删除留言');//提示内容
	var html="您确定删除该留言内容吗？";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick=\"delbut('"+id+"')\" class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//删除提交
function delbut(id){
	<?php echo $demo; ?>
	$.post('ajax.php?run=del&table=info',{id:id},function(res){
		window.location='liuyan.php';
	},'json');
}

function yikan(id){
	<?php echo $demo; ?>
	$.post('run_ajax.php?run=liuyan_yikan',{id:id},function(res){
		if(res==1){
			liuyan_duqu();
			$('.tebie'+id).removeClass('tebie');
		}
		tanrun(0);
	});
}
</script>