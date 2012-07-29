<?php
class IndexShow extends Control{
    public function main() {
		
        $this->assign('hello','Hello,DevePHP');
        $this->display();
    }
} 

