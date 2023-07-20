<?php 
include('c_top.php');
if(isset($_GET['aid'])){
	$aid=$_GET['aid'];
	$arts=$c_sql->select("select * from art where id={$aid}");
	$arts=$arts[0];
	$tid=$arts['tid'];
}
else{
	$aid=0;
	$paixu=50;
	if(isset($_GET['tid'])){$tid=$_GET['tid'];}
	else if(!isset($tid)){$tid=0;}
}
//当前栏目
$types=$c_sql->select("select lanmumingcheng,shujumoxing from type where id=$tid limit 1");
$shujumoxing=$types[0]['shujumoxing'];
$shujumoxing="mid in(1,{$shujumoxing})";
$shujumoxing=str_ireplace(";",",",$shujumoxing);
$moxings=$c_sql->select("select * from moxing where (mid!=0 and $shujumoxing) order by paixu asc");

//发布后是否同步更新
$tonbugengxins=$c_sql->select("select neirong from info where diaoyongbiaoqian='发布时同步更新'");
if(isset($tonbugengxins[0]['neirong'])){$tonbugengxin=$tonbugengxins[0]['neirong'];}
else{$tonbugengxin='否';}

//当前模式
$qidongmoshis=$c_sql->select("select neirong from info where diaoyongbiaoqian='启动模式'");
$qidongmoshi=$qidongmoshis[0]['neirong'];

//关联文章
if(isset($arts['aid'])){
	$glid=$arts['aid'];
}
else if(isset($_GET['glid'])){
	$glid=$_GET['glid'];//关联文章的id
}
else{
	$glid='0';
}

if($glid=='0'){
	$aglid='';
}
else{
	$aglid="&glid=$glid";
}
?>
<!--主体-->
<div class="con">
	<?php include('type_left.php');?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
<a class="dq1">在【<?php echo $types[0]['lanmumingcheng'] ?>】发布内容</a>
<a class='wj1 shangchuan tianjia right_but' onClick="tijiao()">确认</a>
</div>
<form id="edit">
        <table class='from'>
            <input id="id" name="id" type="hidden" value="<?php echo $aid; ?>">
            <input id="aid" name="aid" type="hidden" value="<?php echo $glid; ?>">
            <input id="tid" name="tid" type="hidden" value="<?php echo $tid; ?>">
            <script>var filecz=new Array();</script>
            <!--模型循环开始-->
            <?php 
			$body_ids=array();
			$chongzhi_mx='';//重置
			$bitian_js='';
			foreach($moxings as $mx){
				$neirong='';
				$mid=$mx['mid'];
				$diaoyongmingcheng=$mx['diaoyongmingcheng'];
				$paixu=$mx['paixu'];
				$ziduan=pinyin($diaoyongmingcheng,$lx='all');//字段
				$bitian='';
				//必填项目JS验证
				if($mx['bitian']==1){
					if($leixing=='body'){
						$ziduanid=$ziduan.'_txt';
					}
					else{
						$ziduanid=$ziduan;
					}
					
					$bitian="（<font color='#FF0000'>*</font>）";
					$bitian_js.="if($('#{$ziduanid}').val()==''){
		$('.ts').html(\"<span class='tishi'>【{$diaoyongmingcheng}】不能为空</span>\");
		$(\".ts span\").fadeOut(3000);
		return false;
	}\r\n";
				}
				if(isset($arts[$ziduan])){
					$neirong=$arts[$ziduan];
				}
				if($neirong=='' && $ziduan=='zuozhe'){
					$neirong=$admins['guanliyuan'];
				}
				
				if($ziduan=='fabushijian'){
					date_default_timezone_set('PRC');
					if($neirong=='1' || $neirong==''){
						$neirong=date("Y-m-d H:i",time());
					}
					else{
						$neirong=date("Y-m-d H:i",$neirong);
					}
				}
				
				if($ziduan=='paixu'){
					if($neirong==''){$neirong=50;}
				}
				
				$leixing=$mx['leixing'];
				$morenzhi=$mx['morenzhi'];
				
				if($mid==1 and $paixu>999){
					echo "<tr class='tr more' style='display:none'><th>{$diaoyongmingcheng}{$bitian}</th><td>";
				}
				else{
					echo "<tr class='tr'><th width='180'>{$diaoyongmingcheng}{$bitian}</th><td>";
				}
				
				//input
				if($leixing=='input'){
					echo "<input name='{$ziduan}' id='{$ziduan}' class='w1' value='{$neirong}' />";
					$chongzhi_mx.="$('#{$ziduan}').val('');"."\r\n";
				}
				//option下拉
				else if($leixing=='option'){
					echo "<select name='{$ziduan}' class='w1'>";
					
					$morenzhi=str_ireplace(';',"\r\n",$morenzhi);
					$morenzhi=explode("\r\n",$morenzhi);
					
					
					foreach($morenzhi as $op){
						if($op==$neirong){
							echo "<option selected='selected' value='{$op}'>{$op}</option>";
						}
						else{
							echo "<option value='{$op}'>{$op}</option>";
						}
					}
					echo "</select>";
				}
				//checkbox多选
				else if($leixing=='checkbox'){
					echo "<div class='w1'>";
					$morenzhi=str_ireplace(';',"\r\n",$morenzhi);
					$morenzhi=explode("\n",$morenzhi);
					foreach($morenzhi as $op){
						if($op!=''){
							if($op==$neirong){
								echo "<label><input checked  name='{$ziduan}' type='checkbox' value='{$op}' />{$op}</label>";
							}
							else{
								if(strstr($op,'[默认选中]')){
									$checked='checked';
									$op=str_ireplace('[默认选中]','',$op);//常规替换
								}
								else{$checked='';}
								echo "<label><input {$checked} name='{$ziduan}' type='checkbox' value='{$op}' />{$op}</label>";
							}
						}
					}
					echo "</div>";
				}
				//radio单选
				else if($leixing=='radio'){
					echo "<div class='w1'>";
					$morenzhi=str_ireplace(';',"\n",$morenzhi);
					$morenzhi=explode("\n",$morenzhi);
					foreach($morenzhi as $op){
						if($op!=''){
							//发布时间S
							if($ziduan=='fabushijian'){
								if($op=='当前时间'){
									if($dengji!=2){
										if($arts[$ziduan]==1){
											echo "<label><input name='{$ziduan}' type='radio' value='{$neirong}' />{$neirong}</label>";
										}
										else{
											echo "<label><input checked name='{$ziduan}' type='radio' value='{$neirong}' />{$neirong}</label>";
										}
										echo "<input name='{$ziduan}' value='{$neirong}' style='margin-left:20px;' >";
									}
								}
								else{
									if($arts[$ziduan]==1){
										echo "<label><input checked name='{$ziduan}' type='radio' value='1' />{$op}</label>";
									}
									else{
										if($dengji=2){
											echo "<label><input name='{$ziduan}' type='radio' value='1' />{$op}</label>";
										}
										else{
											echo "<label><input name='{$ziduan}' type='radio' value='1' />{$op}</label>";
										}
									}
								}
							}
							//发布时间E
							
							else{
								if($op==$neirong){
									echo "<label><input checked name='{$ziduan}' type='radio' value='{$op}' />{$op}</label>";
								}
								else{
									if(strstr($op,'[默认选中]')){
										$checked='checked';
										$op=str_ireplace('[默认选中]','',$op);//常规替换
									}
									else{$checked='';}
									echo "<label><input {$checked} name='{$ziduan}' type='radio' value='{$op}' />{$op}</label>";
								}
							}
						}
					}
					
					echo "</div>";
				}
				//textarea
				else if($leixing=='textarea'){
					echo "<textarea name='{$ziduan}' id='{$ziduan}' class='w1'>{$neirong}</textarea>";
					$chongzhi_mx.="$('#{$ziduan}').val('');"."\r\n";
				}
				
				//body
				else if($leixing=='body'){
					echo "<div class='w1' style='overflow:hidden;'>";
					echo "<textarea class='{$ziduan} hide zbzedit' id='{$ziduan}_txt' name='{$ziduan}' data-path='../../../upload/up/' data-path-res='../../upload/up/' data-height='450' data-pic-name='0'>{$neirong}</textarea>";
					echo "</div>";
					$body_ids[]=$ziduan;
				}
				//上传
				else if($leixing=='file'){
					echo "<p class='file1'><p class='file2'>上传</p>";
					echo "<input type='file' multiple='multiple' class='file_input up' data-path='../../upload/up/' data-filename='0' data-zhanshi='{$ziduan}'/>";
					
					/*图片*/
					$fimgs=explode(';',$neirong);
					$fimgx='';
					if($fimgs[0]!==''){
						foreach($fimgs as $k=>$fimg){
							$css='del'.time().$k;
							$fimgx.="<img onclick=\"del('{$fimg}','$css','{$ziduan}')\" class='{$css}' src='{$fimg}' />";
						}
					}
					
					echo "<div class='fimg {$ziduan}'>{$fimgx}</div>";
					echo "<textarea style='display:none' id='{$ziduan}' name='{$ziduan}'>{$neirong}</textarea>";
					echo "<script>filecz[$ifile]='{$ziduan}';</script>";
					$ifile++;
					
				}
				//多级联动
				else if($leixing=='liandong'){
					echo "<input name='{$ziduan}' id='{$ziduan}' type='hidden' value='{$neirong}'>";
					echo "<div class='liandong w1 ld{$ziduan}'>";
					if($neirong=='' || $neirong==0){
						$res=$c_sql->select("select * from liandong where lid={$morenzhi}");
						echo "<select id='{$ziduan}_0' onchange=\"ld('{$ziduan}','{$ziduan}_0')\">";
						echo "<option value='0'>全部</option>";
						foreach($res as $ldarr){
							$ld_id=$ldarr['id'];
							$name=$ldarr['name'];
							echo "<option value='{$ld_id}'>$name</option>";
						}
						echo "</select>";
					}
					//如果联动值存在
					else{
						$result=array();
						$ldfs=$c_sql->diguif('liandong','lid',$neirong,$result);
						foreach($ldfs as $arr){
							$id_dq=$arr['id'];
							$lid=$arr['lid'];
							if($lid!=0){
								$dq_lds=$c_sql->select("select * from liandong where lid=$lid");
								if(count($dq_lds)>0){
									echo "<select id='{$ziduan}_{$ld_qi}' onchange=\"ld('{$ziduan}','{$ziduan}_{$ld_qi}')\">";
									echo "<option value='0'>全部</option>";
									foreach($dq_lds as $ldarr){
										$id=$ldarr['id'];
										$lid=$ldarr['lid'];
										$name=$ldarr['name'];
										if($id==$id_dq){
											echo "<option value='{$id}' selected='selected'>{$name}</option>";
										}
										else{
											echo "<option value='{$id}'>{$name}</option>";
										}
									}
									echo "</select>";
									$ld_qi++;
								}
							}
						}
					}
					echo "</div>";
				}
				echo "</td></tr>"."\r\n";
			}
			?>
            <tr class='tr'><th></th><td><div style="width:80%">
            <a class="more_sz" onclick='more()'>...展示更多设置...</a></div>
            </td></tr>
			<!--模型循环结束-->
            <tr class="but"><th></th><td><a class='queren margin_right5' onClick="tijiao()">确认</a>
            <?php 
			if(isset($_GET['aid'])){
				echo "<a class='quxiao chongzhi' href='../../../search.php?art=".$_GET['aid']."' target='_blank'>演示</a>";
			}
			?>
            <a id='art' href="art_list.php?tid=<?php echo $tid.$aglid; ?>" class='quxiao chongzhi margin_left5'>列表</a>
            <a id='art' href="art.php?tid=<?php echo $tid.$aglid; ?>" title="在【<?php echo $types[0]['lanmumingcheng'] ?>】发布内容" class='quxiao chongzhi margin_left5'>再发一篇</a>
            <span class="ts"></span></td></tr>
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
function more(){
	if($(".more").is(":hidden")){
		$(".more").show();    //如果元素为隐藏,则将它显现
		$('.more_sz').html('...隐藏更多设置...');
	}
	else{
		$(".more").hide();     //如果元素为显现,则将其隐藏
		$('.more_sz').html('...展示更多设置...');
	}
}

function tijiao(){
	<?php echo $demo;?>
	var from=$('#edit').serializeArray();
	var biaoti=$('#biaoti').val();
	
	if(biaoti==''){
		return false;
	}
	//必填项
	<?php echo $bitian_js;?>
	$.post('run_ajax.php?run=art',from,function(data){
		$('#id').val(data);
		if('<?php echo $tonbugengxin; ?>'=='是'){
			make_dan('make_dan&php&art='+data+'&ms=<?php if(strstr($qidongmoshi,'电脑')){echo 'pc';}else{echo 'm';} ?>');
		}
		else{
			var id=$('#id').val();
			var dq_id=$('#tid').val();
			window.location='art_list.php?tid='+dq_id+'&aid='+id+'<?php echo $aglid;?>';
		}
	});
}

//提交完就生成
function make_dan(data){
	$.post('../../../search.php?'+data,{},function(res){
		if(res.indexOf("make_dan")!= -1){
			make_dan(res);
		}
		else{
			$('.ts').html("<span class='tishi'><font color='#1aacda'>保存成功！</font></span>");
			$(".ts span").fadeOut(1000);
			if($('input[name="fabushijian"]:checked').val()!=1){
				html_run('make.php?t='+$('#tid').val());//发布后更新
			}
			var id=$('#id').val();
			var dq_id=$('#tid').val();
			window.location='art_list.php?tid='+dq_id+'&aid='+id+'<?php echo $aglid;?>';
		}
	})
}

//重置
$('.chongzhi').click(function(){
	<?php echo $chongzhi_mx;?>
	$('#id').val(0);//2019-01-11 01:47
	var myDate = new Date();//获取系统当前时间
	$('#fabushijian').val(myDate.getFullYear()+'-'+myDate.getMonth()+'-'+myDate.getDate()+' '+myDate.getHours()+':'+myDate.getMinutes());
	fabudao();
	chongzhiue();
	$(".dq1").css("font-weight","bold");
	$(".dq2").css("font-weight","normal");
	chongzhipic(filecz);
})

//重置去除图片
function chongzhipic(filecz){
	for(var i=0;i<filecz.length;i++){
		$('.'+filecz[i]).html('');
		$('#'+filecz[i]).val('');
	}
}

//联动
function ld(ziduan,ldid){
	var xz_id=$('#'+ldid).val();//选中id
	var id=$('#'+ziduan).val(xz_id);//填充到表单
	//第几个选择
	var lds=ldid.split("_");
	var ld_id=lds[1]*1+1;	
	$('#'+ldid).nextAll().remove();//清除当前元素后的所有元素
	var txt=$('#'+ldid).find("option:selected").text();//选择项的文本
	if(txt=='全部'){
		return false;
	}
	var post={id:xz_id};
	$.post('run_ajax.php?run=liandong',post,function(data){
		var html="<select id='"+ziduan+"_"+ld_id+"' onchange=\"ld('"+ziduan+"','"+ziduan+"_"+ld_id+"')\">";
		html+="<option value='"+xz_id+"'>全部</option>";
		for ( var i = 0; i <data.length; i++){
			var id=data[i].id;
			var lid=data[i].lid;
			var name=data[i].name;
			var id=data[i].id;
			html+="<option value='"+id+"'>"+name+"</option>";
		}
		html+='</select>';
		if(data.length>0){
			$('.ld'+ziduan).append(html);
		}
	},'json');
	
}
</script>
<?php 
//引入编辑器代码
if(count($body_ids)>0){
	echo '<script src="../zbzedit/js/zbz_edit.js"></script>';
}
?>