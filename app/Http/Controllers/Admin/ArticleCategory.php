<?php
// 后台 文章分类

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Illuminate\Support\Facades\View;

class ArticleCategory extends Backend
{
    //列表
    public function index()
    {
        $allowCategory = Model\ArticleChannel::mFindAllow()->toArray();
        //初始化翻页 和 列表数据
        $articleCategoryList = Model\ArticleCategory::where(function ($query) use ($allowCategory) {
            $parent_id = request('parent_id');
            if ($parent_id) {
                $query->where('parent_id', '=', $parent_id);
            } else {
                $query->where('parent_id', '=', 0);
            }

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
                $query->colWhere($allowCategory);
            }

        })->get();
        foreach ($articleCategoryList as &$articleCategory) {
            $articleCategory['has_child']     = Model\ArticleCategory::where('parent_id',
                $articleCategory->id)->count();
            $articleCategory['show']          = ($articleCategory['if_show']) ? trans('common.show') : trans('common.hidden');
            $articleCategory['ajax_api_link'] = route('Admin::ArticleCategory::ajax_api');
            $articleCategory['look_link']     = route('Home::Article::category', ['cate_id' => $articleCategory['id']]);
            $articleCategory['edit_link']     = route('Admin::ArticleCategory::edit', ['id' => $articleCategory['id']]);
            $articleCategory['del_link']      = route('Admin::ArticleCategory::del', ['id' => $articleCategory['id']]);
            $articleCategory['add_link']      = route('Admin::ArticleCategory::add',
                ['cate_id' => $articleCategory['id']]);
        }

        if (request()->ajax()) {
            return $articleCategoryList->toJson();
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
            $data = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\ArticleCategory::create($data);
            if ($resultAdd) {
                $this->addEditAfterCommon($data, $resultAdd->id);
                return $this->success(trans('common.article') . trans('common.category') . trans('common.add') . trans('common.success'),
                    route('Admin::ArticleCategory::index'));

            } else {
                return $this->error(trans('common.article') . trans('common.category') . trans('common.add') . trans('common.error'),
                    route('Admin::ArticleCategory::add'));
            }
        }

        $this->addEditCommon();
        $assign['edit_info']                  = Model\ArticleCategory::columnEmptyData();
        $assign['edit_info']['category_tree'] = Model\ArticleCategory::mCategoryTree(0);
        $assign['title']                      = trans('common.add') . trans('common.article') . trans('common.category');
        return view('admin.ArticleCategory_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::ArticleCategory::index'));
        }


        if (1 != session('backend_info.id')
            && !mInArray($id, Model\ArticleCategory::mFindAllow()->toArray())
        ) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.edit') . trans('common.article') . trans('common.category'),
                route('Admin::ArticleCategory::index'));
        }

        $maAllowArr = Model\ArticleCategory::mFindAllow('ma')->toArray();
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
            Model\ArticleCategory::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                $this->addEditAfterCommon($data, $id);
                return $this->success(trans('common.article') . trans('common.category') . trans('common.edit') . trans('common.success'),
                    route('Admin::ArticleCategory::index'));

            } else {
                return $this->error(trans('common.article') . trans('common.category') . trans('common.edit') . trans('common.error'),
                    route('Admin::ArticleCategory::edit', ['id' => $id]));
            }
        }

        $editInfo = Model\ArticleCategory::colWhere($id)->first()->toArray();
        //如果有管理权限进行进一步数据处理
        if (mInArray($id, $maAllowArr)) {
            $manageIds = [];
            Model\Admin::colWhere($editInfo['manage_id'])->each(function ($item, $key) use (&$manageIds) {
                $manageIds[] = ['value' => $item['id'], 'html' => $item['admin_name']];
            });
            $editInfo['manage_id'] = $manageIds;

            $manageGroupIds = [];
            Model\AdminGroup::colWhere($editInfo['manage_group_id'])->each(function ($item, $key) use (&$manageGroupIds
            ) {
                $manageGroupIds[] = ['value' => $item['id'], 'html' => $item['name']];
            });
            $editInfo['manage_group_id'] = $manageGroupIds;

            $accessGroupIds = [];
            Model\MemberGroup::colWhere($editInfo['access_group_id'])->each(function ($item, $key) use (&$accessGroupIds
            ) {
                $accessGroupIds[] = ['value' => $item['id'], 'html' => $item['name']];
            });
            $editInfo['access_group_id'] = $accessGroupIds;
        }

        $editInfo['category_tree'] = Model\ArticleCategory::mCategoryTree($editInfo['parent_id']);
        $assign['edit_info']       = $editInfo;

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
        if (!mInArray($id, Model\ArticleCategory::mFindAllow('ma')->toArray())
            && 1 != session('backend_info.id')
        ) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.del') . trans('common.article') . trans('common.category'),
                route('Admin::ArticleCategory::index'));
        }

        //解除文章和被删除分类的关系
        $resultClean = true;
        Model\Article::colWhere($id, 'cate_id')->get()->each(function ($item, $key) use (&$resultClean) {
            $resultClean = $item->update(['cate_id' => 0]);
            return $resultClean;
        });
        if (!$resultClean) {
            return $this->error(trans('common.article') . trans('common.clear') . trans('common.category') . trans('common.error'),
                route('Admin::ArticleCategory::index'));
        }

        $resultDel = Model\ArticleCategory::destroy($id);
        if ($resultDel) {
            Model\ArticleCategory::colWhere($id, 'parent_id')->get()->each(function ($item, $key) {
                $item->update(['parent_id' => 0]);
            });
            //释放图片绑定
            Model\ManageUpload::bindFile($id);
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
            return ['status' => false, 'info' => trans('common.not') . trans('common.edit') . $field];
        }

        $edit_data  = [
            $field => (0 <= $data['value'] && 100 >= $data['value']) ? $data['value'] : 100,
        ];
        $resultEdit = Model\ArticleCategory::colWhere($data['id'])->first()->update($edit_data);
        if ($resultEdit) {
            $data['value'] = Model\ArticleCategory::colWhere($data['id'])->first()[$field];
            return ['status' => true, 'info' => $data['value']];
        } else {
            return ['status' => false, 'info' => trans('common.edit') . trans('common.error')];
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
            case 'parent_id':
                $where[]        = ['parent_id', '=', ($data['id']) ? $data['id'] : 0];
                $where[]        = ['parent_id', '!=', request('inserted')];
                $categoryList   = Model\ArticleCategory::select(['id', 'name'])->where($where)->get();
                $result['info'] = $categoryList;
                break;
        }

        return $result;
    }

    //构造数据
    private function makeData($type)
    {
        //初始化参数
        $parentId = request('parent_id');
        $name     = request('name');
        $manageId = request('manage_id');
        $addId    = session('backend_info.id');
        if (('add' == $type || null !== $manageId)
            && (!is_array($manageId) || !in_array($addId, $manageId))
        ) {
            $manageId = [$addId];
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
        if (is_array($postAttribute)) {
            foreach ($postAttribute as $attrs) {
                $attribute[$attrs['name']] = [];
                foreach ($attrs['value'] as $attrValue) {
                    $attribute[$attrs['name']][] = $attrValue;
                }
            }
        }
        $template        = request('template');
        $listTemplate    = request('list_template');
        $articleTemplate = request('article_template');

        $data = [];
        if ('add' == $type || null !== $parentId) {
            $data['parent_id'] = $parentId;
        }
        if ('add' == $type || null !== $name) {
            $data['name'] = $name;
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
        if ('add' == $type || null !== $thumb) {
            $data['thumb'] = $thumb;
        }
        if ('add' == $type || null !== $sort) {
            $data['sort'] = $sort;
        }
        if ('add' == $type || null !== $sLimit) {
            $data['s_limit'] = $sLimit;
        }
        if ('add' == $type || null !== $ifShow) {
            $data['if_show'] = $ifShow;
        }
        if ('add' == $type || null !== $isContent) {
            $data['is_content'] = $isContent;
        }
        if ('add' == $type || null !== $content) {
            $data['content'] = $content;
        }
        if ('add' == $type || null !== $extend) {
            $data['extend'] = $extend;
        }
        if ('add' == $type || null !== $attribute) {
            $data['attribute'] = $attribute;
        }
        if ('add' == $type || null !== $template) {
            $data['template'] = $template;
        }
        if ('add' == $type || null !== $listTemplate) {
            $data['list_template'] = $listTemplate;
        }
        if ('add' == $type || null !== $articleTemplate) {
            $data['article_template'] = $articleTemplate;
        }

        //只有顶级可以设置扩展模板和属性
        if (isset($data['parent_id']) && 0 < $data['parent_id']) {
            $data['extend']    = [];
            $data['attribute'] = [];
        }

        return $data;
    }

    //添加 编辑 之后 公共方法
    private function addEditAfterCommon(&$data, $id)
    {
        $bindFile   = mGetContentUpload($data['content']);

        if (isset($data['thumb']) && $data['thumb']) {
            $bindFile[] = $data['thumb'];
            $bindFile[] = $this->imageThumb($data['thumb'],
                config('system.sys_article_thumb_width'),
                config('system.sys_article_thumb_height'));
        }

        Model\ManageUpload::bindFile($id, $bindFile);
    }

    //构造分类assign公共数据
    private function addEditCommon()
    {
        $id                              = request('id');
        $assign['manage_privilege']      = Model\ArticleCategory::mFindAllow('ma')->search($id) || 1 == session('backend_info.id');
        $assign['template_list']         = mScanTemplate('category', 'Article');
        $assign['list_template_list']    = mScanTemplate('list_category', 'Article');
        $assign['article_template_list'] = mScanTemplate('article', 'Article');
        View::share($assign);
    }
}
