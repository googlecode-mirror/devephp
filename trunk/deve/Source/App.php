<?php
if (!defined('DEVE_PATH')) exit();
/* 项目设定 */
return array(
	'APP_DEBUG'		=> true,   // 是否开启调试模式
	'APP_COMMON_FILES'      => array(),	// 项目公共文件
	'APP_CONFIG_LIST'       => array('taglibs','routes','tags','htmls','modules','actions'),// 项目额外需要加载的配置列表，默认包括：taglibs(标签库定义),routes(路由定义),tags(标签定义),(htmls)静态缓存定义, modules(扩展模块),actions(扩展操作)

	'APP_PLUGIN_ON'         => false,   // 是否开启插件机制
	'APP_TIMEZONE'          =>'Asia/Shanghai',
	
	'APP_AUTOLOAD_ON'           => false,   // 是否开启SPL_AUTOLOAD_REGISTER
	'APP_SESSION_ON'            => true,    // 开启session
	'APP_ROUTER_ON'             => 'false',
	'APP_VAR_MODULE'            => 'm',		// 默认模块获取变量
	'APP_DEFAULT_MODULE'        => 'Index',
	'APP_URL_CASE_INSENSITIVE'  => 'false',
	'APP_VAR_CONTROL'           => 'c',		// 默认控制器获取变量
	'APP_DEFAULT_CONTROL'       => 'Show',
	'APP_VAR_ACTION'            => 'a',		// 默认操作获取变量
	'APP_DEFAULT_ACTION'        => 'main',
	'APP_MCA_ON'                => false,	// 是否开启MCA认证

	'APP_LANG_DEFAULT'          => 'zh-cn',
	'APP_LANG_ON'               => true,
	'APP_LANG_AUTO'				=> true,
	'APP_VAR_LANG'              => 'l',	    // 默认语言切换变量
	
	'APP_COOKIE_EXPIRE'         => 3600,    // Coodie有效期
	'APP_COOKIE_DOMAIN'         => '',      // Cookie有效域名
	'APP_COOKIE_PATH'           => '/',     // Cookie路径
	'APP_COOKIE_PREFIX'         => '',      // Cookie前缀 避免冲突

	'APP_THEME_AUTO'			=>'true',
	'APP_VAR_TEMPLATE'          => 't',		// 默认模板切换变量
	'APP_DEFAULT_THEME'         =>'default',
	'APP_DOMAIN_DEPLOY'         => false,   // 是否使用独立域名部署项目
	
	'APP_HTML_CACHE_ON'         =>'true',

	'APP_FILE_CASE'         => false,   // 是否检查文件的大小写 对Windows平台有效

	'APP_AUTOLOAD_PATH'     => 'Module.Util.',// __autoLoad 机制额外检测路径设置,注意搜索顺序
	
	'APP_VAR_GROUP'             => 'g',     // 默认分组获取变量
	
	'APP_VAR_ROUTER'            => 'r',     // 默认路由获取变量
	'APP_VAR_PAGE'              => 'p',		// 默认分页跳转变量
	
	'APP_VAR_AJAX_SUBMIT'       => 'ajax',  // 默认的AJAX提交变量
	'APP_VAR_PATHINFO'          => 's',  	// PATHINFO 兼容模式获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_MODEL 和 URL_PATHINFO_DEPR

    'APP_SHOW_RUN_TIME'         => true,
	'APP_PAGE_TRACE '           => true,
	'APP_HTML_CACHE_ON'         => false,	// 是否开启htmlcache
	'APP_TRACE_FILE'            => DEVE_PATH.'/Code/Com/View/PageTrace.tpl.php',     // 页面Trace的模板文件',
	
	'APP_LOG_RECORD'            => true,
	'APP_LOG_FILE_SIZE'			=> '2097152',
	'APP_LOG_LEVEL'             => array('EMERG','ALERT','CRIT','ERR','NOTIC','WARN'),// 允许记录的日志级别'

	'APP_TMPL_CHARSET'          => 'utf-8',
	'APP_TMPL_ENGINE'           => 'Think',
	'APP_TMPL_CACHE_SUFFIX'     => '.php',
	'APP_TOKEN_ON'              => false,
	'APP_TMPL_PARSE_STRING'     => '',

	
)
?>
