<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>DevePHP示例：静态页面生成</title>
<link rel='stylesheet' type='text/css' href='View/default/common.css'>
 </head>
 <body>
 <div class="main">
 <h2>DevePHP示例之：静态页面生成</h2>
设置页面生成静态和有效期<br/>
 <table  cellpadding=3 cellspacing=3>
  <tr>
	<td><div class="result" style="color:red">当前时间：{$id|time|date='H:i:s',###}</div></td>
 </tr>
 <tr>
	<td class="tLeft" ><a href="javascript:location.reload()">刷新页面</a> 后注意看时间是否有变化</td>
 </tr>
 <tr>
	<td><hr> 示例源码<br/>配置文件config.php<br/><php>highlight_file(CONFIG_PATH.'/Config.php');</php><br/>
	静态定义文件htmls.php <br/><php>highlight_file(CONFIG_PATH.'/Htmls.php');</php><br/>
	控制器DemoHtml类<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoHtml.class.php');</php>
	</td>
 </tr>
 </table>
</div>
 </body>
</html>
