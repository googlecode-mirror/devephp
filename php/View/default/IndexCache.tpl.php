<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>DevePHP示例：数据缓存</title>
<load href='View/default/common.css' />
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
 <h2>DevePHP示例之：动态数据缓存</h2>
DevePHP可以方便地对动态数据进行缓存，并设置有效期。<br/>下面的例子把数据库的查询结果缓存，有效期设置为10秒，请<a href="__URL__">刷新页面</a>查看SQL记录和数据是否有变化~<br/>
<div class="result" style="font-weight:normal">
查询的数据结果：<br/>
<volist name="list" id="vo">
[{$vo.id}]--{$vo.title}<br/>
</volist>
</div>
<div id="trace_info"></div>
 <table cellpadding=2 cellspacing=2>
 <tr>
 <td></td>
	<td>示例源码<br/>控制器DemoCache类<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoCache.class.php');</php>
	项目配置<br/><php>highlight_file(CONFIG_PATH.'/Config.php');</php></td>
 </tr>
 </table>
</div>
 </body>
</html>
