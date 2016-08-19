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
// 后台 文章分类

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class ArticleCategory extends Backend
{
    //列表
    public function index()
    {
        $ArticleCategoryModel = D('ArticleCategory');
        //建立where
        $v_value                         = '';
        $v_value                         = I('name');
        $v_value && $where['name']       = array('like', '%' . $v_value . '%');
        $v_value                         = I('channel_id');
        $v_value && $where['channel_id'] = $v_value;
        $v_value                         = I('if_show');
        $v_value && $where['if_show']    = (1 == $v_value) ? 1 : 0;
        $where['parent_id']              = 0;
        $v_value                         = I('parent_id');
        $v_value && $where['parent_id']  = $v_value;
        if (1 != session('backend_info.id')) {
            $allow_category = $ArticleCategoryModel->m_find_allow();
            if (isset($where['id']) && in_array($where['id'], $allow_category)) {
                $where['id'] = $where['id'];
            } else {
                $where['id'] = array('in', $allow_category);
            }
        }
        //初始化翻页 和 列表数据
        $article_category_list = $ArticleCategoryModel->m_select($where, $ArticleCategoryModel->where($where)->count());
        foreach ($article_category_list as &$article_category) {
            //parent_id 用完销毁不能产生歧义
            $where['parent_id']            = $article_category['id'];
            $article_category['has_child'] = $ArticleCategoryModel->get_page_count($where);
            unset($where['parent_id']);
            $article_category['show']          = ($article_category['if_show']) ? L('show') : L('hidden');
            $article_category['ajax_api_link'] = U('ajax_api');
            $article_category['look_link']     = U('Home/Article/category', array('cate_id' => $article_category['id']));
            $article_category['edit_link']     = U('edit', array('id' => $article_category['id']));
            $article_category['del_link']      = U('del', array('id' => $article_category['id']));
            $article_category['add_link']      = U('Article/add', array('cate_id' => $article_category['id']));
        }

        if (IS_AJAX) {
            $this->ajaxReturn($article_category_list);
            return;
        }

        $this->assign('article_category_list', $article_category_list);

        //初始化where_info
        $where_info            = array();
        $where_info['name']    = array('type' => 'input', 'name' => L('article') . L('category') . L('name'));
        $where_info['if_show'] = array('type' => 'select', 'name' => L('yes') . L('no') . L('show'), 'value' => array(1 => L('show'), 2 => L('hidden')));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('article') . L('category') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $ArticleCategoryModel = D('ArticleCategory');
            $data                 = $this->_make_data();
            $result_add           = $ArticleCategoryModel->m_add($data);
            if ($result_add) {
                $this->_add_edit_after_common($data, $id);
                $this->success(L('article') . L('category') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('article') . L('category') . L('add') . L('error'), U('add'));
            }
        }

        $this->_add_edit_common();
        $this->assign('title', L('add') . L('article') . L('category'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('get.id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $ArticleCategoryModel = D('ArticleCategory');

        if (1 != session('backend_info.id')
            && !M_in_array($id, $ArticleCategoryModel->m_find_allow())) {
            $this->error(L('none') . L('privilege') . L('edit') . L('article') . L('category'), U('index'));
        }

        $ma_allow_arr = $ArticleCategoryModel->m_find_allow('ma');
        if (IS_POST) {
            $data = $this->_make_data();
            if (1 != session('backend_info.id')
                && !M_in_array($id, $ma_allow_arr)) {
                unset($data['manage_id']);
                unset($data['manage_group_id']);
                unset($data['access_group_id']);
            }
            $result_edit = $ArticleCategoryModel->m_edit($id, $data);
            if ($result_edit) {
                $this->_add_edit_after_common($data, $id);
                $this->success(L('article') . L('category') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('article') . L('category') . L('edit') . L('error'), U('edit', array('id' => $id)));
            }
        }

        $current_config = C('SYS_ARTICLE_SYNC_IMAGE');
        C('SYS_ARTICLE_SYNC_IMAGE', false);
        $edit_info = $ArticleCategoryModel->m_find($id);
        C('SYS_ARTICLE_SYNC_IMAGE', $current_config);
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

        $this->_add_edit_common();
        $this->assign('title', L('edit') . L('article') . L('category'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $ArticleCategoryModel = D('ArticleCategory');
        //删除必须是 属主
        if (!M_in_array($id, $ArticleCategoryModel->m_find_allow('ma'))
            && 1 != session('backend_info.id')
        ) {
            $this->error(L('none') . L('privilege') . L('del') . L('article') . L('category'), U('index'));
        }

        //解除文章和被删除分类的关系
        $ArticleModel = D('Article');
        $result_clean = $ArticleModel->m_clean($id, 'cate_id', 0);
        if (!$result_clean) {
            $this->error(L('article') . L('clear') . L('category') . L('error'), U('index'));
        }

        $result_del = $ArticleCategoryModel->m_del($id);
        if ($result_del) {
            //释放图片绑定
            $ManageUploadModel = D('ManageUpload');
            $ManageUploadModel->m_edit($id);
            $this->success(L('article') . L('category') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('article') . L('category') . L('del') . L('error'), U('index'));
        }
    }

    //异步行编辑
    protected function _line_edit($field, $data)
    {
        $allow_field = array('sort');
        if (!in_array($field, $allow_field)) {
            return L('not') . L('edit') . $field;
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $result_edit          = $ArticleCategoryModel->m_edit($data['id'], array($field => $data['value']));
        if ($result_edit) {
            $data['value'] = $ArticleCategoryModel->m_find_column($data['id'], $field);
            return array('status' => true, 'info' => $data['value']);
        } else {
            return array('status' => false, 'info' => L('edit') . L('error'));
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
        $parent_id = I('parent_id');
        $name      = I('name');
        $manage_id = I('manage_id');
        $add_id    = session('backend_info.id');
        if (('add' == ACTION_NAME || null !== $manage_id)
            && !in_array($add_id, $manage_id)
        ) {
            $manage_id[] = $add_id;
        }

        $manage_group_id = I('manage_group_id');
        $access_group_id = I('access_group_id');
        $thumb           = I('thumb');
        $sort            = I('sort');
        $s_limit         = I('s_limit');
        $if_show         = I('if_show');
        $is_content      = I('is_content');
        $content         = I('content');
        $extend          = I('extend');
        $post_attribute  = I('attribute');
        $attribute       = array();
        foreach ($post_attribute as $attrs) {
            $attribute[$attrs['name']] = array();
            foreach ($attrs['value'] as $attr_value) {
                $attribute[$attrs['name']][] = $attr_value;
            }
        }
        $template         = I('template');
        $list_template    = I('list_template');
        $article_template = I('article_template');

        $data                                                                             = array();
        ('add' == ACTION_NAME || null !== $parent_id) && $data['parent_id']               = $parent_id;
        ('add' == ACTION_NAME || null !== $name) && $data['name']                         = $name;
        ('add' == ACTION_NAME || null !== $manage_id) && $data['manage_id']               = $manage_id;
        ('add' == ACTION_NAME || null !== $manage_group_id) && $data['manage_group_id']   = $manage_group_id;
        ('add' == ACTION_NAME || null !== $access_group_id) && $data['access_group_id']   = $access_group_id;
        ('add' == ACTION_NAME || null !== $thumb) && $data['thumb']                       = $thumb;
        ('add' == ACTION_NAME || null !== $sort) && $data['sort']                         = $sort;
        ('add' == ACTION_NAME || null !== $s_limit) && $data['s_limit']                   = $s_limit;
        ('add' == ACTION_NAME || null !== $if_show) && $data['if_show']                   = $if_show;
        ('add' == ACTION_NAME || null !== $is_content) && $data['is_content']             = $is_content;
        ('add' == ACTION_NAME || null !== $content) && $data['content']                   = $content;
        ('add' == ACTION_NAME || null !== $extend) && $data['extend']                     = $extend;
        ('add' == ACTION_NAME || null !== $attribute) && $data['attribute']               = $attribute;
        ('add' == ACTION_NAME || null !== $template) && $data['template']                 = $template;
        ('add' == ACTION_NAME || null !== $list_template) && $data['list_template']       = $list_template;
        ('add' == ACTION_NAME || null !== $article_template) && $data['article_template'] = $article_template;
        return $data;
    }

    //添加 编辑 之后 公共方法
    private function _add_edit_after_common(&$data, $id)
    {
        $ManageUploadModel = D('ManageUpload');
        $bind_file         = M_get_content_upload($data['content']);
        $bind_file[]       = $data['thumb'];
        $ManageUploadModel->m_edit($id, $bind_file);
    }

    //构造分类assign公共数据
    private function _add_edit_common()
    {
        $ArticleCategoryModel = D('ArticleCategory');
        $id                   = I('id');
        $where                = array();
        if ($id) {
            $where['id'] = array('neq', $id);
        }

        $this->assign('category_list', $ArticleCategoryModel->m_select_tree($where));
        $manage_privilege = (1 == session('backend_info.id')) || in_array($id, $ArticleCategoryModel->m_find_allow('ma'));
        $this->assign('manage_privilege', $manage_privilege);
        $this->assign('template_list', M_scan_template('category', C('DEFAULT_MODULE'), 'Article'));
        $this->assign('list_template_list', M_scan_template('list_category', C('DEFAULT_MODULE'), 'Article'));
        $this->assign('article_template_list', M_scan_template('article', C('DEFAULT_MODULE'), 'Article'));
    }
}
