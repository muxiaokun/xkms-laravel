<?php
// 前台 微信

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Wechat extends Frontend
{
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
        if (!$msgInfo) {
            return;
        }

        $wechat             = new \App\Library\Wechat();
        $data               = [
            'ToUserName'   => $msgInfo['FromUserName'],
            'FromUserName' => $msgInfo['ToUserName'],
            'CreateTime'   => Carbon::now(),
            'MsgType'      => 'text',
        ];
        switch ($msgInfo['Content']) {
            case trans('common.login'):
                $ApiLink    = route('Home::Wechat::member_bind');
                $Oauth2Link = $wechat->Oauth2_enlink($ApiLink, 'snsapi_userinfo');
                $content    = $Oauth2Link;
                break;
            case trans('common.time'):
                $content = trans('common.server') . trans('common.time') . ':' . Carbon::now();
                break;
            default:
                $content = trans('common.welcome') . trans('common.use') . trans('common.app_name');
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
                $code        = request($wechat->Oauth2_code);
                $cacheName   = $code;
                $cacheValue  = Cache::get($cacheName);
                $accessToken = $cacheValue;
                $userInfo    = $wechat->Oauth2_user($accessToken['access_token'], $accessToken['openid']);
                $wechatInfo  = [
                    'openid'     => $userInfo['openid'],
                    'unionid'    => isset($userInfo['unionid']) ? $userInfo['unionid'] : '',
                    'nickname'   => $userInfo['nickname'],
                    'sex'        => $userInfo['sex'],
                    'language'   => $userInfo['language'],
                    'country'    => $userInfo['country'],
                    'province'   => $userInfo['province'],
                    'city'       => $userInfo['city'],
                    'headimgurl' => $userInfo['headimgurl'],
                    'bind_time'  => Carbon::now(),
                    'member_id'  => session('frontend_info.id'),
                ];
                Model\Wechat::updateOrCreate(['openid' => $wechatInfo['openid']], $wechatInfo);
            }
            return $this->_member_bind_msg($msg);
        } else {
            $code       = request($wechat->Oauth2_code);
            $cacheName  = $code;
            $cacheValue = Cache::get($cacheName);
            if (!$cacheValue) {
                $accessToken = $wechat->Oauth2_access_token($code);
                $cacheValue  = $accessToken;
                $expiresAt   = Carbon::now()->addSecond($accessToken['expires_in']);
                Cache::put($cacheName, $cacheValue, $expiresAt);
            }
            $accessToken = $cacheValue;
            //绑定模式逻辑节点
            //已经绑定 直接登陆
            //未绑定 登录绑定
            $userInfo   = $wechat->Oauth2_user($accessToken['access_token'], $accessToken['openid']);
            $wechatInfo = Model\Wechat::where('openid', $userInfo['openid'])->first();
            if (null === $wechatInfo) {
                return view('home.Wechat_member_bind');
            } else {
                $memberId = $wechatInfo['member_id'];
                $msg      = $this->doLogin(null, null, false, $memberId);
                return $this->_member_bind_msg($msg);
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

}
