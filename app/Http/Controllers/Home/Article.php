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
use App\Model;

class Article extends Frontend
{
    // 显示单一的文章
    public function article()
    {
        $id        = request('id');
        $channelId = request('channel_id');
        if (!$id) {
            return $this->error(trans('common.article') . trans('common.id') . trans('common.error'),
                route('Home::Index::index'));
        }

        $articleInfo = Model\Article::where($this->_get_article_where())->where('id', $id)->first();
        if (!$articleInfo) {
            return $this->error(trans('common.article') . trans('common.by') . trans('common.hidden'),
                route('Home::Index::index'));
        }

        Model\Article::where(['id' => $id])->setInc('hits');

        $categoryInfo = Model\ArticleCategory::where('id', $articleInfo['cate_id'])->first();

        $channelInfo = Model\ArticleChannel::where('id', $articleInfo['channel_id'])->first();

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
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.access') . trans('common.comma') . trans('common.please') . trans('common.login'),
                route('Home::IMember::index'));
        }

        //缓存数据
        $cacheName  = MODULE_NAME . CONTROLLER_NAME . 'article' . $id;
        $cacheValue = S($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            $articleInfo['content'] = $cacheValue;
        } else {
            $articleInfo['content'] = mContent2ckplayer($articleInfo['content'], $articleInfo['thumb']);
            config('system.sys_article_sync_image') && $articleInfo['content'] = mSyncImg($articleInfo['content']);
            $cacheValue = $articleInfo['content'];
            S($cacheName, $cacheValue, config('system.sys_td_cache'));
        }

        $assign['article_info']      = $articleInfo;
        $assign['category_info']     = $categoryInfo;
        $assign['channel_info']      = $channelInfo;
        $assign['title']             = $articleInfo['title'];
        $assign['category_position'] = $this->_get_category_position($articleInfo['cate_id']);
        $assign['article_position']  = $this->_get_article_position($articleInfo['cate_id']);
        $pnWhere                     = ['cate_id' => $articleInfo['cate_id'], 'channel_id' => $articleInfo['channel_id']];
        $assign['article_pn']        = $this->_get_article_pn($articleInfo['id'], $pnWhere);
        $template                    = $this->_get_template($articleInfo['cate_id'], $channelId);
        $this->display($template['article_template']);
    }

    // 显示分类的 独立页面内容、文章列表、子级分类列表
    public function category()
    {
        $cateId    = request('cate_id');
        $channelId = request('channel_id');
        if (!$cateId) {
            return $this->error(trans('common.category') . trans('common.id') . trans('common.error'),
                route('Home::Index::index'));
        }

        $categoryInfo = Model\ArticleCategory::where('id', $cateId)->first();
        if (!$categoryInfo) {
            return $this->error(trans('common.category') . trans('common.id') . trans('common.error'),
                route('Home::Index::index'));
        }

        $channelInfo = Model\ArticleChannel::where('id', $articleInfo['channel_id'])->first();

        //检测权限
        $memberGroupId = session('frontend_info.group_id');
        $mFindAllows   = [];
        is_array($categoryInfo['access_group_id']) && $mFindAllows = array_merge($mFindAllows,
            $categoryInfo['access_group_id']);
        is_array($channelInfo['access_group_id']) && $mFindAllows = array_merge($mFindAllows,
            $channelInfo['access_group_id']);
        if ($mFindAllows && !mInArray($memberGroupId, $mFindAllows)) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.access') . trans('common.comma') . trans('common.please') . trans('common.login'),
                route('Home::IMember::index'));
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
                config('system.sys_article_sync_image') && $categoryInfo['content'] = mSyncImg($categoryInfo['content']);
                $cacheValue = $categoryInfo['content'];
                S($cacheName, $cacheValue, config('system.sys_td_cache'));
            }
        } else {
            //如果分类是列表页
            $template = $template['list_template'];
            $childArr = Model\ArticleCategory::mFind_child_id($cateId);
            $where    = array_merge($this->_get_article_where(), [
                'channel_id' => 0,
                'cate_id'    => ['in', $childArr],
            ]);
            $channelId && $where['channel_id'] = ['in', [0, $channelId]];
            $categoryTopInfo = Model\ArticleCategory::mFind_top($categoryInfo['id']);
            $attributeWhere  = mAttributeWhere($categoryTopInfo['attribute']);
            $attributeWhere && $where['attribute'] = $attributeWhere;

            $page         = true;
            if ($categoryInfo['s_limit']) {
                $page                       = $categoryInfo['s_limit'];
                $assign['article_list_max'] = $page;
            }
            $articleLsit = Model\Article::mList($where, $page);

            $assign['article_list']       = $articleLsit;
        }

        $assign['category_info']     = $categoryInfo;
        $assign['channel_info']      = $channelInfo;
        $assign['title']             = $categoryInfo['name'];
        $assign['category_position'] = $this->_get_category_position($cateId);
        $assign['article_position']  = $this->_get_article_position($cateId);
        return view($template, $assign);
    }

    // 显示频道
    public function channel()
    {
        $channelId = request('channel_id');
        if (!$channelId) {
            $this->redirect('Index/index');
        }

        $channelInfo = Model\ArticleChannel::where('id', $channelId)->first();

        //检测权限
        $memberGroupId = session('frontend_info.group_id');
        $mFindAllows   = [];
        is_array($channelInfo['access_group_id']) && $mFindAllows = array_merge($mFindAllows,
            $channelInfo['access_group_id']);
        if ($mFindAllows && !mInArray($memberGroupId, $mFindAllows)) {
            return $this->error(trans('common.none') . trans('common.privilege') . trans('common.access') . trans('common.comma') . trans('common.please') . trans('common.login'),
                route('Home::IMember::index'));
        }

        $assign['channel_info'] = $channelInfo;
        $assign['title']        = $channelInfo['name'];
        $template               = $this->_get_template(0, $channelId);
        $this->display($template['channel_template']);
    }

    // 搜索文章
    public function search()
    {
        $keyword = request('keyword');
        if ('' == $keyword) {
            return $this->error(trans('common.please') . trans('common.input') . trans('common.keywords'),
                route('Home::Index::index'));
        }
        $keyword = '%' . $keyword . '%';

        $where  = $this->_get_article_where();
        $cateId = request('cate_id');
        if ($cateId) {
            $where['cate_id'] = ['in', Model\ArticleCategory::mFind_child_id($cateId)];
            $categoryPosition = $this->_get_category_position($cateId);
            $attributeWhere   = mAttributeWhere($categoryPosition['attribute']);
            $attributeWhere && $where['attribute'] = $attributeWhere;
            $assign['category_position'] = $this->_get_category_position($cateId);
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

        $articleLsit                  = Model\Article::mList($where, true);
        $assign['article_list']       = $articleLsit;

        $request           = request();
        $assign['request'] = $request;
        $assign['title']   = trans('common.search') . trans('common.article');
        $template          = $this->_get_template(0);
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
            $channelInfo        = Model\ArticleChannel::where('id', $channelId)->first();
            $defChannelTemplate = CONTROLLER_NAME . config('TMPL_FILE_DEPR') . 'channel';
            $data               = $channelInfo['ext_info'];
            $template           = [
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
        S($cacheName, $cacheValue, config('system.sys_td_cache'));
        return $cacheValue;
    }

    private function _get_category_template($cateId, $col)
    {
        if (!$cateId || !$col) {
            return false;
        }

        $categoryInfo = Model\ArticleCategory::where('id', $cateId)->first();
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

        $parentId = Model\ArticleCategory::idWhere($cateId)->first()['parent_id'];
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

        $topCateId       = Model\ArticleCategory::mFind_top_id($cateId);
        $categoryTopInfo = Model\ArticleCategory::where('id', $topCateId)->first();

        $where         = [
            'parent_id' => $topCateId,
            'if_show'   => 1,
        ];
        $categoryCount = Model\ArticleCategory::where($where)->count();
        $categoryList  = Model\ArticleCategory::mList($where, $categoryCount);

        $categoryPosition                  = $categoryTopInfo;
        $categoryPosition['category_list'] = $categoryList;

        $cacheValue = $categoryPosition;
        S($cacheName, $cacheValue, config('system.sys_td_cache'));
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

        $articleCategoryInfo = Model\ArticleCategory::where('id', $cateId)->first();
        $path[]              = [
            'name' => $articleCategoryInfo['name'],
            'link' => mroute('Home::Article::category', ['cate_id' => $articleCategoryInfo['id']]),
        ];
        if (0 == $articleCategoryInfo['parent_id']) {
            $path[] = [
                'name' => trans('common.homepage'),
                'link' => route('Home::Index::index'),
            ];
            $path   = array_reverse($path);

            $cacheValue = $path;
            S($cacheName, $cacheValue, config('system.sys_td_cache'));
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
        $limit = config('system.sys_article_pn_limit');
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
        $pCondition  = ('desc' == $mianOrder) ? 'gt' : 'lt';
        $nCondition  = ('desc' == $mianOrder) ? 'lt' : 'gt';
        $pOrder      = ('desc' == $mianOrder) ? $originSort . ' asc' : $originSort . ' desc';
        $nOrder      = ('desc' == $mianOrder) ? $originSort . ' desc' : $originSort . ' asc';
        $articleInfo = Model\Article::where('id', $id)->first();
        //上一篇
        $where[$mianSort] = [$pCondition, $articleInfo[$mianSort]];
        $articlePn['p']   = Model\Article::where($where)->order($pOrder)->limit($limit)->select();
        //下一篇
        $where[$mianSort] = [$nCondition, $articleInfo[$mianSort]];
        $articlePn['n']   = Model\Article::where($where)->order($nOrder)->limit($limit)->select();
        return $articlePn;
    }

    private function _get_article_where($attribute)
    {
        //默认文章查询条件
        //1.文章是否被隐藏
        //2.文章是否到了发布时间
        //3.文章是否被审核
        $currentTime = Carbon::now();
        return [
            'add_time' => ['lt', $currentTime],
            'if_show'  => 1,
            'is_audit' => ['gt', 0],
        ];
    }
}
