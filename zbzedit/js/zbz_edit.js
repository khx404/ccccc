var items=$(".zbzedit"); //获取网页中所有的编辑器元素
for (var i=0;i<items.length;i++){ //由于获取的是数组对象,因此需要把它循环出来
	var id=$(items[i]).attr("id");//编辑器id
	var data_path=$(items[i]).attr("data-path");//图片存储路径
	var data_path_res=$(items[i]).attr("data-path-res");//图片回调路径
	var data_height=$(items[i]).attr("data-height");//编辑器高
	var data_pic_name=$(items[i]).attr("data-pic-name");//图片命名方式
	var iframe="<iframe width='100%' height='"+data_height+"' data-path='"+data_path+"' data-path-res='"+data_path_res+"' scrolling='no' id='"+id+"_iframe' src='../zbzedit/edit.html' frameborder='0' class='zbz_edit' data-pic-name='"+data_pic_name+"'></iframe>";
	$(items[i]).after(iframe);
}
