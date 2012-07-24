<?php
// +----------------------------------------------------------------------
// | DevePHP [ EASIER EFFICIENT SECURE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://devephp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yearnfar <yearnfar@gmail.com>
// +----------------------------------------------------------------------

//记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);
if(!defined('APP_PATH')) define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']));

if(version_compare(PHP_VERSION,'5.0.0','<'))  die('require PHP > 5.0 !');
// DevePHP系统目录定义
defined('DEVE_PATH') or define('DEVE_PATH', dirname(__FILE__));
defined('APP_NAME') or define('APP_NAME', basename(dirname($_SERVER['SCRIPT_FILENAME'])));
defined('IS_DEBUG') or define('IS_DEBUG',false);       // 是否为调试模式
defined('TIMESTAMP') or define('TIMESTAMP',time());    // 系统时间
defined('EACS') or define('EACS',function_exists('eaccelerator_get'));  // 是否将资源代码放进内存
if(is_file(APP_PATH.'/~runtime.php')){
	// 加载框架核心编译缓存
	require APP_PATH.'/~runtime.php';
}else{
	// 加载编译函数文件
	require DEVE_PATH."/Code/Core/Builder.php";
	// 生成核心编译~runtime缓存
	build_runtime();
}

// 记录加载文件时间
$GLOBALS['_loadTime'] = microtime(TRUE);
?>
