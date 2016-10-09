<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2012 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 20014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
// 前台 文章

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Article extends Frontend
{
    // 显示单一的文章
    public function article()
    {
        $id        = request('id');
        $channelId = request('channel_id');
        if (!$id) {
            $this->error(trans('article') . trans('id') . trans('error'), route('Index/index'));
        }

        $ArticleModel = D('Article');
        $articleInfo  = $ArticleModel->where($this->_get_article_where())->mFind($id);
        if (!$articleInfo) {
            $this->error(trans('article') . trans('by') . trans('hidden'), route('Index/index'));
        }

        $ArticleModel->where(['id' => $id])->setInc('hits');

        $ArticleCategoryModel = D('ArticleCategory');
        $categoryInfo         = $ArticleCategoryModel->mFind($articleInfo['cate_id']);

        $ArticleChannelModel = D('ArticleChannel');
        $channelInfo         = $ArticleChannelModel->mFind($articleInfo['channel_id']);

        //检测权限
        $memberGroupId = session('frontend_info.group_id');
        $mFindAllows   = [];
        is_array($articleInfo['access_group_id']) && $mFindAllows = array_merge($mFindAllows,
            $articleInfo['access_group_id']);
        is_array($categoryInfo['access_group_id']) && $mFindAllows = array_merge($mFindAllows,
            $categoryInfo['access_group_id']);
        is_array($channelInfo['access_group_id']) && $mFindAllows = array_merge($mFindAllows,
            $channelInfo['access_group_id']);
        if ($mFindAllows && !mInArray($memberGroupId, $mFindAllows)) {
            $this->error(trans('none') . trans('privilege') . trans('access') . trans('comma') . trans('please') . trans('login'),
                route('Member/index'));
        }

        //缓存数据
        $cacheName  = MODULE_NAME . CONTROLLER_NAME . 'article' . $id;
        $cacheValue = S($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            $articleInfo['content'] = $cacheValue;
        } else {
            $articleInfo['content'] = mContent2ckplayer($articleInfo['content'], $articleInfo['thumb']);
            config('SYS_ARTICLE_SYNC_IMAGE') && $articleInfo['content'] = mSyncImg($articleInfo['content']);
            $cacheValue = $articleInfo['content'];
            S($cacheName, $cacheValue, config('SYS_TD_CACHE'));
        }

        $this->assign('article_info', $articleInfo);
        $this->assign('category_info', $categoryInfo);
        $this->assign('channel_info', $channelInfo);
        $this->assign('title', $articleInfo['title']);
        $this->assign('category_position', $this->_get_category_position($articleInfo['cate_id']));
        $this->assign('article_position', $this->_get_article_position($articleInfo['cate_id']));
        $pnWhere = ['cate_id' => $articleInfo['cate_id'], 'channel_id' => $articleInfo['channel_id']];
        $this->assign('article_pn', $this->_get_article_pn($articleInfo['id'], $pnWhere));
        $template = $this->_get_template($articleInfo['cate_id'], $channelId);
        $this->display($template['article_template']);
    }

    // 显示分类的 独立页面内容、文章列表、子级分类列表
    public function category()
    {
        $cateId    = request('cate_id');
        $channelId = request('channel_id');
        if (!$cateId) {
            $this->error(trans('category') . trans('id') . trans('error'), route('Index/index'));
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $categoryInfo         = $ArticleCategoryModel->mFind($cateId);
        if (!$categoryInfo) {
            $this->error(trans('category') . trans('id') . trans('error'), route('Index/index'));
        }

        $ArticleChannelModel = D('ArticleChannel');
        $channelInfo         = $ArticleChannelModel->mFind($articleInfo['channel_id']);

        //检测权限
        $memberGroupId = session('frontend_info.group_id');
        $mFindAllows   = [];
        is_array($categoryInfo['access_group_id']) && $mFindAllows = array_merge($mFindAllows,
            $categoryInfo['access_group_id']);
        is_array($channelInfo['access_group_id']) && $mFindAllows = array_merge($mFindAllows,
            $channelInfo['access_group_id']);
        if ($mFindAllows && !mInArray($memberGroupId, $mFindAllows)) {
            $this->error(trans('none') . trans('privilege') . trans('access') . trans('comma') . trans('please') . trans('login'),
                route('Member/index'));
        }

        $template = $this->_get_template($cateId, $channelId);
        if ($categoryInfo['is_content'] || request('is_content')) {
            $template = $template['template'];
            //如果分类是单页面
            //缓存数据
            $cacheName  = MODULE_NAME . CONTROLLER_NAME . 'category' . $cateId;
            $cacheValue = S($cacheName);
            if ($cacheValue && true !== config('app.debug')) {
                $categoryInfo['content'] = $cacheValue;
            } else {
                $categoryInfo['content'] = mContent2ckplayer($categoryInfo['content'], $categoryInfo['thumb']);
                config('SYS_ARTICLE_SYNC_IMAGE') && $categoryInfo['content'] = mSyncImg($categoryInfo['content']);
                $cacheValue = $categoryInfo['content'];
                S($cacheName, $cacheValue, config('SYS_TD_CACHE'));
            }
        } else {
            //如果分类是列表页
            $template = $template['list_template'];
            $childArr = $ArticleCategoryModel->mFind_child_id($cateId);
            $where    = array_merge($this->_get_article_where(), [
                'channel_id' => 0,
                'cate_id'    => ['in', $childArr],
            ]);
            $channelId && $where['channel_id'] = ['in', [0, $channelId]];
            $categoryTopInfo = $ArticleCategoryModel->mFind_top($categoryInfo['id']);
            $attributeWhere  = mAttributeWhere($categoryTopInfo['attribute']);
            $attributeWhere && $where['attribute'] = $attributeWhere;

            $ArticleModel = D('Article');
            $page         = true;
            if ($categoryInfo['s_limit']) {
                $page = $categoryInfo['s_limit'];
                $this->assign('article_list_max', $page);
            }
            $articleLsit = $ArticleModel->mSelect($where, $page);

            $this->assign('article_list', $articleLsit);
            $this->assign('article_list_count', $ArticleModel->mGetPageCount($where));
        }

        $this->assign('category_info', $categoryInfo);
        $this->assign('channel_info', $channelInfo);
        $this->assign('title', $categoryInfo['name']);
        $this->assign('category_position', $this->_get_category_position($cateId));
        $this->assign('article_position', $this->_get_article_position($cateId));
        $this->display($template);
    }

    // 显示频道
    public function channel()
    {
        $channelId = request('channel_id');
        if (!$channelId) {
            $this->redirect('Index/index');
        }

        $ArticleChannelModel = D('ArticleChannel');
        $channelInfo         = $ArticleChannelModel->mFind($channelId);

        //检测权限
        $memberGroupId = session('frontend_info.group_id');
        $mFindAllows   = [];
        is_array($channelInfo['access_group_id']) && $mFindAllows = array_merge($mFindAllows,
            $channelInfo['access_group_id']);
        if ($mFindAllows && !mInArray($memberGroupId, $mFindAllows)) {
            $this->error(trans('none') . trans('privilege') . trans('access') . trans('comma') . trans('please') . trans('login'),
                route('Member/index'));
        }

        $this->assign('channel_info', $channelInfo);
        $this->assign('title', $channelInfo['name']);
        $template = $this->_get_template(0, $channelId);
        $this->display($template['channel_template']);
    }

    // 搜索文章
    public function search()
    {
        $keyword = request('keyword');
        if ('' == $keyword) {
            $this->error(trans('please') . trans('input') . trans('keywords'), route('Index/index'));
        }
        $keyword = '%' . $keyword . '%';

        $where  = $this->_get_article_where();
        $cateId = request('cate_id');
        if ($cateId) {
            $ArticleCategoryModel = D('ArticleCategory');
            $where['cate_id']     = ['in', $ArticleCategoryModel->mFind_child_id($cateId)];
            $categoryPosition     = $this->_get_category_position($cateId);
            $attributeWhere       = mAttributeWhere($categoryPosition['attribute']);
            $attributeWhere && $where['attribute'] = $attributeWhere;
            $this->assign('category_position', $this->_get_category_position($cateId));
        }
        $channelId = request('cahnnel_id');
        $channelId && $where['channel_id'] = $channelId;

        $type = request('type');
        if (preg_match('/extend\[(.*?)\]/', $type, $matches)) {
            $type   = 'extend';
            $extend = $matches[1];
        }

        $complex = ['_logic' => 'or'];
        switch ($type) {
            case 'description':
                $complex['description'] = ['like', $keyword];
                break;
            case 'content':
                $complex['content'] = ['like', $keyword];
                break;
            case 'extend':
                $complex['extend'] = ['like', '%|' . $extend . ':' . $keyword . '|%'];
                break;
            case 'all':
                $complex['description'] = ['like', $keyword];
                $complex['content']     = ['like', $keyword];
                $complex['title']       = ['like', $keyword];
                $complex['extend']      = ['like', '|' . $extend . ':' . $keyword . '|'];
                break;
            default:
                $complex['title'] = ['like', $keyword];
        }
        $where['_complex'] = $complex;

        $ArticleModel = D('Article');
        $articleLsit  = $ArticleModel->mSelect($where, true);
        $this->assign('article_list', $articleLsit);
        $this->assign('article_list_count', $ArticleModel->mGetPageCount($where));

        $request = request();
        $this->assign('request', $request);
        $this->assign('title', trans('search') . trans('article'));
        $template = $this->_get_template(0);
        $this->display($template['list_template']);
    }

    // 获得当前文章 分类 频道模板
    private function _get_template($cateId, $channelId = 0)
    {
        //缓存流程 调取模板信息 过多回调
        $cacheName  = MODULE_NAME . CONTROLLER_NAME . '_get_template' . $channelId . '_' . $cateId;
        $cacheValue = S($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            return $cacheValue;
        }

        $template = [];
        // 如果频道编号存在 则查询频道是否有模板的配置 覆盖一般分类配置
        if ($channelId) {
            $ArticleChannelModel = D('ArticleChannel');
            $channelInfo         = $ArticleChannelModel->mFind($channelId);
            $defChannelTemplate  = CONTROLLER_NAME . config('TMPL_FILE_DEPR') . 'channel';
            $data                = $channelInfo['ext_info'];
            $template            = [
                's_limit'          => $this->_get_channel_template($cateId, 's_limit', $data),
                'template'         => $this->_get_channel_template($cateId, 'template', $data),
                'list_template'    => $this->_get_channel_template($cateId, 'list_template', $data),
                'article_template' => $this->_get_channel_template($cateId, 'article_template', $data),
                'channel_template' => ($channelInfo['template']) ? $defChannelTemplate . '_' . $channelInfo['template'] : $defChannelTemplate,
            ];
        }

        empty($template['s_limit']) && $template['s_limit'] = $this->_get_category_template($cateId,
            's_limit'); //分类调用条数
        empty($template['template']) && $template['template'] = $this->_get_category_template($cateId,
            'template'); //分类模板
        empty($template['list_template']) && $template['list_template'] = $this->_get_category_template($cateId,
            'list_template'); //文章列表
        empty($template['article_template']) && $template['article_template'] = $this->_get_category_template($cateId,
            'article_template'); //文章模板

        //返回最终模板文件名 加模板前缀
        $defTemplate                  = CONTROLLER_NAME . config('TMPL_FILE_DEPR') . 'category';
        $defListTemplate              = CONTROLLER_NAME . config('TMPL_FILE_DEPR') . 'list_category';
        $defArticleTemplate           = CONTROLLER_NAME . config('TMPL_FILE_DEPR') . 'article';
        $template['template']         = ($template['template']) ? $defTemplate . '_' . $template['template'] : $defTemplate;
        $template['list_template']    = ($template['list_template']) ? $defListTemplate . '_' . $template['list_template'] : $defListTemplate;
        $template['article_template'] = ($template['article_template']) ? $defArticleTemplate . '_' . $template['article_template'] : $defArticleTemplate;

        $cacheValue = $template;
        S($cacheName, $cacheValue, config('SYS_TD_CACHE'));
        return $cacheValue;
    }

    private function _get_category_template($cateId, $col)
    {
        if (!$cateId || !$col) {
            return false;
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $categoryInfo         = $ArticleCategoryModel->mFind($cateId);
        if ($categoryInfo[$col]) {
            return $categoryInfo[$col];
        }

        return (0 == $categoryInfo['parent_id']) ? '' : $this->_get_category_template($categoryInfo['parent_id'], $col);
    }

    private function _get_channel_template($cateId, $col, &$data)
    {
        if (!$cateId || !$col) {
            return false;
        }

        if (isset($data[$cateId][$col]) && '' != $data[$cateId][$col]) {
            return $data[$cateId][$col];
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $parentId             = $ArticleCategoryModel->mFindColumn($cateId, 'parent_id');
        return ($cateId == $parentId) ? '' : $this->_get_channel_template($parentId, $col, $data);
    }

    //获取当前文章分类子类位置
    private function _get_category_position($cateId)
    {
        $cacheName  = MODULE_NAME . CONTROLLER_NAME . '_get_category_position' . $cateId;
        $cacheValue = S($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            return $cacheValue;
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $topCateId            = $ArticleCategoryModel->mFind_top_id($cateId);
        $categoryTopInfo      = $ArticleCategoryModel->mFind($topCateId);

        $where         = [
            'parent_id' => $topCateId,
            'if_show'   => 1,
        ];
        $categoryCount = $ArticleCategoryModel->where($where)->count();
        $categoryList  = $ArticleCategoryModel->mSelect($where, $categoryCount);

        $categoryPosition                  = $categoryTopInfo;
        $categoryPosition['category_list'] = $categoryList;

        $cacheValue = $categoryPosition;
        S($cacheName, $cacheValue, config('SYS_TD_CACHE'));
        return $cacheValue;
    }

    //获取当前文章分类位置
    private function _get_article_position($cateId, $cacheName = false, $path = [])
    {
        !$cacheName && $cacheName = MODULE_NAME . CONTROLLER_NAME . '_get_article_position' . $cateId;
        $cacheValue = S($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            return $cacheValue;
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $articleCategoryInfo  = $ArticleCategoryModel->mFind($cateId);
        $path[]               = [
            'name' => $articleCategoryInfo['name'],
            'link' => mroute('article_category', ['cate_id' => $articleCategoryInfo['id']]),
        ];
        if (0 == $articleCategoryInfo['parent_id']) {
            $path[] = [
                'name' => trans('homepage'),
                'link' => mroute(),
            ];
            $path   = array_reverse($path);

            $cacheValue = $path;
            S($cacheName, $cacheValue, config('SYS_TD_CACHE'));
            return $cacheValue;
        } else {
            return $this->_get_article_position($articleCategoryInfo['parent_id'], $cacheName, $path);
        }
    }

    /**
     * 获取上下关联的文章
     * @param int    $id    文章编号
     * @param array  $where 查询列表条件
     * @param string $sort  最后一个排序条件
     */
    private function _get_article_pn($id, $where = [], $sort = 'sort asc,update_time desc')
    {
        $limit = config('SYS_ARTICLE_PN_LIMIT');
        if (1 > $limit) {
            return;
        }

        $articlePn = [
            'limit' => $limit,
        ];
        $where     = array_merge($this->_get_article_where(), $where);
        preg_match('/,?(\w*)\s*(\w*)$/', $sort, $matchs);
        $mianSort   = $matchs[1];
        $mianOrder  = $matchs[2];
        $originSort = str_replace($matchs[0], '', $sort);
        $originSort .= $originSort ? ',' . $mianSort : $mianSort;
        //p = gt asc
        //n = lt desc
        $pCondition   = ('desc' == $mianOrder) ? 'gt' : 'lt';
        $nCondition   = ('desc' == $mianOrder) ? 'lt' : 'gt';
        $pOrder       = ('desc' == $mianOrder) ? $originSort . ' asc' : $originSort . ' desc';
        $nOrder       = ('desc' == $mianOrder) ? $originSort . ' desc' : $originSort . ' asc';
        $ArticleModel = D('Article');
        $articleInfo  = $ArticleModel->mFind($id);
        //上一篇
        $where[$mianSort] = [$pCondition, $articleInfo[$mianSort]];
        $articlePn['p']   = $ArticleModel->where($where)->order($pOrder)->limit($limit)->select();
        //下一篇
        $where[$mianSort] = [$nCondition, $articleInfo[$mianSort]];
        $articlePn['n']   = $ArticleModel->where($where)->order($nOrder)->limit($limit)->select();
        return $articlePn;
    }

    private function _get_article_where($attribute)
    {
        //默认文章查询条件
        //1.文章是否被隐藏
        //2.文章是否到了发布时间
        //3.文章是否被审核
        $currentTime = time();
        return [
            'add_time' => ['lt', $currentTime],
            'if_show'  => 1,
            'is_audit' => ['gt', 0],
        ];
    }
}
