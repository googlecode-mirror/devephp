<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>DevePHP示例：数据查询</title>
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
 <h2>DevePHP示例之：数据查询</h2>
  数据查询包括普通查询、组合查询、统计查询、定位查询、动态查询和SQL查询。<br/>
<div class="result" style="font-weight:normal">
普通列表查询结果：<br/>
<volist name="list" id="v">
   {$v.id}--{$v.title}<br/>
</volist>
<hr>
带条件查询结果：<br/>
{$vo.title}
<hr>
组合查询结果：<br/>
<volist name="list2" id="v">
{$v.title}<br/>
</volist>
<hr>
动态查询结果：<br/>
{$vo2.title}
</div>
<div id="trace_info"></div>
 <table cellpadding=2 cellspacing=2>
 <tr>
 <td></td>
	<td>示例源码<br/>控制器DemoQuery类<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoQuery.class.php');</php></td>
 </tr>
 </table>
</div>
 </body>
</html>
