<?php
// 后台 文章分类

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class ArticleCategory extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('channel_id');
        $whereValue && $where[] = ['channel_id', $whereValue];
        $whereValue = request('if_show');
        $whereValue && $where['if_show'] = (1 == $whereValue) ? 1 : 0;
        $where['parent_id'] = 0;
        $whereValue         = request('parent_id');
        $whereValue && $where[] = ['parent_id', $whereValue];
        if (1 != session('backend_info.id')) {
            $allowCategory = Model\ArticleCategory::mFindAllow();
            if (isset($where['id']) && in_array($where['id'], $allowCategory)) {
                $where['id'] = $where['id'];
            } else {
                $where['id'] = ['in', $allowCategory];
            }
        }
        //初始化翻页 和 列表数据
        $articleCategoryList = Model\ArticleCategory::mList($where, Model\ArticleCategory::where($where)->count());
        foreach ($articleCategoryList as &$articleCategory) {
            //parent_id 用完销毁不能产生歧义
            $where['parent_id']           = $articleCategory['id'];
            unset($where['parent_id']);
            $articleCategory['show']          = ($articleCategory['if_show']) ? trans('common.show') : trans('common.hidden');
            $articleCategory['ajax_api_link'] = route('Admin::ArticleCategory::ajax_api');
            $articleCategory['look_link']     = route('Home::Article::category', ['cate_id' => $articleCategory['id']]);
            $articleCategory['edit_link']     = route('Admin::ArticleCategory::edit', ['id' => $articleCategory['id']]);
            $articleCategory['del_link']      = route('Admin::ArticleCategory::del', ['id' => $articleCategory['id']]);
            $articleCategory['add_link']      = route('Admin::ArticleCategory::Article/add',
                ['cate_id' => $articleCategory['id']]);
        }

        if (request()->ajax()) {
            $this->ajaxReturn($articleCategoryList);

            return;
        }

        $assign['article_category_list'] = $articleCategoryList;

        //初始化where_info
        $whereInfo            = [];
        $whereInfo['name']    = [
            'type' => 'input',
            'name' => trans('common.article') . trans('common.category') . trans('common.name'),
        ];
        $whereInfo['if_show'] = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.show'),
            'value' => [1 => trans('common.show'), 2 => trans('common.hidden')],
        ];
        $assign['where_info'] = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.article') . trans('common.category') . trans('common.management');
        return view('admin.ArticleCategory_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData();
            $resultAdd = Model\ArticleCategory::mAdd($data);
            if ($resultAdd) {
                $this->addEditAfterCommon($data, $id);
                return $this->success(trans('common.article') . trans('common.category') . trans('common.add') . trans('common.success'),
                    route('Admin::ArticleCategory::index'));

            } else {
                return $this->error(trans('common.article') . trans('common.category') . trans('common.add') . trans('common.error'),
                    route('Admin::ArticleCategory::add'));
            }
        }

        $this->addEditCommon();
        $assign['title'] = trans('common.add') . trans('common.article') . trans('common.category');
        return view('admin.ArticleCategory_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('get.id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::ArticleCategory::index'));
        }


        if (1 != session('backend_info.id')
            && !mInArray($id, Model\ArticleCategory::mFindAllow())
        ) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.edit') . trans('common.article') . trans('common.category'),
                route('Admin::ArticleCategory::index'));
        }

        $maAllowArr = Model\ArticleCategory::mFindAllow('ma');
        if (request()->isMethod('POST')) {
            $data = $this->makeData();
            if (1 != session('backend_info.id')
                && !mInArray($id, $maAllowArr)
            ) {
                unset($data['manage_id']);
                unset($data['manage_group_id']);
                unset($data['access_group_id']);
            }
            $resultEdit = Model\ArticleCategory::mEdit($id, $data);
            if ($resultEdit) {
                $this->addEditAfterCommon($data, $id);
                return $this->success(trans('common.article') . trans('common.category') . trans('common.edit') . trans('common.success'),
                    route('Admin::ArticleCategory::index'));

            } else {
                return $this->error(trans('common.article') . trans('common.category') . trans('common.edit') . trans('common.error'),
                    route('Admin::ArticleCategory::edit', ['id' => $id]));
            }
        }

        $currentConfig = config('system.sys_article_sync_image');
        config('SYS_ARTICLE_SYNC_IMAGE', false);
        $editInfo = Model\ArticleCategory::mFind($id);
        config('SYS_ARTICLE_SYNC_IMAGE', $currentConfig);
        //如果有管理权限进行进一步数据处理
        if (mInArray($id, $maAllowArr)) {
            foreach ($editInfo['manage_id'] as &$manageId) {
                $adminName = Model\Admins::mFindColumn($manageId, 'admin_name');
                $manageId  = ['value' => $manageId, 'html' => $adminName];
            }
            $editInfo['manage_id'] = json_encode($editInfo['manage_id']);
            foreach ($editInfo['manage_group_id'] as &$manageGroupId) {
                $adminGroupName = Model\AdminGroups::mFindColumn($manageGroupId, 'name');
                $manageGroupId  = ['value' => $manageGroupId, 'html' => $adminGroupName];
            }
            $editInfo['manage_group_id'] = json_encode($editInfo['manage_group_id']);
            foreach ($editInfo['access_group_id'] as &$accessGroupId) {
                $adminGroupName = Model\MemberGroup::mFindColumn($accessGroupId, 'name');
                $accessGroupId  = ['value' => $accessGroupId, 'html' => $adminGroupName];
            }
            $editInfo['access_group_id'] = json_encode($editInfo['access_group_id']);
        }

        $assign['edit_info'] = $editInfo;

        $this->addEditCommon();
        $assign['title'] = trans('common.edit') . trans('common.article') . trans('common.category');
        return view('admin.ArticleCategory_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::ArticleCategory::index'));
        }

        //删除必须是 属主
        if (!mInArray($id, Model\ArticleCategory::mFindAllow('ma'))
            && 1 != session('backend_info.id')
        ) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.del') . trans('common.article') . trans('common.category'),
                route('Admin::ArticleCategory::index'));
        }

        //解除文章和被删除分类的关系
        $resultClean = Model\Article::mClean($id, 'cate_id');
        if (!$resultClean) {
            return $this->error(trans('common.article') . trans('common.clear') . trans('common.category') . trans('common.error'),
                route('Admin::ArticleCategory::index'));
        }

        $resultDel = Model\ArticleCategory::mDel($id);
        if ($resultDel) {
            //释放图片绑定
            Model\ManageUpload::mEdit($id);
            return $this->success(trans('common.article') . trans('common.category') . trans('common.del') . trans('common.success'),
                route('Admin::ArticleCategory::index'));

        } else {
            return $this->error(trans('common.article') . trans('common.category') . trans('common.del') . trans('common.error'),
                route('Admin::ArticleCategory::index'));
        }
    }

    //异步行编辑
    protected function _line_edit($field, $data)
    {
        $allowField = ['sort'];
        if (!in_array($field, $allowField)) {
            return trans('common.not') . trans('common.edit') . $field;
        }

        $resultEdit = Model\ArticleCategory::mEdit($data['id'], [$field => $data['value']]);
        if ($resultEdit) {
            $data['value'] = Model\ArticleCategory::mFindColumn($data['id'], $field);

            return ['status' => true, 'info' => $data['value']];
        } else {
            return ['status' => false, 'info' => trans('common.edit') . trans('common.error')];
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
                isset($data['keyword']) && $where['admin_name'] = ['like', '%' . $data['keyword'] . '%'];
                $adminUserList = Model\Admins::mList($where);
                foreach ($adminUserList as $adminUser) {
                    $result['info'][] = ['value' => $adminUser['id'], 'html' => $adminUser['admin_name']];
                }
                break;
            case 'manage_group_id':
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $adminGroupList = Model\AdminGroups::mList($where);
                foreach ($adminGroupList as $adminGroup) {
                    $result['info'][] = ['value' => $adminGroup['id'], 'html' => $adminGroup['name']];
                }
                break;
            case 'access_group_id':
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberGroupList = Model\MemberGroup::mList($where);
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
        $bindFile          = mGetContentUpload($data['content']);
        $bindFile[]        = $data['thumb'];
        Model\ManageUpload::mEdit($id, $bindFile);
    }

    //构造分类assign公共数据
    private function addEditCommon()
    {
        $id                   = request('id');
        $where                = [];
        if ($id) {
            $where['id'] = ['neq', $id];
        }

        $assign['category_list']         = Model\ArticleCategory::mList_tree($where);
        $managePrivilege                 = (1 == session('backend_info.id')) || in_array($id,
                Model\ArticleCategory::mFindAllow('ma'));
        $assign['manage_privilege']      = $managePrivilege;
        $assign['template_list']         = mScanTemplate('category', config('DEFAULT_MODULE'), 'Article');
        $assign['list_template_list']    = mScanTemplate('list_category', config('DEFAULT_MODULE'), 'Article');
        $assign['article_template_list'] = mScanTemplate('article', config('DEFAULT_MODULE'), 'Article');
    }
}
