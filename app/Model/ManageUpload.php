<?php

namespace App\Model;


class ManageUpload extends Common
{
    //获得全部或者部分管理组列表
    public function mSelect($where = null, $page = false)
    {
        $this->mGetPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->where($where)->select();
        foreach ($data as &$dataRow) {
            $this->mDecodeData($dataRow);
        }
        return $data;
    }

    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $userId            = ('Admin' == MODULE_NAME) ? session('backend_info.id') : session('frontend_info.id');
        $data['user_id']   = $userId;
        $userType          = ('Admin' == MODULE_NAME) ? 1 : 2;
        $data['user_type'] = $userType;
        $data['add_time']  = time();
        return $this->add($data);
    }

    public function mDel($id)
    {
        if (!$id) {
            return false;
        }

        !is_array($id) && $id = [$id];
        foreach ($id as $i) {
            $delFileResult = $this->_mDel_file($i);
            if (false === $delFileResult) {
                return false;
            }

            $delResult = $this->where(['id' => $i])->delete();
            if (!$delResult) {
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
                $editResult = $this->mEdit($i);
                if (!$editResult) {
                    return false;
                }

            }
        }

        //文件解除属主
        $ownerStr   = '|' . CONTROLLER_NAME . ':' . $item . '|';
        $ownerWhere = [
            'bind_info' => ['like', '%' . $ownerStr . '%'],
        ];
        $ownerList  = $this->field('id,bind_info')->where($ownerWhere)->select();
        foreach ($ownerList as $file) {
            $bindInfo = str_replace($ownerStr, '', $file['bind_info']);
            //此处的更新有可能没有影响任何数据返回0
            $this->where(['id' => $file['id']])->data(['bind_info' => $bindInfo])->save();
        }

        //判断是否有文件需要绑定
        if (!$paths) {
            return true;
        }

        //文件绑定属主
        $fileWhere = [
            'path' => ['in', $paths],
        ];
        $fileList  = $this->field('id,bind_info')->where($fileWhere)->select();
        foreach ($fileList as $file) {
            $editResult = $this->where(['id' => $file['id']])->data(['bind_info' => $file['bind_info'] . $ownerStr])->save();
            if (!$editResult) {
                return false;
            }

        }
        return true;
    }

    public function mFind($id, $isPath = false)
    {
        if (!$id) {
            return false;
        }

        $where = ['id' => $id];
        if ($isPath) {
            $where = ['path' => $id];
        }

        $manageUpload = $this->where($where)->find();
        return $manageUpload;
    }

    protected function mDecodeData(&$data)
    {
        $data['size'] = $this->format_size($data['size']);
    }

    private function _mDel_file($id)
    {
        $filePath = $this->mFindColumn($id, 'path');
        return (is_file($filePath)) ? @unlink($filePath) : true;
    }

    private function format_size($size)
    {
        $reStr = '';
        switch ($size) {
            //GB
            case 0 < intval($size / 1073741824):
                $reStr .= round($size / 1073741824, 3) . " GB";
                break;
            //MB
            case 0 < intval($size / 1048576):
                $reStr .= round($size / 1048576, 3) . " MB";
                $size = $size % 1048576;
                break;
            //KB
            case 0 < intval($size / 1024):
                $reStr .= round($size / 1024, 3) . " KB";
                $size = $size % 1024;
                break;
            //Byte
            default:
                $reStr .= $size . " B";
                break;
        }
        return $reStr;
    }
}
