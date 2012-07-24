<?php
if (isset($_GET['openid']) && isset($_GET['openkey'])) {
	header('Location: php/interface.php?m=User&c=Login&a=login&Time='.time().'&'.trim($_SERVER['QUERY_STRING'],'?'));
die;
}

######################################   非腾讯平台进入  #################################################
if(isset($_GET['e']))exit('1');
define('APP_PATH', realpath(dirname(__FILE__)).'/php');
define('DEVE_PATH','../deve');
define('CONFIG_PATH',APP_PATH.'/Config');
define('EACS',false);

$UserTrueIP=get_userip();
if($UserTrueIP!='127.0.0.1' && !check_master_ip($UserTrueIP)){
	$LogoutUrl=S('SERVER_LOGOUT_URL');
	$LogoutUrl.=(strpos($LogoutUrl,'?')===false?'?e=1':'&e=1');
	header('location:'.$LogoutUrl);exit;
}
function check_master_ip($IP=''){
	$MasterIP=explode("\n",S('SERVER_MASTER_IP'));
	foreach ($MasterIP as $k=>$v){
		$MasterIP[$k]=(str_replace("\r",'',$v));
	}
	$MyIp=explode('.',$IP);

	foreach ($MasterIP as $p){
		$pArr=explode('.',$p);
		if(
			($pArr[0]=='*'||$pArr[0]==$MyIp[0])
			&&($pArr[1]=='*'||$pArr[1]==$MyIp[1])
			&&($pArr[2]=='*'||$pArr[2]==$MyIp[2])
			&&($pArr[3]=='*'||$pArr[3]==$MyIp[3])
		){
			return true;
		}
	}
	return false;
}
function S($name=null,$value=null)
{   
	static $_config = array();
	// 无参数时获取所有
	if(empty($name)) return $_config;
	$name = strtolower($name);
	if (is_null($value)){
		if(isset($_config[$name])){
			return $_config[$name];
		}else{
			if(false !== strpos($name,'_') && $tag = explode('_',$name)){						
				if(EACS && !is_null(eaccelerator_get($name))){
					$_config = array_merge($_config,eaccelerator_get($tag[0]));
					return $_config[$name];
				}	
				$file = ucwords($tag[0]);        // Source文件名
				// c0框架配置文件   c1 APP配置文件
				$c0 = file_exists(DEVE_PATH."/Source/{$file}.php")?include(DEVE_PATH."/Source/{$file}.php"):array();
				$c1 = file_exists(CONFIG_PATH."/{$file}.php")?include(CONFIG_PATH."/{$file}.php"):null;								
				$config = is_array($c1) ? array_change_key_case(array_merge($c0,$c1)) : array_change_key_case($c0);
				// 整合所有的配置文件
				$_config = array_merge($_config,$config);
				EACS && eaccelerator_put($tag[0],$config,600);
				// 返回配置
				return isset($_config[$name])?$_config[$name]:null;
			}
			return null;
		}
	}
	$_config[$name] = $value;
	return;
}
function get_userip(){
	if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$UserTrueIP = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$UserTrueIP = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$UserTrueIP = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$UserTrueIP = $_SERVER['REMOTE_ADDR'];
	}
	return $UserTrueIP;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>腾讯应用</title>
</head>

<body onload="document.getElementById('Uname').focus();">
<form action="php/interface.php?m=User&c=Login&a=login" METHOD="POST">
<table border="1" bgcolor="#999900" bordercolor="#000000" align="center" >
	<tr>
	<td>
		<table>
				<tr>
				<td width="300" align="center">登录</td>
			</tr>
			</table>
			<table width="300">
			<tr>
				<td width="80">用户名：</td>
				<td>
				<input type="text" name="Uname" id="Uname" />       
					<input type="hidden" name="Time" id="Time" value="123456789000" />      
					<input type="hidden" name="GameId" id="GameId" value="1" />     
					<input type="hidden" name="ServerId" id="ServerId" value="1" />     
					<input type="hidden" name="domainid" id="domainid" value="1" />     
					</td>
			</tr>

				<tr>
				<td width="80">密码：</td>
				<td>
				<input type="text" name="pass" id="pasTxt" />        </td>
			</tr>

				<tr>
				<td width="80">平台：</td>
				<td>
						<input type="radio" name="platform" value="" checked />无
						<input type="radio" name="platform" value="facebook"  />facebook
						<input type="radio" name="platform" value="pengyou"  />pengyou
					</td>
			</tr>

			</table>
			<table>
				<tr>
				<td width="300" align="center">
					<input type="submit" name="button" id="button" value="确定"/>    	</td>
				</tr>
			</table>
	</td>
    </tr>
</table>
</form>
</body>
</html>
