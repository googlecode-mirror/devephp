<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>DevePHP示例：缩略图生成</title>
<load href='View/default/common.css' />
 </head>
 <body>
 <div class="main">
 <h2>DevePHP示例之：缩略图生成</h2>
 本示例演示了如何使用内置的文件上传类库进行附件上传操作，并自动生成缩略图。
 <div id="main" class="main" > 
<div class="image">
 <volist name='list' id='vo'>
<table width="190" border="0" cellspacing="0" cellpadding="0">
 <tr>
    <td width="126" rowspan="2"><img src="./Data/Uploads/m_{$vo.image}" /></td>
    <td width="64">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="./Data/Uploads/s_{$vo.image}" /></td>
  </tr>
</table>  </volist>
</div>
<div class="content">
<form id="upload" method='post' action="interface.php?m=Demo&c=Upload&a=upload" enctype="multipart/form-data">
<table cellpadding=3 cellspacing=3 width="450PX">
<tr>
	<td colspan="2" class="tLeft">
	<div class="result" style="background:#E9E9F3">上传允许文件类型：gif png jpg 图像文件，分别生产2张缩略图。并且把原图删掉</div>
	</td>
</tr>
<tr>
	<td class="tRight tTop"></td>
	<td class="tLeft tTop">
<div class="impBtn  fLeft" ><input name="image" id="image" type="file" class="file huge" /></div>
	<table id='tbl' style="clear:both"></table>
  <input type="submit" value="提交" class="button" >
</td>
</tr>
<tr>
<td class="tRight tTop"></td>
	<td  class="tLeft"><hr> 示例源码<br/>控制器DemoUpload类<br/><php>highlight_file(CONTROLLER_PATH.'/Demo/DemoUpload.class.php');</php></td>
</tr>
</table>
</form>
</div>
</div>

</div>
 </body>
</html>