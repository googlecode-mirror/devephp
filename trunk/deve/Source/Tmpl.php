<?php
return array(
    'TMPL_ENGINE_TYPE'		=> 'Think',     // Ĭ��ģ������ �������ý���ʹ��Thinkģ��������Ч
	'TMPL_DETECT_THEME'     => false,       // �Զ����ģ������
	'TMPL_TEMPLATE_SUFFIX'  => '.tpl.php',     // Ĭ��ģ���ļ���׺
	'TMPL_CACHFILE_SUFFIX'  => '.php',      // Ĭ��ģ�建���׺
	'TMPL_DENY_FUNC_LIST'	=> 'echo,exit',	// ģ��������ú���
	'TMPL_PARSE_STRING'     => '',          // ģ������Ҫ�Զ��滻���ַ�����������������ʽ��
	'TMPL_L_DELIM'          => '{',			// ģ��������ͨ��ǩ��ʼ���
	'TMPL_R_DELIM'          => '}',			// ģ��������ͨ��ǩ�������
	'TMPL_VAR_IDENTIFY'     => 'array',     // ģ�����ʶ�������Զ��ж�,����Ϊ'obj'���ʾ����
	'TMPL_STRIP_SPACE'      => false,       // �Ƿ�ȥ��ģ���ļ������html�ո��뻻��
	'TMPL_CACHE_ON'			=> true,        // �Ƿ���ģ����뻺��,��Ϊfalse��ÿ�ζ������±���
	'TMPL_CACHE_TIME'		=>	-1,         // ģ�建����Ч�� -1 Ϊ���ã�(������Ϊֵ����λ:��)
	'TMPL_ACTION_ERROR'     => 'Public.success', // Ĭ�ϴ�����ת��Ӧ��ģ���ļ�
	'TMPL_ACTION_SUCCESS'   => 'Public.success', // Ĭ�ϳɹ���ת��Ӧ��ģ���ļ�
	'TMPL_TRACE_FILE'       => VIEW_PATH.'/PageTrace.tpl.php',     // ҳ��Trace��ģ���ļ�
	'TMPL_EXCEPTION_FILE'   => VIEW_PATH.'/DeveException.tpl.php',// �쳣ҳ���ģ���ļ�
	'TMPL_FILE_DEPR'=>'/', //ģ���ļ�MODULE_NAME��ACTION_NAME֮��ķָ����ֻ����Ŀ���鲿����Ч
	'TMPL_DEFAULT_THEME' => 'default',
	'TMPL_CHARSET' => 'utf-8',
	'TMPL_PARSE_STRING'=>'',
);