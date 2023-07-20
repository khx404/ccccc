<?php 
include('c_top.php');
if($_GET['run']==0){
	$tishi='待付款订单';
}
else if($_GET['run']==1){
	$tishi='待提货订单';
}
else if($_GET['run']==2){
	$tishi='已提货订单';
}
?>
<!--主体-->
<div class="con">
	<!--左侧-->
	<div class="con_left">
    	<ul class="con_nav">
        	<li class="tit"><span>订单管理</span></li>
            <li><a href="user_order.php?run=1" class="<?php if($_GET['run']==1){echo 'dq';} ?>">待提货订单</a></li>
            <li><a href="user_order.php?run=2" class="<?php if($_GET['run']==2){echo 'dq';} ?>">已提货订单</a></li>
            <li><a href="user_order.php?run=0" class="<?php if($_GET['run']==0){echo 'dq';} ?>">待付款订单</a></li>
        </ul>
    </div>
	<div class="con_right">
    	<div class="tit"><?php echo $tishi;?></div>
        <table class="list">
       	<tr><th>商品</th><th>金额</th><th>微信名称</th><th>电话号码</th><th>创建时间</th><th>状态</th></tr>
        <?php 
		if($_GET['run']==0){
			$run="run=0 and";
		}
		else if($_GET['run']==1){
			$run="run=1 and";
		}
		else if($_GET['run']==2){
			$run="(run!=0 and run!=1) and";
		}
		
		$uorder=$c_sql->select("select uorder.*,user.nickname,user.shoujihao from uorder,user where({$run} uorder.openid=user.openid) order by run asc");
		if(count($uorder)>0){
			foreach($uorder as $arr){
				$goodtxt='';
				$goods=explode(';',$arr['goods']);
				foreach($goods as $good){
					if($good!=''){
						$goods_ge=explode('=',$good);
						$goodtxt.="<span class='good'><a href='art.php?aid=".$goods_ge[0]."' target='_blank'><img height='50' src='".$goods_ge[1]."' /></a><em>".$goods_ge[2]."份</em></span>";
					}
				}
				
				$run_txt="<a title='".date( "Y-m-d H:i",$arr['run'])."，提走' style='color:#ccc'>已提货</a>";
				$ts='';
				if($arr['run']==1){
					$run_txt="<a class='te' onclick=\"tihuo('".$arr['nickname']."','".$arr['shoujihao']."',".$arr['id'].")\">待提货</a>";
					$ts=" style='color:#F00'";
				}
				else if($arr['run']==0){
					$run_txt="<a style='color:#f00'>未付款</a>";
				}
				
				echo "<tr>";
				echo "<td>".$goodtxt."</td>";
				echo "<td>".$arr['jine']."</td>";
				echo "<td>".$arr['nickname']."</td>";
				echo "<td>".$arr['shoujihao']."</td>";
				echo "<td {$ts}>".date( "Y-m-d H:i",$arr['chuangjianshijian'])."</td>";
				echo "<td class='run'>".$run_txt."</td>";
				echo "</tr>";
			}
		}
		
		?>
        </table>
        <style type="text/css">
        .good{ position:relative; margin-right:5px;}
        .good em{ padding:2px 4px; background:#F00; color:#fff; position:absolute; bottom:4px; right:0; border-radius:3px 0 0 0}
        </style>
        
        
        
        <div class="fenye">
       
        </div>
        
        <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>
<script>
function tihuo(nickname,shoujihao,id){
	tanbox();//引入提示
	$(".tan_tit_con").html('提货确认');//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>微信昵称</th><td><input readonly='readonly' class='w1' value='"+nickname+"' /></td></tr>";
	html+="<tr><th class='w100'>手机号</th><td><input readonly='readonly' class='w1' value='"+shoujihao+"' /></td></tr>";
	html+="<tr><th>提货密码</th><td><input class='w1' id='tihuoma' /></td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<span class='tishi'></span><a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick='tihuo_tj("+id+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

function tihuo_tj(id){
	var tihuoma=$('#tihuoma').val();
	if(tihuoma.length!=6){
		$('.tishi').html('请输入六位数提货码');
		return false;
	}
	$.post('user_ajax.php?run=tihuo',{id:id,tihuoma:tihuoma},function(res){
		if(res==0){
			$('.tishi').html('提货密码有误');
		}
		else{
			window.location="user_order.php";
		}
	});
}


</script>
<style type="text/css">
.tishi{ padding:0 15px;}

</style>