<?php 
include('c_top.php');
?>
<!--主体-->
<div class="con">
	<!--左侧-->
	<div class="con_left">
    	<ul class="con_nav">
        	<li class="tit"><span>会员列表</span></li>
        </ul>
    </div>
	<div class="con_right">
    	<div class="tit">会员列表</div>
        
			
        
        <table class="list">
        	<tr><th>ID</th><th>openid</th><th>头像</th><th>用户名</th><th>手机号</th><th>入驻时间</th></tr>
            <?php 
			$user=$c_sql->select("select * from user");
			if(count($user)>0){
				foreach($user as $arr){
					$id=$arr['id'];
					$openid=$arr['openid'];
					$nickname=$arr['nickname'];
					$avatarurl=$arr['avatarurl'];
					$shoujihao=$arr['shoujihao'];
					$chuangjianshijian=date( "Y-m-d H:i",$arr['chuangjianshijian']);
					echo "<tr>";
					echo "<td>{$id}</td>";
					echo "<td>{$openid}</td>";
					echo "<td><img src='{$avatarurl}' width='40p' /></td>";
					echo "<td>{$nickname}</td>";
					echo "<td>{$shoujihao}</td>";
					echo "<td>{$chuangjianshijian}</td>";
					echo "</tr>";
				}
			}
			?>
        </table>
        
        <div class="fenye">
       
        </div>
        
        <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>