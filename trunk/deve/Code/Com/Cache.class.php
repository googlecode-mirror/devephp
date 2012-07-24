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
 * 缓存管理类
 +------------------------------------------------------------------------------
 * @category   Deve
 * @package  Deve
 * @subpackage  Util
 * @author    yearnfar <yearnfar@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Cache extends Deve
{
	const EC = 1;   // eaccelerator缓存
	const MC = 2;   // memcache缓存
	const FC = 4;   // 文件缓存
    
	protected $cache =0;
	protected $memcache = null;
	public $support = 0;
	public $cacheDir = '';
	public $cKey = '';
	private static $_cache = null;

	private function __construct($type){
		$this->cKey = defined('APP_NAME')?APP_NAME : '';
        defined('TIMESTAMP') or define('TIMESTAMP',time());
		
		if($type & self::EC)
			function_exists('eaccelerator_get') or throw_exception(L('__NOT_EAC__'));
		if($type & self::MC)
			extension_loaded('memcache') || extension_loaded('memcached') or throw_exception(L('__NOT_MEMCACHE__'));
		if($type & self::FC)
			!defined('IS_FILE_CACHE') || IS_FILE_CACHE==false or throw_exception(L('__NOT_MEMCACHE__'));
		
		$this->cache = $type;
		$this->cacheDir = DATA_PATH.'/cache';
	}

	public static function getInstance($type)
	{
		isset(self::$_cache[$type]) or self::$_cache[$type]=new Cache($type);
		return self::$_cache[$type];
	}

	public function get($key){
		if($this->cache & self::EC && (false!==$value=$this->get_eac($key)) && $value!==null){ 
			return $value;
		}
		if($this->cache & self::MC && (false!==$value=$this->get_memcache($key)) && $value!==null){
			return $value;
		}
		if($this->cache & self::FC && (false!==$value=$this->get_filecache($key)) && $value!==null){
			return $value;
		}
		return false;
	}

	public function set($key,$value,$ttl=0,$force=false){
		if($key===false)return false;
		$result=false;
		if($this->cache & self::EC && (false!==$result=$this->set_eac($key,$value,$ttl))){
			return $result;
		}		
		if($this->cache & self::MC && (false!==$result=$this->set_memcache($key,$value,$ttl))){
			return $result;
		}		
		if(($this->cache & self::FC || $force) && (false!==$this->set_filecache($key,$value,$ttl))){
			return $result;
		}
		return $result;
	}

	public function rm($key){
		if($this->cache & self::EC)$this->rm_eac($key);
		if($this->cache & self::MC)$this->rm_memcache($key);	
		if($this->cache & self::FC)$this->rm_filecache($key);
	}

	public function Clear(){
		$this->cache & self::EC && eaccelerator_clear();
		$this->cache & self::MC && $this->memcache->flush();
		$this->cache & self::FC && self::enum_dir($this->cacheDir,array($this,'deletefile'));
	}

	public function get_eac($key){
		return eaccelerator_get($this->cKey.$key);
	}

	public function get_memcache($key){
		if($this->memcache || $this->memcache_connect())
			$value = $this->memcache->get($this->cKey.$key);
		return $value;
	}

	public function get_filecache($key,$force=false){
		$key=$this->cKey.$key;
		$crc32Key = crc32($key);
		$md5Key =md5($key);
		$file=$this->cacheDir.'/'.dechex($crc32Key & 0x0f).'/'.dechex(crc32($md5Key) & 0x0f).'/'.$key;
		if(!is_file($file))return false;

		$content=file_get_contents($file);
		if(strlen($content)<4 || (($arr=unpack('l',substr($content,0,4))) && $arr[1]<TIMESTAMP)){
			unlink($file);
			return false;
		}
		touch($file);
		return unserialize(substr($content,4));
	}

	public function set_eac($key,$value,$ttl=0){
		if(eaccelerator_put($this->cKey.$key,$value,$ttl))
		{
		    $this->cache & self::MC && $this->rm_memcache($key);			//清除第二层memcache缓存
			$this->cache & self::FC && $this->rm_filecache($key);		    //清除第三层文件缓存
			return true;
		}else{
			$this->rm_eac($key);
			return false;
		}
	}

	public function set_memcache($key,$value,$ttl=0){
		if($this->memcache===null && !$this->memcache_connect())return false;
		if($this->memcache->set($this->cKey.$key,$value,0,$ttl)){
			// 清除文件缓存
			$this->cache & self::FC && $this->rm_file($key);
			return true;
		}else{
			//	保存memcache失败
			$this->rm_memcache($key);
			return false;
		}
	}

	public function set_filecache($key,$value,$ttl=0){
		$key=$this->cKey.$key;
		$crc32Key = crc32($key);
		$md5Key =md5($key);
		$dir = $this->cacheDir.'/'.dechex($crc32Key & 0x0f);
		is_dir($dir) or mkdir($dir);
		$dir .= '/'.dechex(crc32($md5Key) & 0x0f);	
		is_dir($dir) or mkdir($dir);
		if($ttl==0)$ttl=31536000;  // 文件缓存保存一年
		return file_put_contents($dir.'/'.$key,pack('l',TIMESTAMP+$ttl).serialize($value));
	}

	public function rm_eac($key){
		return ($this->cache & self::EC && eaccelerator_rm($this->cKey.$key));
	}

	public function rm_memcache($key){
		if($this->cache & self::MC && ($this->memcache || $this->memcache_connect())){
			return $this->memcache->delete($this->cKey.$key);
		}
		return false;
	}

	public function rm_filecache($key){

		$key=$this->cKey.$key;
		$crc32Key = crc32($key);
		$md5Key =md5($key);
		$file=$this->cacheDir.'/'.dechex($crc32Key & 0x0f).'/'.dechex(crc32($md5Key) & 0x0f).'/'.$key;
		if(is_file($file))return @unlink($file);
		return true;
	}

	private function memcache_connect(){
		$isConnect=null;

		$this->memcache=new Memcache();
		$isConnect=S('CACHE_MEMCACHE_PCONNECT')?
			$this->memcache->pconnect(S('CACHE_MEMCACHE_HOST'),S('CACHE_MEMCACHE_PORT'),5):
			$this->memcache->connect(S('CACHE_MEMCACHE_HOST'),S('CACHE_MEMCACHE_PORT'),5);
		$this->memcache->setCompressThreshold(2000, 0.2); //大于2K自动压缩

		if($isConnect)return true;
		$this->memcache=false;
		return false;
	}

	function enum_dir($dir,$callback='',$reduce=true,$skipDirs=array(),$skipPat=array()){
		if(!is_dir($dir))return ;
		if(substr($dir,-1,1)!=='/')$dir.='/';
		if($dh = opendir($dir)){
			while(($filename = readdir($dh))!==false){
				if($filename=="." || $filename==".."){
					continue;
				}else{
					$f1=$dir.$filename;
					if(is_file($f1)){
						if($callback && call_user_func($callback,$f1)===-1)return;
					}elseif(is_dir($f1)){
						if($reduce===true){
							if($callback && call_user_func($callback,$f1)===-1)return;
							self::enum_dir($f1,$callback,$reduce);
						}
					}
				}
			}
		}
	}

	function deletefile($f){
		if(basename($f)=='index.html')return ;
		@unlink($f);
	}

	public function lock($key){
		return ($this->cache & self::EC && eaccelerator_lock($this->cKey.$key));
	}

	public function unlock($key){
		return ($this->cache & self::EC && eaccelerator_unlock($this->cKey.$key));
	}

}//类定义结束
?>