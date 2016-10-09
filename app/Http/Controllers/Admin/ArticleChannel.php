<?php
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
        $whereValue = '';
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('if_show');
        $whereValue && $where['if_show'] = (1 == $whereValue) ? 1 : 0;
        if (1 != session('backend_info.id')) {
            $allowChannel = $ArticleChannelModel->mFind_allow();
            $where['id']  = ['in', $allowChannel];
        }
        //初始化翻页 和 列表数据
        $articleChannelList = $ArticleChannelModel->mSelect($where, true);
        $this->assign('article_channel_list', $articleChannelList);
        $this->assign('article_channel_list_count', $ArticleChannelModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo            = [];
        $whereInfo['name']    = ['type' => 'input', 'name' => trans('channel') . trans('name')];
        $whereInfo['if_show'] = ['type'  => 'select',
                                 'name'  => trans('yes') . trans('no') . trans('show'),
                                 'value' => [1 => trans('show'), 2 => trans('hidden')],
        ];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = [];
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('channel') . trans('management'));
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
            $data                = $this->makeData();
            $resultAdd           = $ArticleChannelModel->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('channel') . trans('add') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('channel') . trans('add') . trans('error'), route('add'));
            }
        }

        $this->addEditCommon();

        $this->assign('title', trans('add') . trans('channel'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $ArticleChannelModel = D('ArticleChannel');
        if (IS_AJAX) {
            $id       = request('get.id');
            $editInfo = $ArticleChannelModel->mFind($id);
            $this->ajaxReturn($this->_add_edit_category_common($editInfo));
            return;
        }

        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        if (1 != session('backend_info.id')
            && !mInArray($id, $ArticleChannelModel->mFind_allow())
        ) {
            $this->error(trans('none') . trans('privilege') . trans('edit') . trans('channel'), route('index'));
        }

        $maAllowArr = $ArticleChannelModel->mFind_allow('ma');
        if (IS_POST) {
            $data = $this->makeData();
            if (1 != session('backend_info.id')
                && !mInArray($id, $maAllowArr)
            ) {
                unset($data['manage_id']);
                unset($data['manage_group_id']);
                unset($data['access_group_id']);
            }
            $resultEdit = $ArticleChannelModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('channel') . trans('edit') . trans('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('channel') . trans('edit') . trans('error'), $errorGoLink);
            }
        }

        $editInfo = $ArticleChannelModel->mFind($id);
        //如果有管理权限进行进一步数据处理
        if (mInArray($id, $maAllowArr)) {
            $AdminModel = D('Admin');
            foreach ($editInfo['manage_id'] as &$manageId) {
                $adminName = $AdminModel->mFindColumn($manageId, 'admin_name');
                $manageId  = ['value' => $manageId, 'html' => $adminName];
            }
            $editInfo['manage_id'] = json_encode($editInfo['manage_id']);
            $AdminGroupModel       = D('AdminGroup');
            foreach ($editInfo['manage_group_id'] as &$manageGroupId) {
                $adminGroupName = $AdminGroupModel->mFindColumn($manageGroupId, 'name');
                $manageGroupId  = ['value' => $manageGroupId, 'html' => $adminGroupName];
            }
            $editInfo['manage_group_id'] = json_encode($editInfo['manage_group_id']);
            $MemberGroupModel            = D('MemberGroup');
            foreach ($editInfo['access_group_id'] as &$accessGroupId) {
                $adminGroupName = $MemberGroupModel->mFindColumn($accessGroupId, 'name');
                $accessGroupId  = ['value' => $accessGroupId, 'html' => $adminGroupName];
            }
            $editInfo['access_group_id'] = json_encode($editInfo['access_group_id']);
        }
        $this->assign('edit_info', $editInfo);
        $this->addEditCommon($editInfo);

        $this->assign('title', trans('edit') . trans('channel'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $ArticleChannelModel = D('ArticleChannel');
        //删除必须是 属主
        if (1 != session('backend_info.id')
            && !mInArray($id, $ArticleChannelModel->mFind_allow('ma'))
        ) {
            $this->error(trans('none') . trans('privilege') . trans('del') . trans('channel'), route('index'));
        }

        //解除文章和被删除频道的关系
        $ArticleModel = D('Article');
        $resultClean  = $ArticleModel->mClean($id, 'channel_id');
        if (!$resultClean) {
            $this->error(trans('article') . trans('clear') . trans('channel') . trans('error'), route('index'));
        }

        $resultDel = $ArticleChannelModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('channel') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('channel') . trans('del') . trans('error'), route('index'));
        }
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'manage_id':
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                $AdminModel = D('Admin');
                isset($data['keyword']) && $where['admin_name'] = ['like', '%' . $data['keyword'] . '%'];
                $adminUserList = $AdminModel->mSelect($where);
                foreach ($adminUserList as $adminUser) {
                    $result['info'][] = ['value' => $adminUser['id'], 'html' => $adminUser['admin_name']];
                }
                break;
            case 'manage_group_id':
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                $AdminGroupModel = D('AdminGroup');
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $adminGroupList = $AdminGroupModel->mSelect($where);
                foreach ($adminGroupList as $adminGroup) {
                    $result['info'][] = ['value' => $adminGroup['id'], 'html' => $adminGroup['name']];
                }
                break;
            case 'access_group_id':
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                $MemberGroupModel = D('MemberGroup');
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberGroupList = $MemberGroupModel->mSelect($where);
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = ['value' => $memberGroup['id'], 'html' => $memberGroup['name']];
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $name        = request('name');
        $keywords    = request('keywords');
        $description = request('description');
        $other       = request('other');
        $manageId    = request('manage_id');
        $addId       = session('backend_info.id');
        if (('add' == ACTION_NAME || null !== $manageId)
            && !in_array($addId, $manageId)
        ) {
            $manageId[] = $addId;
        }

        $manageGroupId       = request('manage_group_id');
        $accessGroupId       = request('access_group_id');
        $ifShow              = request('if_show');
        $template            = request('template');
        $categoryList        = request('category_list', []);
        $sLimit              = request('s_limit');
        $templateList        = request('template_list');
        $listTemplateList    = request('list_template_list');
        $articleTemplateList = request('article_template_list');
        $extInfo             = [];
        foreach ($categoryList as $id) {
            $extInfo[$id] = [
                's_limit'          => $sLimit[$id],
                'template'         => $templateList[$id],
                'list_template'    => $listTemplateList[$id],
                'article_template' => $articleTemplateList[$id],
            ];
        }

        $data = [];
        ('add' == ACTION_NAME || null !== $name) && $data['name'] = $name;
        ('add' == ACTION_NAME || null !== $keywords) && $data['keywords'] = $keywords;
        ('add' == ACTION_NAME || null !== $description) && $data['description'] = $description;
        ('add' == ACTION_NAME || null !== $other) && $data['other'] = $other;
        ('add' == ACTION_NAME || null !== $manageId) && $data['manage_id'] = $manageId;
        ('add' == ACTION_NAME || null !== $manageGroupId) && $data['manage_group_id'] = $manageGroupId;
        ('add' == ACTION_NAME || null !== $accessGroupId) && $data['access_group_id'] = $accessGroupId;
        ('add' == ACTION_NAME || null !== $ifShow) && $data['if_show'] = $ifShow;
        ('add' == ACTION_NAME || null !== $template) && $data['template'] = $template;
        ('add' == ACTION_NAME || null !== $extInfo) && $data['ext_info'] = $extInfo;
        return $data;
    }

    //构造频道assign公共数据
    private function addEditCommon($channelInfo = false)
    {
        $this->assign('article_category_list', $this->_add_edit_category_common($channelInfo));

        $ArticleChannelModel = D('ArticleChannel');
        $id                  = request('id');
        $managePrivilgeg     = in_array($id,
                $ArticleChannelModel->mFind_allow('ma')) || 1 == session('backend_info.id');
        $this->assign('manage_privilege', $managePrivilgeg);

        $ArticleCategoryModel = D('ArticleCategory');
        $this->assign('channel_template_list', mScanTemplate('channel', config('DEFAULT_MODULE'), 'Article'));
        $this->assign('template_list', mScanTemplate('category', config('DEFAULT_MODULE'), 'Article'));
        $this->assign('list_template_list', mScanTemplate('list_category', config('DEFAULT_MODULE'), 'Article'));
        $this->assign('article_template_list', mScanTemplate('article', config('DEFAULT_MODULE'), 'Article'));
    }

    //构造频道公共ajax
    private function _add_edit_category_common($channelInfo = false)
    {
        $ArticleCategoryModel = D('ArticleCategory');
        $where['parent_id']   = 0;
        $whereValue           = request('parent_id');
        $whereValue && $where['parent_id'] = $whereValue;

        $articleCategoryList = $ArticleCategoryModel->mSelect($where, $ArticleCategoryModel->where($where)->count());
        foreach ($articleCategoryList as &$articleCategory) {
            $articleCategory['has_child'] = $ArticleCategoryModel->where(['parent_id' => $articleCategory['id']])->count();
            if ($channelInfo && isset($channelInfo['ext_info'][$articleCategory['id']])) {
                $articleCategory['checked']          = true;
                $articleCategory['s_limit']          = $channelInfo['ext_info'][$articleCategory['id']]['s_limit'];
                $articleCategory['template']         = $channelInfo['ext_info'][$articleCategory['id']]['template'];
                $articleCategory['list_template']    = $channelInfo['ext_info'][$articleCategory['id']]['list_template'];
                $articleCategory['article_template'] = $channelInfo['ext_info'][$articleCategory['id']]['article_template'];
            }
        }
        return $articleCategoryList;
    }
}
