<?php
if (isset($_GET['openid']) && isset($_GET['openkey'])) {
	header('Location: php/interface.php?m=User&c=Login&a=login&Time='.time().'&'.trim($_SERVER['QUERY_STRING'],'?'));
die;
}

