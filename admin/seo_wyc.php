<?php include('c_top.php');?>
<!--主体-->
<div class="con">
	<?php include('seo_left.php');?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
        	<span>伪原创设置</span>
            <a class='wj1 shangchuan tianjia right_but' onClick="bianji()">确认</a>
        </div>
        <form id="post_info">
        <table class='from'>
        	<?php 
			$templets=array_diff(scandir("../../templets/"),array("..","."));//获取模板;
			$infos=$c_sql->select("select * from info where (shuyu='wyc') ORDER BY paixu asc,id asc");
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
				if($leixing=='file'){
					echo "<tr class='tr{$id}'><th class='w150'>{$diaoyongbiaoqian}</th><td class='shangchuan_info'>";
				}
				else{
					echo "<tr class='tr{$id}'><th class='w150'>{$diaoyongbiaoqian}</th><td>";
				}
				//input
				if($leixing=='input'){
					echo "<input class='w1' name='$id' value='{$neirong}' />";
				}
				
				//textarea
				else if($leixing=='textarea'){
					echo "<textarea class='w1' name='$id' style='height:200px'>{$neirong}</textarea>";
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
				
				//上传
				else if($leixing=='file'){
					echo "<p class='file1'><p class='file2'>上传</p>";
					echo "<input type='file' multiple='multiple' class='file_input up' data-path='../../upload/up/' data-filename='0' data-zhanshi='{$id}'/></p>";
					
					/*图片*/
					$fimgs=explode(';',$neirong);
					$fimgx='';
					if($fimgs[0]!==''){
						foreach($fimgs as $k=>$fimg){
							$css='del'.time().$k;
							$fimgx.="<img onclick=\"del('{$fimg}','$css','{$id}')\" class='{$css}' src='{$fimg}' />";
						}
					}
					echo "<div class='fimg {$id}'>{$fimgx}</div>";
					echo "<textarea style='display:none;' id='{$id}' name='{$id}'>{$neirong}</textarea>";
					echo "<script>filecz[$ifile]='{$id}';</script>";
					$ifile++;
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
        </form>
        <div class="cle70"></div>
    </div>
</div>
<?php 
include('c_foot.php');
?>
<script src="js/up.js"></script>
<script>
//编辑提交
function bianji(){
	<?php echo $demo;?>
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