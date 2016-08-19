<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
// system config file
return array(
    'SYS_DATE'                 => 'Y-m-d',
    'SYS_DATE_DETAIL'          => 'Y-m-d H:i:s',
    'SYS_FRONTEND_VERIFY'      => '1',
    'SYS_BACKEND_TIMEOUT'      => '86400',
    'SYS_FRONTEND_TIMEOUT'     => '86400',
    'SYS_DENY_LOG_REQUEST'     => array(
        0 => 'password',
        1 => 'password_again',
        2 => 'content',
        3 => 'start_content',
        4 => 'end_content',
        5 => '__hash__',
        6 => 'cur_password',
    ),
    'SYS_MEMBER_ENABLE'        => '1',
    'SYS_MEMBER_AUTO_ENABLE'   => '1',
    'SYS_MAX_ROW'              => '10',
    'SYS_BACKEND_LOGIN_NUM'    => '10',
    'SYS_FRONTEND_LOGIN_NUM'   => '10',
    'SYS_BACKEND_LOCK_TIME'    => '600',
    'SYS_FRONTEND_LOCK_TIME'   => '600',
    'SYS_ARTICLE_THUMB_WIDTH'  => '195',
    'SYS_ARTICLE_THUMB_HEIGHT' => '120',
    'SYS_TD_CACHE'             => '60',
    'DATA_CACHE_TIME'          => '60',
    'SYS_MAX_PAGE'             => '1000',
    'SYS_ADMIN_AUTO_LOG'       => '1',
    'SYS_BACKEND_VERIFY'       => '1',
    'SYS_ARTICLE_SYNC_IMAGE'   => '1',
    'COMMENT_SWITCH'           => '1',
    'COMMENT_ALLOW'            => '1',
    'COMMENT_ANONY'            => '1',
    'COMMENT_INTERVAL'         => '1',
    'WECHAT_ID'                => 'asdf',
    'WECHAT_SECRET'            => 'asdf',
    'WECHAT_TOKEN'             => 'asdf',
    'WECHAT_RECORD_LOG'        => '1',
    'WECHAT_AESKEY'            => 'asdf',
    'WECHAT_TEMPLATE_ID'       => 'asdf',
    'SYS_DEFAULT_IMAGE'        => 'Public/css/fimages/default.png',
    'SYS_SYNC_IMAGE'           => 'Public/css/bimages/loading.gif',
    'SYS_ARTICLE_PN_LIMIT'     => '1',
);
