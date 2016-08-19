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
// 前台 微信

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Wechat extends Frontend
{
    private $Wechat;

    //重写构造方法 绕过令牌检测
    public function _initialize()
    {
        C('TOKEN_ON', false);
        $this->Wechat = new \Common\Lib\Wechat();
        parent::_initialize();
    }
    public function index()
    {
        $signature      = I('get.signature');
        $timestamp      = I('get.timestamp');
        $nonce          = I('get.nonce');
        $checkSignature = $this->Wechat->checkSignature($signature, $timestamp, $nonce);
        if (!$checkSignature) {
            echo "verify error!";
            return;
        }
        //是否是调试接口数据
        $echostr = I('echostr');
        if ($checkSignature && $echostr) {
            echo $echostr;
            return;
        }
        //验证成功之后如果有数据提交
        if ($GLOBALS["HTTP_RAW_POST_DATA"]) {
            $xml_str  = $GLOBALS["HTTP_RAW_POST_DATA"];
            $node_cfg = array(
                'ToUserName',
                'FromUserName',
                'CreateTime',
                'MsgType',
                'Content',
                'MsgId',
            );
            $msg_info = $this->Wechat->msg_decode($xml_str, $node_cfg);
            switch ($msg_info['MsgType']) {
                case 'text':
                    $this->_index_text($msg_info);
                    break;
            }
        }
    }

    //主接口 自动回复文本消息
    private function _index_text($msg_info)
    {
        if (!$msg_info) {
            return;
        }

        $data = array(
            'ToUserName'   => $msg_info['FromUserName'],
            'FromUserName' => $msg_info['ToUserName'],
            'CreateTime'   => time(),
            'MsgType'      => 'text',
        );
        $content = '';
        switch ($msg_info['Content']) {
            case '登录':
                $Api_link    = 'http://' . $_SERVER['SERVER_NAME'] . U(C('DEFAULT_MODULE') . '/Wechat/member_bind');
                $Oauth2_link = $this->Wechat->Oauth2_enlink($Api_link);
                $content     = $Oauth2_link;
                break;
            case '时间':
                $content = L('server') . L('time') . ':' . date(C('SYS_DATE_DETAIL'));
                break;
            default:
                $content = '您发送的内容是：' . $msg_info['Content'];
        }
        $data['Content'] = $content;
        echo $this->Wechat->msg_encode($data);
    }

    //网页授权获取用户基本信息 开发者中心页配置授权回调域名
    public function member_bind()
    {
        if (IS_POST) {
            $member_name = I('user');
            $member_pwd  = I('pwd');
            $msg         = $this->_login($member_name, $member_pwd);
            if ($this->_is_login()) {
                $WechatModel              = D('Wechat');
                $wechat_info              = session('wechat_info');
                $wechat_info['member_id'] = session('frontend_info.id');
                $WechatModel->bind_wechat($wechat_info);
                session('wechat_info', null);
            }
            $this->_member_bind_msg($msg);
        } else {
            $code         = I($this->Wechat->Oauth2_code);
            $access_token = $this->Wechat->Oauth2_access_token($code);
            $user_info    = $this->Wechat->Oauth2_user($access_token['access_token'], $access_token['openid']);
            $data         = array(
                'openid'     => $user_info['openid'],
                'nickname'   => $user_info['nickname'],
                'sex'        => $user_info['sex'],
                'language'   => $user_info['language'],
                'country'    => $user_info['country'],
                'province'   => $user_info['province'],
                'city'       => $user_info['city'],
                'headimgurl' => $user_info['headimgurl'],
                'bind_time'  => time(),
            );
            //绑定模式逻辑节点
            //已经绑定 直接登陆
            //未绑定 登录绑定
            $WechatModel = D('Wechat');
            $wechat_id   = $WechatModel->m_find_id($user_info['openid']);
            if ($wechat_id) {
                $member_id = $WechatModel->m_find_column($wechat_id, 'member_id');
                $msg       = $this->_login(null, null, false, $member_id);
                $this->_member_bind_msg($msg);
            } else {
                session('wechat_info', $data);
                $this->display();
            }
        }
    }

    private function _member_bind_msg($msg)
    {
        switch ($msg) {
            case 'user_pwd_error':
                $this->error(L('account') . L('or') . L('pass') . L('error'), U(ACTION_NAME));
                break;
            case 'verify_error':
                $this->error(L('verify_code') . L('error'), U(ACTION_NAME));
                break;
            case 'lock_user_error':
                $this->error(L('admin') . L('by') . L('lock') . L('please') . C('SYS_FRONTEND_LOCK_TIME') . L('second') . L('again') . L('login'), U(ACTION_NAME));
                break;
            default:
                $this->success(L('login') . L('success'), U('Member/index'));
        }
    }

// switch($_GET['a'])
    // {
    // case 'access_token':
    // $access_token = $this->Wechat->access_token();
    // break;
    // case 'msg_send':
    // $access_token = $this->Wechat->access_token();
    // $access_token['access_token'] = 'b3yJwu5WrI26w7jF8-GYyJrwacZ17hNjgjlIpMdH71HUJToyXX_S58toIfGEbWqQy133YPo27SToCPLWmw5FuWMz3Hu8jylYOuXCuW_w2CE';
    // $data = array(
    // "touser"=>"oIckguFz2cIEQK6jU7LIPQwdxT7o",
    // "msgtype"=>"text",
    // "text"=>array(
    // "content"=>"Hello World",
    // ),
    // );
    // $this->Wechat->msg_send($access_token['access_token'],$data);
    // break;
    // }

}
