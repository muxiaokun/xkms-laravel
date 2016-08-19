<?php
return array(
    'control_group' => '扩展管理',
    'control_info'  => '评论',
    'tables'        => array(
        'comment' => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'audit_id INT(10) UNSIGNED DEFAULT 0 COMMENT "审核id 0未审核"',
                'send_id INT(10) UNSIGNED DEFAULT 0 COMMENT "0为游客评论"',
                'add_time INT(10) UNSIGNED DEFAULT 0 COMMENT "评论时间"',
                'add_ip INT(10) UNSIGNED DEFAULT 0 COMMENT "评论的IP"',
                'controller VARCHAR(32) DEFAULT NULL COMMENT "上传控制器"',
                'item INT(10) UNSIGNED DEFAULT 0 COMMENT "属于分组0属于游离"',
                'level TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "评论级别"',
                'content VARCHAR(256) DEFAULT NULL COMMENT "评论内容"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'Comment' => array('index' => '评论管理', 'add' => '审核评论', 'del' => '删除评论', 'edit' => '回复评论'),
        ),
    ),
);
