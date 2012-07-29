<?php
class IndexHello extends Control{
    public function main() {
        $this->assign('hello','Hello,DevePHP');
		echo 'aa';
        $this->display();
    }
} 

