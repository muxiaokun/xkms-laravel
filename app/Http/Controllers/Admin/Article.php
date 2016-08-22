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
        $whereValue                         = '';
        $whereValue                         = I('title');
        $whereValue && $where['title']      = array('like', '%' . $whereValue . '%');
        $whereValue                         = I('cate_id');
        $whereValue && $where['cate_id']    = array('in', $ArticleCategoryModel->mFind_child_id($whereValue));
        $whereValue                         = I('channel_id');
        $whereValue && $where['channel_id'] = $whereValue;
        $whereValue                         = M_mktime_range('add_time');
        $whereValue && $where['add_time']   = $whereValue;
        $whereValue                         = I('is_audit');
        $whereValue && $where['is_audit']   = (1 == $whereValue) ? array('gt', 0) : 0;
        $whereValue                         = I('if_show');
        $whereValue && $where['if_show']    = (1 == $whereValue) ? 1 : 0;
        $channelWhere                   = $categoryWhere                   = array();
        if (1 != session('backend_info.id')) {
            $allowChannel                             = $ArticleChannelModel->mFind_allow();
            is_array($allowChannel) && $channelWhere = array('id' => array('in', $allowChannel));
            if (isset($where['channel_id']) && in_array($where['channel_id'], $allowChannel)) {
                $where['channel_id'] = $where['channel_id'];
            } else {
                $where['channel_id'] = array('in', $allowChannel);
            }

            $allowCategory                              = $ArticleCategoryModel->mFind_allow();
            is_array($allowCategory) && $categoryWhere = array('id' => array('in', $allowCategory));
            if (isset($where['cate_id']) && !M_in_array($where['cate_id'], $allowCategory)) {
                $where['cate_id'] = array('in', $allowCategory);
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
        $articleList = $ArticleModel->mSelect($where, true);
        foreach ($articleList as &$article) {
            $article['channel_name'] = ($article['channel_id']) ? $ArticleCategoryModel->mFindColumn($article['channel_id'], 'name') : L('empty');
            $article['cate_name']    = ($article['cate_id']) ? $ArticleCategoryModel->mFindColumn($article['cate_id'], 'name') : L('empty');
        }
        $this->assign('article_list', $articleList);
        $this->assign('article_list_count', $ArticleModel->mGetPageCount($where));

        //初始化where_info
        $channelList         = $ArticleChannelModel->mSelect($channelWhere, $ArticleChannelModel->where($channelWhere)->count());
        $categoryList        = $ArticleCategoryModel->mSelect_tree($categoryWhere);
        $searchChannelList  = array();
        $searchCategoryList = array();
        foreach ($channelList as $channel) {
            $searchChannelList[$channel['id']] = $channel['name'];
        }

        foreach ($categoryList as $category) {
            $searchCategoryList[$category['id']] = $category['name'];
        }

        //初始化where_info
        $whereInfo               = array();
        $whereInfo['title']      = array('type' => 'input', 'name' => L('title'));
        $whereInfo['cate_id']    = array('type' => 'select', 'name' => L('category'), 'value' => $searchCategoryList);
        $whereInfo['channel_id'] = array('type' => 'select', 'name' => L('channel'), 'value' => $searchChannelList);
        $whereInfo['is_audit']   = array('type' => 'select', 'name' => L('yes') . L('no') . l('audit'), 'value' => array(1 => L('audit'), 2 => L('none') . L('audit')));
        $whereInfo['if_show']    = array('type' => 'select', 'name' => L('yes') . L('no') . l('show'), 'value' => array(1 => L('show'), 2 => L('hidden')));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('article') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $ArticleModel                        = D('Article');
            $data                                = $this->makeData();
            isset($data['thumb']) && $thumbFile = $this->imageThumb($data['thumb'], C('SYS_ARTICLE_THUMB_WIDTH'), C('SYS_ARTICLE_THUMB_HEIGHT'));
            $resultAdd                          = $ArticleModel->mAdd($data);
            //增加了一个分类快捷添加文章的回跳链接
            $rebackLink = I('get.cate_id') ? U('ArticleCategory/index') : U('index');
            if ($resultAdd) {
                $data['new_thumb'] = $thumbFile;
                $this->addEditAfterCommon($data, $ArticleModel->getLastInsID());
                $this->success(L('article') . L('add') . L('success'), $rebackLink);
                return;
            } else {
                $this->error(L('article') . L('add') . L('error'), U('add', array('cate_id' => I('get.cate_id'))));
            }
        }

        $this->addEditCommon();
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
            $data                                = $this->makeData();
            isset($data['thumb']) && $thumbFile = $this->imageThumb($data['thumb'], C('SYS_ARTICLE_THUMB_WIDTH'), C('SYS_ARTICLE_THUMB_HEIGHT'));
            $resultEdit                         = $ArticleModel->mEdit($id, $data);
            if ($resultEdit) {
                $data['new_thumb'] = $thumbFile;
                $this->addEditAfterCommon($data, $id);
                $this->success(L('article') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('article') . L('edit') . L('error'), $errorGoLink);
            }
        }
        $currentConfig = C('SYS_ARTICLE_SYNC_IMAGE');
        C('SYS_ARTICLE_SYNC_IMAGE', false);
        $editInfo = $ArticleModel->mFind($id);
        C('SYS_ARTICLE_SYNC_IMAGE', $currentConfig);

        $MemberGroupModel = D('MemberGroup');
        foreach ($editInfo['access_group_id'] as &$accessGroupId) {
            $adminGroupName = $MemberGroupModel->mFindColumn($accessGroupId, 'name');
            $accessGroupId  = array('value' => $accessGroupId, 'html' => $adminGroupName);
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $extendTpl           = $ArticleCategoryModel->mFindTopColumn($editInfo['cate_id'], 'extend');
        $valExtend           = array();
        foreach ($extendTpl as $template) {
            $valExtend[$template] = ($editInfo['extend'][$template]) ? $editInfo['extend'][$template] : '';
        }
        $editInfo['extend']        = $valExtend;
        $editInfo['album']         = array_map("json_encode", $editInfo['album']);
        $editInfo['attribute_tpl'] = $ArticleCategoryModel->mFindTopColumn($editInfo['cate_id'], 'attribute');

        $this->assign('edit_info', $editInfo);

        $this->addEditCommon();
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
        $resultDel   = $ArticleModel->mDel($id);
        if ($resultDel) {
            $ManageUploadModel = D('ManageUpload');
            $ManageUploadModel->mEdit($id);
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
        $allowField = array('sort');
        if (!in_array($field, $allowField)) {
            return L('not') . L('edit') . $field;
        }

        $ArticleModel = D('Article');
        $resultEdit  = $ArticleModel->mEdit($data['id'], array($field => $data['value']));
        if ($resultEdit) {
            $data['value'] = $ArticleModel->mFindColumn($data['id'], $field);
            return array('status' => true, 'info' => $data['value']);
        } else {
            return array('status' => false, 'info' => L('edit') . L('error'));
        }
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'access_group_id':
                $MemberGroupModel                         = D('MemberGroup');
                isset($data['keyword']) && $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $memberGroupList                        = $MemberGroupModel->mSelect($where);
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = array('value' => $memberGroup['id'], 'html' => $memberGroup['name']);
                }
                break;
            case 'exttpl_id':
                isset($data['id']) && $cateId = $data['id'];
                $ArticleCategoryModel          = D('ArticleCategory');
                $extendTpl                    = $ArticleCategoryModel->mFindTopColumn($cateId, 'extend');
                foreach ($extendTpl as $template) {
                    $result['info'][$template] = '';
                }
                break;
            case 'attribute':
                if ($data['id']) {
                    $cateId              = $data['id'];
                    $ArticleCategoryModel = D('ArticleCategory');
                    $result['info']       = $ArticleCategoryModel->mFindTopColumn($cateId, 'attribute');
                } else {
                    $result = array('status' => false, 'info' => 'id error');
                }
                break;
        }
        return $result;
    }

    //构造数据
    //$isPwd 是否检测密码规则
    private function makeData()
    {
        //初始化参数
        $accessGroupId       = I('access_group_id');
        $title                 = I('title');
        $author                = I('author');
        $description           = I('description');
        $content               = I('content');
        $cateId               = I('cate_id');
        $channelId            = I('channel_id');
        $thumb                 = I('thumb');
        $addTime              = M_mktime(I('add_time'), true);
        $updateTime           = M_mktime(I('update_time'), true);
        $sort                  = I('sort');
        $isStick              = I('is_stick');
        $isAudit              = I('is_audit');
        $isAudit && $isAudit = session('backend_info.id');
        $ifShow               = I('if_show');
        $extend                = I('extend');
        $album                 = I('album');
        foreach ($album as &$imageInfo) {
            $imageInfo = json_decode(htmlspecialchars_decode($imageInfo), true);
        }
        $attribute = I('attribute');

        !$description && $description = trim(M_substr(strip_tags(htmlspecialchars_decode($content)), 100));

        $data                                                                           = array();
        ('add' == ACTION_NAME || null !== $accessGroupId) && $data['access_group_id'] = $accessGroupId;
        ('add' == ACTION_NAME || null !== $title) && $data['title']                     = $title;
        ('add' == ACTION_NAME || null !== $author) && $data['author']                   = $author;
        ('add' == ACTION_NAME || null !== $description) && $data['description']         = $description;
        ('add' == ACTION_NAME || null !== $content) && $data['content']                 = $content;
        ('add' == ACTION_NAME || null !== $cateId) && $data['cate_id']                 = $cateId;
        ('add' == ACTION_NAME || null !== $channelId) && $data['channel_id']           = $channelId;
        ('add' == ACTION_NAME || null !== $thumb) && $data['thumb']                     = $thumb;
        ('add' == ACTION_NAME || null !== $addTime) && $data['add_time']               = $addTime;
        ('add' == ACTION_NAME || null !== $updateTime) && $data['update_time']         = $updateTime;
        ('add' == ACTION_NAME || null !== $sort) && $data['sort']                       = $sort;
        ('add' == ACTION_NAME || null !== $isStick) && $data['is_stick']               = $isStick;
        ('add' == ACTION_NAME || null !== $isAudit) && $data['is_audit']               = $isAudit;
        ('add' == ACTION_NAME || null !== $ifShow) && $data['if_show']                 = $ifShow;
        ('add' == ACTION_NAME || null !== $extend) && $data['extend']                   = $extend;
        ('add' == ACTION_NAME || null !== $attribute) && $data['attribute']             = $attribute;
        ('add' == ACTION_NAME || null !== $album) && $data['album']                     = $album;
        $this->_check_aed($data);
        return $data;
    }

    //添加 编辑 之后 公共方法
    private function addEditAfterCommon(&$data, $id)
    {
        // 批量修改时不进行文件绑定
        if (is_array($id)) {
            return;
        }

        $ManageUploadModel = D('ManageUpload');
        foreach ($data['album'] as &$imageInfo) {
            $bindFile[] = $imageInfo['src'];
        }

        $bindFile[]    = $data['new_thumb'];
        $bindFile[]    = $data['thumb'];
        $contentUpload = M_get_content_upload($data['content']);
        $bindFile      = array_merge($bindFile, $contentUpload);
        $ManageUploadModel->mEdit($id, $bindFile);
    }

    //添加 编辑 公共方法
    private function addEditCommon()
    {
        $ArticleChannelModel  = D('ArticleChannel');
        $ArticleCategoryModel = D('ArticleCategory');
        $channelWhere        = $categoryWhere        = array();
        if (1 != session('backend_info.id')) {
            $channelWhere['id']  = array('in', $ArticleChannelModel->mFind_allow());
            $categoryWhere['id'] = array('in', $ArticleCategoryModel->mFind_allow());
        }
        $channelList  = $ArticleChannelModel->mSelect($channelWhere, $ArticleChannelModel->where($channelWhere)->count());
        $categoryList = $ArticleCategoryModel->mSelect_tree($categoryWhere);
        $this->assign('channel_list', $channelList);
        $this->assign('category_list', $categoryList);
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
            $data         = $ArticleModel->mFind($id);
        }
        $ArticleCategoryModel = D('ArticleCategory');
        $ArticleChannelModel  = D('ArticleChannel');
        if (!in_array($data['channel_id'], $ArticleChannelModel->mFind_allow())
            && !in_array($data['cate_id'], $ArticleCategoryModel->mFind_allow())
        ) {
            $this->error(L('none') . L('privilege') . L('handle') . L('article'), U('index'));
        }

    }
}
