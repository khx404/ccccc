<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>生成静态页面</title>
<script src="../../common/js/jquery.min.js"></script>
</head>
<body>
<span id="tishi"></span>
<?php 
include('../include/class_sql.php');
//详情页总数
$art_counts=$c_sql->select("select count(id) from art");
$art_count=$art_counts[0]['count(id)'];

//栏目页总数
$type_counts=$c_sql->select("select count(id) from type");
$type_count=$type_counts[0]['count(id)'];

//更新总页数
$zong=$art_count+$type_count+1;
?>


<script>
var art_count=<?php echo $art_count; ?>;
var type_count=<?php echo $type_count; ?>;
var zong=<?php echo $zong; ?>;

make(1);
function make(jindu){
	
	var url='ajax.php?';
	//判断生成内容页
	if(jindu<=art_count){
		url+='run=html&table=art&limit='+jindu;
	}
	else if(jindu>art_count && jindu<=(art_count+type_count)){
		url+='run=html&table=type&limit='+(jindu-art_count);
	}
	else{
		url+='run=html&table=index';
	}
	$.post(url,{},function(res){
		jindu++;
		if(jindu<=zong){
			$('#tishi').html(res);
			
			$.post(res,{},function(res){
				make(jindu)
			})
			
			;
		}
		else{
			$('#tishi').html('ok');
			return false;
		}
		
	});
}
</script>

</body>
</html>