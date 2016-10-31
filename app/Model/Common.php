<?php
// 公共

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    protected static $instance;
    protected        $guarded = [];

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * 列出数据
     * @access public
     * @param array $where 查询条件
     * @param int   $page  翻页数量
     * @return array 返回数据数组
     */
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            (new static)->mDecodeData($dataRow);
        }
        return $data;
    }

    /**
     * 添加数据
     * @param array $data
     * @return boolean
     */
    public static function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        (new static)->mEncodeData($data);
        return (new static)->create($data);
    }

    /**
     * 删除数据
     * @param mixed $id
     * @return boolean
     */
    public static function mDel($id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = ['in', $id];
        return self::where(['id' => $id])->delete();
    }

    /**
     * 编辑数据
     * @param mixed $id
     * @param array $data
     * @return boolean
     */
    public static function mEdit($id, $data)
    {
        if (!$id || !$data) {
            return false;
        }

        is_array($id) && $id = ['in', $id];
        (new static)->mEncodeData($data);
        return self::where(['id' => $id])->update($data);
    }

    /**
     * 查找数据
     * @param mixed $id
     * @return array
     */
    public static function mFind($id)
    {
        if (!$id) {
            return false;
        }

        $data = self::where(['id' => $id])->first();
        (new static)->mDecodeData($data);
        return $data;
    }

    /**
     * 查找数据id
     * @param string $value
     * @param string $columnName
     * @return string
     */
    public static function mFindId($value, $columnName)
    {
        if (!$value || !$columnName) {
            return false;
        }

        $column = self::where($columnName, '=', $value)->first();
        return $column->id;
    }

    /**
     * 查找数据
     * @param int    $id
     * @param string $columnName
     * @return string
     */
    public static function mFindColumn($id, $columnName)
    {
        if (!$id || !$columnName) {
            return false;
        }

        $column = self::select($columnName)->where(['id' => $id])->first();
        (new static)->mDecodeData($column);
        return $column[$columnName];
    }

    /**
     * 清除数据
     * @param mixed  $id
     * @param string $columnName
     * @param string $data
     * @return boolean
     */
    public static function mClean($id, $columnName = '', $data = false)
    {
        if (!$id) {
            return false;
        }

        //默认清除id的列为第二列
        if (!$columnName) {
            $columnName = self::selects[1];
        }

        is_array($id) && $id = ['in', $id];
        self::where([$columnName => $id]);
        if (0 == self::count()) {
            return true;
        }
        self::where([$columnName => $id]);
        return (false === $data) ? self::delete() : self::save([$columnName => $data]);
    }

    /**
     * 获取最大页数
     * @access public
     * @param array $where 查询条件
     * @return int 最大页数
     */
    public static function mGetPageCount($where)
    {
        (new static)->mParseWhere($where);
        $pageCount  = self::where($where)->count();
        $sysMaxPage = config('system.sys_max_page') * config('system.sys_max_row');
        return ($pageCount < $sysMaxPage) ? $pageCount : $sysMaxPage;
    }

    /**
     * 获取指定列数组合集
     * @access public
     * @param string $column 列名称
     * @return array 指定列数组合集
     */
    public static function mColumn2Array($column)
    {
        $where = self::options['where'];
        self::limit(self::count());
        $selectResult = self::select($column)->where($where)->select();
        $reArr        = [];
        foreach ($selectResult as $row) {
            $reArr[] = $row[$column];
        }
        return $reArr;
    }

    /**
     * 设置翻页中数据数量
     * @param int $maxNum
     */
    protected function mGetPage($maxNum)
    {
        if (!$maxNum) {
            return;
        }
        $maxNum = (true === $maxNum) ? config('system.sys_max_row') : $maxNum;
        return static::getInstance()->paginate($maxNum);
    }

    /**
     * 构造查询时用的like数组
     * @param array  $whereArr
     * @param string $logic AND or OR
     * @return boolean
     */
    protected function mMakeLikeArray($where, $logic = 'OR')
    {
        //将$where转换成数组
        is_string($where) && $where = explode('|', $where);
        if (!$where) {
            return false;
        }

        foreach ($where as &$mid) {
            $mid = '%|' . $mid . '|%';
        }
        $result = [
            'like',
            $where,
        ];
        'OR' != $logic && array_push($result, $logic);
        return $result;
    }

    /**
     * 生成随机字符串
     * @param type $length 长度
     * @return string
     */
    protected function _make_rand($length = 4)
    {
        $rand_range = '0123456789abcdecfghijklmnopqrstuvwxyzABCDECFGHIJKLMNOPQRSTUVWXYZ';
        $rand       = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $rand_range[rand(0, strlen($rand_range) - 1)];
        }
        return $rand;
    }

    /**
     * 建立缩进树状数据(自动去除查询数量限制)
     * @param array $config
     * @param int   $parentId
     * @param int   $level
     * @return array
     */
    protected function mMakeTree($config, $parentId = 0, $level = 0)
    {
        $listWhere = $config['list_where'];
        if (!is_array($listWhere)) {
            $listWhere = [];
        }

        $listWhere[$config['parent_id']] = $parentId;
        $countRow                        = self::where($listWhere)->count();
        $parentList                      = self::limit($countRow)->$config['list_fn']($listWhere);
        //占位符
        $retractStr = '&nbsp;';
        for ($i = 0; $i < $level; $i++) {
            $retractStr .= $retractStr;
        }
        $parentTree = [];
        $level++;
        foreach ($parentList as $parentKey => $parent) {
            if (0 != $parentId) {
                $tag                            = ($parentList[$parentKey + 1][$config['retract_col']]) ? "├" : "└";
                $parent[$config['retract_col']] = $retractStr . $tag . $parent[$config['retract_col']];
            }
            $parentTree[] = $parent;
            //这里的limit解除系统限制的数量
            $countRow  = self::where($listWhere)->count();
            $childList = self::limit($countRow)->$config['tree_fn']($listWhere, $parent[$config['id']], $level);
            foreach ($childList as $child) {
                $parentTree[] = $child;
            }
        }
        return $parentTree;
    }

    /**
     * 格式化编辑器生成的内容
     * 将内容的站内资源路径修改成相对路径
     * @param string $content
     * @return string
     */
    protected function mEncodeContent($content)
    {
        //删除相对路径前的../
        $content = htmlspecialchars_decode($content);
        if (URL_REWRITE != config('system.url_model')) {
            return $content;
        }

        $urlpreg = MGetUrlpreg();
        return preg_replace($urlpreg['pattern'], $urlpreg['replacement'], $content);
    }

    /**
     * 格式化查询条件接口
     * @param type &$data
     */
    protected function mParseWhere(&$where)
    {
    }

    /**
     * 格式化数据接口
     * @param type &$data
     */
    protected function mEncodeData(&$data)
    {
    }

    protected function mDecodeData(&$data)
    {
    }
}
