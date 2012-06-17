<?php
class DemoHello extends Control{
    public function index() {
        $this->assign('hello','Hello,DevePHP');
        $this->display();
    }
} 

