<?php

namespace App\Model;


class ArticleChannel extends Common
{
    public function m_select($where = null, $page = false)
    {
        $this->_parse_where($where);
        $this->_get_page($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->_decode_data($data_row);}
        return $data;
    }

    //返回有权管理的频道
    public function m_find_allow($type = true)
    {
        $where = array();
        //ma = manage admin 编辑属主 属组
        if (true === $type || 'ma' == $type) {
            $where['manage_id'] = session('backend_info.id');
        }

        //mg = manage group 编辑 基本信息
        if (true === $type || 'mg' == $type) {
            $where['manage_group_id'] = session('backend_info.group_id');
        }

        $m_find_allow = array(0);
        if (empty($where['manage_id']) && empty($where['manage_group_id'])) {
            return $m_find_allow;
        }

        $article_channel = $this->field('id')->m_select($where);
        foreach ($article_channel as $channel) {
            $m_find_allow[] = $channel['id'];
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
        isset($data['manage_id']) && $data['manage_id']             = '|' . implode('|', $data['manage_id']) . '|';
        isset($data['manage_group_id']) && $data['manage_group_id'] = '|' . implode('|', $data['manage_group_id']) . '|';
        isset($data['access_group_id']) && $data['access_group_id'] = serialize($data['access_group_id']);
        isset($data['ext_info']) && $data['ext_info']               = serialize($data['ext_info']);
    }
    protected function _decode_data(&$data)
    {
        isset($data['manage_id']) && $data['manage_id']             = explode('|', substr($data['manage_id'], 1, strlen($data['manage_id']) - 2));
        isset($data['manage_group_id']) && $data['manage_group_id'] = explode('|', substr($data['manage_group_id'], 1, strlen($data['manage_group_id']) - 2));
        isset($data['access_group_id']) && $data['access_group_id'] = unserialize($data['access_group_id']);
        isset($data['ext_info']) && $data['ext_info']               = unserialize($data['ext_info']);
    }
}
