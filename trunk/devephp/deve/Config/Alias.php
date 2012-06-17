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

// 导入别名定义
alias_import(
   array(
	'Model'             => DEVE_PATH.'/Core/Model.class.php',
	'Dispatcher'        => DEVE_PATH.'/Router/Dispatcher.class.php',
	'HtmlCache'         => DEVE_PATH.'/Lib/Util/HtmlCache.class.php',
	'Db'                => DEVE_PATH.'/Lib/Db/Db.class.php',
	'ThinkTemplate'     => DEVE_PATH.'/Lib/Template/ThinkTemplate.class.php',
//	'Template'          => DEVE_PATH.'/Util/Template.class.php',
	'TagLib'            => DEVE_PATH.'/Lib/Template/TagLib.class.php',
	'Cache'             => DEVE_PATH.'/Lib/Cache/Cache.class.php',
	'Debug'             => DEVE_PATH.'/Debug/Debug.class.php',
      //'Cookie'            => DEVE_PATH.'/Lib/Cookie.class.php',
	'Session'           => DEVE_PATH.'/Lib/Session/Session.class.php',
	'TagLibCx'          => DEVE_PATH.'/Lib/Template/TagLib/TagLibCx.class.php',
	/*
	'ViewModel'         => DEVE_PATH.'/Core/ViewModel.class.php',
	'AdvModel'          => DEVE_PATH.'/Core/AdvModel.class.php',
	'RelationModel'     => DEVE_PATH.'/Core/RelationModel.class.php',
	 */
	'AmfTranslate'      => DEVE_PATH.'/Lib/Request/AmfTranslate.class.php',
	'RpcDataParse'      => DEVE_PATH.'/Lib/Request/RpcDataParse.class.php',
        )
);
?>
