<?php include('c_top.php');$shuyu=1;?>
<!--主体-->
<div class="con">
	<!--左侧-->
	<div class="con_left">
    	<ul class="con_nav">
        	<li class="tit"><span>友链广告</span></li>
            <li><a href="youlian.php" class='dq'>友链链接</a></li>
            <li><a href="ad.php">广告管理</a></li>
        </ul>
    </div>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit"><span>友情链接</span></div>
        <table class="list">
        <tr><th>id</th><th>网站名称</th><th>链接</th><th>图片</th><th class="runth">执行</th><tr>

<?php 
//循环模型
$tmp="<tr class='tr[id]'><td>[id]</td>";
$tmp.="<input type='hidden' class='id' value='[id]' />";
$tmp.="<input type='hidden' class='shuyu' value='$shuyu' />";
$tmp.="<input type='hidden' class='width' value='[width]' />";
$tmp.="<input type='hidden' class='height' value='[height]' />";
$tmp.="<input type='hidden' class='run' value='[run]' />";
$tmp.="<input type='hidden' class='paixu' value='[paixu]' />";
$tmp.="<td><input type='text' class='biaoti' value='[biaoti]' /></td>";
$tmp.="<td><input class='lianjie' value='[lianjie]' /></td>";
$tmp.="<td><input type='hidden' class='tupian' value='[tupian]' />";
$tmp.="<div class='file-btn'><input type='file' onchange=\"uppic('tupian',[id],'youad')\" class='up_tupian'/><img class='img_tupian' src='[tupian]' /></div></td>";


$tmp.="<td class='run'><a class='te' onclick=\"tijiao([id],'youad')\">保存</a><a onclick=\"del([id],'youad',0)\">删除</a></td></tr>";
echo "<textarea id='tmp' style='display:none'>$tmp</textarea>";		

//最大id
$id_maxs=$c_sql->select("select id from youad order by id desc limit 1");
$id_max=$id_maxs[0]['id'];
echo "<input type='hidden' id='id_max' value='{$id_max}' />";

//数据列表
$lists=$c_sql->select("select * from youad where shuyu=$shuyu order by id asc");
foreach($lists as $arr){
	$tmp_new=$tmp;
	foreach($arr as $k=>$v){
		if($k=='tupian' && $v==''){
			$tmp_new=str_ireplace("value='[tupian]'","value=''",$tmp_new);
			$tmp_new=str_ireplace("src='[tupian]'","src='images/shangchuan.png'",$tmp_new);
		}
		else{
			$tmp_new=str_ireplace("[{$k}]",$v,$tmp_new);
		}
	}
	echo $tmp_new;
}
?>
</table>
<div class="cle20"></div>
<a class='queren' onclick="add(0)">添加</a>
<span class="ts"></span>
        <div class="cle70"></div>
    </div>
</div>
<?php include('c_foot.php');?>
<script>

//读取模板并且替换
function qutmp(){
	var tmp=$('#tmp').val();
	//处理模型
	tmp=tmp.replace('[shuyu]','<?php echo $shuyu; ?>');
	tmp=tmp.replace('[width]','100');
	tmp=tmp.replace('[height]','100');
	tmp=tmp.replace('[run]','1');
	tmp=tmp.replace('[paixu]','50');
	tmp=tmp.replace('[biaoti]','');
	
	tmp=tmp.replace("src='[tupian]'","src='images/shangchuan.png'");
	
	tmp=tmp.replace('[tupian]','');
	tmp=tmp.replace('[lianjie]','');
	return tmp;
}
</script>
<script src="js/ajax.js"></script>