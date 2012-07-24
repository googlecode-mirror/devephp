<?php
//REV_AUTH 0未定义 1无需认证 2需要认证
return array(
// 模块整体配置
'~Global'=> array (
    'Name' => '{Lang.User.MName}',
    'REV_AUTH' => 2,
    'Params' => array(),
),
 // 功能配置 
'Login'=>array(
    '~Global' => 
    array (
      'Name' => '{Lang.UserLogin.MName}',
      'REV_AUTH' => 0,
      'Params' => array(),
    ),
    // 方法配置
    'login' => 
    array (
      'Name' => '{Lang.UserLogin.loginMName}',
      'REV_AUTH' => 1,
      'Params' => array(),
    ),
),
'Info'=>array(
    '~Global' => 
    array (
      'Name' => '{Lang.UserInfo.MName}',
      'REV_AUTH' => 0,
      'Params' => array(),
    ),
    // 方法配置
    'show' => 
    array (
      'Name' => '{Lang.UserLogin.showMName}',
      'REV_AUTH' => 2,
      'Params' => array(),
    ),
),

);