<?php 
if(!isset($_GET['id'])){
	header("Location:type_list.php"); 
	exit;
}
$id=$_GET['id'];
include('c_top.php');
if(isset($_GET['dq'])){
	$dq=$_GET['dq'];
}
else{
	$dq=1;
}
$types=$c_sql->select("select * from type where id={$id}");
$types=$types[0];
$id=$types['id'];
$lanmumingcheng=$types['lanmumingcheng'];
$fulanmumingcheng=$types['fulanmumingcheng'];
$shujumoxing=$types['shujumoxing'];
$lanmumoban=$types['lanmumoban'];
$xiangqingmoban=$types['xiangqingmoban'];
$baocunlujing=$types['baocunlujing'];
$youhuabiaoti=$types['youhuabiaoti'];
$youhuaguanjianci=$types['youhuaguanjianci'];
$youhuazhaiyao=$types['youhuazhaiyao'];
$lanmutupian=$types['lanmutupian'];
$lanmuneirong=$types['lanmuneirong'];
$paixu=$types['tid'];
?>
<!--主体-->
<div class="con">
	<?php include('type_left.php');?>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit"><a onclick="dq(1)" class="dq1"><?php echo $types['lanmumingcheng']; ?></a> | 
        <a onclick="dq(2)" class="dq2">SEO</a> | <a href="type.php?id=<?php echo $id;?>&dq=3" class="dq3">栏目内容</a></div>
        <form id='edit' method="post">
        <table class='from'>
        	<input type="hidden" name="id" value="<?php echo $id;?>" />
        	<tr class='ttr1'><th class='w150'>栏目名称</th><td><input name="lanmumingcheng" class='w1' value="<?php echo $lanmumingcheng; ?>" /></td></tr>
            <tr class='ttr1'><th class='w150'>副栏目名称</th><td><input name="fulanmumingcheng" class='w1' value="<?php echo $fulanmumingcheng; ?>" /></td></tr>
            <tr class='ttr1'><th class='w150'>保存路径</th><td><input name="baocunlujing" class='w1' value="<?php echo $baocunlujing; ?>" /></td></tr>
            <tr class='ttr1'><th>栏目模板</th><td>
            <select name="lanmumoban" class='w1'>
            <?php 
			$lanmumobans=array_diff(scandir("../../templets/".ii('电脑模板文件')),array("..","."));//获取模板;
			foreach($lanmumobans as $v){
				if(strstr($v, 'list')){
					if($v==$lanmumoban){
						echo "<option value='{$v}' selected='selected'>{$v}</option>";
					}
					else{
						echo "<option value='{$v}'>{$v}</option>";
					}
				}
			}
			?>
            </select>
            
            </td></tr>
            <tr class='ttr1'><th>详情模板</th><td><select name="xiangqingmoban" class='w1'>
            <?php 
			$lanmumobans=array_diff(scandir("../../templets/".ii('电脑模板文件')),array("..","."));//获取模板;
			foreach($lanmumobans as $v){
				if(strstr($v, 'art')){
					if($v==$xiangqingmoban){
						echo "<option value='{$v}' selected='selected'>{$v}</option>";
					}
					else{
						echo "<option value='{$v}'>{$v}</option>";
					}
				}
			}
			?>
            </select></td></tr>
            <tr class='ttr1'><th>数据模型</th><td><select name="shujumoxing" class='w1'>
            <?php 
			$moxings=$c_sql->select("select id,diaoyongmingcheng from moxing where (mid=0 and id!=1 and diaoyongmingcheng!='')");
			echo "<option value='' selected='selected'>无</option>";
			foreach($moxings as $arr){
				$id=$arr['id'];
				$diaoyongmingcheng=$arr['diaoyongmingcheng'];
				if($id==$shujumoxing){echo "<option value='{$id}' selected='selected'>{$diaoyongmingcheng}</option>";}
				else{echo "<option value='{$id}'>{$diaoyongmingcheng}</option>";}
			}
			?>
            </select></td></tr>
           
           <tr class='ttr2'><th class='w150'>优化标题</th><td><input name="youhuabiaoti" class='w1' value="<?php echo $youhuabiaoti; ?>" /></td></tr>
           <tr class='ttr2'><th class='w150'>优化关键词</th><td><textarea name="youhuaguanjianci" class='w1'><?php echo $youhuaguanjianci; ?></textarea></td></tr>
           <tr class='ttr2'><th class='w150'>优化摘要</th><td><textarea name="youhuazhaiyao" class='w1'><?php echo $youhuazhaiyao; ?></textarea></td></tr>
           
           <tr class='ttr3'><th class='w150'>栏目内容</th><td><div class="w1" style="overflow:hidden;">
<!--
编辑器必填参数
id='name_txt'
class="zbzedit"
data-path:图片存储路径
data-path-res:图片回调路径
data-height:图片高
data-pic-name:0改名，1原名
-->
<textarea style="display:none" name='lanmuneirong' id='lanmuneirong' class="zbzedit" data-path='../../../upload/up/'  data-path-res='../../upload/up/' data-height='450' data-pic-name='0'><?php echo $lanmuneirong; ?></textarea>
           
           </div></td></tr>
           
           <tr class='ttr3' id="lmtp"><th>栏目图片</th><td><p class="file1"><p class="file2">上传</p>
           <input type='file' multiple='multiple' class='file_input up' data-path='../../upload/up/' data-filename='0' data-zhanshi='lanmutupian'/>
           </p>
           	<?php 
			/*图片*/
			$fimgs=explode(';',$lanmutupian);
			$fimgx='';
			if($fimgs[0]!==''){
				foreach($fimgs as $k=>$fimg){
					$css='del'.time().$k;
					$fimgx.="<img onclick=\"del('{$fimg}','$css','lanmutupian')\" class='{$css}' src='{$fimg}' />";
				}
			}
			?>
            <div class="fimg lanmutupian"><?php echo $fimgx; ?></div>
            <textarea style='display:none' id='lanmutupian' name='lanmutupian'><?php echo $lanmutupian; ?></textarea>
            </td></tr>
            
            
            <tr><th></th><td><a class='queren margin_right5' onclick="tijiao()">确认</a>
            <?php 
			if($shujumoxing!=''){
				echo "<a class='quxiao fb' href='art.php?tid=$id'>发布</a>";
			}
			?>
            <span class="ts"></span></td></tr>
        </table>
        </form>

        <div class="cle70"></div>
    </div>
</div>

<?php include('c_foot.php');?>
<script src="../zbzedit/js/zbz_edit.js"></script>
<script src="js/up.js"></script>
<script>
function dq(dq){
	if(dq==1){
		$(".dq1").css("font-weight","bold");
		$(".dq2").css("font-weight","normal");
		$(".dq3").css("font-weight","normal");
		$(".ttr1").show();
		$(".ttr2").hide();
		$(".ttr3").hide();
	}
	if(dq==2){
		$(".dq1").css("font-weight","normal");
		$(".dq2").css("font-weight","bold");
		$(".dq3").css("font-weight","normal");
		$(".ttr1").hide();
		$(".ttr2").show();
		$(".ttr3").hide();
	}
	if(dq==3){
		$(".dq1").css("font-weight","normal");
		$(".dq2").css("font-weight","normal");
		$(".dq3").css("font-weight","bold");
		$(".ttr1").hide();
		$(".ttr2").hide();
		$(".ttr3").show();
	}
}

function tijiao(){
	<?php echo $demo;?>
	var post=$('#edit').serializeArray();
	$.post('run_ajax.php?run=edit_type',post,function(data){
		$('.ts').html("<span class='tishi'><font color='#1aacda'>保存成功！</font></span>");
		$(".ts span").fadeOut(1000);
		window.location='type_list.php';
	});
}
dq(<?php echo $dq;?>);
</script>