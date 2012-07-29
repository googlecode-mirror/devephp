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
 * DevePHP 应用程序类 执行应用过程管理
 +------------------------------------------------------------------------------
 * @category   DevePHP
 * @package  DevePHP
 * @subpackage  Core
 * @author    yearnfar <yearnfar@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class App
{//类定义开始

    /**
     +----------------------------------------------------------
     * 应用程序初始化
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	static public function init()
	{
		// 设定错误和异常处理
		set_error_handler(array('App','appError'));
		set_exception_handler(array('App','appException'));

		// 检查项目是否编译过
		if(is_file(APP_PATH.'/~app.php') && 
			(!is_file(CONFIG_PATH.'/Config.php') || filemtime(APP_PATH.'/~app.php')>filemtime(CONFIG_PATH.'/Config.php')))
		{
			// 直接读取编译后的项目文件
			include APP_PATH.'/~app.php';
		}else{
			// 预编译项目
			App::build();
		}

		// 项目开始标签
		if(S('APP_PLUGIN_ON'))   tag('app_begin');

		// 设置系统时区 PHP5支持
		if(function_exists('date_default_timezone_set'))
			date_default_timezone_set(S('APP_DEFAULT_TIMEZONE'));

		// 允许注册AUTOLOAD方法
		if(S('APP_AUTOLOAD_ON') && function_exists('spl_autoload_register'))
			spl_autoload_register(array('Deve', 'autoload'));

		if(S('APP_SESSION_ON'))session_start(); // Session初始化

		// URL路由器
		if(S('APP_ROUTER_ON'))Router::request();

		// 当前文件
		if(!defined('PHP_FILE'))
			define('PHP_FILE',_PHP_FILE_);

		defined('MODULE_NAME') or define('MODULE_NAME',   App::getModule());        // Module 名称
		defined('CONTROL_NAME') or define('CONTROL_NAME',  App::getControl());      // Control 名称
		defined('ACTION_NAME') or define('ACTION_NAME',   App::getAction());        // Action 名称

		// MCA认证  MODULE_NAME,CONTROL_NAME,ACTION_NAME
		if(S('APP_MCA_ON'))App::MCAAuth(MODULE_NAME,CONTROL_NAME,ACTION_NAME);
		defined('REV_AUTH') or define('REV_AUTH',false);  // 默认需要无认证

		// 系统检查
		App::checkLanguage();         // 语言检查
		App::checkTemplate();         // 模板检查
		// 开启静态缓存
		if(S('APP_HTMLCACHE_ON')){        
			HtmlCache::readHTMLCache();
		}
		// 项目初始化标签
		if(S('APP_PLUGIN_ON'))   tag('app_init');
		return ;
	}

    /**
     +----------------------------------------------------------
     * 读取配置信息 编译项目
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	static private function build()
	{
		$common   = '';
		// 是否为开发模式
		$runtime = IS_DEBUG == false;
		// 加载项目公共文件
		if(is_file(LIB_PATH.'/Common.php')) {
			include LIB_PATH.'/Common.php';
			if($runtime) // 编译文件
				$common .= compile(LIB_PATH.'/Common.php',false);
		}
		// 加载项目编译文件列表
		if(is_array(S('APP_COMMON_FILES'))) {
			foreach (S('APP_COMMON_FILES') as $file){
				// 加载并编译文件
				require $file;
				if($runtime) $common .= compile($file,false);
			}
		}
		// 读取扩展配置文件
		$list = S('APP_CONFIG_LIST');
		foreach ($list as $val){
			if(is_file(CONFIG_PATH.'/'.$val.'.php'))
				S('_'.$val.'_',array_change_key_case(include CONFIG_PATH.'/'.$val.'.php'));
		}
		// 开发模式下不编译项目文件
		if($runtime){
			$content  = "<?php ".$common."\n?>";
			file_put_contents(APP_PATH.'/~app.php',strip_whitespace($content));
		}
		return ;
	}

	 /**
     +----------------------------------------------------------
     * MCA认证
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
	  */
	static private function MCAAuth($M,$C,$A){

		$_CFG=array();
		// 是否存在MCAAuth配置文件
		if(file_exists(CONFIG_PATH.'/Module/'.$M.'.config.php'))
			$_CFG = include(CONFIG_PATH.'/Module/'.$M.'.config.php');
		// 控制器全局配置     
		isset($_CFG[$C]['~Global']['IsClose']) or $_CFG[$C]['~Global']['IsClose']  = false;
		isset($_CFG[$C]['~Global']['StartTime']) or $_CFG[$C]['~Global']['StartTime']= 0;
		isset($_CFG[$C]['~Global']['EndTime']) or $_CFG[$C]['~Global']['EndTime']  = 0;
		// 功能配置
		isset($_CFG[$C][$A]) or throw_exception(L('_ACTION_NO_REGISTER_'));
		isset($_CFG[$C][$A]['IsClose']) or $_CFG[$C][$A]['IsClose'] = false;
		isset($_CFG[$C][$A]['StartTime']) or $_CFG[$C][$A]['StartTime']= 0;
		isset($_CFG[$C][$A]['EndTime']) or $_CFG[$C][$A]['EndTime'] = 0;
		// 模块全局配置
		isset($_CFG['~Global']['IsClose']) or $_CFG['~Global']['IsClose'] = false;
		isset($_CFG['~Global']['StartTime']) or $_CFG['~Global']['StartTime']= 0;
		isset($_CFG['~Global']['EndTime']) or $_CFG['~Global']['EndTime']= 0;
		// 功能、控制器、模块开启状态
		if($_CFG[$C][$A]['IsClose']==true || 
			($_CFG[$C][$A]['StartTime']>0 && TIMESTAMP<$_CFG[$C][$A]['StartTime']) ||
			($_CFG[$C][$A]['EndTime']>0 && TIMESTAMP>$_CFG[$C][$A]['EndTime']))     
			throw_exception(L('_ACTION_SHUTDOWM_'));
		if($_CFG[$C]['IsClose']==true || 
			($_CFG[$C]['StartTime']>0 && TIMESTAMP<$_CFG[$C]['StartTime']) ||
			($_CFG[$C]['EndTime']>0 && TIMESTAMP>$_CFG[$C]['EndTime']))
			throw_exception(L('_CONTROL_SHUTDOWM_'));	    
		if($_CFG['IsClose']==true || 
			($_CFG['StartTime']>0 && TIMESTAMP<$_CFG['StartTime']) ||
			($_CFG['EndTime']>0 && TIMESTAMP>$_CFG['EndTime']))
			throw_exception(L('_MODULE_SHUTDOWM_'));
		
		// REV = reveal 显示认证
		// REV_AUTH  0未定义 1无需认证 2需要认证
		if(!isset($_CFG[$C][$A]['REV_AUTH']) || $_CFG[$C][$A]['REV_AUTH']==0){
			if(!isset($_CFG[$C]['~Global']['REV_AUTH']) || $_CFG[$C]['~Global']['REV_AUTH']==0){
				$REV_AUTH = $_CFG['~Global']['REV_AUTH'];
			}else{
				$REV_AUTH = $_CFG[$C]['~Global']['REV_AUTH'];
			}
		}else{
			$REV_AUTH = $_CFG[$C][$A]['REV_AUTH'];
		}
		define('REV_AUTH' , ($REV_AUTH != 1));  // true 需要认证  false无需认证
	}

    /**
     +----------------------------------------------------------
     * 获得实际的模块名称
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	static private function getModule()
	{
		$var  =  S('APP_VAR_MODULE');
		$module = !empty($_POST[$var]) ?
			$_POST[$var] :
			(!empty($_GET[$var])? $_GET[$var]:S('APP_DEFAULT_MODULE'));
		if(S('APP_URL_CASE_INSENSITIVE')) {
			// URL地址不区分大小写
			define('P_MODULE_NAME',strtolower($module));
			// 智能识别方式 index.php/user_type/index/ 识别到 UserTypeAction 模块
			$module = ucfirst(parse_name(P_MODULE_NAME,1));
		}
		unset($_POST[$var],$_GET[$var]);
		return $module;
	}

    /**
     +----------------------------------------------------------
     * 获得控制器的名称
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	static private function getControl()
	{
		$var  =  S('APP_VAR_CONTROL');
		$control   = !empty($_POST[$var]) ?
			$_POST[$var] :
			(!empty($_GET[$var])?$_GET[$var]:S('APP_DEFAULT_CONTROL'));
		unset($_POST[$var],$_GET[$var]);
		return $control;
	}

    /**
     +----------------------------------------------------------
     * 获得实际的操作名称
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */  
	static private function getAction()
	{
		$var  =  S('APP_VAR_ACTION');
		$action   = !empty($_POST[$var]) ?
			$_POST[$var] :
			(!empty($_GET[$var])?$_GET[$var]:S('APP_DEFAULT_ACTION'));
		unset($_POST[$var],$_GET[$var]);
		return $action;
	}

    /**
     +----------------------------------------------------------
     * 语言检查
     * 检查浏览器支持语言，并自动加载语言包
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	static private function checkLanguage()
	{
		$langSet = S('APP_LANG_DEFAULT');
		// 不开启语言包功能，仅仅加载框架语言文件直接返回
		if (!S('APP_LANG_ON')){
			L(include DEVE_PATH.'/Config/'.$langSet.'.php');
			return;
		}
		// 启用了语言包功能
		// 根据是否启用自动侦测设置获取语言选择
		if (S('LANG_AUTO_DETECT')){
			if(isset($_GET[S('LANG_VAR')])){// 检测浏览器支持语言
				$langSet = $_GET[S('LANG_VAR')];// url中设置了语言变量
				cookie('deve_lang',$langSet,3600);
			}elseif(cookie('deve_lang'))// 获取上次用户的选择
				$langSet = cookie('deve_lang');
			elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){// 自动侦测浏览器语言
				preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
				$langSet = $matches[1];
				cookie('deve_lang',$langSet,3600);
			}
		}
		// 定义当前语言
		define('LANG_SET',strtolower($langSet));
		// 加载框架语言包
		if(is_file(DEVE_PATH.'/Config/'.$langSet.'.php'))
			L(include DEVE_PATH.'/Config/'.$langSet.'.php');
		// 读取项目公共语言包
		if (is_file(LOCALE_PATH.'/'.$langSet.'/~Common.lang.php'))
			L(include LOCALE_PATH.'/'.$langSet.'/~Common.php');

		// 读取当前模块语言包
		if (is_file(LOCALE_PATH.'/'.$langSet.'/'.MODULE_NAME.CONTROL_NAME.'.lang.php'))
			L(include LOCALE_PATH.'/'.$langSet.'/'.MODULE_NAME.CONTROL_NAME.'.lang.php');
	}

    /**
     +----------------------------------------------------------
     * 模板检查，如果不存在使用默认
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	static private function checkTemplate()
	{
		if(S('TMPL_DETECT_THEME')) {// 自动侦测模板主题
			$t = S('APP_VAR_TEMPLATE');
			if (isset($_GET[$t])){
				$templateSet = $_GET[$t];
				cookie('deve_template',$templateSet,3600);
			}else{
				if(cookie('deve_template')){
					$templateSet = cookie('deve_template');
				}else{
					$templateSet =    S('TMPL_DEFAULT_THEME');
					cookie('deve_template',$templateSet,3600);
				}
			}
			if(!is_dir(VIEW_PATH.'/'.$templateSet))
				//模版不存在的话，使用默认模版
				$templateSet =    S('TMPL_DEFAULT_THEME');
		}else{
			$templateSet =    S('TMPL_DEFAULT_THEME');
		}
		//	S('TMPL_TEMPLATE_SUFFIX','.html');

		//模版名称
		define('TEMPLATE_NAME',$templateSet);

		// 当前模版路径
		define('TEMPLATE_PATH',VIEW_PATH.'/'.TEMPLATE_NAME);
		$tmplDir = TMP_DIR.'/'.TEMPLATE_NAME.'/';

		//当前项目地址
		define('__APP__',PHP_FILE);
		//当前页面地址
		define('__SELF__',$_SERVER['PHP_SELF']);
		// 应用URL根目录
		if(S('APP_DOMAIN_DEPLOY')) {
			// 独立域名部署需要指定模板从根目录开始
			$appRoot   =  '/';
		}else{
			$appRoot   =  __ROOT__.'/'.APP_NAME.'/';
		}
		$depr = S('URL_PATHINFO_MODEL')==2?S('URL_PATHINFO_DEPR'):'/';
		$module = defined('P_MODULE_NAME')?P_MODULE_NAME:MODULE_NAME;
		{
			define('__URL__',PHP_FILE.'?'.S('APP_VAR_MODULE').'='.$module);
			S('TMPL_FILE_NAME',TEMPLATE_PATH.'/'.MODULE_NAME.CONTROL_NAME.S('TMPL_TEMPLATE_SUFFIX'));
			S('CACHE_PATH',CACHE_PATH.'/');
		}
		//当前操作地址
		define('__ACTION__',__URL__.S('URL_PATHINFO_DEPR').ACTION_NAME);
		define('__CURRENT__',__URL__.S('URL_PATHINFO_DEPR').S('APP_VAR_CONTROL').'='.CONTROL_NAME);
		//		define('__CURRENT__', __ROOT__.'/'.APP_NAME.'/'.$tmplDir.MODULE_NAME);
		//项目模板目录
		define('APP_TMPL_PATH', $appRoot.$tmplDir);

		return ;
	}

    /**
     +----------------------------------------------------------
     * 执行应用程序
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws DeveExecption
     +----------------------------------------------------------
     */
	static public function exec()
	{
		// 是否开启标签扩展
		$tagOn   =  S('APP_PLUGIN_ON');
		// 项目运行标签
		if($tagOn)  tag('app_run');

		//创建Control控制器实例
		$module  =  A(CONTROL_NAME);
		if(!$module) {
			// 是否存在扩展模块
			$_module = S('_modules_.'.MODULE_NAME);
			if($_module) {
				// 'module'=>array('classImportPath'[,'className'])
				import($_module[0]);
				$class = isset($_module[1])?$_module[1]:MODULE_NAME.'Action';
				$module = new $class;
			}else{
				// 是否定义Empty模块
				$module = A("Empty");
			}
			if(!$module)
				// 模块不存在 抛出异常
				throw_exception(L('_MODULE_NOT_EXIST_').MODULE_NAME);
		}

		//获取当前操作名
		$action = ACTION_NAME;
		if(strpos($action,':')) {
			// 执行操作链 最多只能有一个输出
			$actionList	=	explode(':',$action);
			foreach ($actionList as $action){
				$module->$action();
			}
		}else{
			if (method_exists($module,'_before_'.$action)) {
				// 执行前置操作
				call_user_func(array(&$module,'_before_'.$action));
			}
			//执行当前操作
			call_user_func(array(&$module,$action));
			if (method_exists($module,'_after_'.$action)) {
				//  执行后缀操作
				call_user_func(array(&$module,'_after_'.$action));
			}
		}
		// 项目结束标签
		if($tagOn)  tag('app_end');
		return ;
	}

    /**
     +----------------------------------------------------------
     * 运行应用实例 入口文件使用的快捷方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	static public function run() {
		App::init();
		// 记录应用初始化时间
		if(S('SHOW_RUN_TIME'))  $GLOBALS['_initTime'] = microtime(TRUE);
		App::exec();
		// 保存日志记录
		if(S('LOG_RECORD')) Log::save();
		return ;
	}

    /**
     +----------------------------------------------------------
     * 自定义异常处理
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $e 异常对象
     +----------------------------------------------------------
     */
	static public function appException($e)
	{
		halt($e->__toString());
	}

    /**
     +----------------------------------------------------------
     * 自定义错误处理
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	static public function appError($errno, $errstr, $errfile, $errline)
	{
		switch ($errno) {
		case E_ERROR:
		case E_USER_ERROR:
			$errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
			if(S('LOG_RECORD')) Log::write($errorStr,Log::ERR);
			halt($errorStr);
			break;
		case E_STRICT:
		case E_USER_WARNING:
		case E_USER_NOTICE:
		default:
			$errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
			Log::record($errorStr,Log::NOTICE);
			break;
		}
	}

};//类定义结束
?>
