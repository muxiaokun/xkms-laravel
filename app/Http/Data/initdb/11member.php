<?php
return array(
    'control_group' => '成员管理',
    'control_info'  => '会员管理',
    'tables'        => array(
        'member'       => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'member_name VARCHAR(64) NOT NULL UNIQUE COMMENT "用户名"',
                'member_pwd VARCHAR(32) NOT NULL COMMENT "密码"',
                'member_rand VARCHAR(32) NOT NULL COMMENT "随机加密值"',
                'group_id MEDIUMTEXT DEFAULT NULL COMMENT "NULL不属于任何组"',
                'register_time INT(10) UNSIGNED NOT NULL COMMENT "注册时间"',
                'last_time INT(10) UNSIGNED DEFAULT 0 COMMENT "活跃时间"',
                'login_ip INT(10) UNSIGNED DEFAULT 0 COMMENT "活跃IP"',
                'login_num TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "尝试登录数"',
                'lock_time INT(10) UNSIGNED DEFAULT 0 COMMENT "登录锁定时间"',
                'is_enable TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "是否启用"',
                'email VARCHAR(64) DEFAULT NULL COMMENT "电邮"',
                'phone VARCHAR(64) DEFAULT NULL COMMENT "手机"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
        'member_group' => array(
            'table_info' => '分组',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'manage_id MEDIUMTEXT DEFAULT NULL COMMENT "组管理员"',
                'name VARCHAR(64) NOT NULL  UNIQUE COMMENT "分组名称"',
                'explains VARCHAR(128) DEFAULT NULL COMMENT "分组说明"',
                'privilege MEDIUMTEXT DEFAULT NULL COMMENT "分组权限"',
                'is_enable TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "是否启用"',
            ),
            'insert_row' => array(
                '(name,explains,privilege,is_enable) VALUES("会员默认分组","会员默认分组","all","1")',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'Member'      => array('setting' => '会员配置', 'index' => '会员管理', 'add' => '添加会员', 'del' => '删除会员', 'edit' => '编辑会员'),
            'MemberGroup' => array('index' => '会员组管理', 'add' => '添加会员组', 'del' => '删除会员组', 'edit' => '编辑会员组'),
        ),
        'Home'  => array(
            'Member' => array('index' => '会员中心', 'edit' => '编辑会员'),
        ),
    ),
);
