<?php
// 后台 文章管理

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Illuminate\Support\Facades\View;

class Article extends Backend
{
    //列表
    public function index()
    {
        $allowCategory = Model\ArticleCategory::mFindAllow();
        $allowChannel  = Model\ArticleChannel::mFindAllow();
        //初始化翻页 和 列表数据
        $articleList = Model\Article::where(function ($query) use ($allowCategory, $allowChannel) {
            $title = request('title');
            if ($title) {
                $query->where('title', 'like', '%' . $title . '%');
            }

            $is_audit = request('is_audit');
            if ($is_audit) {
                $query->where('is_audit', (1 == $is_audit) ? '>' : '=', 0);
            }

            $if_show = request('if_show');
            if ($if_show) {
                $query->where('if_show', '=', (1 == $if_show) ? 1 : 0);
            }

            $cate_id = request('cate_id');
            if ($cate_id) {
                $query->whereIn('cate_id', $this->findCateChildIds($cate_id));
            }

            $channel_id = request('channel_id');
            if ($channel_id) {
                $query->where('channel_id', $channel_id);
            }

            $login_id = session('backend_info.id');
            if (1 != $login_id) {
                //非root需要权限
                $query->whereIn('cate_id', $allowCategory);
                $query->whereIn('channel_id', $allowChannel);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($articleList as &$article) {
            $article['channel_name'] = trans('common.empty');
            $article['cate_name']    = trans('common.empty');
            if ($article['channel_id']) {
                $article['channel_name'] = Model\ArticleChannel::colWhere($article['channel_id'])->first()['name'];
            }
            if ($article['cate_id']) {
                $article['cate_name'] = Model\ArticleCategory::colWhere($article['cate_id'])->first()['name'];
            }
        }
        $assign['article_list'] = $articleList;

        //初始化where_info
        $categoryList       = Model\ArticleCategory::colWhere($allowCategory)->get();
        $searchCategoryList = [];
        foreach ($categoryList as $category) {
            $searchCategoryList[$category['id']] = $category['name'];
        }
        $channelList           = Model\ArticleChannel::colWhere($allowChannel)->get();
        $searchChannelList     = [];
        foreach ($channelList as $channel) {
            $searchChannelList[$channel['id']] = $channel['name'];
        }


        //初始化where_info
        $whereInfo               = [];
        $whereInfo['title']      = ['type' => 'input', 'name' => trans('common.title')];
        $whereInfo['cate_id']    = [
            'type'  => 'select',
            'name'  => trans('common.category'),
            'value' => $searchCategoryList,
        ];
        $whereInfo['channel_id'] = [
            'type'  => 'select',
            'name'  => trans('common.channel'),
            'value' => $searchChannelList,
        ];
        $whereInfo['is_audit'] = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.audit'),
            'value' => [
                1 => trans('common.audit'),
                2 => trans('common.none') . trans('common.audit'),
            ],
        ];
        $whereInfo['if_show']  = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.show'),
            'value' => [1 => trans('common.show'), 2 => trans('common.hidden')],
        ];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.article') . trans('common.management');
        return view('admin.Article_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            isset($data['thumb']) && $thumbFile = $this->imageThumb($data['thumb'],
                config('system.sys_article_thumb_width'),
                config('system.sys_article_thumb_height'));
            $resultAdd = Model\Article::create($data);
            //增加了一个分类快捷添加文章的回跳链接
            $rebackLink = request('cate_id') ? route('Admin::ArticleCategory::index') : route('Admin::Article::index');
            if ($resultAdd) {
                $data['new_thumb'] = $thumbFile;
                $this->addEditAfterCommon($data, $resultAdd->id);
                return $this->success(trans('common.article') . trans('common.add') . trans('common.success'),
                    $rebackLink);
            } else {
                return $this->error(trans('common.article') . trans('common.add') . trans('common.error'),
                    route('Admin::Article::add', ['cate_id' => request('cate_id')]));
            }
        }

        $this->addEditCommon();
        $assign['edit_info']                  = Model\Article::columnEmptyData();
        $assign['edit_info']['attribute_tpl'] = [];
        $assign['title']                      = trans('common.article') . trans('common.add');
        return view('admin.Article_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Article::index'));
        }

        if (request()->isMethod('POST')) {
            $data = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }

            isset($data['thumb']) && $thumbFile = $this->imageThumb($data['thumb'],
                config('system.sys_article_thumb_width'),
                config('system.sys_article_thumb_height'));

            $resultEdit = false;
            Model\Article::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                $data['new_thumb'] = $thumbFile;
                $this->addEditAfterCommon($data, $id);
                return $this->success(trans('common.article') . trans('common.edit') . trans('common.success'),
                    route('Admin::Article::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Article::index') : route('Admin::Article::edit',
                    ['id' => $id]);
                return $this->error(trans('common.article') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }
        $currentConfig = config('system.sys_article_sync_image');
        config('SYS_ARTICLE_SYNC_IMAGE', false);
        $editInfo = Model\Article::colWhere($id)->first()->toArray();
        config('SYS_ARTICLE_SYNC_IMAGE', $currentConfig);

        foreach ($editInfo['access_group_id'] as &$accessGroupId) {
            $adminGroupName = Model\MemberGroup::colWhere($accessGroupId)->first()['name'];
            $accessGroupId  = ['value' => $accessGroupId, 'html' => $adminGroupName];
        }

        $extendTpl                 = $this->findTopCategory($editInfo['cate_id'], 'extend');
        $valExtend = [];
        foreach ($extendTpl as $template) {
            $valExtend[$template] = (isset($editInfo['extend'][$template])) ? $editInfo['extend'][$template] : '';
        }
        $editInfo['extend']        = $valExtend;
        $editInfo['attribute_tpl'] = $this->findTopCategory($editInfo['cate_id'], 'attribute');

        $assign['edit_info'] = $editInfo;

        $this->addEditCommon();
        $assign['title'] = trans('common.article') . trans('common.edit');
        return view('admin.Article_addedit', $assign);
    }

    //删除
    public function del()
    {
        $this->_check_aed();
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Article::index'));
        }
        $resultDel = Model\Article::destroy($id);
        if ($resultDel) {
            Model\ManageUpload::bindFile($id);
            return $this->success(trans('common.article') . trans('common.del') . trans('common.success'),
                route('Admin::Article::index'));
        } else {
            return $this->error(trans('common.article') . trans('common.del') . trans('common.error'),
                route('Admin::Article::index'));
        }
    }

    //配置
    public function setting()
    {
        if (request()->isMethod('POST')) {
            //表单提交的名称
            $col = [
                'sys_article_sync_image',
                'sys_article_pn_limit',
                'sys_article_thumb_width',
                'sys_article_thumb_height',
            ];
            return $this->_put_config($col, 'system');
        }

        $assign['title'] = trans('common.article') . trans('common.config');
        return view('admin.Article_setting', $assign);
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
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'access_group_id':
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberGroupList = Model\MemberGroup::where($where)->get();
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = ['value' => $memberGroup['id'], 'html' => $memberGroup['name']];
                }
                break;
            case 'exttpl_id':
                isset($data['id']) && $cateId = $data['id'];
                $extendTpl = $this->findTopCategory($cateId, 'extend');
                foreach ($extendTpl as $template) {
                    $result['info'][$template] = '';
                }
                break;
            case 'attribute':
                if ($data['id']) {
                    $cateId         = $data['id'];
                    $result['info'] = $this->findTopCategory($cateId, 'attribute');
                } else {
                    $result = ['status' => false, 'info' => 'id error'];
                }
                break;
        }
        return $result;
    }

    //构造数据
    //$isPwd 是否检测密码规则
    private function makeData($type)
    {
        //初始化参数
        $accessGroupId = request('access_group_id');
        $title         = request('title');
        $author        = request('author');
        $description   = request('description');
        $content       = request('content');
        $cateId        = request('cate_id');
        $channelId     = request('channel_id');
        $thumb         = request('thumb');
        $createdAt = mMktime(request('created_at'), true);
        $updatedAt = mMktime(request('updated_at'), true);
        $sort          = request('sort');
        $isStick       = request('is_stick');
        $isAudit       = request('is_audit');
        $ifShow = request('if_show');
        $extend = request('extend');
        $album = request('album');
        $attribute = request('attribute');

        $data = [];
        if ('add' == $type || null !== $accessGroupId) {
            $data['access_group_id'] = $accessGroupId;
        }
        if ('add' == $type || null !== $title) {
            $data['title'] = $title;
        }
        if ('add' == $type || null !== $author) {
            $data['author'] = $author;
        }
        if ('add' == $type || null !== $description) {
            if ($description) {
                $data['description'] = $description;
            } else {
                $data['description'] = trim(mSubstr(strip_tags(htmlspecialchars_decode($content)), 100));
            }
        }
        if ('add' == $type || null !== $content) {
            $data['content'] = $content;
        }
        if ('add' == $type || null !== $cateId) {
            $data['cate_id'] = $cateId;
        }
        if ('add' == $type || null !== $channelId) {
            $data['channel_id'] = $channelId;
        }
        if ('add' == $type || null !== $thumb) {
            $data['thumb'] = $thumb;
        }
        if (null !== $createdAt) {
            $data['created_at'] = $createdAt;
        }
        if (null !== $updatedAt) {
            $data['updated_at'] = $updatedAt;
        }
        if ('add' == $type || null !== $sort) {
            $data['sort'] = $sort;
        }
        if ('add' == $type || null !== $isStick) {
            $data['is_stick'] = $isStick;
        }
        if ('add' == $type || null !== $isAudit) {
            $data['is_audit'] = $isAudit;
        }
        if ('add' == $type || null !== $ifShow) {
            $data['if_show'] = $ifShow;
        }
        if ('add' == $type || null !== $extend) {
            $data['extend'] = $extend;
        }
        if ('add' == $type || null !== $attribute) {
            $data['attribute'] = $attribute;
        }
        if ('add' == $type || null !== $album) {
            $data['album'] = is_array($album) ? $album : [];
        }
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
        if (isset($data['album']) && is_array($data['album'])) {
            foreach ($data['album'] as $imageInfo) {
                $info = json_decode($imageInfo, true);
                isset($info['src']) && $bindFile[] = $info['src'];
            }
        }
        $bindFile[]    = $data['new_thumb'];
        $bindFile[]    = $data['thumb'];
        $contentUpload = mGetContentUpload($data['content']);
        $bindFile      = array_merge($bindFile, $contentUpload);
        Model\ManageUpload::bindFile($id, $bindFile);
    }

    //添加 编辑 公共方法
    private function addEditCommon()
    {
        $channelWhere = $categoryWhere = [];
        if (1 != session('backend_info.id')) {
            $channelWhere['id']  = ['in', Model\ArticleChannel::mFindAllow()];
            $categoryWhere['id'] = ['in', Model\ArticleCategory::mFindAllow()];
        }
        $channelList  = Model\ArticleChannel::where($channelWhere)->get();
        $categoryList = Model\ArticleCategory::where($categoryWhere)->get();
        $assign['channel_list']  = $channelList;
        $assign['category_list'] = $categoryList;
        View::share($assign);
    }

    //检查是否有 add edit del privilege
    private function _check_aed($data = false)
    {
        if (1 == session('backend_info.id')) {
            return true;
        }

        if (!$data) {
            $id   = request('id');
            $data = Model\Article::colWhere($id)->first()->toArray();
        }
        if (!in_array($data['channel_id'], Model\ArticleChannel::mFindAllow())
            || !in_array($data['cate_id'], Model\ArticleCategory::mFindAllow())
        ) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.handle') . trans('common.article'),
                route('Admin::Article::index'));
        }

    }

    private function findTopCategory($id, $column)
    {
        $categoryInfo = Model\ArticleCategory::colWhere($id)->first();
        if ($categoryInfo && $categoryInfo->parent_id) {
            return $this->findTopCategory($categoryInfo->parent_id, $column);
        }
        return $categoryInfo[$column] ? $categoryInfo[$column] : [];
    }

    private function findCateChildIds($id)
    {
        $childIds = collect();
        Model\ArticleCategory::colWhere($id, 'parent_id')->get()->each(function ($item, $key) use ($childIds) {
            $childIds->merge($this->findCateChildIds($item->id));
            $childIds->push($item->id);
        });
        $childIds->push($id);
        return $childIds->toArray();
    }
}
