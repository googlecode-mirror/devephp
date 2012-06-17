<?php
// 获取文件扩展名
function file_ext($file_name)
{
	$retval='';
	$pt=strrpos($file_name, '.');
	if ($pt) $retval=substr($file_name, $pt+1, strlen($file_name) - $pt);
	return ($retval);
}

function ConfigFile($name) {
	$file = CONFIG_PATH."/Controller/".$name.".config.php";
	if (is_file($file)) return $file ;
	return false;
}
?>
