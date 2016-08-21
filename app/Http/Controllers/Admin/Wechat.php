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
        $v_value                          = I('member_name');
        $v_value && $where['member_name'] = $v_value;
        $v_value                          = M_mktime_range('bind_time');
        $v_value && $where['bind_time']   = $v_value;

        //初始化翻页 和 列表数据
        $wechat_list = $WechatModel->mSelect($where, true);
        foreach ($wechat_list as &$wechat) {
            $wechat['member_name'] = $MemberModel->mFindColumn($wechat['member_id'], 'member_name');
        }
        $this->assign('wechat_list', $wechat_list);
        $this->assign('wechat_list_count', $WechatModel->getPageCount($where));

        //初始化where_info
        $where_info                = array();
        $where_info['member_name'] = array('type' => 'input', 'name' => L('member') . L('name'));
        $where_info['bind_time']   = array('type' => 'time', 'name' => L('bind') . L('time'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

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
        $Api_link = 'http://' . $_SERVER['SERVER_NAME'] . U(C('DEFAULT_MODULE') . '/Wechat/member_bind');
        $this->assign('Api_link', $Api_link);
        $Oauth2_link = $Wechat->Oauth2_enlink($Api_link);
        $this->assign('Oauth2_link', $Oauth2_link);

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
            $template_id_short = C('WECHAT_TEMPLATE_ID');
            if (!$template_id_short) {
                $this->error('WECHAT_TEMPLATE_ID' . L('empty'), U('config'));
            }

        }
        $WechatModel              = D('Wechat');
        $edit_info                = $WechatModel->mFind($id);
        $MemberModel              = D('Member');
        $edit_info['member_name'] = $MemberModel->mFindColumn($edit_info['member_id'], 'member_name');
        $this->assign('edit_info', $edit_info);
        if (IS_POST) {
            $error_go_link = U('edit', array('id' => $id));
            $Wechat        = new \Common\Lib\Wechat();
            $access_token  = $Wechat->get_access_token();
            if (!APP_DEBUG) {
                $template_id = $Wechat->get_template($template_id_short);
                if (0 != $template_id['errcode']) {
                    $this->error('template_id' . L('error'), $error_go_link);
                }

                $template_id = $template_id['template_id'];
            } else {
                $template_id = 'LDB2O9YxLivGqFr-ihZt8EcXf7QlRIH4yRA7kIHlPq4';
            }
            $data = array(
                "touser"      => $edit_info['openid'],
                "template_id" => $template_id,
                "url"         => "http://ms.xjhywh.cn",
                "topcolor"    => "#000000",
            );
            $data['data'] = $this->_make_data();
            $put_template = $Wechat->put_template($data);
            if (0 === $put_template['errcode']) {
                $this->success(L('wechat') . L('send') . L('success'), U('Wechat/index'));
                return;
            } else {
                $this->error(L('wechat') . L('send') . L('error') . L('error' . $put_template['errcode']), $error_go_link);
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
        $result_del  = $WechatModel->mDel($id);
        if ($result_del) {
            $this->success(L('wechat') . L('bind') . L('del') . L('success'), U('Wechat/index'));
            return;
        } else {
            $this->error(L('wechat') . L('bind') . L('del') . L('error'), U('Wechat/edit', array('id' => $id)));
        }
    }

    //构造数据
    private function _make_data()
    {
        $start_content       = I('start_content');
        $start_content_color = I('start_content_color');
        $end_content         = I('end_content');
        $end_content_color   = I('end_content_color');
        $content1            = I('content1');
        $content1_color      = I('content1_color');
        $content2            = I('content2');
        $content2_color      = I('content2_color');

        $data = array(
            'first'    => array('value' => $start_content, 'color' => $start_content_color),
            'keyword1' => array('value' => $content1, 'color' => $content1_color),
            'keyword2' => array('value' => $content2, 'color' => $content2_color),
            'remark'   => array('value' => $end_content, 'color' => $end_content_color),
        );

        return $data;
    }
}
