var height=$(".box").outerHeight();//获取高度
var height_window=$(window).height();//浏览器可见区域高度
var martop=(height_window-height)/2;
$(".box").css("margin-top",martop+"px");

