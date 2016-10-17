<?php

namespace App\Model;


class ArticleCategory extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        self::mParseWhere($where);
        self::mGetPage($page);
        !isset(self::options['order']) && self::order('sort');
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            self::mDecodeData($dataRow);
        }
        return $data;
    }

    //获得缩进的分类树
    public static function mSelect_tree($where = null, $parentId = 0, $level = 0)
    {
        $config = [
            'list_fn'     => 'mSelect',
            'list_where'  => $where,
            'tree_fn'     => 'mSelect_tree',
            'id'          => 'id',
            'parent_id'   => 'parent_id',
            'retract_col' => 'name',
        ];
        return self::field('id,name')->mMakeTree($config, $parentId, $level);
    }

    public static function mDel($id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = ['in', $id];
        //如果被删除的分类有子级，将子级的parent_id=0
        self::where(['parent_id' => $id])->data(['parent_id' => 0])->save();
        return self::where(['id' => $id])->delete();
    }

    //返回子级所有分类id 数组集合
    //$pushMe 是否包含传入id
    public static function mFind_child_id($id, $pushMe = true)
    {
        $where           = ['parent_id' => $id];
        $articleCategory = self::field('id')->mSelect($where);
        $categoryChildId = [];
        foreach ($articleCategory as $category) {
            $categoryChildId[] = $category['id'];
            if (0 < self::where(['parent_id' => $category['id']])->count()) {
                $articleCategoryChild = self::mFind_child_id($category['id'], false);
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
    public static function mFind_top($id)
    {
        if (!$id) {
            return false;
        }

        $articleCategoryTopId = self::mFind_top_id($id);
        return self::mFind($articleCategoryTopId);
    }

    // 寻找分类的顶级分类ID
    public static function mFind_top_id($id)
    {
        if (!$id) {
            return false;
        }

        $categoryInfo = self::field('id,parent_id')->mFind($id);
        if (0 != $categoryInfo['parent_id']) {
            return self::mFind_top_id($categoryInfo['parent_id']);
        }

        return $categoryInfo['id'];
    }

    // 寻找分类的顶级分类列
    public static function mFindTopColumn($id, $columnName)
    {
        if (!$id) {
            return false;
        }

        $articleCategoryTopId = self::mFind_top_id($id);
        return self::mFindColumn($articleCategoryTopId, $columnName);
    }

    //返回有权管理的频道
    public static function mFind_allow($type = true)
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

        $articleCategory = self::field('id')->mSelect($where);
        foreach ($articleCategory as $category) {
            $mFindAllow[] = $category['id'];
        }
        return $mFindAllow;
    }

    protected static function mParseWhere(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['manage_id']) && $where['manage_id'] = self::mMakeLikeArray($where['manage_id']);
        isset($where['manage_group_id']) && $where['manage_group_id'] = self::mMakeLikeArray($where['manage_group_id']);

        if ($where['manage_id'] && $where['manage_group_id']) {
            $where['_complex'] = [
                '_logic'          => 'or',
                'manage_id'       => $where['manage_id'],
                'manage_group_id' => $where['manage_group_id'],
            ];
            unset($where['manage_id']);
            unset($where['manage_group_id']);
        }
    }

    protected static function mEncodeData(&$data)
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
        isset($data['content']) && $data['content'] = self::mEncodeContent($data['content']);
        isset($data['extend']) && $data['extend'] = serialize($data['extend']);
        isset($data['attribute']) && $data['attribute'] = serialize($data['attribute']);
    }

    protected static function mDecodeData(&$data)
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
