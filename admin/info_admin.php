<?php 
include('c_top.php');
$admins=$c_sql->select("select * from admin");
?>
<!--主体-->
<div class="con">
	<?php include('info_left.php');?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">管理员列表</div>
        <table class="list">
        <tr><th>管理员名称</th><th>等级</th><th>创建时间</th><th class="runth">操作</th><tr>
        <?php 
		foreach($admins as $arr){
			$mid=$arr['id'];
			$guanliyuan=$arr['guanliyuan'];
			$dengji=$arr['dengji'];
			if($dengji==1){
				$dengji='超级管理员';
			}
			else if($dengji==2){
				$dengji='发布员';
			}
			$chuangjianshijian=$arr['chuangjianshijian'];
			$chuangjianshijian=date("Y-m-d",$chuangjianshijian);
			echo "<tr class='admin{$mid}'><td>{$guanliyuan}</td>";
			echo "<td>{$dengji}</td>";
			echo "<td>{$chuangjianshijian}</td>";
			echo "<td class='run'><a onClick=\"edit($mid,'{$guanliyuan}',".$arr['dengji'].")\" class='te'>编辑</a><a onClick=\"del($mid,'$guanliyuan')\">删除</a></td></tr>";
		}
		?>
        </table>
        <div class="cle20"></div>
		<a class='queren' onClick="add(0,'添加管理员')">添加管理员</a>
        <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>
<script>
function add(){
	tanbox();//引入提示
	$(".tan_tit_con").html("添加管理员");//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>管理员名称</th><td><input id='guanliyuan' class='w1' /></td></tr>";
	html+="<tr><th>密码</th><td><input id='mima' type='password' class='w1' /></td></tr>";
	html+="<tr><th>重置密码</th><td><input id='mima1' type='password' class='w1' /></td></tr>";
	html+="<tr><th>等级</th>";
	html+="<td><div class='w1'><label><input name='dengji' type='radio' value='2' checked />发布员</label>";
	html+="<label><input name='dengji' type='radio' value='1' />超级管理员</label></div></td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<span class='tishi'></span><a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick='add_tijiao()' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//提交
function add_tijiao(){
	<?php echo $demo;?>
	var guanliyuan=$('#guanliyuan').val();
	var mima=$('#mima').val();
	var mima1=$('#mima1').val();
	var dengji=$('input[name="dengji"]:checked').val();
	var dengjiid=dengji;
	
	if(mima!=mima1 || mima.length<5 || guanliyuan.length<2){
		$(".tishi").html("<font color='#FF0000'>用户名密码长度不能低于5个字符</font>");
		$(".tishi font").fadeOut(5000);
		return false;
	}
	
	var reg = /^[0-9a-zA-Z]+$/
	if(!reg.test(guanliyuan) || !reg.test(mima) || !reg.test(mima1)){
		$(".tishi").html("<font color='#FF0000'>用户名密码只能为字母和数字</font>");
		$(".tishi font").fadeOut(5000);
		return false;
	}
	
	var post={guanliyuan:guanliyuan,mima:mima,dengji:dengji};
	$.post('run_ajax.php?run=admin',post,function(data){
		var classname=qukong('admin'+data);
		if(dengji==1){
			dengji='超级管理员';
		}
		else if(dengji==2){
			dengji='发布员';
		}
		
		var html="<tr class='"+classname+"'><td>"+guanliyuan+"</td>";
		html+="<td>"+dengji+"</td>";
		html+="<td>"+shijian()+"</td>";
		html+="<td class='run'><a onClick=\"edit("+data+",'"+guanliyuan+"',"+dengjiid+")\" class='te'>编辑</a>";
		html+="<a onClick=\"del("+data+",'"+guanliyuan+"')\">删除</a></td></tr>";
		$('.list').append(html);
		tanrun(0);//弹窗消失
	});
}

//编辑
function edit(id,guanliyuan,dengji){
	tanbox();//引入提示
	$(".tan_tit_con").html("编辑管理员");//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>管理员名称</th><td><input id='guanliyuan' class='w1' value='"+guanliyuan+"' /></td></tr>";
	html+="<tr><th>新管理员名称</th><td><input id='guanliyuan1' class='w1' /></td></tr>";
	html+="<tr><th>原密码</th><td><input id='mima' type='password' class='w1' /></td></tr>";
	html+="<tr><th>新密码</th><td><input id='mima1' type='password' class='w1' /></td></tr>";
	html+="<tr><th>等级</th>";
	var dj1='';
	var dj2='';
	if(dengji==1){dj1=" checked";}
	else{dj2=" checked";}
	
	html+="<td><div class='w1'><label><input name='dengji' type='radio' value='2'"+dj2+"/>发布员</label>";
	html+="<label><input name='dengji' type='radio' value='1'"+dj1+" />超级管理员</label></div></td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<span class='tishi'></span><a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick='edit_tijiao("+id+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//编辑提交
function edit_tijiao(id){
	<?php echo $demo;?>
	var guanliyuan=$('#guanliyuan').val();
	var guanliyuan1=$('#guanliyuan1').val();
	var mima=$('#mima').val();
	var mima1=$('#mima1').val();
	var dengji=$('input[name="dengji"]:checked').val();
	var dengjiid=dengji;
	if(guanliyuan.length<5 || guanliyuan1.length<5 || mima.length<5 || mima1.length<5){
		$(".tishi").html("<font color='#FF0000'>输入不能小于5个字符</font>");
		$(".tishi font").fadeOut(2000);
		return false;
	}
	
	var reg = /^[0-9a-zA-Z]+$/
	if(!reg.test(guanliyuan) || !reg.test(mima) || !reg.test(mima1)){
		$(".tishi").html("<font color='#FF0000'>用户名密码只能为字母和数字</font>");
		$(".tishi font").fadeOut(2000);
		return false;
	}
	
	var post={id:id,guanliyuan:guanliyuan,guanliyuan1:guanliyuan1,mima:mima,mima1:mima1,dengji:dengji};
	$.post('run_ajax.php?run=admin_edit',post,function(res){
		if(res==1){
			tui();
		}
		else{
			$(".tishi").html("<font color='#FF0000'>原用户名或密码有误！</font>");
			$(".tishi font").fadeOut(2000);
			return false;
		}
	});
}

//删除管理员
function del(id,guanliyuan){
	tanbox();//引入提示
	$(".tan_tit_con").html('温馨提示');//提示内容
	var html="您确定删除<b>"+guanliyuan+"</b>管理员吗？";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick='delbut("+id+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//删除栏目提交按钮
function delbut(id){
	<?php echo $demo;?>
	var post={id:id};
	$.post('run_ajax.php?run=deladmin',post,function(res){
		$('.admin'+id).remove();
	});
	tanrun(0);//弹窗消失
}
</script>