<?php
//后台 微信

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Wechat extends Backend
{
    //列表 系统已绑定微信账号
    public function index()
    {
        $WechatModel = D('Wechat');
        $MemberModel = D('Member');
        $where       = [];
        $whereValue  = request('member_name');
        $whereValue && $where['member_name'] = $whereValue;
        $whereValue = mMktimeRange('bind_time');
        $whereValue && $where['bind_time'] = $whereValue;

        //初始化翻页 和 列表数据
        $wechatList = $WechatModel->mSelect($where, true);
        foreach ($wechatList as &$wechat) {
            $wechat['member_name'] = $MemberModel->mFindColumn($wechat['member_id'], 'member_name');
        }
        $this->assign('wechat_list', $wechatList);
        $this->assign('wechat_list_count', $WechatModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo                = [];
        $whereInfo['member_name'] = ['type' => 'input', 'name' => trans('member') . trans('name')];
        $whereInfo['bind_time']   = ['type' => 'time', 'name' => trans('bind') . trans('time')];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = [];
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('wechat') . trans('management'));
        $this->display();
    }

    //配置
    public function add()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = [
                'WECHAT_ID',
                'WECHAT_SECRET',
                'WECHAT_TOKEN',
                'WECHAT_RECORD_LOG',
                'WECHAT_AESKEY',
                'WECHAT_TEMPLATE_ID',
            ];
            $this->_put_config($col, 'system');
            return;
        }

        //认证连接
        $Wechat  = new \Common\Lib\Wechat();
        $ApiLink = 'http://' . $_SERVER['SERVER_NAME'] . route(config('DEFAULT_MODULE') . '/Wechat/member_bind');
        $this->assign('Api_link', $ApiLink);
        $Oauth2Link = $Wechat->Oauth2_enlink($ApiLink);
        $this->assign('Oauth2_link', $Oauth2Link);

        $this->assign('title', trans('config') . trans('wechat'));
        $this->display();
    }

    //对单一微信发送信息
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        if (!config('app.debug')) {
            $templateIdShort = config('WECHAT_TEMPLATE_ID');
            if (!$templateIdShort) {
                $this->error('WECHAT_TEMPLATE_ID' . trans('empty'), route('config'));
            }

        }
        $WechatModel             = D('Wechat');
        $editInfo                = $WechatModel->mFind($id);
        $MemberModel             = D('Member');
        $editInfo['member_name'] = $MemberModel->mFindColumn($editInfo['member_id'], 'member_name');
        $this->assign('edit_info', $editInfo);
        if (IS_POST) {
            $errorGoLink = route('edit', ['id' => $id]);
            $Wechat      = new \Common\Lib\Wechat();
            $accessToken = $Wechat->get_access_token();
            if (!config('app.debug')) {
                $templateId = $Wechat->get_template($templateIdShort);
                if (0 != $templateId['errcode']) {
                    $this->error('template_id' . trans('error'), $errorGoLink);
                }

                $templateId = $templateId['template_id'];
            } else {
                $templateId = 'LDB2O9YxLivGqFr-ihZt8EcXf7QlRIH4yRA7kIHlPq4';
            }
            $data         = [
                "touser"      => $editInfo['openid'],
                "template_id" => $templateId,
                "url"         => "http://ms.xjhywh.cn",
                "topcolor"    => "#000000",
            ];
            $data['data'] = $this->makeData();
            $putTemplate  = $Wechat->put_template($data);
            if (0 === $putTemplate['errcode']) {
                $this->success(trans('wechat') . trans('send') . trans('success'), route('Wechat/index'));
                return;
            } else {
                $this->error(trans('wechat') . trans('send') . trans('error') . trans('error' . $putTemplate['errcode']),
                    $errorGoLink);
            }
        }

        $this->assign('title', trans('send') . trans('wechat'));
        $this->display();
    }

    //解除绑定
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('Wechat/index'));
        }

        $WechatModel = D('Wechat');
        $resultDel   = $WechatModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('wechat') . trans('bind') . trans('del') . trans('success'), route('Wechat/index'));
            return;
        } else {
            $this->error(trans('wechat') . trans('bind') . trans('del') . trans('error'),
                route('Wechat/edit', ['id' => $id]));
        }
    }

    //构造数据
    private function makeData()
    {
        $startContent      = request('start_content');
        $startContentColor = request('start_content_color');
        $endContent        = request('end_content');
        $endContentColor   = request('end_content_color');
        $content1          = request('content1');
        $content1Color     = request('content1_color');
        $content2          = request('content2');
        $content2Color     = request('content2_color');

        $data = [
            'first'    => ['value' => $startContent, 'color' => $startContentColor],
            'keyword1' => ['value' => $content1, 'color' => $content1Color],
            'keyword2' => ['value' => $content2, 'color' => $content2Color],
            'remark'   => ['value' => $endContent, 'color' => $endContentColor],
        ];

        return $data;
    }
}
