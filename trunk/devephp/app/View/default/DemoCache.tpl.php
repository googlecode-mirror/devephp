<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>DevePHPʾ�������ݻ���</title>
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
 <h2>DevePHPʾ��֮����̬���ݻ���</h2>
DevePHP���Է���ضԶ�̬���ݽ��л��棬��������Ч�ڡ�<br/>��������Ӱ����ݿ�Ĳ�ѯ������棬��Ч������Ϊ10�룬��<a href="__URL__">ˢ��ҳ��</a>�鿴SQL��¼�������Ƿ��б仯~<br/>
<div class="result" style="font-weight:normal">
��ѯ�����ݽ����<br/>
<volist name="list" id="vo">
[{$vo.id}]--{$vo.title}<br/>
</volist>
</div>
<div id="trace_info"></div>
 <table cellpadding=2 cellspacing=2>
 <tr>
 <td></td>
	<td>ʾ��Դ��<br/>������DemoCache��<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoCache.class.php');</php>
	��Ŀ����<br/><php>highlight_file(CONFIG_PATH.'/Config.php');</php></td>
 </tr>
 </table>
</div>
 </body>
</html>
