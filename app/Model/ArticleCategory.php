<?php

namespace App\Model;


class ArticleCategory extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->mParseWhere($where);
        $this->mGetPage($page);
        !isset($this->options['order']) && $this->order('sort');
        $data = $this->where($where)->select();
        foreach ($data as &$dataRow) {$this->mDecodeData($dataRow);}
        return $data;
    }

    //获得缩进的分类树
    public function mSelect_tree($where = null, $parentId = 0, $level = 0)
    {
        $config = array(
            'list_fn'     => 'mSelect',
            'list_where'  => $where,
            'tree_fn'     => 'mSelect_tree',
            'id'          => 'id',
            'parent_id'   => 'parent_id',
            'retract_col' => 'name',
        );
        return $this->field('id,name')->mMakeTree($config, $parentId, $level);
    }

    public function mDel($id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = array('in', $id);
        //如果被删除的分类有子级，将子级的parent_id=0
        $this->where(array('parent_id' => $id))->data(array('parent_id' => 0))->save();
        return $this->where(array('id' => $id))->delete();
    }

    //返回子级所有分类id 数组集合
    //$pushMe 是否包含传入id
    public function mFind_child_id($id, $pushMe = true)
    {
        $where             = array('parent_id' => $id);
        $articleCategory  = $this->field('id')->mSelect($where);
        $categoryChildId = array();
        foreach ($articleCategory as $category) {
            $categoryChildId[] = $category['id'];
            if (0 < $this->where(array('parent_id' => $category['id']))->count()) {
                $articleCategoryChild = $this->mFind_child_id($category['id'], false);
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
    public function mFind_top($id)
    {
        if (!$id) {
            return false;
        }

        $articleCategoryTopId = $this->mFind_top_id($id);
        return $this->mFind($articleCategoryTopId);
    }

    // 寻找分类的顶级分类ID
    public function mFind_top_id($id)
    {
        if (!$id) {
            return false;
        }

        $categoryInfo = $this->field('id,parent_id')->mFind($id);
        if (0 != $categoryInfo['parent_id']) {
            return $this->mFind_top_id($categoryInfo['parent_id']);
        }

        return $categoryInfo['id'];
    }

    // 寻找分类的顶级分类列
    public function mFindTopColumn($id, $columnName)
    {
        if (!$id) {
            return false;
        }

        $articleCategoryTopId = $this->mFind_top_id($id);
        return $this->mFindColumn($articleCategoryTopId, $columnName);
    }

    //返回有权管理的频道
    public function mFind_allow($type = true)
    {
        $where = array();
        //ma = manage admin 编辑属主 属组
        if (session('backend_info.id') && (true === $type || 'ma' == $type)) {
            $where['manage_id'] = session('backend_info.id');
        }

        //mg = manage group 编辑 基本信息
        if (session('backend_info.group_id') && (true === $type || 'mg' == $type)) {
            $where['manage_group_id'] = session('backend_info.group_id');
        }

        $mFindAllow = array(0);
        if (empty($where['manage_id']) && empty($where['manage_group_id'])) {
            return $mFindAllow;
        }

        $articleCategory = $this->field('id')->mSelect($where);
        foreach ($articleCategory as $category) {
            $mFindAllow[] = $category['id'];
        }
        return $mFindAllow;
    }

    protected function mParseWhere(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['manage_id']) && $where['manage_id']             = $this->mMakeLikeArray($where['manage_id']);
        isset($where['manage_group_id']) && $where['manage_group_id'] = $this->mMakeLikeArray($where['manage_group_id']);

        if ($where['manage_id'] && $where['manage_group_id']) {
            $where['_complex'] = array(
                '_logic'          => 'or',
                'manage_id'       => $where['manage_id'],
                'manage_group_id' => $where['manage_group_id'],
            );
            unset($where['manage_id']);
            unset($where['manage_group_id']);
        }
    }

    protected function mEncodeData(&$data)
    {
        //只有顶级可以设置扩展模板和属性
        if (isset($data['parent_id']) && 0 < $data['parent_id']) {
            unset($data['extend']);
            unset($data['attribute']);
        }
        isset($data['manage_id']) && $data['manage_id']             = '|' . implode('|', $data['manage_id']) . '|';
        isset($data['manage_group_id']) && $data['manage_group_id'] = '|' . implode('|', $data['manage_group_id']) . '|';
        isset($data['access_group_id']) && $data['access_group_id'] = serialize($data['access_group_id']);
        isset($data['content']) && $data['content']                 = $this->mEncodeContent($data['content']);
        isset($data['extend']) && $data['extend']                   = serialize($data['extend']);
        isset($data['attribute']) && $data['attribute']             = serialize($data['attribute']);
    }

    protected function mDecodeData(&$data)
    {
        isset($data['manage_id']) && $data['manage_id']             = explode('|', substr($data['manage_id'], 1, strlen($data['manage_id']) - 2));
        isset($data['manage_group_id']) && $data['manage_group_id'] = explode('|', substr($data['manage_group_id'], 1, strlen($data['manage_group_id']) - 2));
        isset($data['access_group_id']) && $data['access_group_id'] = unserialize($data['access_group_id']);
        isset($data['extend']) && $data['extend']                   = unserialize($data['extend']);
        isset($data['attribute']) && $data['attribute']             = unserialize($data['attribute']);
    }
}
