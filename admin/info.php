<?php include('c_top.php');?>
<!--主体-->
<div class="con">
	<?php include('info_left.php');?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
        	<span>基本设置</span>
            <a class='wj1 shangchuan tianjia right_but' onClick="bianji()">确认</a>
        </div>
        <form id="post_info">
        <table class='from'>
        	<?php 
			$templets=array_diff(scandir("../../templets/"),array("..","."));//获取模板;
			$infos=$c_sql->select("select * from info where (shuyu=1 or shuyu=2) ORDER BY paixu asc,id asc");
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
					echo "<textarea style='display:none' id='{$id}' name='{$id}'>{$neirong}</textarea>";
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
            	<a class='queren margin_right5' onclick="bianji()">确认</a><a class='quxiao' onclick="addinfo()">新增参数</a>
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

//弹出新增参数
function addinfo(){
	tanbox();//引入提示
	$(".tan_tit_con").html('新增参数');//提示内容
	var html="<form id='from'><table class='from'>";
	html+="<tr><th class='w100'>调用名称</th><td><input class='w1' id='diaoyongbiaoqian' /></td></tr>";
	html+="<tr><th>内容</th><td><textarea class='w1' id='neirong'></textarea></td></tr>";
	html+="<tr><th>数据类型</th><td>";
	html+="<label><input name='leixing' type='radio' value='input' checked='checked' />单行文本</label>";
	html+="<label><input name='leixing' type='radio' value='textarea' />多行文本</label>";
	html+="<label><input name='leixing' type='radio' value='file' />上传</label>";
	html+="</td></tr>";
	html+="</table></form>";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right5 quxiao'>取消</a><a onclick='tijiao()' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//新增参数提交
function tijiao(){
	<?php echo $demo;?>
	var diaoyongbiaoqian=$('#diaoyongbiaoqian').val();
	var neirong=$('#neirong').val();
	var leixing=$('input[name="leixing"]:checked').val();
	var post={diaoyongbiaoqian:diaoyongbiaoqian,neirong:neirong,leixing:leixing};
	
	if(diaoyongbiaoqian.length<2){
		return false;
	}
	
	$.post('run_ajax.php?run=info_add',post,function(data){
		if(data==0){
			alert('添加失败');
		}
		else{
			tanrun(0);
			if(leixing=='file'){
				window.location='info.php';
			}
			
			var html="<tr class='"+qukong('tr'+data)+"'><th class='w100'>"+diaoyongbiaoqian+"</th><td>";
			if(leixing=='input'){
				html+="<input class='w1' name='"+data+"' value='"+neirong+"' />";
			}
			else if(leixing=='textarea'){
				html+="<textarea class='w1' name='"+data+"'>"+neirong+"</textarea>";
			}
			
			html+="<a class='shan' onclick=\"shan("+data+",'"+diaoyongbiaoqian+"')\">X</a>";
			
			html+="</td></tr>";
			$('.tag_add').before(html);
		}
	});
}

//删除参数弹出提示
function shan(id,diaoyongbiaoqian){
	tanbox();//引入提示
	$(".tan_tit_con").html('温馨提示');//提示内容
	var html='你确定删除<b>'+diaoyongbiaoqian+'</b>参数吗，不能恢复哦，删除前是否先备份一下数据呢？';
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right5 quxiao'>取消</a><a onclick='shan_tj("+id+")' class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//删除参数提交
function shan_tj(id){
	<?php echo $demo;?>
	$.post('run_ajax.php?run=info_shan',{id:id},function(data){
		if(data==1){
			tanrun(0);//弹窗消失
			$(qukong('.tr'+id)).remove();
		}
	});
}
</script>