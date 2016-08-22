<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
// 公共

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    /**
     * 列出数据
     * @access public
     * @param array $where 查询条件
     * @param int $page 翻页数量
     * @return array 返回数据数组
     */
    public function mSelect($where = null, $page = false)
    {
        $this->mGetPage($page);
        $data = $this->where($where)->select();
        foreach ($data as &$dataRow) {$this->mDecodeData($dataRow);}
        return $data;
    }

    /**
     * 添加数据
     * @param array $data
     * @return boolean
     */
    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $this->mEncodeData($data);
        return $this->add($data);
    }

    /**
     * 删除数据
     * @param mixed $id
     * @return boolean
     */
    public function mDel($id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = array('in', $id);
        return $this->where(array('id' => $id))->delete();
    }

    /**
     * 编辑数据
     * @param mixed $id
     * @param array $data
     * @return boolean
     */
    public function mEdit($id, $data)
    {
        if (!$id || !$data) {
            return false;
        }

        is_array($id) && $id = array('in', $id);
        $this->mEncodeData($data);
        return $this->where(array('id' => $id))->data($data)->save();
    }

    /**
     * 查找数据
     * @param mixed $id
     * @return array
     */
    public function mFind($id)
    {
        if (!$id) {
            return false;
        }

        $data = $this->where(array('id' => $id))->find();
        $this->mDecodeData($data);
        return $data;
    }

    /**
     * 查找数据id
     * @param string $value
     * @param string $columnName
     * @return string
     */
    public function mFindId($value, $columnName = '')
    {
        if (!$value) {
            return false;
        }

        //默认查询id的列为第二列
        if (!$columnName) {
            $columnName = $this->fields[1];
        }
        $column = $this->field('id')->where(array($columnName => $value))->find();
        return $column['id'];
    }

    /**
     * 查找数据
     * @param int $id
     * @param string $columnName
     * @return string
     */
    public function mFindColumn($id, $columnName)
    {
        if (!$id || !$columnName) {
            return false;
        }

        $column = $this->field($columnName)->where(array('id' => $id))->find();
        $this->mDecodeData($column);
        return $column[$columnName];
    }

    /**
     * 清除数据
     * @param mixed $id
     * @param string $columnName
     * @param string $data
     * @return boolean
     */
    public function mClean($id, $columnName = '', $data = false)
    {
        if (!$id) {
            return false;
        }

        //默认清除id的列为第二列
        if (!$columnName) {
            $columnName = $this->fields[1];
        }

        is_array($id) && $id = array('in', $id);
        $this->where(array($columnName => $id));
        if (0 == $this->count()) {
            return true;
        }
        $this->where(array($columnName => $id));
        return (false === $data) ? $this->delete() : $this->save(array($columnName => $data));
    }

    /**
     * 获取最大页数
     * @access public
     * @param array $where 查询条件
     * @return int 最大页数
     */
    public function mGetPageCount($where)
    {
        $this->mParseWhere($where);
        $pageCount   = $this->where($where)->count();
        $sysMaxPage = C('SYS_MAX_PAGE') * C('SYS_MAX_ROW');
        return ($pageCount < $sysMaxPage) ? $pageCount : $sysMaxPage;
    }

    /**
     * 获取指定列数组合集
     * @access public
     * @param string $column 列名称
     * @return array 指定列数组合集
     */
    public function mColumn2Array($column)
    {
        $where = $this->options['where'];
        $this->limit($this->count());
        $selectResult = $this->field($column)->where($where)->select();
        $reArr        = array();
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

        $maxNum = (true === $maxNum) ? C('SYS_MAX_ROW') : $maxNum;
        //p 是 Think\Page中的p的配置
        $p       = I('p');
        $p       = ($p < C('SYS_MAX_PAGE')) ? $p : C('SYS_MAX_PAGE');
        $maxNum = ($p) ? $p . "," . $maxNum : "1," . $maxNum;
        $this->page($maxNum);
    }

    /**
     * 构造查询时用的like数组
     * @param array $whereArr
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
        $result = array(
            'like', $where,
        );
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
        $randArray = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
            'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        );
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $randArray[rand(0, count($randArray) - 1)];
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
    protected function mMakeTree($config, $parentId = 0, $level = 0)
    {
        $listWhere = $config['list_where'];
        if (!is_array($listWhere)) {
            $listWhere = array();
        }

        $listWhere[$config['parent_id']] = $parentId;
        $countRow                        = $this->where($listWhere)->count();
        $parentList                      = $this->limit($countRow)->$config['list_fn']($listWhere);
        //占位符
        $retractStr = '&nbsp;';
        for ($i = 0; $i < $level; $i++) {$retractStr .= $retractStr;}
        $parentTree = array();
        $level++;
        foreach ($parentList as $parentKey => $parent) {
            if (0 != $parentId) {
                $tag                            = ($parentList[$parentKey + 1][$config['retract_col']]) ? "├" : "└";
                $parent[$config['retract_col']] = $retractStr . $tag . $parent[$config['retract_col']];
            }
            $parentTree[] = $parent;
            //这里的limit解除系统限制的数量
            $countRow  = $this->where($listWhere)->count();
            $childList = $this->limit($countRow)->$config['tree_fn']($listWhere, $parent[$config['id']], $level);
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
        if (URL_REWRITE != C('URL_MODEL')) {
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
    {}

    /**
     * 格式化数据接口
     * @param type &$data
     */
    protected function mEncodeData(&$data)
    {}
    protected function mDecodeData(&$data)
    {}
}
