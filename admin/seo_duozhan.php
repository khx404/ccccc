<?php 
include('c_top.php');
$liandongs=$c_sql->digui('liandong','lid',$lid=1,$result,$spac=0);
$liandong_lids=array();
foreach($liandongs as $arr_ldids){
	$liandong_lids[]=$arr_ldids['lid'];
}
?>
<!--主体-->
<div class="con">
	<?php include('seo_left.php');?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
        	<span>多地方站点设置</span>
        </div>
        
        <table class="list">
        <tr><th class="xiaji" onclick="zi(0)">+</th><th width="150">名称</th><th width="150">保存文件夹</th><th width="150">是否启动</th><th class="id">排序</th><th class="runth">操作</th><tr>
        <?php 
		foreach($liandongs as $arr){
			$id=$arr['id'];//id
			$lid=$arr['lid'];//父栏目id
			$name=$arr['name'];//栏目名称
			$pinyin=$arr['pinyin'];//保存文件夹
			
			//启动
			$qidong=$arr['run'];
			if($qidong==''){$qidong=2;}
			
			$jibie=$arr['jibie'];//级别
			$paixu=$arr['paixu'];//排序
			
			//层级空格符号
			$jiange="";
			for ($x=1; $x<$jibie; $x++) {$jiange.="|--";} 
			$jiange="<span class='jiange'>{$jiange}</span>";
			
			//+展开
			if(in_array($id,$liandong_lids)){
				$jia="<a class='jia{$id}' onclick='zi({$id})'>+</a>";	
			}
			else{
				$jia="<a class='jia{$id}' style='color:#CCC' onclick='zi({$id})'>-</a>";
			}
			
			//tr控制css
			$css='';
			if($lid==1){
				$css.='fu';
			}
			else{
				$css.='zi';
			}
			$css.=' lid'.$lid;
			$css.=' id'.$id;
			$css.=' tr'.$id;
			
			//run操作
			if($jibie>=4){
				$run="<a style='color:#CCC'>加子类</a>";
			}
			else{
				$run="<a class='te' onclick=\"add_zi({$id},'给<b>{$name}</b>加子类',{$jibie})\">加子类</a>";
			}
			if($id==1){
				$run.="<a style='color:#CCC'>编辑</a>";
				$run.="<a style='color:#CCC'>删除</a>";
			}
			else{
				$run.="<a onclick=\"edit({$id},'{$name}','{$pinyin}')\">编辑</a>";
				$run.="<a onclick=\"del({$id},'{$name}')\">删除</a>";
			}

			//列表
			$html_list='';
			$html_list.="<tr class='{$css}'>";
			$html_list.="<td>{$jia}</td>";
			$html_list.="<td>{$jiange}{$name}</td>";
			$html_list.="<td>{$pinyin}</td>";
			if($jibie>1){$html_list.="<td></td>";}
			else{
				$html_list.="<td>";
				if($qidong==1){
					$html_list.="<select style='color:#1aacda' class='run' data-id='{$id}'>";
					$html_list.="<option value='1' selected='selected'>是</option><option value='2'>否</option>";
				}
				else{
					$html_list.="<select class='run' data-id='{$id}'>";
					$html_list.="<option value='2' selected='selected'>否</option><option value='1'>是</option>";
				}
				$html_list.="</select></td>";
			}
			$html_list.="<td><input onchange='paixu({$id})' class='paixu px{$id}' value='{$paixu}' /></td>";
			$html_list.="<td class='run'>{$run}</td>";
			$html_list.="</tr>";
			echo $html_list;
		}
		?>
        </table>
        <div class="cle20"></div>
        <span class="addxinzeng"></span>
        <span class="ts"></span>
        <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>
<script>
addxinzeng();
function addxinzeng(){
	$.post('ajax.php?run=addxinzeng&web=<?php echo web_dq();?>',{},function(res){
		if(res==0){
			var but="<a class='queren' href='seo_fukuan.php?run=duozhan'>付费使用</a>";
		}
		else{
			var but="<a class='queren' onclick=\"add('新增联动项目名称')\">新增地方站</a>";
		}
		$('.addxinzeng').html(but);
	});
}

//导航点击展示
function zi(id){
	if(id==0){var cla='.zi';}
	else{
		var cla='.lid'+id;
	}
	var lizi=$(cla).attr("class");
	if($(cla).is(':hidden')){
		$(cla).show();
	}
	else{
		if(cla=='.zi'){$(cla).hide();}
		else{
			$.post('run_ajax.php?run=ld_zi_id&id='+id,{},function(res){
				$.each(res, function(i) {
					var id=res[i];
					$('.tr'+id).hide();
				});
			},'json');
		}
	}
}

//是否启动
$('.run').change(function(){
	<?php echo $demo;?>
	var id=$(this).data('id');
	var run=$(this).val();
	$.post('ajax.php?run=addedit&table=liandong',{id:id,run:run},function(res){
		window.location='seo_duozhan.php';
	});
});

//新增联动项目名称
function add(tishi){
	tanbox();//引入提示
	$(".tan_tit_con").html(tishi);//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>城市名</th><td><input id='name' class='w1' /></td></tr>";
	html+="<tr><th class='w100'>保存文件夹</th><td><input id='pinyin' class='w1' /></td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_left5 quxiao'>取消</a><a onclick='add_but()' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//新增联动项目名称提交
function add_but(){
	<?php echo $demo;?>
	var name=$('#name').val();
	var pinyin=$('#pinyin').val();
	var post={name:name,pinyin:pinyin,lid:1,run:1,paixu:50}
	$.post('ajax.php?run=addedit&table=liandong',post,function(data){
		window.location='seo_duozhan.php';
	});
}

//批量加子类
function add_zi(id,tishi,jibie){
	tanbox();//引入提示
	$(".tan_tit_con").html(tishi);//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>名称一行一个</th><td><textarea class='w1' id='names' style='height:300px'></textarea></td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_left5 quxiao'>取消</a><a onclick='add_zi_but("+id+","+jibie+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//批量加子类提交
function add_zi_but(id,jibie){
	<?php echo $demo;?>
	jibie=jibie*1+1;
	var names=$('#names').val();
	var post={id:id,names:names}
	$.post('run_ajax.php?run=liandong_add_zi_but',post,function(data){
		window.location='seo_duozhan.php';
	},'json');
}

//删除
function del(id,name){
	tanbox();//引入提示
	$(".tan_tit_con").html('温馨提示');//提示内容
	var html="您确定删除<b>"+name+"</b>联动项及它的所有子项吗？";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick='delbut("+id+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//删除提交按钮
function delbut(id){
	<?php echo $demo;?>
	var post={id:id};
	$.post('run_ajax.php?run=delliandong',post,function(data){
		if(data!=0){
			for ( var i = 0; i <data.length; i++){
				$(".tr"+data[i]).remove();
			}
			tanrun(0);//关闭
		}
		else{
			alert('删除失败');
		}
	},'json');
}

//排序
function paixu(id){
	<?php echo $demo;?>
	var paixu=$('.px'+id).val();
	var post={id:id,paixu:paixu};
	$.post('run_ajax.php?run=paixu_liandong',post,function(res){
		if(res==1){
			$('.ts').html("<span class='tishi'><font color='#1aacda'>OK</font></span>");
			$(".ts span").fadeOut(1000);
		}
		else{
			$('.ts').html("<span class='tishi'>ERROR</span>");
			$(".ts span").fadeOut(1000);
		}
	});
}

//编辑
function edit(id,name,pinyin){
	tanbox();//引入提示
	$(".tan_tit_con").html('项目【'+name+'】需要修改吗？');//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>地方名称</th><td><input id='name' value='"+name+"' class='w1' /></td></tr>";
	html+="<tr><th class='w100'>保存文件夹</th><td><input id='pinyin' value='"+pinyin+"' class='w1' /></td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_left5 quxiao'>取消</a><a onclick='edit_but("+id+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//编辑提交
function edit_but(id){
	<?php echo $demo;?>
	var name=$('#name').val();
	var pinyin=$('#pinyin').val();
	$.post('ajax.php?run=addedit&table=liandong',{id:id,name:name,pinyin:pinyin},function(res){
		window.location='seo_duozhan.php';
	});
}
</script>