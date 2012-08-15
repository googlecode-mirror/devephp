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


/**
 +------------------------------------------------------------------------------
 * DevePHP公共函数库
 +------------------------------------------------------------------------------
 * @category   DevePHP
 * @package  Common
 * @author   yearnfar <yearnfar@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */

// URL组装 支持不同模式和路由
function U($url,$params=array(),$redirect=false,$suffix=true) {
	if(0===strpos($url,'/'))
		$url   =  substr($url,1);
	if(!strpos($url,'://')) // 没有指定项目名 使用当前项目名
		$url   =  APP_NAME.'://'.$url;
	if(stripos($url,'@?')) { // 给路由传递参数
		$url   =  str_replace('@?','@think?',$url);
	}elseif(stripos($url,'@')) { // 没有参数的路由
		$url   =  $url.MODULE_NAME;
	}
	// 分析URL地址
	$array   =  parse_url($url);

	$app      =  isset($array['scheme'])?   $array['scheme']  :APP_NAME;
	$route    =  isset($array['user'])?$array['user']:'';
	if(defined('GROUP_NAME') && strcasecmp(GROUP_NAME,S('DEFAULT_GROUP')))
		$group=  GROUP_NAME;
	if(isset($array['path'])) {
		$action  =  substr($array['path'],1);
		if(!isset($array['host'])) {
			// 没有指定模块名
			$module = MODULE_NAME;
		}else{// 指定模块
			if(strpos($array['host'],'-')) {
				list($group,$module) = explode('-',$array['host']);
			}else{
				$module = $array['host'];
			}
		}
	}else{ // 只指定操作
		$module = MODULE_NAME;
		$action   =  $array['host'];
	}
	if(isset($array['query'])) {
		parse_str($array['query'],$query);
		$params = array_merge($query,$params);
	}

	if(S('URL_DISPATCH_ON') && S('URL_MODEL')>0) {
		$depr = S('URL_PATHINFO_MODEL')==2?S('URL_PATHINFO_DEPR'):'/';
		$str    =   $depr;
		foreach ($params as $var=>$val)
			$str .= $var.$depr.$val.$depr;
		$str = substr($str,0,-1);
		$group   = isset($group)?$group.$depr:'';
		if(!empty($route)) {
			$url    =   str_replace(APP_NAME,$app,__APP__).'/'.$group.$route.$str;
		}else{
			$url    =   str_replace(APP_NAME,$app,__APP__).'/'.$group.$module.$depr.$action.$str;
		}
		if($suffix && S('URL_HTML_SUFFIX'))
			$url .= S('URL_HTML_SUFFIX');
	}else{
		$params =   http_build_query($params);
		if(isset($group)) {
			$url    =   str_replace(APP_NAME,$app,__APP__).'?'.S('APP_VAR_GROUP').'='.$group.'&'.S('APP_VAR_MODULE').'='.$module.'&'.S('APP_VAR_ACTION').'='.$action.'&'.$params;
		}else{
			$url    =   str_replace(APP_NAME,$app,__APP__).'?'.S('APP_VAR_MODULE').'='.$module.'&'.S('APP_VAR_ACTION').'='.$action.'&'.$params;
		}
	}
	if($redirect)
		redirect($url);
	else
		return $url;
}

/**
 +----------------------------------------------------------
 * 字符串命名风格转换
 * type
 * =0 将Java风格转换为C的风格
 * =1 将C风格转换为Java的风格
 +----------------------------------------------------------
 * @access protected
 +----------------------------------------------------------
 * @param string $name 字符串
 * @param integer $type 转换类型
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function parse_name($name,$type=0) {
	if($type) {
		return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
	}else{
		$name = preg_replace("/[A-Z]/", "_\\0", $name);
		return strtolower(trim($name, "_"));
	}
}

// 错误输出
function halt($error) {
	if(IS_CLI)   exit ($error);
	$e = array();
	if(S('APP_DEBUG')){
		//调试模式下输出错误信息
		if(!is_array($error)) {
			$trace = debug_backtrace();
			$e['message'] = $error;
			$e['file'] = $trace[0]['file'];
			$e['class'] = $trace[0]['class'];
			$e['function'] = $trace[0]['function'];
			$e['line'] = $trace[0]['line'];
			$traceInfo='';
			$time = date("y-m-d H:i:m");
			foreach($trace as $t)
			{
				$traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
				$traceInfo .= $t['class'].$t['type'].$t['function'].'(';
				$traceInfo .= implode(', ', $t['args']);
				$traceInfo .=")<br/>";
			}
			$e['trace']  = $traceInfo;
		}else {
			$e = $error;
		}
		// 包含异常页面模板
		include S('APP_TMPL_EXCEPTION_FILE');
	}
	else
	{
		//否则定向到错误页面
		$error_page =   S('APP_SHOW_ERROR_PAGE');
		if(!empty($error_page)){
			redirect($error_page);
		}else {
			if(S('APP_SHOW_ERROR_MSG'))
				$e['message'] =  is_array($error)?$error['message']:$error;
			else
				$e['message'] = S('APP_SHOW_ERROR_MESSAGE');
			// 包含异常页面模板
			include S('APP_TMPL_EXCEPTION_FILE');
		}
	}
	exit;
}

// URL重定向
function redirect($url,$time=0,$msg='')
{
	//多行URL地址支持
	$url = str_replace(array("\n", "\r"), '', $url);
	if(empty($msg))
		$msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
	if (!headers_sent()) {
		// redirect
		if(0===$time) {
			header("Location: ".$url);
		}else {
			header("refresh:{$time};url={$url}");
			echo($msg);
		}
		exit();
	}else {
		$str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if($time!=0)
			$str   .=   $msg;
		exit($str);
	}
}

// 自定义异常处理
function throw_exception($msg,$type='DeveException',$code=0)
{
	if(IS_CLI)   exit($msg);
	if(class_exists($type,false))
		throw new $type($msg,$code,true);
	else
		halt($msg);        // 异常类型不存在则输出错误信息字串
}

// 区间调试开始
function debug_start($label='')
{
	$GLOBALS[$label]['_beginTime'] = microtime(TRUE);
	if ( MEMORY_LIMIT_ON )  $GLOBALS[$label]['_beginMem'] = memory_get_usage();
}

// 区间调试结束，显示指定标记到当前位置的调试
function debug_end($label='')
{
	$GLOBALS[$label]['_endTime'] = microtime(TRUE);
	echo '<div style="text-align:center;width:100%">Process '.$label.': Times '.number_format($GLOBALS[$label]['_endTime']-$GLOBALS[$label]['_beginTime'],6).'s ';
	if ( MEMORY_LIMIT_ON )  {
		$GLOBALS[$label]['_endMem'] = memory_get_usage();
		echo ' Memories '.number_format(($GLOBALS[$label]['_endMem']-$GLOBALS[$label]['_beginMem'])/1024).' k';
	}
	echo '</div>';
}

// 浏览器友好的变量输出
function dump($var, $echo=true,$label=null, $strict=true)
{
	$label = ($label===null) ? '' : rtrim($label) . ' ';
	if(!$strict) {
		if (ini_get('html_errors')) {
			$output = print_r($var, true);
			$output = "<pre>".$label.htmlspecialchars($output,ENT_QUOTES)."</pre>";
		} else {
			$output = $label . " : " . print_r($var, true);
		}
	}else {
		ob_start();
		var_dump($var);
		$output = ob_get_clean();
		if(!extension_loaded('xdebug')) {
			$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
			$output = '<pre>'. $label. htmlspecialchars($output, ENT_QUOTES). '</pre>';
		}
	}
	if ($echo) {
		echo($output);
		return null;
	}else
		return $output;
}

// 取得对象实例 支持调用类的静态方法
function get_instance_of($name,$method='',$args=array())
{
	static $_instance = array();
	$identify   =   empty($args)?$name.$method:$name.$method.to_guid_string($args);
	if (!isset($_instance[$identify])) {
		if(class_exists($name)){
			$o = new $name();
			if(method_exists($o,$method)){
				if(!empty($args)) {
					$_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
				}else {
					$_instance[$identify] = $o->$method();
				}
			}
			else
				$_instance[$identify] = $o;
		}
		else
			halt(L('_CLASS_NOT_EXIST_').':'.$name);
	}
	return $_instance[$identify];
}

/**
 +----------------------------------------------------------
 * 系统自动加载ThinkPHP基类库和当前项目的model和Action对象
 * 并且支持配置自动加载路径
 +----------------------------------------------------------
 * @param string $name 对象类名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function __autoload($name)
{
	// 检查是否存在别名定义 
	if(alias_import($name)) return ;
	// 自动加载当前项目的Actioon类和Model类
	if(require_cache(LIB_PATH.'/'.$name.'.class.php')
		|| require_cache(MODEL_PATH.'/'.$name.'.class.php'))
		return;  

	// 根据自动加载路径设置进行尝试搜索
	if(S('APP_AUTOLOAD_PATH')) {
		$paths  =   explode(',',S('APP_AUTOLOAD_PATH'));
		foreach ($paths as $path){
			if(import($path.$name)) {
				// 如果加载类成功则返回
				return ;
			}
		}
	}
	return ;
}

// 优化的require_once
function require_cache($filename)
{
	static $_importFiles = array();
	$filename   =  realpath($filename);
	if (!isset($_importFiles[$filename])) {
		if(file_exists_case($filename)){
			require $filename;
			$_importFiles[$filename] = true;
		}
		else
		{
			$_importFiles[$filename] = false;
		}
	}
	return $_importFiles[$filename];
}

// 区分大小写的文件存在判断
function file_exists_case($filename) {
	if(is_file($filename)) {
		if(IS_WIN && S('APP_FILE_CASE_INSENSITIVE')) {
			if(basename(realpath($filename)) != basename($filename))
				return false;
		}
		return true;
	}
	return false;
}

/**
 +----------------------------------------------------------
 * 导入所需的类库 同java的Import
 * 本函数有缓存功能
 +----------------------------------------------------------
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function import($class,$baseUrl = '',$ext='.class.php')
{
	static $_file = array();
	static $_class = array();
	$class    =   str_replace(array('.','#'), array('/','.'), $class);
	if('' === $baseUrl && false === strpos($class,'/')) {
		// 检查别名导入
		return alias_import($class);
	}
	if(isset($_file[$class.$baseUrl]))
		return true;
	else
		$_file[$class.$baseUrl] = true;
	$class_strut = explode("/",$class);

	if(empty($baseUrl)) {
		$baseUrl   =  APP_PATH;
		if('C' == $class_strut[0] ) {
			// 加载控制器类   
			$class =  str_replace('C/',CONTROLLER_DIR.'/',$class);
		}elseif('M' == $class_strut[0]) {
			// 加载数据库模型类
			$class =  str_replace('M/',MODEL_DIR.'/',$class);  
		}else{
			// 加载公共类库
			$class =  str_replace($class_strut[0],LIB_DIR.'/'.$class_strut[0].'/',$class); 
		}
	}
	if(substr($baseUrl, -1) != "/")    $baseUrl .= "/";

	$classfile = $baseUrl . $class . $ext;
	if($ext == '.class.php' && is_file($classfile)) {
		// 冲突检测
		$class = basename($classfile,$ext);
		if(isset($_class[$class]))
			throw_exception(L('_CLASS_CONFLICT_').':'.$_class[$class].' '.$classfile.' '.$class);
		$_class[$class] = $classfile;
	}
	//导入目录下的指定类库文件
	return require_cache($classfile);
}

/**
 +----------------------------------------------------------
 * 基于命名空间方式导入函数库
 * load('@.Util.Array')
 +----------------------------------------------------------
 * @param string $name 函数库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function load($name,$baseUrl='',$ext='.php') {
	$name    =   str_replace(array('.','#'), array('/','.'), $name);
	if(empty($baseUrl)) {
		if(0 === strpos($name,'@/')) {
			//加载当前项目函数库
			$baseUrl   =  APP_PATH.'/Common/';
			$name =  substr($name,2);
		}else{
			//加载ThinkPHP 系统函数库
			$baseUrl =  SYS_PATH.'/Etc/';
		}
	}
	if(substr($baseUrl, -1) != "/")    $baseUrl .= "/";
	include $baseUrl . $name . $ext;
}

// 快速导入第三方框架类库
// 所有第三方框架的类库文件统一放到 系统的Vendor目录下面
// 并且默认都是以.php后缀导入
function vendor($class,$baseUrl = '',$ext='.php')
{
	if(empty($baseUrl))  $baseUrl    =   PLUS_PATH;
	return import($class,$baseUrl,$ext);
}

// 快速定义和导入别名
function alias_import($alias,$classfile='') {
	static $_alias   =  array();
	if('' !== $classfile) {
		// 定义别名导入
		$_alias[$alias]  = $classfile;
		return ;
	}
	if(is_string($alias)) {
		if(isset($_alias[$alias]))
			return require_cache($_alias[$alias]);
	}elseif(is_array($alias)){
		foreach ($alias as $key=>$val)
			$_alias[$key]  =  $val;
		return ;
	}
	return false;
}

// 获取配置值
function C($name=null,$value=null)
{
	static $_config;
	// 名字为空或者不是字符串
	if(empty($name) || is_array($name))return;
	if($value===null){

		if(isset($_config[$name]))return $_config[$name];

		if($name[0] !== '{'){
			false === strpos($name,'.') && $name = MODULE_NAME.CONTROL_NAME.'.'.$name;
			$name = '{System.'.$name.'}';
		} 
		preg_match('/\{System\.([\w]+)\.?([\w]+)?\}/',$name,$matches);	
		if(!isset($_config[$matches[1]])){
			if(EACS && !is_null(eaccelerator_get($matches[1]))){
				$_config[$matches[1]] = eaccelerator_get($matches[1]);
			}else{
				$_config[$matches[1]] = file_exists(CONFIG_PATH."/System/{$matches[1]}.config.php")?include(CONFIG_PATH."/System/{$matches[1]}.config.php"):array();
				EACS && eaccelerator_put($matches[1],$_config[$matches[1]],600);
			}
		}
		return isset($_config[$matches[1]][$matches[2]]) ? $_config[$matches[1]][$matches[2]] : '';
	}
	return $_config[$name] = $value;
}	

/**
 +----------------------------------------------------------
 * D函数用于实例化Model
 +----------------------------------------------------------
 * @param string name Model名称
 * @param string app Model所在项目
 +----------------------------------------------------------
 * @return Model
 +----------------------------------------------------------
 */
function D($name='',$app='M')
{
	static $_model = array();
	if(empty($name)) return new Model;
	if(isset($_model[$app.$name]))
		return $_model[$app.$name];
	$OriClassName = $name;
	$className =  $name.'Model';
	import($app.'.'.$className);

	if(class_exists($className)) {
		$model = new $className();
	}else {
		$model  = new Model($name);
	}
	$_model[$app.$OriClassName] =  $model;
	return $model;
}

/**
 +----------------------------------------------------------
 * M函数用于实例化一个没有模型文件的Model
 +----------------------------------------------------------
 * @param string name Model名称
 +----------------------------------------------------------
 * @return Model
 +----------------------------------------------------------
 */
function M($name='',$class='Model') {
	static $_model = array();
	if(!isset($_model[$name.'_'.$class]))
		$_model[$name.'_'.$class]   = new $class($name);
	return $_model[$name.'_'.$class];
}

/**
 +----------------------------------------------------------
 * A函数用于实例化Control
 +----------------------------------------------------------
 * @param string name Control名称
 * @param string app Module模块名
 +----------------------------------------------------------
 * @return Action
 +----------------------------------------------------------
 */
function A($name,$app='C',$module='')
{
	static $_action = array();
	$module=($module=='')?MODULE_NAME:$module;
	if(isset($_action[$app.$module.$name]))
		return $_action[$app.$module.$name];
	$OriClassName = $name;
	$className =  $module.$name;
	import($app.'.'.$module.'.'.$className);

	if(class_exists($className)) {
		$action = new $className();
		$_action[$app.$OriClassName] = $action;
		return $action;
	}else {
		return false;
	}
}

// 远程调用模块的操作方法
function R($module,$control,$action,$param=array(),$app='C') {
	$class = A($control,$app,$module);
	if($class)
		return call_user_func_array(array(&$class,$action),$param);
	else
		return false;
}

// 获取和设置语言定义(不区分大小写)
function L($name=null,$value=array()) {
	// 空参数返回所有定义
	if(empty($name)) return;
	// 判断语言获取(或设置)
	if($name[0] !== '{'){
		false === strpos($name,'.') && $name = MODULE_NAME.CONTROL_NAME.'.'.$name;
		$name = '{Lang.'.$name.'}';
	}
	return parseLang($name,$value);
}

function parseLang($name,$value){
	static $_key=array('{$1}','{$2}','{$3}','{$4}','{$5}','{$6}','{$7}','{$8}','{$9}','{$10}','{$11}','{$12}','{$13}','{$14}','{$15}');

	if(is_string($name)){
		$name = preg_replace_callback('/\{Lang\.([\w]+)\.?([\w]+)?\}/','parseLang_callback',$name);
		$count = count($value);
		if($count>0 && is_array($value)){
			$name = str_replace(array_slice($_key,0,$count),$value,$name);
		}
		return $name;
	}elseif(is_array($name)){
		foreach($name as $k=>$v){
			$name[$k] = parseLang($v);
		}
	}elseif(is_object($name)){
		foreach($name as $k=>$v){
			$name->$k = parseLang($v);
		}
	}
	return $name;
}

function parseLang_callback($arr){
	static $_lang;

	if(count($arr)===2){
		$name = '~Common';
		$label = $arr[1];
	}else{
		$name = $arr[1];
		$label = $arr[2];
	}
	if($name == '' || $label == '')throw_exception('wrong lang_key:' . $lang_key);

	if(!isset($_lang[$name])){
		if(EACS && !is_null(eaccelerator_get($name))){
			$_lang[$name] = eaccelerator_get($name);
		}else{
			$lang = file_exists(LOCALE_PATH."/{$name}.lang.php")?include(LOCALE_PATH."/{$name}.lang.php"):array();
			$_lang[$name] = $lang;
			EACS && eaccelerator_put($name,$lang,600);
		}
	}
	return isset($_lang[$name][$label]) ? $_lang[$name][$label] : '';  
}

// 处理标签
function tag($name,$params=array()) {
	$tags   =  S('_tags_.'.$name);
	if($tags) {
		foreach ($tags   as $key=>$call){
			if(is_callable($call))
				$result = call_user_func_array($call,$params);
		}
		return $result;
	}
	return false;
}

// 执行行为
function B($name) {
	$class = $name.'Behavior';
	require_cache(MODULE_PATH.'Behavior/'.$class.'.class.php');
	$behavior   =  new $class();
	$behavior->run();
}

// 渲染输出Widget
function W($name,$data=array(),$return=false) {
	$class = $name.'Widget';
	require_cache(MODULE_PATH.'Widget/'.$class.'.class.php');
	if(!class_exists($class))
		throw_exception(L('_CLASS_NOT_EXIST_').':'.$class);
	$widget  =  Deve::instance($class);
	$content = $widget->render($data);
	if($return)
		return $content;
	else
		echo $content;
}

// 获取配置值
function S($name=null,$value=null)
{   
	static $_config = array();
	// 无参数时获取所有
	if(empty($name)) return $_config;
	$name = strtolower($name);
	if (is_null($value)){
		if(isset($_config[$name])){
			return $_config[$name];
		}else{
			if(false !== strpos($name,'_') && $tag = explode('_',$name)){						
				if(EACS && !is_null(eaccelerator_get($name))){
					$_config = array_merge($_config,eaccelerator_get($tag[0]));
					return $_config[$name];
				}	
				$file = ucwords($tag[0]);        // Source文件名
				// c0框架配置文件   c1 APP配置文件
				$c0 = file_exists(DEVE_PATH."/Source/{$file}.php")?include(DEVE_PATH."/Source/{$file}.php"):array();
				$c1 = file_exists(CONFIG_PATH."/{$file}.php")?include(CONFIG_PATH."/{$file}.php"):null;								
				$config = is_array($c1) ? array_change_key_case(array_merge($c0,$c1)) : array_change_key_case($c0);
				// 整合所有的配置文件
				$_config = array_merge($_config,$config);
				EACS && eaccelerator_put($tag[0],$config,600);
				// 返回配置
				return isset($_config[$name])?$_config[$name]:null;
			}
			return null;
		}
	}
	$_config[$name] = $value;
	return;
}

// 快速文件数据读取和保存 针对简单类型数据 字符串、数组
function F($name,$value='',$path=DATA_PATH) {
	static $_cache = array();
	$filename   =   $path.$name.'.php';
	if('' !== $value) {
		if(is_null($value)) {
			// 删除缓存
			return unlink($filename);
		}else{
			// 缓存数据
			$dir   =  dirname($filename);
			// 目录不存在则创建
			if(!is_dir($dir))  mkdir($dir);
			return file_put_contents($filename,"<?php\nreturn ".var_export($value,true).";\n?>");
		}
		}
		if(isset($_cache[$name])) return $_cache[$name];
		// 获取缓存数据
		if(is_file($filename)) {
			$value   =  include $filename;
			$_cache[$name]   =   $value;
		}else{
			$value  =   false;
		}
		return $value;
		}

		// 根据PHP各种类型变量生成唯一标识号
		function to_guid_string($mix)
		{
			if(is_object($mix) && function_exists('spl_object_hash')) {
				return spl_object_hash($mix);
			}elseif(is_resource($mix)){
				$mix = get_resource_type($mix).strval($mix);
			}else{
				$mix = serialize($mix);
			}
			return md5($mix);
		}

		//[RUNTIME]
		// 编译文件
		function compile($filename,$runtime=false) {
			$content = file_get_contents($filename);
			if(true === $runtime)
				// 替换预编译指令
				$content = preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s','',$content);
			$content = substr(trim($content),5);
			if('?>' == substr($content,-2))
				$content = substr($content,0,-2);
			return $content;
		}

		// 去除代码中的空白和注释
		function strip_whitespace($content) {
			$stripStr = '';
			//分析php源码
			$tokens =   token_get_all ($content);
			$last_space = false;
			for ($i = 0, $j = count ($tokens); $i < $j; $i++)
			{
				if (is_string ($tokens[$i]))
				{
					$last_space = false;
					$stripStr .= $tokens[$i];
				}
				else
				{
					switch ($tokens[$i][0])
					{
						//过滤各种PHP注释
					case T_COMMENT:
					case T_DOC_COMMENT:
						break;
						//过滤空格
					case T_WHITESPACE:
						if (!$last_space)
						{
							$stripStr .= ' ';
							$last_space = true;
						}
						break;
					default:
						$last_space = false;
						$stripStr .= $tokens[$i][1];
					}
				}
			}
			return $stripStr;
		}
		// 根据数组生成常量定义
		function array_define($array) {
			$content = '';
			foreach($array as $key=>$val) {
				$key =  strtoupper($key);
				if(in_array($key,array('DEVE_PATH','APP_NAME','APP_PATH','RUNTIME_PATH','RUNTIME_ALLINONE','THINK_MODE')))
					$content .= 'if(!defined(\''.$key.'\')) ';
				if(is_int($val) || is_float($val)) {
					$content .= "define('".$key."',".$val.");";
				}elseif(is_bool($val)) {
					$val = ($val)?'true':'false';
					$content .= "define('".$key."',".$val.");";
				}elseif(is_string($val)) {
					$content .= "define('".$key."','".addslashes($val)."');";
				}
			}
			return $content;
		}
		//[/RUNTIME]

		// 循环创建目录
		function mk_dir($dir, $mode = 0755)
		{
			if (is_dir($dir) || @mkdir($dir,$mode)) return true;
			if (!mk_dir(dirname($dir),$mode)) return false;
			return @mkdir($dir,$mode);
		}

		// 自动转换字符集 支持数组转换
		function auto_charset($fContents,$from,$to){
			$from   =  strtoupper($from)=='UTF8'? 'utf-8':$from;
			$to       =  strtoupper($to)=='UTF8'? 'utf-8':$to;
			if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
				//如果编码相同或者非字符串标量则不转换
				return $fContents;
			}
			if(is_string($fContents) ) {
				if(function_exists('mb_convert_encoding')){
					return mb_convert_encoding ($fContents, $to, $from);
				}elseif(function_exists('iconv')){
					return iconv($from,$to,$fContents);
				}else{
					return $fContents;
				}
			}
			elseif(is_array($fContents)){
				foreach ( $fContents as $key => $val ) {
					$_key =     auto_charset($key,$from,$to);
					$fContents[$_key] = auto_charset($val,$from,$to);
					if($key != $_key )
						unset($fContents[$key]);
				}
				return $fContents;
			}
			else{
				return $fContents;
			}
		}

		// xml编码
		function xml_encode($data,$encoding='utf-8',$root="think") {
			$xml = '<?xml version="1.0" encoding="'.$encoding.'"?>';
			$xml.= '<'.$root.'>';
			$xml.= data_to_xml($data);
			$xml.= '</'.$root.'>';
			return $xml;
		}

		function data_to_xml($data) {
			if(is_object($data)) {
				$data = get_object_vars($data);
			}
			$xml = '';
			foreach($data as $key=>$val) {
				is_numeric($key) && $key="item id=\"$key\"";
				$xml.="<$key>";
				$xml.=(is_array($val)||is_object($val))?data_to_xml($val):$val;
				list($key,)=explode(' ',$key);
				$xml.="</$key>";
			}
			return $xml;
		}

		function cookie($name,$value='',$option=null)
		{
			static $config = null;
			// 默认设置
		    is_null($config) && $config = array(
				'prefix' => S('APP_COOKIE_PREFIX'), // cookie 名称前缀
				'expire' => S('APP_COOKIE_EXPIRE'), // cookie 保存时间
				'path'   => S('APP_COOKIE_PATH'),   // cookie 保存路径
				'domain' => S('APP_COOKIE_DOMAIN'), // cookie 有效域名
            );
		    // 参数设置(会覆盖黙认设置)
			if (!empty($option)) {
				if (is_numeric($option))
					$option = array('expire'=>$option);
				elseif( is_string($option) )
					parse_str($option,$option);
				array_merge($config,array_change_key_case($option));
			}
			// 清除指定前缀的所有cookie
			if (is_null($name)) {
				if (empty($_COOKIE)) return;
				// 要删除的cookie前缀，不指定则删除config设置的指定前缀
				$prefix = empty($value)? $config['prefix'] : $value;
				if (!empty($prefix))// 如果前缀为空字符串将不作处理直接返回
				{
					foreach($_COOKIE as $key=>$val) {
						if (0 === stripos($key,$prefix)){
							setcookie($_COOKIE[$key],'',time()-3600,$config['path'],$config['domain']);
							unset($_COOKIE[$key]);
						}
					}
				}
				return;
			}
			$name = $config['prefix'].$name;
			if (''===$value){
				if(isset($_COOKIE[$name])){
				    return unserialize(MAGIC_QUOTES_GPC===true?stripslashes($_COOKIE[$name]):$_COOKIE[$name]);
				}else{
					return null;
				}	
			}else {
				if (is_null($value)) {
					setcookie($name,'',time()-3600,$config['path'],$config['domain']);
					unset($_COOKIE[$name]);// 删除指定cookie
				}else {
					// 设置cookie
					$expire = !empty($config['expire'])? time()+ intval($config['expire']):0;
					setcookie($name,serialize($value),$expire,$config['path'],$config['domain']);
					$_COOKIE[$name] = serialize($value);
				}
			}
		}
?>
