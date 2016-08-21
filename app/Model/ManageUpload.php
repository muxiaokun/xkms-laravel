<?php

namespace App\Model;


class ManageUpload extends Common
{
    //获得全部或者部分管理组列表
    public function mSelect($where = null, $page = false)
    {
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $user_id           = ('Admin' == MODULE_NAME) ? session('backend_info.id') : session('frontend_info.id');
        $data['user_id']   = $user_id;
        $user_type         = ('Admin' == MODULE_NAME) ? 1 : 2;
        $data['user_type'] = $user_type;
        $data['add_time']  = time();
        return $this->add($data);
    }

    public function mDel($id)
    {
        if (!$id) {
            return false;
        }

        !is_array($id) && $id = array($id);
        foreach ($id as $i) {
            $del_file_result = $this->_mDel_file($i);
            if (false === $del_file_result) {
                return false;
            }

            $del_result = $this->where(array('id' => $i))->delete();
            if (!$del_result) {
                return false;
            }

        }
        return true;
    }

    //修改文件属主关系 $paths 不进行传参 就只进行 属主文件归零
    public function mEdit($item, $paths = false)
    {
        if (!$item) {
            return false;
        }

        if (is_array($item)) {
            foreach ($item as $i) {
                $edit_result = $this->mEdit($i);
                if (!$edit_result) {
                    return false;
                }

            }
        }

        //文件解除属主
        $owner_str   = '|' . CONTROLLER_NAME . ':' . $item . '|';
        $owner_where = array(
            'bind_info' => array('like', '%' . $owner_str . '%'),
        );
        $owner_list = $this->field('id,bind_info')->where($owner_where)->select();
        foreach ($owner_list as $file) {
            $bind_info = str_replace($owner_str, '', $file['bind_info']);
            //此处的更新有可能没有影响任何数据返回0
            $this->where(array('id' => $file['id']))->data(array('bind_info' => $bind_info))->save();
        }

        //判断是否有文件需要绑定
        if (!$paths) {
            return true;
        }

        //文件绑定属主
        $file_where = array(
            'path' => array('in', $paths),
        );
        $file_list = $this->field('id,bind_info')->where($file_where)->select();
        foreach ($file_list as $file) {
            $edit_result = $this->where(array('id' => $file['id']))->data(array('bind_info' => $file['bind_info'] . $owner_str))->save();
            if (!$edit_result) {
                return false;
            }

        }
        return true;
    }

    public function mFind($id, $is_path = false)
    {
        if (!$id) {
            return false;
        }

        $where = array('id' => $id);
        if ($is_path) {
            $where = array('path' => $id);
        }

        $manage_upload = $this->where($where)->find();
        return $manage_upload;
    }

    protected function decodeData(&$data)
    {
        $data['size'] = $this->format_size($data['size']);
    }

    private function _mDel_file($id)
    {
        $file_path = $this->mFindColumn($id, 'path');
        return (is_file($file_path)) ? @unlink($file_path) : true;
    }

    private function format_size($size)
    {
        $re_str = '';
        switch ($size) {
            //GB
            case 0 < intval($size / 1073741824):
                $re_str .= round($size / 1073741824, 3) . " GB";
                break;
            //MB
            case 0 < intval($size / 1048576):
                $re_str .= round($size / 1048576, 3) . " MB";
                $size = $size % 1048576;
                break;
            //KB
            case 0 < intval($size / 1024):
                $re_str .= round($size / 1024, 3) . " KB";
                $size = $size % 1024;
                break;
            //Byte
            default:
                $re_str .= $size . " B";
                break;
        }
        return $re_str;
    }
}
