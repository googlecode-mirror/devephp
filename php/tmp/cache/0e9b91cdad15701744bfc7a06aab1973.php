<?php if (!defined('DEVE_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>DevePHP示例：Hello,DevePHP</title>
<link rel='stylesheet' type='text/css' href='View/default/common.css'>
 </head>
 <body>
 <div class="main">
 <h2>DevePHP示例之Hello,DevePHP</h2>
最简单的示例。
 <table  cellpadding=3 cellspacing=3>
  <tr>
	<td><div class="result"><?php echo ($hello); ?></div></td>
 </tr>
 <tr>
	<td><hr> 示例源码<br/>控制器IndexAction类<br/><?php highlight_file(CONTROLLER_PATH.'/Index/IndexHello.class.php'); ?>
	</td>
 </tr>
 </table>
</div>
 </body>
</html>