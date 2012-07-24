<?php
return array(
    'TMPL_ENGINE_TYPE'		=> 'Think',     // 默认模板引擎 以下设置仅对使用Think模板引擎有效
	'TMPL_DETECT_THEME'     => false,       // 自动侦测模板主题
	'TMPL_TEMPLATE_SUFFIX'  => '.tpl.php',     // 默认模板文件后缀
	'TMPL_CACHFILE_SUFFIX'  => '.php',      // 默认模板缓存后缀
	'TMPL_DENY_FUNC_LIST'	=> 'echo,exit',	// 模板引擎禁用函数
	'TMPL_PARSE_STRING'     => '',          // 模板引擎要自动替换的字符串，必须是数组形式。
	'TMPL_L_DELIM'          => '{',			// 模板引擎普通标签开始标记
	'TMPL_R_DELIM'          => '}',			// 模板引擎普通标签结束标记
	'TMPL_VAR_IDENTIFY'     => 'array',     // 模板变量识别。留空自动判断,参数为'obj'则表示对象
	'TMPL_STRIP_SPACE'      => false,       // 是否去除模板文件里面的html空格与换行
	'TMPL_CACHE_ON'			=> true,        // 是否开启模板编译缓存,设为false则每次都会重新编译
	'TMPL_CACHE_TIME'		=>	-1,         // 模板缓存有效期 -1 为永久，(以数字为值，单位:秒)
	'TMPL_ACTION_ERROR'     => 'Public.success', // 默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS'   => 'Public.success', // 默认成功跳转对应的模板文件
	'TMPL_TRACE_FILE'       => VIEW_PATH.'/PageTrace.tpl.php',     // 页面Trace的模板文件
	'TMPL_EXCEPTION_FILE'   => VIEW_PATH.'/DeveException.tpl.php',// 异常页面的模板文件
	'TMPL_FILE_DEPR'=>'/', //模板文件MODULE_NAME与ACTION_NAME之间的分割符，只对项目分组部署有效
	'TMPL_DEFAULT_THEME' => 'default',
	'TMPL_CHARSET' => 'utf-8',
	'TMPL_PARSE_STRING'=>'',
);