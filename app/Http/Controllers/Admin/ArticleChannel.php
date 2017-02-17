<?php
// 后台 文章频道

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Illuminate\Support\Facades\View;

class ArticleChannel extends Backend
{
    //列表
    public function index()
    {

        $allowChannel = Model\ArticleChannel::mFindAllow();
        //初始化翻页 和 列表数据
        $articleChannelList             = Model\ArticleChannel::where(function ($query) use ($allowChannel) {
            $name = request('name');
            if ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            }

            $if_show = request('if_show');
            if ($if_show) {
                $query->where('if_show', '=', (1 == $if_show) ? 1 : 0);
            }

            $login_id = session('backend_info.id');
            if (1 != $login_id) {
                //非root需要权限
                $query->colWhere($allowChannel);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['article_channel_list'] = $articleChannelList;

        //初始化where_info
        $whereInfo            = [];
        $whereInfo['name']    = ['type' => 'input', 'name' => trans('common.channel') . trans('common.name')];
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

        $assign['title'] = trans('common.channel') . trans('common.management');
        return view('admin.ArticleChannel_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->ajax()) {
            return $this->_add_edit_category_common()->toJson();
        }
        if (request()->isMethod('POST')) {
            $data      = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\ArticleChannel::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.channel') . trans('common.add') . trans('common.success'),
                    route('Admin::ArticleChannel::index'));
            } else {
                return $this->error(trans('common.channel') . trans('common.add') . trans('common.error'),
                    route('Admin::ArticleChannel::add'));
            }
        }

        $this->addEditCommon();
        $assign['edit_info'] = Model\ArticleChannel::columnEmptyData();
        $assign['title']     = trans('common.add') . trans('common.channel');
        return view('admin.ArticleChannel_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        if (request()->ajax()) {
            $id       = request('id');
            $editInfo = Model\ArticleChannel::colWhere($id)->first()->toArray();
            return $this->_add_edit_category_common($editInfo)->toJson();
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
            $data = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }

            if (1 != session('backend_info.id')
                && !mInArray($id, $maAllowArr)
            ) {
                unset($data['manage_id']);
                unset($data['manage_group_id']);
                unset($data['access_group_id']);
            }

            $resultEdit = false;
            Model\ArticleChannel::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                return $this->success(trans('common.channel') . trans('common.edit') . trans('common.success'),
                    route('Admin::ArticleChannel::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::ArticleChannel::index') : route('Admin::ArticleChannel::edit',
                    ['id' => $id]);
                return $this->error(trans('common.channel') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo = Model\ArticleChannel::colWhere($id)->first()->toArray();
        //如果有管理权限进行进一步数据处理
        if (mInArray($id, $maAllowArr)) {
            foreach ($editInfo['manage_id'] as &$manageId) {
                $adminName = Model\Admin::colWhere($manageId)->first()['admin_name'];
                $manageId  = ['value' => $manageId, 'html' => $adminName];
            }
            foreach ($editInfo['manage_group_id'] as &$manageGroupId) {
                $adminGroupName = Model\AdminGroup::colWhere($manageGroupId)->first()['name'];
                $manageGroupId  = ['value' => $manageGroupId, 'html' => $adminGroupName];
            }
            foreach ($editInfo['access_group_id'] as &$accessGroupId) {
                $adminGroupName = Model\MemberGroup::colWhere($accessGroupId)->first()['name'];
                $accessGroupId  = ['value' => $accessGroupId, 'html' => $adminGroupName];
            }
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
        $resultClean = Model\Article::colWhere($id, 'channel_id')->delete();
        if (!$resultClean) {
            return $this->error(trans('common.article') . trans('common.clear') . trans('common.channel') . trans('common.error'),
                route('Admin::ArticleChannel::index'));
        }

        $resultDel = Model\ArticleChannel::destroy($id);
        if ($resultDel) {
            return $this->success(trans('common.channel') . trans('common.del') . trans('common.success'),
                route('Admin::ArticleChannel::index'));
        } else {
            return $this->error(trans('common.channel') . trans('common.del') . trans('common.error'),
                route('Admin::ArticleChannel::index'));
        }
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'manage_id':
                Model\Admin::where(function ($query) use ($data) {
                    if (isset($data['inserted'])) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword'])) {
                        $query->where('admin_name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get()->each(function ($item, $key) use (&$result) {
                    $result['info'][] = ['value' => $item['id'], 'html' => $item['admin_name']];
                });
                break;
            case 'manage_group_id':
                Model\AdminGroup::where(function ($query) use ($data) {
                    if (isset($data['inserted'])) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword'])) {
                        $query->where('name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get()->each(function ($item, $key) use (&$result) {
                    $result['info'][] = ['value' => $item['id'], 'html' => $item['name']];
                });
                break;
            case 'access_group_id':
                Model\MemberGroup::where(function ($query) use ($data) {
                    if (isset($data['inserted'])) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword'])) {
                        $query->where('name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get()->each(function ($item, $key) use (&$result) {
                    $result['info'][] = ['value' => $item['id'], 'html' => $item['name']];
                });
                break;
        }
        return $result;
    }

    //构造数据
    private function makeData($type)
    {
        //初始化参数
        $name        = request('name');
        $keywords    = request('keywords');
        $description = request('description');
        $other       = request('other');
        $manageId    = request('manage_id');
        $addId       = session('backend_info.id');
        if (('add' == $type || null !== $manageId)
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
        if ('add' == $type || null !== $name) {
            $data['name'] = $name;
        }
        if ('add' == $type || null !== $keywords) {
            $data['keywords'] = $keywords;
        }
        if ('add' == $type || null !== $description) {
            $data['description'] = $description;
        }
        if ('add' == $type || null !== $other) {
            $data['other'] = $other;
        }
        if ('add' == $type || null !== $manageId) {
            $data['manage_id'] = $manageId;
        }
        if ('add' == $type || null !== $manageGroupId) {
            $data['manage_group_id'] = $manageGroupId;
        }
        if ('add' == $type || null !== $accessGroupId) {
            $data['access_group_id'] = $accessGroupId;
        }
        if ('add' == $type || null !== $ifShow) {
            $data['if_show'] = $ifShow;
        }
        if ('add' == $type || null !== $template) {
            $data['template'] = $template;
        }
        if ('add' == $type || null !== $extInfo) {
            $data['ext_info'] = $extInfo;
        }
        return $data;
    }

    //构造频道assign公共数据
    private function addEditCommon($channelInfo = false)
    {
        $assign['article_category_list'] = $this->_add_edit_category_common($channelInfo);

        $id              = request('id');
        $managePrivilege = Model\ArticleChannel::mFindAllow('ma')->search($id) || 1 == session('backend_info.id');
        $assign['manage_privilege'] = $managePrivilege;

        $assign['channel_template_list'] = mScanTemplate('channel', 'Article');
        $assign['template_list']         = mScanTemplate('category', 'Article');
        $assign['list_template_list']    = mScanTemplate('list_category', 'Article');
        $assign['article_template_list'] = mScanTemplate('article', 'Article');
        View::share($assign);
    }

    //构造频道公共ajax
    private function _add_edit_category_common($channelInfo = false)
    {
        $where['parent_id'] = 0;
        $whereValue         = request('parent_id');
        $whereValue && $where[] = ['parent_id', $whereValue];

        $articleCategoryList = Model\ArticleCategory::where($where)->get();
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
