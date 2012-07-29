<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>DevePHP示例：CURD操作</title>
<load type="css" href='View/default/common.css' />
<load href="View/default/js/Base.js" />
<load href="View/default/js/prototype.js" />
<load href="View/default/js/mootools.js" />
<load href="View/default/js/ThinkAjax.js" />
 </head>
 <body><script language="JavaScript">
 <!--
 
        function add(){
		window.location.href="interface.php?m=Demo&c=Curd&a=add";
	}
	function edit(id){
		window.location.href="interface.php?m=Demo&c=Curd&a=edit&id="+id;
	}
        function add2(){
		ThinkAjax.sendForm('form1','interface.php?m=Demo&c=Curd&a=insert',complete,'result');
	}
	
	function del(id){
		ThinkAjax.send('interface.php?m=Demo&c=Curd&a=delete','ajax=1&id='+id,complete,'result');
	}
	function complete(data,status){
		if (status==1)
		{
			$('div_'+data).outerHTML = '';
		}
	}
        function save(){
		ThinkAjax.sendForm('form1','interface.php?m=Demo&c=Curd&a=update',complete,'result');
	}
	
        
 //-->
 </script>
<php>if(ACTION_NAME == 'index'){</php>
<div class="main">
 <h2>DevePHP示例之：CURD操作</h2>
方便地完成对单表的CURD操作<P>

 <table cellpadding=2 cellspacing=2>
  <tr>
	<td colspan="2"><input type="button" value="新 增" class="small button" onClick="add()"><div id="result" class="none result" style="font-family:微软雅黑,Tahoma;letter-spacing:2px"></div></td>
 </tr>
  <tr>
  <td></td>
	<td> <div id="list" >
	<volist name="list" id="vo"><div id="div_{$vo.id}" class="result" style='font-weight:normal;<eq name="odd" value="1">background:#ECECFF</eq>'><div style="border-bottom:1px dotted silver">{$vo.title}  [{$vo.email} {$vo.create_time|date='Y-m-d H:i:s',###}] <br/><input type="button" value="编辑" class="small button" onClick="edit({$vo.id})"> <input type="button" value="删除" class="small button" onClick="del({$vo.id})"> </div>
	<div class="content">{$vo.content|nl2br}</div></div>
	</volist></div></td>
  </tr>
 <tr>
 <td></td>
	<td><hr> 示例源码<br/>控制器IndexAction类<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoCurd.class.php');</php><br/>模型FormModel类<br/><php>highlight_file(MODEL_PATH.'/FormModel.class.php');</php></td>
 </tr>
 </table>
</div>
<php>}elseif(ACTION_NAME == 'add'){</php>
 <div class="main">
 <h2>DevePHP示例之：CURD操作 [ 新增数据表单 ] </h2>
    <form id="form1" method='post' action=interface.php?m=Demo&c=Curd&a=insert">
 <table cellpadding=2 cellspacing=2>
  <tr>
	<td colspan="2"><div id="result" class="none result" style="font-family:微软雅黑,Tahoma;letter-spacing:2px"></div></td>
 </tr>
 <tr>
	<td class="tRight" width="12%">标题：</td>
	<td class="tLeft" ><input type="text" name="title" style="height:23px" class="huge bLeft"> </td>
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
	<td><input type="button" onClick="add2()" class="button" value="保 存"> <input type="reset" class="button" value="清 空"></td>
 </tr>
 </table>
   </form>
</div>
<php>}elseif(ACTION_NAME == 'edit'){</php>
 <div class="main">
 <h2>DevePHP示例之：CURD操作</h2>
 编辑数据表单
   <form id="form1" method='post' action="">
 <table cellpadding=2 cellspacing=2>
  <tr>
	<td colspan="2"><div id="result" class="none result" style="font-family:微软雅黑,Tahoma;letter-spacing:2px"></div></td>
 </tr>

 <tr>
	<td class="tRight" width="12%">标题：</td>
	<td class="tLeft" ><input type="text" name="title" style="height:23px" class="large bLeft" value="{$vo.title}"></td>
 </tr>
  <tr>
	<td class="tRight" >邮箱：</td>
	<td class="tLeft" ><input type="text" name="email" style="height:23px" class="huge bLeft" value="{$vo.email}"></td>
 </tr>
 <tr>
	<td class="tRight tTop" >内容：</td>
	<td><textarea name="content" class="huge bLeft" rows="8" cols="25">{$vo.content}</textarea></td>
 </tr>
 <tr>
	<td><input type="hidden" name="ajax" value="1"><input type="hidden" name="id" value="{$vo.id}"></td>
	<td><input type="button" onClick="save()" class="button" value="保 存"> <input type="reset" class="button" value="清 空"></td>
 </tr>
 </table>
   </form>
</div>


<php>}</php>
 </body>
</html>
