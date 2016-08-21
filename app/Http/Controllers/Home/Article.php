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
        $id         = I('id');
        $channel_id = I('channel_id');
        if (!$id) {
            $this->error(L('article') . L('id') . L('error'), U('Index/index'));
        }

        $ArticleModel = D('Article');
        $article_info = $ArticleModel->where($this->_get_article_where())->mFind($id);
        if (!$article_info) {
            $this->error(L('article') . L('by') . L('hidden'), U('Index/index'));
        }

        $ArticleModel->where(array('id' => $id))->setInc('hits');

        $ArticleCategoryModel = D('ArticleCategory');
        $category_info        = $ArticleCategoryModel->mFind($article_info['cate_id']);

        $ArticleChannelModel = D('ArticleChannel');
        $channel_info        = $ArticleChannelModel->mFind($article_info['channel_id']);

        //检测权限
        $member_group_id                                              = session('frontend_info.group_id');
        $mFind_allows                                                = array();
        is_array($article_info['access_group_id']) && $mFind_allows  = array_merge($mFind_allows, $article_info['access_group_id']);
        is_array($category_info['access_group_id']) && $mFind_allows = array_merge($mFind_allows, $category_info['access_group_id']);
        is_array($channel_info['access_group_id']) && $mFind_allows  = array_merge($mFind_allows, $channel_info['access_group_id']);
        if ($mFind_allows && !M_in_array($member_group_id, $mFind_allows)) {
            $this->error(L('none') . L('privilege') . L('access') . L('comma') . L('please') . L('login'), U('Member/index'));
        }

        //缓存数据
        $cache_name  = MODULE_NAME . CONTROLLER_NAME . 'article' . $id;
        $cache_value = S($cache_name);
        if ($cache_value && true !== APP_DEBUG) {
            $article_info['content'] = $cache_value;
        } else {
            $article_info['content']                                = M_content2ckplayer($article_info['content'], $article_info['thumb']);
            C('SYS_ARTICLE_SYNC_IMAGE') && $article_info['content'] = M_sync_img($article_info['content']);
            $cache_value                                            = $article_info['content'];
            S($cache_name, $cache_value, C('SYS_TD_CACHE'));
        }

        $this->assign('article_info', $article_info);
        $this->assign('category_info', $category_info);
        $this->assign('channel_info', $channel_info);
        $this->assign('title', $article_info['title']);
        $this->assign('category_position', $this->_get_category_position($article_info['cate_id']));
        $this->assign('article_position', $this->_get_article_position($article_info['cate_id']));
        $pn_where = array('cate_id' => $article_info['cate_id'], 'channel_id' => $article_info['channel_id']);
        $this->assign('article_pn', $this->_get_article_pn($article_info['id'], $pn_where));
        $template = $this->_get_template($article_info['cate_id'], $channel_id);
        $this->display($template['article_template']);
    }

    // 显示分类的 独立页面内容、文章列表、子级分类列表
    public function category()
    {
        $cate_id    = I('cate_id');
        $channel_id = I('channel_id');
        if (!$cate_id) {
            $this->error(L('category') . L('id') . L('error'), U('Index/index'));
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $category_info        = $ArticleCategoryModel->mFind($cate_id);
        if (!$category_info) {
            $this->error(L('category') . L('id') . L('error'), U('Index/index'));
        }

        $ArticleChannelModel = D('ArticleChannel');
        $channel_info        = $ArticleChannelModel->mFind($article_info['channel_id']);

        //检测权限
        $member_group_id                                              = session('frontend_info.group_id');
        $mFind_allows                                                = array();
        is_array($category_info['access_group_id']) && $mFind_allows = array_merge($mFind_allows, $category_info['access_group_id']);
        is_array($channel_info['access_group_id']) && $mFind_allows  = array_merge($mFind_allows, $channel_info['access_group_id']);
        if ($mFind_allows && !M_in_array($member_group_id, $mFind_allows)) {
            $this->error(L('none') . L('privilege') . L('access') . L('comma') . L('please') . L('login'), U('Member/index'));
        }

        $template = $this->_get_template($cate_id, $channel_id);
        if ($category_info['is_content'] || I('is_content')) {
            $template = $template['template'];
            //如果分类是单页面
            //缓存数据
            $cache_name  = MODULE_NAME . CONTROLLER_NAME . 'category' . $cate_id;
            $cache_value = S($cache_name);
            if ($cache_value && true !== APP_DEBUG) {
                $category_info['content'] = $cache_value;
            } else {
                $category_info['content']                                = M_content2ckplayer($category_info['content'], $category_info['thumb']);
                C('SYS_ARTICLE_SYNC_IMAGE') && $category_info['content'] = M_sync_img($category_info['content']);
                $cache_value                                             = $category_info['content'];
                S($cache_name, $cache_value, C('SYS_TD_CACHE'));
            }
        } else {
            //如果分类是列表页
            $template  = $template['list_template'];
            $child_arr = $ArticleCategoryModel->mFind_child_id($cate_id);
            $where     = array_merge($this->_get_article_where(), array(
                'channel_id' => 0,
                'cate_id'    => array('in', $child_arr),
            ));
            $channel_id && $where['channel_id']     = array('in', array(0, $channel_id));
            $category_top_info                      = $ArticleCategoryModel->mFind_top($category_info['id']);
            $attribute_where                        = M_attribute_where($category_top_info['attribute']);
            $attribute_where && $where['attribute'] = $attribute_where;

            $ArticleModel = D('Article');
            $page         = true;
            if ($category_info['s_limit']) {
                $page = $category_info['s_limit'];
                $this->assign('article_list_max', $page);
            }
            $article_lsit = $ArticleModel->mSelect($where, $page);

            $this->assign('article_list', $article_lsit);
            $this->assign('article_list_count', $ArticleModel->getPageCount($where));
        }

        $this->assign('category_info', $category_info);
        $this->assign('channel_info', $channel_info);
        $this->assign('title', $category_info['name']);
        $this->assign('category_position', $this->_get_category_position($cate_id));
        $this->assign('article_position', $this->_get_article_position($cate_id));
        $this->display($template);
    }

    // 显示频道
    public function channel()
    {
        $channel_id = I('channel_id');
        if (!$channel_id) {
            $this->redirect('Index/index');
        }

        $ArticleChannelModel = D('ArticleChannel');
        $channel_info        = $ArticleChannelModel->mFind($channel_id);

        //检测权限
        $member_group_id                                             = session('frontend_info.group_id');
        $mFind_allows                                               = array();
        is_array($channel_info['access_group_id']) && $mFind_allows = array_merge($mFind_allows, $channel_info['access_group_id']);
        if ($mFind_allows && !M_in_array($member_group_id, $mFind_allows)) {
            $this->error(L('none') . L('privilege') . L('access') . L('comma') . L('please') . L('login'), U('Member/index'));
        }

        $this->assign('channel_info', $channel_info);
        $this->assign('title', $channel_info['name']);
        $template = $this->_get_template(0, $channel_id);
        $this->display($template['channel_template']);
    }

    // 搜索文章
    public function search()
    {
        $keyword = I('keyword');
        if ('' == $keyword) {
            $this->error(L('please') . L('input') . L('keywords'), U('Index/index'));
        }
        $keyword = '%' . $keyword . '%';

        $where   = $this->_get_article_where();
        $cate_id = I('cate_id');
        if ($cate_id) {
            $ArticleCategoryModel                   = D('ArticleCategory');
            $where['cate_id']                       = array('in', $ArticleCategoryModel->mFind_child_id($cate_id));
            $category_position                      = $this->_get_category_position($cate_id);
            $attribute_where                        = M_attribute_where($category_position['attribute']);
            $attribute_where && $where['attribute'] = $attribute_where;
            $this->assign('category_position', $this->_get_category_position($cate_id));
        }
        $channel_id                         = I('cahnnel_id');
        $channel_id && $where['channel_id'] = $channel_id;

        $type = I('type');
        if (preg_match('/extend\[(.*?)\]/', $type, $matches)) {
            $type   = 'extend';
            $extend = $matches[1];
        }

        $complex = array('_logic' => 'or');
        switch ($type) {
            case 'description':
                $complex['description'] = array('like', $keyword);
                break;
            case 'content':
                $complex['content'] = array('like', $keyword);
                break;
            case 'extend':
                $complex['extend'] = array('like', '%|' . $extend . ':' . $keyword . '|%');
                break;
            case 'all':
                $complex['description'] = array('like', $keyword);
                $complex['content']     = array('like', $keyword);
                $complex['title']       = array('like', $keyword);
                $complex['extend']      = array('like', '|' . $extend . ':' . $keyword . '|');
                break;
            default:
                $complex['title'] = array('like', $keyword);
        }
        $where['_complex'] = $complex;

        $ArticleModel = D('Article');
        $article_lsit = $ArticleModel->mSelect($where, true);
        $this->assign('article_list', $article_lsit);
        $this->assign('article_list_count', $ArticleModel->getPageCount($where));

        $request = I();
        $this->assign('request', $request);
        $this->assign('title', L('search') . L('article'));
        $template = $this->_get_template(0);
        $this->display($template['list_template']);
    }

    // 获得当前文章 分类 频道模板
    private function _get_template($cate_id, $channel_id = 0)
    {
        //缓存流程 调取模板信息 过多回调
        $cache_name  = MODULE_NAME . CONTROLLER_NAME . '_get_template' . $channel_id . '_' . $cate_id;
        $cache_value = S($cache_name);
        if ($cache_value && true !== APP_DEBUG) {
            return $cache_value;
        }

        $template = array();
        // 如果频道编号存在 则查询频道是否有模板的配置 覆盖一般分类配置
        if ($channel_id) {
            $ArticleChannelModel  = D('ArticleChannel');
            $channel_info         = $ArticleChannelModel->mFind($channel_id);
            $def_channel_template = CONTROLLER_NAME . C('TMPL_FILE_DEPR') . 'channel';
            $data                 = $channel_info['ext_info'];
            $template             = array(
                's_limit'          => $this->_get_channel_template($cate_id, 's_limit', $data),
                'template'         => $this->_get_channel_template($cate_id, 'template', $data),
                'list_template'    => $this->_get_channel_template($cate_id, 'list_template', $data),
                'article_template' => $this->_get_channel_template($cate_id, 'article_template', $data),
                'channel_template' => ($channel_info['template']) ? $def_channel_template . '_' . $channel_info['template'] : $def_channel_template,
            );
        }

        empty($template['s_limit']) && $template['s_limit']                   = $this->_get_category_template($cate_id, 's_limit'); //分类调用条数
        empty($template['template']) && $template['template']                 = $this->_get_category_template($cate_id, 'template'); //分类模板
        empty($template['list_template']) && $template['list_template']       = $this->_get_category_template($cate_id, 'list_template'); //文章列表
        empty($template['article_template']) && $template['article_template'] = $this->_get_category_template($cate_id, 'article_template'); //文章模板

        //返回最终模板文件名 加模板前缀
        $def_template                 = CONTROLLER_NAME . C('TMPL_FILE_DEPR') . 'category';
        $def_list_template            = CONTROLLER_NAME . C('TMPL_FILE_DEPR') . 'list_category';
        $def_article_template         = CONTROLLER_NAME . C('TMPL_FILE_DEPR') . 'article';
        $template['template']         = ($template['template']) ? $def_template . '_' . $template['template'] : $def_template;
        $template['list_template']    = ($template['list_template']) ? $def_list_template . '_' . $template['list_template'] : $def_list_template;
        $template['article_template'] = ($template['article_template']) ? $def_article_template . '_' . $template['article_template'] : $def_article_template;

        $cache_value = $template;
        S($cache_name, $cache_value, C('SYS_TD_CACHE'));
        return $cache_value;
    }

    private function _get_category_template($cate_id, $col)
    {
        if (!$cate_id || !$col) {
            return false;
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $category_info        = $ArticleCategoryModel->mFind($cate_id);
        if ($category_info[$col]) {
            return $category_info[$col];
        }

        return (0 == $category_info['parent_id']) ? '' : $this->_get_category_template($category_info['parent_id'], $col);
    }

    private function _get_channel_template($cate_id, $col, &$data)
    {
        if (!$cate_id || !$col) {
            return false;
        }

        if (isset($data[$cate_id][$col]) && '' != $data[$cate_id][$col]) {
            return $data[$cate_id][$col];
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $parent_id            = $ArticleCategoryModel->mFindColumn($cate_id, 'parent_id');
        return ($cate_id == $parent_id) ? '' : $this->_get_channel_template($parent_id, $col, $data);
    }

    //获取当前文章分类子类位置
    private function _get_category_position($cate_id)
    {
        $cache_name  = MODULE_NAME . CONTROLLER_NAME . '_get_category_position' . $cate_id;
        $cache_value = S($cache_name);
        if ($cache_value && true !== APP_DEBUG) {
            return $cache_value;
        }

        $ArticleCategoryModel = D('ArticleCategory');
        $top_cate_id          = $ArticleCategoryModel->mFind_top_id($cate_id);
        $category_top_info    = $ArticleCategoryModel->mFind($top_cate_id);

        $where = array(
            'parent_id' => $top_cate_id,
            'if_show'   => 1,
        );
        $category_count = $ArticleCategoryModel->where($where)->count();
        $category_list  = $ArticleCategoryModel->mSelect($where, $category_count);

        $category_position                  = $category_top_info;
        $category_position['category_list'] = $category_list;

        $cache_value = $category_position;
        S($cache_name, $cache_value, C('SYS_TD_CACHE'));
        return $cache_value;
    }

    //获取当前文章分类位置
    private function _get_article_position($cate_id, $cache_name = false, $path = array())
    {
        !$cache_name && $cache_name = MODULE_NAME . CONTROLLER_NAME . '_get_article_position' . $cate_id;
        $cache_value                = S($cache_name);
        if ($cache_value && true !== APP_DEBUG) {
            return $cache_value;
        }

        $ArticleCategoryModel  = D('ArticleCategory');
        $article_category_info = $ArticleCategoryModel->mFind($cate_id);
        $path[]                = array(
            'name' => $article_category_info['name'],
            'link' => M_U('article_category', array('cate_id' => $article_category_info['id'])),
        );
        if (0 == $article_category_info['parent_id']) {
            $path[] = array(
                'name' => L('homepage'),
                'link' => M_U(),
            );
            $path = array_reverse($path);

            $cache_value = $path;
            S($cache_name, $cache_value, C('SYS_TD_CACHE'));
            return $cache_value;
        } else {
            return $this->_get_article_position($article_category_info['parent_id'], $cache_name, $path);
        }
    }

    /**
     * 获取上下关联的文章
     * @param int $id 文章编号
     * @param array $where 查询列表条件
     * @param string $sort 最后一个排序条件
     */
    private function _get_article_pn($id, $where = array(), $sort = 'sort asc,update_time desc')
    {
        $limit = C('SYS_ARTICLE_PN_LIMIT');
        if (1 > $limit) {
            return;
        }

        $article_pn = array(
            'limit' => $limit,
        );
        $where = array_merge($this->_get_article_where(), $where);
        preg_match('/,?(\w*)\s*(\w*)$/', $sort, $matchs);
        $mian_sort   = $matchs[1];
        $mian_order  = $matchs[2];
        $origin_sort = str_replace($matchs[0], '', $sort);
        $origin_sort .= $origin_sort ? ',' . $mian_sort : $mian_sort;
        //p = gt asc
        //n = lt desc
        $p_condition  = ('desc' == $mian_order) ? 'gt' : 'lt';
        $n_condition  = ('desc' == $mian_order) ? 'lt' : 'gt';
        $p_order      = ('desc' == $mian_order) ? $origin_sort . ' asc' : $origin_sort . ' desc';
        $n_order      = ('desc' == $mian_order) ? $origin_sort . ' desc' : $origin_sort . ' asc';
        $ArticleModel = D('Article');
        $article_info = $ArticleModel->mFind($id);
        //上一篇
        $where[$mian_sort] = array($p_condition, $article_info[$mian_sort]);
        $article_pn['p']   = $ArticleModel->where($where)->order($p_order)->limit($limit)->select();
        //下一篇
        $where[$mian_sort] = array($n_condition, $article_info[$mian_sort]);
        $article_pn['n']   = $ArticleModel->where($where)->order($n_order)->limit($limit)->select();
        return $article_pn;
    }

    private function _get_article_where($attribute)
    {
        //默认文章查询条件
        //1.文章是否被隐藏
        //2.文章是否到了发布时间
        //3.文章是否被审核
        $current_time = time();
        return array(
            'add_time' => array('lt', $current_time),
            'if_show'  => 1,
            'is_audit' => array('gt', 0),
        );
    }
}
