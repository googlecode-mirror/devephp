<?php
return array(
    'TMPL_DENY_FUNC_LIST'	=> 'echo,exit',	// 模板引擎禁用函数
	'TMPL_L_DELIM'          => '{',			// 模板引擎普通标签开始标记
	'TMPL_R_DELIM'          => '}',			// 模板引擎普通标签结束标记
	'TMPL_VAR_IDENTIFY'     => 'array',     // 模板变量识别。留空自动判断,参数为'obj'则表示对象
	'TMPL_STRIP_SPACE'      => false,       // 是否去除模板文件里面的html空格与换行
);