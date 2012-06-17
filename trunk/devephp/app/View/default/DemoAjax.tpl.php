<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>DevePHP示例：Ajax表单提交</title>
<load type="css" href='View/default/common.css' />
<load href="View/default/js/Base.js" />
<load href="View/default/js/prototype.js" />
<load href="View/default/js/mootools.js" />
<load href="View/default/js/ThinkAjax.js" />
</head>
<body><script language="JavaScript">
<!--
function add(){
	ThinkAjax.sendForm('form1','interface.php?m=Demo&c=Ajax&a=insert',complete,'result');
}

function complete(data,status){
	if (status==1)
	{
	// 更新列表
	$('list').innerHTML += 
	'<div class="result" style=\'font-weight:normal;background:#A6FF4D\'><div style="border-bottom:1px dotted silver">'+data.title+'  ['+data.email+data.create_time+']</div><div class="content">'+data.content+'</div></div>';
	}
}
function checkTitle(){
	ThinkAjax.send('interface.php?m=Demo&c=Ajax&a=checkTitle','ajax=1&title='+$('title').value,'','result');
}
//-->
</script>
<div class="main">
<h2>DevePHP示例之：Ajax表单提交</h2>
本示例同表单处理，只是改变提示方式为Ajax方式，采用了ThinkAjax类库实现。其他Ajax类库的实现方式类似~
<form id="form1" method='post' action="interface.php?m=Demo&c=Ajax&a=insert">
<table cellpadding=2 cellspacing=2>
<tr>
<td colspan="2"><div id="result" class="none result" style="font-family:微软雅黑,Tahoma;letter-spacing:2px"></div></td>
</tr>

<tr>
<td class="tRight" width="12%">标题：</td>
<td class="tLeft" ><input type="text" name="title" id="title" style="height:23px" class="large bLeft"> <input type="button" value="检 查" class="small button" onClick="checkTitle()"></td>
</tr>
<tr>
<td class="tRight" >邮箱：</td>
<td class="tLeft" ><input type="text" name="email" style="height:23px" class="huge bLeft"></td>
</tr>
<tr>
<td class="tRight tTop" >内容：</td>
<td><textarea name="content" class="huge bLeft" rows="8" cols="25"></textarea></td>
</tr>
<tr>
<td><input type="hidden" name="ajax" value="1"></td>
<td><input type="button" onClick="add()" class="button" value="提 交"> <input type="reset" class="button" value="清 空"></td>
</tr>

<tr>
<td></td>
<td><hr></td>
</tr>
<tr>
<td></td>
<td> <div id="list" >
<volist name="list" id="vo"><div class="result" style='font-weight:normal;<eq name="mod" value="1">background:#ECECFF</eq>'><div style="border-bottom:1px dotted silver">{$vo.title}  [{$vo.email} {$vo.create_time|date='Y-m-d H:i:s',###}]</div>
<div class="content">{$vo.content|nl2br}</div></div>
</volist></div></td>
</tr>
<tr>
<td></td>
<td><hr> 示例源码<br/>控制器DemoAjax类<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoAjax.class.php');</php><br/>模型FormModel类<br/><php>highlight_file(MODEL_PATH.'/FormModel.class.php');</php></td>
</tr>
</table>
</form>
</div>
</body>
</html>
