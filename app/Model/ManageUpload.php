<?php

namespace App\Model;


class ManageUpload extends Common
{
    public static function deleteFile($id)
    {
        if (!$id) {
            return false;
        }

        !is_array($id) && $id = [$id];
        foreach ($id as $i) {

            $filePath      = (new static)->idWhere($id)->first()['path'];
            $delFileResult = (is_file($filePath)) ? @unlink($filePath) : true;
            if (false === $delFileResult) {
                return false;
            }

            $delResult = (new static)->idWhere($i)->delete();
            if (!$delResult) {
                return false;
            }

        }
        return true;
    }

    //修改文件属主关系 $paths 不进行传参 就只进行 属主文件归零
    public static function bindFile($item, $paths = false)
    {
        if (!$item) {
            return false;
        }

        if (is_array($item)) {
            foreach ($item as $i) {
                $editResult = static::bindFile($i);
                if (!$editResult) {
                    return false;
                }
            }
            return true;
        }

        $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
        //文件解除属主
        $ownerStr  = '|' . $routeName . ':' . $item . '|';
        $ownerList = (new static)->select(['id', 'bind_info'])
            ->where('bind_info', 'like', '%' . $ownerStr . '%')->get();
        foreach ($ownerList as $file) {
            $bindInfo = str_replace($ownerStr, '', $file['bind_info']);
            //此处的更新有可能没有影响任何数据返回0
            (new static)->where('id', $file['id'])->update(['bind_info' => $bindInfo]);
        }
        //判断是否有文件需要绑定
        if (!$paths) {
            return true;
        }

        //文件绑定属主
        $fileWhere = [
            'path' => ['in', $paths],
        ];
        $fileList  = (new static)->select(['id', 'bind_info'])->where($fileWhere)->get();
        foreach ($fileList as $file) {
            $editResult = (new static)->idWhere($file['id'])->update(['bind_info' => $file['bind_info'] . $ownerStr]);
            if (!$editResult) {
                return false;
            }
        }

        return true;
    }

    public function scopeMDecodeData($query, $data)
    {
        $data['size'] = $query->format_size($data['size']);
    }

    private function format_size($query, $size)
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
