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


// 生成核心编译缓存
function build_runtime() {
	// 加载常量定义文件
	require DEVE_PATH.'/Config/Const.php';

	// 定义核心编译的文件
	$runtime[]  =  DEVE_PATH.'/Core/Functions.php'; // 系统函数
	if(version_compare(PHP_VERSION,'5.2.0','<') )
		// 加载兼容函数
		$runtime[]	=	 DEVE_PATH.'/Core/Compat.php';
	// 核心基类必须加载
	$runtime[]  =  DEVE_PATH.'/Core/Deve.class.php';
	// 读取项目核心编译文件列表
	if(is_file(CONFIG_PATH.'/Core.php'))
		// 加载项目自定义的核心编译文件列表
		$list = include CONFIG_PATH.'/Core.php';
    else
	    // 默认核心
	    $list = include DEVE_PATH.'/Config/Core.php';

	$runtime = array_merge($runtime,$list);
	// 加载核心编译文件列表
	foreach ($runtime as $key=>$file){
		if(is_file($file))  require $file;
	}
	
	// 生成运行模式缓存
	if(!defined('IS_DEBUG') || IS_DEBUG == false) {
		check_app_dirs();   // 先校验目录的完整性	
		$compile = false;
		$content = compile(DEVE_PATH.'/Config/Const.php',$compile);
		foreach ($runtime as $file){
			$content .= compile($file,$compile);
		}
		if(defined('STRIP_RUNTIME_SPACE') && STRIP_RUNTIME_SPACE == false ) {
			file_put_contents(APP_PATH.'/~runtime.php','<?php'.$content);
		}else{
			file_put_contents(APP_PATH.'/~runtime.php',strip_whitespace('<?php'.$content));
		}
		unset($content);
	}
}

// 批量创建目录
function mkdirs($dirs,$mode=0777) {
    foreach ($dirs as $dir){
        is_dir($dir) or mkdir($dir,$mode);
    }
}

function check_app_dirs() {
// 没有创建项目目录的话自动创建
	is_dir(APP_PATH) or mk_dir(APP_PATH,0777);
	if(is_writeable(APP_PATH)) {
		$dirs  = array(
			CONFIG_PATH,
			CONSOLE_PATH,
			CONTROLLER_PATH,
			DATA_PATH,
			LIB_PATH,
			LOCALE_PATH,
			MODEL_PATH,
			PLUGIN_PATH,
			TEST_PATH,
			TMP_PATH,
			LOGS_PATH,
			CACHE_PATH,
			VENDOR_PATH,
			VIEW_PATH,
			WEBROOT_PATH,
			CONTROLLER_PATH.'/Index'
		);
		mkdirs($dirs);
		// 目录安全写入
		if(defined('BUILD_DIR_SECURE') && BUILD_DIR_SECURE==true) {
			if(!defined('DIR_SECURE_FILENAME')) define('DIR_SECURE_FILENAME','index.html');
			if(!defined('DIR_SECURE_CONTENT')) define('DIR_SECURE_CONTENT',' ');
			// 自动写入目录安全文件
			$content        =   DIR_SECURE_CONTENT;
			$a = explode(',', DIR_SECURE_FILENAME);
			foreach ($a as $filename){
				foreach ($dirs as $dir)
					file_put_contents($dir.$filename,$content);
			}
		}
                
		// 写入配置文件
		if(!is_file(CONFIG_PATH.'/Config.php'))
			file_put_contents(CONFIG_PATH.'/Config.php',"<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);\n?>");
		// 写入测试Action
		if(!is_file(CONTROLLER_PATH.'/Index/IndexAction.class.php')) {
			$content =
'<?php
    // 本类由系统自动生成，仅供测试用途
    class IndexAction extends Control{
	      public function show(){
		       header("Content-Type:text/html; charset=utf-8");
		       echo "<div style=\'font-weight:normal;color:blue;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;font-size:14px;font-family:Tahoma\'>^_^ Hello,欢迎使用<span style=\'font-weight:bold;color:red\'>DevePHP</span></div>";
	      }
    }
?>';
	         file_put_contents(CONTROLLER_PATH.'/Index/IndexAction.class.php',$content);
	   }
    }else{
	    header("Content-Type:text/html; charset=utf-8");
	    exit('<div style=\'font-weight:bold;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>项目目录不可写，目录无法自动生成！<BR>请使用项目生成器或者手动生成项目目录~</div>');
    }
}
?>
