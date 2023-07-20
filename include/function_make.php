<?php 
//2019.8.8
include("class_sql.php");
//{包含=xx.html}标签
function baohan($html){
	global $path_muban;
	global $type_dq;
	preg_match_all('/{包含=(.*?)}/ism',$html,$baohans);
	if(count($baohans[1])>0){
		foreach($baohans[1] as $k=>$baohan){
			$baohan=trim($baohan);
			$html_baohan=get($path_muban.'/'.$baohan);
			$html= str_ireplace($baohans[0][$k],$html_baohan,$html);
		}
	}
	
	//筛选栏目S
	preg_match_all('/{筛选栏目=(.*?)}/ism',$html,$shaixuan_id);
	$sx_id=1;
	if(!isset($shaixuan_id[1][0])){$sx_id=$type_dq[0]['id'];}
	else{
		$sx_id=$shaixuan_id[1][0];
		$html=str_ireplace($shaixuan_id[0][0],'',$html);
	}
	//筛选栏目E
	$res['sx_id']=$sx_id;
	$res['str']=$html;
	return $res;
}

function baohan_qita($html){
	global $path_muban;
	global $pc_www;
	global $muban;
	global $c_sql;
	
	//替换{样式路径}
	$html= str_ireplace('{样式路径}',$pc_www.'cms/templets/'.$muban,$html);
	
	//替换{基础标签}
	//读取并存入基础数据
	$infos=$c_sql->select("select diaoyongbiaoqian,neirong from info where shuyu in(1,2)");
	if(count($infos)>0){
		foreach($infos as $info_arr){
			$html= str_ireplace('{'.$info_arr['diaoyongbiaoqian'].'}',$info_arr['neirong'],$html);
		}
	}
	
	$html=difang($html);//地方站切换
	$html=shouye($html);//{首页}标签
	$html=ad($html);//{广告}标签
	$html=youlian($html);//友情链接
	$html=daohang($html);//导航
	$html=lanmu($html);//指定栏目名称链接
	$html=art($html);//万能文章标签
	
	$htmls=shaixuan($html);//筛选功能
	$html=$htmls['html'];
	$title_sx=$htmls['title_sx'];
	
	$html=difangxia($html);//{地方下=0}标签
	$html=difangxialj($html);//{地方下链接=0}标签
	$html=dangqianzhan($html);//{当前站}标签
	$html=difang_er($html);//{地方}标签
	$html=shibie($html);//自动识别跳转代码
	$html=souwenjian($html);//{搜索文件}
	
	$res=array('title_sx'=>$title_sx,'html'=>$html);
	return $res;
}

//{搜索文件}
function souwenjian($html){
	$lianjie='../search.php';
	if(isset($_GET['php']) || isset($_GET['index'])){
		$lianjie='search.php';
	}
	return str_ireplace('{搜索文件}',$lianjie,$html);
}

//{首页}标签
function shouye($html){
	global $difangs_dq;
	global $ms;
	global $pc_www;
	global $m_www;
	
	if(isset($_GET['php']) || isset($_GET['index'])){
		$path_sy='index.html';
	}
	else{
		$path_sy='../';
	}
	
	if(strstr($html,'{首页}')){
		if($ms=='pc'){$shouye=$path_sy;}
		else{$shouye=$path_sy;}
		$html= str_ireplace('{首页}',$shouye,$html);
	}
	return $html;
}

//地方站切换
function difang($html){
	global $c_sql;
	global $ms;
	global $pc_www;
	global $m_www;
	preg_match_all('/{地方站}(.*?){\/地方站}/ism',$html,$difang_tmp);
	if(count($difang_tmp[1])>0){
		foreach($difang_tmp[1] as $k=>$v){
			$difangs=$c_sql->select("select name,pinyin from liandong where (lid=1 and run=1) order by paixu,id");
			$i=0;
			$difang_txt='';
			foreach($difangs as $arr){
				$difang=str_ireplace('[地方名]',$arr['name'],$difang_tmp[1][0]);
				if($i==0){
					if($ms=='pc'){$lianjie=$pc_www;}
					else{$lianjie=$m_www;}
				}
				else{
					if($ms=='pc'){$lianjie=$pc_www.$arr['pinyin'];}
					else{$lianjie=$pc_www.$arr['pinyin'].'/m';}
				}				
				$difang=str_ireplace('[链接]',$lianjie,$difang);
				$difang=str_ireplace('[拼音]',$arr['pinyin'],$difang);
				if($i==$df){
					$difang=str_ireplace('[当前]','dq',$difang);
				}
				else{
					$difang=str_ireplace('[当前]','',$difang);
				}
				
				$difang_txt.=$difang;
				$i++;
			}
			$html= str_ireplace($difang_tmp[0][$k],$difang_txt,$html);
		}
	}
	return $html;
}

//替换广告
function ad($html){
	global $pc_www;
	preg_match_all('/{广告=(.*?)}/ism',$html,$ads); //导航循环
	if(count($ads[1])>0){
		foreach($ads[1] as $k=> $ad_id){
			$ad_txt="<script src='{$pc_www}cms/common/php/ajax.php?run=ad&id={$ad_id}'></script>";
			$html= str_ireplace($ads[0][$k],$ad_txt,$html);
		}
	}
	return $html;
}

//友情链接替换
function youlian($html){
	global $c_sql;
	preg_match_all("/{友情链接}(.*?){\/友情链接}/ism",$html,$youlians);
	if(isset($youlians[1][0])){
		$youlink_tmp=$youlians[1][0];
		$yous=$c_sql->select("select * from youad where shuyu=1");
		$youlink='';
		if(count($yous)>0){
			foreach($yous as $arr){
				$biaoti=$arr['biaoti'];
				$lianjie=$arr['lianjie'];
				$tupian=$arr['tupian'];
				$youlink_dan=str_ireplace("[标题]",$biaoti,$youlink_tmp);
				$youlink_dan=str_ireplace("[链接]",$lianjie,$youlink_dan);
				$youlink_dan=str_ireplace("[图片]",$tupian,$youlink_dan);
				$youlink.=$youlink_dan;
			}
		}
		$html=str_ireplace($youlians[0][0],$youlink,$html);
	}
	return $html;
}

//替换标签导航
function daohang($html){
	global $type_q;
	global $type_1;
	global $type_2;
	global $type_dq;
	//清除隐藏数组
	if(count($type_1)>0){
		foreach($type_1 as $k=> $arr){
			if($arr['run']==110){
				unset($type_1[$k]);
			}
		}
	}
	
	preg_match_all('/{导航：(.*?)}(.*?){\/导航}/ism',$html,$navs); //导航循环
	if(count($navs[2])>0){
		$dh_dz=1;
		foreach($navs[2] as $k=>$dh){
			//导航条件数组S
			$type_news=$type_1;
			preg_match_all('/{导航：(.*?)}/ism',$navs[0][$k],$tiaojians);
			$tjs=where($navs[1][$k]);//条件数组
			
			if($tjs['编号']=='全部'){
				if($tjs['条']!='全部'){
					$type_news=array_slice($type_1,0,$tjs['条']);
				}
			}
			else if($tjs['编号']=='当前'){
				$type_news=array();
				if($type_dq[0]['tid']!=0){
					$type_news[0]=$type_q[$type_dq[0]['tid']];
				}
				else{
					$type_news[0]=$type_q[$type_dq[0]['id']];
				}
			}
			else{
				$type_news=array();
				
				$type_ids=explode(',',$tjs['编号']);
				foreach($type_ids as $type_id){
					$type_news[$type_id]=$type_q[$type_id];
				}
			}
			//导航条件数组E
			$dh_txt='';
			//开始处理栏目
			preg_match_all('/{子导航}(.*?){\/子导航}/ism',$dh,$dh_2);
			foreach($type_news as $type){
				$dh_new=$dh;
				$id=$type['id'];//栏目id
				$dh_2_txt='';
				/**先处理2级栏目**/
				//如果2级栏目标签存在
				if(isset($dh_2[1][0])){
					if(isset($type_2[$id])){
						foreach($type_2[$id] as $type_2_dqs){
							$dh_2_dan=str_ireplace('[栏目名称]',$type_2_dqs['lanmumingcheng'],$dh_2[1][0]);
							$dh_2_dan=str_ireplace('[副栏目名称]',$type_2_dqs['fulanmumingcheng'],$dh_2_dan);
							$dh_2_dan=str_ireplace('[栏目图片]',$type_2_dqs['lanmutupian'],$dh_2_dan);
							$dh_2_dan=str_ireplace('[优化摘要]',$type_2_dqs['youhuazhaiyao'],$dh_2_dan);
							//高亮显示
							preg_match_all('/\[当前样式=(.*?)\]/ism',$dh_2_dan,$gaoliangs);
							if(isset($gaoliangs[1][0])){
								if($type_dq[0]['id']==$type_2_dqs['id'] && !isset($_GET['index'])){
									$dh_2_dan=str_ireplace($gaoliangs[0][0],$gaoliangs[1][0],$dh_2_dan);
								}
								else{
									$dh_2_dan=str_ireplace($gaoliangs[0][0],'',$dh_2_dan);
								}
							}
							
							//子导航链接处理
							$lianjie2='';
							if((isset($_GET['php']) || isset($_GET['index'])) || (strstr($type_2_dqs['baocunlujing'],'http') && strstr($type_2_dqs['baocunlujing'],'://'))){
								$lianjie2=$type_2_dqs['baocunlujing'];
							}
							else{
								$lianjie2='../'.$type_2_dqs['baocunlujing'];
							}
							$dh_2_txt.=str_ireplace('[链接]',$lianjie2,$dh_2_dan);
						}
					}
					$dh_new=str_ireplace($dh_2[0][0],$dh_2_txt,$dh_new);
				}
				//再处理1级栏目
				$dh_1_dan=str_ireplace('[栏目名称]',$type['lanmumingcheng'],$dh_new);
				$dh_1_dan=str_ireplace('[副栏目名称]',$type['fulanmumingcheng'],$dh_1_dan);
				$dh_1_dan=str_ireplace('[栏目图片]',$type['lanmutupian'],$dh_1_dan);
				$dh_1_dan=str_ireplace('[优化摘要]',$type['youhuazhaiyao'],$dh_1_dan);
				$dh_1_dan=str_ireplace('[递增]',$dh_dz,$dh_1_dan);
				$dh_1_dan=str_ireplace('[id]',$id,$dh_1_dan);
				$dh_dz++;
				
				//高亮显示
				preg_match_all('/\[当前样式=(.*?)\]/ism',$dh_1_dan,$gaoliangs);
				if(isset($gaoliangs[1][0])){
					
					
					
					if($type_dq[0]['id']==$type['id'] && !isset($_GET['index'])){
						$dh_1_dan=str_ireplace($gaoliangs[0][0],$gaoliangs[1][0],$dh_1_dan);
					}
					else if(isset($type_2[$type['id']])){
						foreach($type_2[$type['id']] as $arr_gl){
							if($arr_gl['id']==$type_dq[0]['id']){
								$dh_1_dan=str_ireplace($gaoliangs[0][0],$gaoliangs[1][0],$dh_1_dan);
							}
						}
					}
					else{
						$dh_1_dan=str_ireplace($gaoliangs[0][0],'',$dh_1_dan);
					}
				}
				
				//导航链接处理
				$lianjie1='';
				if((isset($_GET['php']) || isset($_GET['index'])) || (strstr($type['baocunlujing'],'http') && strstr($type['baocunlujing'],'://'))){$lianjie1=$type['baocunlujing'];}
				else{$lianjie1='../'.$type['baocunlujing'];}
				
				$dh_txt.=str_ireplace('[链接]',$lianjie1,$dh_1_dan);
				
			}
			$html=str_ireplace($navs[0][$k],$dh_txt,$html);
		}
	}
	//首页高亮1
	preg_match_all('/{首页样式=(.*?)}/ism',$html,$sygaoliangs);
	if(isset($sygaoliangs[1][0])){
		if(isset($_GET['index'])){$html=str_ireplace($sygaoliangs[0][0],$sygaoliangs[1][0],$html);}
		else{$html=str_ireplace($sygaoliangs[0][0],'',$html);}
	}
	
	return $html;
}

//指定栏目名称栏目链接栏目图片
function lanmu($html){
	global $c_sql;
	global $type_q;
	global $type_id;
	//{栏目名称=栏目id}标签替换
	preg_match_all('/{栏目名称=(.*?)}/ism',$html,$lmmcs);
	if(count($lmmcs[1])>0){
		foreach($lmmcs[1] as $k=>$lmmc){
			$lmmc=trim($lmmc);
			if($lmmc=='当前'){
				$lmmc=$type_id;
			}
			$lmmc_txt="很抱歉栏目ID为<b>$lmmc</b>不存在";
			if(isset($type_q[$lmmc])){
				$lmmc_txt=$type_q[$lmmc]['lanmumingcheng'];
			}
			$html=str_ireplace($lmmcs[0][$k],$lmmc_txt,$html);
		}
	}
	
	//{栏目链接=栏目id}标签替换
	preg_match_all('/{栏目链接=(.*?)}/ism',$html,$lmljs);
	if(count($lmljs[1])>0){
		foreach($lmljs[1] as $k=>$lmlj){
			if($lmlj=='当前'){
				$lmlj=$type_id;
			}
			$lmlj_txt="很抱歉栏目ID为<b>$lmlj</b>不存在";
			if(isset($type_q[$lmlj])){
				if(isset($_GET['php']) || isset($_GET['index'])){
					$lianjie=$type_q[$lmlj]['baocunlujing'];
				}
				else{
					$lianjie='../'.$type_q[$lmlj]['baocunlujing'];
				}
				$lmlj_txt=$lianjie;
			}
			$html=str_ireplace($lmljs[0][$k],$lmlj_txt,$html);
		}
	}
	
	//{栏目图片=栏目id}标签替换
	preg_match_all('/{栏目图片=(.*?)}/ism',$html,$lmtps);
	if(count($lmtps[1])>0){
		foreach($lmtps[1] as $k=>$lmtp){
			if($lmtp=='当前'){
				$lmtp=$type_id;
			}
			$lmtp_txt="很抱歉栏目ID为<b>$lmtp</b>不存在";
			if(isset($type_q[$lmtp])){
				$lmtp_txt=$type_q[$lmtp]['lanmutupian'];
			}
			$html=str_ireplace($lmtps[0][$k],$lmtp_txt,$html);
		}
	}
	
	//{栏目内容=栏目id}标签替换
	preg_match_all('/{栏目内容=(.*?)}/ism',$html,$lmnrs);
	if(count($lmnrs[1])>0){
		foreach($lmnrs[1] as $k=>$lmnr){
			$fens=explode('|',$lmnr);
			$tid=$fens[0];
			$lanmuneirongs=$c_sql->select('select lanmuneirong from type where id='.$tid);
			if(isset($lanmuneirongs[0]['lanmuneirong'])){$lanmuneirong=$lanmuneirongs[0]['lanmuneirong'];}
			else{$lanmuneirong="栏目id为<b>$tid</b>不存在";}
			
			if(isset($fens[1])){
				$zishu=str_ireplace('字数=','',$fens[1]);
				$lanmuneirong=jiequ($lanmuneirong,$zishu);
			}
			$html=str_ireplace($lmnrs[0][$k],$lanmuneirong,$html);
		}
	}
	
	//{当前栏目id}标签替换
	$html=str_ireplace('{当前栏目id}',$type_id,$html);
	
	return $html;
}


//万能文章调用
function art($html){
	global $type_2;
	global $c_sql;
	global $df_1_id;
	global $type_dq;
	preg_match_all('/{文章：(.*?)}(.*?){\/文章}/ism',$html,$art_tmps); //文章循环

	if(count($art_tmps[2])>0){
		foreach($art_tmps[2] as $k=>$art_tmp){
			/**art条件S**/
			$art_where=where($art_tmps[1][$k]);//条件数组
			$where='fabushijian!=1';//条件
			$paixu='id desc';//排序方式
			$limit_s=0;//默认截取开始为o
			$limit_e=5;//默认截取5条
			foreach($art_where as $wk=>$wv){
				//指定栏目
				if($wk=='栏目'){
					if($wv!='全部'){
						if($wv=='当前'){$dq_id=$type_dq[0]['id'];}
						else{$dq_id=$wv;}	
						
						$in_id=$dq_id;
						if(isset($type_2[$dq_id])){
							foreach($type_2[$dq_id] as $arr){
								$in_id.=','.$arr['id'];
							}
						}
						if($where==''){$where="tid in(".$in_id.")";}
						else{$where.=" and tid in(".$in_id.")";}
					}
				}
				//当前地方站第几个地方
				else if($wk=='地方下'){
					if($wv==0){$df_2_id=$df_1_id;}
					else{
						$sql="select id from liandong where lid=$df_1_id order by paixu,id limit ".($wv-1).",1";
						$dfx=$c_sql->select($sql);
						$df_2_id=$dfx[0]['id'];
					}
					//地方下全部id
					$result=array();
					$df_xiaquan=$c_sql->digui('liandong','lid',$df_2_id,$result);
					
					if(count($df_xiaquan)>0){
						foreach($df_xiaquan as $arr){
							$df_2_id.=','.$arr['id'];
						}
					}
			
					if($where==''){$where.=" fabudao in($df_2_id)";}
					else{$where.=" and fabudao in($df_2_id)";}
				}
				//推荐
				else if($wk=='推荐'){
					if($where==''){$where="tuijian in('".$wv."')";}
					else{$where.=" and tuijian in('".$wv."')";}
				}
				
				//缩略图限制
				else if($wk=='缩略图'){
					$slt_tj='';
					if($wv=='有'){$slt_tj.="suoluetu !=''";}
					else{$slt_tj.="suoluetu= ''";}
					if($where==''){$where=$slt_tj;}
					else{$where.=" and ".$slt_tj;}
				}
				
				//排序
				else if($wk=='排序'){
					if($wv=='升'){$paixu='paixu asc,id asc';}
					else{$paixu='paixu desc,id desc';}
				}
				
				else if($wk=='起'){
					$limit_s=$wv;
				}
				else if($wk=='条'){
					$limit_e=$wv;
				}
				else{
					if($where==''){
						$where=pinyin($wk,$lx='all')." in('".$wv."')";
					}
					else{
						$where.=' and '.pinyin($wk,$lx='all')." in('".$wv."')";
					}
				}
			}
			
			//组装条件
			if($where!=''){
				$where=" where ($where) ";
			}
			
			//获取模型字段s
			preg_match_all('/\[(.*?)\]/ism',$art_tmp,$mxs);
			$ziduan='id';
			$mx_new=array();
			foreach($mxs[1] as $mx){
				if(strstr($mx,'=') || $mx=='链接'){}
				else{
					$mx_new[]=$mx;
					$ziduan.=','.pinyin($mx,$lx='all');
				}
			}
			
			$mxziduans=ziduan2_tq($art_tmp);
			//字段
			$mxziduans[1][]='id';
			
			//关联文章主文章
			if(strstr($art_tmp,'[aid]')){
				$mxziduans[1][]='aid';
				$mxziduans[1][]='tid';
			}
			
			$ziduan=implode(',',$mxziduans[1]);
			//获取模型字段e
			$sql="select $ziduan from art{$where} order by $paixu limit $limit_s,$limit_e";
			$arts=$c_sql->select($sql);
			$art_txt='';
			
			if(count($arts)>0){
				$art_txt=art_th($art_tmp,$arts,$mxziduans[0]);
			}
			
			$html=str_ireplace($art_tmps[0][$k],$art_txt,$html);
		}
	}
	return $html;
}

//筛选功能S
function shaixuan($html){
	global $c_sql;
	global $difangs_dq;
	global $pc_www;
	$title_sx='';
	preg_match_all('/{筛选=(.*?)}(.*?){\/筛选}/ism',$html,$shaixuan_tmp);
	if(count($shaixuan_tmp[1])>0){
		$seo_fbd='';
		//获取原URL
		foreach($shaixuan_tmp[1] as $k=>$v){
			$s_ziduan='s_'.pinyin($v,$lx='all');//筛选字段
			$s_moxing=$c_sql->select("select morenzhi from moxing where diaoyongmingcheng='$v'");
			$s_shaixuan_tj=$s_moxing[0]['morenzhi'];
			$s_shaixuan_tj=str_ireplace(";","\n",$s_shaixuan_tj);
			$s_shaixuan_tjs=explode("\n",$s_shaixuan_tj);
			
			if(count($s_shaixuan_tjs)>0){
				//当前s
				$sx_bxtxt=$shaixuan_tmp[2][$k];
				if(!isset($_GET[$s_ziduan])){
					preg_match_all('/\[当前样式=(.*?)\]/ism',$sx_bxtxt,$sx_bxdqys);
					if(isset($sx_bxdqys[1][0])){
						$sx_bxtxt=str_ireplace($sx_bxdqys[0][0],$sx_bxdqys[1][0],$sx_bxtxt);
					}
				}
				//当前e
				$s_th_txt=str_ireplace('[条件]','不限',$sx_bxtxt);
				if(isset($_GET['php']) || isset($_GET['index'])){
					$qian='';
				}
				else{
					$qian='../';
				}
				$s_th_txt=str_ireplace('[链接]',"{$qian}search.php".shaixuan_url('','',$s_ziduan),$s_th_txt);	
				$sx_dz=1;	
				foreach($s_shaixuan_tjs as $v_tj){
					//发布到联动
					if($v_tj=='1'){
						preg_match_all('/{循环}(.*?){\/循环}/ism',$shaixuan_tmp[0][$k],$quyu_tmp);
						if(isset($_GET['s_fabudao'])){
							$lid=$_GET['s_fabudao'];
							$weizhi=$c_sql->select("select lid from liandong where id=$lid order by paixu,id");
							$baohan=array($weizhi[0]['lid'],$lid);
							ksort($baohan);//升序
						}
						//区域
						$fbd_lds=$c_sql->select("select id,name from liandong where lid=".$difangs_dq[0]['id']." order by paixu,id");
                        
						
						if(isset($_GET['php']) || isset($_GET['index'])){
							$qian='';
						}
						else{
							$qian='../';
						}
						
						$txt_1=str_ireplace('[链接]',"{$qian}search.php".shaixuan_url('','',$s_ziduan),$quyu_tmp[1][0]);
						
						$txt_1=str_ireplace('[条件]',"全".$difangs_dq[0]['name'],$txt_1);
						preg_match_all('/\[当前样式=(.*?)\]/ism',$txt_1,$txt_1_dqys);
						if(isset($txt_1_dqys[1][0])){
							if(isset($_GET['s_fabudao'])){$txt_1=str_ireplace($txt_1_dqys[0][0],'',$txt_1);}
							else{$txt_1=str_ireplace($txt_1_dqys[0][0],$txt_1_dqys[1][0],$txt_1);}
						}
						
						
						if(isset($quyu_tmp[1][0]) && count($fbd_lds)>0){
							foreach($fbd_lds as $arr_qy){
								if(isset($_GET['php']) || isset($_GET['index'])){
									$qian='';
								}
								else{
									$qian='../';
								}
								$href=$qian.'search.php'.shaixuan_url('s_fabudao',$arr_qy['id'],'art');
								$txt=str_ireplace('[链接]',$href,$quyu_tmp[1][0]);
								$txt=str_ireplace('[条件]',$dq_a.$arr_qy['name'].$dq_b,$txt);
								
								preg_match_all('/\[当前样式=(.*?)\]/ism',$txt,$txt_dqys);
								if(isset($txt_dqys[1][0])){
									if(in_array($arr_qy['id'],$baohan)){
										$txt=str_ireplace($txt_dqys[0][0],$txt_dqys[1][0],$txt);
										$bx2=array($arr_qy['id'],$arr_qy['name']);
										$seo_fbd=$arr_qy['name'];
									}
								}
								
								$txt_1.=$txt;
							}
						}
						$txt_1=str_ireplace($quyu_tmp[0][0],$txt_1,$shaixuan_tmp[2][$k]);
						$txt_1=str_ireplace('[级别]','区域',$txt_1);
						
						//街道
						if(isset($_GET['s_fabudao'])){
							$fabudao_2=$c_sql->select("select id,name from liandong where lid=$lid order by paixu,id");
							if(count($fabudao_2)<=0){
								$fabudao_2=$c_sql->select("select id,name from liandong where lid=".$weizhi[0]['lid']." order by paixu,id");
							}
							
							if(count($fabudao_2)>0 && $fbd_lds!=$fabudao_2){
								if(isset($_GET['php']) || isset($_GET['index'])){
									$qian='';
								}
								else{
									$qian='../';
								}
								$txt_2=str_ireplace('[链接]',$qian.'search.php'.shaixuan_url('s_fabudao',$bx2[0],'art'),$quyu_tmp[1][0]);
								$txt_2=str_ireplace('[条件]','全'.$bx2[1],$txt_2);
								preg_match_all('/\[当前样式=(.*?)\]/ism',$txt_2,$txt_2_dqys);
								if(isset($txt_2_dqys[1][0])){
									if($difangs_dq[0]['id']==$baohan[0]){
										$txt_2=str_ireplace($txt_dqys[0][0],$txt_dqys[1][0],$txt_2);
									}
									else{
										$txt_2=str_ireplace($txt_dqys[0][0],'',$txt_2);
									}
								}
								
								
								
								foreach($fabudao_2 as $arr_fbd2){
									$lid_1=$arr_fbd2['id'];
									if(isset($_GET['php']) || isset($_GET['index'])){
										$qian='';
									}
									else{
										$qian='../';
									}
									$href=$qian.'search.php'.shaixuan_url('s_fabudao',$lid_1,'art');
									$txt=str_ireplace('[链接]',$href,$quyu_tmp[1][0]);
									$txt=str_ireplace('[条件]',$arr_fbd2['name'],$txt);
									preg_match_all('/\[当前样式=(.*?)\]/ism',$txt,$txt_dqys);
									if(isset($txt_dqys[1][0])){
										if(in_array($lid_1,$baohan)){
											$txt=str_ireplace($txt_dqys[0][0],$txt_dqys[1][0],$txt);
										}
									}
									$txt_2.=$txt;
								}
								$txt_2=str_ireplace($quyu_tmp[0][0],$txt_2,$shaixuan_tmp[2][$k]);
								$txt_2=str_ireplace('[级别]','街道',$txt_2);
							}
						}
						$s_th_txt=$txt_1.$txt_2;
					}
					
					else{
						$v_tj=str_ireplace('[默认选中]','',$v_tj);
						$sx_txt=$shaixuan_tmp[2][$k];
						if(isset($_GET[$s_ziduan])){
							if($_GET[$s_ziduan]==$v_tj){
								preg_match_all('/\[当前样式=(.*?)\]/ism',$sx_txt,$sx_dqys);
								if(isset($sx_dqys[1][0])){
									$sx_txt=str_ireplace($sx_dqys[0][0],$sx_dqys[1][0],$sx_txt);
								}
							}	
						}
						$s_th=str_ireplace('[条件]',$v_tj,$sx_txt);
						$s_th=str_ireplace('[递增]',$sx_dz,$s_th);
						$sx_dz++;
						if(isset($_GET['php']) || isset($_GET['index'])){
							$qian='';
						}
						else{
							$qian='../';
						}
						$s_th=str_ireplace('[链接]',$qian.'search.php'.shaixuan_url($s_ziduan,$v_tj,'art'),$s_th);
						$s_th_txt.=$s_th;
					}
				}
			}
			$html=str_ireplace($shaixuan_tmp[0][$k],$s_th_txt,$html);
			
			//组合筛选优化标题
			if(isset($_GET[$s_ziduan]) && $s_ziduan!='s_fabudao'){
				$zhi=$_GET[$s_ziduan];
				$title_sx.=$zhi;
			}
			
		}
	}
	if(isset($_GET['s_fabudao'])){
		$title_sx=difanglian($_GET['s_fabudao']).$title_sx;
	}
	$res=array('title_sx'=>$title_sx,'html'=>$html);
	return $res;
}

function shaixuan_url($add,$zhi,$del){
	global $sx_id;
	$get_new=$_GET;
	if($del!=''){unset($get_new[$del]);}
	unset($get_new['index']);
	unset($get_new['php']);
	unset($get_new['t']);
	unset($get_new['art_p']);
	unset($get_new['html']);
	unset($get_new['ms']);
	unset($get_new['df']);
	unset($get_new['p']);
	unset($get_new['make_dan']);
	$get_new['list']=$sx_id;
	if($add!=''){$get_new[$add]=$zhi;}
	$res='';
	if(count($get_new)<=0){return $res;}
	foreach($get_new as $k=>$v){
		$zhi='';
		if($v!=''){$zhi="={$v}";}
		if($res==''){
			$res="?{$k}{$zhi}";
		}
		else{
			$res.="&{$k}{$zhi}";
		}
	}

	return $res;
}

//{地方下=0}标签
function difangxia($html){
	global $c_sql;
	global $df_1_id;
	preg_match_all('/{地方下=(.*?)}/ism',$html,$dfx_tmps);
	if(count($dfx_tmps[1])>0){
		foreach($dfx_tmps[1] as $k=> $dfx){
			$dfxs=$c_sql->select("select name from liandong where lid=$df_1_id  order by paixu,id limit ".($dfx-1).",1");
			$html=str_ireplace($dfx_tmps[0][$k],$dfxs[0]['name'],$html);
		}
	}
	
	return $html;
}

//{地方下链接=0}标签
function difangxialj($html){
	global $c_sql;
	global $sx_id;
	global $df_1_id;
	preg_match_all('/{地方下链接=(.*?)}/ism',$html,$dfx_tmps);
	if(count($dfx_tmps[1])>0){
		foreach($dfx_tmps[1] as $k=> $dfx){
			$dfxs=$c_sql->select("select id from liandong where lid=$df_1_id order by paixu,id limit ".($dfx-1).",1");
			$html=str_ireplace($dfx_tmps[0][$k],'&s_fabudao='.$dfxs[0]['id'],$html);
		}
	}
	return $html;
}

//替换{当前站}
function dangqianzhan($html){
	global $c_sql;
	global $df;
	if(strstr($html,'{当前站}')){
		$df_dq=$c_sql->select("select name from liandong where (lid=1 and run=1) order by paixu,id asc limit $df,1");
		
		$html= str_ireplace('{当前站}',$df_dq[0]['name'],$html);
	}
	return $html;
}

//生成栏目页
function make_type($html,$list){
	global $c_sql;
	global $path_make;
	global $pc_www;
	global $title_sx;
	//发布到id s
	global $difangs_dq;
	if(isset($difangs_dq[0]['id'])){
		$difangs_dq_id=$difangs_dq[0]['id'];
	}
	else{
		$difangs_dq_id=0;
	}
	
	$result=array();
	$spac='';
	$difangids=$c_sql->digui('liandong','lid',$difangs_dq_id,$result,$spac);
	$difangs_dq_id.=','.'0';
	if(count($difangids)>0){
		foreach($difangids as $arr){
			$difangs_dq_id.=','.$arr['id'];
		}
	}
	//发布到id e
	preg_match_all('/{列表=(.*?)}(.*?){\/列表}/is',$html,$list_tmps);
	
	//获取文章id in
	$tiao=$list_tmps[1][0];//显示条数
	
	//起始位置
	if(isset($_GET['p'])){$p=$_GET['p'];}
	else{$p=1;}
	$qi=($p-1)*$tiao;
	
	//筛选条件S
	$id_in=$list;
	$ids_zi=$c_sql->select("select id from type where tid=".$list);
	if(count($ids_zi)>0){
		foreach($ids_zi as $arr){
			$id_in.=','.$arr['id'];
		}
	}
	$id_in='tid in('.$id_in.')';
	$where=$id_in;
	
	//特殊
	$sx_teshu='';//筛选
	$sou='';//搜索
	if(count($_GET)>0){
		foreach($_GET as $k=>$v){
			if(strstr($k,'s_')){
				$k=str_ireplace('s_','',$k);
				if(strstr($v,'-')){
					$qujians=explode('-',$v);
					$dayu=shuzi($qujians[0]);
					$xiaoyu=shuzi($qujians[1]);
					$sx_teshu.=" and $k>=$dayu and $k<=$xiaoyu";
				}
				else{
					$fbd_id_in='';
					if($k=='fabudao'){
						$result=array();
						$zi_dfs=$c_sql->digui('liandong','lid',$lid=$v,$result,$spac=0);
						
						$fbd_id_in=$v;
						if(count($zi_dfs)>0){
							foreach($zi_dfs as $arr){
								$fbd_id_in.=','.$arr['id'];
							}
						}
						$fbd_id_in=' and fabudao in('.$fbd_id_in.')';
					}
					else if(strstr($v,'上')){
						$sx_teshu.=" and $k >".shuzi($v);
					}
					else if(strstr($v,'下')){
						$sx_teshu.=" and $k <".shuzi($v);
					}
					else{
						$sx_teshu.=" and $k='$v'";
					}
					$sx_teshu.=$fbd_id_in;
				}
				
			}
			
			else if(strstr($k,'sou_')){
				$k=str_ireplace('sou_','',$k);
				$sou.=" and {$k} like '%{$v}%'";
			}
			
		}
	}
	
	if($sou!=''){$where.=$sou;}
	else{$where.=$sx_teshu;}
	$where.=" and fabushijian!=1 and fabudao in($difangs_dq_id)";
	//筛选条件E
	$lists_id=$c_sql->select("select id from art where ({$where}) order by paixu asc,id desc limit $qi,$tiao");	
	$id_ins=array();
	foreach($lists_id as $arr){
		$id_ins[]=$arr['id'];
	}
	$id_in=implode(',',$id_ins);
	
	//读取文章内容
	$mxziduans=ziduan2_tq($list_tmps[2][0]);
	//字段
	$mxziduans[1][]='id';
	$ziduan=implode(',',$mxziduans[1]);
	$lists=$c_sql->select("select $ziduan from art where id in($id_in) order by paixu asc,id desc");
	
	$list_txt=art_th($list_tmps[2][0],$lists,$mxziduans[0]);
	$html=str_ireplace($list_tmps[0][0],$list_txt,$html);
	
	//下面生成列表页第一页
	//{栏目内容}
	$ziduan_jia='';
	if(strstr($html,'{栏目内容}')){$ziduan_jia.=',lanmuneirong';}
	//{栏目图片}
	if(strstr($html,'{栏目图片}')){$ziduan_jia.=',lanmutupian';}
	
	$typeinfo=$c_sql->select("select tid,baocunlujing,youhuabiaoti,youhuaguanjianci,youhuazhaiyao{$ziduan_jia} from type where id=$list");
	if(isset($typeinfo[0]['lanmuneirong'])){
		$html=str_ireplace('{栏目内容}',$typeinfo[0]['lanmuneirong'],$html);
	}
	else{
		$html=str_ireplace('{栏目内容}','主人太懒没有编辑该栏目内容……',$html);
	}
	if(strstr($html,'{栏目图片}')){
		//栏目图片S
		if($typeinfo[0]['lanmutupian']=='' && $typeinfo[0]['tid']!=0){
			$typeinfo_mys=$c_sql->select("select lanmutupian from type where id=".$typeinfo[0]['tid']);
			$html=str_ireplace('{栏目图片}',$typeinfo_mys[0]['lanmutupian'],$html);
		}
		else{
			$html=str_ireplace('{栏目图片}',$typeinfo[0]['lanmutupian'],$html);
		}
		//栏目图片E
	}
	
	/*分页s*/
	preg_match_all('/{分页=(.*?)}/is',$html,$fenyes);
	if(isset($fenyes[1][0])){
		$fenye_txt=fenye($where,$fenyes[1][0],$tiao);
		$html= str_ireplace($fenyes[0][0],$fenye_txt,$html);
	}
	/*分页e*/
	
	$seo_arr=array($title_sx.$typeinfo[0]['youhuabiaoti'],$typeinfo[0]['youhuaguanjianci'],$typeinfo[0]['youhuazhaiyao']);
	$html=seo($html,$seo_arr);
	
	$html=str_ireplace('../../upload/',"{$pc_www}cms/upload/",$html);//图片路径转为绝对路径
	if((isset($_GET['php']) || isset($_GET['list'])) && !isset($_GET['make_dan'])){
		echo $html;
		exit;
	}
	//单个生成详情页
	if(isset($_GET['make_dan'])){
		write($path_make.'/'.$typeinfo[0]['baocunlujing'].'/index.html',$html);
		exit('make_dan&index'.'&ms='.$_GET['ms'].'&art_id='.$_GET['art_id']);
	}
	write($path_make.'/'.$typeinfo[0]['baocunlujing'].'/index.html',$html);
	return 'type_ok';
}

//生成详情页
function make_art($html,$tid){
	global $pc_www;
	global $c_sql;
	global $path_make;
	global $pc_www;
	global $type_dq;
	if(isset($_GET['php']) && isset($_GET['art'])){
		$where_ins=$_GET['art'];
	}
	else{
		if(isset($_GET['art_p'])){$art_p=$_GET['art_p'];}
		else{$art_p=1;}
		
		//计算起始位置
		$pagesite=50;
		$qi=($art_p-1)*$pagesite;//起止位置
		$arts_id=$c_sql->select("select id from art where tid=$tid limit $qi,$pagesite");
		//组合文章id
		$where_ins='';
		if(count($arts_id)>0){
			foreach($arts_id as $arts){
				if($where_ins==''){$where_ins=$arts['id'];}
				else{$where_ins.=','.$arts['id'];}
			}
		}
		else{
			return 'art_ok';
		}
	}
	
	//提取标签
	preg_match_all("/{(.*?)}/ism",$html,$biaoqians);
	$art_ziduan=ziduan_tq($biaoqians[1]);
	$tihuan_arr=$art_ziduan[0];//要替换的标签
	$ziduan_arr=implode(',',$art_ziduan[1]);//要查询的字段
	if($ziduan_arr==''){$ziduan_arr='id';}
	else{$ziduan_arr="$ziduan_arr,id";}
	$arts=$c_sql->select("select $ziduan_arr from art where id in ($where_ins)");
	foreach($arts as $k=> $arr){
		$kk=$k;
		$id=$arr['id'];
		
		//关联S
		$html_gl=guanlian($html,$id);
		
		//关联E
		
		//发布时间
		if(isset($arr['fabushijian'])){
			$fabushijian=$arr['fabushijian'];
			$fabushijian_k=array_search($fabushijian,array_values($arr));
			$arr['fabushijian']=shijian($tihuan_arr[$fabushijian_k],$fabushijian);
		}
		
		//发布到
		if(isset($arr['fabudao'])){
			$fabudao_id=$arr['fabudao'];
			$fabudao_name='全部地方';
			if($fabudao_id!=''){
				$fabudao_name=difanglian($fabudao_id);
			}
			$arr['fabudao']=$fabudao_name;
		}
		
		$art_html=str_ireplace($tihuan_arr,$arr,$html_gl);//常规替换
		
		//标题截取
		preg_match_all("/{标题=(.*?)}/ism",$art_html,$jiequs);
		if(count($jiequs[1])>0){
			foreach($jiequs[1] as $k=>$zishu){
				$art_html=str_ireplace($jiequs[0][$k],jiequ($arr['biaoti'],$zishu),$art_html);//常规替换
			}
		}
		
		//下面生成内容页
		$seo_arr=array($arr['biaoti'],$arr['guanjianci'],$arr['zhaiyao']);
		$art_html=seo($art_html,$seo_arr);
		
		//上一篇
		$shang_biaoti='';
		preg_match_all("/{上一篇标题=(.*?)}/ism",$art_html,$shangs);
		if(isset($shangs[1][0])){
			$shang_biaoti=$shangs[0][0];
			$shang_biaoti_shu=$shangs[1][0];
		}
		else if(strstr($art_html,'{上一篇标题}')){
			$shang_biaoti='{上一篇标题}';
			$shang_biaoti_shu=1000;
		}
			
		if($shang_biaoti!=''){
			if(isset($arts[$k-1])){
				$art_html=str_ireplace($shang_biaoti,jiequ($arts[$k-1]['biaoti'],$shang_biaoti_shu),$art_html);
			}
			else{
				$arts_sx=$c_sql->select("select id,biaoti from art where (tid=$tid and id<".$arr['id'].") order by id desc limit 1");
				if(isset($arts_sx[0])){
					$art_html=str_ireplace($shang_biaoti,jiequ($arts_sx[0]['biaoti'],$shang_biaoti_shu),$art_html);
				}
			}
			
		}
		
		if(strstr($art_html,'{上一篇链接}')){
			if(isset($arts[$k-1])){
				$art_html=str_ireplace('{上一篇链接}',$arts[$k-1]['id'].'.html',$art_html);//常规替换
			}
			else{
				$arts_sx=$c_sql->select("select id,biaoti from art where (tid=$tid and id<".$arr['id'].") order by id desc limit 1");
				if(isset($arts_sx[0])){
					if(isset($_GET['php'])){
						$art_html=str_ireplace('{上一篇链接}',$arts_sx[0]['id'].'.html',$art_html);//常规替换
					}
					else{
						$art_html=str_ireplace('{上一篇链接}',$arts_sx[0]['id'].'.html',$art_html);
					}
				}
			}
			
		}
		
		//下一篇
		$xia_biaoti='';
		preg_match_all("/{下一篇标题=(.*?)}/ism",$art_html,$xias);
		if(isset($xias[1][0])){
			$xia_biaoti=$xias[0][0];
			$xia_biaoti_shu=$xias[1][0];
		}
		else if(strstr($art_html,'{下一篇标题}')){
			$xia_biaoti='{下一篇标题}';
			$xia_biaoti_shu=100;
		}
		
		if($xia_biaoti!=''){
			if(isset($arts[$kk+1])){
				$art_html=str_ireplace($xia_biaoti,jiequ($arts[$kk+1]['biaoti'],$xia_biaoti_shu),$art_html);
			}
			else{
				$arts_sx=$c_sql->select("select id,biaoti from art where (tid=$tid and id>".$arr['id'].") order by id asc limit 1");
				if(isset($arts_sx[0])){
					$art_html=str_ireplace($xia_biaoti,jiequ($arts_sx[0]['biaoti'],$xia_biaoti_shu),$art_html);
				}
			}
			
		}
		
		if(strstr($art_html,'{下一篇链接}')){
			
			if(isset($arts[$kk+1])){
				$art_html=str_ireplace('{下一篇链接}',$arts[$kk+1]['id'].'.html',$art_html);//常规替换
			}
			else{
				
				$arts_sx=$c_sql->select("select id,biaoti from art where (tid=$tid and id>".$arr['id'].") order by id asc limit 1");
				if(isset($arts_sx[0])){
					$art_html=str_ireplace('{下一篇链接}',$arts_sx[0]['id'].'.html',$art_html);
				}
			}
			
		}
		
		$art_html=preg_replace("/{上一篇标题.*?}/","没有了",$art_html);
		$art_html=preg_replace("/{下一篇标题.*?}/","没有了",$art_html);
		$art_html=preg_replace("/{上一篇链接.*?}/","#",$art_html);
		$art_html=preg_replace("/{下一篇链接.*?}/","#",$art_html);
		//上一篇下一篇文章E
		$art_html=duotu($art_html,$id);
		$art_html=str_ireplace('{点击量}','<script src="'.$pc_www.'cms/common/php/ajax.php?run=dj&id='.$id.'"></script>',$art_html);
		
		$art_html=str_ireplace('../../upload/',"{$pc_www}cms/upload/",$art_html);//图片路径转为绝对路径
		
		if(isset($_GET['php']) && isset($_GET['art'])){
			if(isset($_GET['make_dan'])){
				write($path_make.'/'.$type_dq[0]['baocunlujing'].'/'.$arr['id'].'.html',$art_html);
				exit('make_dan&list='.$tid.'&ms='.$_GET['ms'].'&art_id='.$_GET['art']);
			}
			echo $art_html;
			exit;
		}
		
		write($path_make.'/'.$type_dq[0]['baocunlujing'].'/'.$arr['id'].'.html',$art_html);
	}
}

/*************************辅助操作函数***********************/

//关联
function guanlian($html,$id){
	global $c_sql;
	preg_match_all('/{关联：(.*?)}(.*?){\/关联}/ism',$html,$gl_tmps); //文章循环
	if(count($gl_tmps[2])>0){
		foreach($gl_tmps[2] as $k=>$gl_tmp){
			$art_where=where($gl_tmps[1][$k]);//条件数组
			$aid=$id;
			$typeid=$art_where['栏目'];//栏目id
			$limit_s=$art_where['起'];//默认截取开始为o
			$limit_e=$art_where['条'];//默认截取5条
			
			$mxziduans=ziduan2_tq($gl_tmp);
			//字段
			$mxziduans[1][]='id';
			$ziduan=implode(',',$mxziduans[1]);
			$arts=$c_sql->select("select {$ziduan} from art where (tid={$typeid} and aid={$aid}) limit {$limit_s},{$limit_e}");
			if(count($arts)>0){
				$art_txt=art_th($gl_tmp,$arts,$mxziduans[0]);
			}
			$html=str_ireplace($gl_tmps[0][$k],$art_txt,$html);

		}
	}
	return $html;
}


//地方链接起来
function difanglian($fabudao_id){
	global $c_sql;
	$result=array();
	$fabudaos=$c_sql->diguif('liandong','lid',$fabudao_id,$result);
	$fbdids=$fabudao_id;
	foreach($fabudaos as $fabudao_arr){
		if($fabudao_arr['id']!=1 && $fabudao_arr['id']!=$fabudao_id){
			$fbdids.=','.$fabudao_arr['id'];
		}
	}
	$fabudao_names=$c_sql->select("select name from liandong where id in({$fbdids})");
	foreach($fabudao_names as $names){
		if($fabudao_name=='全部地方'){
			$fabudao_name=$names['name'];
		}
		else{
			$fabudao_name.=$names['name'];
		}
	}
	return $fabudao_name;
}


//多图标签处理
function duotu($html,$id){
	global $c_sql;
	preg_match_all("/{多图=(.*?)}(.*?){\/多图}/ism",$html,$biaoqians);
	if(isset($biaoqians[1])){
		foreach($biaoqians[1] as $k=>$v){
			$ziduan=pinyin($v,$lx='all');
			$duotus=$c_sql->select("select $ziduan from art where id=$id limit 1");
			$duotus=$duotus[0][$ziduan];
			$duotus=explode(';',$duotus);
			$txt='';
			foreach($duotus as $picsrc){
				$txt.=str_ireplace('[图片路径]',$picsrc,$biaoqians[2][$k]);
			}
			$html=str_ireplace($biaoqians[0][$k],$txt,$html);
		}
		
	}
	return $html;
}

//条件组合函数
function where($str){
	$tiaojians=explode('|',$str);
	$tjs=array();
	foreach($tiaojians as $tiaojian){
		$tjs_y=explode('=',$tiaojian);
		$tjs[$tjs_y[0]]=$tjs_y[1];
	}
	return $tjs;
}

//文章标签替换[](被替换的模板，替换的文章数组,数据模型)
function art_th($art_tmp,$arts,$mxs){
	global $type_q;
	global $pc_www;
	global $c_sql;
	global $type_dq;
	//获取需要截取的字段
	$jiequ_arr=array();
	foreach($mxs as $k=> $v){
		if(strstr($v,'=')){
			$jiequ_arr[]=$k;
		}
	}
	$art_txt='';
	$i=1;
	foreach($arts as $arr){
		$art=$art_tmp;
		//缩略图
		if(isset($arr['suoluetu'])){
			if($arr['suoluetu']==''){
				$art=str_ireplace('[缩略图]',$pc_www.'cms/upload/pic.jpg',$art);
			}
		}
		
		if(isset($arr['fabudao'])){
			$name_dq=$c_sql->select("select name from liandong where id=".$arr['fabudao']);
			$art=str_ireplace('[发布到]',$name_dq[0]['name'],$art);
		}
		
		$id=$arr['id'];
		$tid=$c_sql->select("select tid from art where id=$id limit 1");
		$tid=$tid[0]['tid'];
		$type=$c_sql->select("select baocunlujing from type where id=$tid limit 1");
		if(isset($_GET['php']) || isset($_GET['index'])){
			$lianjie=$type[0]['baocunlujing'].'/'.$arr['id'].'.html';
		}
		else if($type[0]['baocunlujing']!=$type_dq[0]['baocunlujing']){
			$lianjie='../'.$type[0]['baocunlujing'].'/'.$arr['id'].'.html';
		}
		else{
			$lianjie=$arr['id'].'.html';
		}
		
		$art=str_ireplace('[链接]',$lianjie,$art);
		
		//关联文章主文章
		if(strstr($art,'[aid]')){
			$gl_tids=$c_sql->select("select tid from art where id=".$arr['aid']);
			$gl_tid=$gl_tids[0]['tid'];
			$gl_baocunlujing=$type_q[$gl_tid]['baocunlujing'];
			if(isset($_GET['php']) || isset($_GET['index'])){
				$gl_lianjie=$gl_baocunlujing.'/'.$arr['aid'].'.html';
			}
			else if($type[0]['baocunlujing']!=$type_dq[0]['baocunlujing']){
				$gl_lianjie='../'.$gl_baocunlujing.'/'.$arr['id'].'.html';
			}
			else{
				$gl_lianjie=$arr['aid'].'.html';
			}
			$art=str_ireplace('[aid]',$gl_lianjie,$art);
		}
		
		
		$art=str_ireplace('[递增]',$i,$art);
		unset($arr['id']);
		
		//如果摘要不存在则赋值标题
		if(isset($arr['zhaiyao']) && isset($arr['biaoti'])){
			if($arr['zhaiyao']==''){
				$arr['zhaiyao']=$arr['biaoti'];
			}
		}
		
		$art=str_ireplace($mxs,$arr,$art);
		//截取字数标签
		preg_match_all('/\[(.*?)\]/ism',$art,$duos);
		if(count($duos[1])>0){
			foreach($duos[1] as $k=>$v){
				$fs=explode('=',$v);
				if(strstr($v,'发布时间')){
					if(isset($arr[pinyin($fs[0],$lx='all')])){
						$art=str_ireplace($duos[0][$k],shijiana($duos[0][$k],$arr[pinyin($fs[0],$lx='all')]),$art);
					}
				}
				else{
					if(isset($arr[pinyin($fs[0],$lx='all')])){
						$art=str_ireplace($duos[0][$k],jiequ($arr[pinyin($fs[0],$lx='all')],$fs[1]),$art);
					}
				}
			}
		}
		if(isset($type_dq[0]['lanmumingcheng'])){
			$art=str_ireplace('[栏目名称]',$type_dq[0]['lanmumingcheng'],$art);
			$art=str_ireplace('[栏目链接]',$type_dq[0]['baocunlujing'].'.html',$art);
		}
		$art_txt.=$art;
		$i++;
	}
	return $art_txt;
}

//提取字段和被替换标签
function ziduan_tq($arr){
	global $mx_ziduans_news;//所有art字段
	$arr=array_unique($arr);
	$tihuan_arr=array();
	$ziduan_arr=array();
	foreach($arr as $v){
		if(strstr($v,'=')){
			$zds=explode('=',$v);
			$biaoqian=$zds[0];
		}
		else{$biaoqian=$v;}
		if(strstr($mx_ziduans_news,'{'.$biaoqian.'}')){
			$ziduan=pinyin($biaoqian,$lx='all');
			if(!in_array($ziduan,$ziduan_arr)){
				$tihuan_arr[]='{'.$v.'}';
				$ziduan_arr[]=$ziduan;
			}
		}
	}
	$res[0]=array_unique($tihuan_arr);
	$res[1]=array_unique($ziduan_arr);
	return $res;
}

//提取模型字段[]
function ziduan2_tq($str){
	global $mx_ziduans_news_b;
	preg_match_all('/\[(.*?)\]/ism',$str,$mxs);
	$ziduan_arr=array();
	$mx_arr=array();
	$mxs=$arr=array_unique($mxs[1]);
	foreach($mxs as $mx){
		if($mx=='链接'){}
		else{
			$mx_thy=$mx;
			if(strstr($mx,'=')){
				$mxs=explode('=',$mx);
				$mx=$mxs[0];
			}
			
			if(strstr($mx_ziduans_news_b,'['.$mx.']')){
				$mx_arr[]='['.$mx.']';
				$ziduan_arr[]=pinyin($mx,$lx='all');
			}
		}
	}
	$res[0]=array_unique($mx_arr);
	$res[1]=array_unique($ziduan_arr);
	return $res;
}

//时间替换（时间模型,时间戳）
function shijian($tmp,$shijianchuo){
    if ($shijianchuo == '' || !strlen($shijianchuo) == 10) {
        $shijianchuo = time();
    }
    preg_match_all('/{发布时间=(.*?)}/ism', $tmp, $sjs);
    foreach ($sjs[1] as $k => $v) {
        $geshi = str_ireplace('年', 'Y', $v);
        $geshi = str_ireplace('月', 'm', $geshi);
        $geshi = str_ireplace('日', 'd', $geshi);
        $tmp = str_ireplace($sjs[0][$k], date($geshi, $shijianchuo), $tmp);
    }
    return $tmp;
}
//时间替换（时间模型,时间戳）
function shijiana($tmp,$shijianchuo){
    if ($shijianchuo == '' || !strlen($shijianchuo) == 10) {
        $shijianchuo = time();
    }
    preg_match_all('/\[发布时间=(.*?)\]/ism', $tmp, $sjs);
    if (count($sjs[1]) > 0) {
        foreach ($sjs[1] as $k => $v) {
            $geshi = str_ireplace('年', 'Y', $v);
            $geshi = str_ireplace('月', 'm', $geshi);
            $geshi = str_ireplace('日', 'd', $geshi);
            $tmp = str_ireplace($sjs[0][$k], date($geshi, $shijianchuo), $tmp);
        }
    }
    return $tmp;
}

//分页
function fenye($where,$fenye_tmp,$pageSize){
	global $c_sql;
	global $pc_www;
	$zong=$c_sql->select("select count(*) from art where ({$where})");
	$zong=$zong[0]['count(*)'];//总条数
	$totalPage = ceil($zong / $pageSize);//总页数
	
	if(isset($_GET['p'])){
		$p=$_GET['p'];
	}
	else{
		$p=1;
	}
	
	$page_txt='';
	$fenye_tmps=explode('|',$fenye_tmp);
	foreach($fenye_tmps as $v){
		if($v=='首页' && $p>1){
			if(isset($_GET['php']) || isset($_GET['index'])){
				$page_txt.="<a href='search.php".shaixuan_url('p','1','art')."'>首页</a>";
			}
			else{
				$page_txt.="<a href='../search.php".shaixuan_url('p','1','art')."'>首页</a>";
			}
		}
		
		if($v=='上一页' && $p>1){
			if(isset($_GET['php']) || isset($_GET['index'])){
				$page_txt.="<a href='search.php".shaixuan_url('p',($p-1),'art')."'>上一页</a>";
			}
			else{
				$page_txt.="<a href='../search.php".shaixuan_url('p',($p-1),'art')."'>上一页</a>";
			}
			
		}
		if(strstr($v,'页码')){
			$yemas=explode('=',$v);
			$yema=3;
			if(isset($yemas[1])){
				$yema=$yemas[1];
			}
			$yema_py=floor($yema/2);
			
			if($p==1){$star=$p;$end=$p+$yema-1;}
			if($p==2){$star=1;$end=$p+$yema-2;}
			else if($p==$totalPage){$star=$p-($yema-1);$end=$p;}
			else{$star=$p-$yema_py;$end=$p+$yema_py;}
			
			if($end>$totalPage){
				$end=$totalPage;
			}
			
			for($i = $star ; $i <= $end ; $i++){
				if($i==$p){
					$page_txt.='<span>'.$i.'</span>';
				}
				else if($i>0){
					if(isset($_GET['php']) || isset($_GET['index'])){
						$page_txt.="<a href='search.php".shaixuan_url('p',$i,'art')."'>$i</a>";
					}
					else{
						$page_txt.="<a href='../search.php".shaixuan_url('p',$i,'art')."'>$i</a>";
					}
				}
			}
		}
		
		if($v=='末页' && $p<$totalPage){
			if(isset($_GET['php']) || isset($_GET['index'])){
				$page_txt.="<a href='search.php".shaixuan_url('p',$totalPage,'art')."'>末页</a>";
			}
			else{
				$page_txt.="<a href='../search.php".shaixuan_url('p',$totalPage,'art')."'>末页</a>";
			}
		}
		
		if($v=='下一页' && $p<$totalPage){
			if(isset($_GET['php']) || isset($_GET['index'])){
				$page_txt.="<a href='search.php".shaixuan_url('p',($p+1),'art')."'>下一页</a>";
			}
			else{
				$page_txt.="<a href='../search.php".shaixuan_url('p',($p+1),'art')."'>下一页</a>";
			}
		}
		
		if($v=='总数'){
			$page_txt.='<span>总：'.$zong.'条</span>';
		}
	}
	return $page_txt;
}

//seo优化
function seo($html,$seo_arr){
	$arr_th=array('{标题}','{关键词}','{摘要}');
	$html=str_ireplace($arr_th,$seo_arr,$html);
	return $html;
}

//二级地方
/*function difang_er($html){
	global $c_sql;
	global $difangs_dq;
	global $pc_www;
	preg_match_all('/{地方=(.*?)}(.*?){\/地方}/ism',$html,$difangs);
	//地方下1级地方
	if(!isset($difangs[1][0])){return $html;}
	
	$difang_1=$c_sql->select("select id,name from liandong where lid=".$difangs_dq[0]['id'])." order by paixu,id";
	//地方下2级地方
	if(strstr($html,'{子地方}')){
		$difang_2=array();
		if(count($difang_1)>0){
			foreach($difang_1 as $arr){
				$difang_2[$arr['id']][0]=$arr;
				$liandong_2=$c_sql->select("select id,name from liandong where lid=".$arr['id'])." order by paixu,id";
				$difang_2[$arr['id']][1]=$liandong_2;
			}
		}
	}
	
	foreach($difangs[1] as $k=>$v){
		if($v==1){
			//当前地方下1
			if(count($difang_1)>0){
				$difang_1_txt=str_ireplace('[名称]','全'.$difangs_dq[0]['name'],$difangs[2][$k]);
				$difang_1_txt=str_ireplace('[链接]','不限',$difang_1_txt);
				foreach($difang_1 as $arr){
					$tihuan_k=array('[链接]','[名称]','[id]');
					$tihuan_v=array('search.php'.shaixuan_url('s_fabudao',$arr['id'],$s_ziduan),$arr['name'],$arr['id']);
					$difang_1_txt.=str_ireplace($tihuan_k,$tihuan_v,$difangs[2][$k]);
				}
				$html=str_ireplace($difangs[0][$k],$difang_1_txt,$html);
			}
		}
		else if($v==2){
			//当前地方下2
			preg_match_all('/{子地方}(.*?){\/子地方}/ism',$difangs[2][$k],$difangs2);
			if(isset($difangs2[1][0])){
				foreach($difang_2 as $arr){
					$difang_2_txt='';
					$difang_2_zi_txt='';
					foreach($arr[1] as $arr2){
						$tihuan_k=array('[链接]','[名称]','[id]');
						$tihuan_v=array('search.php'.shaixuan_url('s_fabudao',$arr2['id'],$s_ziduan),$arr2['name'],$arr2['id']);
						$difang_2_zi_txt.=str_ireplace($tihuan_k,$tihuan_v,$difangs2[1][0]);
					}
					$difang_2_zi_txt=str_ireplace('[名称]','全'.$arr[0]['name'],$difangs2[1][0]).$difang_2_zi_txt;
					$difang_2_txt=str_ireplace($difangs2[0][0],$difang_2_zi_txt,$difangs[2][$k]);
					$tihuan_k=array('[链接]','[名称]','[id]');
					$tihuan_v=array('search.php'.shaixuan_url('s_fabudao',$arr[0]['id'],$s_ziduan),$arr[0]['name'],$arr[0]['id']);
					
					$difang_2.=str_ireplace($tihuan_k,$tihuan_v,$difang_2_txt);
				}
				$html=str_ireplace($difangs[0][$k],$difang_2,$html);
			}
		}
	}
	return $html;
}*/

//二级地方
function difang_er($html){
	global $c_sql;
	preg_match_all('/{地方}(.*?){\/地方}/ism',$html,$difangs);
	
	if(!isset($difangs[1][0])){return $html;}
	
	$liandong_1=$c_sql->select("select id,name,pinyin from liandong where lid=1");
	$difang_2=array();
	if(count($liandong_1)>0){
		foreach($liandong_1 as $arr){
			$difang_2[$arr['id']][0]=$arr;
			$liandong_2=$c_sql->select("select id,name,pinyin from liandong where lid=".$arr['id']);
			$difang_2[$arr['id']][1]=$liandong_2;
			
		}
	}
	//有子地方添加子地方
	preg_match_all('/{子地方}(.*?){\/子地方}/ism',$difangs[1][0],$zi_difangs);
	
	if(count($difang_2)>0){
		$difang_str='';
		foreach($difang_2 as $arr){
			if(isset($zi_difangs[1][0])){
				$str_2='';
				foreach($arr[1] as $arr_zi){
					$tihuan_b=array('[名称]','[拼音]','[链接]');
					$tihuan_t=array($arr_zi['name'],$arr_zi['pinyin'],'?php&list=1&s_fabudao='.$arr_zi['id']);
					$str_2.=str_ireplace($tihuan_b,$tihuan_t,$zi_difangs[1][0]);
				}
				$str_z=str_ireplace($zi_difangs[0][0],$str_2,$difangs[1][0]);
			}
			//替换一级地方
			$tihuan_b=array('[名称]','[拼音]','[链接]');
			$tihuan_t=array($arr[0]['name'],$arr[0]['pinyin'],'?php&list=1&s_fabudao='.$arr_zi['id']);
			$difang_str.=str_ireplace($tihuan_b,$tihuan_t,$str_z);
		}
		$html=str_ireplace($difangs[0][0],$difang_str,$html);
		
		return $html;
	}
}

//识别跳转
function shibie($html){
	global $kaiqi;
	global $m_www;
	global $pc_www;
	global $ms;
	if(!strstr($kaiqi,'手机')){
		return $html;
	}
	$js='';
	if($ms=='m'){
		/*$tiao=$pc_www;
		$js='<script>'."\r\n";
		$js.="if(navigator.platform.indexOf('Win32')!=-1 || navigator.platform.indexOf('Win64')!=-1){"."\r\n";
		$js.="window.location='{$tiao}'}"."\r\n";
		$js.="</script>";*/
		$js='<script></script>';
	}
	else{
		$tiao=$m_www;
		$js='<script>'."\r\n";
		$js.="if(navigator.platform.indexOf('Win32')!=-1 || navigator.platform.indexOf('Win64')!=-1){}"."\r\n";
		$js.="else{window.location='{$tiao}'}"."\r\n";
		$js.="</script>";
	}
	if(!strstr($html,$js)){
		$html=str_ireplace('<head>',"<head>\r\n{$js}",$html);
	}
	return $html;
}
?>