<?php include('c_top.php');?>
<!--主体-->
<div class="con">
	<?php include('info_left.php');?>
	
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
        	<span>安全中心</span>
        </div>
        
        <table class='from'>
            <tr class="woyao"><th class="w150">操作内容</th><td>
            <label><input name="woyao" type='radio' value="1" />备份数据</label>
            <label><input name="woyao" type='radio' value="3" />还原数据</label>
            </td></tr>
            <tr><th class='w150'><span class="jisuan">计算X+X等于</span></th><td><input class='w250' id='jisuan' /></td></tr>
            <tr><th></th><td><a class='queren margin_right5 zhixing'>执行</a><span class="tishi"></span></td></tr>
            <tr><th></th><td class="iframe"></td></tr>
        </table>
        <div class="cle70"></div>
    </div>
</div>
<?php 
include('c_foot.php');
//读取数据文件
$huanyuans=array_diff(scandir("../data"),array("..",".","yaoqing.txt"));
rsort($huanyuans);
$huanyuan='';
foreach($huanyuans as $riqi){
	if($riqi==0){
		$huanyuan.="<option value='$riqi'>不带数据</option>";
	}
	else{
		$huanyuan.="<option value='$riqi'>$riqi</option>";
	}
}
?>
<script>
jisuan();//输出计算验证
//点击还原弹出选择还原日期
$(':radio').click(function(){
	jisuan()//重置验证码
	$('.after').remove();
    var woyao = $(this).val();
	if(woyao==3){
		var html="<tr class='after'><th>还原到</th><td><select class='w250' id='riqi'><?php echo $huanyuan; ?></select></td></tr>";
		$('.woyao').after(html);
	}
});

//点击执行按钮
$('.zhixing').click(function(){
	<?php echo $demo; ?>
	$('.tishi').html('');//清空提示
	var woyao=$('input:radio:checked').val();
	if(woyao==undefined){
		$('.tishi').html('请选择你要操作的内容');
		return false;
	}
	
	//计算验证等于1正确，0错误
	if(jisuan_jg()==0){
		$('.tishi').html('计算验证有误');
		return false;
	};
	jisuan()//重置验证码
	
	//数据备份
	if(woyao==1){
		$('.iframe').html("<iframe frameborder='0' src='run_beifen.php?jindu=0&page=1'></iframe>");
	}
	else if(woyao==2){
		$('.iframe').html("<iframe frameborder='0' src='run_beifen_zip.php?jindu=0&page=1'></iframe>");
	}
	else if(woyao==3){
		var riqi=$('#riqi').val();
		$('.iframe').html("<iframe src='run_huanyuan.php?jindu=2&riqi="+riqi+"' frameborder='0'><html>");
	}
});
</script>