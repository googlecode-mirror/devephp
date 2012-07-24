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


/**
 +------------------------------------------------------------------------------
 * DevePHP内置的Router类
 * 完成URL解析、路由和调度
 +------------------------------------------------------------------------------
 * @category   DevePHP
 * @package  DevePHP
 * @subpackage  Util
 * @author    yearnfar <yearnfar@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Router extends Deve
{//类定义开始

    /**
     +----------------------------------------------------------
     * URL映射到控制器
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	static public function run()
	{
		 
	}

    /**
     +----------------------------------------------------------
     * 分析请求数据的参数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	public static function request()
	{
		static $RQ = null;
		if($RQ !== null)return $RQ;
		if(isset($_SERVER['CONTENT_TYPE'])&& $_SERVER['CONTENT_TYPE']=='application/x-amf')
		{
			$RQ = Router::parse('AmfTranslate');            // PHP的AMF代理
		}elseif(isset($_SERVER['HTTP_USER_AGENT'])&& substr($_SERVER['HTTP_USER_AGENT'],0,6)=='PHPRPC')
		{
			$RQ = Router::parse('RpcDataParse');            // JAVA的PHPRPC代理
		}else{
			defined('T_HTML') or define('T_HTML',true);                          // HTML
		}

		// 对存在数据解析的进行数据封装数据
		if($RQ !== null){
			if(!isset($_GET[S('APP_VAR_MODULE')])) 
				$_POST[S('APP_VAR_MODULE')] = $RQ->ModuleName;
			if(!isset($_GET[S('APP_VAR_CONTROL')]))
				$_POST[S('APP_VAR_CONTROL')] = $RQ->ControlName;
			if(!isset($_GET[S('APP_VAR_ACTION')]))
				$_POST[S('APP_VAR_ACTION')] = $RQ->ActionName;
			$Data = $RQ->get_input();
			$_POST= array_merge($_POST,$Data[0]);
			// 返回数据解析器
			return $RQ;
		}
		return null;
	}

    /**
     +----------------------------------------------------------
     * 获取数据请求类库
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	private static function parse($DPN){
		static $DataParse=array();
		if(!$DPN)return null;
		if(isset($DataParse[$DPN]))return $DataParse[$DPN];

		if(!class_exists($DPN)){
			if(strtolower(substr($DPN,0,3))=='_dp'){
				$file = LIB_PATH.'/DataParse/'.$DPN.'.class.php';
			}

			if(!is_file($file)){
				throw_exception('DPN file: '.$file.' no exists! call from '.$this->IP);
				return null;
			}
			include($file);
			if(!class_exists($DPN)){
				throw_exception('DPN class : '.$DPN.' no exists!');
				return null;
			};
		}
		$DataParse[$DPN] = new $DPN();
		return $DataParse[$DPN];	
	}

    /**
     +----------------------------------------------------------
     * 路由检测
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	static public function check() {
		// 搜索路由映射 把路由名称解析为对应的模块和操作
		$routes = S('_routes_');
		if(!empty($routes)) {

		}
	}
}//类定义结束
?>