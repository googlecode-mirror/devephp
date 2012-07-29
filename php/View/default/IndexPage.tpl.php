<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">   
<html xmlns="http://www.w3.org/1999/xhtml">   
<head>   
<title>DevePHP: 分页操作</title>      
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />   
<load href="View/default/myPage.css" /> 
<load href="View/default/style.css" /> 
</head>   
<body>
<php>if(ACTION_NAME == 'Mypage'){</php>
<div class="main">
<h2>DevePHP示例之分页操作：普通分页和自定义样式分页</h2>
  本示例主要是展示一下分页的使用方法和基本原理。如果没有看到数据 请在表单处理或者CURD例子里面添加更多的数据。
 <div><A href="interface.php?m=Demo&c=Page&a=index">自定义分页</a>   <a href="interface.php?m=Demo&c=Page&a=Mypage">普通分页</a></div> 
 <table cellpadding=2 cellspacing=2>
  <volist name="list" id="vo">
  <tr>
  <td></td>
	<td style="border-bottom:1px dotted silver">{$vo.title} <span style="color:gray">[ {$vo.create_time|date='Y-m-d H:i:s',###}]</span></td>
  </tr>
  </volist>
 <tr>
 <tr>
 	<td></td><td><p class="sabrosus">{$page}</p></td>
 </tr>
 <td></td>
	<td><hr> 示例源码<br/>控制器IndexAction类<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoPage.class.php');</php><br/>模型FormModel类<br/><php>highlight_file(MODEL_PATH.'/FormModel.class.php');</php></td>
 </tr>
 </table>
   </form>
</div>
 <php>}else{</php>
<h2>DevePHP示例之分页操作：普通分页和自定义样式分页</h2>
 本示例主要是展示一下分页的使用方法和基本原理。如果没有看到数据 请在表单处理或者CURD例子里面添加更多的数据。
 <div><A href="interface.php?m=Demo&c=Page&a=index">自定义分页</a>   <a href="interface.php?m=Demo&c=Page&a=Mypage">普通分页</a></div> 

<div id="test" >
<table cellpadding=2 cellspacing=2>
	<volist name="list" id="vo">
	  <tr>
			 <td></td>
			<td style="border-bottom:1px dotted silver">{$vo.title} <span style="color:gray">[ {$vo.create_time|date='Y-m-d H:i:s',###}]</span></td>
	  </tr>
	</volist>
	 <tr>		
	 </tr>
 </table>
</div> <div class="green-black">{$page}</div>
<table>
<tr>
 <td></td>
	<td><hr> 示例源码<br/>控制器IndexAction类<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoPage.class.php');</php><br/>模型FormModel类<br/><php>highlight_file(MODEL_PATH.'/FormModel.class.php');</php></td>
 </tr>
 </table>
 <php>}</php>
</body>   
</html>
