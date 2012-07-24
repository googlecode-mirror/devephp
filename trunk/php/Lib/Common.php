<?php
define('DBO_PATH',MODEL_PATH.'/DBO');

/**
 +----------------------------------------------------------
 * O函数用于实例化数据对象
 +----------------------------------------------------------
 * @param string name 对象名
 * @param int id 用户对象id
 * @param int uid 用户id
 +----------------------------------------------------------
 * @return Object
 +----------------------------------------------------------
 */
function O($objectName,$id,$uid,$rs=false,$ForRead=true){
	static $_object = array();
	static $_oTable = array();

	if(empty($objectName))return false;

	$className =  '__DBO_'.$objectName;
	$classFile = DBO_PATH.'/'.$className.'.class.php';
	require_cache($classFile);

	if(!class_exists($className)){
		throw_exception(L($className));
	}

	if(isset($_oTable[$className.$uid])){
		$objectTable = $_oTable[$className.$uid]; 
	}else{
		$objectTable = $_oTable[$className.$uid] = call_user_func(array($className,'get_obj_table'),$uid);
	}

	if(isset($_object[$objectName.'&'.$objectTable.'&'.$id]))
		return $_object[$objectName.'&'.$objectTable.'&'.$id];

	$db = Db::getInstance();
	$cache = Cache::getInstance(S('DBO_CACHE_TYPE'));
	// 实例化数据对象
	$object = new $className($id,$uid,$cache,$db,true,$rs);
	if($object->exists===false || $object->id===0){
		return $object;
	}else{
		$_object[$objectName.'&'.$objectTable.'&'.$object->id] = $object;
		return $_object[$objectName.'&'.$objectTable.'&'.$object->id];
	}
}

function formatSize($size,$acuteDeg=3){
	if($size>=1073741824){
		return strval(number_format($size/1073741824,$acuteDeg,".",""))."GB";
	}elseif($size>=1048576){
		return strval(number_format($size/1048576,$acuteDeg,".",""))."MB";
	}elseif($size>=1024){
		return strval(number_format($size/1024,$acuteDeg,".",""))."KB";
	}else{
		return strval($size)."Bytes";
	}
}
/**
 * 将数组转为 Flex 的 VO对象
 *
 * @param array $arr
 */
function arrayToVO($arr,$vo=''){
	static $voList=array();
	$o = null;
	
	if(!is_array($arr) || $vo=='')return $arr;
	if(!isset($voList[$vo])){
		if(!class_exists($vo) && is_file(CONFIG_PATH.'/VO/'.$vo.'.php')){
		   include(CONFIG_PATH.'/VO/'.$vo.'.php');
		   $voList[$vo] = true;
		}else{
		   $voList[$vo] = false;
		}	
	}
	if($voList[$vo] == true){
		$o=new $vo();
		foreach ($arr as $k=>$v){
			$o->$k=$v;
		}
		return $o->toObject();
	}else{
		$o=(object)$arr;
		$o->_explicitType = $vo;
		return $o;
	}
}

// 获取文件扩展名
function file_ext($file_name)
{
	$retval='';
	$pt=strrpos($file_name, '.');
	if ($pt) $retval=substr($file_name, $pt+1, strlen($file_name) - $pt);
	return ($retval);
}

function sendMessage($cmd,$tuid='',$GroupName='',$Params=array(),$Return=false,$JavaCmd='Trans'){
	if($GroupName==''){
		$GroupName=APP_NAME.SERVER_UNIQUEID;
	}elseif($GroupName=='CrossServer'){
		//不修改组名
		//空组名
		//		$GroupName='';
		$GroupName='CrossServer_'.trim(S('co_action'));
	}else{
		$GroupName=APP_NAME.$GroupName;
	}

	$Params['tuid']=strval($tuid);
	$Params['grp']=$GroupName;
	$Params['cmd']=$cmd;


	/*if($Return===false&&file_exists(SEND_MESSAGE_SOCKET_TMP)){
		//信息量处理慢的时候直接发送，不使用系统对列
		if(filesize(SEND_MESSAGE_SOCKET_TMP)<1024){
			$fp=fopen(SEND_MESSAGE_SOCKET_TMP,'ab+');
			if($fp){
				fwrite($fp,MakeJavaInfo(0,$JavaCmd,$Params)."***\r\n\r\n***");
				fclose($fp);
			}
			return true;
		}
	}*/
	$ServerHost=S('JS_ServerHost');
	$ServerPort=intval(S('JS_ServerPort'));


	if($ServerHost==''){
		throw_exception('No Set System Variables JavaHost ');
		return false;
	}
	if($ServerPort==0){
		$ServerPort=8080;//默认端口
	}
	//return ;;//暂时禁用
	$errno=$errstr=false;
	$socket=fsockopen($ServerHost,$ServerPort,$errstr,$errno,5);
	if($socket){
		$Info=MakeJavaInfo(0,$JavaCmd,$Params);
		if($Info>32767)//限制包的最大字节数
		{
			WriteLog('Too long packet on socket');
			return false;
		}

		//读延迟
		if($Return===true){
			stream_set_timeout($socket,2);
		}else{
			stream_set_timeout($socket,0,10000);
		}

		fwrite($socket,$Info);


		if($Return===true)//获取客户端返回的结果
		{
			$b=fread($socket,32768);
			$len=StrToLong(substr($b,0,4));
			$Proxy=substr($b,4,3);
			$EncodeNum=ord($b[7]);
			$EncodeStr=substr($b,8,$len);
			$data=null;
			if($EncodeNum==1)//XXTEA加密
			{
				$EncodeStr=xxtea_decrypt($EncodeStr,ENCRYPT_KEY);
			}

			$Amf=new AMFDeserializer($EncodeStr);
			$data=$Amf->readAmf3Data();
			fclose($socket);
			return $data;
		}
		fclose($socket);
		return true;
	}else{
		throw_exception('Can not Connect to the socket server');
		return false;
	}
}
//包装发送到 java服务的信息
function MakeJavaInfo($UserId,$Cmd,$Object){
	static $serializer=null;
	if($serializer===null)$serializer = new AMFSerializer();
	$serializer->outBuffer='';
	$serializer->writeAmf3Data($Object); // serialize the data
	$result = "GM".$serializer->outBuffer;//GM 为附加的两个字节，不一定是GM
	if($Cmd===null||$Cmd==='')$Cmd="Trans";////转发命令
	$AttachToken=$info='';
	if($UserId==0){
		$AttachToken=rand(1,200);//附加的Token参数，避免同一个session中断掉
	}
	$info.=LongToStr(strlen($result));
	$info.='hrq';

	$info.=chr(strlen($Cmd));
	$info.=$Cmd;

	$sid=createToken($UserId,'',$AttachToken);

	$info.=LongToStr(strlen($sid));
	$info.=$sid;

	//不加密
	$info.=chr(0);
	$info.=$result.chr(0);
	return $info;
}
function createToken($uid,$key='',$AttachToken='')
{
	return passport_encrypt($uid.'|'.getDoamin().'|'.APP_NAME.SERVER_UNIQUEID.($AttachToken===''?'':'|'.$AttachToken),ENCRYPT_KEY,$key);
}
function LongToStr($l){
	return chr($l>>24).chr(($l>>16)&0xFF).chr($l>>8).chr($l&0xFF);
}
function getDoamin(){
	$arr=explode(':',$_SERVER['HTTP_HOST']);
	return 'www.i.com';
	return $arr[0];
}

###########encrypt start##############
function passport_decrypt($txt, $key) {
	$txt = passport_key(gf_base64_decode($txt), $key);
	$tmp = '';
	for ($i = 0; $i < strlen($txt); $i++) {
		$tmp .= $txt[$i] ^ $txt[++$i];
	}
	return $tmp;
}

function passport_encrypt($txt, $key,$encrypt_key='') {
	if($encrypt_key=='')$encrypt_key = md5(microtime(true));
	$ctr = 0;
	$tmp = '';
	$tl=strlen($txt);
	for($i = 0; $i < $tl; $i++) {
		$ctr = $ctr == 32 ? 0 : $ctr;
		$tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
	}
	return gf_base64_encode(passport_key($tmp, $key)); //R 真正使用密码加密
}
function passport_key($txt, $encrypt_key) {

	$encrypt_key = md5($encrypt_key);
	$ctr = 0;
	$tmp = '';
	$tl=strlen($txt);
	for($i = 0; $i < $tl; $i++) {
		$ctr = $ctr == 32 ? 0 : $ctr;
		$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
	}
	return $tmp;
}

function gf_base64_encode($plain_str){
	return str_replace(
		array('=','+','/'),array(',','_','(')
		,base64_encode($plain_str));
}

function gf_base64_decode($encode_str){
	return base64_decode(str_replace(
	array(',','_','('),array('=','+','/')
	,$encode_str));
}
##############encrypt end#############
?>