<?php

namespace App\Model;


class Member extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->parseWhere($where);
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->field('*,inet_ntoa(login_ip) as aip')->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $data['register_time'] = time();
        return parent::mAdd($data);
    }

    public function mFind($id)
    {
        $this->field('*,inet_ntoa(login_ip) as aip');
        return parent::mFind($id);
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
            $member_info = $this->mFind($member_info['id']);
            return $member_info;
        } else {
            return false;
        }
    }

    protected function parseWhere(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['group_id']) && $where['group_id'] = $this->_make_like_arr($where['group_id']);
    }

    protected function encodeData(&$data)
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

    protected function decodeData(&$data)
    {
        unset($data['member_pwd']);
        unset($data['member_rand']);
        isset($data['group_id']) && $data['group_id']   = explode('|', substr($data['group_id'], 1, strlen($data['group_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info']   = unserialize($data['ext_info']);
    }
}
