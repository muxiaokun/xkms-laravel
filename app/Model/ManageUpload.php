<?php

namespace App\Model;

use Illuminate\Support\Facades\Route;

class ManageUpload extends Common
{

    public function getSizeAttribute($value)
    {
        return mFormatSize($value);
    }

    public function getBindInfoAttribute($value)
    {
        return $this->transfixionDecode($value, true);
    }

    public function setBindInfoAttribute($value)
    {
        //['id'=>?,'paths'=>?]
        $old_info                      = $this->getAttribute('bind_info');
        $this->attributes['bind_info'] = $this->transfixionEncode($value);
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

        $routeName = Route::currentRouteName();
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
            $editResult = (new static)->colWhere($file['id'])->update(['bind_info' => $file['bind_info'] . $ownerStr]);
            if (!$editResult) {
                return false;
            }
        }

        return true;
    }
}
