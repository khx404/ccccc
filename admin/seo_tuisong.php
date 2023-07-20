<?php 
include('c_top.php');
?>
<!--主体-->
<div class="con">
	<?php include('seo_left.php');?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit"><a>快速提升搜索引擎收录</a></div>
        <div class="con1">
            <form id="post_info">
        <table class='from'>
        	<?php 
			$templets=array_diff(scandir("../templets/"),array("..","."));//获取模板;
			
			$infos=$c_sql->select("select * from info where (shuyu=5) ORDER BY paixu asc");
			foreach($infos as $info){
				$id=$info['id'];//id
				$diaoyongbiaoqian=$info['diaoyongbiaoqian'];//调用标签
				$shuyu=$info['shuyu'];//属于
				$leixing=$info['leixing'];//输入框类型
				$morenzhi=$info['morenzhi'];//默认值
				
				/*模板读取S*/
				if($diaoyongbiaoqian=='电脑模板文件'){
					$morenzhi='';
					if(count($templets)>0){
						foreach($templets as $mb){
							if(strstr($mb,'pc')){
								if($morenzhi==''){$morenzhi=$mb;}
								else{$morenzhi.=';'.$mb;}
							}
						}
					}
				}
				
				if($diaoyongbiaoqian=='手机模板文件'){
					$morenzhi='';
					if(count($templets)>0){
						foreach($templets as $mb){
							if(strstr($mb,'m')){
								if($morenzhi==''){$morenzhi=$mb;}
								else{$morenzhi.=';'.$mb;}
							}
						}
					}
				}
				/*模板读取S*/

				$neirong=$info['neirong'];//内容
				
				echo "<tr class='tr{$id}'><th class='w150'>{$diaoyongbiaoqian}</th><td>";
				
				//input
				if($leixing=='input'){
					echo "<input class='w1' name='$id' value='{$neirong}' />";
				}
				
				//textarea
				else if($leixing=='textarea'){
					echo "<textarea class='w1' name='$id'>{$neirong}</textarea>";
				}
				
				//select
				else if($leixing=='select'){
					echo "<select class='w1' name='$id'>";
					$xuanzes=explode(";", $morenzhi);
					foreach($xuanzes as $v){
						if($v==$neirong){
							echo "<option value='{$v}' selected='selected'>{$v}</option>";
						}
						else{
							echo "<option value='{$v}'>{$v}</option>";
						}
						
					}
					echo "</select>";
				}
				
				//删除按钮
				if($shuyu==2){
					echo "<a class='shan' onclick=\"shan({$id},'{$diaoyongbiaoqian}')\">X</a>";
				}
				
				
				echo "</td></tr>\r\n";
			}
			?>
			<tr class="tag_add"><th></th><td>
            	<a class='queren margin_right5' onclick="bianji()">确认</a>
                <span class="ts"></span>
            </td></tr>
            </table>
            <div class="cle20"></div>
        </div>
       <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>
<script>
//编辑提交
function bianji(){
	<?php echo $demo; ?>
	var post = $('#post_info').serializeArray();
	$.post('run_ajax.php?run=post_info',post,function(data){
		if(data==1){
			$('.ts').html("<span class='tishi'><font color='#1aacda'>保存成功！</font></span>");
		}
		else{
			$('.ts').html("<span class='tishi'>保存失败！</span>");
		}
		$(".ts span").fadeOut(1000);
	})
}
</script>