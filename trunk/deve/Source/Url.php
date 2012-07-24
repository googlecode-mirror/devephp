<?php
return array(
	/* URL设置 */
	'URL_CASE_INSENSITIVE'  => false,   // URL地址是否不区分大小写
	'URL_ROUTER_ON'         => false,   // 是否开启URL路由
	'URL_DISPATCH_ON'       => true,	// 是否启用Dispatcher
	'URL_MODEL'      => 1,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
	// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式) 当URL_DISPATCH_ON开启后有效; 默认为PATHINFO 模式，提供最好的用户体验和SEO支持
	'URL_PATHINFO_MODEL'    => 2,       // PATHINFO 模式,使用数字1、2、3代表以下三种模式:
	// 1 普通模式(参数没有顺序,例如/m/module/a/action/id/1);
	// 2 智能模式(系统默认使用的模式，可自动识别模块和操作/module/action/id/1/ 或者 /module,action,id,1/...);
	// 3 兼容模式(通过一个GET变量将PATHINFO传递给dispather，默认为s index.php?s=/module/action/id/1)
	'URL_PATHINFO_DEPR'     => '/',	// PATHINFO模式下，各参数之间的分割符号
	'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置

);
?>
