<?php
// 后台 文章频道

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class ArticleChannel extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('if_show');
        $whereValue && $where['if_show'] = (1 == $whereValue) ? 1 : 0;
        if (1 != session('backend_info.id')) {
            $allowChannel = Model\ArticleChannel::mFindAllow();
            $where['id']  = ['in', $allowChannel];
        }
        //初始化翻页 和 列表数据
        $articleChannelList                   = Model\ArticleChannel::mList($where, true);
        $assign['article_channel_list']       = $articleChannelList;

        //初始化where_info
        $whereInfo            = [];
        $whereInfo['name']    = ['type' => 'input', 'name' => trans('common.channel') . trans('common.name')];
        $whereInfo['if_show'] = ['type'  => 'select',
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

        $assign['title'] = trans('common.channel') . trans('common.management');
        return view('admin.ArticleChannel_index');
    }

    //新增
    public function add()
    {
        if (request()->ajax()) {
            $this->ajaxReturn($this->_add_edit_category_common(), $assign);
            return;
        }
        if (request()->isMethod('POST')) {
            $data      = $this->makeData();
            $resultAdd = Model\ArticleChannel::mAdd($data);
            if ($resultAdd) {
                return $this->success(trans('common.channel') . trans('common.add') . trans('common.success'),
                    route('Admin::ArticleChannel::index'));
                return;
            } else {
                return $this->error(trans('common.channel') . trans('common.add') . trans('common.error'),
                    route('Admin::ArticleChannel::add'));
            }
        }

        $this->addEditCommon();

        $assign['title'] = trans('common.add') . trans('common.channel');
        return view('admin.ArticleChannel_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        if (request()->ajax()) {
            $id       = request('get.id');
            $editInfo = Model\ArticleChannel::mFind($id);
            $this->ajaxReturn($this->_add_edit_category_common($editInfo));
            return;
        }

        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::ArticleChannel::index'));
        }

        if (1 != session('backend_info.id')
            && !mInArray($id, Model\ArticleChannel::mFindAllow())
        ) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.edit') . trans('common.channel'),
                route('Admin::ArticleChannel::index'));
        }

        $maAllowArr = Model\ArticleChannel::mFindAllow('ma');
        if (request()->isMethod('POST')) {
            $data = $this->makeData();
            if (1 != session('backend_info.id')
                && !mInArray($id, $maAllowArr)
            ) {
                unset($data['manage_id']);
                unset($data['manage_group_id']);
                unset($data['access_group_id']);
            }
            $resultEdit = Model\ArticleChannel::mEdit($id, $data);
            if ($resultEdit) {
                return $this->success(trans('common.channel') . trans('common.edit') . trans('common.success'),
                    route('Admin::ArticleChannel::index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::ArticleChannel::index') : route('Admin::ArticleChannel::edit',
                    ['id' => $id]);
                return $this->error(trans('common.channel') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo = Model\ArticleChannel::mFind($id);
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
        $this->addEditCommon($editInfo);

        $assign['title'] = trans('common.edit') . trans('common.channel');
        return view('admin.ArticleChannel_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::ArticleChannel::index'));
        }

        //删除必须是 属主
        if (1 != session('backend_info.id')
            && !mInArray($id, Model\ArticleChannel::mFindAllow('ma'))
        ) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.del') . trans('common.channel'),
                route('Admin::ArticleChannel::index'));
        }

        //解除文章和被删除频道的关系
        $resultClean = Model\Article::mClean($id, 'channel_id');
        if (!$resultClean) {
            return $this->error(trans('common.article') . trans('common.clear') . trans('common.channel') . trans('common.error'),
                route('Admin::ArticleChannel::index'));
        }

        $resultDel = Model\ArticleChannel::mDel($id);
        if ($resultDel) {
            return $this->success(trans('common.channel') . trans('common.del') . trans('common.success'),
                route('Admin::ArticleChannel::index'));
            return;
        } else {
            return $this->error(trans('common.channel') . trans('common.del') . trans('common.error'),
                route('Admin::ArticleChannel::index'));
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
        $assign['article_category_list'] = $this->_add_edit_category_common($channelInfo);

        $id                  = request('id');
        $managePrivilgeg     = in_array($id,
                Model\ArticleChannel::mFindAllow('ma')) || 1 == session('backend_info.id');
        $assign['manage_privilege'] = $managePrivilgeg;

        $assign['channel_template_list'] = mScanTemplate('channel', config('DEFAULT_MODULE'), 'Article');
        $assign['template_list']         = mScanTemplate('category', config('DEFAULT_MODULE'), 'Article');
        $assign['list_template_list']    = mScanTemplate('list_category', config('DEFAULT_MODULE'), 'Article');
        $assign['article_template_list'] = mScanTemplate('article', config('DEFAULT_MODULE'), 'Article');
    }

    //构造频道公共ajax
    private function _add_edit_category_common($channelInfo = false)
    {
        $where['parent_id']   = 0;
        $whereValue           = request('parent_id');
        $whereValue && $where[] = ['parent_id', $whereValue];

        $articleCategoryList = Model\ArticleCategory::mList($where, Model\ArticleCategory::where($where)->count());
        foreach ($articleCategoryList as &$articleCategory) {
            $articleCategory['has_child'] = Model\ArticleCategory::where(['parent_id' => $articleCategory['id']])->count();
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
