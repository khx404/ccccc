<?php 
include('c_top.php');
$moxings=$c_sql->digui('moxing','mid',$tid=0,$result,$spac=0);
?>
<!--主体-->
<div class="con">
	<?php include('info_left.php');?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
        	<span>数据模型</span>
        </div>
        <table class="list">
        <tr><th class="xiaji" onclick="zi(0)">+</th><th>模型名称即字段</th><th class="id">排序</th><th class="runth">操作</th><tr>
        <?php 
		foreach($moxings as $arr){
			$id=$arr['id'];//id
			$diaoyongmingcheng=$arr['diaoyongmingcheng'];//模型名称即调用名称
			$morenzhi=$arr['morenzhi'];//默认值
			$mid=$arr['mid'];//父级id
			$jibie=$arr['jibie'];//级别
			$paixu=$arr['paixu'];//排序
			$leixing=$arr['leixing'];//数据类型
			$jiange="";
			if($mid>0){
				$jiange="<span class='jiange'>|--</span>";
			}
			if($jibie==1 && $id!=1){				
				echo "<tr class='fu tr{$id}'><td onclick='zi({$id})'>+</td><td>{$jiange}{$diaoyongmingcheng}</td><td><input value='{$paixu}' class='paixu px{$id}' onchange='paixu({$id})' /></td><td class='run'>";
				
				if($id==12){
					echo "<a style='color:#CCC'>新增字段</a>";
					echo "<a style='color:#CCC'>删除</a></td></tr>";
				}
				else{
					echo "<a class='te' onclick=\"add_zd({$id},0,'给模型<b>{$diaoyongmingcheng}</b>新增字段','','','')\">新增字段</a>";
					echo "<a onclick=\"shan({$id},'{$diaoyongmingcheng}')\">删除</a></td></tr>";
				}
				
				
				//公共字段S
				echo "<tr class='zi t{$id} tr{$id}'><td>-</td>
				<td><span class='jiange'>|--</span>标题</td>
				<td><input readonly='readonly' value='1' class='paixu px2' />
				</td><td class='run'>
				<a style='color:#CCC'>编辑</a>
				<a style='color:#CCC'>删除</a></td></tr>";
				
				echo "<tr class='zi t{$id} tr{$id}'><td>-</td>
				<td><span class='jiange'>|--</span>缩略图</td>
				<td><input readonly='readonly' value='1' class='paixu px2' />
				</td><td class='run'>
				<a style='color:#CCC'>编辑</a>
				<a style='color:#CCC'>删除</a></td></tr>";
				//公共字段E
				
			}
			else{
				echo "<tr class='zi t{$mid} tr{$id}'><td>-</td><td>{$jiange}{$diaoyongmingcheng}</td><td><input value='{$paixu}' class='paixu px{$id}' onchange='paixu({$id})' /></td><td class='run'>";
				if($id==1 || $mid==1 || $id==13){
					echo "<a style='color:#CCC'>新增字段</a><a style='color:#CCC'>编辑</a><a style='color:#CCC'>删除</a>";
				}
				else{
					echo "<a class='te' onclick=\"edit({$id},'{$diaoyongmingcheng}')\">编辑</a>
					<a onclick=\"shan({$id},'{$diaoyongmingcheng}')\">删除</a>";
				}
				echo "</td></tr>";
			}
		}
		?>
        </table>
        <div class="cle20"></div>
<a class='queren' onclick="add_mx()">新增模型</a>
<span class="ts"></span>
        <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>
<script>
//导航点击展示
function zi(id){
	if(id==0){var cla='.zi';}
	else{var cla='.t'+id;}
	if($(cla).is(':hidden')){$(cla).show();}
	else{$(cla).hide();}
}

//新增模型弹窗
function add_mx(){
	tanbox();//引入提示
	$(".tan_tit_con").html('新增模型');//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>模型名称</th><td><input id='diaoyongmingcheng' class='w1' /></td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right5 quxiao'>取消</a><a onclick='add_mx_tj()' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//新增模型提交
function add_mx_tj(){
	<?php echo $demo; ?>
	var diaoyongmingcheng=$('#diaoyongmingcheng').val();
	var post={diaoyongmingcheng:diaoyongmingcheng};
	if(diaoyongmingcheng.length<2){
		return false;
	}
	$.post('run_ajax.php?run=moxing_add',post,function(data){
		data=qukong(data);
		if(data!=0){
			var html="<tr class='fu tr"+data+"'><td onclick='zi("+data+")'>+</td><td>"+diaoyongmingcheng+"</td><td><input value='50' /></td><td class='run'><a class='te' onclick=\"add_zd("+data+",0,'给模型<b>"+diaoyongmingcheng+"</b>新增字段','','','')\">新增字段</a><a onclick=\"shan("+data+",'"+diaoyongmingcheng+"')\">删除</a></td></tr>";
			$('.list').append(html);
			tanrun(0);//弹窗消失
		}
	})
}

//新增及编辑字段弹窗
function add_zd(id,mid,tishi,diaoyongmingcheng,morenzhi,leixing){
	tanbox();//引入提示
	$(".tan_tit_con").html(tishi+'（<span>注：选择型默认值用<b>;</b>隔开</span>）');//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>调用名称</th><td><input id='diaoyongmingcheng' class='w1' value='"+diaoyongmingcheng+"' /></td></tr>";
	html+="<tr><th class='w100'>默认值</th><td><textarea id='morenzhi' class='w1'>"+morenzhi+"</textarea></td></tr>";
	html+="<tr><th class='w100'>数据类型</th><td><div class='w1'>";
	html+="<label><input type='radio' name='leixing' selected='selected' class='input' value='input' />单行文本</label>";
	html+="<label><input type='radio' name='leixing' class='textarea' value='textarea' />多行文本</label>";
	html+="<label><input type='radio' name='leixing' class='option' value='option' />下拉框</label>";
	html+="<label><input type='radio' name='leixing' class='radio' value='radio' />单选框</label>";
	html+="<label><input type='radio' name='leixing' class='checkbox' value='checkbox' />多选框</label>";
	html+="<label><input type='radio' name='leixing' class='body' value='body' />编辑器</label>";
	html+="<label><input type='radio' name='leixing' class='file' value='file' />上传</label>";
	html+="</div></td></tr>";
	html+="<tr><th class='w100'>必填项</th><td><div class='w1'>";
	html+="<label><input type='radio' name='bitian' checked='checked' value='0' />否</label>";
	html+="<label><input type='radio' name='bitian' value='1' />是</label>";
	html+="</div></td></tr>";
	
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	
	if(mid!=0){
		$("#diaoyongmingcheng").attr("disabled", true);
		$('input[name="leixing"]').attr("disabled", true);	
	}
	
	if(leixing!=''){
		var lx=qukong("."+leixing);
		$(lx).attr("checked","checked");
	}
	else{
		$('.input').attr("checked","checked");
	}
	
	var but="<span class='tcts'></span><a onclick='tanrun(0)' class='margin_right5 quxiao'>取消</a><a onclick='add_zd_tj("+id+","+mid+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//新增字段提交
function add_zd_tj(id,mid){
	<?php echo $demo; ?>
	var diaoyongmingcheng=$('#diaoyongmingcheng').val();
	var morenzhi=$('#morenzhi').val();
	var leixing=$('input[name="leixing"]:checked').val();
	var bitian=$('input[name="bitian"]:checked').val();
	if(diaoyongmingcheng.length>1){
		//类型为联动时候特殊处理S
		if(leixing=='liandong'){
			$.post('run_ajax.php?run=moxing_liandong',{name:diaoyongmingcheng},function(res){
				if(res==0){
					$('.tcts').html("<span class='tishi'>调用名称【"+diaoyongmingcheng+"】在联动菜单中找不到，请设置</span>");
					return false;
				}
				else{
					morenzhi=res;
					var post={id:id,mid:mid,diaoyongmingcheng:diaoyongmingcheng,morenzhi:morenzhi,leixing:leixing,bitian:bitian};
					$.post('run_ajax.php?run=moxing_ziduan_add',post,function(data){
						data=qukong(data);
						var jiange="<span class='jiange'>|--</span>";
						var html="<tr class='zi t"+id+" tr"+data+"'><td onclick='zi("+data+")'>-</td><td>"+jiange+diaoyongmingcheng+"</td><td><input value='50' class='paixu px"+id+"' onchange='paixu("+id+")' /></td><td class='run'><a onclick=\"edit("+data+",'"+diaoyongmingcheng+"','"+morenzhi+"')\">编辑</a><a onclick=\"shan("+data+",'"+diaoyongmingcheng+"')\">删除</a></td></tr>";
						if($('.t'+id).length>0){
							$('.t'+id).last().after(html)
						}
						else{
							$('.tr'+id).after(html);
						}
						$('.t'+id).show();//打开栏目
						tanrun(0);//定位和弹出
					});
				}
			});
		}
		//类型为联动时候特殊处E
		else{
			var post={id:id,mid:mid,diaoyongmingcheng:diaoyongmingcheng,morenzhi:morenzhi,leixing:leixing};
			$.post('run_ajax.php?run=moxing_ziduan_add',post,function(data){
				data=qukong(data);
				var jiange="<span class='jiange'>|--</span>";
				var html="<tr class='zi t"+id+" tr"+data+"'><td onclick='zi("+data+")'>-</td><td>"+jiange+diaoyongmingcheng+"</td><td><input value='50' class='paixu px"+id+"' onchange='paixu("+id+")' /></td><td class='run'><a onclick=\"shan("+data+",'"+diaoyongmingcheng+"')\">删除</a></td></tr>";
				if($('.t'+id).length>0){
					$('.t'+id).last().after(html)
				}
				else{
					$('.tr'+id).after(html);
				}
				$('.t'+id).show();//打开栏目
				tanrun(0);//定位和弹出
			});
		}	
	}
}

//删除字段
function shan(id,diaoyongmingcheng){
	tanbox();//引入提示
	$(".tan_tit_con").html('温馨提示');//提示内容
	var html="您确定删除<b>"+diaoyongmingcheng+"</b>字段及其内容吗？";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right5 quxiao'>取消</a><a onclick='delbut("+id+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}
//删除字段提交按钮
function delbut(id){
	<?php echo $demo; ?>
	var post={id:id};
	$.post('run_ajax.php?run=delziduan',post,function(data){
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

//排序
function paixu(id){
	<?php echo $demo; ?>
	var paixu=$('.px'+id).val();
	var post={id:id,paixu:paixu};
	$.post('run_ajax.php?run=paixu_moxing',post,function(res){
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

//编辑字段
function edit(id,name){
	tanbox();//引入提示
	$(".tan_tit_con").html('字段【'+name+'】编辑默认值（<span>注：选择型默认值用<b>;</b>隔开</span>）');//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>默认值</th><td><textarea id='morenzhi' class='w1'></textarea></td></tr>";
	html+="<tr><th class='w100'>必填项</th><td><div class='w1'>";
	html+="<label><input type='radio' name='bitian' checked='checked' value='0' />否</label>";
	html+="<label><input type='radio' name='bitian' value='1' />是</label>";
	html+="</div></td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	
	$.post('run_ajax.php?run=moxing_morenzhi',{id:id},function(res){
		$('#morenzhi').val(res.morenzhi);
		$("input[type=radio][name=bitian][value="+res.bitian+"]").attr("checked",'checked')
	},'json');
	
	var but="<a onclick='tanrun(0)' class='margin_right5 quxiao'>取消</a><a onclick='edit_mx_tj("+id+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

function edit_mx_tj(id){
	<?php echo $demo; ?>
	var morenzhi=$('#morenzhi').val();
	var bitian=$('input[name="bitian"]:checked').val();
	$.post('run_ajax.php?run=edit_moxing_morenzhi&id='+id,{morenzhi:morenzhi,bitian:bitian},function(res){
		tanrun(0);//关闭
	});
}
</script>