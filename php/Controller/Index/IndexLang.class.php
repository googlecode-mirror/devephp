<?php
class IndexLang extends Control{

    function main(){
	    $title = L('{Lang.IndexLang.title}',array('DevePHP','1.1.0'));
        $time = L('{Lang.IndexLang.time}',array(date(L('{Lang.IndexLang.dateformat}'))));
        
		$this->assign('title',$title);
		$this->assign('content',$content);
		$this->assign('time',$time);
		$this->display();
	}

	function cross($a,$b){
	    $title = L('{Lang.IndexLang.title}',array('DevePHP','1.1.0'));
        $time = L('{Lang.IndexLang.time}',array(date(L('{Lang.IndexLang.dateformat}'))));
        echo $a,$b;
		echo $title."<br />";
		echo $content."<br />";
		echo $time."<br />";
	}

}