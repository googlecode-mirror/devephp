<?php
class IndexLang extends Control{

    function main(){
	    echo "<p>".L('{Lang.IndexLang.title}',array('DevePHP','1.1.0'))."</p>";
        echo "<p>".L('{Lang.IndexLang.content}')."</p>";
        echo "<p>".L('{Lang.IndexLang.time}',array(date('Y-m-d H:i:s',time())))."</p>";
	}

}