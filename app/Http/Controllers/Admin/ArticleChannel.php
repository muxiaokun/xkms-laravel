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
// 后台 文章频道

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class ArticleChannel extends Backend
{
    //列表
    public function index()
    {
        $ArticleChannelModel = D('ArticleChannel');
        //建立where
        $v_value                      = '';
        $v_value                      = I('name');
        $v_value && $where['name']    = array('like', '%' . $v_value . '%');
        $v_value                      = I('if_show');
        $v_value && $where['if_show'] = (1 == $v_value) ? 1 : 0;
        if (1 != session('backend_info.id')) {
            $allow_channel = $ArticleChannelModel->m_find_allow();
            $where['id']   = array('in', $allow_channel);
        }
        //初始化翻页 和 列表数据
        $article_channel_list = $ArticleChannelModel->m_select($where, true);
        $this->assign('article_channel_list', $article_channel_list);
        $this->assign('article_channel_list_count', $ArticleChannelModel->get_page_count($where));

        //初始化where_info
        $where_info            = array();
        $where_info['name']    = array('type' => 'input', 'name' => L('channel') . L('name'));
        $where_info['if_show'] = array('type' => 'select', 'name' => L('yes') . L('no') . L('show'), 'value' => array(1 => L('show'), 2 => L('hidden')));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('channel') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_AJAX) {
            $this->ajaxReturn($this->_add_edit_category_common());
            return;
        }
        if (IS_POST) {
            $ArticleChannelModel = D('ArticleChannel');
            $data                = $this->_make_data();
            $result_add          = $ArticleChannelModel->m_add($data);
            if ($result_add) {
                $this->success(L('channel') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('channel') . L('add') . L('error'), U('add'));
            }
        }

        $this->_add_edit_common();

        $this->assign('title', L('add') . L('channel'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $ArticleChannelModel = D('ArticleChannel');
        if (IS_AJAX) {
            $id        = I('get.id');
            $edit_info = $ArticleChannelModel->m_find($id);
            $this->ajaxReturn($this->_add_edit_category_common($edit_info));
            return;
        }

        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        if (1 != session('backend_info.id')
            && !M_in_array($id, $ArticleChannelModel->m_find_allow())) {
            $this->error(L('none') . L('privilege') . L('edit') . L('channel'), U('index'));
        }

        $ma_allow_arr = $ArticleChannelModel->m_find_allow('ma');
        if (IS_POST) {
            $data = $this->_make_data();
            if (1 != session('backend_info.id')
                && !M_in_array($id, $ma_allow_arr)) {
                unset($data['manage_id']);
                unset($data['manage_group_id']);
                unset($data['access_group_id']);
            }
            $result_edit = $ArticleChannelModel->m_edit($id, $data);
            if ($result_edit) {
                $this->success(L('channel') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('channel') . L('edit') . L('error'), $error_go_link);
            }
        }

        $edit_info = $ArticleChannelModel->m_find($id);
        //如果有管理权限进行进一步数据处理
        if (M_in_array($id, $ma_allow_arr)) {
            $AdminModel = D('Admin');
            foreach ($edit_info['manage_id'] as &$manage_id) {
                $admin_name = $AdminModel->m_find_column($manage_id, 'admin_name');
                $manage_id  = array('value' => $manage_id, 'html' => $admin_name);
            }
            $edit_info['manage_id'] = json_encode($edit_info['manage_id']);
            $AdminGroupModel        = D('AdminGroup');
            foreach ($edit_info['manage_group_id'] as &$manage_group_id) {
                $admin_group_name = $AdminGroupModel->m_find_column($manage_group_id, 'name');
                $manage_group_id  = array('value' => $manage_group_id, 'html' => $admin_group_name);
            }
            $edit_info['manage_group_id'] = json_encode($edit_info['manage_group_id']);
            $MemberGroupModel             = D('MemberGroup');
            foreach ($edit_info['access_group_id'] as &$access_group_id) {
                $admin_group_name = $MemberGroupModel->m_find_column($access_group_id, 'name');
                $access_group_id  = array('value' => $access_group_id, 'html' => $admin_group_name);
            }
            $edit_info['access_group_id'] = json_encode($edit_info['access_group_id']);
        }
        $this->assign('edit_info', $edit_info);
        $this->_add_edit_common($edit_info);

        $this->assign('title', L('edit') . L('channel'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $ArticleChannelModel = D('ArticleChannel');
        //删除必须是 属主
        if (1 != session('backend_info.id')
            && !M_in_array($id, $ArticleChannelModel->m_find_allow('ma'))
        ) {
            $this->error(L('none') . L('privilege') . L('del') . L('channel'), U('index'));
        }

        //解除文章和被删除频道的关系
        $ArticleModel = D('Article');
        $result_clean = $ArticleModel->m_clean($id, 'channel_id', 0);
        if (!$result_clean) {
            $this->error(L('article') . L('clear') . L('channel') . L('error'), U('index'));
        }

        $result_del = $ArticleChannelModel->m_del($id);
        if ($result_del) {
            $this->success(L('channel') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('channel') . L('del') . L('error'), U('index'));
        }
    }

    //异步数据获取
    protected function _get_data($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'manage_id':
                isset($data['inserted']) && $where['id']        = array('not in', $data['inserted']);
                $AdminModel                                     = D('Admin');
                isset($data['keyword']) && $where['admin_name'] = array('like', '%' . $data['keyword'] . '%');
                $admin_user_list                                = $AdminModel->m_select($where);
                foreach ($admin_user_list as $admin_user) {
                    $result['info'][] = array('value' => $admin_user['id'], 'html' => $admin_user['admin_name']);
                }
                break;
            case 'manage_group_id':
                isset($data['inserted']) && $where['id']  = array('not in', $data['inserted']);
                $AdminGroupModel                          = D('AdminGroup');
                isset($data['keyword']) && $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $admin_group_list                         = $AdminGroupModel->m_select($where);
                foreach ($admin_group_list as $admin_group) {
                    $result['info'][] = array('value' => $admin_group['id'], 'html' => $admin_group['name']);
                }
                break;
            case 'access_group_id':
                isset($data['inserted']) && $where['id']  = array('not in', $data['inserted']);
                $MemberGroupModel                         = D('MemberGroup');
                isset($data['keyword']) && $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $member_group_list                        = $MemberGroupModel->m_select($where);
                foreach ($member_group_list as $member_group) {
                    $result['info'][] = array('value' => $member_group['id'], 'html' => $member_group['name']);
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function _make_data()
    {
        //初始化参数
        $name        = I('name');
        $keywords    = I('keywords');
        $description = I('description');
        $other       = I('other');
        $manage_id   = I('manage_id');
        $add_id      = session('backend_info.id');
        if (('add' == ACTION_NAME || null !== $manage_id)
            && !in_array($add_id, $manage_id)
        ) {
            $manage_id[] = $add_id;
        }

        $manage_group_id       = I('manage_group_id');
        $access_group_id       = I('access_group_id');
        $if_show               = I('if_show');
        $template              = I('template');
        $category_list         = I('category_list', array());
        $s_limit               = I('s_limit');
        $template_list         = I('template_list');
        $list_template_list    = I('list_template_list');
        $article_template_list = I('article_template_list');
        $ext_info              = array();
        foreach ($category_list as $id) {
            $ext_info[$id] = array(
                's_limit'          => $s_limit[$id],
                'template'         => $template_list[$id],
                'list_template'    => $list_template_list[$id],
                'article_template' => $article_template_list[$id],
            );
        }

        $data                                                                           = array();
        ('add' == ACTION_NAME || null !== $name) && $data['name']                       = $name;
        ('add' == ACTION_NAME || null !== $keywords) && $data['keywords']               = $keywords;
        ('add' == ACTION_NAME || null !== $description) && $data['description']         = $description;
        ('add' == ACTION_NAME || null !== $other) && $data['other']                     = $other;
        ('add' == ACTION_NAME || null !== $manage_id) && $data['manage_id']             = $manage_id;
        ('add' == ACTION_NAME || null !== $manage_group_id) && $data['manage_group_id'] = $manage_group_id;
        ('add' == ACTION_NAME || null !== $access_group_id) && $data['access_group_id'] = $access_group_id;
        ('add' == ACTION_NAME || null !== $if_show) && $data['if_show']                 = $if_show;
        ('add' == ACTION_NAME || null !== $template) && $data['template']               = $template;
        ('add' == ACTION_NAME || null !== $ext_info) && $data['ext_info']               = $ext_info;
        return $data;
    }

    //构造频道assign公共数据
    private function _add_edit_common($channel_info = false)
    {
        $this->assign('article_category_list', $this->_add_edit_category_common($channel_info));

        $ArticleChannelModel = D('ArticleChannel');
        $id                  = I('id');
        $manage_privilgeg    = in_array($id, $ArticleChannelModel->m_find_allow('ma')) || 1 == session('backend_info.id');
        $this->assign('manage_privilege', $manage_privilgeg);

        $ArticleCategoryModel = D('ArticleCategory');
        $this->assign('channel_template_list', M_scan_template('channel', C('DEFAULT_MODULE'), 'Article'));
        $this->assign('template_list', M_scan_template('category', C('DEFAULT_MODULE'), 'Article'));
        $this->assign('list_template_list', M_scan_template('list_category', C('DEFAULT_MODULE'), 'Article'));
        $this->assign('article_template_list', M_scan_template('article', C('DEFAULT_MODULE'), 'Article'));
    }

    //构造频道公共ajax
    private function _add_edit_category_common($channel_info = false)
    {
        $ArticleCategoryModel           = D('ArticleCategory');
        $where['parent_id']             = 0;
        $v_value                        = I('parent_id');
        $v_value && $where['parent_id'] = $v_value;

        $article_category_list = $ArticleCategoryModel->m_select($where, $ArticleCategoryModel->where($where)->count());
        foreach ($article_category_list as &$article_category) {
            $article_category['has_child'] = $ArticleCategoryModel->where(array('parent_id' => $article_category['id']))->count();
            if ($channel_info && isset($channel_info['ext_info'][$article_category['id']])) {
                $article_category['checked']          = true;
                $article_category['s_limit']          = $channel_info['ext_info'][$article_category['id']]['s_limit'];
                $article_category['template']         = $channel_info['ext_info'][$article_category['id']]['template'];
                $article_category['list_template']    = $channel_info['ext_info'][$article_category['id']]['list_template'];
                $article_category['article_template'] = $channel_info['ext_info'][$article_category['id']]['article_template'];
            }
        }
        return $article_category_list;
    }
}
