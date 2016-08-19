<?php
return array(
    'control_group' => '扩展管理',
    'control_info'  => '站内信',
    'tables'        => array(
        'message' => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'send_id INT(10) UNSIGNED DEFAULT 0 COMMENT "0为系统发送"',
                'receive_id INT(10) UNSIGNED DEFAULT 0 COMMENT "0为系统接收"',
                'send_time INT(10) UNSIGNED DEFAULT 0 COMMENT "发送时间"',
                'receive_time INT(10) UNSIGNED DEFAULT 0 COMMENT "查看时间"',
                'content MEDIUMTEXT NOT NULL COMMENT "发送的内容"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'Message' => array('index' => '站内信管理', 'add' => '发送站内信', 'del' => '删除站内信'),
        ),
        'Home'  => array(
            'Message' => array('index' => '站内信管理', 'add' => '发送站内信', 'del' => '删除站内信'),
        ),
    ),
);
