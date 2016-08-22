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
//后台 微信

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Wechat extends Backend
{
    //列表 系统已绑定微信账号
    public function index()
    {
        $WechatModel                      = D('Wechat');
        $MemberModel                      = D('Member');
        $where                            = array();
        $whereValue                          = I('member_name');
        $whereValue && $where['member_name'] = $whereValue;
        $whereValue                          = mMktimeRange('bind_time');
        $whereValue && $where['bind_time']   = $whereValue;

        //初始化翻页 和 列表数据
        $wechatList = $WechatModel->mSelect($where, true);
        foreach ($wechatList as &$wechat) {
            $wechat['member_name'] = $MemberModel->mFindColumn($wechat['member_id'], 'member_name');
        }
        $this->assign('wechat_list', $wechatList);
        $this->assign('wechat_list_count', $WechatModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo                = array();
        $whereInfo['member_name'] = array('type' => 'input', 'name' => L('member') . L('name'));
        $whereInfo['bind_time']   = array('type' => 'time', 'name' => L('bind') . L('time'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('wechat') . L('management'));
        $this->display();
    }

    //配置
    public function add()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = array(
                'WECHAT_ID',
                'WECHAT_SECRET',
                'WECHAT_TOKEN',
                'WECHAT_RECORD_LOG',
                'WECHAT_AESKEY',
                'WECHAT_TEMPLATE_ID',
            );
            $this->_put_config($col, 'system');
            return;
        }

        //认证连接
        $Wechat   = new \Common\Lib\Wechat();
        $ApiLink = 'http://' . $_SERVER['SERVER_NAME'] . U(C('DEFAULT_MODULE') . '/Wechat/member_bind');
        $this->assign('Api_link', $ApiLink);
        $Oauth2Link = $Wechat->Oauth2_enlink($ApiLink);
        $this->assign('Oauth2_link', $Oauth2Link);

        $this->assign('title', L('config') . L('wechat'));
        $this->display();
    }

    //对单一微信发送信息
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        if (!APP_DEBUG) {
            $templateIdShort = C('WECHAT_TEMPLATE_ID');
            if (!$templateIdShort) {
                $this->error('WECHAT_TEMPLATE_ID' . L('empty'), U('config'));
            }

        }
        $WechatModel              = D('Wechat');
        $editInfo                = $WechatModel->mFind($id);
        $MemberModel              = D('Member');
        $editInfo['member_name'] = $MemberModel->mFindColumn($editInfo['member_id'], 'member_name');
        $this->assign('edit_info', $editInfo);
        if (IS_POST) {
            $errorGoLink = U('edit', array('id' => $id));
            $Wechat        = new \Common\Lib\Wechat();
            $accessToken  = $Wechat->get_access_token();
            if (!APP_DEBUG) {
                $templateId = $Wechat->get_template($templateIdShort);
                if (0 != $templateId['errcode']) {
                    $this->error('template_id' . L('error'), $errorGoLink);
                }

                $templateId = $templateId['template_id'];
            } else {
                $templateId = 'LDB2O9YxLivGqFr-ihZt8EcXf7QlRIH4yRA7kIHlPq4';
            }
            $data = array(
                "touser"      => $editInfo['openid'],
                "template_id" => $templateId,
                "url"         => "http://ms.xjhywh.cn",
                "topcolor"    => "#000000",
            );
            $data['data'] = $this->makeData();
            $putTemplate = $Wechat->put_template($data);
            if (0 === $putTemplate['errcode']) {
                $this->success(L('wechat') . L('send') . L('success'), U('Wechat/index'));
                return;
            } else {
                $this->error(L('wechat') . L('send') . L('error') . L('error' . $putTemplate['errcode']), $errorGoLink);
            }
        }

        $this->assign('title', L('send') . L('wechat'));
        $this->display();
    }

    //解除绑定
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('Wechat/index'));
        }

        $WechatModel = D('Wechat');
        $resultDel  = $WechatModel->mDel($id);
        if ($resultDel) {
            $this->success(L('wechat') . L('bind') . L('del') . L('success'), U('Wechat/index'));
            return;
        } else {
            $this->error(L('wechat') . L('bind') . L('del') . L('error'), U('Wechat/edit', array('id' => $id)));
        }
    }

    //构造数据
    private function makeData()
    {
        $startContent       = I('start_content');
        $startContentColor = I('start_content_color');
        $endContent         = I('end_content');
        $endContentColor   = I('end_content_color');
        $content1            = I('content1');
        $content1Color      = I('content1_color');
        $content2            = I('content2');
        $content2Color      = I('content2_color');

        $data = array(
            'first'    => array('value' => $startContent, 'color' => $startContentColor),
            'keyword1' => array('value' => $content1, 'color' => $content1Color),
            'keyword2' => array('value' => $content2, 'color' => $content2Color),
            'remark'   => array('value' => $endContent, 'color' => $endContentColor),
        );

        return $data;
    }
}
