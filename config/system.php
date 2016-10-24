<?php
return [
    'sys_date'                 => 'Y-m-d',
    'sys_date_detail'          => 'Y-m-d H:i:s',
    'sys_frontend_verify'      => '1',
    'sys_backend_timeout'      => '86400',
    'sys_frontend_timeout'     => '86400',
    'sys_deny_log_request'     =>
        [
            0 => 'password',
            1 => 'password_again',
            2 => 'content',
            3 => 'start_content',
            4 => 'end_content',
            5 => '__hash__',
            6 => 'cur_password',
        ],
    'sys_member_enable'        => '1',
    'sys_member_auto_enable'   => '1',
    'sys_max_row'              => '10',
    'sys_backend_login_num'    => '10',
    'sys_frontend_login_num'   => '10',
    'sys_backend_lock_time'    => '600',
    'sys_frontend_lock_time'   => '600',
    'sys_article_thumb_width'  => '195',
    'sys_article_thumb_height' => '120',
    'sys_td_cache'             => '60',
    'data_cache_time'          => '60',
    'sys_max_page'             => '1000',
    'sys_admin_auto_log'       => '1',
    'sys_backend_verify'       => '1',
    'sys_article_sync_image'   => '1',
    'comment_switch'           => '1',
    'comment_allow'            => '1',
    'comment_anony'            => '1',
    'comment_interval'         => '1',
    'wechat_id'                => 'asdf',
    'wechat_secret'            => 'asdf',
    'wechat_token'             => 'asdf',
    'wechat_record_log'        => '1',
    'wechat_aeskey'            => 'asdf',
    'wechat_template_id'       => 'asdf',
    'sys_default_image'        => 'css/fimages/default.png',
    'sys_sync_image'           => 'css/bimages/loading.gif',
    'sys_article_pn_limit'     => '1',
    'minify_cache_time'        => '31536000',
    'default_theme'            => '',
];
?>