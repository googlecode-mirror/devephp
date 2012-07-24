<?php
return array(
	// Think模板引擎标签库相关设定
	'TAGLIB_BEGIN'          => '<',  // 标签库标签开始标记
	'TAGLIB_END'            => '>',  // 标签库标签结束标记
	'TAGLIB_LOAD'           => true, // 是否使用内置标签库之外的其它标签库，默认自动检测
	'TAGLIB_BUILD_IN'       => 'cx', // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔
	'TAGLIB_PRE_LOAD'       => '',   // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔
);
?>
