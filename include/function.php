<?php
header("Content-type: text/html; charset=utf-8");
include('class_pinyin.php');
/*删除目录及目录下所有文件或删除指定文件($path待删除目录路径,$delDir1或true删除目录，0或false则只删除文件保留目录*/
function delDirAndFile($path, $delDir = FALSE) {
	if($path=='' || $path=='/' || $path=='./' || $path=='../' || $path=='../../' || $path=='../../../'){
		exit('严禁该操作');
	}
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )){
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return false;
        }
    }
}

//将编码转换成utf8
function utf8($data){
  if( !empty($data) ){
    $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
    if( $fileType != 'UTF-8'){
      $data = mb_convert_encoding($data ,'utf-8' , $fileType);
    }
  }
  return $data;
}

//清楚数组中值字符串两边的空格符（一维数组）
function TrimArray($arr){
	$res=array();
	if(count($arr)>0){
		foreach($arr as $k=>$v){
			$res[$k]=trim($v);
		}
	}
	return $res;
}

//快速生成html
function write($path,$str){
	//违法路径替换
	$str=str_ireplace('..//','../',$str);
	is_dir(dirname($path)) OR mkdir(dirname($path), 0777, true);
	$fp = fopen($path,"w");
	fwrite($fp,$str);
	fclose($fp);
}

//跳转
function tiao($canshu=NULL){
	$url='';
	if(count($canshu)>0){
		$i=0;
		foreach($canshu as $k=>$v){
			if($i==0){$url="?$k=$v";}
			else{$url.="&$k=$v";}
			$i++;
		}
	}
	echo "<script>window.location='$url';</script>";
}

//跳转
function url($canshu=NULL){
	$url='';
	if(count($canshu)>0){
		$i==0;
		foreach($canshu as $k=>$v){
			if($i==0){$url="?$k=$v";}
			else{$url.="&$k=$v";}
			$i++;
		}
	}
	return $url;
}

//汉子转拼音（汉子，all为全拼head为首拼）
function pinyin($str,$lx='all'){
	$py = new PinYin();
	return $py->getpy($str,true); 
}

//所有编码转换为UFT8
function characet($data){
	if( !empty($data) ){
		$fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
		if( $fileType != 'UTF-8'){
			$data = mb_convert_encoding($data ,'utf-8' , $fileType);
		}
	}
	$data=str_ireplace('gb2312','utf-8',$data);
	return $data;
}

//获取文件后缀名
function getExt1($filename)
{
   $arr = explode('.',$filename);
   if(count($arr)>1){
   	return array_pop($arr);
   }
   else{
   	return 1; 
   }
}

//获取当前文件名
function wenjian_dq(){
	$url=$_SERVER['REQUEST_URI'];
	$urls=explode('/',$url);
	$url=$urls[count($urls)-1];
	$urls=explode('?',$url);
	return $urls[0];
}

function jiami($str){
	$str=md5($str);
	$str=md5($str);
	return $str;
}

//字符串base64二次加密
function jiami64($str){
	$a=base64_encode($str);
	$b=base64_encode($a);
	$c=base64_encode($b);
	$d=base64_encode($c);
	return $d;
}

//解密
function jiemi64($str){
	$a=base64_decode($str);
	$b=base64_decode($a);
	$c=base64_decode($b);
	$d=base64_decode($c);
	return $d;
}

//调用参数
function ii($k){
	global $c_sql;
	$infos=$c_sql->select("select neirong,morenzhi from info where diaoyongbiaoqian='{$k}'");
	if(isset($infos[0]['neirong'])){
		if($infos[0]['neirong']==''){
			return $infos[0]['morenzhi'];
		}
		else{
			return $infos[0]['neirong'];
		}
	}
	else{
		return "无法找到<b>{$k}</b>该标签";
	}
}

//分页函数（$total总条数,$pageSize每页显示条数,$showPage=5页码显示几个）
function pageBar($total,$pageSize,$showPage=5){
	if(isset($_GET['p'])){$page = $_GET['p'];}
	else{$page = 1;}
	$totalPage = ceil($total / $pageSize);    //获取总页数
	$pageOffset = ($showPage - 1) / 2;    //页码偏移量
	$pageBanner = "";
	$pageSelf = $_SERVER['PHP_SELF'];//当前文件
	
	/*带参数的处理S*/
	$pageget = $_SERVER['REQUEST_URI'];//带参数
	$get=str_replace($pageSelf,'',$pageget);
	if(strstr($get,'?')){
		$get=str_replace('?','',$get);
		$gets=explode("&",$get);
		$p='';
		if(count($gets)>0){
			foreach($gets as $v){
				if(strstr($v,'p=')){
					$p=$v;
				}
			}
		}
		$pageSelf=str_replace($p,'',$pageget);
		if(!strstr($pageSelf,'&')){
			$pageSelf.='&';
		}
	}
	else{
		$pageSelf=$pageSelf.'?';
	}
	/*带参数的处理E*/
	
	$start = 1;    //开始页码
	$end = $totalPage;    //结束页码    
	if($page > 1){
		$pageBanner .= "<a href='".$pageSelf."p=1'>首页</a>";
		$pageBanner .= "<a href='".$pageSelf."p=".($page - 1)."'>上一页</a>";
	}
	if($totalPage > $showPage){    //当总页数大于显示页数时
		if($page > $pageOffset){        //当当前页大于页码偏移量时 开始页码变为当前页-偏移页码
			$start = $page - $pageOffset;
			$end = $totalPage > $page + $pageOffset ?  $page + $pageOffset : $totalPage;
		//如果当前页数+偏移量大于总页数 那么$end为总页数
		}
		else{
			$start = 1;
			$end = $totalPage > $showPage ? $showPage : $totalPage;
		}
		if($page + $pageOffset > $totalPage){
			$start = $start - ($page + $pageOffset - $end);
		}
	}
	for($i = $start ; $i <= $end ; $i++){    //循环出页码
		if($i == $page){
			$pageBanner .= "<span>".$i."</span>";
		}
		else{
			$pageBanner .= "<a href='".$pageSelf."p=".$i."'>".$i."</a>";
		}
	}
	if($page < $totalPage){
		$pageBanner .= "<a href='".$pageSelf."p=".($page + 1)."'>下一页</a>";
		$pageBanner .= "<a href='".$pageSelf."p=".$totalPage."'>末页</a>";
	}
	if($pageBanner=='<span>1</span>'){
		$pageBanner='';
	}
	return $pageBanner;
}

//生成分页函数（$total总条数,$pageSize每页显示条数,$showPage=5页码显示几个）
function make_pageBar($total,$pageSize,$showPage=5,$baocunlujing,$page){
	$totalPage = ceil($total / $pageSize);    //获取总页数
	$pageOffset = ($showPage - 1) / 2;    //页码偏移量
	$pageBanner = "";
	$start = 1;    //开始页码
	$end = $totalPage;    //结束页码
	//筛选
	if(isset($_GET['s'])){
		if($page > 1){
			$_GET['ym']='list_0_1';
			$pageBanner .= "<a href='".url($_GET)."'>首页</a>"."\r\n";
			if($page==2){
				$pageBanner .= "<a href='".url($_GET)."'>上一页</a>"."\r\n";
			}
			else{
				$_GET['ym']='list_0_'.($page - 1);
				$pageBanner .= "<a href='".url($_GET)."'>上一页</a>"."\r\n";
			}
			
			
		}
		if($totalPage > $showPage){    //当总页数大于显示页数时
			if($page > $pageOffset){        //当当前页大于页码偏移量时 开始页码变为当前页-偏移页码
				$start = $page - $pageOffset;
				$end = $totalPage > $page + $pageOffset ?  $page + $pageOffset : $totalPage;
			//如果当前页数+偏移量大于总页数 那么$end为总页数
			}
			else{
				$start = 1;
				$end = $totalPage > $showPage ? $showPage : $totalPage;
			}
			if($page + $pageOffset > $totalPage){
				$start = $start - ($page + $pageOffset - $end);
			}
		}
		for($i = $start ; $i <= $end ; $i++){    //循环出页码
			if($i == $page){
				$pageBanner .= "<span>".$i."</span>"."\r\n";
			}
			else{
				$_GET['ym']='list_0_'.$i;
				$pageBanner .= "<a href='".url($_GET)."'>".$i."</a>"."\r\n";
			}
		}
		if($page < $totalPage){
			$_GET['ym']='list_0_'.($page + 1);
			$pageBanner .= "<a href='".url($_GET)."'>下一页</a>"."\r\n";
			$_GET['ym']='list_0_'.$totalPage;
			$pageBanner .= "<a href='".url($_GET)."'>末页</a>"."\r\n";
		}
		if($pageBanner=="<span>1</span>\r\n"){
			return '';
		}
	}
	
	//非筛选
	else{
		if($page > 1){
			$pageBanner .= "<a href='{$baocunlujing}.html'>首页</a>"."\r\n";
			if($page==2){
				$pageBanner .= "<a href='{$baocunlujing}.html'>上一页</a>"."\r\n";
			}
			else{
				$pageBanner .= "<a href='{$baocunlujing}_".($page - 1).".html'>上一页</a>"."\r\n";
			}
			
			
		}
		if($totalPage > $showPage){    //当总页数大于显示页数时
			if($page > $pageOffset){        //当当前页大于页码偏移量时 开始页码变为当前页-偏移页码
				$start = $page - $pageOffset;
				$end = $totalPage > $page + $pageOffset ?  $page + $pageOffset : $totalPage;
			//如果当前页数+偏移量大于总页数 那么$end为总页数
			}
			else{
				$start = 1;
				$end = $totalPage > $showPage ? $showPage : $totalPage;
			}
			if($page + $pageOffset > $totalPage){
				$start = $start - ($page + $pageOffset - $end);
			}
		}
		for($i = $start ; $i <= $end ; $i++){    //循环出页码
			if($i == $page){
				$pageBanner .= "<span>".$i."</span>"."\r\n";
			}
			else{
				if($i==1){
					$pageBanner .= "<a href='{$baocunlujing}.html'>".$i."</a>"."\r\n";
				}
				else{
					$pageBanner .= "<a href='{$baocunlujing}_".$i.".html'>".$i."</a>"."\r\n";
				}
			}
		}
		if($page < $totalPage){
			$pageBanner .= "<a href='{$baocunlujing}_".($page + 1).".html'>下一页</a>"."\r\n";
			$pageBanner .= "<a href='{$baocunlujing}_".$totalPage.".html'>末页</a>"."\r\n";
		}
		if($pageBanner=="<span>1</span>\r\n"){
			return '';
		}
	}
	
	
	
	return $pageBanner;
}
//获取当前网址
function url_dq(){
	if($_SERVER['REQUEST_SCHEME']==''){$http='http';}
	else{$http=$_SERVER['REQUEST_SCHEME'];}
	$www=$http.'://';
	$www.=$_SERVER['HTTP_HOST'];
	$www.=$_SERVER['REQUEST_URI'];
	return $www;
}

//当前网址根目录
function web_dq(){
	$url=url_dq();
	$urls=explode('/cms/',$url);
	return $urls[0];
}

//读取文件方便编码处理
function get($path){
	if(!file_exists($path)){
		return '文件<b>'.$path.'</b>不存在！';
	}
	$get=file_get_contents($path);
	return $get;
}

//去除html标签
function cutstr_html($string){  
    $string = strip_tags($string);  
    $string = trim($string);  
    $string = str_ireplace("\t","",$string);
    $string = str_ireplace("\r\n","",$string);  
    $string = str_ireplace("\r","",$string);  
    $string = str_ireplace("\n","",$string);  
    $string = str_ireplace(" ","",$string);
	$string = str_ireplace("","",$string);
	$string = str_ireplace("&nbsp;","",$string);
	$string = str_ireplace("&ldquo;","",$string);
	$string = str_ireplace("&rdquo;","",$string);
	$string = str_ireplace("&quot;","",$string);
	$string = str_ireplace("&mdash;","",$string);
	$string = str_ireplace("-","",$string);
	
	//序列替换
	$string = str_ireplace("1、","<br/>1、",$string);
	$string = str_ireplace("2、","<br/>2、",$string);
	$string = str_ireplace("3、","<br/>3、",$string);
	$string = str_ireplace("4、","<br/>4、",$string);
	$string = str_ireplace("5、","<br/>5、",$string);
	$string = str_ireplace("6、","<br/>6、",$string);
	$string = str_ireplace("7、","<br/>7、",$string);
	$string = str_ireplace("8、","<br/>8、",$string);
	$string = str_ireplace("9、","<br/>9、",$string);
	
	$string = str_ireplace("1,","<br/>1、",$string);
	$string = str_ireplace("2,","<br/>2、",$string);
	$string = str_ireplace("3,","<br/>3、",$string);
	$string = str_ireplace("4,","<br/>4、",$string);
	$string = str_ireplace("5,","<br/>5、",$string);
	$string = str_ireplace("6,","<br/>6、",$string);
	$string = str_ireplace("7,","<br/>7、",$string);
	$string = str_ireplace("8,","<br/>8、",$string);
	$string = str_ireplace("9,","<br/>9、",$string);
	
	$string = str_ireplace("1，","<br/>1、",$string);
	$string = str_ireplace("2，","<br/>2、",$string);
	$string = str_ireplace("3，","<br/>3、",$string);
	$string = str_ireplace("4，","<br/>4、",$string);
	$string = str_ireplace("5，","<br/>5、",$string);
	$string = str_ireplace("6，","<br/>6、",$string);
	$string = str_ireplace("7，","<br/>7、",$string);
	$string = str_ireplace("8，","<br/>8、",$string);
	$string = str_ireplace("9，","<br/>9、",$string);
	
    return trim($string);  
}

//反斜杠处理
function xiegang($str){
	if(get_magic_quotes_gpc()){
		return stripslashes($str);//将字符串进行处理
	}
	else{
		return $str;
	}
}

//文字UTF8个数截取
function jiequ($str,$len){
	$str=cutstr_html($str);
	$str=str_ireplace("<br/>",'',$str);
	$str=str_ireplace("\r",'',$str);
	$str=str_ireplace("\n",'',$str);
	
	$str_ys=$str;
	if($len<=0){
		return $str;
	}
	else{
		for($i=0;$i<$len;$i++){
			$temp_str=substr($str,0,1);
			if(ord($temp_str) > 127){
				if($i<$len){
					$new_str[]=substr($str,0,3);
					$str=substr($str,3);
				}
			}else{
				$new_str[]=substr($str,0,1);
				$str=substr($str,1);
			}
		}
		$str_jq=join($new_str);
		if($str_ys!=$str_jq){
			$str_jq=$str_jq."...";
		}
		return $str_jq;
	}
}

/* 
*功能：php完美实现下载远程图片保存到本地 
*参数：文件url,保存文件目录,保存文件名称，使用的下载方式 
*当保存文件名称为空时则使用远程文件原来的名称 
*/ 
function getImage($url,$save_dir='',$filename='',$type=0){
    if(trim($url)==''){
        return array('file_name'=>'','save_path'=>'','error'=>1);
    }
    if(trim($save_dir)==''){
        $save_dir='./';
    }
    if(trim($filename)==''){//保存文件名
        $ext=strrchr($url,'.');
        if(!stristr($ext,'.gif') && !stristr($ext,'.jpg') && !stristr($ext,'.png')){
            return array('file_name'=>'','save_path'=>'','error'=>3);
        }
		$filename=strrchr($url,'/');
		$filename=str_ireplace('/','',$filename);
    }
    if(0!==strrpos($save_dir,'/')){
        $save_dir.='/';
    }
    //创建保存目录
    if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
        return array('file_name'=>'','save_path'=>'','error'=>5);
    }
    //获取远程文件所采用的方法
    if($type){
        $ch=curl_init();
        $timeout=5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $img=curl_exec($ch);
        curl_close($ch);
    }else{
        ob_start();
        readfile($url);
        $img=ob_get_contents();
        ob_end_clean();
    }
    //$size=strlen($img);
    //文件大小
    $fp2=@fopen($save_dir.$filename,'a');
    fwrite($fp2,$img);
    fclose($fp2);
    unset($img,$url);
    return $save_dir.$filename;
}

//安全策略管理员登陆
function login_input($input){
    $clean = strtolower($input);
    $clean = preg_replace("/[^a-z][^0-9]/","", $clean);
    $clean = substr($clean,0,100);
    return $clean;
}

//字符串只留数字
function shuzi($str){
	if(preg_match('/\d+/',$str,$arr)){
		return $arr[0];
	}
	else{
		return  $str;
	}
}
?>