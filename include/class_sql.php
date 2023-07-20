<?php
session_start();
error_reporting(E_ALL^E_NOTICE^E_WARNING);
//get过滤
date_default_timezone_set('PRC');//中国时间
header("Content-type: text/html; charset=utf-8"); 
include("function.php");
include("class_curl.php");
include("config.php");
include('search.php');
//数据库操作类
class sql{
	public $host;
	public $username;
	public $password;
	public $dbname;
	public $charset;
	
	//初始化数据库
	public function link()
	{
		//把一批成员属性都初始化
		$this->host = HOST;
		$this->username = USERNAME;
		$this->password = PASSWORD;
		$this->dbname = DBNAME;
		$this->charset = CHARSET;
		$conn = mysqli_connect($this->host,$this->username,$this->password);
		if (!$conn){return flase;}//连接数据库失败处理
		if (!mysqli_select_db($conn,$this->dbname)){return false ;}//选择数据失败处理
		mysqli_set_charset($conn,$this->charset);//设置字符集
		return $conn;
	}
	
	//插入数据库数据安全处理
	function chuli_sql($str){
		if(!get_magic_quotes_gpc()){
			$str=addslashes($str);
		}
		return $str;
	}
	
	//增（表，数组）
	function insert($table,$array){
		$conn=$this->link();//链接字符集
		//拼接成sql语句
		$keys='';
		$vals='';
		foreach($array as $key=>$val){
			if($vals==null){$sep='';}
			else{$sep=',';}
			$keys.=$sep.$key;
			$vals.=$sep."'".$this->chuli_sql($val)."'";
		}
		$sql="insert {$table} ({$keys}) values ({$vals})";
		mysqli_query($conn,$sql);//执行语句
		return mysqli_insert_id($conn);//返回刚刚插入产生的id
	}
	
	//删（表，条件）
	function delete($table,$where=null){
		$conn=$this->link();//链接字符集
		$where=$where==null?null:" where ".$where;
		$sql="delete from {$table} {$where}";
		mysqli_query($conn,$sql);
		return mysqli_affected_rows($conn);//返回1删除成功、返回0删除失败
	}
	
	//改（表，数组，条件）
	function update($table,$array,$where=null){
		$conn=$this->link();//链接字符集
		$str='';
		foreach($array as $key=>$val){
			if($str==null){$sep='';	}
			else{$sep=',';}
			$str.=$sep.$key."='".$this->chuli_sql($val)."'";
		}
		$sql="update {$table} set {$str}".($where==null?null:" where ".$where);
		mysqli_query($conn,$sql);
		return mysqli_affected_rows($conn);//返回刚刚插入产生的id
	}
	
	//查（查询语句）
	public function select($sql){
		$query = mysqli_query($this->link(),$sql);
		$data=array();
		if($query && mysqli_num_rows($query)){
			while($row = mysqli_fetch_assoc($query)){
				$data[] = $row;
			}
		}
		return $data;
	}
	
	//执行
	function zhixing($sql){
		if(!mysqli_query($this->link(),$sql)){return 0;}
		else{return 1;}
	}
	
	//栏目递归调用s
	public function getlist($tid=0,&$result=array(),$spac=0){
		$spac=$spac+1;
		$sql="select * from type where tid='$tid' order by paixu asc";
		$res=mysqli_query($this->link(),$sql);
		while($row=mysqli_fetch_assoc($res)){
			$row['jibie']=$spac;
			$result[]=$row;
			$row['jibie']=$spac;
			$this->getlist($row['id'],$result,$spac);
		}
		
		return $result;	
	}	
	//递归调用s
	
	//栏目递归调用s
	public function getlist_mx($tid=0,&$result=array(),$spac=0){
		$spac=$spac+1;
		$sql="select * from moxing where mid='$tid' order by paixu asc";
		$res=mysqli_query($this->link(),$sql);
		while($row=mysqli_fetch_assoc($res)){
			$row['jibie']=$spac;
			$result[]=$row;
			$row['jibie']=$spac;
			$this->getlist_mx($row['id'],$result,$spac);
		}
		return $result;	
	}	
	//递归调用s
	
	//栏目递归调用s
	public function getlist_ly_mx($lid=0,&$result=array(),$spac=0){
		$spac=$spac+1;
		$sql="select * from liuyanmx where lid='$lid' order by paixu asc";
		$res=mysqli_query($this->link(),$sql);
		while($row=mysqli_fetch_assoc($res)){
			$row['jibie']=$spac;
			$result[]=$row;
			$row['jibie']=$spac;
			$this->getlist_ly_mx($row['id'],$result,$spac);
		}
		return $result;	
	}	
	//递归调用s
	
	//递归s(表，副id)
	public function digui($table,$ziduan,$tid=0,&$result=array(),$spac=0){
		$spac=$spac+1;
		$sql="select * from $table where $ziduan='$tid' order by paixu,id asc";
		$res=mysqli_query($this->link(),$sql);
		while($row=mysqli_fetch_assoc($res)){
			$row['jibie']=$spac;
			$result[]=$row;
			$row['jibie']=$spac;
			$this->digui($table,$ziduan,$row['id'],$result,$spac);
		}
		return $result;	
	}	
	//递归e
	
	//反查询s(表，副id)
	public function diguif($table,$ziduan,$tid=0,&$result=array()){
		$res=$this->select("select id,{$ziduan} from $table where id=$tid");//获取所有表
		if(count($res)>0){
			$result[]=$res[0];
			$this->diguif($table,$ziduan,$res[0][$ziduan],$result);
		}
		krsort($result);
		return $result;
	}	
	//反查询e

	


	//数据备份(进度)
	public function beifen($jindu=0,$page=1){
		$path="../data/20".date('y-m-d',time());//当前目录
		
		//只有jindu==0是才清空表
		if($jindu==0 && $page==1){
			if(!file_exists($path)){mkdir ($path,0777,true);}
			else{delDirAndFile($path,0);}
		}
		$tables=$this->select("show tables");//获取所有表
		if(isset($tables[$jindu])){
			foreach($tables[$jindu] as $v){
				$table=$v;//当前的表名称
			}
			
			//备份表结构S
			if($page==1){
				$table_jiegou=$this->select("show create table {$table}");//表结构
				$table_jiegou=$table_jiegou[0]['Create Table'];
				preg_match_all("/MyISAM(.*?)DEFAULT/i",$table_jiegou,$jiegous);
				$jiegou=str_ireplace($jiegous[1][0],' ',$table_jiegou);
				file_put_contents($path.'/'.$table.'.txt',$jiegou);
			}
			//备份表数据S
			$shu=5;//每页多少条数据量
			$qishi=($page-1)*$shu;
			$shujus=$this->select("select * from {$table} order by id asc limit $qishi,$shu");
			if(count($shujus)>0){
				$intxt=json_encode($shujus);//转为数组
				file_put_contents($path.'/'.$table.'_'.$page.'.txt',$intxt);
				return "正在备份表[<b>{$table}</b>]的第[<b>{$page}</b>]页";
			}
			else{
				return 1;//下一个表返回1
			}
		}
		else{
			return 0;//备份结束返回0
		}

	}
	
	//数据还原
	public function huanyuan($riqi,$jindu=2){
		$path="../data/$riqi";//还原的数据路径
		$sqls=scandir($path);//获取数据文件SQL
		if(isset($sqls[$jindu])){
			$table=$sqls[$jindu];
			$table_n=str_replace(".txt",'',$table);
			if(!strstr($table,'_')){
				$this->zhixing("DROP TABLE IF EXISTS $table_n");
			}
			$url=$path.'/'.$sqls[$jindu];//sql文件路径
			$sql=file_get_contents($url);//sql语句
			
			if(strstr($table,'_')){
				$sql_arr=json_decode($sql,true);
				$tables=explode('_',$table);
				$table_name=$tables[0];
				foreach($sql_arr as $arr){
					if($this->insert($table_name,$arr)==0){
						setcookie('cuo',$url);
						return "<span style='color:#F00'>".$url.'文件代码有误</span>';
					}
				}
				return "正在还原数据".$url;
			}
			else{
				if($this->zhixing($sql)==1){
					return "正在还原数据".$url;
				}
				else{
					setcookie('cuo',$url);
					return "<span style='color:#F00'>".$url.'文件代码有误</span>';
				}
			}
		}
		else{
			$url=$_SERVER['REQUEST_URI'];
			$urls=explode('/',$url);
			$path_dq=$urls[count($urls)-5];//当前网站安装路径
			if($path_dq==''){$path_dq='/';}
			else{$path_dq="/{$path_dq}/";}
			$array['neirong']=$path_dq;
			$this->update('info',$array,"diaoyongbiaoqian='电脑站网址'");
			$array['neirong']=$path_dq.'m/';
			$this->update('info',$array,"diaoyongbiaoqian='手机站网址'");
			return '100';	
		}
	}
	
	//读取单条记录(表名，条件)
	public function dan($table,$ziduan,$where){
		if($where!=''){
			$where=" where $where";
		}
		$res=$this->select("select {$ziduan} from {$table}{$where}");
		return $res[0];
	}
}
$c_sql=new sql;
$c_curl=new Curl;
?>