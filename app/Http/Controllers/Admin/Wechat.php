<?php
//后台 微信

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Wechat extends Backend
{
    //列表 系统已绑定微信账号
    public function index()
    {
        $where       = [];
        $whereValue  = request('member_name');
        $whereValue && $where[] = ['member_name', $whereValue];
        $whereValue = mMktimeRange('bind_time');
        $whereValue && $where[] = ['bind_time', $whereValue];

        //初始化翻页 和 列表数据
        $wechatList = Model\Wechat::mList($where, true);
        foreach ($wechatList as &$wechat) {
            $wechat['member_name'] = Model\Member::mFindColumn($wechat['member_id'], 'member_name');
        }
        $assign['wechat_list']       = $wechatList;
        $assign['wechat_list_count'] = Model\Wechat::mGetPageCount($where);

        //初始化where_info
        $whereInfo                = [];
        $whereInfo['member_name'] = ['type' => 'input', 'name' => trans('common.member') . trans('common.name')];
        $whereInfo['bind_time']   = ['type' => 'time', 'name' => trans('common.bind') . trans('common.time')];
        $assign['where_info']     = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.wechat') . trans('common.management');
        return view('admin.', $assign);
    }

    //配置
    public function add()
    {
        if (request()->isMethod('POST')) {
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
        $Wechat                = new \Common\Lib\Wechat();
        $ApiLink               = 'http://' . $_SERVER['SERVER_NAME'] . route(config('DEFAULT_MODULE') . '/Wechat/member_bind');
        $assign['Api_link']    = $ApiLink;
        $Oauth2Link            = $Wechat->Oauth2_enlink($ApiLink);
        $assign['Oauth2_link'] = $Oauth2Link;

        $assign['title'] = trans('common.config') . trans('common.wechat');
        return view('admin.', $assign);
    }

    //对单一微信发送信息
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        if (!config('app.debug')) {
            $templateIdShort = config('system.wechat_template_id');
            if (!$templateIdShort) {
                return $this->error('WECHAT_TEMPLATE_ID' . trans('common.empty'), route('config'));
            }

        }
        $editInfo                = Model\Wechat::mFind($id);
        $editInfo['member_name'] = Model\Member::mFindColumn($editInfo['member_id'], 'member_name');
        $assign['edit_info']     = $editInfo;
        if (request()->isMethod('POST')) {
            $errorGoLink = route('edit', ['id' => $id]);
            $Wechat      = new \Common\Lib\Wechat();
            $accessToken = $Wechat->get_access_token();
            if (!config('app.debug')) {
                $templateId = $Wechat->get_template($templateIdShort);
                if (0 != $templateId['errcode']) {
                    return $this->error('template_id' . trans('common.error'), $errorGoLink);
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
                return $this->success(trans('common.wechat') . trans('common.send') . trans('common.success'),
                    route('Wechat/index'));
                return;
            } else {
                return $this->error(trans('common.wechat') . trans('common.send') . trans('common.error') . trans('error' . $putTemplate['errcode']),
                    $errorGoLink);
            }
        }

        $assign['title'] = trans('common.send') . trans('common.wechat');
        return view('admin.', $assign);
    }

    //解除绑定
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Wechat/index'));
        }

        $resultDel = Model\Wechat::mDel($id);
        if ($resultDel) {
            return $this->success(trans('common.wechat') . trans('common.bind') . trans('common.del') . trans('common.success'),
                route('Wechat/index'));
            return;
        } else {
            return $this->error(trans('common.wechat') . trans('common.bind') . trans('common.del') . trans('common.error'),
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
