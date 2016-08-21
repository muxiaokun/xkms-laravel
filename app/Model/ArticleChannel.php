<?php

namespace App\Model;


class ArticleChannel extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->parseWhere($where);
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    //返回有权管理的频道
    public function mFind_allow($type = true)
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

        $mFind_allow = array(0);
        if (empty($where['manage_id']) && empty($where['manage_group_id'])) {
            return $mFind_allow;
        }

        $article_channel = $this->field('id')->mSelect($where);
        foreach ($article_channel as $channel) {
            $mFind_allow[] = $channel['id'];
        }
        return $mFind_allow;
    }

    protected function parseWhere(&$where)
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

    protected function encodeData(&$data)
    {
        isset($data['manage_id']) && $data['manage_id']             = '|' . implode('|', $data['manage_id']) . '|';
        isset($data['manage_group_id']) && $data['manage_group_id'] = '|' . implode('|', $data['manage_group_id']) . '|';
        isset($data['access_group_id']) && $data['access_group_id'] = serialize($data['access_group_id']);
        isset($data['ext_info']) && $data['ext_info']               = serialize($data['ext_info']);
    }
    protected function decodeData(&$data)
    {
        isset($data['manage_id']) && $data['manage_id']             = explode('|', substr($data['manage_id'], 1, strlen($data['manage_id']) - 2));
        isset($data['manage_group_id']) && $data['manage_group_id'] = explode('|', substr($data['manage_group_id'], 1, strlen($data['manage_group_id']) - 2));
        isset($data['access_group_id']) && $data['access_group_id'] = unserialize($data['access_group_id']);
        isset($data['ext_info']) && $data['ext_info']               = unserialize($data['ext_info']);
    }
}
