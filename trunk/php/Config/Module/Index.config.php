<?php
return array(
// 模块整体配置
'~Global'=> array (
    'Name' => '{Lang.Index.MName}',
    'REV_AUTH' => 2,
    'Params' => array(),
),
 // 功能配置 
'Show'=>array(
    '~Global' => 
    array (
      'Name' => '{Lang.IndexShow.MName}',
      'REV_AUTH' => 0,
      'Params' => array(),
    ),
    // 方法配置
    'main' => 
    array (
      'Name' => '{Lang.IndexShow.mainMName}',
      'REV_AUTH' => 2,
      'Params' => array(),
    ),
),

);
