<?php 
include('../include/class_sql.php');
if(!isset($_GET['run'])){
	exit("参数有误");
}
$run=$_GET['run'];
//登陆
if($run=='login'){
	$guanliyuan=login_input($_POST['guanliyuan']);
	$pwd=jiami(login_input($_POST['pwd']));
	$admins=$c_sql->select("select * from admin where (guanliyuan='{$guanliyuan}' and mima='{$pwd}') limit 1");
	if(count($admins)>0){
		$_SESSION['id']=$admins[0]['id'];
		$_SESSION['guanliyuan']=$admins[0]['guanliyuan'];
		$res=$admins[0]['dengji'];//等级
		exit($res);
	}
	else{
		exit('0');
	}
}

//退出登陆
if($run=='logontui'){
	$_SESSION['id']='';
	$_SESSION['guanliyuan']='';
	$_SESSION['shaohou']='';
}

//判断是否登陆

if($run=='paiming_du'){
	if(isset($_SESSION['paiming'])){
		if(time()-$_SESSION['paiming']<86400){echo 0;}
		else{echo 1;}
	}
	else{echo 1;}
}

//2019/5/5增删改
if($run=='add'){
	echo $c_sql->insert($_GET['table'],$_POST);
}

if($run=='del'){
	echo $c_sql->delete($_GET['table'],$_GET['where']);
}

if($run=='edit'){
	echo $c_sql->update($_GET['table'],$_POST,$_GET['where']);
}

if($run=='jtbaocunlujing'){
	$lanmumingcheng=$_POST['lanmumingcheng'];
	echo pinyin($lanmumingcheng,$lx='all');
}


//联动子id
if($run=='ld_zi_id'){
	$result=array();
	$id=$_GET['id'];
	$zis=$c_sql->digui('liandong','lid',$lid=$id,$result,$spac=0);
	$res=array();
	foreach($zis as $arr){
		$res[]=$arr['id'];
	}
	echo json_encode($res);//返回json
}

//栏目子id
if($run=='type_zi_id'){
	$result=array();
	$id=$_GET['id'];
	$zis=$c_sql->digui('type','tid',$lid=$id,$result,$spac=0);
	$res=array();
	foreach($zis as $arr){
		$res[]=$arr['id'];
	}
	echo json_encode($res);//返回json
}

//栏目移动
if($run=='typeyidong'){
	$post['tid']=$_POST['tid'];
	echo $c_sql->update('type',$post,"id=".$_POST['id']);
}

if($run=='moxing_liandong'){
	$name=$_POST['name'];
	$liandongs=$c_sql->select("select id from liandong where name='{$name}'");
	if(isset($liandongs[0]['id'])){
		echo $liandongs[0]['id'];
	}
	else{
		echo 0;
	}
}

if($run=='youad'){
	if($_POST['id']==0){
		unset($_POST['id']);
		echo $c_sql->insert('youad',$_POST);
	}
	else{
		$id=$_POST['id'];
		unset($_POST['id']);
		echo $c_sql->update('youad',$_POST,"id=$id");
	}
}

if($run=='youad_pic'){
	$path='../../upload/';
	$path_res='../../upload/';
	$res='';
	foreach($_FILES as $i=> $arr){
		$tmp_name=$arr['tmp_name'];//临时文件
		
		if($data_pic_name==0){
			//后缀
			$houzhuis=explode('.',$arr['name']);
			$houzhui=$houzhuis[count($houzhuis)-1];
			$pathurl=$path.time().'_'.$i.'.'.$houzhui;
		}
		else{
			$pathurl=$path.$arr['name'];
		}
		
		is_dir($path) OR mkdir($path, 0777, true);//文件夹不存在创建文件夹
		$pathurl=iconv("UTF-8","gb2312",$pathurl);//目标路径
		if(move_uploaded_file($tmp_name,$pathurl)){
			$pathurl_res=str_ireplace($path,$path_res,$pathurl);
			
			$res.=$pathurl_res;
		}
	}
	echo $res;
}


//留言一看
if($run=='liuyan_yikan'){
	$id=$_POST['id'];
	$post['paixu']=2;
	echo $c_sql->update('info',$post,"id=$id");
}

//留言提醒
if($run=='liuyan_tx'){
	$tiaos=$c_sql->select("select * from info where (shuyu=3 and paixu=0)");
	if(count($tiaos)>0){
		echo 1;
		$post['paixu']=1;
		$c_sql->update('info',$post,'paixu=0');
	}
}

//info编辑提交
if($run=='post_info'){
	$sql="UPDATE info"."\r\n";
	$sql.="SET neirong = CASE id"."\r\n";
	$tiaojians='';
	$i=0;
	foreach($_POST as $k=>$v){
		$sql.="WHEN '$k' THEN '".$c_sql->chuli_sql($v)."'"."\r\n";
		if($i==0){
			$tiaojians="'{$k}'";
		}
		else{
			$tiaojians.=",'{$k}'";
		}
		$i++;
	}
	$sql.="END"."\r\n";
	$sql.="WHERE id IN ({$tiaojians})"."\r\n";
	echo $c_sql->zhixing($sql);
	file_put_contents('../../../robots.txt',ii('robots'));//robots设置
}

//info新增参数提交
if($run=='info_add'){
	//判断该标签是否以经存在
	$_POST['paixu']=50;
	$_POST['shuyu']=2;
	echo $c_sql->insert('info',$_POST);
}

//info删除参数
if($run=='info_shan'){
	$id=$_POST['id'];
	echo $c_sql->delete('info',"id={$id}");
}

//moxing新增
if($run=='moxing_add'){
	$_POST['mid']=0;
	$_POST['paixu']=50;
	$_POST=TrimArray($_POST);
	echo $c_sql->insert('moxing',$_POST);
}



//moxing新增字段，art表对应新增字段
if($run=='moxing_ziduan_add'){
	$id=$_POST['id'];
	$mid=$_POST['mid'];
	$diaoyongmingcheng=$_POST['diaoyongmingcheng'];
	$morenzhi=$_POST['morenzhi'];
	$leixing=$_POST['leixing'];
	$bitian=$_POST['bitian'];
	//新增字段
	if($mid==0){
		//模型
		$post_mx=array();
		$post_mx['mid']=$id;
		$post_mx['diaoyongmingcheng']=$diaoyongmingcheng;
		$post_mx['leixing']=$leixing;
		$post_mx['morenzhi']=$morenzhi;
		$post_mx['paixu']=50;
		$post_mx['bitian']=$bitian;
		$res=$c_sql->insert('moxing',$post_mx);
		
		if($res!=0){
			$ziduan=pinyin($diaoyongmingcheng,$lx='all');//字段名 radio checkbox body
			if($leixing=='input' || $leixing=='option' || $leixing=='radio' || $leixing=='checkbox' || $leixing=='file'){
				$lx='varchar(400)';
			}
			else if($leixing=='textarea' || $leixing=='body'){
				$lx='mediumtext';
			}
			else if($leixing=='liandong'){
				$lx='char(50)';
			}
			$sql_z="alter table art add $ziduan $lx";
			$c_sql->zhixing($sql_z);
			echo $res;
		}
	}
	//编辑字段
	else{
		$where="id=$id";
		$post_mx_edit['bitian']=$bitian;
		$post_mx_edit['morenzhi']=$morenzhi;
		$c_sql->update('moxing',$post_mx_edit,$where);
		echo $mid;
	}
}
//删除字段
if($run=='delziduan'){
	$id=$_POST['id'];
	$moxing0=$c_sql->select("select * from moxing where id={$id}");
	$moxings=$c_sql->digui('moxing','mid',$id,$result,$spac=0);
	if(count($moxings)>0){
		$moxings=array_merge($moxing0,$moxings);
	}
	else{
		$moxings=$moxing0;
	}
	$ids[]=$id;
	if(count($moxings)>0){
		foreach($moxings as $arr){
			$ziduan=pinyin($arr['diaoyongmingcheng'],$lx='all');
			$sql="ALTER TABLE art DROP COLUMN {$ziduan}";
			$c_sql->zhixing($sql);//删除表art对应字段
			$ids[]=$arr['id'];
		}	
	}
	$id=implode(',',$ids);
	$sql="delete from moxing where id in ({$id})";
	$res=$c_sql->zhixing($sql);
	if($res==1){
		echo json_encode($ids);
	}
	else{
		exit(0);
	}
}

//新增栏目提交
if($run=='type_add'){
	$_POST['fulanmumingcheng']=pinyin($_POST['lanmumingcheng'],$lx='head');
	$_POST['youhuabiaoti']=$_POST['lanmumingcheng'];
	$_POST['youhuaguanjianci']=$_POST['lanmumingcheng'];
	$_POST['youhuazhaiyao']=$_POST['lanmumingcheng'];
	$_POST['paixu']=50;
	$jibie=$_POST['jibie'];
	unset($_POST['jibie']);
	$typeid=$c_sql->insert('type',$_POST);
	
	if($_POST['tid']==0){
		$red['id']=$typeid;
		$red['tid']=$_POST['tid'];
		$red['jibie']=$jibie+1;
		$red['weizhi_id']=0;
	}
	else{
		$diguis=$c_sql->digui('type','tid',$_POST['tid'],$result);
		$red['id']=$typeid;
		$red['tid']=$_POST['tid'];
		$red['jibie']=$jibie+1;
		foreach($diguis as $k=>$dgs){
			if($typeid==$dgs['id']){
				$red['weizhi_id']=$diguis[$k-1]['id'];
			}
		}
		if(!isset($red['weizhi_id'])){
			$red['weizhi_id']=$_POST['tid'];
		}
		
	}
	echo json_encode($red);
}

//删除栏目
if($run=='deltype'){
	$id=$_POST['id'];
	$typeids=$c_sql->digui('type','tid',$id,$result,$spac=0);
	$ids[]=$id;
	if(count($typeids)>0){
		foreach($typeids as $arr){
			$ids[]=$arr['id'];
		}	
	}
	$id=implode(',',$ids);
	$sql="delete from type where id in ({$id})";
	$res=$c_sql->zhixing($sql);
	if($res==1){
		echo json_encode($ids);
	}
	else{
		exit(0);
	}
}

//显示隐藏
if($run=='type_run'){
	$post['run']=$_POST['run'];
	echo $c_sql->update('type',$post,"id=".$_POST['id']);
}

//编辑栏目
if($run=='edit_type'){
	//下载远程图片到本地S
	preg_match_all('/<img .*?src=["|\'](.*?)["|\']/ism',$_POST['lanmuneirong'],$tupians);
	if(count($tupians[1])>0){
		foreach($tupians[1] as $v){
			if(strstr($v,'http://') || strstr($v,'https://')){
				$tupian_src=getImage($v,"../../upload");
				$_POST['lanmuneirong']=str_ireplace($v,$tupian_src,$_POST['lanmuneirong']);
			}
		}
	}
	//下载远程图片到本地E
	$where="id=".$_POST['id'];
	unset($_POST['id']);
	echo $c_sql->update('type',$_POST,$where);
}

//内容
if($run=='art'){
	//下载远程图片到本地S
	foreach($_POST as $kk=>$vv){
		if (get_magic_quotes_gpc()){
			$vv=stripslashes($vv);
		}
		$tupians=array();
		preg_match_all('/<img .*?src=["|\'](.*?)["|\']/ism',$vv,$tupians);
		if(count($tupians[1])>0){
			foreach($tupians[1] as $v){
				if(strstr($v,'http://') || strstr($v,'https://')){
					$tupian_src=getImage($v,"../../upload");
					$vv=str_ireplace($v,$tupian_src,$vv);
				}
			}
			$_POST[$kk]=$vv;
		}
	}
	//下载远程图片到本地E
	
	//提取缩略图S
	if($_POST['suoluetu']==''){
		foreach($_POST as $sv){
			preg_match_all('/<img .*?src=["|\'](.*?)["|\']/ism',$sv,$slts);
			if(isset($slts[1][0])){
				$_POST['suoluetu']=$slts[1][0];
				break;
			}
		}
	}
	//提取缩略图E
	
	//提取摘要S
	if($_POST['zhaiyao']==''){
		foreach($_POST as $zyv){
			if(strlen($zyv)>100){
				$_POST['zhaiyao']=jiequ($zyv,200);
				break;
			}
		}
	}
	//提取摘要E
		
	if($_POST['fabushijian']!='1'){
		$_POST['fabushijian']=strtotime($_POST['fabushijian']);
	}
	//新增
	if($_POST['id']==0){
		unset($_POST['id']);
		echo $c_sql->insert('art',$_POST);
	}
	//编辑
	else{
		$id=$_POST['id'];
		$where="id=$id";
		unset($_POST['id']);
		$c_sql->update('art',$_POST,$where);
		echo $id;
	}
}

//删除文章
if($run=='artdel'){
	$ids=$_POST['id'];
	$ids=str_ireplace(';',',',$ids);
	if(strstr($ids,',')){
		$where="id in($ids)";
	}
	else{
		$where="id=$ids";
	}
	$c_sql->delete('art',$where);
	$ids=explode(',',$ids);
	echo json_encode($ids);
}

//文章内容转移
if($run=='artzhuanyi'){
	$post['tid']=$_POST['tid'];
	$ids=$_POST['yixuan'];
	
	$ids=str_ireplace(';',',',$ids);
	if(strstr($ids,',')){
		$where="id in($ids)";
	}
	else{
		$where="id=$ids";
	}
	$c_sql->update('art',$post,$where);
	$ids=explode(',',$ids);
	echo json_encode($ids);
}

//文件
if($run=='wenjian'){
	$path=$_POST['path'];
	echo get($path);
}

//文件编辑保存
if($run=='wenjian_edit'){
	$path=$_POST['path'];
	$neirong=$_POST['neirong'];
	//如果get_magic_quotes_gpc()是打开的
	if(get_magic_quotes_gpc()){
		$neirong=stripslashes($neirong);//将字符串进行处理
	}
	echo file_put_contents($path,$neirong);
}

//删除文件
if($run=='delpath'){
	$path=$_POST['path'];
	delDirAndFile($path, $delDir = true);
}

//新增联动名称
if($run=='liandong_add_but'){
	echo $c_sql->insert('liandong',$_POST);
}

//新增联动名称提交
if($run=='liandong_add_zi_but'){
	$id=$_POST['id'];
	$names=$_POST['names'];
	$names=explode("\n",$names);
	
	$sql="insert into liandong (lid,name,paixu) values"."\r\n";
	$i=0;
	foreach($names as $name){
		if($name!=''){
			if($i==0){$sql.="($id,'{$name}',50)";}
			else{$sql.=",($id,'{$name}',50)";}
			$i++;
		}
	}
	$id_chushi=$c_sql->select("select id from liandong order by id desc limit 1");//初始id
	$id_chushi=$id_chushi[0]['id'];
	$res=$c_sql->zhixing($sql);
	if($res==1){
		$liandongs=$c_sql->digui('liandong','lid',$id,$result,$spac=0);
		$res_arr[0]['weizhi']=0;
		$ii=0;
		foreach($liandongs as $k=>$arr){
			if($id_chushi<$arr['id']){
				if($res_arr[0]['weizhi']==0){
					if(isset($liandongs[$k-1]['id'])){
						$res_arr[0]['weizhi']=$liandongs[$k-1]['id'];
					}
					else{
						$res_arr[0]['weizhi']=$id;
					}
				}
				$res_arr[$ii]['id']=$arr['id'];
				$res_arr[$ii]['lid']=$arr['lid'];
				$res_arr[$ii]['name']=$arr['name'];
				$ii++;
			}
		}
		echo json_encode($res_arr);
	}
	else{
		echo 0;	
	}
}
//删除联动
if($run=='delliandong'){
	$id=$_POST['id'];
	$liandongids=$c_sql->digui('liandong','lid',$id,$result,$spac=0);
	$ids[]=$id;
	if(count($liandongids)>0){
		foreach($liandongids as $arr){
			$ids[]=$arr['id'];
		}	
	}
	$id=implode(',',$ids);
	$sql="delete from liandong where id in ({$id})";
	$res=$c_sql->zhixing($sql);
	if($res==1){
		echo json_encode($ids);
	}
	else{
		exit(0);
	}
}

//联动展示
if($run=='liandong'){
	$id=$_POST['id'];
	$lds=$c_sql->select("select * from liandong where lid={$id}");
	echo json_encode($lds);
}

//管理员添加编辑
if($run=='admin'){
	$_POST['mima']=jiami(login_input($_POST['mima']));
	$_POST['guanliyuan']=login_input($_POST['guanliyuan']);
	$_POST['chuangjianshijian']=time();
	echo $c_sql->insert('admin',$_POST);
}

//多地方站点
if($run=='difangzhan'){
	echo $c_sql->update('info',$_POST,"diaoyongbiaoqian='difangzhan'");
}



//删除管理员
if($run=='deladmin'){
	$id=$_POST['id'];
	echo $c_sql->delete('admin',"id=$id");
}

//编辑管理员
if($run=='admin_edit'){
	$id=$_POST['id'];
	$guanliyuan=login_input($_POST['guanliyuan']);
	$guanliyuan1=login_input($_POST['guanliyuan1']);
	$mima=jiami(login_input($_POST['mima']));
	$mima1=jiami(login_input($_POST['mima1']));
	$res=$c_sql->select("select * from admin where(id=$id and guanliyuan='$guanliyuan' and mima='{$mima}')");
	if(count($res)<1){
		echo 0;
	}
	else{
		$post['guanliyuan']=$guanliyuan1;
		$post['mima']=$mima1;
		$c_sql->update('admin',$post,"id=$id");
		echo 1;
	}
}

//文章属性设置
if($run=='shuxing'){
	$id=$_POST['id'];
	$post['tuijian']=$_POST['tuijian'];
	echo $c_sql->update('art',$post,"id={$id}");
}

//图库词库编辑
if($run=='post_tukuciku'){
	$c_sql->update('info',array('neirong'=>$_POST['ciku']),"diaoyongbiaoqian='ciku'");
	$c_sql->update('info',array('neirong'=>$_POST['lianjie']),"diaoyongbiaoqian='lianjie'");
	$c_sql->update('info',array('neirong'=>$_POST['maowenben']),"diaoyongbiaoqian='maowenben'");
	$c_sql->update('info',array('neirong'=>$_POST['guanjiancishu']),"diaoyongbiaoqian='guanjiancishu'");
	$c_sql->update('info',array('neirong'=>$_POST['charutupian']),"diaoyongbiaoqian='charutupian'");
	$c_sql->update('info',array('neirong'=>$_POST['tuku']),"diaoyongbiaoqian='tuku'");
	echo 1;
}

//栏目排序
if($run=='paixu_type'){
	if(preg_match("/ ^ [1-9] [0-9] * $ /",$_POST['paixu'])){
    	exit(0);
	}
	$post['paixu']=$_POST['paixu'];
	echo $c_sql->update('type',$post,"id=".$_POST['id']);
}

//模型排序
if($run=='paixu_moxing'){
	if(preg_match("/ ^ [1-9] [0-9] * $ /",$_POST['paixu'])){
    	exit(0);
	}
	$post['paixu']=$_POST['paixu'];
	echo $c_sql->update('moxing',$post,"id=".$_POST['id']);
}

//模型字段默认值
if($run=='moxing_morenzhi'){
	$morenzhis=$c_sql->select("select morenzhi,bitian from moxing where id=".$_POST['id']);
	$res['morenzhi']=$morenzhis[0]['morenzhi'];
	if($morenzhis[0]['bitian']==1){
		$res['bitian']=1;
	}
	else{
		$res['bitian']=0;
	}
	
	echo json_encode($res);
}

//编辑字段默认值
if($run=='edit_moxing_morenzhi'){
	echo $c_sql->update('moxing',$_POST,"id=".$_GET['id']);
}


//模型排序
if($run=='paixu_liandong'){
	if(preg_match("/ ^ [1-9] [0-9] * $ /",$_POST['paixu'])){
    	exit(0);
	}
	$post['paixu']=$_POST['paixu'];
	echo $c_sql->update('liandong',$post,"id=".$_POST['id']);
}

//编辑联动
if($run=='edit_liandong'){
	$id=$_GET['id'];
	echo $c_sql->update('liandong',$_POST,"id=$id");
}

//文件安全检测
if($run=='anquan'){
	if($_SESSION['shaohou']==1){}
	//主程序目录检测
	else if(file_exists('../../cms')){echo "1";}
	else if(file_exists('../admin')){echo "2";}
}

//文件安全检测
if($run=='anquan_run'){
	$cms_oldname=$_POST['cms_oldname'];
	$admin_oldname=$_POST['admin_oldname'];
	$cms=$_POST['cms'];
	$admin=$_POST['admin'];
	$admin_url=ii('电脑站网址');
	
	if(stristr($admin_url,'http://') || stristr($admin_url,'https://')){$admin_url.=cms.'/';}
	else{$admin_url='http://'.$_SERVER['HTTP_HOST'].ii('电脑站网址').cms.'/';}

	if($_GET['i']==1){
		rename("../{$admin_oldname}","../{$admin}");
		echo 2;
	}
	else if($_GET['i']==2){
		if(rename("../../{$cms_oldname}","../../{$cms}")){
			echo $admin_url.$cms.'/'.$admin;//返回新后台地址
		}
	}
}

//稍后处理
if($run=='shaohou'){
	if($_SESSION['shaohou']='1'){
		echo 1;
	}
}

//批量上传txt文章
if($run=='uptxt'){
	$id=$_GET['id'];
	$chatu=$_GET['chatu'];
	$fabushijian=$_GET['fabushijian'];
	
	foreach($_FILES as $k=>$arr){
		$tmp_name=$arr['tmp_name'];//临时文件
		$path='uptxt';
		is_dir($path) OR mkdir($path, 0777, true);//文件夹不存在创建文件夹
		$pathurl=$path.'/'.$arr['name'];
		$pathurl=utf8($pathurl);
		if($arr['error']==0){
			if(!move_uploaded_file($tmp_name,iconv("UTF-8","gb2312",$pathurl))){
				move_uploaded_file($tmp_name,$pathurl);
			}
			$gml='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];//当前文件夹
			$gml=dirname($gml);
			$neirong=file_get_contents($gml.'/'.$pathurl);
			$neirong=utf8($neirong);
			$neirong='<p>'.str_ireplace("\r\n","</p><p>",$neirong).'</p>';
			if($chatu==1){
				$tuku=ii('tuku');//图库
				$tukus=explode(";",$tuku);
				$tupians=array();
				$suoluetu='';
	
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
					
				$neirongs=explode('</p>',$neirong);
				if(count($tupians)>0){
					foreach($tupians as $k=>$v){
						$neirongs[$k]=$neirongs[$k].$v;
					}
				}
				$neirong=implode('',$neirongs);
			}
				
			$art['tid']=$id;
			$art['biaoti']=str_ireplace('.txt','',$arr['name']);
			$art['neirong']=$neirong;
			$art['zhaiyao']=jiequ($neirong,100);//截取摘要
			$art['suoluetu']=$suoluetu;
			$art['paixu']=50;
			$art['fabudao']=0;
			$art['zuozhe']=$_SESSION['guanliyuan'];
			if($fabushijian==0){
				$art['fabushijian']=1;
			}
			else{
				$art['fabushijian']=time();
			}
			$c_sql->insert('art',$art);
		}
	}
	delDirAndFile($path,1);
}

/*自动采集更新数据对接S*/

//自动更新提交
if($run=='gengxin_add'){
	$canshu='';
	foreach($_POST as $k=>$v){$canshu.="&$k=$v";}
	echo get('http://www.zhanbangzhu.com/api/seo/api.php?run=gengxin_add&web='.web_dq().$canshu);
}

//自动更新读取初始数据
if($run=='gengxin_du'){
	echo get('http://www.zhanbangzhu.com/api/seo/api.php?run=gengxin_du&web='.web_dq());
}

//自动更新删除数据
if($run=='gengxin_shan'){
	$id=$_POST['id'];
	echo get("http://www.zhanbangzhu.com/api/seo/api.php?run=gengxin_shan&id=$id&web=".web_dq());
}

//自动更新测试
if($run=='gengxin_ceshi'){
	$id=$_POST['id'];
	echo get("http://www.zhanbangzhu.com/api/seo/api.php?run=gengxin_ceshi&id=$id&web=".web_dq());
}

/*自动采集更新数据对接S*/
?>