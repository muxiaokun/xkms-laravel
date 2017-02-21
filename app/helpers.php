<?php
// 公共函数

/**
 * @param        $key
 * @param string $item
 * 修改环境变量文件
 */
function mPutenv($key, $item = '')
{
    $env_path     = base_path('.env');
    $filesystem   = new \Illuminate\Filesystem\Filesystem();
    $env_contents = $filesystem->get($env_path);
    if (is_array($key)) {
        foreach ($key as $k => $i) {
            //不用回调 因为I/O
            $k            = strtoupper($k);
            $env_contents = preg_replace('/' . $k . '=.*/', $k . '=' . $i, $env_contents);
        }
    } else {
        $key          = strtoupper($key);
        $env_contents = preg_replace('/' . $key . '=.*/', $key . '=' . $item, $env_contents);
    }

    return $filesystem->put($env_path, $env_contents) ? true : false;
}

/**
 * @param       $path
 * @param array $arr
 * @param bool  $useOld
 * @return bool|int
 * 数组写入到文件
 */
function mPutArr($path, $arr = [], $useOld = true)
{
    if (!$path || !$arr) {
        return false;
    }
    $filesystem = new \Illuminate\Filesystem\Filesystem();
    if ($useOld && $oldArr = mGetArr($path)) {
        $arr = array_merge($oldArr, $arr);
    }
    $arrStr    = var_export($arr, true);
    $putConfig = <<<EOF
<?php
return {$arrStr};
?>
EOF;
    return $filesystem->put($path, $putConfig);
}

/**
 * @param $path
 * @return bool|mixed
 * 获取文件中的数组
 */
function mGetArr($path)
{
    $filesystem = new \Illuminate\Filesystem\Filesystem();
    if (!$path || !$filesystem->isFile($path)) {
        return false;
    }
    $arr = $filesystem->getRequire($path);
    return is_array($arr) ? $arr : [];
}

/**
 * @param string $type
 * @param int    $length
 * @return string
 * 随即字符串
 */
function mRandStr($type = 'vc', $length = 4)
{
    $rand_range = [
        //VerificationCode
        'vc' => 'ABCDECFGHIJKLMNOPQRSTUVWXYZ',
        //PaaswordRand
        'pr' => '0123456789abcdecfghijklmnopqrstuvwxyzABCDECFGHIJKLMNOPQRSTUVWXYZ',
    ];
    $rand       = '';
    for ($i = 0; $i < $length; $i++) {
        $rand .= $rand_range[$type][rand(0, strlen($rand_range[$type]) - 1)];
    }
    return $rand;
}

/**
 * @param $fileUrl
 * @return mixed
 * @return string
 * 资源绝对路径转相对路径
 */
function mParseUploadUrl($fileUrl)
{
    $baseUrl = request()->getBaseUrl() . '/';
    $urlPreg = '/' . str_replace('/', '\/', $baseUrl) . '/';
    return preg_replace($urlPreg, '', $fileUrl, 1);
}

/**
 * @param $fileUrl
 * @return string
 * 资源相对路径转绝对路径
 */
function mMakeUploadUrl($fileUrl)
{
    $baseUrl = request()->getBaseUrl() . '/';
    return $baseUrl . $fileUrl;
}

/**
 * @param      $content
 * @param bool $useBaseUrl
 * @return string
 * 格式化内容中的资源地址 绝对和相对互转
 */
function mParseContent($content, $useBaseUrl = false)
{
    $content = htmlspecialchars_decode($content);
    $urlPreg = mGetUrlpreg($useBaseUrl);
    $content = preg_replace($urlPreg['pattern'], $urlPreg['replacement'], $content);
    return htmlspecialchars($content);
}

/**
 * @param $content
 * @return mixed
 * 获取内容中的资源地址
 */
function mGetContentUpload($content)
{
    $baseUrl = request()->getBaseUrl() . '/';
    $baseUrl = str_replace('/', '\/', $baseUrl);
    preg_match_all('/(\'|\")' . $baseUrl . '(storage\/.*?)\1/i', $content, $uploadLinks);
    return $uploadLinks[2];
}

//切割字符串
//提取 Org\Util\String::msubstr
function mSubstr($str, $len, $suffix = true, $start = 0)
{
    $charset      = 'utf-8';
    $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $strLen = ($match[0]) ? count($match[0]) : strlen($str);
    if ($len > $strLen) {
        return $str;
    } else {
        $slice = join("", array_slice($match[0], $start, $len));
    }
    return $suffix ? $slice . '...' : $slice;
}

//转换本系统的格式时间到时间戳
//暂时只支持基本的格式YmdHis
function mMktime($date, $isDetail = false)
{
    if ('' === $date) {
        return $date;
    }

    $dateFormat  = ($isDetail) ? config('system.sys_date_detail') : config('system.sys_date');
    $matchFormat = $dateFormat;
    $strSearch   = ['-', '[', ']', '(', ')', '^', '$'];
    $strReplace  = ['\-', '\[', '\]', '\(', '\)', '\^', '\$'];
    $matchFormat = '/' . str_replace($strSearch, $strReplace, $matchFormat) . '/';
    //注意d 一定要放第一个 否则会和替换值重复
    $dateSearch  = ['d', 'Y', 'm', 'H', 'i', 's'];
    $dateReplace = ['(\d{2})', '(\d{4})', '(\d{2})', '(\d{2})', '(\d{2})', '(\d{2})'];
    $pos         = [];
    foreach ($dateSearch as $v) {
        $pos[$v] = strpos($dateFormat, $v);
    }
    $matchFormat = str_replace($dateSearch, $dateReplace, $matchFormat);
    asort($pos);
    $i      = 1;
    $subPos = [];
    foreach ($pos as $k => $v) {
        $subPos[$k] = ($v === false) ? 0 : $i++;
    }
    $pos = $subPos;
    if (preg_match($matchFormat, $date, $sub)) {
        $sub[0] = 0;
        $time   = mktime($sub[$pos['H']], $sub[$pos['i']], $sub[$pos['s']], $sub[$pos['m']], $sub[$pos['d']],
            $sub[$pos['Y']]);
    } else {
        $time = null;
    }
    return $time;
}

//创建where_info中时间范围的数组
function mMktimeRange($inputName)
{
    $timeRange      = [];
    $startInputName = $inputName . '_start';
    $endInputName   = $inputName . '_end';
    $gtTime         = mMktime(request($startInputName));
    $ltTime         = mMktime(request($endInputName)) + 86400;
    if ($gtTime && 0 < $gtTime) {
        $timeRange[$startInputName] = date('Y-m-d H:i:s', $gtTime);
    }

    if ($ltTime && 86400 < $ltTime) {
        $timeRange[$endInputName] = date('Y-m-d H:i:s', $ltTime);
    }

    return $timeRange;
}

function mExists($url = '', $isThumb = false)
{
    if (!$url || !is_file(public_path($url))) {
        $url = config('system.sys_default_image');
    } elseif ($isThumb) {
        $pathinfo = pathinfo(public_path($url));
        $newName  = $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
        $newFile  = $pathinfo['dirname'] . '/' . $newName;
        if (is_file($newFile)) {
            return $newFile;
        }
    }
    return mMakeUploadUrl($url);
}

//将内容中的IMG标签替换成异步IMG标签
function mAsyncImg($content)
{
    $pattern = '/(<img.*?)\ssrc=([\'|\"])(.*?)\2(.*?\/?>)/is';
    $content = preg_replace_callback($pattern, function ($match) {
        $newElement  = base64_encode($match[1] . $match[4]);
        $replacement = '<img src="' . mExists(config('system.sys_sync_image')) . '" ';
        $replacement .= 'M_Img="' . $newElement . '" ';
        $replacement .= 'M_img_src=' . $match[2] . $match[3] . $match[2] . ' />';
        return $replacement;
    }, $content);
    return $content;
}

//创建CKplayer
function mCkplayer($config)
{
    $id          = $config['id'];
    $src         = $config['src'];
    $pathInfo    = pathinfo($src);
    $suffix      = $pathInfo['extension'];
    $allowSuffix = ['flv', 'f4v', 'mp4', 'm3u8', 'webm', 'ogg', 'flv', 'f4v', 'mp4'];
    if (!$id || !$src || !in_array($suffix, $allowSuffix)) {
        return '';
    }

    $img       = ($config['img']) ? $config['img'] : ''; //default image
    $loop      = ($config['loop']) ? $config['loop'] : 2; //1repeat 2stop 2
    $volume    = ($config['volume']) ? $config['volume'] : 100; //0-100 100
    $autostart = ($config['autostart']) ? $config['autostart'] : 2; //0stop 1play 2notload 2
    $configXml = ($config['right_close']) ? 'ckplayer_min.xml' : 'ckplayer.xml';
    return <<<EOF
<script type="text/javascript" src="Public/ckplayer/ckplayer.js"></script>
<script type="text/javascript">
    var flashvars={f:<ntag url='{$src}' />,e:'{$loop}',v:'{$volume}',p:'{$autostart}',i:<ntag url='{$img}' />,c:1,x:'{$configXml}'};
    CKobject.embed(<ntag url='Public/ckplayer/ckplayer.swf' />,'{$id}','','100%','100%',false,flashvars);
</script>
EOF;
}

//将内容中的视频替换成CKplayer
//custom_id 自定义输出div id
//jop=>just one player 只返回一个视频
function mContent2ckplayer($content, $image, $customId = false, $jop = false)
{
    if (!preg_match_all('/<embed.*?\/>/i', $content, $elements)) {
        return $content;
    }

    $allowSuffix = ['flv', 'f4v', 'mp4', 'm3u8', 'webm', 'ogg', 'flv', 'f4v', 'mp4'];
    $attrPattern = [
        '(\ssrc=(\'|")(.*?(' . implode('|', $allowSuffix) . '))\3)', //4
        '(\swidth=(\'|")(.*?)\7)', //8
        '(\sheight=(\'|")(.*?)\10)', //11
        '(\sautostart=(\'|")(.*?)\13)', //14
        '(\sloop=(\'|")(.*?)\16)', //17
    ];
    $pattern     = '/(' . implode('|', $attrPattern) . ')/i'; //1
    foreach ($elements[0] as $id => $element) {
        if (!preg_match_all($pattern, $element, $matches)) {
            continue;
        }

        $src = $matches[4][0];
        if ($src) {
            $width     = $matches[8][1] . 'px';
            $height    = $matches[11][2] . 'px';
            $autostart = ('true' == $matches[14][3]) ? 1 : 2;
            $loop      = ('true' == $matches[17][4]) ? 1 : 2;
            $image     = mExists($image);
            $divId     = ($customId) ? $customId : 'content2ckplayer_' . $id;
            if ($jop) {
                $width  = '100%';
                $height = '100%';
                $id     = mt_rand();
            }
            $config = [
                'id'          => $divId,
                'src'         => $src,
                'img'         => $image,
                'autostart'   => $autostart,
                'loop'        => $loop,
                'right_close' => 'true',
            ];
            $MTag   = mCkplayer($config);
            if ('' != $MTag) {
                $reContent = 'span id="' . $divId . '" style="display:block;margin:0 auto;';
                $reContent .= 'width:' . $width . ';height:' . $height . ';" >' . $MTag . '</span';
                //替换时左右 <> 会被当成正则符号 所以默认返回内容缺少 <>
                if ($jop) {
                    return '<' . $reContent . '>';
                }

                $content = preg_replace($element, $reContent, $content, 1);
            }
        }
    }
    return $content;
}

// 自定义将字符串转换成系统连接
// 1 M/C/A?ars1=val1&ars2=val2
// 2 \w*://
function mStr2url($url)
{
    if ($url && preg_match('/^(\w*?:\/\/|javascript|#).*/', $url)) {
        return $url;
    } elseif ($url && preg_match('/^[\w\/]*?\??[\w=,]*?$/', $url)) {
        $varUrl         = explode('?', $url);
        $varUrlArray    = [];
        $varUrlArrayStr = explode(',', $varUrl[1]);
        foreach ($varUrlArrayStr as $varUrlValue) {
            list($key, $value) = explode('=', trim($varUrlValue));
            $varUrlArray[$key] = $value;
        }
        return mU(trim($varUrl[0]), $varUrlArray);
    } else {
        return ($url) ? '#' . $url : mU();
    }
}

function mInArray($target, $source)
{
    if (is_array($target)) {
        foreach ($target as $t) {
            if (!mInArray($t, $source)) {
                return false;
            }

        }
        return true;
    } else {
        return in_array($target, $source);
    }
}

function mAttributeArr($attribute, $cateId = 0)
{
    $attrStrs = request('attr');
    //清空非合法属性格式
    if ($attrStrs && !preg_match('/^((\d+)(_(\d+))+-?)+$/', $attrStrs)) {
        $attrStrs = '';
    }
    $request = request();
    $cateId && $request['cate_id'] = $cateId;
    if (config('system.token_on')) {
        unset($request[config('system.token_name', null, '__hash__')]);
    }

    $cacheName  = 'M_attribute_arr' . serialize($attribute) . $attrStrs . serialize($request);
    $cacheValue = S($cacheName);
    if ($cacheValue && true !== APP_DEBUG) {
        return $cacheValue;
    }

    $validateData = [];
    $key          = 0;
    foreach ($attribute as $values) {
        $validateData[$key] = count($values);
        ++$key;
    }

    //解析attr字符串 开始
    $attrValue = [];
    foreach (explode('-', $attrStrs) as $attrStr) {
        $attrArr = explode('_', $attrStr);
        $k       = array_shift($attrArr);
        if ('' == $k) {
            continue;
        }

        foreach ($attrArr as $v) {
            if (!preg_match('/_' . $valueKey . '(?!\d)/', $data[$key]) && $v < $validateData[$k]) {
                $attrValue[$k] .= '_' . $v;
            }
        }
        isset($attrValue[$k]) && $attrValue[$k] = $k . $attrValue[$k];
    }
    //解析attr字符串 结束

    $attributeList = [];
    $key           = 0;
    foreach ($attribute as $name => $values) {
        unset($request['attr']);
        $data    = $attrValue;
        $checked = !isset($data[$key]);
        if (!$checked) {
            unset($data[$key]);
        }

        $dataStr = implode('-', $data);
        $dataStr && $request['attr'] = $dataStr;
        $attributeList[$key][] = [
            'name'    => $name,
            'checked' => $checked,
            'link'    => $checked ? 'javascript:void(0);' : mU('article_category', $request),
        ];
        foreach ($values as $valueKey => $value) {
            unset($request['attr']);
            $data    = $attrValue;
            $checked = false;
            if (!preg_match('/_' . $valueKey . '(?!\d)/', $data[$key])) {
                //添加参数
                if (!isset($data[$key])) {
                    $data[$key] = $key;
                }

                $data[$key] .= '_' . $valueKey;
            } else {
                //削减参数
                $checked = true;
                $oldData = explode('_', preg_replace('/^' . $key . '_/', '', $data[$key]));
                $newData = [];
                //BUG创建新数据时错误
                array_walk($oldData, function ($v, $k) use ($valueKey, &$newData) {
                    ($v != $valueKey) && $newData[] = $v;
                });
                if (0 < count($newData)) {
                    $data[$key] = $key . '_' . implode('_', $newData);
                } else {
                    unset($data[$key]);
                }
            }
            $dataStr = implode('-', $data);
            $dataStr && $request['attr'] = $dataStr;
            $attributeList[$key][] = [
                'name'    => $value,
                'checked' => $checked,
                'link'    => mU('article_category', $request),
            ];
        }
        ++$key;
    }

    $cacheValue = $attributeList;
    S($cacheName, $cacheValue, config('system.sys_td_cache'));

    return $attributeList;
}

function mAttributeWhere($attribute, $attr)
{
    $attrStrs = request('attr', $attr);
    //清空非合法属性格式
    if ($attrStrs && !preg_match('/^((\d+)(_(\d+))+-?)+$/', $attrStrs)) {
        $attrStrs = '';
    }

    $cacheName  = 'M_attribute_arr' . serialize($attribute) . $attrStrs;
    $cacheValue = S($cacheName);
    if ($cacheValue && true !== APP_DEBUG) {
        return $cacheValue;
    }

    $where     = [];
    $attrValue = [];
    foreach (explode('-', $attrStrs) as $attrStr) {
        $attrArr = explode('_', $attrStr);
        $k       = array_shift($attrArr);
        if ('' == $k) {
            continue;
        }

        $attrValue[$k];
        foreach ($attrArr as $v) {
            $attrValue[$k][$v] = $v;
        }
    }
    $key = 0;
    foreach ($attribute as $name => $values) {
        $twhere = [];
        foreach ($values as $valueKey => $value) {
            if (isset($attrValue[$key][$valueKey])) {
                $twhere[] = $name . ":" . $value;
            }
        }
        $where[] = $twhere;
        ++$key;
    }

    $cacheValue = $where;
    S($cacheName, $cacheValue, config('system.sys_td_cache'));

    return $where;
}

function mDate($date, $format = '', $toZh = false)
{
    if (!$date) {
        return '';
    }

    $carbon = \Carbon\Carbon::parse($date);
    !$format && $format = config('system.sys_date_detail');
    $date_str = $carbon->format($format);
    if ($toZh) {
        $date_str = str_replace([
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ], [
            trans('common.week') . trans('common.one'),
            trans('common.week') . trans('common.two'),
            trans('common.week') . trans('common.three'),
            trans('common.week') . trans('common.four'),
            trans('common.week') . trans('common.five'),
            trans('common.week') . trans('common.six'),
            trans('common.week') . trans('common.day'),
            trans('common.one') . trans('common.month'),
            trans('common.two') . trans('common.month'),
            trans('common.three') . trans('common.month'),
            trans('common.four') . trans('common.month'),
            trans('common.five') . trans('common.month'),
            trans('common.six') . trans('common.month'),
            trans('common.seven') . trans('common.month'),
            trans('common.eight') . trans('common.month'),
            trans('common.nine') . trans('common.month'),
            trans('common.ten') . trans('common.month'),
            trans('common.ten') . trans('common.one') . trans('common.month'),
            trans('common.ten') . trans('common.two') . trans('common.month'),
        ], $date_str);
    }
    return $date_str;
}

function mIptoadd($ip, $type = 0)
{
    if ('' == $ip) {
        return '';
    }

    $IpLocation = new \App\Library\IpLocation();
    $ipInfo     = $IpLocation->getlocation($ip);
    $reStr      = '';
    switch ($type) {
        case 1:
            $ipInfo['country'];
            break;
        case 2:
            $ipInfo['area'];
            break;
        default:
            $reStr .= $ipInfo['country'] . ' ' . $ipInfo['area'];
    }
    $reStr = mb_convert_encoding($reStr, 'utf-8', ['gbk', 'utf-8']);
    return $reStr;
}

/*
 * 生成二维码方法
 * $level smallest -> L M Q H ->best
 * $size 1 - 10
 * $margin 0 - N
 * 并生成缓存 无失效期
 */
function mQrcode($data, $level = 'H', $size = 10, $margin = 0)
{
    if (!$data) {
        return mExists();
    }

    $dataMd5 = 'QRcode_' . md5($data . $level . $size . $margin);
    $pngData = S($dataMd5);
    if (!$pngData) {
        $QRcode  = new \Common\Lib\QRcode();
        $pngPath = TEMP_PATH . md5($dataMd5) . '.png';
        $QRcode->png($data, $pngPath, $level, $size, $margin);
        $pngData = file_get_contents($pngPath);
        @unlink($pngPath);
        S($dataMd5, $pngData);
    }
    return U('Home/Index/cache', ['type' => 'qrcode', 'id' => $dataMd5]);
}

// 构造Page html 必须放在公共函数中 配合ViewFilterBehavior
function mPage($config)
{
    $maxRow   = ($config['max_row']) ? $config['max_row'] : config('system.sys_max_row');
    $countRow = ($config['count_row']) ? $config['count_row'] : 0;
    $roll     = ($config['roll']) ? $config['roll'] : 5;
    if ($maxRow >= $countRow) {
        return '';
    }
    $parameter = request('', '', 'urlencode');
    if (config('system.token_on')) {
        unset($parameter[config('system.token_name')]);
    }

    $Page           = new \Think\Page($countRow, $maxRow, $parameter);
    $Page->rollPage = $roll;
    $Page->setConfig('header',
        '<span class="rows">' . trans('common.inall') . ' %TOTAL_ROW% ' . trans('common.inall') . trans('common.item') . '</span>');
    $Page->setConfig('prev', trans('common.previous') . trans('common.page'));
    $Page->setConfig('next', trans('common.next') . trans('common.page'));
    $Page->setConfig('first', trans('common.first') . trans('common.page') . '...');
    $Page->setConfig('last', '...' . trans('common.last') . trans('common.one') . trans('common.page'));
    $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');

    unset($parameter['p']);
    $inputJumpLink = U(ACTION_NAME, $parameter);
    !isset($config['preg_njump']) && $inputJump = <<<EOF
    <div class="fr">
        <form class="form-inline" action="{$inputJumpLink}">
            <input class="form-control w80" type="text" name="p" onKeyup="mInIntRange(this,1,{$countRow});" />
            <button class="btn btn-default" type="submit" >GO</button>
        </form>
    </div>
EOF;
    //默认的翻页样式
    $replacement = [
        'preg_div'     => '<ul class="pagination">' . $inputJump . '\1</ul>',
        'preg_a'       => '<li>\1</li>',
        'preg_current' => '<li class="active"><a>\1</a></li>',
        'preg_rows'    => '<li><a>\1</a></li>',
    ];
    isset($config['preg_div']) && $replacement['preg_div'] = $config['preg_div'];
    isset($config['preg_a']) && $replacement['preg_a'] = $config['preg_a'];
    isset($config['preg_current']) && $replacement['preg_current'] = $config['preg_current'];
    isset($config['preg_rows']) && $replacement['preg_rows'] = $config['preg_rows'];
    $pageStr = $Page->show();
    $pageStr = preg_replace('/<div>(.*)<\/div>/', $replacement['preg_div'], $pageStr);
    $pageStr = preg_replace('/(<a[^<]+<\/a>)/', $replacement['preg_a'], $pageStr);
    $pageStr = preg_replace('/<span class\=\"current.+>([^<]+)<\/span>/', $replacement['preg_current'], $pageStr);
    $pageStr = preg_replace('/<span class\=\"rows.+>([^<]+)<\/span>/', $replacement['preg_rows'], $pageStr);

    return $pageStr;
}

//扫描模板
function mScanTemplate($name, $controller)
{
    $dir = resource_path('views/home/');
    config('system.default_theme') && $dir .= config('system.default_theme') . '/';
    $themeInfo    = mGetArr($dir . 'theme_info.php');
    $preg         = '/^' . $controller . '_' . $name . '_(\w*)\./';
    $templateList = [];
    $dirs         = scandir($dir);
    foreach ($dirs as $file) {
        if (preg_match($preg, $file, $match)) {
            $template = ['value' => $match[1]];
            if ($themeInfo[md5($file)]) {
                $template['name'] = $themeInfo[md5($file)]['name'];
            } else {
                $template['name'] = $match[1];
            }
            $templateList[] = $template;
        }
    }
    return $templateList;
}

//生成处理url的preg
function mGetUrlpreg($useBaseUrl = false)
{
    $baseUrl  = request()->getBaseUrl() . '/';
    $pregRoot = '(';
    $pregRoot .= ($baseUrl) ? str_replace('/', '\/', $baseUrl) : '\/';
    $pregRoot .= '|#|\w*:)?';
    $urlPreg['pattern']     = [
        '/(<a.*?\shref=)([\'|\"])' . $pregRoot . '(.*?)\2(.*?>)/is',
        '/(<script.*?\ssrc=)([\'|\"])' . $pregRoot . '(.*?)\2(.*?>)/is',
        '/(<link.*?\shref=)([\'|\"])' . $pregRoot . '(.*?)\2(.*?\/?>)/is',
        '/(<img.*?\ssrc=)([\'|\"])' . $pregRoot . '(.*?)\2(.*?\/?>)/is',
        '/(url\()([\'|\"]?)' . $pregRoot . '(.*?)\2(.*?\))/is',
        '/(<embed.*?\ssrc=)([\'|\"])' . $pregRoot . '(.*?)\2(.*?\/?>)/is',
        //分割注视 上：html 下:自定义的
        //异步加载图片功能
        '/(<img.*?\sM_img_src=)([\'|\"])' . $pregRoot . '(.*?)\2(.*?\/?>)/is',
        //处理获得src
        '/(.?)<ntag.*?\surl=([\'|\"])' . $pregRoot . '(.*?)\2.*?\/?>(.?)/is',
    ];
    $urlPreg['replacement'] = $useBaseUrl ? '\1\2' . $baseUrl . '\4\2\5' : '\1\2\4\2\5';
    return $urlPreg;
}

//使用PHPMailer发送邮件
function mSendmail($to, $title = '', $content = '', $chart = 'utf-8', $attachment = '')
{
    $from               = 'test20121212@qq.com';
    $PHPMailer          = new \Common\Lib\PHPMailer();
    $PHPMailer->CharSet = $chart; //设置采用gb2312中文编码
    $PHPMailer->IsSMTP(); //设置采用SMTP方式发送邮件
    $PHPMailer->Host = "smtp.qq.com"; //设置邮件服务器的地址
    //$PHPMailer->Port = 465; //设置邮件服务器的端口，默认为25
    $PHPMailer->From     = $from; //设置发件人的邮箱地址
    $PHPMailer->FromName = trans('common.system'); //设置发件人的姓名
    $PHPMailer->SMTPAuth = true; //设置SMTP是否需要密码验证，true表示需要
    $PHPMailer->Username = $from; //设置发送邮件的邮箱
    $PHPMailer->Password = "test20121212"; //设置邮箱的密码
    $PHPMailer->Subject  = $title; //设置邮件的标题
    $PHPMailer->AltBody  = "text/html"; // optional, comment out and test
    $PHPMailer->Body     = $content; //设置邮件内容
    $PHPMailer->IsHTML(true); //设置内容是否为html类型
    //$PHPMailer->Timeout = 30; //设置每行的字符数
    //$PHPMailer->WordWrap = 50; //设置每行的字符数
    //$PHPMailer->AddReplyTo("地址","名字"); //设置回复的收件人的地址
    $PHPMailer->AddAddress($to, $title); //设置收件的地址
    if ($attachment != '') {
        $PHPMailer->AddAttachment($attachment, $attachment);
    }
    $PHPMailer->Send();
}

/**
 * @param int $size Byte
 * @return string
 *                  格式化文件大小
 */
function mFormatSize($size = 0)
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