<?php 
include('c_top.php');
$types=$c_sql->digui('type','tid',$tid=0,$result,$spac=0);
$type_tids=array();
foreach($types as $arr_tids){
	$type_tids[]=$arr_tids['tid'];
}
?>
<!--主体-->
<div class="con">
	<?php include('type_left.php');?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
        	<span>
			<?php 
			if(isset($_GET['fabu'])){
				echo "请选择你要发布的栏目点击右侧‘发布’按钮";
			}
			else{
				echo "栏目中心";
			}
			?></span>
        </div>
        
        <table class="list type_list">
        <tr class="top0"><th class="id xia0" style="width:40px;"><a onclick='xia(0)'>+</a></th><th style="width:30px;">ID</th><th style="width:250px;">栏目名称</th>
        <?php 
		if(!isset($_GET['fabu'])){
			echo '<th style="width:70px;" class="id">排序</th>';
		}
		?>
        <th class="runth">操作</th></tr>
        <?php
		//子栏目
		$arr_zi=array();
		if(count($types)>0){
			foreach($types as $arrz){
				if($arrz['tid']!=0){
					$arr_zi[$arrz['tid']][]=$arrz['id'];
				}
			}
		}

		foreach($types as $arr){
			$id=$arr['id'];//id
			$tid=$arr['tid'];//父栏目id
			$lanmumingcheng=$arr['lanmumingcheng'];//栏目名称
			$jibie=$arr['jibie'];//级别
			$paixu=$arr['paixu'];//排序
			$shujumoxing=$arr['shujumoxing'];//对应的数据模型
			
			//该栏目的文档数量
			if(isset($arr_zi[$id])){
				$in=$id.','.implode(',',$arr_zi[$id]);
				$zong_sql="select count(*) from art where tid in($in)";
			}
			else{
				$zong_sql="select count(*) from art where tid=$id";
			}
			$zongs=$c_sql->select($zong_sql);
			$wd=$zongs[0]['count(*)'];
			
			//显示隐藏
			if($arr['run']==110){
				$xianyin="<span id='run_{$id}'><a onclick='xianyin($id,1)'>显示</a></span>";
			}
			else{
				$xianyin="<span id='run_{$id}'><a onclick='xianyin($id,110)'>隐藏</a></span>";
			}
			
			//层级空格符号
			$jiange="";
			for ($x=1; $x<$jibie; $x++) {$jiange.="|---";} 
			$jiange="<span class='jiange'>{$jiange}</span>";
			//run操作
			if($shujumoxing==''){
				$run="<a class='te' href='type.php?id={$id}&dq=3'>内容</a>";
			}
			else{
				$run="<a class='te tbfb' href='art.php?tid={$id}'>发布</a>";
			}
			
			if(!isset($_GET['fabu'])){
				$run.="<a href='type.php?id={$id}&dq=1'>设置</a>";
				$run.="<a href='type.php?id={$id}&dq=2'>SEO</a>";
				$run.="<a href='type.php?id={$id}&dq=3#lmtp'>栏目图片</a>";
				$run.="<a onclick=\"yidong({$id},'将<b>{$lanmumingcheng}</b>移动到',{$jibie})\">移动</a>";
				$run.="<a target='_blank' href='../../../search.php?list={$id}'>预览</a>{$xianyin}";
				$run.="<a onclick=\"deltype({$id},'{$lanmumingcheng}')\">删除</a>";
				$run.="<a onclick=\"add({$id},'给<b>{$lanmumingcheng}</b>加子栏目',{$jibie})\">加子栏目</a>";
				if($shujumoxing==12){
					$run.="<a onclick=\"piliang({$id},'$lanmumingcheng')\">批量上传文章</a>";
				}
			}
			//列表
			$html_list='';
			$html_list.="
<tr class='tr{$id} top$tid'>";
			$html_list.="<td class='xia{$id}'><a onclick='xia({$id})'><font color='#ccc'>-</font></a></td>";
			$html_list.="<td>{$id}</td>";
			$html_list.="<td>{$jiange}<a href='art_list.php?tid={$id}'>{$lanmumingcheng}<span class='wd'>（文档：{$wd}）</span></a></td>";
			if(!isset($_GET['fabu'])){
				$html_list.="<td><input class='paixu px{$id}' onchange='paixu({$id})' value='{$paixu}' /></td>";
			}
			$html_list.="<td class='run'>{$run}</td>";
			$html_list.="</tr>\r\n";
			echo $html_list;
		}
		?>
<script>
$(".type_list tr").each(function(i){
	if(i>0){
		var css=$(this).attr('class');
		var strs= new Array(); //定义一数组
		strs=css.split(" "); //字符分割
		var tr=strs[0].replace('tr',"");
		var top=strs[1].replace('top',"");
		if(top!='0'){
			$(this).hide();
		}
		else if($(".type_list tr").hasClass("top"+tr)){
			$('.xia'+tr).html("<a onclick='xia("+tr+")'>+</a>");
		}
	}
});

function xia(id){
	if(id==0){
		
	}
	
	else if($(".type_list tr").hasClass("top"+id)){
		if($(".top"+id).is(':hidden')){
			$(".top"+id).show();
			$('.xia'+id).html("<a onclick='xia("+id+")'>-</a>");
		}
		else{
			$(".top"+id).hide();
			$('.xia'+id).html("<a onclick='xia("+id+")'>+</a>");
		}
	}
}
<?php 
if(isset($_GET['dq'])){
	echo "xia(".$_GET['dq'].");";	
}
?>
</script>

        
        
        </table>
<?php if(!isset($_GET['fabu'])){?>
        <div class="cle20"></div>
<a class='queren margin_right5' onclick="add(0,'新增栏目')">新建一级栏目</a><span class="ts"></span>
<?php }?>
        <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>
<?php 
$types=$c_sql->select("select id,lanmumingcheng from type order by id asc");
$beixuan='';
foreach($types as $arr){
	if($tid==$arr['id']){
		$beixuan.="<option selected='selected' value='".$arr['id']."'>".$arr['lanmumingcheng']."</option>";
	}
	else{
		$beixuan.="<option value='".$arr['id']."'>".$arr['lanmumingcheng']."</option>";
	}
}
?>
<script>
//栏目移动
function yidong(id,tishi){
	tanbox();//引入提示
	$(".tan_tit_con").html(tishi);//提示内容
	var html="您确定将ID为<b>"+id+"</b>的栏目移动到这里吗？";
	html+="<select class='tid_xz'>";
	html+="<option selected='selected' value='0'>顶级栏目</option>";
	html+="<?php echo $beixuan;?>";
    html+="</select>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick=\"yidong_but('"+id+"')\" class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

function yidong_but(id){
	<?php echo $demo;?>
	var tid_xz=$('.tid_xz').val();
	$.post('run_ajax.php?run=typeyidong',{tid:tid_xz,id:id},function(res){
		if(res==1){
			window.location='type_list.php';
		}
		else{
			tanrun(0);
		}
	})
}

//排序
$('.paixu').mousemove(function(){
	var zhi=$(this).val();
})

//显示隐藏
function xianyin(id,run){
	<?php echo $demo;?>
	var post={id:id,run:run};
	$.post('run_ajax.php?run=type_run',post,function(res){
		if(res==1){
			if(run==110){
				var html="<a onclick='xianyin("+id+",1)'>显示</a>";
			}
			else{
				var html="<a onclick='xianyin("+id+",110)'>隐藏</a>";
			}
			$('#run_'+id).html(html);
		}
	});
}

//排序
function paixu(id){
	<?php echo $demo;?>
	var paixu=$('.px'+id).val();
	var post={id:id,paixu:paixu};
	$.post('run_ajax.php?run=paixu_type',post,function(res){
		if(res==1){
			$('.ts').html("<span class='tishi'><font color='#1aacda'>OK</font></span>");
			$(".ts span").fadeOut(1000);
			window.location='type_list.php';
		}
		else{
			$('.ts').html("<span class='tishi'>ERROR</span>");
			$(".ts span").fadeOut(1000);
		}
	});
}

//新增弹窗
function add(tid,tishi,jibie){
	tanbox();//引入提示
	$(".tan_tit_con").html(tishi);//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>栏目名称</th><td><input id='lanmumingcheng' onchange='jtbaocunlujing()' class='w1' /></td></tr>";
	html+="<tr><th class='w100'>保存路径</th><td><input id='baocunlujing' class='w1' /></td></tr>";
	html+="<tr><th>栏目模板</th><td><select class='w1' id='lanmumoban'>";
	<?php 
	$lanmumobans=array_diff(scandir("../../templets/".ii('电脑模板文件')),array("..","."));//获取模板;
	foreach($lanmumobans as $v){
		if(strstr($v, 'list')){
			echo "html+=\"<option value='{$v}'>{$v}</option>\"\r\n";
		}
	}
	?>
	html+="</select></td></tr>";
	html+="<tr><th>详情模板</th><td><select class='w1' id='xiangqingmoban'>";
	<?php 
	foreach($lanmumobans as $v){
		if(strstr($v, 'art')){
			echo "html+=\"<option value='{$v}'>{$v}</option>\"\r\n";
		}
	}
	?>
	html+="</select></td></tr>";
	html+="<tr><th>数据模型</th><td><select class='w1' id='shujumoxing'>";
	<?php 
	$shujumoxings=explode(";",$shujumoxing);
	$moxings=$c_sql->select("select id,diaoyongmingcheng from moxing where (mid=0 and id!=1 and diaoyongmingcheng!='')");
	foreach($moxings as $arr){
		$id=$arr['id'];
		$diaoyongmingcheng=$arr['diaoyongmingcheng'];
		echo "html+=\"<option value='{$id}'>{$diaoyongmingcheng}</option>\"\r\n";
	}
	echo "html+=\"<option value=''>无</option>\"\r\n";
	?>
	html+="</select></td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick='tijiao("+tid+","+jibie+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//保存路径
function jtbaocunlujing(){
	var lanmumingcheng=$('#lanmumingcheng').val();
	$.post('run_ajax.php?run=jtbaocunlujing',{lanmumingcheng:lanmumingcheng},function(res){
		$('#baocunlujing').val(res);
	});
}

//新增提交
function tijiao(tid,jibie){
	<?php echo $demo;?>
	var lanmumingcheng=$('#lanmumingcheng').val();
	var baocunlujing=$('#baocunlujing').val();
	var lanmumoban=$('#lanmumoban').val();
	var xiangqingmoban=$('#xiangqingmoban').val();
	var shujumoxing=$('#shujumoxing').val();
	if(lanmumingcheng.length<2){
		alert('栏目名称不能为空');
		return false;
	}
	if(lanmumoban==''){
		alert('栏目模板不能为空，没有可选模板请先创建模板');
		return false;
	}
	
	if(shujumoxing!='' && xiangqingmoban==''){
		alert('详情模板不能为空，没有可选模板请先创建模板');
		return false;
	}
	var post={jibie:jibie,tid:tid,lanmumingcheng:lanmumingcheng,baocunlujing:baocunlujing,lanmumoban:lanmumoban,xiangqingmoban:xiangqingmoban,shujumoxing:shujumoxing};
	$.post('run_ajax.php?run=type_add',post,function(data){
		var id=data['id'];
		var tid=data['tid'];
		
		if(tid!=0){
			window.location='type_list.php?dq='+tid;
		}
		else{
			window.location='type_list.php';
		}
		
		
		return false;
		
		var jibie=data['jibie'];
		var weizhi_id=data['weizhi_id'];
		
		//层级空格符号
		var jiange="";
		for (var x=1; x<jibie; x++) {
			jiange+="|---";
		} 
		jiange="<span class='jiange'>"+jiange+"</span>";
			
		//+展开
		var jia="<a class='jia"+id+"' style='color:#CCC' onclick='zi("+id+")'>-</a>";
			
		//tr控制css
		var css='tr'+id;
		//run操作
		if(shujumoxing==''){
			var run="<a class='te' href='art.php?tid="+id+"'>内容</a>";
		}
		else{
			var run="<a class='te' href='art.php?tid="+id+"'>发布</a>";
		}
		run+="<a href='type.php?id="+id+"&dq=1'>设置</a>";
		run+="<a href='type.php?id="+id+"&dq=2'>SEO</a>";
		run+="<a href='type.php?id="+id+"&dq=3'>栏目图片</a>";
		run+="<a  onclick=\"yidong("+id+",'将<b>"+lanmumingcheng+"</b>移动到')\">移动</a>";
		run+="<a target='_blank' href='../../../search.php?list="+id+"'>预览</a>";
		run+="<a onclick='html("+id+")'>生成html</a>";
		
		run+="<span id='run_"+id+"'><a onclick='xianyin("+id+",110)'>隐藏</a></span>";
		run+="<a onclick=\"deltype("+id+",'"+lanmumingcheng+"')\">删除</a>";
		if(jibie==1){
			run+="<a onclick=\"add("+id+",'给<b>"+lanmumingcheng+"</b>加子栏目',"+jibie+")\">加子栏目</a>";
		}
		//列表
		var html_list='';
		html_list+="<tr class='"+css+"'>";
		html_list+="<td>"+id+"</td>";
		html_list+="<td>"+jiange+"<a href='art_list.php?tid="+id+"'>"+lanmumingcheng+"</a></td>";
		html_list+="<td><input value='50' class='paixu px"+id+"' onchange='paixu("+id+")' /></td>";
		html_list+="<td class='run'>"+run+"</td>";
		html_list+="</tr>";
		if(weizhi_id==0){
			$('.list').append(html_list);
		}
		else{
			$('.tr'+weizhi_id).after(html_list);
			$('.jia'+tid).html('+');
			$('.jia'+tid).css("color","#000");
			$(qukong('.tid'+tid)).show();//打开栏目
		}
		tanrun(0);//弹窗消失
	},'json');
}

//删除栏目弹窗
function deltype(id,tishi){
	tanbox();//引入提示
	$(".tan_tit_con").html('温馨提示');//提示内容
	var html="您确定删除<b>"+tishi+"</b>栏目及它的所有子栏目吗？";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick='delbut("+id+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//删除栏目提交按钮
function delbut(id){
	<?php echo $demo;?>
	var post={id:id};
	$.post('run_ajax.php?run=deltype',post,function(data){
		if(data!=0){
			for ( var i = 0; i <data.length; i++){
				var tr=qukong(".tr"+data[i]);
				$(tr).remove();
			}
			tanrun(0);//关闭
		}
		else{
			alert('删除失败');
		}
	},'json');
}
</script>