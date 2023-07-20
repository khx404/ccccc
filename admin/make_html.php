<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>生成html</title>
</head>
<body>
<script src="../../common/js/jquery.min.js"></script>
<p class="tishi"></p>

<script>
function make(post){
	$.post('ajax.php?run=html',post,function(res){alert(res);
		if(res=='ok'){
			$('.tishi').html(res);
			return;
		}
		var ms=res['ms'];
		var df=res['df'];
		var table=res['table'];
		var limit=res['limit'];
		var tishi=res['tishi'];
		var post={'ms':ms,'df':df,'table':table,'limit':limit};
		$('.tishi').html(tishi);
		make(post);
	},'json');
}
var post='{}';
make(post);
</script>

</body>
</html>