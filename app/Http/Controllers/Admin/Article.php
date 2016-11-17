<?php
// 后台 文章管理

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Article extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('title');
        $whereValue && $where['title'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('cate_id');
        $whereValue && $where['cate_id'] = ['in', Model\ArticleCategory::mFind_child_id($whereValue)];
        $whereValue = request('channel_id');
        $whereValue && $where[] = ['channel_id', $whereValue];
        $whereValue = mMktimeRange('add_time');
        $whereValue && $where[] = ['add_time', $whereValue];
        $whereValue = request('is_audit');
        $whereValue && $where['is_audit'] = (1 == $whereValue) ? ['gt', 0] : 0;
        $whereValue = request('if_show');
        $whereValue && $where['if_show'] = (1 == $whereValue) ? 1 : 0;
        $channelWhere = $categoryWhere = [];
        if (1 != session('backend_info.id')) {
            $allowChannel = Model\ArticleChannel::mFindAllow();
            is_array($allowChannel) && $channelWhere = ['id' => ['in', $allowChannel]];
            if (isset($where['channel_id']) && in_array($where['channel_id'], $allowChannel)) {
                $where['channel_id'] = $where['channel_id'];
            } else {
                $where['channel_id'] = ['in', $allowChannel];
            }

            $allowCategory = Model\ArticleCategory::mFindAllow();
            is_array($allowCategory) && $categoryWhere = ['id' => ['in', $allowCategory]];
            if (isset($where['cate_id']) && !mInArray($where['cate_id'], $allowCategory)) {
                $where['cate_id'] = ['in', $allowCategory];
            }

            if (isset($where['channel_id']) && isset($where['cate_id'])) {
                $where['_complex'] = [
                    '_logic'     => 'and',
                    'channel_id' => $where['channel_id'],
                    'cate_id'    => $where['cate_id'],
                ];
                unset($where['channel_id']);
                unset($where['cate_id']);
            }
        }
        //初始化翻页 和 列表数据
        $articleList            = Model\Article::where($where)->paginate(config('system.sys_max_row'));
        $assign['article_list'] = $articleList;

        //初始化where_info
        $channelList        = Model\ArticleChannel::where($channelWhere)->all();
        $categoryList       = Model\ArticleCategory::where($categoryWhere)->all();
        $searchChannelList  = [];
        $searchCategoryList = [];
        foreach ($channelList as $channel) {
            $searchChannelList[$channel['id']] = $channel['name'];
        }

        foreach ($categoryList as $category) {
            $searchCategoryList[$category['id']] = $category['name'];
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
        $whereInfo['is_audit']   = ['type'  => 'select',
                                    'name'  => trans('common.yes') . trans('common.no') . l('audit'),
                                    'value' => [
                                        1 => trans('common.audit'),
                                        2 => trans('common.none') . trans('common.audit'),
                                    ],
        ];
        $whereInfo['if_show']    = ['type'  => 'select',
                                    'name'  => trans('common.yes') . trans('common.no') . l('show'),
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
            $data         = $this->makeData();
            isset($data['thumb']) && $thumbFile = $this->imageThumb($data['thumb'], config('system.sys_article_thumb_width'),
                C('SYS_ARTICLE_THUMB_HEIGHT'));
            $resultAdd = Model\Article::create($data);
            //增加了一个分类快捷添加文章的回跳链接
            $rebackLink = request('get.cate_id') ? route('Admin::ArticleCategory::index') : route('Admin::Article::index');
            if ($resultAdd) {
                $data['new_thumb'] = $thumbFile;
                return $this->success(trans('common.article') . trans('common.add') . trans('common.success'),
                    $rebackLink);
            } else {
                return $this->error(trans('common.article') . trans('common.add') . trans('common.error'),
                    route('Admin::Article::add', ['cate_id' => request('get.cate_id')]));
            }
        }

        $this->addEditCommon();
        $assign['title'] = trans('common.article') . trans('common.add');
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
            $data = $this->makeData();
            isset($data['thumb']) && $thumbFile = $this->imageThumb($data['thumb'], config('system.sys_article_thumb_width'),
                C('SYS_ARTICLE_THUMB_HEIGHT'));
            $resultEdit = Model\Article::idWhere($id)->update($data);
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
        $editInfo = Model\Article::where('id', $id)->first();
        config('SYS_ARTICLE_SYNC_IMAGE', $currentConfig);

        foreach ($editInfo['access_group_id'] as &$accessGroupId) {
            $adminGroupName = Model\MemberGroup::idWhere($accessGroupId)->first()['name'];
            $accessGroupId  = ['value' => $accessGroupId, 'html' => $adminGroupName];
        }

        $extendTpl = Model\ArticleCategory::mFindTopColumn($editInfo['cate_id'], 'extend');
        $valExtend = [];
        foreach ($extendTpl as $template) {
            $valExtend[$template] = ($editInfo['extend'][$template]) ? $editInfo['extend'][$template] : '';
        }
        $editInfo['extend']        = $valExtend;
        $editInfo['album']         = array_map("json_encode", $editInfo['album']);
        $editInfo['attribute_tpl'] = Model\ArticleCategory::mFindTopColumn($editInfo['cate_id'], 'attribute');

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
                'SYS_ARTICLE_SYNC_IMAGE',
                'SYS_ARTICLE_PN_LIMIT',
                'SYS_ARTICLE_THUMB_WIDTH',
                'SYS_ARTICLE_THUMB_HEIGHT',
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
            return trans('common.not') . trans('common.edit') . $field;
        }

        $resultEdit = Model\Article::idWhere($id)->update($data);
        if ($resultEdit) {
            $data['value'] = Model\Article::idWhere($data['id'])->first()[$field];
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
                $extendTpl = Model\ArticleCategory::mFindTopColumn($cateId, 'extend');
                foreach ($extendTpl as $template) {
                    $result['info'][$template] = '';
                }
                break;
            case 'attribute':
                if ($data['id']) {
                    $cateId         = $data['id'];
                    $result['info'] = Model\ArticleCategory::mFindTopColumn($cateId, 'attribute');
                } else {
                    $result = ['status' => false, 'info' => 'id error'];
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
        $accessGroupId = request('access_group_id');
        $title         = request('title');
        $author        = request('author');
        $description   = request('description');
        $content       = request('content');
        $cateId        = request('cate_id');
        $channelId     = request('channel_id');
        $thumb         = request('thumb');
        $addTime       = mMktime(request('add_time'), true);
        $updateTime    = mMktime(request('update_time'), true);
        $sort          = request('sort');
        $isStick       = request('is_stick');
        $isAudit       = request('is_audit');
        $isAudit && $isAudit = session('backend_info.id');
        $ifShow = request('if_show');
        $extend = request('extend');
        $album  = request('album');
        foreach ($album as &$imageInfo) {
            $imageInfo = json_decode(htmlspecialchars_decode($imageInfo), true);
        }
        $attribute = request('attribute');

        !$description && $description = trim(mSubstr(strip_tags(htmlspecialchars_decode($content)), 100));

        $data = [];
        ('add' == ACTION_NAME || null !== $accessGroupId) && $data['access_group_id'] = $accessGroupId;
        ('add' == ACTION_NAME || null !== $title) && $data['title'] = $title;
        ('add' == ACTION_NAME || null !== $author) && $data['author'] = $author;
        ('add' == ACTION_NAME || null !== $description) && $data['description'] = $description;
        ('add' == ACTION_NAME || null !== $content) && $data['content'] = $content;
        ('add' == ACTION_NAME || null !== $cateId) && $data['cate_id'] = $cateId;
        ('add' == ACTION_NAME || null !== $channelId) && $data['channel_id'] = $channelId;
        ('add' == ACTION_NAME || null !== $thumb) && $data['thumb'] = $thumb;
        ('add' == ACTION_NAME || null !== $addTime) && $data['add_time'] = $addTime;
        ('add' == ACTION_NAME || null !== $updateTime) && $data['update_time'] = $updateTime;
        ('add' == ACTION_NAME || null !== $sort) && $data['sort'] = $sort;
        ('add' == ACTION_NAME || null !== $isStick) && $data['is_stick'] = $isStick;
        ('add' == ACTION_NAME || null !== $isAudit) && $data['is_audit'] = $isAudit;
        ('add' == ACTION_NAME || null !== $ifShow) && $data['if_show'] = $ifShow;
        ('add' == ACTION_NAME || null !== $extend) && $data['extend'] = $extend;
        ('add' == ACTION_NAME || null !== $attribute) && $data['attribute'] = $attribute;
        ('add' == ACTION_NAME || null !== $album) && $data['album'] = $album;
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

        foreach ($data['album'] as &$imageInfo) {
            $bindFile[] = $imageInfo['src'];
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
        $channelWhere         = $categoryWhere = [];
        if (1 != session('backend_info.id')) {
            $channelWhere['id']  = ['in', Model\ArticleChannel::mFindAllow()];
            $categoryWhere['id'] = ['in', Model\ArticleCategory::mFindAllow()];
        }
        $channelList             = Model\ArticleChannel::where($channelWhere)->all();
        $categoryList            = Model\ArticleCategory::where($categoryWhere)->all();
        $assign['channel_list']  = $channelList;
        $assign['category_list'] = $categoryList;
    }

    //检查是否有 add edit del privilege
    private function _check_aed($data = false)
    {
        if (1 == session('backend_info.id')) {
            return true;
        }

        if (!$data) {
            $id   = request('id');
            $data = Model\Article::where('id', $id)->first();
        }
        if (!in_array($data['channel_id'], Model\ArticleChannel::mFindAllow())
            && !in_array($data['cate_id'], Model\ArticleCategory::mFindAllow())
        ) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.handle') . trans('common.article'),
                route('Admin::Article::index'));
        }

    }
}
