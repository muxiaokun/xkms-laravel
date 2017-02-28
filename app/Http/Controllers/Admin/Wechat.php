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
        //初始化翻页 和 列表数据
        $wechatList = Model\Wechat::where(function ($query) {
            $member_name = request('member_name');
            if ($member_name) {
                $memberIds = Model\Member::where('member_name', 'like',
                    '%' . $member_name . '%')->select(['id'])->pluck('id');
                $query->whereIn('member_id', $memberIds);
            }

            $bind_time = mMktimeRange('bind_time');
            if ($bind_time) {
                $query->timeWhere('bind_time', $bind_time);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($wechatList as &$wechat) {
            $wechat['member_name'] = Model\Member::colWhere($wechat['member_id'])->first()['member_name'];
        }
        $assign['wechat_list'] = $wechatList;

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

        $assign['title'] = trans('wechat.wechat') . trans('common.management');
        return view('admin.Wechat_index', $assign);
    }

    //配置
    public function add()
    {
        if (request()->isMethod('POST')) {
            //表单提交的名称
            $col = [
                'wechat_id',
                'wechat_secret',
                'wechat_token',
                'wechat_aeskey',
                'wechat_template_id',
            ];
            return $this->_put_config($col, 'system');
        }

        //认证连接
        $Wechat                = new \App\Library\Wechat();
        $ApiLink               = route('Home::Wechat::member_bind');
        $assign['Api_link']    = $ApiLink;
        $Oauth2Link            = $Wechat->Oauth2_enlink($ApiLink);
        $assign['Oauth2_link'] = $Oauth2Link;

        $assign['edit_info'] = Model\Wechat::columnEmptyData();
        $assign['title']       = trans('common.config') . trans('wechat.wechat');
        return view('admin.Wechat_add', $assign);
    }

    //对单一微信发送信息
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Wechat::index'));
        }

        if (!config('app.debug')) {
            $templateIdShort = config('system.wechat_template_id');
            if (!$templateIdShort) {
                return $this->error('WECHAT_TEMPLATE_ID' . trans('common.empty'), route('Admin::Wechat::config'));
            }

        }
        $editInfo = Model\Wechat::colWhere($id)->first()->toArray();
        $editInfo['member_name'] = Model\Member::colWhere($editInfo['member_id'])->first()['member_name'];
        $assign['edit_info']     = $editInfo;
        if (request()->isMethod('POST')) {
            $errorGoLink = route('Admin::Wechat::edit', ['id' => $id]);
            $Wechat = new \App\Library\Wechat();
            if (config('app.debug')) {
                $templateId = 'LDB2O9YxLivGqFr-ihZt8EcXf7QlRIH4yRA7kIHlPq4';
            } else {
                $templateId = $Wechat->get_template($templateIdShort);
                if (0 != $templateId['errcode']) {
                    return $this->error('template_id' . trans('common.error'), $errorGoLink);
                }

                $templateId = $templateId['template_id'];
            }
            $data         = [
                "touser"      => $editInfo['openid'],
                "template_id" => $templateId,
                "url"         => "https://wwww.baidu.com",
                "topcolor"    => "#000000",
            ];
            $data['data'] = $this->makeData();
            $putTemplate  = $Wechat->put_template($data);
            if (0 === $putTemplate['errcode']) {
                return $this->success(trans('wechat.wechat') . trans('common.send') . trans('common.success'),
                    route('Admin::Wechat::index'));
            } else {
                return $this->error(trans('wechat.wechat') . trans('common.send') . trans('common.error') . trans('error' . $putTemplate['errcode']),
                    $errorGoLink);
            }
        }

        $assign['title'] = trans('common.send') . trans('wechat.wechat');
        return view('admin.Wechat_edit', $assign);
    }

    //解除绑定
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Wechat/index'));
        }

        $resultDel = Model\Wechat::destroy($id);
        if ($resultDel) {
            return $this->success(trans('wechat.wechat') . trans('common.bind') . trans('common.del') . trans('common.success'),
                route('Admin::Wechat::index'));
        } else {
            return $this->error(trans('wechat.wechat') . trans('common.bind') . trans('common.del') . trans('common.error'),
                route('Admin::Wechat::edit', ['id' => $id]));
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
