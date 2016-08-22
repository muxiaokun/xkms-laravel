<?php
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
            $xmlStr  = $GLOBALS["HTTP_RAW_POST_DATA"];
            $nodeCfg = array(
                'ToUserName',
                'FromUserName',
                'CreateTime',
                'MsgType',
                'Content',
                'MsgId',
            );
            $msgInfo = $this->Wechat->msg_decode($xmlStr, $nodeCfg);
            switch ($msgInfo['MsgType']) {
                case 'text':
                    $this->_index_text($msgInfo);
                    break;
            }
        }
    }

    //主接口 自动回复文本消息
    private function _index_text($msgInfo)
    {
        if (!$msgInfo) {
            return;
        }

        $data = array(
            'ToUserName'   => $msgInfo['FromUserName'],
            'FromUserName' => $msgInfo['ToUserName'],
            'CreateTime'   => time(),
            'MsgType'      => 'text',
        );
        $content = '';
        switch ($msgInfo['Content']) {
            case '登录':
                $ApiLink    = 'http://' . $_SERVER['SERVER_NAME'] . U(C('DEFAULT_MODULE') . '/Wechat/member_bind');
                $Oauth2Link = $this->Wechat->Oauth2_enlink($ApiLink);
                $content     = $Oauth2Link;
                break;
            case '时间':
                $content = L('server') . L('time') . ':' . date(C('SYS_DATE_DETAIL'));
                break;
            default:
                $content = '您发送的内容是：' . $msgInfo['Content'];
        }
        $data['Content'] = $content;
        echo $this->Wechat->msg_encode($data);
    }

    //网页授权获取用户基本信息 开发者中心页配置授权回调域名
    public function member_bind()
    {
        if (IS_POST) {
            $memberName = I('user');
            $memberPwd  = I('pwd');
            $msg         = $this->doLogin($memberName, $memberPwd);
            if ($this->isLogin()) {
                $WechatModel              = D('Wechat');
                $wechatInfo              = session('wechat_info');
                $wechatInfo['member_id'] = session('frontend_info.id');
                $WechatModel->bind_wechat($wechatInfo);
                session('wechat_info', null);
            }
            $this->_member_bind_msg($msg);
        } else {
            $code         = I($this->Wechat->Oauth2_code);
            $accessToken = $this->Wechat->Oauth2_access_token($code);
            $userInfo    = $this->Wechat->Oauth2_user($accessToken['access_token'], $accessToken['openid']);
            $data         = array(
                'openid'     => $userInfo['openid'],
                'nickname'   => $userInfo['nickname'],
                'sex'        => $userInfo['sex'],
                'language'   => $userInfo['language'],
                'country'    => $userInfo['country'],
                'province'   => $userInfo['province'],
                'city'       => $userInfo['city'],
                'headimgurl' => $userInfo['headimgurl'],
                'bind_time'  => time(),
            );
            //绑定模式逻辑节点
            //已经绑定 直接登陆
            //未绑定 登录绑定
            $WechatModel = D('Wechat');
            $wechatId   = $WechatModel->mFindId($userInfo['openid']);
            if ($wechatId) {
                $memberId = $WechatModel->mFindColumn($wechatId, 'member_id');
                $msg       = $this->doLogin(null, null, false, $memberId);
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
    // $accessToken = $this->Wechat->access_token();
    // break;
    // case 'msg_send':
    // $accessToken = $this->Wechat->access_token();
    // $accessToken['access_token'] = 'b3yJwu5WrI26w7jF8-GYyJrwacZ17hNjgjlIpMdH71HUJToyXX_S58toIfGEbWqQy133YPo27SToCPLWmw5FuWMz3Hu8jylYOuXCuW_w2CE';
    // $data = array(
    // "touser"=>"oIckguFz2cIEQK6jU7LIPQwdxT7o",
    // "msgtype"=>"text",
    // "text"=>array(
    // "content"=>"Hello World",
    // ),
    // );
    // $this->Wechat->msg_send($accessToken['access_token'],$data);
    // break;
    // }

}
