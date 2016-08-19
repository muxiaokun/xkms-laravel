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
// 后台 文章管理

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Article extends Backend
{
    //列表
    public function index()
    {
        $ArticleModel         = D('Article');
        $ArticleChannelModel  = D('ArticleChannel');
        $ArticleCategoryModel = D('ArticleCategory');
        //建立where
        $v_value                         = '';
        $v_value                         = I('title');
        $v_value && $where['title']      = array('like', '%' . $v_value . '%');
        $v_value                         = I('cate_id');
        $v_value && $where['cate_id']    = array('in', $ArticleCategoryModel->m_find_child_id($v_value));
        $v_value                         = I('channel_id');
        $v_value && $where['channel_id'] = $v_value;
        $v_value                         = M_mktime_range('add_time');
        $v_value && $where['add_time']   = $v_value;
        $v_value                         = I('is_audit');
        $v_value && $where['is_audit']   = (1 == $v_value) ? array('gt', 0) : 0;
        $v_value                         = I('if_show');
        $v_value && $where['if_show']    = (1 == $v_value) ? 1 : 0;
        $channel_where                   = $category_where                   = array();
        if (1 != session('backend_info.id')) {
            $allow_channel                             = $ArticleChannelModel->m_find_allow();
            is_array($allow_channel) && $channel_where = array('id' => array('in', $allow_channel));
            if (isset($where['channel_id']) && in_array($where['channel_id'], $allow_channel)) {
                $where['channel_id'] = $where['channel_id'];
            } else {
                $where['channel_id'] = array('in', $allow_channel);
            }

            $allow_category                              = $ArticleCategoryModel->m_find_allow();
            is_array($allow_category) && $category_where = array('id' => array('in', $allow_category));
            if (isset($where['cate_id']) && !M_in_array($where['cate_id'], $allow_category)) {
                $where['cate_id'] = array('in', $allow_category);
            }

            if (isset($where['channel_id']) && isset($where['cate_id'])) {
                $where['_complex'] = array(
                    '_logic'     => 'and',
                    'channel_id' => $where['channel_id'],
                    'cate_id'    => $where['cate_id'],
                );
                unset($where['channel_id']);
                unset($where['cate_id']);
            }
        }
        //初始化翻页 和 列表数据
        $article_list = $ArticleModel->m_select($where, true);
        foreach ($article_list as &$article) {
            $article['channel_name'] = ($article['channel_id']) ? $ArticleCategoryModel->m_find_column($article['channel_id'], 'name') : L('empty');
            $article['cate_name']    = ($article['cate_id']) ? $ArticleCategoryModel->m_find_column($article['cate_id'], 'name') : L('empty');
        }
        $this->assign('article_list', $article_list);
        $this->assign('article_list_count', $ArticleModel->get_page_count($where));

        //初始化where_info
        $channel_list         = $ArticleChannelModel->m_select($channel_where, $ArticleChannelModel->where($channel_where)->count());
        $category_list        = $ArticleCategoryModel->m_select_tree($category_where);
        $search_channel_list  = array();
        $search_category_list = array();
        foreach ($channel_list as $channel) {
            $search_channel_list[$channel['id']] = $channel['name'];
        }

        foreach ($category_list as $category) {
            $search_category_list[$category['id']] = $category['name'];
        }

        //初始化where_info
        $where_info               = array();
        $where_info['title']      = array('type' => 'input', 'name' => L('title'));
        $where_info['cate_id']    = array('type' => 'select', 'name' => L('category'), 'value' => $search_category_list);
        $where_info['channel_id'] = array('type' => 'select', 'name' => L('channel'), 'value' => $search_channel_list);
        $where_info['is_audit']   = array('type' => 'select', 'name' => L('yes') . L('no') . l('audit'), 'value' => array(1 => L('audit'), 2 => L('none') . L('audit')));
        $where_info['if_show']    = array('type' => 'select', 'name' => L('yes') . L('no') . l('show'), 'value' => array(1 => L('show'), 2 => L('hidden')));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('article') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $ArticleModel                        = D('Article');
            $data                                = $this->_make_data();
            isset($data['thumb']) && $thumb_file = $this->_image_thumb($data['thumb'], C('SYS_ARTICLE_THUMB_WIDTH'), C('SYS_ARTICLE_THUMB_HEIGHT'));
            $result_add                          = $ArticleModel->m_add($data);
            //增加了一个分类快捷添加文章的回跳链接
            $reback_link = I('get.cate_id') ? U('ArticleCategory/index') : U('index');
            if ($result_add) {
                $data['new_thumb'] = $thumb_file;
                $this->_add_edit_after_common($data, $ArticleModel->getLastInsID());
                $this->success(L('article') . L('add') . L('success'), $reback_link);
                return;
            } else {
                $this->error(L('article') . L('add') . L('error'), U('add', array('cate_id' => I('get.cate_id'))));
            }
        }

        $this->_add_edit_common();
        $this->assign('title', L('article') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $ArticleModel = D('Article');
        if (IS_POST) {
            $data                                = $this->_make_data();
            isset($data['thumb']) && $thumb_file = $this->_image_thumb($data['thumb'], C('SYS_ARTICLE_THUMB_WIDTH'), C('SYS_ARTICLE_THUMB_HEIGHT'));
            $result_edit                         = $ArticleModel->m_edit($id, $data);
            if ($result_edit) {
                $data['new_thumb'] = $thumb_file;
                $this->_add_edit_after_common($data, $id);
                $this->success(L('article') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('article') . L('edit') . L('error'), $error_go_link);
            }
        }
        $current_config = C('SYS_ARTICLE_SYNC_IMAGE');
        C('SYS_ARTICLE_SYNC_IMAGE', false);
        $edit_info = $ArticleModel->m_find($id);
        C('SYS_ARTICLE_SYNC_IMAGE', $current_config);

        $MemberGroupModel = D('MemberGroup');
        foreach ($edit_info['access_group_id'] as &$access_group_id) {
            $admin_group_name = $MemberGroupModel->m_find_column($access_group_id, 'name');
            $access_group_id  = array('value' => $access_group_id, 'html' => $admin_group_name);
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $extend_tpl           = $ArticleCategoryModel->m_find_top_column($edit_info['cate_id'], 'extend');
        $val_extend           = array();
        foreach ($extend_tpl as $template) {
            $val_extend[$template] = ($edit_info['extend'][$template]) ? $edit_info['extend'][$template] : '';
        }
        $edit_info['extend']        = $val_extend;
        $edit_info['album']         = array_map("json_encode", $edit_info['album']);
        $edit_info['attribute_tpl'] = $ArticleCategoryModel->m_find_top_column($edit_info['cate_id'], 'attribute');

        $this->assign('edit_info', $edit_info);

        $this->_add_edit_common();
        $this->assign('title', L('article') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $this->_check_aed();
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $ArticleModel = D('Article');
        $result_del   = $ArticleModel->m_del($id);
        if ($result_del) {
            $ManageUploadModel = D('ManageUpload');
            $ManageUploadModel->m_edit($id);
            $this->success(L('article') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('article') . L('del') . L('error'), U('index'));
        }
    }

    //配置
    public function setting()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = array(
                'SYS_ARTICLE_SYNC_IMAGE',
                'SYS_ARTICLE_PN_LIMIT',
                'SYS_ARTICLE_THUMB_WIDTH',
                'SYS_ARTICLE_THUMB_HEIGHT',
            );
            $this->_put_config($col, 'system');
            return;
        }

        $this->assign('title', L('article') . L('config'));
        $this->display();
    }

    //异步行编辑
    protected function _line_edit($field, $data)
    {
        $allow_field = array('sort');
        if (!in_array($field, $allow_field)) {
            return L('not') . L('edit') . $field;
        }

        $ArticleModel = D('Article');
        $result_edit  = $ArticleModel->m_edit($data['id'], array($field => $data['value']));
        if ($result_edit) {
            $data['value'] = $ArticleModel->m_find_column($data['id'], $field);
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
            case 'access_group_id':
                $MemberGroupModel                         = D('MemberGroup');
                isset($data['keyword']) && $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $member_group_list                        = $MemberGroupModel->m_select($where);
                foreach ($member_group_list as $member_group) {
                    $result['info'][] = array('value' => $member_group['id'], 'html' => $member_group['name']);
                }
                break;
            case 'exttpl_id':
                isset($data['id']) && $cate_id = $data['id'];
                $ArticleCategoryModel          = D('ArticleCategory');
                $extend_tpl                    = $ArticleCategoryModel->m_find_top_column($cate_id, 'extend');
                foreach ($extend_tpl as $template) {
                    $result['info'][$template] = '';
                }
                break;
            case 'attribute':
                if ($data['id']) {
                    $cate_id              = $data['id'];
                    $ArticleCategoryModel = D('ArticleCategory');
                    $result['info']       = $ArticleCategoryModel->m_find_top_column($cate_id, 'attribute');
                } else {
                    $result = array('status' => false, 'info' => 'id error');
                }
                break;
        }
        return $result;
    }

    //构造数据
    //$is_pwd 是否检测密码规则
    private function _make_data()
    {
        //初始化参数
        $access_group_id       = I('access_group_id');
        $title                 = I('title');
        $author                = I('author');
        $description           = I('description');
        $content               = I('content');
        $cate_id               = I('cate_id');
        $channel_id            = I('channel_id');
        $thumb                 = I('thumb');
        $add_time              = M_mktime(I('add_time'), true);
        $update_time           = M_mktime(I('update_time'), true);
        $sort                  = I('sort');
        $is_stick              = I('is_stick');
        $is_audit              = I('is_audit');
        $is_audit && $is_audit = session('backend_info.id');
        $if_show               = I('if_show');
        $extend                = I('extend');
        $album                 = I('album');
        foreach ($album as &$image_info) {
            $image_info = json_decode(htmlspecialchars_decode($image_info), true);
        }
        $attribute = I('attribute');

        !$description && $description = trim(M_substr(strip_tags(htmlspecialchars_decode($content)), 100));

        $data                                                                           = array();
        ('add' == ACTION_NAME || null !== $access_group_id) && $data['access_group_id'] = $access_group_id;
        ('add' == ACTION_NAME || null !== $title) && $data['title']                     = $title;
        ('add' == ACTION_NAME || null !== $author) && $data['author']                   = $author;
        ('add' == ACTION_NAME || null !== $description) && $data['description']         = $description;
        ('add' == ACTION_NAME || null !== $content) && $data['content']                 = $content;
        ('add' == ACTION_NAME || null !== $cate_id) && $data['cate_id']                 = $cate_id;
        ('add' == ACTION_NAME || null !== $channel_id) && $data['channel_id']           = $channel_id;
        ('add' == ACTION_NAME || null !== $thumb) && $data['thumb']                     = $thumb;
        ('add' == ACTION_NAME || null !== $add_time) && $data['add_time']               = $add_time;
        ('add' == ACTION_NAME || null !== $update_time) && $data['update_time']         = $update_time;
        ('add' == ACTION_NAME || null !== $sort) && $data['sort']                       = $sort;
        ('add' == ACTION_NAME || null !== $is_stick) && $data['is_stick']               = $is_stick;
        ('add' == ACTION_NAME || null !== $is_audit) && $data['is_audit']               = $is_audit;
        ('add' == ACTION_NAME || null !== $if_show) && $data['if_show']                 = $if_show;
        ('add' == ACTION_NAME || null !== $extend) && $data['extend']                   = $extend;
        ('add' == ACTION_NAME || null !== $attribute) && $data['attribute']             = $attribute;
        ('add' == ACTION_NAME || null !== $album) && $data['album']                     = $album;
        $this->_check_aed($data);
        return $data;
    }

    //添加 编辑 之后 公共方法
    private function _add_edit_after_common(&$data, $id)
    {
        // 批量修改时不进行文件绑定
        if (is_array($id)) {
            return;
        }

        $ManageUploadModel = D('ManageUpload');
        foreach ($data['album'] as &$image_info) {
            $bind_file[] = $image_info['src'];
        }

        $bind_file[]    = $data['new_thumb'];
        $bind_file[]    = $data['thumb'];
        $content_upload = M_get_content_upload($data['content']);
        $bind_file      = array_merge($bind_file, $content_upload);
        $ManageUploadModel->m_edit($id, $bind_file);
    }

    //添加 编辑 公共方法
    private function _add_edit_common()
    {
        $ArticleChannelModel  = D('ArticleChannel');
        $ArticleCategoryModel = D('ArticleCategory');
        $channel_where        = $category_where        = array();
        if (1 != session('backend_info.id')) {
            $channel_where['id']  = array('in', $ArticleChannelModel->m_find_allow());
            $category_where['id'] = array('in', $ArticleCategoryModel->m_find_allow());
        }
        $channel_list  = $ArticleChannelModel->m_select($channel_where, $ArticleChannelModel->where($channel_where)->count());
        $category_list = $ArticleCategoryModel->m_select_tree($category_where);
        $this->assign('channel_list', $channel_list);
        $this->assign('category_list', $category_list);
    }

    //检查是否有 add edit del privilege
    private function _check_aed($data = false)
    {
        if (1 == session('backend_info.id')) {
            return true;
        }

        if (!$data) {
            $id           = I('id');
            $ArticleModel = D('Article');
            $data         = $ArticleModel->m_find($id);
        }
        $ArticleCategoryModel = D('ArticleCategory');
        $ArticleChannelModel  = D('ArticleChannel');
        if (!in_array($data['channel_id'], $ArticleChannelModel->m_find_allow())
            && !in_array($data['cate_id'], $ArticleCategoryModel->m_find_allow())
        ) {
            $this->error(L('none') . L('privilege') . L('handle') . L('article'), U('index'));
        }

    }
}
