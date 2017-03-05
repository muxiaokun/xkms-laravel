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
        $this->attributes['bind_info'] = $this->transfixionEncode($value, true);
    }

    //修改文件属主关系 $paths 不进行传参 就只进行 属主文件归零
    public static function bindFile($id, $paths = false)
    {
        if (!$id) {
            return false;
        }

        if (is_array($id)) {
            foreach ($id as $i) {
                $editResult = ManageUpload::bindFile($i);
                if (!$editResult) {
                    return false;
                }
            }
            return true;
        }

        $currentRouteName = Route::currentRouteName();
        preg_match_all('/(.*?)::/', $currentRouteName, $matchs);
        $routeName = implode('_', $matchs[1]);
        $routeName = $routeName ? $routeName : $currentRouteName;

        //文件解除属主
        $ownerStr = '|' . $routeName . ':' . $id . '|';
        ManageUpload::select(['id', 'bind_info'])
            ->where('bind_info', 'like', '%' . $ownerStr . '%')->get()
            ->each(function ($item, $key) use ($routeName, $id) {
                $item['bind_info'] = $item['bind_info']->reject(function ($item, $key) use ($routeName, $id) {
                    return ($item == $id && $key == $routeName);
                });
                $item->save();
            });

        //判断是否有文件需要绑定
        if (!$paths) {
            return true;
        }
        $paths = collect($paths)->map(function ($item, $key) {
            return mParseUploadUrl($item);
        });

        //文件绑定属主
        $resultEdit = false;
        ManageUpload::select(['id', 'bind_info'])
            ->whereIn('path', $paths)->get()
            ->each(function ($item, $key) use ($routeName, $id, &$resultEdit) {
                $item['bind_info'] = $item['bind_info']->put($routeName, $id);
                $resultEdit        = $item->save();
                return $resultEdit;
            });
        if (!$resultEdit) {
            return false;
        }
        return true;
    }
}
