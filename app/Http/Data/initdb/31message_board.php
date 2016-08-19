<?php
return array(
    'control_group' => '扩展管理',
    'control_info'  => '留言板',
    'tables'        => array(
        'message_board'     => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'name VARCHAR(64) NOT NULL COMMENT "名称"',
                'template VARCHAR(64) DEFAULT NULL COMMENT "模板"',
                'config	MEDIUMTEXT DEFAULT NULL COMMENT "配置"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
            'insert_row' => array(
                '(name,config) VALUES("default",\'a:8:{s:6:"姓名";a:4:{s:8:"msg_name";s:6:"姓名";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:1;s:10:"msg_length";s:2:"20";}s:6:"性别";a:5:{s:10:"msg_option";a:3:{i:0;s:3:"男";i:1;s:3:"女";i:2;s:6:"未知";}s:8:"msg_name";s:6:"性别";s:8:"msg_type";s:5:"radio";s:12:"msg_required";b:0;s:10:"msg_length";s:2:"20";}s:6:"爱好";a:5:{s:10:"msg_option";a:3:{i:0;s:5:"test1";i:1;s:5:"test2";i:2;s:5:"test3";}s:8:"msg_name";s:6:"爱好";s:8:"msg_type";s:8:"checkbox";s:12:"msg_required";b:0;s:10:"msg_length";s:2:"20";}s:12:"联系电话";a:4:{s:8:"msg_name";s:12:"联系电话";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:1;s:10:"msg_length";s:2:"20";}s:12:"公司名称";a:4:{s:8:"msg_name";s:12:"公司名称";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:1;s:10:"msg_length";s:2:"20";}s:12:"公司地址";a:4:{s:8:"msg_name";s:12:"公司地址";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:1;s:10:"msg_length";s:2:"20";}s:6:"E-mail";a:4:{s:8:"msg_name";s:6:"E-mail";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:0;s:10:"msg_length";s:2:"60";}s:12:"网站需求";a:4:{s:8:"msg_name";s:12:"网站需求";s:8:"msg_type";s:8:"textarea";s:12:"msg_required";b:0;s:10:"msg_length";s:3:"560";}}\')',
            ),
        ),
        'message_board_log' => array(
            'table_info' => '日志',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'msg_id INT(10) UNSIGNED DEFAULT 0 COMMENT "留言板id"',
                'audit_id INT(10) UNSIGNED DEFAULT 0 COMMENT "审核id 0未审核"',
                'send_id INT(10) UNSIGNED DEFAULT 0 COMMENT "留言用户"',
                'add_time INT(10) UNSIGNED DEFAULT 0 COMMENT "添加时间"',
                'add_ip INT(10) UNSIGNED DEFAULT 0 COMMENT "发送的IP"',
                'send_info MEDIUMTEXT DEFAULT NULL COMMENT "发送信息"',
                'reply_info MEDIUMTEXT DEFAULT NULL COMMENT "回复信息"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'MessageBoard'    => array('index' => '留言板管理', 'add' => '添加留言板', 'del' => '删除留言板', 'edit' => '编辑留言板'),
            'MessageBoardLog' => array('index' => '留言管理', 'del' => '删除留言', 'edit' => '审核回复'),
        ),
        'Home'  => array(
            'MessageBoard' => array('index' => '留言板'),
        ),
    ),
);
