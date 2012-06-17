<?php
class HttpFile extends Control{

	function wget(){

		$getfilename = $this->getParam('filename');

		if( $getfilename && substr($getfilename,0,1) === '/') $getfilename = substr($getfilename, 1);
		if( $getfilename && substr($getfilename,-1,1) === '/') $getfilename = substr($getfilename, 0, -1);

		$getfilename = DATA_PATH.'/Download/'.$getfilename;

		$file_extname = file_ext($getfilename);

		$strrpos = strrpos($getfilename,'/');
		$strrpos = $strrpos !=0 ? $strrpos+1 : $strrpos;

		$downFileName = substr($getfilename, $strrpos, strlen($getfilename) - $strrpos );

		header("Content-type: application/{$file_extname}");
		header("Content-Disposition: attachment; filename={$downFileName}");
		header("Content-Length: ".filesize($getfilename));
		readfile($getfilename);

		include(ConfigFile(__CLASS__));

		if(is_array($WgetCountFiles) && in_array($downFileName,$WgetCountFiles)){
			$Counter = D('Counter');
			$Counter ->wgetCount($downFileName,1);
		}
	//	exit;

	}

	function upload(){

	}
}
?>
