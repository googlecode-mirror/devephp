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
    DEVE_PATH.'/Debug/DeveException.class.php',// 异常处理类
    DEVE_PATH.'/Debug/Log.class.php',      // 日志处理类
    DEVE_PATH.'/Core/App.class.php',       // 应用程序类
    DEVE_PATH.'/Core/Control.class.php',    // 控制器类
  //DEVE_PATH.'/Core/Model.class.php',     // 模型类
    DEVE_PATH.'/Router/Router.class.php',     // 路由类
    DEVE_PATH.'/View/View.class.php',      // 视图类
    DEVE_PATH.'/Config/Alias.php',           // 加载别名
);
?>
