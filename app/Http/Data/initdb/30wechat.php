<?php
return array(
    'control_group' => '扩展管理',
    'control_info'  => '微信',
    'tables'        => array(
        'wechat' => array(
            'table_info' => '主表',
            'column'     => array(
                'id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT "编号"',
                'openid  VARCHAR(64) DEFAULT NULL COMMENT "公众账号唯一id"',
                'member_id INT(10) UNSIGNED NOT NULL UNIQUE COMMENT "系统会员编号"',
                'unionid  VARCHAR(64) DEFAULT NULL COMMENT "全局唯一id"',
                'nickname  VARCHAR(64) DEFAULT NULL COMMENT "昵称"',
                'sex  TINYINT(3) UNSIGNED DEFAULT 0 COMMENT "性别"',
                'country  VARCHAR(64) DEFAULT NULL COMMENT "国家"',
                'province  VARCHAR(64) DEFAULT NULL COMMENT "省市"',
                'city  VARCHAR(64) DEFAULT NULL COMMENT "城市"',
                'language  VARCHAR(64) DEFAULT NULL COMMENT "语言"',
                'headimgurl  VARCHAR(256) DEFAULT NULL COMMENT "头像46、64、96、132 "',
                'bind_time  INT(10) UNSIGNED DEFAULT 0 COMMENT "绑定时间"',
            ),
            'attribute'  => array(
                'DEFAULT CHARACTER SET' => 'utf8',
                'ENGINE'                => 'MyISAM',
            ),
        ),
    ),
    'privilege'     => array(
        'Admin' => array(
            'Wechat' => array('index' => '微信管理', 'add' => '配置微信', 'del' => '删除微信绑定', 'edit' => '推送消息'),
        ),
    ),
);
