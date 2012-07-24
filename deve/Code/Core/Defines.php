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

if (!defined('DEVE_PATH')) exit();
//  版本信息
define('DEVE_VERSION', '0.0.1');
//  系统信息
if(version_compare(PHP_VERSION,'6.0.0','<') ) {
    @set_magic_quotes_runtime (0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
}
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );     // apcahe是apache2handler nginx可能是cgi
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

if(!IS_CLI) {
    // 当前文件名
    if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
            define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER["SCRIPT_NAME"],'/'));
        }
    }
    // 网站URL根目录
    if(!defined('__ROOT__')) {          
        if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
			$_root = dirname(_PHP_FILE_);
        }else {
            $_root = dirname(dirname(_PHP_FILE_));
        }
        define('__ROOT__',   (($_root=='/' || $_root=='\\')?'':$_root));
    }

}

// 记录内存初始使用
if(MEMORY_LIMIT_ON) {
     $GLOBALS['_startUseMems'] = memory_get_usage();
}


// 目录设置
define('CONFIG_DIR' , 'Config');
define('CONSOLE_DIR', 'Console');
define('CONTROLLER_DIR' , 'Controller');
define('DATA_DIR'   , 'Data');
define('LIB_DIR'    , 'Lib');
define('LOCALE_DIR' , 'Locale');
define('MODEL_DIR'  , 'Model');
define('PLUGIN_DIR' , 'Plugin');
define('TEST_DIR'   , 'Test');
define('TMP_DIR'    , 'tmp');
define('LOGS_DIR'   , 'logs');
define('CACHE_DIR'  , 'cache');
define('VENDOR_DIR' , 'Vendor');
define('VIEW_DIR'   , 'View');
define('WEBROOT_DIR', 'webroot');

// 路径设置
define('CONFIG_PATH' , APP_PATH.'/'.CONFIG_DIR);
define('CONSOLE_PATH', APP_PATH.'/'.CONSOLE_DIR);
define('CONTROLLER_PATH' , APP_PATH.'/'.CONTROLLER_DIR);
define('DATA_PATH'   , APP_PATH.'/'.DATA_DIR);	
define('LIB_PATH'    , APP_PATH.'/'.LIB_DIR);
define('LOCALE_PATH' , APP_PATH.'/'.LOCALE_DIR);
define('MODEL_PATH'  , APP_PATH.'/'.MODEL_DIR);
define('PLUGIN_PATH' , APP_PATH.'/'.PLUGIN_DIR);
define('TEST_PATH'   , APP_PATH.'/'.TEST_DIR);
define('TMP_PATH'    , APP_PATH.'/'.TMP_DIR);
define('LOGS_PATH'   , TMP_PATH.'/'.LOGS_DIR);
define('CACHE_PATH'  , TMP_PATH.'/'.CACHE_DIR);
define('VENDOR_PATH' , APP_PATH.'/'.VENDOR_DIR);
define('VIEW_PATH'   , APP_PATH.'/'.VIEW_DIR);
define('WEBROOT_PATH', APP_PATH.'/'.WEBROOT_DIR);

// 为了方便导入第三方类库 设置Vendor目录到include_path
set_include_path(get_include_path() . PATH_SEPARATOR . VENDOR_PATH);
?>