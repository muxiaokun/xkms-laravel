<?php

namespace App\Model;


class AdminGroup extends Common
{
    //获得全部或者部分管理组列表
    public function m_select($where = null, $page = false)
    {
        $this->_parse_where($where);
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    public function m_del($id)
    {
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return parent::m_del($id);
    }

    //查找出组权限
    public function m_find_privilege($id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = array('in', $id);
        $data                = $this->field('privilege')->where(array('id' => $id, 'is_enable' => 1))->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        $privilege = array();
        foreach ($data as $group) {
            $privilege = array_merge($privilege, $group['privilege']);
        }
        return $privilege;
    }

    //返回有权管理的组
    public function m_find_allow()
    {
        $where = array(
            'manage_id' => session('backend_info.id'),
        );
        $manage_group = $this->field('id')->m_select($where);
        $m_find_allow = array();
        foreach ($manage_group as $group) {
            $m_find_allow[] = $group['id'];
        }
        //不归组的任何人都可以管理
        $m_find_allow[] = 0;
        return $m_find_allow;
    }

    protected function _parse_where(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['manage_id']) && $where['manage_id'] = $this->_make_like_arr($where['manage_id']);
    }

    //检查和格式化数据
    protected function _encode_data(&$data)
    {
        if (isset($data['id']) && (1 == $data['id'] || (is_array($data['id']) && in_array(1, $data['id'])))) {
            unset($data['privilege']);
        }
        isset($data['manage_id']) && $data['manage_id']       = '|' . implode('|', array_unique($data['manage_id'])) . '|';
        isset($data['privilege']) && $data['privilege']       = implode('|', $data['privilege']);
        isset($data['ext_template']) && $data['ext_template'] = serialize($data['ext_template']);
    }

    //检查和去除格式化数据
    protected function _decode_data(&$data)
    {
        isset($data['manage_id']) && $data['manage_id']       = explode('|', substr($data['manage_id'], 1, strlen($data['manage_id']) - 2));
        isset($data['privilege']) && $data['privilege']       = explode('|', $data['privilege']);
        isset($data['ext_template']) && $data['ext_template'] = unserialize($data['ext_template']);
    }
}
