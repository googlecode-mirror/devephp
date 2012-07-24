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
 * DevePHP Control控制器基类 抽象类
 +------------------------------------------------------------------------------
 * @category   DevePHP
 * @package  DevePHP
 * @subpackage  Core
 * @author   yearnfar <yearnfar@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
abstract class Control extends Deve
{//类定义开始
    
    protected $dp = null;
    // 视图实例对象
    protected $view = null;
    // 数据输出
    protected $response = null;
    // 缓存技术
    protected $cache = null;
    // 用户信息
    protected $userInfo = array();
    
    protected $targetURI = '';
    // ActiveVO
    private $activeVO = array();
    // 输出数据
    private $outData = array();

   /**
     +----------------------------------------------------------
     * 架构函数 取得模板对象实例
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public final function __construct()
    {
        // 用户认证
        if(MODULE_NAME !== 'Admin'){
            $this->userInfo = R('User','Auth','check');   // 用户认证       
        }else{
        	$this->userInfo = R('Admin','Auth','check');  // 管理员认证
        }
        // 验证是否有操作权限
        if($this->userInfo['isAuth']==false && REV_AUTH == true){
        	$this->redirect(S('SERVER_LOGIN_URL'));
        }
        // 实例化视图类
        if(null === $this->dp = Router::request()){
        	$this->view = Deve::instance('View'); 
        }
        // 实例化缓存类
        $this->cache      = Cache::getInstance(S('APP_CACHE_TYPE'));
	    // 控制器初始化
        if(method_exists($this,'_initialize'))
            $this->_initialize();
    }

    /**
     +----------------------------------------------------------
     * 获取传入的参数的名称
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     */
    protected function getParam($var) {
        $value   = !empty($_POST[$var]) ?
            $_POST[$var] :
            (!empty($_GET[$var])?$_GET[$var]:null);
        return $value;
    }

    /**
     +----------------------------------------------------------
     * 是否AJAX请求
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
    protected function isAjax() {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
            if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return true;
        }
        if(!empty($_POST[S('VAR_AJAX_SUBMIT')]) || !empty($_GET[S('VAR_AJAX_SUBMIT')]))
            // 判断Ajax方式提交
            return true;
        return false;
    }

    /**
     +----------------------------------------------------------
     * 模板显示
     * 调用内置的模板引擎显示方法，
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function display($templateFile='',$charset='',$contentType='text/html')
    {
		$this->view->display($templateFile,$charset,$contentType);
    }

    /**
     +----------------------------------------------------------
     *  获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function fetch($templateFile='',$charset='',$contentType='text/html')
    {
        return $this->view->fetch($templateFile,$charset,$contentType);
    }

    /**
     +----------------------------------------------------------
     *  创建静态页面
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @htmlfile 生成的静态文件名称
     * @htmlpath 生成的静态文件路径
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function buildHtml($htmlfile='',$htmlpath='',$templateFile='',$charset='',$contentType='text/html') {
        return $this->view->buildHtml($htmlfile,$htmlpath,$templateFile,$charset,$contentType);
    }

    /**
     +----------------------------------------------------------
     * 模板变量赋值
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function assign($name,$value='')
    {
        $this->view->assign($name,$value);
    }

    public function __set($name,$value) {
        $this->view->assign($name,$value);
    }

    /**
     +----------------------------------------------------------
     * 取得模板显示变量的值
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $name 模板显示变量
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    protected function get($name)
    {
        return $this->view->get($name);
    }

    /**
     +----------------------------------------------------------
     * Trace变量赋值
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function trace($name,$value='')
    {
        if($this->dp){
        	$this->dp->debug(array($name,$value));
        }else{
        	$this->view->trace($name,$value);
        }    
     }

    /**
     +----------------------------------------------------------
     * 魔术方法 有不存在的操作的时候执行
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $method 方法名
     * @param array $parms 参数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __call($method,$parms) {
        if( 0 === strcasecmp($method,ACTION_NAME)) {
            // 检查扩展操作方法
            $_action = S('_actions_');
            if($_action) {
                // 'module:action'=>'callback'
                if(isset($_action[MODULE_NAME.':'.ACTION_NAME])) {
                    $action  =  $_action[MODULE_NAME.':'.ACTION_NAME];
                }elseif(isset($_action[ACTION_NAME])){
                    // 'action'=>'callback'
                    $action  =  $_action[ACTION_NAME];
                }
                if(!empty($action)) {
                    call_user_func($action);
                    return ;
                }
            }
            // 如果定义了_empty操作 则调用
            if(method_exists($this,'_empty')) {
                $this->_empty($method,$parms);
            }else {
                // 检查是否存在默认模版 如果有直接输出模版
                if(file_exists_case(S('TMPL_FILE_NAME')))
                    $this->display();
                else
                    // 抛出异常
                    throw_exception(L('_ERROR_ACTION_').ACTION_NAME);
            }
        }elseif(in_array(strtolower($method),array('ispost','isget','ishead','isdelete','isput'))){
            return strtolower($_SERVER['REQUEST_METHOD']) == strtolower(substr($method,2));
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
        }
    }

    /**
     +----------------------------------------------------------
     * 操作错误跳转的快捷方法
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $message 错误信息
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function error($message,$langArr=array(),$ajax=false)
    {
        $langArr && $message = L($message,$langArr);	
    	if($this->dp){
        	$this->msg($message,MESSAGE_FAILED);
        }else{
    	    $this->_dispatch_jump($message,1,$ajax);
        }
    }

    /**
     +----------------------------------------------------------
     * 操作成功跳转的快捷方法
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $message 提示信息
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function success($message,$langArr=array(),$ajax=false)
    {
        $langArr && $message = L($message,$langArr);	
    	if($this->dp){
        	$this->msg($message,MESSAGE_SUCCESS);
        }else{
    	    $this->_dispatch_jump($message,1,$ajax);
        }
    }

    /**
     +----------------------------------------------------------
     * Ajax方式返回数据到客户端
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $data 要返回的数据
     * @param String $info 提示信息
     * @param boolean $status 返回状态
     * @param String $status ajax返回类型 JSON XML
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function ajaxReturn($data,$info='',$status=1,$type='')
    {
        // 保证AJAX返回后也能保存日志
        if(S('LOG_RECORD')) Log::save();
        $result  =  array();
        $result['status']  =  $status;
        $result['info'] =  $info;
        $result['data'] = $data;
        if(empty($type)) $type  =   S('DEFAULT_AJAX_RETURN');
        if(strtoupper($type)=='JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header("Content-Type:text/html; charset=utf-8");
            exit(json_encode($result));
        }elseif(strtoupper($type)=='XML'){
            // 返回xml格式数据
            header("Content-Type:text/xml; charset=utf-8");
            exit(xml_encode($result));
        }elseif(strtoupper($type)=='EVAL'){
            // 返回可执行的js脚本
            header("Content-Type:text/html; charset=utf-8");
            exit($data);
        }else{
            // TODO 增加其它格式
        }
    }

    /**
     +----------------------------------------------------------
     * Action跳转(URL重定向） 支持指定模块和延时跳转
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $url 跳转的URL表达式
     * @param array $params 其它URL参数
     * @param integer $delay 延时跳转的时间 单位为秒
     * @param string $msg 跳转提示信息
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function redirect($url,$params=array(),$delay=0,$msg='') {
    	if(S('LOG_RECORD')) Log::save();
    	if($this->dp){
			$this->dp->redirect($url,$params);
			$this->onEnd();
		}else{
		    redirect($url,$delay,$msg);
		}
	}

    /**
     +----------------------------------------------------------
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     +----------------------------------------------------------
     * @param string $message 提示信息
     * @param Boolean $status 状态
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    private function _dispatch_jump($message,$status=1,$ajax=false)
    {
        // 判断是否为AJAX返回
        if($ajax || $this->isAjax()) $this->ajaxReturn('',$message,$status);
        // 提示标题
        $this->assign('msgTitle',$status? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->get('closeWin'))    $this->assign('jumpUrl','javascript:window.close();');
        $this->assign('status',$status);   // 状态
        $this->assign('message',$message);// 提示信息
        //保证输出不受静态缓存影响
        S('HTML_CACHE_ON',false);
        if($status) { //发送成功信息
            // 成功操作后默认停留1秒
            if(!$this->get('waitSecond'))    $this->assign('waitSecond',"1");
            // 默认操作成功自动返回操作前页面
            if(!$this->get('jumpUrl')) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->display(S('TMPL_ACTION_SUCCESS'));
        }else{
            // 发生错误时候默认停留3秒
            if(!$this->get('waitSecond'))    $this->assign('waitSecond',"3");
            // 默认发生错误的话自动返回上页
            if(!$this->get('jumpUrl')) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display(S('TMPL_ACTION_ERROR'));
        }
        if(S('LOG_RECORD')) Log::save();
        // 中止执行  避免出错后继续执行
        exit ;
    }
    
    ################################################################################
                       #        数据解析器
    ################################################################################
    
    
    protected function output($params=array(),$activeVO=array())
    {
		if($this->dp === null){ 
		    print_r($params);
        }
    	if(empty($this->outData)){
			$this->outData = $params;
		}elseif(is_object($this->outData)){
			foreach ((array)$params as $k=>$v){
				$this->outData->$k = $v;
			}
		}else{
			foreach ((array)$params as $k=>$v){
				$this->outData[$k] = $v;
			}
		};
		$this->onEnd();
    }
    
    protected function setActiveVO($key,$value,$VO='IndexVO'){
		if(!isset($this->activeVO[$VO])){
			$this->activeVO[$VO]=array();
		}
		$this->activeVO[$VO][$key]=$value;
	}
	
    protected function msg($message='',$msgno=MESSAGE_FAILED,$backurl='',$backparams=array()){
		if(S('APP_DEBUG')){
			$this->trace('load_time',number_format(($GLOBALS['_loadTime'] -$GLOBALS['_beginTime'] ), 3));
			$this->trace('init_time',number_format(($GLOBALS['_initTime'] -$GLOBALS['_loadTime'] ), 3));
			$this->trace('exec_time',number_format(($startTime  -$GLOBALS['_initTime'] ), 3));
			$this->trace('parse_time',number_format(($endTime - $startTime), 3));
		}
		if($this->dp){
			$activeVO=array();
			foreach ($this->activeVO as $k=>$vo){
				if(is_array($vo)){
					$activeVO[] = arrayToVO($vo,$k);
				}else{
					$activeVO[] = $vo;
				}
			}
			$this->dp->msg($msgno,$message,$backurl,$this->outData,$activeVO,$backparams);
		}
		exit;
    }

    protected function onEnd(){
        if(S('APP_DEBUG')){
			$this->trace('load_time',number_format(($GLOBALS['_loadTime'] -$GLOBALS['_beginTime'] ), 3));
			$this->trace('init_time',number_format(($GLOBALS['_initTime'] -$GLOBALS['_loadTime'] ), 3));
			$this->trace('exec_time',number_format(($startTime  -$GLOBALS['_initTime'] ), 3));
			$this->trace('parse_time',number_format(($endTime - $startTime), 3));
        }
        
		if($this->dp){
			$activeVO=array();
			foreach ($this->activeVO as $k=>$vo){
				if(is_array($vo)){
					$activeVO[] = arrayToVO($vo,$k);
				}else{
					$activeVO[] = $vo;
				}
			}
			$this->dp->out($this->outData,$activeVO);
		}
    }
        
   function __destruct(){
   //	   $this->onEnd();
   } 

}//类定义结束
?>
