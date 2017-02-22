<?php
// 前台 微信

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;
use Carbon\Carbon;

class Wechat extends Frontend
{
    private $Wechat;

    public function index()
    {
        $wechat = new \App\Library\Wechat();
        if (request()->isMethod('GET')) {
            $signature      = request('signature');
            $timestamp      = request('timestamp');
            $nonce          = request('nonce');
            $checkSignature = $wechat->checkSignature($signature, $timestamp, $nonce);
            if (!$checkSignature) {
                return "verify error!";
            }
            //是否是调试接口数据
            $echostr = request('echostr');
            if ($checkSignature && $echostr) {
                return $echostr;
            }
        } else {
            //验证成功之后如果有数据提交
            if ($GLOBALS["HTTP_RAW_POST_DATA"]) {
                $xmlStr  = $GLOBALS["HTTP_RAW_POST_DATA"];
                $nodeCfg = [
                    'ToUserName',
                    'FromUserName',
                    'CreateTime',
                    'MsgType',
                    'Content',
                    'MsgId',
                ];
                $msgInfo = $wechat->msg_decode($xmlStr, $nodeCfg);
                switch ($msgInfo['MsgType']) {
                    case 'text':
                        return $this->_index_text($msgInfo);
                        break;
                }
            }
        }
    }

    //主接口 自动回复文本消息
    private function _index_text($msgInfo)
    {
        $wechat = new \App\Library\Wechat();
        if (!$msgInfo) {
            return;
        }

        $data    = [
            'ToUserName'   => $msgInfo['FromUserName'],
            'FromUserName' => $msgInfo['ToUserName'],
            'CreateTime'   => Carbon::now(),
            'MsgType'      => 'text',
        ];
        switch ($msgInfo['Content']) {
            case '登录':
                $ApiLink    = route('Home::Wechat::member_bind');
                $Oauth2Link = $wechat->Oauth2_enlink($ApiLink);
                $content    = $Oauth2Link;
                break;
            case '时间':
                $content = trans('common.server') . trans('common.time') . ':' . date(config('system.sys_date_detail'));
                break;
            default:
                $content = '您发送的内容是：' . $msgInfo['Content'];
        }
        $data['Content'] = $content;
        return $wechat->msg_encode($data);
    }

    //网页授权获取用户基本信息 开发者中心页配置授权回调域名
    public function member_bind()
    {
        $wechat = new \App\Library\Wechat();
        if (request()->isMethod('POST')) {
            $memberName = request('user');
            $memberPwd  = request('pwd');
            $msg        = $this->doLogin($memberName, $memberPwd);
            if ($this->isLogin()) {
                $wechatInfo              = session('wechat_info');
                $wechatInfo['member_id'] = session('frontend_info.id');
                Model\Wechat::updateOrCreate(['openid' => $wechatInfo['openid']], $wechatInfo);
                session('wechat_info', null);
            }
            $this->_member_bind_msg($msg);
        } else {
            $code        = request($wechat->Oauth2_code);
            $accessToken = $wechat->Oauth2_access_token($code);
            //TODO access_token 复用
            $userInfo    = $wechat->Oauth2_user($accessToken['access_token'], $accessToken['openid']);
            $data        = [
                'openid'     => $userInfo['openid'],
                'nickname'   => $userInfo['nickname'],
                'sex'        => $userInfo['sex'],
                'language'   => $userInfo['language'],
                'country'    => $userInfo['country'],
                'province'   => $userInfo['province'],
                'city'       => $userInfo['city'],
                'headimgurl' => $userInfo['headimgurl'],
                'bind_time'  => Carbon::now(),
            ];
            //绑定模式逻辑节点
            //已经绑定 直接登陆
            //未绑定 登录绑定
            $wechatId = Model\Wechat::where('openid', $userInfo['openid'])->first()['id'];
            if ($wechatId) {
                $memberId = Model\Wechat::colWhere($wechatId)->first()['member_id'];
                $msg      = $this->doLogin(null, null, false, $memberId);
                $this->_member_bind_msg($msg);
            } else {
                session('wechat_info', $data);
                return view('home.Wechat_member_bind');
            }
        }
    }

    private function _member_bind_msg($msg)
    {
        switch ($msg) {
            case 'user_pwd_error':
                return $this->error(trans('common.account') . trans('common.or') . trans('common.pass') . trans('common.error'));
                break;
            case 'verify_error':
                return $this->error(trans('common.verify_code') . trans('common.error'));
                break;
            case 'lock_user_error':
                return $this->error(trans('common.admin') . trans('common.by') . trans('common.lock') . trans('common.please') . config('system.sys_frontend_lock_time') . trans('common.second') . trans('common.again') . trans('common.login'));
                break;
            default:
                return $this->success(trans('common.login') . trans('common.success'), route('Home::Member::index'));
        }
    }

// switch($_GET['a'])
    // {
    // case 'access_token':
    // $accessToken = $wechat->access_token();
    // break;
    // case 'msg_send':
    // $accessToken = $wechat->access_token();
    // $accessToken['access_token'] = 'b3yJwu5WrI26w7jF8-GYyJrwacZ17hNjgjlIpMdH71HUJToyXX_S58toIfGEbWqQy133YPo27SToCPLWmw5FuWMz3Hu8jylYOuXCuW_w2CE';
    // $data = array(
    // "touser"=>"oIckguFz2cIEQK6jU7LIPQwdxT7o",
    // "msgtype"=>"text",
    // "text"=>array(
    // "content"=>"Hello World",
    // ),
    // );
    // $wechat->msg_send($accessToken['access_token'],$data);
    // break;
    // }

}
