<?php
// 公共

namespace App\Model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    public $guarded = [];

    protected $whereClauses = [
        'Between',
        'NotBetween',
        'In',
        'NotIn',
        'Null',
        'NotNull',
        'Date',
        'Month',
        'Day',
        'Year',
    ];

    /**
     * 列出数据
     * @access public
     * @param array $where 查询条件
     * @param int $page 翻页数量
     * @return array 返回数据数组
     */
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mParseWhere($where);
        if (!$query->getQuery()->orders) {
            $query->orderBy('id', 'desc');
        }
        if ($page) {
            $data = $query->mGetPage($page);
        } else {
            $data = $query->get();
        }
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    /**
     * 添加数据
     * @param array $data
     * @return boolean
     */
    public function scopeMAdd($query, $data)
    {
        if (!$data) {
            return false;
        }

        $query->mEncodeData($data);
        return $query->create($data);
    }

    /**
     * 删除数据
     * @param mixed $id
     * @return boolean
     */
    public function scopeMDel($query, $id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = ['in', $id];
        return $query->where(['id' => $id])->delete();
    }

    /**
     * 编辑数据
     * @param mixed $id
     * @param array $data
     * @return boolean
     */
    public function scopeMEdit($query, $id, $data)
    {
        if (!$id || !$data) {
            return false;
        }

        is_array($id) && $id = ['in', $id];
        $query->mEncodeData($data);
        return $query->where(['id' => $id])->update($data);
    }

    /**
     * 查找数据
     * @param mixed $id
     * @return array
     */
    public function scopeMFind($query, $id)
    {
        if (!$id) {
            return false;
        }

        $data = $query->where(['id' => $id])->first();
        $query->mDecodeData($data);
        return $data;
    }

    /**
     * 查找数据id
     * @param string $value
     * @param string $columnName
     * @return string
     */
    public function scopeMFindId($query, $value, $columnName)
    {
        if (!$value || !$columnName) {
            return false;
        }

        $column = $query->where($columnName, '=', $value)->first();
        return $column->id;
    }

    /**
     * 查找数据
     * @param int $id
     * @param string $columnName
     * @return string
     */
    public function scopeMFindColumn($query, $id, $columnName)
    {
        if (!$id || !$columnName) {
            return false;
        }

        $column = $query->select($columnName)->where(['id' => $id])->first();
        $query->mDecodeData($column);
        return $column[$columnName];
    }

    /**
     * 清除数据
     * @param mixed $id
     * @param string $columnName
     * @param string $data
     * @return boolean
     */
    public function scopeMClean($query, $id, $columnName = '', $data = false)
    {
        if (!$id) {
            return false;
        }

        //默认清除id的列为第二列
        if (!$columnName) {
            $columnName = $query->selects[1];
        }

        is_array($id) && $id = ['in', $id];
        $query->where([$columnName => $id]);
        if (0 == $query->count()) {
            return true;
        }
        $query->where([$columnName => $id]);
        return (false === $data) ? $query->delete() : $query->save([$columnName => $data]);
    }

    /**
     * 获取最大页数
     * @access public
     * @param array $where 查询条件
     * @return int 最大页数
     */
    public function scopeMGetPageCount($query, $where)
    {
        $query->mParseWhere($where);
        $pageCount  = $query->where($where)->count();
        $sysMaxPage = config('system.sys_max_page') * config('system.sys_max_row');
        return ($pageCount < $sysMaxPage) ? $pageCount : $sysMaxPage;
    }

    /**
     * 获取指定列数组合集
     * @access public
     * @param string $column 列名称
     * @return array 指定列数组合集
     */
    public function scopeMColumn2Array($query, $column)
    {
        $selectResult = $query->select($column)->get();
        $reArr        = [];
        foreach ($selectResult as $row) {
            $reArr[] = $row[$column];
        }
        return new Collection($reArr);
    }

    /**
     * 设置翻页中数据数量
     * @param int $maxNum
     */
    public function scopeMGetPage($query, $maxNum)
    {
        if (!$maxNum) {
            return;
        }
        $maxNum = (true === $maxNum) ? config('system.sys_max_row') : $maxNum;
        return $query->paginate($maxNum);
    }

    /**
     * 构造查询时用的like数组
     * @param array $whereArr
     * @param string $logic AND or OR
     * @return boolean
     */
    public function scopeMMakeLikeArray($query, $where, $logic = 'OR')
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
    public function _make_rand($query, $length = 4)
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
     * @param int $parentId
     * @param int $level
     * @return array
     */
    public function scopeMMakeTree($query, $config, $parentId = 0, $level = 0)
    {
        $listWhere = $config['list_where'];
        if (!is_array($listWhere)) {
            $listWhere = [];
        }

        $listWhere[$config['parent_id']] = $parentId;
        $parentList                      = $query->$config['list_fn']($listWhere);
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
            $childList    = $query->$config['tree_fn']($listWhere, $parent[$config['id']], $level);
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
    public function scopeMEncodeContent($query, $content)
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
    public function scopeMParseWhere($query, $wheres)
    {
        foreach ($wheres as $where) {
            if (3 == count($where)) {
                if (in_array($where[1], $this->whereClauses)) {
                    $whereClause = 'where' . $where[1];
                    $query->$whereClause($where[0], $where[2]);
                }
            } elseif (2 == count($where)) {
                $query->where($where[0], $where[1]);
            } else {
                throw new \Exception('parse where error!');
            }
        }
    }

    /**
     * 格式化数据接口
     * @param type &$data
     */
    public function scopeMEncodeData($query, $data)
    {
    }

    public function scopeMDecodeData($query, $data)
    {
    }
}
