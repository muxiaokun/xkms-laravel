<?php
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
        $whereValue = '';
        $whereValue = request('title');
        $whereValue && $where['title'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('cate_id');
        $whereValue && $where['cate_id'] = ['in', $ArticleCategoryModel->mFind_child_id($whereValue)];
        $whereValue = request('channel_id');
        $whereValue && $where['channel_id'] = $whereValue;
        $whereValue = mMktimeRange('add_time');
        $whereValue && $where['add_time'] = $whereValue;
        $whereValue = request('is_audit');
        $whereValue && $where['is_audit'] = (1 == $whereValue) ? ['gt', 0] : 0;
        $whereValue = request('if_show');
        $whereValue && $where['if_show'] = (1 == $whereValue) ? 1 : 0;
        $channelWhere = $categoryWhere = [];
        if (1 != session('backend_info.id')) {
            $allowChannel = $ArticleChannelModel->mFind_allow();
            is_array($allowChannel) && $channelWhere = ['id' => ['in', $allowChannel]];
            if (isset($where['channel_id']) && in_array($where['channel_id'], $allowChannel)) {
                $where['channel_id'] = $where['channel_id'];
            } else {
                $where['channel_id'] = ['in', $allowChannel];
            }

            $allowCategory = $ArticleCategoryModel->mFind_allow();
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
        $articleList = $ArticleModel->mSelect($where, true);
        foreach ($articleList as &$article) {
            $article['channel_name'] = ($article['channel_id']) ? $ArticleCategoryModel->mFindColumn($article['channel_id'],
                'name') : trans('empty');
            $article['cate_name']    = ($article['cate_id']) ? $ArticleCategoryModel->mFindColumn($article['cate_id'],
                'name') : trans('empty');
        }
        $this->assign('article_list', $articleList);
        $this->assign('article_list_count', $ArticleModel->mGetPageCount($where));

        //初始化where_info
        $channelList        = $ArticleChannelModel->mSelect($channelWhere,
            $ArticleChannelModel->where($channelWhere)->count());
        $categoryList       = $ArticleCategoryModel->mSelect_tree($categoryWhere);
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
        $whereInfo['title']      = ['type' => 'input', 'name' => trans('title')];
        $whereInfo['cate_id']    = ['type' => 'select', 'name' => trans('category'), 'value' => $searchCategoryList];
        $whereInfo['channel_id'] = ['type' => 'select', 'name' => trans('channel'), 'value' => $searchChannelList];
        $whereInfo['is_audit']   = ['type'  => 'select',
                                    'name'  => trans('yes') . trans('no') . l('audit'),
                                    'value' => [1 => trans('audit'), 2 => trans('none') . trans('audit')],
        ];
        $whereInfo['if_show']    = ['type'  => 'select',
                                    'name'  => trans('yes') . trans('no') . l('show'),
                                    'value' => [1 => trans('show'), 2 => trans('hidden')],
        ];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = [];
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('article') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $ArticleModel = D('Article');
            $data         = $this->makeData();
            isset($data['thumb']) && $thumbFile = $this->imageThumb($data['thumb'], config('SYS_ARTICLE_THUMB_WIDTH'),
                C('SYS_ARTICLE_THUMB_HEIGHT'));
            $resultAdd = $ArticleModel->mAdd($data);
            //增加了一个分类快捷添加文章的回跳链接
            $rebackLink = request('get.cate_id') ? route('ArticleCategory/index') : U('index');
            if ($resultAdd) {
                $data['new_thumb'] = $thumbFile;
                $this->addEditAfterCommon($data, $ArticleModel->getLastInsID());
                $this->success(trans('article') . trans('add') . trans('success'), $rebackLink);
                return;
            } else {
                $this->error(trans('article') . trans('add') . trans('error'),
                    route('add', ['cate_id' => request('get.cate_id')]));
            }
        }

        $this->addEditCommon();
        $this->assign('title', trans('article') . trans('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $ArticleModel = D('Article');
        if (IS_POST) {
            $data = $this->makeData();
            isset($data['thumb']) && $thumbFile = $this->imageThumb($data['thumb'], config('SYS_ARTICLE_THUMB_WIDTH'),
                C('SYS_ARTICLE_THUMB_HEIGHT'));
            $resultEdit = $ArticleModel->mEdit($id, $data);
            if ($resultEdit) {
                $data['new_thumb'] = $thumbFile;
                $this->addEditAfterCommon($data, $id);
                $this->success(trans('article') . trans('edit') . trans('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('article') . trans('edit') . trans('error'), $errorGoLink);
            }
        }
        $currentConfig = config('SYS_ARTICLE_SYNC_IMAGE');
        config('SYS_ARTICLE_SYNC_IMAGE', false);
        $editInfo = $ArticleModel->mFind($id);
        config('SYS_ARTICLE_SYNC_IMAGE', $currentConfig);

        $MemberGroupModel = D('MemberGroup');
        foreach ($editInfo['access_group_id'] as &$accessGroupId) {
            $adminGroupName = $MemberGroupModel->mFindColumn($accessGroupId, 'name');
            $accessGroupId  = ['value' => $accessGroupId, 'html' => $adminGroupName];
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $extendTpl            = $ArticleCategoryModel->mFindTopColumn($editInfo['cate_id'], 'extend');
        $valExtend            = [];
        foreach ($extendTpl as $template) {
            $valExtend[$template] = ($editInfo['extend'][$template]) ? $editInfo['extend'][$template] : '';
        }
        $editInfo['extend']        = $valExtend;
        $editInfo['album']         = array_map("json_encode", $editInfo['album']);
        $editInfo['attribute_tpl'] = $ArticleCategoryModel->mFindTopColumn($editInfo['cate_id'], 'attribute');

        $this->assign('edit_info', $editInfo);

        $this->addEditCommon();
        $this->assign('title', trans('article') . trans('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $this->_check_aed();
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $ArticleModel = D('Article');
        $resultDel    = $ArticleModel->mDel($id);
        if ($resultDel) {
            $ManageUploadModel = D('ManageUpload');
            $ManageUploadModel->mEdit($id);
            $this->success(trans('article') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('article') . trans('del') . trans('error'), route('index'));
        }
    }

    //配置
    public function setting()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = [
                'SYS_ARTICLE_SYNC_IMAGE',
                'SYS_ARTICLE_PN_LIMIT',
                'SYS_ARTICLE_THUMB_WIDTH',
                'SYS_ARTICLE_THUMB_HEIGHT',
            ];
            $this->_put_config($col, 'system');
            return;
        }

        $this->assign('title', trans('article') . trans('config'));
        $this->display();
    }

    //异步行编辑
    protected function _line_edit($field, $data)
    {
        $allowField = ['sort'];
        if (!in_array($field, $allowField)) {
            return trans('not') . trans('edit') . $field;
        }

        $ArticleModel = D('Article');
        $resultEdit   = $ArticleModel->mEdit($data['id'], [$field => $data['value']]);
        if ($resultEdit) {
            $data['value'] = $ArticleModel->mFindColumn($data['id'], $field);
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
            case 'access_group_id':
                $MemberGroupModel = D('MemberGroup');
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberGroupList = $MemberGroupModel->mSelect($where);
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = ['value' => $memberGroup['id'], 'html' => $memberGroup['name']];
                }
                break;
            case 'exttpl_id':
                isset($data['id']) && $cateId = $data['id'];
                $ArticleCategoryModel = D('ArticleCategory');
                $extendTpl            = $ArticleCategoryModel->mFindTopColumn($cateId, 'extend');
                foreach ($extendTpl as $template) {
                    $result['info'][$template] = '';
                }
                break;
            case 'attribute':
                if ($data['id']) {
                    $cateId               = $data['id'];
                    $ArticleCategoryModel = D('ArticleCategory');
                    $result['info']       = $ArticleCategoryModel->mFindTopColumn($cateId, 'attribute');
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

        $ManageUploadModel = D('ManageUpload');
        foreach ($data['album'] as &$imageInfo) {
            $bindFile[] = $imageInfo['src'];
        }

        $bindFile[]    = $data['new_thumb'];
        $bindFile[]    = $data['thumb'];
        $contentUpload = mGetContentUpload($data['content']);
        $bindFile      = array_merge($bindFile, $contentUpload);
        $ManageUploadModel->mEdit($id, $bindFile);
    }

    //添加 编辑 公共方法
    private function addEditCommon()
    {
        $ArticleChannelModel  = D('ArticleChannel');
        $ArticleCategoryModel = D('ArticleCategory');
        $channelWhere         = $categoryWhere = [];
        if (1 != session('backend_info.id')) {
            $channelWhere['id']  = ['in', $ArticleChannelModel->mFind_allow()];
            $categoryWhere['id'] = ['in', $ArticleCategoryModel->mFind_allow()];
        }
        $channelList  = $ArticleChannelModel->mSelect($channelWhere,
            $ArticleChannelModel->where($channelWhere)->count());
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
            $id           = request('id');
            $ArticleModel = D('Article');
            $data         = $ArticleModel->mFind($id);
        }
        $ArticleCategoryModel = D('ArticleCategory');
        $ArticleChannelModel  = D('ArticleChannel');
        if (!in_array($data['channel_id'], $ArticleChannelModel->mFind_allow())
            && !in_array($data['cate_id'], $ArticleCategoryModel->mFind_allow())
        ) {
            $this->error(trans('none') . trans('privilege') . trans('handle') . trans('article'), route('index'));
        }

    }
}
