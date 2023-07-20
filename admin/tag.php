<?php include('c_top.php');?>
<!--主体-->
<div class="con">
	<div class="con_left">
    	<ul class="con_nav">
        	<li class="tit"><span>标签生成</span></li>
            <li><a class="lmtype_list">标签生成</a></li>
        </ul>
    </div>
	<!--右侧-->
	<div class="con_right">
    	<div class="tit">
        <a class="dq0" onmousemove="dq(0)">公用标签</a> / 
        <a class="dq1" onmousemove="dq(1)">栏目页标签</a> / 
        <a class="dq2" onmousemove="dq(2)">详情页标签</a>
        </div>
        <table class='from tag'>
        	<tr class="tr0"><th class="w250">网站title标题</th><td><input value="{标题}" /></td></tr>
        	<tr class="tr0"><th class="w250">网站keywords关键词</th><td><input value="{关键词}" /></td></tr>
        	<tr class="tr0"><th class="w250">网站description摘要</th><td><input value="{摘要}" /></td></tr>
            <tr class="tr0"><th class="w250">样式相对路径</th><td><input value="{样式路径}" /></td></tr>
            <tr class="tr0"><th class="w250">包含xxx.html文件</th><td><input value="{包含=xxx.html}" /></td></tr>
            <tr class="tr0"><th>首页</th><td><input value="{首页}" /></td></tr>
            <tr class="tr0"><th>指定栏目链接</th><td><input value="{栏目链接=对应栏目id}" /></td></tr>
            <tr class="tr0"><th>指定栏目名称</th><td><input value="{栏目名称=对应栏目id}" /></td></tr>
            <tr class="tr0"><th>指定栏目内容</th><td><input value="{栏目内容=对应栏目id|字数=100}" /></td></tr>
            <tr class="tr0"><th>导航条（无子栏目）</th><td><textarea style="height:90px;">
<a href='{首页}' class='{首页样式=on}'>首页</a>
{导航：编号=全部|条=全部|当前样式=on}
<a href='[链接]' class='li[递增][当前样式]'>[栏目名称][栏目图片][优化摘要]</a>
{/导航}</textarea></td></tr>

            <tr class="tr0"><th>导航条（有子栏目）</th><td><textarea style="height:140px;">
<a href='{首页}' class='{首页样式=on}'>首页</a>
{导航：编号=全部|条=全部|当前样式=on}
<a href='[链接]' class='li[递增][当前样式]'>[栏目名称][栏目图片][优化摘要]</a>
    {子导航}
    <a href='[链接]' class='li[递增]'>[栏目名称][栏目图片][优化摘要]</a>
    {/子导航}
{/导航}</textarea></td></tr>

            <tr class="tr0"><th>文章调用</th><td><textarea>
{文章：栏目=1,2|起=0|条=6|推荐=头条|排序=升|缩略图=有}
<a href='[链接]' id='[递增]'>[id][标题=截取字数][缩略图][发布时间=年/月/日]</a>
{/文章}</textarea></td></tr>

		<tr class="tr0"><th>基本设置参数调用</th><td><input value="{参数名称}" /></td></tr>
        <tr class="tr0"><th>友情链接</th><td><textarea style=" height:70px;">
{友情链接}
<a href='[链接]' target='_blank'>[标题][图片]</a>
{/友情链接}</textarea>
</td></tr>
        <tr class="tr0"><th>广告</th><td><input value="{广告=广告ID}" /></td></tr>
        <tr class="tr0"><th>当前站点名称</th><td><input value="{当前站}" /></td></tr>
        <tr class="tr0"><th>当前站下2级地方</th><td><input value="{地方下=2}" /></td></tr>

        <tr class="tr0"><th class="w250">地方站</th><td><textarea style=" height:70px;">
{地方站}
<a href="[链接]" target="_blank">[地方名]</a>
{/地方站}</textarea>
        </td></tr>
        
        <tr class="tr0"><th class="w250">获取全部地方</th><td><textarea style=" height:70px;">
{地方}
<a href='[链接]'>全部[名称]</a>
{子地方}<a href='[链接]'>[名称]</a>{/子地方}
{/地方}</textarea>
        </td></tr>
        

        
        
        <tr class="tr0"><th class="w250">客户留言</th><td><textarea style=" height:140px;">
&lt;form id="liuyan1" name="学生报名" onsubmit="return liuyan('liuyan1')">
姓名：<input name="姓名" /><br />
性别：<select name="性别"><option value="男">男</option><option value="女">女</option></select><br />
手机号：<input name="手机号" /><br />
<input type="submit" onclick="liuyan('liuyan1')" value="提交" />
</form>

<script src="{电脑站网址}cms/common/js/jquery.min.js"></script>
<script>
function liuyan(id){
	//各种认证写在这里下面
	var title=$('#'+id).attr('name');//表单名称
	var post= $('#'+id).serializeArray();
	$.post('{电脑站网址}cms/common/php/ajax.php?run=liuyan&title='+title+'&toemail=807015853@qq.com',post,function(res){
		if(res==1){alert('留言成功！');}
		else{alert('留言失败');}
	});
	return false;
}
</script></textarea>
        </td></tr>
        
        <tr class="tr0"><th class="w250">搜索</th><td><textarea style=" height:140px;">
&lt;form method="get" action="{电脑站网址}show.php" class="form" >
<input name="ms" value="pc" type="hidden"/> 
<input name="list" value="1" type="hidden"/> 
<input name="sou_biaoti" type="text" value="" placeholder="搜索关键词" /> 
<input type="submit" class="searanniu" value="搜索" /> 
&lt;/form> </textarea>
        </td></tr>


        <tr class="tr1"><th class="w250">当前位置</th><td><input value="<a href='{首页}'>首页</a> > <a href='{栏目链接=当前}'>{栏目名称=当前}</a>" /></td></tr>
		<tr class="tr1"><th class="w250">当前栏目名称</th><td><input value="{栏目名称=当前}" /></td></tr>
        <tr class="tr1"><th class="w250">当前栏目链接</th><td><input value="{栏目链接=当前}" /></td></tr>
        <tr class="tr1"><th class="w250">栏目内容</th><td><input value="{栏目内容}" /></td></tr>
        <tr class="tr1"><th>文章调用</th><td><textarea>
{列表=5}
<a href='[链接]' id='[递增]'>[id][标题=截取字数][缩略图][发布时间=年/月/日]</a>
{/列表}</textarea></td></tr>
		<tr class="tr1"><th class="w250">分页条</th><td><textarea style=" height:140px;">
&lt;p class='zbzpage'>{分页=首页|上一页|页码|下一页|末页|总数}</p>
<style type='text/css'>
.zbzpage{ padding:20px 0;}
.zbzpage a,.zbzpage span{ padding:5px 10px; margin-left:5px;}
.zbzpage a{ border:1px solid #ccc;color:#333}
.zbzpage a:hover{ border:1px solid #03F;color:#03F}
.zbzpage span{ border:1px solid #ccc; background:#ccc; color:#333}
</style></textarea>
        </td></tr>
        
        
        <tr class="tr1"><th class="w250">筛选功能</th><td>
        <textarea>
地方：{筛选=发布到}<a href="[链接]"> [条件] </a>{/筛选}
价格：{筛选=价格}<a href="[链接]"> [条件] </a>{/筛选}
楼层：{筛选=楼层}<a href="[链接]"> [条件] </a>{/筛选}</textarea>
        </td></tr>
            
            <tr class="tr2"><th class="w250">当前位置</th><td><input value="<a href='{首页}'>首页</a> > <a href='{栏目链接=当前}'>{栏目名称=当前}</a> > {标题}" /></td></tr>
            <tr class="tr2"><th class="w250">文章标题</th><td><input value="{标题}" /></td></tr>
            <tr class="tr2"><th class="w250">文章标题截取</th><td><input value="{标题=5}" /></td></tr>
            <tr class="tr2"><th>发布时间</th><td><input value="{发布时间=年/月/日}" /></td></tr>
            <tr class="tr2"><th>点击量</th><td><input value="{点击}" /></td></tr>
            <tr class="tr2"><th>缩略图</th><td><input value="{缩略图}" /></td></tr>
            <tr class="tr2"><th>多图循环读取</th><td><input value="{多图=调用名称}<img src='[图片路径]'>{/多图}" /></td></tr>
            <tr class="tr2"><th>摘要</th><td><input value="{摘要}" /></td></tr>
            <tr class="tr2"><th>内容</th><td><input value="{内容}" /></td></tr>
            <tr class="tr2"><th>上一篇链接</th><td><input value="{上一篇链接}" /></td></tr>
            <tr class="tr2"><th>上一篇标题</th><td><input value="{上一篇标题}" /></td></tr>
            <tr class="tr2"><th>下一篇链接</th><td><input value="{下一篇链接}" /></td></tr>
            <tr class="tr2"><th>下一篇标题</th><td><input value="{下一篇标题}" /></td></tr>
            <tr class="tr2"><th>其他</th><td><input value="{其他}" /></td></tr>
            
            <tr class="but"><th></th><td><a class='queren margin_right5'>视频教程</a>
            <span class="ts"></span></td></tr>
        </table>
        <div class="cle70"></div>
    </div>
</div>
<?php 
include('c_foot.php');
?>
<script>
dq(0);
function dq(id){
	for(i=0;i<=2;i++){
		if(id==i){
			$(".tr"+i).show();
			$(".dq"+i).css("font-weight","bold");
		}
		else{
			$(".tr"+i).hide();
			$(".dq"+i).css("font-weight","normal");
		}
	}
}

//复制
$('.tag input,.tag textarea').click(function(){
	  $(this).select();
      document.execCommand("copy"); // 执行浏览器复制命令
      $('.ts').html("<span class='tishi'><font color='#1aacda'>已复制！</font></span>");
	  $(".ts span").fadeOut(1000);
});
</script>