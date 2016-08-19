<?php

namespace App\Model;


class ArticleCategory extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_parse_where($where);
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('sort');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    //获得缩进的分类树
    public function m_select_tree($where = null, $parent_id = 0, $level = 0)
    {
        $config = array(
            'list_fn'     => 'm_select',
            'list_where'  => $where,
            'tree_fn'     => 'm_select_tree',
            'id'          => 'id',
            'parent_id'   => 'parent_id',
            'retract_col' => 'name',
        );
        return $this->field('id,name')->_make_tree($config, $parent_id, $level);
    }

    public function m_del($id)
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
    //$push_me 是否包含传入id
    public function m_find_child_id($id, $push_me = true)
    {
        $where             = array('parent_id' => $id);
        $article_category  = $this->field('id')->m_select($where);
        $category_child_id = array();
        foreach ($article_category as $category) {
            $category_child_id[] = $category['id'];
            if (0 < $this->where(array('parent_id' => $category['id']))->count()) {
                $article_category_child = $this->m_find_child_id($category['id'], false);
                foreach ($article_category_child as $child) {
                    $category_child_id[] = $child;
                }
            }
        }
        if ($push_me) {
            $category_child_id[] = $id;
        }

        //不归组的任何人都可以管理;
        return $category_child_id;
    }

    // 寻找分类的顶级分类
    public function m_find_top($id)
    {
        if (!$id) {
            return false;
        }

        $article_category_top_id = $this->m_find_top_id($id);
        return $this->m_find($article_category_top_id);
    }

    // 寻找分类的顶级分类ID
    public function m_find_top_id($id)
    {
        if (!$id) {
            return false;
        }

        $category_info = $this->field('id,parent_id')->m_find($id);
        if (0 != $category_info['parent_id']) {
            return $this->m_find_top_id($category_info['parent_id']);
        }

        return $category_info['id'];
    }

    // 寻找分类的顶级分类列
    public function m_find_top_column($id, $column_name)
    {
        if (!$id) {
            return false;
        }

        $article_category_top_id = $this->m_find_top_id($id);
        return $this->m_find_column($article_category_top_id, $column_name);
    }

    //返回有权管理的频道
    public function m_find_allow($type = true)
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

        $m_find_allow = array(0);
        if (empty($where['manage_id']) && empty($where['manage_group_id'])) {
            return $m_find_allow;
        }

        $article_category = $this->field('id')->m_select($where);
        foreach ($article_category as $category) {
            $m_find_allow[] = $category['id'];
        }
        return $m_find_allow;
    }

    protected function _parse_where(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['manage_id']) && $where['manage_id']             = $this->_make_like_arr($where['manage_id']);
        isset($where['manage_group_id']) && $where['manage_group_id'] = $this->_make_like_arr($where['manage_group_id']);

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

    protected function _encode_data(&$data)
    {
        //只有顶级可以设置扩展模板和属性
        if (isset($data['parent_id']) && 0 < $data['parent_id']) {
            unset($data['extend']);
            unset($data['attribute']);
        }
        isset($data['manage_id']) && $data['manage_id']             = '|' . implode('|', $data['manage_id']) . '|';
        isset($data['manage_group_id']) && $data['manage_group_id'] = '|' . implode('|', $data['manage_group_id']) . '|';
        isset($data['access_group_id']) && $data['access_group_id'] = serialize($data['access_group_id']);
        isset($data['content']) && $data['content']                 = $this->_encode_content($data['content']);
        isset($data['extend']) && $data['extend']                   = serialize($data['extend']);
        isset($data['attribute']) && $data['attribute']             = serialize($data['attribute']);
    }

    protected function _decode_data(&$data)
    {
        isset($data['manage_id']) && $data['manage_id']             = explode('|', substr($data['manage_id'], 1, strlen($data['manage_id']) - 2));
        isset($data['manage_group_id']) && $data['manage_group_id'] = explode('|', substr($data['manage_group_id'], 1, strlen($data['manage_group_id']) - 2));
        isset($data['access_group_id']) && $data['access_group_id'] = unserialize($data['access_group_id']);
        isset($data['extend']) && $data['extend']                   = unserialize($data['extend']);
        isset($data['attribute']) && $data['attribute']             = unserialize($data['attribute']);
    }
}
