<?php
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
        $whereValue = '';
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('channel_id');
        $whereValue && $where['channel_id'] = $whereValue;
        $whereValue = request('if_show');
        $whereValue && $where['if_show'] = (1 == $whereValue) ? 1 : 0;
        $where['parent_id'] = 0;
        $whereValue         = request('parent_id');
        $whereValue && $where['parent_id'] = $whereValue;
        if (1 != session('backend_info.id')) {
            $allowCategory = $ArticleCategoryModel->mFind_allow();
            if (isset($where['id']) && in_array($where['id'], $allowCategory)) {
                $where['id'] = $where['id'];
            } else {
                $where['id'] = ['in', $allowCategory];
            }
        }
        //初始化翻页 和 列表数据
        $articleCategoryList = $ArticleCategoryModel->mSelect($where, $ArticleCategoryModel->where($where)->count());
        foreach ($articleCategoryList as &$articleCategory) {
            //parent_id 用完销毁不能产生歧义
            $where['parent_id']           = $articleCategory['id'];
            $articleCategory['has_child'] = $ArticleCategoryModel->mGetPageCount($where);
            unset($where['parent_id']);
            $articleCategory['show']          = ($articleCategory['if_show']) ? trans('show') : trans('hidden');
            $articleCategory['ajax_api_link'] = route('ajax_api');
            $articleCategory['look_link']     = route('Home/Article/category', ['cate_id' => $articleCategory['id']]);
            $articleCategory['edit_link']     = route('edit', ['id' => $articleCategory['id']]);
            $articleCategory['del_link']      = route('del', ['id' => $articleCategory['id']]);
            $articleCategory['add_link']      = route('Article/add', ['cate_id' => $articleCategory['id']]);
        }

        if (IS_AJAX) {
            $this->ajaxReturn($articleCategoryList);

            return;
        }

        $this->assign('article_category_list', $articleCategoryList);

        //初始化where_info
        $whereInfo            = [];
        $whereInfo['name']    = ['type' => 'input', 'name' => trans('article') . trans('category') . trans('name')];
        $whereInfo['if_show'] = [
            'type'  => 'select',
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

        $this->assign('title', trans('article') . trans('category') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $ArticleCategoryModel = D('ArticleCategory');
            $data                 = $this->makeData();
            $resultAdd            = $ArticleCategoryModel->mAdd($data);
            if ($resultAdd) {
                $this->addEditAfterCommon($data, $id);
                $this->success(trans('article') . trans('category') . trans('add') . trans('success'), route('index'));

                return;
            } else {
                $this->error(trans('article') . trans('category') . trans('add') . trans('error'), route('add'));
            }
        }

        $this->addEditCommon();
        $this->assign('title', trans('add') . trans('article') . trans('category'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('get.id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $ArticleCategoryModel = D('ArticleCategory');

        if (1 != session('backend_info.id')
            && !mInArray($id, $ArticleCategoryModel->mFind_allow())
        ) {
            $this->error(trans('none') . trans('privilege') . trans('edit') . trans('article') . trans('category'),
                route('index'));
        }

        $maAllowArr = $ArticleCategoryModel->mFind_allow('ma');
        if (IS_POST) {
            $data = $this->makeData();
            if (1 != session('backend_info.id')
                && !mInArray($id, $maAllowArr)
            ) {
                unset($data['manage_id']);
                unset($data['manage_group_id']);
                unset($data['access_group_id']);
            }
            $resultEdit = $ArticleCategoryModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->addEditAfterCommon($data, $id);
                $this->success(trans('article') . trans('category') . trans('edit') . trans('success'), route('index'));

                return;
            } else {
                $this->error(trans('article') . trans('category') . trans('edit') . trans('error'),
                    route('edit', ['id' => $id]));
            }
        }

        $currentConfig = config('system.sys_article_sync_image');
        config('SYS_ARTICLE_SYNC_IMAGE', false);
        $editInfo = $ArticleCategoryModel->mFind($id);
        config('SYS_ARTICLE_SYNC_IMAGE', $currentConfig);
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

        $this->addEditCommon();
        $this->assign('title', trans('edit') . trans('article') . trans('category'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $ArticleCategoryModel = D('ArticleCategory');
        //删除必须是 属主
        if (!mInArray($id, $ArticleCategoryModel->mFind_allow('ma'))
            && 1 != session('backend_info.id')
        ) {
            $this->error(trans('none') . trans('privilege') . trans('del') . trans('article') . trans('category'),
                route('index'));
        }

        //解除文章和被删除分类的关系
        $ArticleModel = D('Article');
        $resultClean  = $ArticleModel->mClean($id, 'cate_id');
        if (!$resultClean) {
            $this->error(trans('article') . trans('clear') . trans('category') . trans('error'), route('index'));
        }

        $resultDel = $ArticleCategoryModel->mDel($id);
        if ($resultDel) {
            //释放图片绑定
            $ManageUploadModel = D('ManageUpload');
            $ManageUploadModel->mEdit($id);
            $this->success(trans('article') . trans('category') . trans('del') . trans('success'), route('index'));

            return;
        } else {
            $this->error(trans('article') . trans('category') . trans('del') . trans('error'), route('index'));
        }
    }

    //异步行编辑
    protected function _line_edit($field, $data)
    {
        $allowField = ['sort'];
        if (!in_array($field, $allowField)) {
            return trans('not') . trans('edit') . $field;
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $resultEdit           = $ArticleCategoryModel->mEdit($data['id'], [$field => $data['value']]);
        if ($resultEdit) {
            $data['value'] = $ArticleCategoryModel->mFindColumn($data['id'], $field);

            return ['status' => true, 'info' => $data['value']];
        } else {
            return ['status' => false, 'info' => trans('edit') . trans('error')];
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
        $parentId = request('parent_id');
        $name     = request('name');
        $manageId = request('manage_id');
        $addId    = session('backend_info.id');
        if (('add' == ACTION_NAME || null !== $manageId)
            && !in_array($addId, $manageId)
        ) {
            $manageId[] = $addId;
        }

        $manageGroupId = request('manage_group_id');
        $accessGroupId = request('access_group_id');
        $thumb         = request('thumb');
        $sort          = request('sort');
        $sLimit        = request('s_limit');
        $ifShow        = request('if_show');
        $isContent     = request('is_content');
        $content       = request('content');
        $extend        = request('extend');
        $postAttribute = request('attribute');
        $attribute     = [];
        foreach ($postAttribute as $attrs) {
            $attribute[$attrs['name']] = [];
            foreach ($attrs['value'] as $attrValue) {
                $attribute[$attrs['name']][] = $attrValue;
            }
        }
        $template        = request('template');
        $listTemplate    = request('list_template');
        $articleTemplate = request('article_template');

        $data = [];
        ('add' == ACTION_NAME || null !== $parentId) && $data['parent_id'] = $parentId;
        ('add' == ACTION_NAME || null !== $name) && $data['name'] = $name;
        ('add' == ACTION_NAME || null !== $manageId) && $data['manage_id'] = $manageId;
        ('add' == ACTION_NAME || null !== $manageGroupId) && $data['manage_group_id'] = $manageGroupId;
        ('add' == ACTION_NAME || null !== $accessGroupId) && $data['access_group_id'] = $accessGroupId;
        ('add' == ACTION_NAME || null !== $thumb) && $data['thumb'] = $thumb;
        ('add' == ACTION_NAME || null !== $sort) && $data['sort'] = $sort;
        ('add' == ACTION_NAME || null !== $sLimit) && $data['s_limit'] = $sLimit;
        ('add' == ACTION_NAME || null !== $ifShow) && $data['if_show'] = $ifShow;
        ('add' == ACTION_NAME || null !== $isContent) && $data['is_content'] = $isContent;
        ('add' == ACTION_NAME || null !== $content) && $data['content'] = $content;
        ('add' == ACTION_NAME || null !== $extend) && $data['extend'] = $extend;
        ('add' == ACTION_NAME || null !== $attribute) && $data['attribute'] = $attribute;
        ('add' == ACTION_NAME || null !== $template) && $data['template'] = $template;
        ('add' == ACTION_NAME || null !== $listTemplate) && $data['list_template'] = $listTemplate;
        ('add' == ACTION_NAME || null !== $articleTemplate) && $data['article_template'] = $articleTemplate;

        return $data;
    }

    //添加 编辑 之后 公共方法
    private function addEditAfterCommon(&$data, $id)
    {
        $ManageUploadModel = D('ManageUpload');
        $bindFile          = mGetContentUpload($data['content']);
        $bindFile[]        = $data['thumb'];
        $ManageUploadModel->mEdit($id, $bindFile);
    }

    //构造分类assign公共数据
    private function addEditCommon()
    {
        $ArticleCategoryModel = D('ArticleCategory');
        $id                   = request('id');
        $where                = [];
        if ($id) {
            $where['id'] = ['neq', $id];
        }

        $this->assign('category_list', $ArticleCategoryModel->mSelect_tree($where));
        $managePrivilege = (1 == session('backend_info.id')) || in_array($id, $ArticleCategoryModel->mFind_allow('ma'));
        $this->assign('manage_privilege', $managePrivilege);
        $this->assign('template_list', mScanTemplate('category', config('DEFAULT_MODULE'), 'Article'));
        $this->assign('list_template_list', mScanTemplate('list_category', config('DEFAULT_MODULE'), 'Article'));
        $this->assign('article_template_list', mScanTemplate('article', config('DEFAULT_MODULE'), 'Article'));
    }
}
