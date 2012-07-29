<?php
class IndexAjax extends Control{
	// 首页
	public function main(){
	    
		$this->success(L('AjaxMsg',array('All Right!')));
	}
}
?>