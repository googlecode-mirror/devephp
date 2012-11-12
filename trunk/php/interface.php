<?php
// +----------------------------------------------------------------------
// | DevePHP [ EASIER EFFICIENT SECURE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://DevePHP.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yearnfar <yearnfar@gmail.com>
// +----------------------------------------------------------------------

//定义项目名称和路径
//error_reporting(0);

define('APP_NAME', 'DEVE-TEST');
define('APP_PATH', realpath(dirname(__FILE__)));
// 是否为开发模式
define('IS_DEBUG',1);

// 定义框架路径、加载框架入口文件
define('DEVE_PATH', "../deve");
define('TIMESTAMP',time());
define('EACS',false);

require(DEVE_PATH."/DevePHP.php");
// 实例化一个网站应用实例
App::run();
?>
