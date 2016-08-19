<?php
return array(
    'control_group' => '成员管理',
    'control_info'  => '管理员',
    'tables'        => array(
        'admin'       => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'admin_name VARCHAR(64) NOT NULL  UNIQUE COMMENT "用户名"',
                'admin_pwd VARCHAR(32) NOT NULL COMMENT "密码"',
                'admin_rand VARCHAR(32) NOT NULL COMMENT "随机加密值"',
                'group_id MEDIUMTEXT DEFAULT NULL COMMENT "NULL不属于任何组"',
                'privilege MEDIUMTEXT DEFAULT NULL COMMENT "all 是全部权限"',
                'add_time INT(10) UNSIGNED NOT NULL COMMENT "添加时间"',
                'last_time INT(10) UNSIGNED DEFAULT 0 COMMENT "活跃时间"',
                'login_ip INT(10) UNSIGNED DEFAULT 0 COMMENT "活跃IP"',
                'login_num TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "尝试登录数"',
                'lock_time INT(10) UNSIGNED DEFAULT 0 COMMENT "登录锁定时间"',
                'is_enable TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "是否启用"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
            'insert_row' => array(
                '(admin_name,admin_pwd,admin_rand,group_id,privilege,add_time,is_enable) VALUES("root","cb300cf0d7943bb87225d9a105ee9636","0","|1|","all","0","1")',
                '(admin_name,admin_pwd,admin_rand,group_id,privilege,add_time,is_enable) VALUES("admin","b51975874e28ff9170dae62cafe98dfd","0","|2|","","0","1")',
            ),
        ),
        'admin_group' => array(
            'table_info' => '分组',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'manage_id MEDIUMTEXT DEFAULT NULL COMMENT "组管理员"',
                'name VARCHAR(64) NOT NULL  UNIQUE COMMENT "分组名称"',
                'explains VARCHAR(128) DEFAULT NULL COMMENT "分组说明"',
                'privilege MEDIUMTEXT DEFAULT NULL COMMENT "分组权限"',
                'is_enable TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "是否启用"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
            'insert_row' => array(
                '(manage_id,name,explains,privilege,is_enable) VALUES("|1|","root","顶级管理","all","1")',
                '(manage_id,name,explains,privilege,is_enable) VALUES("|1|", "webadmin","网站管理","","1")',
            ),
        ),
        'admin_log'   => array(
            'table_info' => '日志',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'admin_id INT(10) UNSIGNED NOT NULL COMMENT "管理员编号"',
                'add_time INT(10) UNSIGNED NOT NULL COMMENT "日志添加时间"',
                'module_name VARCHAR(32) NOT NULL COMMENT "操作分组"',
                'controller_name VARCHAR(32) NOT NULL COMMENT "操作控制器"',
                'action_name VARCHAR(32) NOT NULL COMMENT "操作方法"',
                'model_name VARCHAR(32) NOT NULL COMMENT "操作模型"',
                'request MEDIUMTEXT DEFAULT NULL COMMENT "参数"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'Admin'      => array('setting' => '管理员配置', 'index' => '管理员管理', 'add' => '添加管理员', 'del' => '删除管理员', 'edit' => '编辑管理员'),
            'AdminGroup' => array('index' => '管理组管理', 'add' => '添加管理组', 'del' => '删除管理组', 'edit' => '编辑管理组'),
            'AdminLog'   => array('index' => '查看管理日志', 'del' => '删除管理日志'),
        ),
    ),
);
