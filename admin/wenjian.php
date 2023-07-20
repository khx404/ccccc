<?php 
include('c_top.php');
if(!isset($_GET['path'])){
	header('Location:index.php');
	exit;
}
$path=$_GET['path'];
if(strstr($path, '../../../..')){
	header('Location:index.php');
	exit;
}
$wenjians=array_diff(scandir($path),array("..","."));//获取模板;
?>
<!--主体-->
<div class="con">
	<!--左侧-->
	<div class="con_left">
    	<ul class="con_nav">
        	<li class="tit"><span>文件管理</span></li>
            <li><a href="wenjian.php?path=../../templets/<?php echo ii('电脑模板文件'); ?>">电脑模板</a></li>
            <li><a href="wenjian.php?path=../../templets/<?php echo ii('手机模板文件'); ?>">手机模板</a></li>
            <li><a href="wenjian.php?path=../../.." class="lminfo_liandong">根目录</a></li>
        </ul>
    </div>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
		<?php 
		$ps=explode("/",$path);
		$i=0;
		$a='';
		foreach($ps as $p){
			$herf='';
			$ii=0;
			foreach($ps as $hr){
				if($herf==''){
					$herf=$hr;
				}
				else{
					$herf.='/'.$hr;
				}
				if($ii==$i){
					break;	
				}
				$ii++;
			}
			if($a==''){
				$a="<a href='wenjian.php?path={$herf}'>{$p}</a>";
			}
			else{
				$a.="/<a href='wenjian.php?path={$herf}'>{$p}</a>";
			}
			$i++;
		}
		echo $a;
		?>
        
        
        <a class='wj1 shangchuan tianjia right_but'>上传
        <input type='file' multiple='multiple' class='file_input up' data-tishi='1' data-path='<?php echo $path; ?>/' data-filename='1' data-zhanshi='lanmutupian'/>
        </a>
        
        </div>
        <div class="wenjian_dh">
        <ul>
        <?php 
		$i=1;
		foreach($wenjians as $pa){
			$pa=iconv("GB2312//IGNORE","UTF-8",$pa);
			$houzui=getExt1($pa);
			if($path=='../../..' && $houzui=='html'){}
			else{
				echo "<li class='li{$i}'>";
				if($houzui==1){
					echo "<p><a href='wenjian.php?path={$path}/{$pa}' class='wenjianjia'>{$pa}</a>";
				}
				else if($houzui=='jpg' || $houzui=='png' || $houzui=='gif'){
					echo "<p class='img'><a target='_blank' href='{$path}/{$pa}' onClick='pic($i)'><img src='{$path}/{$pa}' /></a>";
					echo "<br><span class='pic'></span>";
					
				}
				else{
					echo "<p class='dq{$i}'><a onClick=\"wenjian('{$path}/{$pa}',$i)\" class='{$houzui}'>{$pa}</a>";
				}
				echo "<a class='shan' onclick=\"del($i,'{$path}/{$pa}')\">X</a></</p></li>"."\r\n";
				
				$i++;
			}
		}
		?>
        </ul>
        </div>
        <div class="cle20"></div>
        <div class="edit"></div>
    </div>
</div>
<?php 
include('c_foot.php');
?>
<script src="js/up.js"></script>
<script>
//删除文件弹窗
function del(i,path){
	tanbox();//引入提示
	$(".tan_tit_con").html('温馨提示');//提示内容
	var html="您确定删除<b>"+path+"</b>吗？";
	$(".tan_con").html(html);//写入内容
	var but="<a onclick='tanrun(0)' class='margin_right8 quxiao'>取消</a><a onclick=\"delbut('"+i+"','"+path+"')\" class='queren'>确认</a>";
	$(".tan_but").html(but);//写入按钮
	tanrun();//定位和弹出
}

//删除文件提交
function delbut(i,path){
	<?php echo $demo;?>
	$.post('run_ajax.php?run=delpath',{path:path},function(res){
		$('.li'+i).remove();
		tanrun(0);
	});
}

function wenjian(path,i){
	var post={path:path}
	$.post('run_ajax.php?run=wenjian',post,function(data){
		data=html_encode(data)
		var html="<form id='edit'>";
		html+="<div class='tex'><textarea id='wenjian_edit'>"+data+"</textarea></div><div class='cle20'></div>";
		html+="<a class='queren margin_right5' onClick='edit(\""+path+"\")'>确认</a>";
		html+="<span class='ts'></span></form><div class='cle20'></div>";
		$('.edit').html(html);
	});
	$('.wenjian_dh .dq').removeClass('dq');
	$('.'+qukong('dq'+i)).addClass('dq');
}

function pic(i){
	$('.wenjian_dh .dq').removeClass('dq');
	$('.'+qukong('dq'+i)).addClass('dq');
}

function edit(path){
	<?php echo $demo;?>
	var neirong=$('#wenjian_edit').val();
	var post={path:path,neirong:neirong}
	$.post('run_ajax.php?run=wenjian_edit',post,function(data){
		window.location='wenjian.php?path=<?php echo $path; ?>';
	})
}

//图片大小
$("img").mousemove(function(){
	var screenImage = $(this);
	var theImage = new Image();
	theImage.src = screenImage.attr("src");
	var path=theImage.src;
	var filename;
	if(path.indexOf("/")>0){
		filename=path.substring(path.lastIndexOf("/")+1,path.length);
	}
	else{
		filename=path;
	} 
	var imageWidth = theImage.width;
	var imageHeight = theImage.height;
	$('.pic').html('图片名：'+filename+' <br>宽：'+imageWidth+'px， 高：'+imageHeight+'px');
})
</script>