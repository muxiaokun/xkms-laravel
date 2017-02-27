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
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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

        $articleInfo = Model\Article::where($this->_get_article_where())->colWhere($id)->first();
        if (null === $articleInfo) {
            return $this->error(trans('common.article') . trans('common.by') . trans('common.hidden'),
                route('Home::Index::index'));
        }

        Model\Article::where(['id' => $id])->increment('hits');

        $categoryInfo = Model\ArticleCategory::colWhere($articleInfo['cate_id'])->first();

        $channelInfo = Model\ArticleChannel::colWhere($articleInfo['channel_id'])->first();

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
        $cacheName            = 'Home::Article::article' . $id;
        $cacheValue           = Cache::get($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            $articleInfo['content'] = $cacheValue;
        } else {
            $articleInfo['content'] = mContent2ckplayer($articleInfo['content'], $articleInfo['thumb']);
            config('system.sys_article_sync_image') && $articleInfo['content'] = mAsyncImg($articleInfo['content']);
            $cacheValue = $articleInfo['content'];
            $expiresAt        = Carbon::now()->addSecond(config('system.sys_td_cache'));
            Cache::put($cacheName, $cacheValue, $expiresAt);
        }

        $assign['article_info']      = $articleInfo;
        $assign['category_info']     = $categoryInfo;
        $assign['channel_info']      = $channelInfo;
        $assign['title']             = $articleInfo['title'];
        $assign['category_position'] = $this->_get_category_position($articleInfo['cate_id']);
        $assign['article_position']  = $this->_get_article_position($articleInfo['cate_id']);
        $assign['article_pn'] = $this->_get_article_pn($articleInfo['id']);
        $template                    = $this->_get_template($articleInfo['cate_id'], $channelId);
        return view($template['article_template'], $assign);
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

        $categoryInfo = Model\ArticleCategory::colWhere($cateId)->first();
        if (null === $categoryInfo) {
            return $this->error(trans('common.category') . trans('common.id') . trans('common.error'),
                route('Home::Index::index'));
        }

        $channelInfo = Model\ArticleChannel::colWhere($channelId)->first();

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
            $cacheName     = 'Home::Article::category' . $cateId;
            $cacheValue    = Cache::get($cacheName);
            if ($cacheValue && true !== config('app.debug')) {
                $categoryInfo['content'] = $cacheValue;
            } else {
                $categoryInfo['content'] = mContent2ckplayer($categoryInfo['content'], $categoryInfo['thumb']);
                config('system.sys_article_sync_image') && $categoryInfo['content'] = mAsyncImg($categoryInfo['content']);
                $cacheValue = $categoryInfo['content'];
                $expiresAt = Carbon::now()->addSecond(config('system.sys_td_cache'));
                Cache::put($cacheName, $cacheValue, $expiresAt);
            }
        } else {
            $page                   = ($categoryInfo['s_limit']) ? $categoryInfo['s_limit'] : config('system.sys_max_page');
            $template               = $template['list_template'];
            $articleLsit            = Model\Article::where(function ($query) use ($categoryInfo, $cateId, $channelId) {
                //如果分类是列表页
                if ($cateId) {
                    $childArr = Model\ArticleCategory::mFindCateChildIds($cateId);
                    $query->whereIn('cate_id', $childArr);
                }
                $query->whereIn('channel_id', [0, $channelId]);
                $query->where($this->_get_article_where());
                $categoryTopId             = Model\ArticleCategory::mFindTopId($categoryInfo['id']);
                $categoryTopInfo           = Model\ArticleCategory::colWhere($categoryTopId)->first();
                $categoryInfo['attribute'] = $categoryTopInfo['attribute'];
                $attributeWhere            = mAttributeWhere($categoryTopInfo['attribute']);
                $attributeWhere && $query->transfixionWhere('attribute', $attributeWhere, false);

            })->paginate($page)->appends(request()->all());
            $assign['article_list'] = $articleLsit;
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

        $channelInfo = Model\ArticleChannel::colWhere($channelId)->first()->toArray();

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
        return view($template['channel_template'], $assign);
    }

    // 搜索文章
    public function search()
    {
        $keyword   = request('keyword');
        $cateId    = request('cate_id');
        $channelId = request('cahnnel_id');
        $type      = request('type');
        if ('' == $keyword) {
            return $this->error(trans('common.please') . trans('common.input') . trans('common.keywords'),
                route('Home::Index::index'));
        }
        $keyword = '%' . $keyword . '%';

        if ($cateId) {
            $assign['category_position'] = $this->_get_category_position($cateId);
        }

        $articleLsit            = Model\Article::where(function ($query) use ($keyword, $cateId, $channelId, $type) {
            if ($cateId) {
                $childArr = Model\ArticleCategory::mFindCateChildIds($cateId);
                $query->whereIn('cate_id', $childArr);
                $categoryTopId   = Model\ArticleCategory::mFindTopId($cateId);
                $categoryTopInfo = Model\ArticleCategory::colWhere($categoryTopId)->first();
                $attributeWhere  = mAttributeWhere($categoryTopInfo['attribute']);
                $attributeWhere && $query->transfixionWhere('attribute', $attributeWhere, false);
            }
            if ($channelId) {
                $query->where('channel_id', '=', $channelId);
            }

            $query->where($this->_get_article_where());

            $extend = '';
            if (preg_match('/extend\[(.*?)\]/', $type, $matches)) {
                $type   = 'extend';
                $extend = $matches[1];
            }
            switch ($type) {
                case 'description':
                    $query->where('description', 'like', $keyword);
                    break;
                case 'content':
                    $query->where('content', 'like', $keyword);
                    break;
                case 'extend':
                    $query->where('extend', 'like', '%|' . $extend . ':' . $keyword . '|%');
                    break;
                case 'all':
                    $query->where(function ($query) use ($keyword, $extend) {
                        $query->orWhere('title', 'like', $keyword);
                        $query->orWhere('description', 'like', $keyword);
                        $query->orWhere('content', 'like', $keyword);
                        $extend && $query->orWhere('extend', 'like', '%|' . $extend . ':' . $keyword . '|%');
                    });
                    break;
                default:
                    $query->where('title', 'like', $keyword);
            }
        })->
        paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['article_list'] = $articleLsit;

        $assign['title'] = trans('common.search') . trans('common.article');
        $template        = $this->_get_template(0);
        return view($template['list_template'], $assign);
    }

    // 获得当前文章 分类 频道模板
    private function _get_template($cateId, $channelId = 0)
    {
        //缓存流程 调取模板信息 过多回调
        $cacheName  = 'Home::Article::_get_template' . $channelId . '_' . $cateId;
        $cacheValue = Cache::get($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            return $cacheValue;
        }

        $template = [
            's_limit'          => '',
            'template'         => '',
            'list_template'    => '',
            'article_template' => '',
            'channel_template' => '',
        ];
        // 如果频道编号存在 则查询频道是否有模板的配置 覆盖一般分类配置
        if ($channelId) {
            $channelInfo        = Model\ArticleChannel::colWhere($channelId)->first()->toArray();
            $defChannelTemplate = 'home.Article_channel';
            $data = $channelInfo['extend'];
            $template           = [
                's_limit'          => $this->_get_channel_template($cateId, 's_limit', $data),
                'template'         => $this->_get_channel_template($cateId, 'template', $data),
                'list_template'    => $this->_get_channel_template($cateId, 'list_template', $data),
                'article_template' => $this->_get_channel_template($cateId, 'article_template', $data),
                'channel_template' => ($channelInfo['template']) ? $defChannelTemplate . '_' . $channelInfo['template'] : $defChannelTemplate,
            ];
        }

        !$template['s_limit'] && $template['s_limit'] = $this->_get_category_template($cateId,
            's_limit'); //分类调用条数
        !$template['template'] && $template['template'] = $this->_get_category_template($cateId,
            'template'); //分类模板
        !$template['list_template'] && $template['list_template'] = $this->_get_category_template($cateId,
            'list_template'); //文章列表
        !$template['article_template'] && $template['article_template'] = $this->_get_category_template($cateId,
            'article_template'); //文章模板

        //返回最终模板文件名 加模板前缀
        $defTemplate                  = 'home.Article_category';
        $defListTemplate              = 'home.Article_list_category';
        $defArticleTemplate           = 'home.Article_article';
        $template['template']         = ($template['template']) ? $defTemplate . '_' . $template['template'] : $defTemplate;
        $template['list_template']    = ($template['list_template']) ? $defListTemplate . '_' . $template['list_template'] : $defListTemplate;
        $template['article_template'] = ($template['article_template']) ? $defArticleTemplate . '_' . $template['article_template'] : $defArticleTemplate;

        $cacheValue = $template;
        $expiresAt                    = Carbon::now()->addSecond(config('system.sys_td_cache'));
        Cache::put($cacheName, $cacheValue, $expiresAt);
        return $cacheValue;
    }

    private function _get_category_template($cateId, $col)
    {
        if (!$cateId || !$col) {
            return false;
        }

        $categoryInfo = Model\ArticleCategory::colWhere($cateId)->first()->toArray();
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

        $parentId = Model\ArticleCategory::colWhere($cateId)->first()['parent_id'];
        return ($cateId == $parentId) ? '' : $this->_get_channel_template($parentId, $col, $data);
    }

    //获取当前文章分类子类位置
    private function _get_category_position($cateId)
    {
        $cacheName  = 'Home::Article::_get_category_position' . $cateId;
        $cacheValue = Cache::get($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            return $cacheValue;
        }

        $topCateId       = Model\ArticleCategory::mFindTopId($cateId);
        $categoryTopInfo = Model\ArticleCategory::colWhere($topCateId)->first();

        $where        = [
            'parent_id' => $topCateId,
            'if_show'   => 1,
        ];
        $categoryList = Model\ArticleCategory::where($where)->get();

        $categoryPosition                  = $categoryTopInfo;
        $categoryPosition['category_list'] = $categoryList;

        $cacheValue = $categoryPosition;
        $expiresAt = Carbon::now()->addSecond(config('system.sys_td_cache'));
        Cache::put($cacheName, $cacheValue, $expiresAt);
        return $cacheValue;
    }

    //获取当前文章分类位置
    private function _get_article_position($cateId, $cacheName = false, $path = [])
    {
        !$cacheName && $cacheName = 'Home::Article::_get_article_position' . $cateId;
        $cacheValue    = Cache::get($cacheName);
        if ($cacheValue && true !== config('app.debug')) {
            return $cacheValue;
        }

        $articleCategoryInfo = Model\ArticleCategory::colWhere($cateId)->first()->toArray();
        $path[]        = [
            'name' => $articleCategoryInfo['name'],
            'link' => route('Home::Article::category', ['cate_id' => $articleCategoryInfo['id']]),
        ];
        if (0 == $articleCategoryInfo['parent_id']) {
            $path[] = [
                'name' => trans('common.homepage'),
                'link' => route('Home::Index::index'),
            ];
            $path   = array_reverse($path);

            $cacheValue = $path;
            $expiresAt = Carbon::now()->addSecond(config('system.sys_td_cache'));
            Cache::put($cacheName, $cacheValue, $expiresAt);
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
    private function _get_article_pn($id, $where = [], $sort = ['sort' => 'asc', 'updated_at' => 'desc'])
    {
        $limit = config('system.sys_article_pn_limit');
        if (1 > $limit) {
            return;
        }

        $articlePn   = [
            'limit' => $limit,
        ];
        $articleInfo = Model\Article::colWhere($id)->first()->toArray();
        $pnWhere     = [
            ['cate_id', '=', $articleInfo['cate_id']],
            ['channel_id', '=', $articleInfo['channel_id']],
        ];
        $where       = array_merge($this->_get_article_where(), $where, $pnWhere);
        $pOrder      = $nOrder = $sort;
        end($sort);
        $mianSort  = key($sort);
        $mianOrder = current($sort);

        //p = gt asc
        //n = lt desc
        $pCondition        = ('desc' == $mianOrder) ? '>' : '<';
        $nCondition        = ('desc' == $mianOrder) ? '<' : '>';
        $pOrder[$mianSort] = ('desc' == $mianOrder) ? ' asc' : ' desc';
        $nOrder[$mianSort] = ('desc' == $mianOrder) ? ' desc' : ' asc';
        //上一篇
        $pWhere = array_merge($where, [[$mianSort, $pCondition, $articleInfo[$mianSort]]]);;
        $articlePn['p'] = Model\Article::where($pWhere)->mOrdered($pOrder)->take($limit)->get();
        //下一篇
        $nWhere = array_merge($where, [[$mianSort, $nCondition, $articleInfo[$mianSort]]]);;
        $articlePn['n'] = Model\Article::where($nWhere)->mOrdered($nOrder)->take($limit)->get();
        return $articlePn;
    }

    private function _get_article_where()
    {
        //默认文章查询条件
        //1.文章是否被隐藏
        //2.文章是否到了发布时间
        //3.文章是否被审核
        $currentTime = Carbon::now();
        return [
            ['created_at', '<', $currentTime],
            ['if_show', '=', 1],
            ['is_audit', '>', 0],
        ];
    }
}
