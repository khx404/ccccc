<?php 
include('c_top.php');
$where='';
if(isset($_GET['tid'])){
	$tid=$_GET['tid'];
	$tid_zis=$c_sql->select("select id from type where tid={$tid}");
	
	if(count($tid_zis)>0){
		foreach($tid_zis as $arr_type_zis){
			$tzis[]=$arr_type_zis['id'];
		}
		$where="where art.tid in({$tid},".implode(',',$tzis).')';
	}
	else{
		$where="where art.tid=".$_GET['tid'];
	}
	
	$types=$c_sql->select("select lanmumingcheng,id,shujumoxing from type where id=".$_GET['tid']);
	$tishi=$types[0]['lanmumingcheng']."（<a target='_blank' href='type.php?id=".$types[0]['id']."'>编辑</a>）";
}
else{
	$tishi='内容列表'."（<a href='type_list.php?fabu'>发布</a>）";
}

//分页信息
$pageSize=20;//每页显示条数
$total=$c_sql->select("select count(*) from art {$where}");
$total=$total[0]['count(*)'];
if($where==''){
	$where="where art.tid=type.id";
}
else{
	$where.=" and type.id=".$_GET['tid'];
}
if($dengji==2){
	$where.=" and zuozhe='{$guanliyuan}'";
}


if(isset($_GET['p'])){$page = $_GET['p'];}
else{$page = 1;}
if($page>ceil($total / $pageSize)){
	$page = 1;
}

//关联文章
$aglid='';
if(isset($_GET['glid'])){
	$glid=$_GET['glid'];//关联文章的id
	$glid_sql=" and aid='$glid'";
	$where.=$glid_sql;
	$aglid="&glid=$glid";
}

$arts=$c_sql->select("select biaoti,art.id,art.tid,art.zuozhe,art.suoluetu,art.paixu,lanmumingcheng,fabushijian,tuijian from art,type {$where} order by paixu asc,id desc limit ".($page-1)*$pageSize.",".$pageSize."");

//关联内容
$type_guanlians=array();
$type_guanlian=$c_sql->select("select id from type where lanmumingcheng='关联'");
if(isset($type_guanlian[0]['id'])){
	$gl_id=$type_guanlian[0]['id'];
	$type_guanlians=$c_sql->select("select id,lanmumingcheng from type where tid={$gl_id}");
}
?>
<!--主体-->
<div class="con">
	<?php include('type_left.php');?>
	<div class="con_right">
    	<div class="tit">
		<?php 
		echo $tishi;
		if(!isset($_GET['tid'])){
			echo "<a class='wj1 shangchuan tianjia right_but' href='type_list.php?fabu{$aglid}'>发布</a>";
		}
		
		else if($types[0]['shujumoxing']==''){
			echo "<a class='wj1 shangchuan tianjia right_but' href='type.php?id=".$types[0]['id']."&dq=3'>栏目内容</a>";
		}
		else{
			echo "<a class='wj1 shangchuan tianjia right_but' href='art.php?tid=".$types[0]['id']."$aglid'>发布</a>";
		}		
		?>
        </div>
        
        <table class="list">
        <tr><th class="id"><input class='quanxuan' name="quanxuan" onchange="quanxuan()" type="checkbox" /></th>
        <th width='30'>ID</th>
        <th>标题</th>
        <?php 
		if(count($type_guanlians)>0){
			echo "<th>关联内容</th>";
		}
		?>
        
        <th>排序</th>
        <th style="width:130px">所属栏目</th>
        <th style="width:120px">发布时间</th>
        <th style="width:60px">推荐</th>
        
        <th<?php echo $quanxian; ?> width="200" class="runth">操作</th>
        <tr>
        <?php
		foreach($arts as $art){
			$id=$art['id'];
			$tid=$art['tid'];
			$biaoti=$art['biaoti'];
			$biaoti=jiequ($biaoti,30);
			$tuijian=$art['tuijian'];
			$lanmumingcheng=$art['lanmumingcheng'];
			$suoluetu=$art['suoluetu'];
			$paixu=$art['paixu'];
			$zuozhe=$art['zuozhe'];
			$fabushijian=$art['fabushijian'];
			$fabushijian=date("Y-m-d H:i",$fabushijian);
			if(strstr($fabushijian,'1970')){$fabushijian="<span style='font-size:12px; color:#F00'>待发布</span>";}
			if($suoluetu!=''){$suoluetu="<a href='{$suoluetu}' target='_blank' class='spantu'>[图]</a>";}
			else{$suoluetu='';}
			
			echo "<tr class='tr{$id}'><td><input class='xuanzhong' value='{$id}' name='xuanzhong' type='checkbox' /></td>";
			echo "<td>{$id}</td>";
			if(isset($_GET['aid'])){
				if($_GET['aid']==$id){echo "<td><a style='color:#F00' href='art.php?aid={$id}'>{$biaoti}</a>{$suoluetu}</td>";}
				else{echo "<td><a href='art.php?aid={$id}'>{$biaoti}</a>{$suoluetu}</td>";}
			}
			else{
				echo "<td><a href='art.php?aid={$id}'>{$biaoti}</a>{$suoluetu}</td>";
			}
			
			
			if(count($type_guanlians)>0){
				echo "<td><select class='guanlian'><option>选择前往</option>";
				foreach($type_guanlians as $arr_gl){
					echo "<option value='{$id}_".$arr_gl['id']."'>".$arr_gl['lanmumingcheng']."</option>";
				}
				echo "</select></td>";
			}
			
			echo "<td><input class='paixu px{$id}' onchange='paixu({$id})' value='{$paixu}' /></td>";
			echo "<td><a target='_blank' href='art_list.php?tid={$tid}'>{$lanmumingcheng}</a></td>";
			echo "<td>{$fabushijian}</td>";
			echo "<td><select data-id='{$id}' class='shuxing'>";
			$tj0='';
			$tj1='';
			$tj2='';
			$tj3='';
			$tj4='';
			$tj5='';
			if($tuijian==''){$tj0=" selected='selected'";}
			if($tuijian=='一级'){$tj1=" selected='selected'";}
			if($tuijian=='二级'){$tj2=" selected='selected'";}
			if($tuijian=='三级'){$tj3=" selected='selected'";}
			if($tuijian=='四级'){$tj4=" selected='selected'";}
			if($tuijian=='五级'){$tj5=" selected='selected'";}
			echo "<option value=''{$tj0}>无</option><option value='一级'{$tj1}>一级</option><option value='二级'{$tj2}>二级</option><option value='三级'{$tj3}>三级</option><option value='四级'{$tj4}>四级</option><option value='五级'{$tj5}>五级</option>";
			echo "</select></td>";
			
			echo "<td{$quanxian} class='run'>";
			echo "<a target='_blank' href='../../../search.php?art=$id'>预览</a><a onclick='del($id)'>删除</a></td></tr>";
		}
		?>
        </table>
        
        <div class="fenye">
        <?php 
		if(!isset($_GET['tid'])){
			echo "<a class='dq' href='type_list.php?fabu{$aglid}'>发布</a>";
		}
		
		else if($types[0]['shujumoxing']==''){
			echo "<a class='dq' href='type.php?id=".$types[0]['id']."&dq=3'>栏目内容</a>";
		}
		else{
			echo "<a class='dq' href='art.php?tid=".$types[0]['id']."{$aglid}'>发布</a>";
		}
		?>
        <a<?php echo $quanxian; ?> onclick="piliang_del()">批量删除</a>
        <a<?php echo $quanxian; ?> onclick="piliang_zhuanyi()">批量转移栏目</a>
        <?php echo pageBar($total,$pageSize,$showPage=5);echo '<span>总共'.$total.'条记录</span>';?>
        </div>
        <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>
<?php 
$result=array();
$types=$c_sql->digui('type','tid',0,$result,$spac=0);
$beixuan='';
foreach($types as $arr){
	$kongge='';
	if($arr['jibie']==2){
		$kongge="|--";
	}
	
	if($tid==$arr['id']){
		$beixuan.="<option selected='selected' value='".$arr['id']."'>".$kongge.$arr['lanmumingcheng']."</option>";
	}
	else{
		$beixuan.="<option value='".$arr['id']."'>".$kongge.$arr['lanmumingcheng']."</option>";
	}
}
?>
<script>
$('.guanlian').change(function(){
	var zhi=$(this).val();
	if(zhi==='选择前往'){
		return false;
	}
	var zhis= new Array(); //定义一数组
	zhis=zhi.split("_"); //字符分割
	
	window.open("art_list.php?glid="+zhis[0]+"&tid="+zhis[1],"_blank");      
})

//全选
function quanxuan(){
	if($('.quanxuan').prop("checked")){
		$('.xuanzhong').prop("checked", true);
	}
	else{
		$('.xuanzhong').prop("checked", false);
	}
}

//批量删除
function piliang_zhuanyi(){
	var yixuan=duoxuan('xuanzhong');
	tanbox();//引入提示
	$(".tan_tit_con").html('批量转移');//提示内容
	var html="您确定转移ID为<b>"+yixuan+"</b>的内容到如下栏目吗？";
	html+="<select class='tid_xz'>";
	html+="<?php echo $beixuan;?>";
    html+="</select>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick=\"zhuanyibut('"+yixuan+"')\" class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

function zhuanyibut(yixuan){
	<?php echo $demo;?>
	var tid_xz=$('.tid_xz').val();
	var yixuan=duoxuan('xuanzhong');
	$.post('run_ajax.php?run=artzhuanyi',{tid:tid_xz,yixuan:yixuan},function(res){
		$.each(res,function(i,v){
			$('.tr'+v).remove();
		});
		tanrun(0);//弹窗消失
	},'json')
}

//排序
function paixu(id){
	<?php echo $demo;?>
	var paixu=$('.px'+id).val();
	var post={id:id,paixu:paixu};
	$.post('ajax.php?run=addedit&table=art',post,function(res){
		if(res==1){
			$('.ts').html("<span class='tishi'><font color='#1aacda'>OK</font></span>");
			$(".ts span").fadeOut(1000);
			var tid='';
			<?php 
			if(isset($_GET['tid'])){
				echo "var tid='?tid=".$_GET['tid']."';\r\n";
			}
			else{
				echo "var tid='';";
			}
			?>
			window.location='art_list.php'+tid;
		}
		else{
			$('.ts').html("<span class='tishi'>ERROR</span>");
			$(".ts span").fadeOut(1000);
		}
	});
}

//批量删除
function piliang_del(){
	<?php echo $demo;?>
	var yixuan=duoxuan('xuanzhong');
	del(yixuan);
}

//删除文章弹窗
function del(id){
	tanbox();//引入提示
	$(".tan_tit_con").html('批量删除');//提示内容
	var html="您确定删除ID为<b>"+id+"</b>的内容吗？";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick=\"delbut('"+id+"')\" class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//删除提交
function delbut(id){
	<?php echo $demo;?>
	$.post('run_ajax.php?run=artdel',{id:id},function(res){
		$.each(res,function(i,v){
			$('.tr'+v).remove();
		});
		tanrun(0);//弹窗消失
	},'json');
}

//属性
$('.shuxing').change(function(){
	<?php echo $demo;?>
	var id=$(this).data('id');
	var tuijian=$(this).val();
	$.post('run_ajax.php?run=shuxing',{id:id,tuijian:tuijian},function(res){});
});
</script>