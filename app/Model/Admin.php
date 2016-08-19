<?php

namespace App\Model;


class Admin extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_parse_where($where);
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->field('*,inet_ntoa(login_ip) as aip')->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    public function m_add($data)
    {
        if (!$data) {
            return false;
        }

        $data['add_time'] = time();
        return parent::m_add($data);
    }

    public function m_del($id)
    {
        //不能删除root用户
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return parent::m_del($id);
    }

    public function m_find($id)
    {
        $this->field('*,inet_ntoa(login_ip) as aip');
        return parent::m_find($id);
    }

    public function authorized($user, $pwd)
    {
        if (!$user) {
            return false;
        }

        $where = array(
            'admin_name' => $user,
            'is_enable'  => '1',
        );
        $admin_info = $this->where($where)->find();
        if ($admin_info['admin_pwd'] == md5($pwd . $admin_info['admin_rand'])) {
            $data = array(
                'last_time' => time(),
                'login_ip'  => array('exp', 'inet_aton("' . $_SERVER['REMOTE_ADDR'] . '")'),
            );
            $this->where(array('id' => $admin_info['id']))->data($data)->save();
            $admin_info = $this->m_find($admin_info['id']);
            return $admin_info;
        } else {
            return false;
        }
    }

    protected function _parse_where(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['group_id']) && $where['group_id'] = $this->_make_like_arr($where['group_id']);
    }

    protected function _encode_data(&$data)
    {
        if (isset($data['id']) && (1 == $data['id'] || (is_array($data['id']) && in_array(1, $data['id'])))) {
            unset($data['privilege']);
        }
        if ($data['admin_pwd']) {
            $rand_str           = $this->_make_rand();
            $data['admin_pwd']  = md5($data['admin_pwd'] . $rand_str);
            $data['admin_rand'] = $rand_str;
        } else {
            unset($data['admin_pwd']);
            unset($data['admin_rand']);
        }
        //组合权限
        isset($data['group_id']) && $data['group_id']   = '|' . implode('|', $data['group_id']) . '|';
        isset($data['privilege']) && $data['privilege'] = implode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info']   = serialize($data['ext_info']);
    }

    protected function _decode_data(&$data)
    {
        unset($data['admin_pwd']);
        unset($data['admin_rand']);
        isset($data['group_id']) && $data['group_id']   = explode('|', substr($data['group_id'], 1, strlen($data['group_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info']   = unserialize($data['ext_info']);
    }
}
