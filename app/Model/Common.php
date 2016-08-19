<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
// 公共

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    /**
     * 列出数据
     * @access public
     * @param array $where 查询条件
     * @param int $page 翻页数量
     * @return array 返回数据数组
     */
    public function m_select($where = null, $page = false)
    {
        $this->_get_page($page);
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    /**
     * 添加数据
     * @param array $data
     * @return boolean
     */
    public function m_add($data)
    {
        if (!$data) {
            return false;
        }

        $this->_encode_data($data);
        return $this->add($data);
    }

    /**
     * 删除数据
     * @param mixed $id
     * @return boolean
     */
    public function m_del($id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = array('in', $id);
        return $this->where(array('id' => $id))->delete();
    }

    /**
     * 编辑数据
     * @param mixed $id
     * @param array $data
     * @return boolean
     */
    public function m_edit($id, $data)
    {
        if (!$id || !$data) {
            return false;
        }

        is_array($id) && $id = array('in', $id);
        $this->_encode_data($data);
        return $this->where(array('id' => $id))->data($data)->save();
    }

    /**
     * 查找数据
     * @param mixed $id
     * @return array
     */
    public function m_find($id)
    {
        if (!$id) {
            return false;
        }

        $data = $this->where(array('id' => $id))->find();
        $this->_decode_data($data);
        return $data;
    }

    /**
     * 查找数据id
     * @param string $value
     * @param string $column_name
     * @return string
     */
    public function m_find_id($value, $column_name = '')
    {
        if (!$value) {
            return false;
        }

        //默认查询id的列为第二列
        if (!$column_name) {
            $column_name = $this->fields[1];
        }
        $column = $this->field('id')->where(array($column_name => $value))->find();
        return $column['id'];
    }

    /**
     * 查找数据
     * @param int $id
     * @param string $column_name
     * @return string
     */
    public function m_find_column($id, $column_name)
    {
        if (!$id || !$column_name) {
            return false;
        }

        $column = $this->field($column_name)->where(array('id' => $id))->find();
        $this->_decode_data($column);
        return $column[$column_name];
    }

    /**
     * 清除数据
     * @param mixed $id
     * @param string $column_name
     * @param string $data
     * @return boolean
     */
    public function m_clean($id, $column_name = '', $data = false)
    {
        if (!$id) {
            return false;
        }

        //默认清除id的列为第二列
        if (!$column_name) {
            $column_name = $this->fields[1];
        }

        is_array($id) && $id = array('in', $id);
        $this->where(array($column_name => $id));
        if (0 == $this->count()) {
            return true;
        }
        $this->where(array($column_name => $id));
        return (false === $data) ? $this->delete() : $this->save(array($column_name => $data));
    }

    /**
     * 获取最大页数
     * @access public
     * @param array $where 查询条件
     * @return int 最大页数
     */
    public function get_page_count($where)
    {
        $this->_parse_where($where);
        $page_count   = $this->where($where)->count();
        $sys_max_page = C('SYS_MAX_PAGE') * C('SYS_MAX_ROW');
        return ($page_count < $sys_max_page) ? $page_count : $sys_max_page;
    }

    /**
     * 获取指定列数组合集
     * @access public
     * @param string $column 列名称
     * @return array 指定列数组合集
     */
    public function col_arr($column)
    {
        $where = $this->options['where'];
        $this->limit($this->count());
        $select_result = $this->field($column)->where($where)->select();
        $re_arr        = array();
        foreach ($select_result as $row) {
            $re_arr[] = $row[$column];
        }
        return $re_arr;
    }

    /**
     * 设置翻页中数据数量
     * @param int $max_num
     */
    protected function _get_page($max_num)
    {
        if (!$max_num) {
            return;
        }

        $max_num = (true === $max_num) ? C('SYS_MAX_ROW') : $max_num;
        //p 是 Think\Page中的p的配置
        $p       = I('p');
        $p       = ($p < C('SYS_MAX_PAGE')) ? $p : C('SYS_MAX_PAGE');
        $max_num = ($p) ? $p . "," . $max_num : "1," . $max_num;
        $this->page($max_num);
    }

    /**
     * 构造查询时用的like数组
     * @param array $where_arr
     * @param string $logic AND or OR
     * @return boolean
     */
    protected function _make_like_arr($where, $logic = 'OR')
    {
        //将$where转换成数组
        is_string($where) && $where = explode('|', $where);
        if (!$where) {
            return false;
        }

        foreach ($where as &$mid) {
            $mid = '%|' . $mid . '|%';
        }
        $result = array(
            'like', $where,
        );
        'OR' != $logic && array_push($result, $logic);
        return $result;
    }

    /**
     * 生成随机字符串
     * @param type $length 长度
     * @return string
     */
    protected function _make_rand($length = 4)
    {
        $rand_arr = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        );
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $rand_arr[rand(0, count($rand_arr) - 1)];
        }
        return $rand;
    }

    /**
     * 建立缩进树状数据(自动去除查询数量限制)
     * @param array $config
     * @param int $parent_id
     * @param int $level
     * @return array
     */
    protected function _make_tree($config, $parent_id = 0, $level = 0)
    {
        $list_where = $config['list_where'];
        if (!is_array($list_where)) {
            $list_where = array();
        }

        $list_where[$config['parent_id']] = $parent_id;
        $count_row                        = $this->where($list_where)->count();
        $parent_list                      = $this->limit($count_row)->$config['list_fn']($list_where);
        //占位符
        $retract_str = '&nbsp;';
        for ($i = 0; $i < $level; $i++) {$retract_str .= $retract_str;}
        $parent_tree = array();
        $level++;
        foreach ($parent_list as $parent_key => $parent) {
            if (0 != $parent_id) {
                $tag                            = ($parent_list[$parent_key + 1][$config['retract_col']]) ? "├" : "└";
                $parent[$config['retract_col']] = $retract_str . $tag . $parent[$config['retract_col']];
            }
            $parent_tree[] = $parent;
            //这里的limit解除系统限制的数量
            $count_row  = $this->where($list_where)->count();
            $child_list = $this->limit($count_row)->$config['tree_fn']($list_where, $parent[$config['id']], $level);
            foreach ($child_list as $child) {
                $parent_tree[] = $child;
            }
        }
        return $parent_tree;
    }

    /**
     * 格式化编辑器生成的内容
     * 将内容的站内资源路径修改成相对路径
     * @param string $content
     * @return string
     */
    protected function _encode_content($content)
    {
        //删除相对路径前的../
        $content = htmlspecialchars_decode($content);
        if (URL_REWRITE != C('URL_MODEL')) {
            return $content;
        }

        $urlpreg = M_get_urlpreg();
        return preg_replace($urlpreg['pattern'], $urlpreg['replacement'], $content);
    }

    /**
     * 格式化查询条件接口
     * @param type &$data
     */
    protected function _parse_where(&$where)
    {}

    /**
     * 格式化数据接口
     * @param type &$data
     */
    protected function _encode_data(&$data)
    {}
    protected function _decode_data(&$data)
    {}
}
