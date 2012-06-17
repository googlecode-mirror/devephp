<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>DevePHP示例：页面Trace</title>
<link rel='stylesheet' type='text/css' href='View/default/common.css'>
 </head>
 <script language="JavaScript">
 <!--
	function showTrace(){
		document.getElementById('trace_info').innerHTML = document.getElementById('think_page_trace').innerHTML;
		document.getElementById('think_page_trace').innerHTML = '';
	}
 //-->
 </script>
 <body onload="showTrace()">
 <div class="main">
 <h2>DevePHP示例之：页面Trace</h2>
调试模式会自动开启页面Trace信息显示，也可以单独设置显示页面Trace。显示效果如下所示~<br/>
页面Trace的模板文件位于：<span style="color:#FF6600"><php>echo VIEW_PATH.'/PageTrace.tpl.php'</php></span>。
<div id="trace_info"></div>
 <table cellpadding=2 cellspacing=2>
 <tr>
 <td></td>
	<td>示例源码<br/>控制器DemoTrace类<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoTrace.class.php');</php></td>
 </tr>
 </table>
</div>
 </body>
</html>
