<?php include("class_sql.php");?>
<style type="text/css">
*{ padding:10px 0; margin:0; color:#F00; font-size:12px;}
a{ padding:5px 10px; border:1px solid #1aacda;border-radius:3px; font-size:12px; text-decoration:none; color:#1aacda;}
</style>
<script src="../../common/js/jquery.min.js"></script>
<?php 
if(isset($_GET['install']) && $_SESSION['install']){
	$typeid=0;
}
else if(isset($_GET['id']) && isset($_GET['tid']) && isset($_GET['laiyuan']) && isset($_GET['sou']) && isset($_GET['wid'])){
	$id=$_GET['id'];//对应自动更新系统id
	$tid=$_GET['tid'];//栏目id
	$typeid=$tid;
	$sou=$_GET['sou'];//采集关键词
	$wid=$_GET['wid'];//对应网站id
	$laiyuan=$_GET['laiyuan'];//数据来源
	
	if($laiyuan==0){
		//审核文章然后更新
		$kucuns=$c_sql->select("select id from art where (tid=$tid and fabushijian=1) order by id asc limit 1");
		if(count($kucuns)>0){
			$ids='';
			foreach($kucuns as $arr){
				if($ids==''){$ids=$arr['id'];}
				else{$ids.=','.$arr['id'];}
			}
			
			if(strstr($ids,',')){
				$where="id in({$ids})";
			}
			else{
				$where="id={$ids}";
			}
			$fabushijian=time();
			$sql_up="update art set fabushijian='{$fabushijian}' where {$where}";
			$c_sql->zhixing($sql_up);
		}
		else{
			exit('无库存文章');
		}
		
	}
	else{
		$ciku=ii('ciku');//词库
		$lianjie=ii('lianjie');//链接库
		$tuku=ii('tuku');//图库
		
		$maowenben=ii('maowenben');//锚文本设置1随机，2指定
		$guanjiancishu=ii('guanjiancishu');//关键词插入次数
		$charutupian=ii('charutupian');//插入图片数
		
		//随机关键词
		$cikus=explode("\n",$ciku);
		$ciku_k=rand(0,count($cikus)-1);//随机下标
		$key=$cikus[$ciku_k];
		
		
		//对应链接
		$lianjies=explode("\n",$lianjie);
		if($maowenben==1){
			$lianjie=$lianjies[rand(0,count($cikus)-1)];
		}
		else{
			$lianjie=$lianjies[$ciku_k];
		}
		
		//随机图片
		$tukus=explode(";",$tuku);
		$tupians=array();
		$suoluetu='';
		if($charutupian==1){
			$tupianshu=rand(1,3);
			for ($x=1; $x<=$tupianshu; $x++) {
				$k=rand(0,count($tukus)-1);
				if($x==1){
					$suoluetu=$tukus[$k];
				}
				$tupians[]="\r\n<p style='text-align:center'><img alt='{$key}' src='".$tukus[$k]."' /></p>";
				unset($tukus[$k]);
				$tukus = array_values($tukus);
			} 
		}
		//获取接口文章
		$url="http://www.zhanbangzhu.com/api/seo/api.php?run=zd_gengxin_shuju&wid={$wid}&sou={$sou}";
		$art_json=get($url);

		$arts= json_decode($art_json,true);
		
		//标题处理
		if(rand(1,2)==1){$post_art['biaoti']=$key.$arts[0]['biaoti'];}
		else{$post_art['biaoti']=$arts[0]['biaoti'].$key;}
		
		
		//内容处理
		//插入关键词
		$maowenben="<b><a href='{$lianjie}' title='{$key}' target='_blank'>{$key}</a></b>";
		$neirong=replaceString('。',"。".$maowenben,$arts[0]['neirong'],rand(1,5));
		
		//插入图片
		$neirongs=explode('</p>',$neirong);
		if(count($tupians)>0){
			foreach($tupians as $k=>$v){
				$neirongs[$k]=$neirongs[$k].$v;
			}
		}
		$post_art['neirong']=implode('',$neirongs);
		$post_art['suoluetu']=$suoluetu;
		$post_art['tid']=$tid;
		if($laiyuan==2){$post_art['fabushijian']=1;}//存为待发布文章
		else{$post_art['fabushijian']=time();}//立马发布
		$art_id=$c_sql->insert("art",$post_art);
		$art_id=1;
		if($laiyuan==2){
			exit('采集到库存完成');
		}
	}
}
else{
	exit('参数有误');
}

function replaceString($search,$replace,$content,$limit=-1){
    if(is_array($search)){
        foreach ($search as $k=>$v){
            $search[$k]='`'.preg_quote($search[$k],'`').'`';
        }
    }else{
        $search='`'.preg_quote($search,'`').'`';
    }
    //把描述去掉
    $content=preg_replace("/alt=([^ >]+)/is",'',$content);
    return preg_replace($search,$replace,$content,$limit);
}
?>
<span class="ts"></span>
<script>
html_run('make.php?t=<?php echo $typeid; ?>');//发布后更新
function html_run(url){
	var post={url:url}
	$.post('make_run.php',post,function(res){
		$('.ts').html(res.tiao);
		if(res.tiao==''){
			var tijiao="<iframe src='../../../<?php echo $art_id; ?>.html' height='0' width='0' frameborder='0'></iframe>";
			
			$('.ts').html("<a href='../admin' target='_blank'>前往后台</a> <a target='_blank' href='../../../'>浏览</a>"+tijiao);
			return false;
		}
		html_run('make.php'+qukong(res.tiao));
	},'json');
}

//去除字符串空格
function qukong(str){
	return str.replace(/\s+/g,"");//去除class name 空格
}
</script>