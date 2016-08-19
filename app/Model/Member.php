<?php

namespace App\Model;


class Member extends Common
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

        $data['register_time'] = time();
        return parent::m_add($data);
    }

    public function m_find($id)
    {
        $this->field('*,inet_ntoa(login_ip) as aip');
        return parent::m_find($id);
    }

    public function authorized($user, $pwd, $member_id)
    {
        if (!$user && !$member_id) {
            return false;
        }

        if ($member_id) {
            $where = array(
                'id' => $member_id,
            );
        } else {
            $where = array(
                'member_name' => $user,
                'is_enable'   => '1',
            );
        }
        $member_info = $this->where($where)->find();
        if ($member_info['member_pwd'] == md5($pwd . $member_info['member_rand']) || $member_id) {
            $data = array(
                'last_time' => time(),
                'login_ip'  => array('exp', 'inet_aton("' . $_SERVER['REMOTE_ADDR'] . '")'),
            );
            $this->where(array('id' => $member_info['id']))->data($data)->save();
            $member_info = $this->m_find($member_info['id']);
            return $member_info;
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
        if ($data['member_pwd']) {
            $rand_str            = $this->_make_rand();
            $data['member_pwd']  = md5($data['member_pwd'] . $rand_str);
            $data['member_rand'] = $rand_str;
        } else {
            unset($data['member_pwd']);
            unset($data['member_rand']);
        }
        !isset($data['group_id']) && $data['group_id'] = array(1);
        //组合权限
        isset($data['group_id']) && $data['group_id']   = '|' . implode('|', $data['group_id']) . '|';
        isset($data['privilege']) && $data['privilege'] = implode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info']   = serialize($data['ext_info']);
    }

    protected function _decode_data(&$data)
    {
        unset($data['member_pwd']);
        unset($data['member_rand']);
        isset($data['group_id']) && $data['group_id']   = explode('|', substr($data['group_id'], 1, strlen($data['group_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info']   = unserialize($data['ext_info']);
    }
}
