<?

require_once ('email.class.php');
//##########################################
$smtpserver = "smtp.gmail.com";//SMTP������
$smtpserverport = 587;//SMTP�������˿�
$smtpusermail = "yearnfar@gmail.com";//SMTP���������û�����
$smtpemailto = "812913501@qq.com";//���͸�˭
$smtpuser = "yearnfar";//SMTP���������û��ʺ�
$smtppass = "huaping_1989";//SMTP���������û�����
$mailsubject = "DevePHP�����ʼ�ϵͳ";//�ʼ�����
$mailbody = "<h1> ����һ�����Գ��� devephp.com </h1>";//�ʼ�����
$mailtype = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
##########################################
$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
$smtp->debug = FALSE;//�Ƿ���ʾ���͵ĵ�����Ϣ
$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);

?>
