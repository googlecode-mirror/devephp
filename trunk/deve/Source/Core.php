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

// 系统默认的核心列表文件
return array(
    DEVE_PATH.'/Code/Core/DeveException.class.php',// 异常处理类 
    DEVE_PATH.'/Code/Core/App.class.php',       // 应用程序类
    DEVE_PATH.'/Code/Core/Control.class.php',    // 控制器类
    DEVE_PATH.'/Code/Core/Model.class.php',     // 模型类
    
    DEVE_PATH.'/Code/Com/Log.class.php',      // 日志处理类
    DEVE_PATH.'/Code/Com/Router.class.php',     // 路由类
    DEVE_PATH.'/Code/Com/View.class.php',      // 视图类
    DEVE_PATH.'/Code/Com/Alias.php'           // 载入别名文件
);
?>
