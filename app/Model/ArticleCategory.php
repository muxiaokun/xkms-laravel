<?php

namespace App\Model;


class ArticleCategory extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        if (!$query->getQuery()->orders) {
            $query->orderBy('sort', 'asc');
        }
        return parent::scopeMList($query, $where, $page);
    }

    //返回子级所有分类id 数组集合
    //$pushMe 是否包含传入id
    public function scopeMFind_child_id($query, $id, $pushMe = true)
    {
        $where           = ['parent_id' => $id];
        $articleCategory = $query->select('id')->where($where)->get();
        $categoryChildId = [];
        foreach ($articleCategory as $category) {
            $categoryChildId[] = $category['id'];
            if (0 < $query->where(['parent_id' => $category['id']])->count()) {
                $articleCategoryChild = $query->mFind_child_id($category['id'], false);
                foreach ($articleCategoryChild as $child) {
                    $categoryChildId[] = $child;
                }
            }
        }
        if ($pushMe) {
            $categoryChildId[] = $id;
        }

        //不归组的任何人都可以管理;
        return $categoryChildId;
    }

    // 寻找分类的顶级分类
    public function scopeMFind_top($query, $id)
    {
        if (!$id) {
            return false;
        }

        $articleCategoryTopId = $query->mFind_top_id($id);
        return $query->where('id', $articleCategoryTopId)->first();
    }

    // 寻找分类的顶级分类ID
    public function scopeMFind_top_id($query, $id)
    {
        if (!$id) {
            return false;
        }

        $categoryInfo = $query->select(['id', 'parent_id'])->where('id', $id)->first();
        if (0 != $categoryInfo['parent_id']) {
            return $query->mFind_top_id($categoryInfo['parent_id']);
        }

        return $categoryInfo['id'];
    }

    // 寻找分类的顶级分类列
    public function scopeMFindTopColumn($query, $id, $columnName)
    {
        if (!$id) {
            return false;
        }

        $articleCategoryTopId = $query->mFind_top_id($id);
        return $query->idWhere($articleCategoryTopId)->first()[$columnName];
    }

    //返回有权管理的频道
    public function scopeMFind_allow($query, $type = true)
    {
        $where = [];
        //ma = manage admin 编辑属主 属组
        if (session('backend_info.id') && (true === $type || 'ma' == $type)) {
            $where['manage_id'] = session('backend_info.id');
        }

        //mg = manage group 编辑 基本信息
        if (session('backend_info.group_id') && (true === $type || 'mg' == $type)) {
            $where['manage_group_id'] = session('backend_info.group_id');
        }

        $mFindAllow = [0];
        if (empty($where['manage_id']) && empty($where['manage_group_id'])) {
            return $mFindAllow;
        }

        $articleCategory = $query->select('id')->where($where)->get();
        foreach ($articleCategory as $category) {
            $mFindAllow[] = $category['id'];
        }
        return $mFindAllow;
    }

    public function scopeMEncodeData($query, $data)
    {
        //只有顶级可以设置扩展模板和属性
        if (isset($data['parent_id']) && 0 < $data['parent_id']) {
            unset($data['extend']);
            unset($data['attribute']);
        }
        isset($data['manage_id']) && $data['manage_id'] = '|' . implode('|', $data['manage_id']) . '|';
        isset($data['manage_group_id']) && $data['manage_group_id'] = '|' . implode('|',
                $data['manage_group_id']) . '|';
        isset($data['access_group_id']) && $data['access_group_id'] = serialize($data['access_group_id']);
        isset($data['content']) && $data['content'] = $query->mEncodeContent($data['content']);
        isset($data['extend']) && $data['extend'] = serialize($data['extend']);
        isset($data['attribute']) && $data['attribute'] = serialize($data['attribute']);
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['manage_id']) && $data['manage_id'] = explode('|',
            substr($data['manage_id'], 1, strlen($data['manage_id']) - 2));
        isset($data['manage_group_id']) && $data['manage_group_id'] = explode('|',
            substr($data['manage_group_id'], 1, strlen($data['manage_group_id']) - 2));
        isset($data['access_group_id']) && $data['access_group_id'] = unserialize($data['access_group_id']);
        isset($data['extend']) && $data['extend'] = unserialize($data['extend']);
        isset($data['attribute']) && $data['attribute'] = unserialize($data['attribute']);
    }
}
