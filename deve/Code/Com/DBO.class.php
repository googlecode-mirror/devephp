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
 * DevePHP 数据库数据对象
 +------------------------------------------------------------------------------
 * @category   DevePHP Game
 * @package  DevePHP App
 * @subpackage  Core
 * @author    yearnfar <yearnfar@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */

class DBO{

	protected $id = 0;                   // 对象ID
	protected $uid = 0;                  // 用户ID
	protected $cache = null;             // 缓存对象
	protected $db = null;                // 数据库对象
	protected $usePackCache = 0;         // 是否使用缓存
	protected $omitUid = false;          // 是否核对uid
	protected $syncTime = 0;             // 缓存校准时间

	protected $expireFiled = '';         //
	protected $propChanged = array();
	protected $protectedField = array(); //
	protected $__propChanged = array();  //	
	protected $triggerFileds = array();      // 触发器关联的字段列表,子类中覆盖此属性 
    	
	private $_extProperties = array();       // 扩展属性副本
	private $isExtPropertiesChange = false;  // 扩展属性是否已变化
	private $extPropertiesValue = array();
	private $oldProperties = array();        // 对象的初始值
	private $isSave = false;                 //
	private $__synctime = 0;                 //
	private $__fields = array();             // 发生改变的属性

	public $exists = false;                  // 对象是否存在
	public $isNew = false;
	public $_explicitType = '';              // 

    /**
     +----------------------------------------------------------
     * 构造函数、获得数据对象
     +----------------------------------------------------------
     * @access public final
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	public final function  __construct($id,$uid,&$cache,&$db,$auto=false,$rs=false){
		$this->id = $id;
		$this->uid = $uid;

		$this->cache = $cache === null?Cache::getInstance(S('DBO_CACHE_TYPE')):$cache;
		$this->db = $db === null?Db::getInstance():$db;
		if(!$this->db) throw_exception(L('__NOT_DB_SUPPORT__'));

		if($rs!== false){
			if(S('DBO_PERSIST_CACHE')==false || !$this->load_from_cache()){
				$this->load_from_rs($rs,true);
			}
		}elseif($auto && $id>0){
			$this->load_from_cache() or $this->load_from_db();			
		}
		if($this->exists){
			$this->uid = intval($this->properties['uid']);
			$this->oldProperties = $this->properties;
			if(isset($this->properties['extProperties'])){
				$this->_extProperties = $this->getObject($this->properties['extProperties']);
				is_array($this->_extProperties) or $this->_extProperties=array();
				$isClear=false;
				foreach ($this->_extProperties as $k=>$v){
					if(count($v)!=3 || ($v[2]>0 && $v[2]<TIMESTAMP)){
						unset($this->_extProperties[$k]);
						$isClear=true;
					}else{
						$this->extPropertiesValue[$v[0]]+=$v[1];
					}
				}
				if($isClear){
					$this->propChanged['extProperties'] = 1;
					$this->properties['extProperties'] = serialize($this->_extProperties);
				}
			}
		}
	}

	/**
     +----------------------------------------------------------
     * 从缓存获取数据对象
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
	 */
	public function load_from_cache(){
		if(!$this->cache || $this->usePackCache==0)return false;
		$key=$this->objectName.'_'.$this->get_obj_table($this->uid).'_'.$this->id;

		$result = $this->cache->get($key);

		if(false !== $result){
			if($this->omitUid == false && $result['uid'] != $this->uid){
				$this->cache->rm($key);
				return false;
			}
			// 对象是否更新过
			$handleTime = defined('DBO_HANDLE_'.$this->objectName)?constant('DBO_HANDLE_'.$this->objectName):0;
			$__savetime=isset($result['__savetime'])?$result['__savetime']:0;
			if($handleTime>0 && $handleTime>=$__savetime){
				return false;
			}
			$this->initProperties($result);
			$this->propChanged=array();
			if($this->properties['deleted']==0)$this->exists=true;
			return true;
		}
	}

    /**
     +----------------------------------------------------------
     * 从数据库获取数据对象
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
	public function load_from_rs($result=array(),$source=false){
		if($result && is_array($result)){
			if($this->omitUid == false && $result['uid'] != $this->uid){  	//判断是否需要核对用户id
				return false;
			}
			if($source===false){
				$this->initProperties($result);
			}else{
				$this->properties = $result;
			}
			$this->id = $this->properties['id'];
			$this->uid = $this->properties['uid'];
			if($source === false)$this->save_to_cache();
			if($this->properties['deleted']==0)$this->exists=true;
			return true;
		}else{
			return false;
		}
	}

	/**
     +----------------------------------------------------------
     * 从数据库获取数据对象
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
	 */
	public function load_from_db(){
		
		$options['table'] = $this->get_obj_table($this->uid);
		$options['field'] = $this->get_sql_fields();
		$options['where'] = $this->mainKey.'='.$this->id.' AND deleted=0'.
		                    ($this->expireFiled?' AND ('.$this->expireFiled.'=0 OR '.$this->expireFiled.'>'.TIMESTAMP.')':''.
		                    ($this->omitUid?'':' AND uid='.$this->uid));
		$options['limit'] = 1; 
		$result = $this->db->select($options);
		if($result[0] && is_array($result[0])){
			return $this->load_from_rs($result[0]);
		}else{
			return false;
		}
	}

	/**
     +----------------------------------------------------------
     * 从数据库获取数据对象
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
	 */
	public function reload_from_db($fields=array()){
		if(count($fields) == 0)return false;
		$options['table'] = $this->get_obj_table($this->uid);
		$options['field'] = $fields;
		$options['where'] = $this->mainKey.'='.$this->id.' AND deleted=0'.
		                    ($this->expireFiled?' AND ('.$this->expireFiled.'=0 OR '.$this->expireFiled.'>'.TIMESTAMP.')':''.
		                    ($this->omitUid?'':' AND uid='.$this->uid));
		$options['limit'] = 1;
		$result=$this->db->select($options);
		if($result[0] && is_array($result[0])){
			return $this->load_from_rs($result[0]);
		}else{
			return false;
		}
	}

	/**
     +----------------------------------------------------------
     * 创建用户对象
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
	 */
	public final function create(){
		if($this->exists===true)return false;
		$data = array();
		$options['table'] = $this->get_obj_table($this->uid);
		if($this->id==0){
			//$this->Properties['Id']=$this->Id=$this->get_unique_id($table);
		}else{
			$this->properties['id'] = $this->id;
		}

		$this->properties['uid'] = $this->uid;
		foreach($this->properties as $key=>$value){
			if($key[0]=='_'){
				$value = TIMESTAMP;
			}elseif($key=='extProperties'){
				$value = serialize(array());
			}
			$data[$key] = $value;
		}
		$this->db->insert($data,$options);
		if($this->db->error() == ''){
			$this->id = $this->properties['id'] = 0;
		}elseif($this->id==0){
			$this->id = $this->properties['id'] = $this->db->lastInsID;
		}
		if($this->id==0)return false;
		$this->isNew = true;
		$this->propChanged = array();
		$this->save_to_cache();
		$this->exists = true;

//		$this->DataCon->DataObjects[$this->objectName.'&'.$options['table'].'&'.$this->id] = $this;
		return true;
	}

	/**
     +----------------------------------------------------------
     * 将数据对象保存到缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
	 */
	private final function save_to_cache(&$encodeArr=null){
		if($this->id===0||$this->cache===false)return false;
		$key = $this->objectName.'_'.$this->get_obj_table($this->uid).'_'.$this->id;
		$properties = $this->properties;
		$objectProperties=$this->objectProperties;

		if(is_array($objectProperties)){
			foreach ($objectProperties as $pKey){
				if($encodeArr!==null && is_array($encodeArr) && isset($encodeArr[$pKey])){
					$properties[$pKey] = $encodeArr[$pKey];
				}else{
					$properties[$pKey] = $this->setObject($this->properties[$pKey]);
					if($encodeArr!==null)$encodeArr[$pKey]=$properties[$pKey];
				}
			}
		}
		$properties['__savetime'] = TIMESTAMP;
		if(S('DBO_PERSIST_CACHE') == true){
			$__synctime=$this->__synctime;
			$__fields=$this->__fields;

			if($this->syncTime==0 || $__synctime==0 || ($__synctime+$this->syncTime) <= TIMESTAMP){
				$this->__synctime = TIMESTAMP;
				$properties['__fields'] = array();
			}elseif(empty($this->__fields)){
				$properties['__fields'] = array_keys($this->propChanged);
			}else{
				$properties['__fields'] = array_merge(array_keys($this->propChanged),$this->__fields);
				$properties['__fields'] = array_unique($properties['__fields']);
			}
		}
		$properties['__synctime']=$this->__synctime;
		return $this->cache->set($key,$properties,0);	
	}

	/**
     +----------------------------------------------------------
     * 将数据对象保存到数据库
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
	 */
	private final function save_to_db($immediately=false,&$encodeArr=null){
		if($this->Id===0)return false;
		$Fields=$Values=array();
		$keys=array_keys($this->PropChanged);
		if(is_array($this->__fields)&&count($this->__fields)>0){
			$keys=array_unique(array_merge($keys,$this->__fields));
		}
		foreach($keys as $key){
			if($v<1&&$this->PropDefines[$key][1]==3
				&&is_object($this->Properties[$key])
				&&!$this->Properties[$key]->isChanged())continue;

			$Fields[]=$key;
			if($this->PropDefines[$key][1]==3){
				if($encodeArr!==null&&is_array($encodeArr)&&isset($encodeArr[$key])){
					$Values[]=$this->Db->escape_string($encodeArr[$key]);
				}else{
					$Values[]=$this->Db->escape_string($this->setObject($this->Properties[$key]));
				}
			}elseif($this->PropDefines[$key][1]==1){
				$Values[]=$this->Db->escape_string($this->Properties[$key]);
			}else{
				$Values[]=$this->Properties[$key];
			}
		}
		if(count($Fields)==0)return true;
		return $this->Db->update(
			$this->get_obj_table($this->UserId)
			,$Fields
			,$Values
			,$this->MainKey.'='.$this->Id
			,!$immediately   //延迟更新
			,false   //缓冲更新
		);
	}

	/**
     +----------------------------------------------------------
     * 保存数据对象
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
	 */
	public final function save($immediate=false){

		if(count($this->propChanged)>0 || $immediate){
			$key='DBO_PF_'.$this->objectName.'_'.$this->id;

			//再次验证安全保护的属性
			$arr=$this->cache->get_eac($key);
			if(is_array($arr)){
				foreach ($arr as $k=>$v){
					if($v!=0 && isset($this->properties[$k])){
						$this->properties[$k] = $v;
					}
				}
			}
			//编码扩展属性
			$this->encodeExtProperties();
			$encodeArr=array();
			$SaveResult=false;
			if($this->UserPackCache>0)$SaveResult=$this->save_to_cache($encodeArr);//使用缓存模式
			if($SaveResult===false||S('DBO_PERSIST_CACHE')==false||$this->__synctime==SYSTEM_TIME||$immediately){
				$this->save_to_db($immediate,$encodeArr);
			}
			$this->IsSave=true;
			$this->__propChanged=array_merge($this->__propChanged,$this->propChanged);
			$this->propChanged=array();
			return true;
		}
		return false;
	}
    
	/**
     +----------------------------------------------------------
     * 获取DBO对象对应表名
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
	 */
	public static function get_obj_table($uid=0){}

	/**
     +----------------------------------------------------------
     * 获取DBO字段
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
	 */
	public static function get_sql_fields(){}

	/**
     +----------------------------------------------------------
     * 初始化数据对象属性
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
	 */
	function initProperties($result){
		foreach($result as $k=>$v){
			if(!isset($this->properties[$k]))continue;
			if($v === null){
				$v = $this->propDefines[$k][1]==0?0:'';
			}elseif(!is_object($v)){
				if($this->propDefines[$k][1]==3 && $k!='extProperties'){
					$this->properties[$k]=$this->getObject($v);
					if($this->properties[$k]===false){
						$this->properties[$k]=new Object();
					}
				}else{
					$this->properties[$k] = $this->propDefines[$k][1]==0?(double)$v:(string)$v;
				}
			}
		}

		$this->__synctime = isset($result['__synctime'])?$result['__synctime']:TIMESTAMP;
		$this->__fields = (isset($result['__fields']) && is_array($result['__fields']))?$result['__fields']:array();
	}

	/**
     +----------------------------------------------------------
     * 获取数据对象属性
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
	 */
	public final function getProperties(){
		return $this->properties;
	}

	/**
     +----------------------------------------------------------
     * 获取数据对象额外属性
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
	 */
	public final function getExtProperties($key='',$tag=false){
		if($key!=''){
			$arr=array();
			foreach ($this->_extProperties as $k=>$v){
				if($v[0]==$key && ($tag===false||(isset($v[3]) && $v[3]===$tag))){
					$arr[$k]=$v;
				}
			}
			return $arr;
		}else{
			return $this->_extProperties;
		}
	}

	/**
     +----------------------------------------------------------
     * 设置额外属性
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
	 */
	public final function setExtProperties($key,$value,$expire=0,$over=false,$tag=false){
		if($key!=''){
			if(!isset($this->propDefines['extProperties']))return ;
			$expire < TIMESTAMP	&& $expire = 0;

			$find=false;
			foreach ($this->_extProperties as $k=>$v){
				if($v[0]==$key){
					if($tag===false){
						if($over){
							if($value==$v[1]&&$expire==$v[2])return;
							unset($this->_extProperties[$k]);
						}elseif($v[2]==$expire){
							$this->_extProperties[$k][1]+=$value;
							$find=true;
							break;
						}
					}elseif(isset($v[3])&&$v[3]===$tag){
						if($value==0){
							unset($this->_extProperties[$k]);
						}else{
							$this->_extProperties[$k][1]=$value;
						}
						$this->extPropertiesValue[$key]=$this->extPropertiesValue[$key]-$v[2]+$value;
						$find=true;
						break;
					}
				}
			}
			if($tag===false){
				if($over){
					if($value==0){
						unset($this->extPropertiesValue[$key]);
						$find=true;
					}else{
						$this->extPropertiesValue[$key]=$value;
					}
				}else{
					$this->extPropertiesValue[$key]+=$value;
				}
			}
			if($find===false && $tag===false){
				$this->_extProperties[]=array($key,$value,$expire);
			}elseif($find === false){
				$this->_extProperties[]=array($key,$value,$expire,$tag);

			}
			if(isset($this->propChanged['extProperties']))
				$this->propChanged['extProperties']+=1;
			else
				$this->propChanged['extProperties']=1;

			$this->isExtPropertiesChange = true;
		}
	}


	/**
     +----------------------------------------------------------
     * 获取变化前数据对象属性
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return unknow
     +----------------------------------------------------------
	 */
	public final function getOldProperties($name)
	{
		$value=null;
		if(isset($this->propDefines[$name]) && $this->propDefines[$name][1]!=2){
			$value=$this->oldProperties[$name];
		}
		if($this->propDefines[$name]==0 && isset($this->extPropertiesValue[$name])){
			return $value+$this->extPropertiesValue[$name];
		}else{
			return $value;
		}
	}

	/**
     +----------------------------------------------------------
     * 写保护的属性，避免原子操作冲突 
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
	 */
	private function writeProtected($field,$value){

		if($this->id==0 || !isset($this->protectedField[$field]))return false;
		$key = 'DBO_PF_'.$this->objectName.'_'.$this->Id;

		$arr = $this->cache->get_eac($key);
		is_array($arr) or $arr=array();
		$arr[$field]=$value;

		return $this->cache->set_eac($key,$arr,60);
	}

    /**
     +----------------------------------------------------------
     * 读取保护属性
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return int
     +----------------------------------------------------------
     */
	private function readProtected($field){
		if($this->id==0 || !isset($this->protectedField[$field]))return 0;
		$key='DBO_PF_'.$this->objectName.'_'.$this->id;

		$arr=$this->cache->get_eac($key);
		if(!is_array($arr))return 0;
		return intval($arr[$field]);
	}

    /**
     +----------------------------------------------------------
     * 获取DBO扩展数据
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return array or object
     +----------------------------------------------------------
     */
	private function getObject($str){
		if($str=='')return false;
		if(substr($str,0,4)==='amf3'){
			$obj=self::$ByteArray->readObject(substr($str,4));
			if($obj instanceof Object )$obj->reset();
			return $obj;
		}else{
			return unserialize($this->properties['extProperties']);
		}
	}

	/**
     +----------------------------------------------------------
     * 保存DBO扩展数据
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
	 */
	private function setObject($object){
		if(self::$ByteArray !== false){
			return 'amf3'.self::$ByteArray->writeObject($object);
		}else{
			return serialize($object);
		}
	}

	/**
     +----------------------------------------------------------
     * DBO转换为数组
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
	 */
	public final function toArray($propertiesList=false){
		$P=array();
		$checkProp=false;
		if($propertiesList!==false && is_array($propertiesList))$checkProp=true;
		foreach ($this->propDefines as $k=>$v){
			if($k!=='extProperties' && ($checkProp===false || isset($propertiesList[$k]) || in_array($k,$propertiesList))
				&& $k[0]!=='_') $P[$k] = $this->$k;
		}
		return $P;
	}

	/**
     +----------------------------------------------------------
     * 数据库属性转换为对象
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return object
     +----------------------------------------------------------
	 */
	final public function toObject($propertiesList=false){
		$o=(object)$this->toArray($propertiesList);
		$o->_explicitType=$this->_explicitType;
		return $o;
	}

	/**
     +----------------------------------------------------------
     * 检查触发器
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return unknow
     +----------------------------------------------------------
	 */
	protected function checkTrigger(){}

	/**
     +----------------------------------------------------------
     * 调用触发器
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return unknow
     +----------------------------------------------------------
	 */
		public final function callTrigger($func,$params=array(),$repeat=true,$end=true){
			static $callList=array();
			if(!function_exists($func))return ;
			$key = $func.join('_',$params);

			if(end!==true && !isset($callList[$key])){
				call_user_func_array($func,$params);
			}

			if(isset($callList[$key])){
				$callList[$key]+=1;
			}else{
				$callList[$key] =1;
			}
		}

    /**
     +----------------------------------------------------------
     * 设置属性
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return unknow
     +----------------------------------------------------------
     */
	final private function __set($name ,$value)
	{
		if($value===null || $name[0]=='_' || $name=='id')return false;
		if(isset($this->propDefines[$name])
			&&($this->propDefines[$name][0]==false || in_array($this->propDefines[$name][1],array(0,1)))
			&&($this->properties[$name] != $value || ($this->PropDefines[$name][1]==1 && strlen($value)!=strlen($this->properties[$name])))
		){
			$varType=$this->propDefines[$name][1];
			$oldValue=$this->properties[$name];

			//如果是一个保护属性，写之前先对比之前的读
			if(isset($this->protectedField[$name])){
				$proValue = $this->readPropected($name);

				//该属性已经被别的进程更新了
				if($proValue!==0 && $proValue !== $oldValue){
					$value=$value+($proValue-$oldValue);
				}
			}

			if($varType==1
				&&(strlen($value)<$this->propDefines[$name][2] || strlen($value)>$this->propDefines[$name][3])
			){
				return false;
			}
			if($varType==0){
				if($value<$this->propDefines[$name][2]){
					$value=doubleval($this->propDefines[$name][2]);
				}elseif($value>$this->propDefines[$name][3]){
					$value=doubleval($this->propDefines[$name][3]);
				}
				$this->properties[$name]=doubleval($value);
			}else{
				$this->properties[$name]=$value;
			}
			//DBO写保护
			if(isset($this->protectedField[$name]))$this->writePropected($name,$value);

			if($oldValue!=$value || ($varType==1 && (strlen($oldValue)!=strlen($value)))){
				if(!isset($this->propChanged[$name])){
					$this->propChanged[$name] =1;
				}else{
					$this->propChanged[$name]+=1;
				}

				//触发器扩展
				if(isset($this->triggerFileds[$name])){
					$this->checkTrigger();
				}
			}
		}elseif(!isset($this->propDefines[$name])){
			//WriteLog('Access empty property "'.$name.'" in '.$this->ObjectName.'!');
		}
	}

	 /**
     +----------------------------------------------------------
     * 判断属性是否存在
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return unknow
     +----------------------------------------------------------
	  */
	final public function __isset($name)
	{
		return isset($this->properties[$name]);
	}

	/**
     +----------------------------------------------------------
     * 获取属性值
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return unknow
     +----------------------------------------------------------
	 */
	final private function __get($name)
	{
		$value=null;
		// 读写保护DBO属性
		if(isset($this->protectedField[$name])){
			$value = $this->readProtected($name);
			if($value != 0){
				$this->properties[$name] = $value;
				return $value;
			}
		}

		if(isset($this->propDefines[$name]) && $this->propDefines[$name][1]===3){
			$value = $this->properties[$name];
			$this->propChanged[$name] = 0;
		}elseif(isset($this->propDefines[$name]) && $this->propDefines[$name][1]!=2){
			$value = $this->properties[$name];
		}elseif(($method='__p_get_'.$name) && method_exists($this,$method)){
			$value = call_user_func(array($this,$method));
			if(isset($this->properties[$name]) && $this->properties[$name]!=$value){
				$this->properties[$name] = $value;
				$this->propChanged[$name]= 1;
			}
		}
		if(!is_object($value) && $value!=0 && isset($this->protectedField[$name]))$this->writePropected($name,$value);
		if(!is_object($value) && isset($this->extPropertiesValue[$name])){
			return $value+$this->extPropertiesValue[$name];
		}else{
			return $value;
		}
	}


    /**
     +----------------------------------------------------------
     * 析构函数
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return unknow
     +----------------------------------------------------------
     */
	function __destruct(){

	}
}

?>