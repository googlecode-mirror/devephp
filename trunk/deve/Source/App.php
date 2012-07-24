<?php
if (!defined('DEVE_PATH')) exit();
/* 项目设定 */
return array(
	'APP_DEBUG'		=> true,   // 是否开启调试模式
	'APP_TIMEZONE' =>'PRC',
	'APP_DOMAIN_DEPLOY'     => false,   // 是否使用独立域名部署项目
	'APP_PLUGIN_ON'         => false,   // 是否开启插件机制
	'APP_FILE_CASE'         => false,   // 是否检查文件的大小写 对Windows平台有效
	'APP_GROUP_DEPR'        => '.',     // 模块分组之间的分割符
	'APP_GROUP_LIST'        => '',      // 项目分组设定,多个组之间用逗号分隔,例如'Home,Admin'
	'APP_AUTOLOAD_REG'      => false,   // 是否开启SPL_AUTOLOAD_REGISTER
	'APP_AUTOLOAD_PATH'     => 'Module.Util.',// __autoLoad 机制额外检测路径设置,注意搜索顺序
	'APP_CONFIG_LIST'       => array('taglibs','routes','tags','htmls','modules','actions'),// 项目额外需要加载的配置列表，默认包括：taglibs(标签库定义),routes(路由定义),tags(标签定义),(htmls)静态缓存定义, modules(扩展模块),actions(扩展操作)
    'APP_VAR_GROUP'             => 'g',     // 默认分组获取变量
	'APP_VAR_MODULE'            => 'm',		// 默认模块获取变量
	'APP_VAR_CONTROL'           => 'c',		// 默认控制器获取变量
	'APP_VAR_ACTION'            => 'a',		// 默认操作获取变量
	'APP_VAR_ROUTER'            => 'r',     // 默认路由获取变量
	'APP_VAR_PAGE'              => 'p',		// 默认分页跳转变量
	'APP_VAR_TEMPLATE'          => 't',		// 默认模板切换变量
	'APP_VAR_LANGUAGE'          => 'l',		// 默认语言切换变量
	'APP_VAR_AJAX_SUBMIT'       => 'ajax',  // 默认的AJAX提交变量
	'APP_VAR_PATHINFO'          => 's',	// PATHINFO 兼容模式获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_MODEL 和 URL_PATHINFO_DEPR
    'APP_MCA_ON'                => false,
	'APP_DEFAULT_MODULE'        =>'Index',
	'APP_DEFAULT_CONTROL'       =>'Show',
	'APP_DEFAULT_ACTION'        =>'main',
	'APP_RUN_TIME'              =>true,
	'APP_PAGE_TRACE '           =>true,
)
?>
