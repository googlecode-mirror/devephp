<?php
//REV_AUTH 0未定义 1无需认证 2需要认证
return array(
// 模块整体配置
'~Global'=> array (
    'Name' => '{Lang.Admin.MName}',
    'REV_AUTH' => 2,
    'Params' => array(),
),
 // 功能配置 
'Flex'=>array(
    '~Global' => 
    array (
      'Name' => '{Lang.AdminFlex.MName}',
      'REV_AUTH' => 0,
      'Params' => array(),
    ),
    // 方法配置
    'showMCA' => 
    array (
      'Name' => '{Lang.AdminFlex.showMCAMName}',
      'REV_AUTH' => 1,
      'Params' => array(),
    ),
),

);