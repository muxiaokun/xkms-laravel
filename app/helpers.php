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
 * @param bool $useOld
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
 * @param int $length
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
    $baseUrl = request()->getBaseUrl() . '/storage/';
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
    $baseUrl = request()->getBaseUrl() . '/storage/';
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
    preg_match_all('/(\'|\")' . $baseUrl . 'storage\/(.*?)\1/i', $content, $uploadLinks);
    return $uploadLinks[2];
}

/**
 * @param $str
 * @param $len
 * @param bool $suffix
 * @param int $start
 * @return string
 * 切割字符串
 */
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

/**
 * @param $date
 * @param bool $isDetail
 * @return false|int|null
 * 转换本系统的格式时间到时间戳
 * 暂时只支持基本的格式YmdHis
 */
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

/**
 * @param $inputName
 * @return array
 * 创建where_info中时间范围的数组
 */
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

/**
 * @param string $url
 * @param bool $isThumb
 * @return string
 * 从三个位置查找资源文件
 */
function mExists($url = '', $isThumb = false)
{
    if (!$url) {
        //return sys_default_image
        return asset(config('system.sys_default_image'));
    } elseif (is_file(public_path($url))) {
        //return public file
        return asset($url);
    } elseif ($isThumb && is_file(storage_path('app/public/' . $url))) {
        //return thumb file
        $pathinfo = pathinfo(mMakeUploadUrl($url));
        $newName  = $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
        $newFile  = $pathinfo['dirname'] . '/' . $newName;
        return $newFile;
    } elseif (is_file(storage_path('app/public/' . $url))) {
        //return storage file
        return mMakeUploadUrl($url);
    }

    //return sys_default_image
    return asset(config('system.sys_default_image'));
}

/**
 * @param $content
 * @return mixed
 * 将内容中的IMG标签替换成异步IMG标签
 */
function mAsyncImg($content)
{
    $pattern = '/(<img.*?)\ssrc=([\'|\"])(.*?)\2(.*?\/?>)/is';
    $content = preg_replace_callback($pattern, function ($matches) {
        $newElement  = base64_encode($matches[1] . $matches[4]);
        $replacement = $matches[1] . ' src="' . mExists(config('system.sys_sync_image')) . '" ';
        $replacement .= 'M_Img="' . $newElement . '" ';
        $replacement .= 'M_img_src=' . $matches[2] . $matches[3] . $matches[2];
        $replacement .= ' style="width:85px;height:85px;padding:0px;"' . $matches[4];
        return $replacement;
    }, $content);
    return $content;
}

/**
 * @param $config
 * @return string
 * 创建CKplayer
 */
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

    $img       = (isset($config['img'])) ? $config['img'] : ''; //default image
    $loop      = (isset($config['loop'])) ? $config['loop'] : 2; //1repeat 2stop 2
    $volume    = (isset($config['volume'])) ? $config['volume'] : 100; //0-100 100
    $autostart = (isset($config['autostart'])) ? $config['autostart'] : 2; //0stop 1play 2notload 2
    $configXml = (isset($config['right_close'])) ? 'ckplayer_min.xml' : 'ckplayer.xml';
    $swfPath   = asset('ckplayer/ckplayer.swf');
    return <<<EOF
<script type="text/javascript" src="ckplayer/ckplayer.js"></script>
<script type="text/javascript">
    var flashvars={f:'{$src}',e:'{$loop}',v:'{$volume}',p:'{$autostart}',i:'{$img}',c:1,x:'{$configXml}'};
    CKobject.embed('{$swfPath}','{$id}','','100%','100%',false,flashvars);
</script>
EOF;
}

/**
 * @param $content
 * @param string $image
 * @param bool $customId 自定义输出div id
 * @param bool $jop 只返回一个视频 just one player
 * @return mixed|string
 * 将内容中的视频替换成CKplayer
 */
function mContent2ckplayer($content, $image = '', $customId = false, $jop = false)
{
    $content = htmlspecialchars_decode($content);
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

/**
 * @param $url
 * @return string
 * 自定义将字符串转换成系统连接
 * 1 RouteName?ars1=val1&ars2=val2
 * 2 \w*://
 */
function mStr2url($url)
{
    if ($url && preg_match('/^(\w*?:\/\/|javascript|#).*/', $url)) {
        return $url;
    } elseif ($url && preg_match('/^[\w:]*?\??[\w=,]*?$/', $url)) {
        $varUrl      = explode('?', $url);
        $varUrlArray = [];
        if (isset($varUrl[1])) {
            $varUrlArrayStr = explode(',', $varUrl[1]);
            foreach ($varUrlArrayStr as $varUrlValue) {
                list($key, $value) = explode('=', trim($varUrlValue));
                $varUrlArray[$key] = $value;
            }
        }
        return route(trim($varUrl[0]), $varUrlArray);
    } else {
        $baseUrl = request()->getBaseUrl() . '/';
        return ($url) ? '#' . $url : $baseUrl;
    }
}

/**
 * @param $target
 * @param $source
 * @return bool
 * 查找数组中的值是否在数组中
 */
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

/**
 * @param     $attribute
 * @param int $cateId
 * @return array
 * 构造属性url 默认使用request attr
 */
function mAttributeArr($attribute, $cateId = 0)
{
    $attrStrs = request('attr');
    //清空非合法属性格式
    if ($attrStrs && !preg_match('/^((\d+)(_(\d+))+-?)+$/', $attrStrs)) {
        $attrStrs = '';
    }
    $request = request()->all();
    $cateId && $request['cate_id'] = $cateId;

    $cacheName  = 'M_attribute_arr_' . md5(serialize($attribute) . $attrStrs . serialize($request));
    $cacheValue = Illuminate\Support\Facades\Cache::get($cacheName);
    if ($cacheValue && true !== config('app.debug')) {
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
        sort($attrArr);
        if ('' == $k) {
            continue;
        }
        $attrValue[$k] = '';
        foreach ($attrArr as $valueKey => $v) {
            if (preg_match('/_' . $v . '(?!\d)/', $attrStr) && $v < $validateData[$k]) {
                $attrValue[$k] .= '_' . $v;
            }
        }
        $attrValue[$k] && $attrValue[$k] = $k . $attrValue[$k];
    }
    ksort($attrValue);
    //解析attr字符串 结束

    //构造连接 开始
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
            'link'    => $checked ? 'javascript:void(0);' : route('Home::Article::category', $request),
        ];
        foreach ($values as $valueKey => $value) {
            unset($request['attr']);
            $data    = $attrValue;
            $oldData = [];
            if (isset($data[$key])) {
                $oldData = explode('_', preg_replace('/^' . $key . '_/', '', $data[$key]));
            }

            if (isset($data[$key]) && preg_match('/_' . $valueKey . '(?!\d)/', $data[$key])) {
                //削减参数
                $checked = true;
                $newData = [];
                //BUG创建新数据时错误
                array_walk($oldData, function ($v, $k) use ($valueKey, &$newData) {
                    ($v != $valueKey) && $newData[] = $v;
                });
                if (0 < count($newData)) {
                    sort($newData);
                    $data[$key] = $key . '_' . implode('_', $newData);
                } else {
                    unset($data[$key]);
                }
            } else {
                //添加参数
                $checked = false;
                if (!isset($data[$key])) {
                    $data[$key] = $key;
                }
                $newData   = $oldData;
                $newData[] = $valueKey;
                sort($newData);
                $data[$key] = $key . '_' . implode('_', $newData);
            }
            $dataStr = implode('-', $data);
            $dataStr && $request['attr'] = $dataStr;
            $attributeList[$key][] = [
                'name'    => $value,
                'checked' => $checked,
                'link'    => route('Home::Article::category', $request),
            ];
        }
        ++$key;
    }
    //构造连接 结束

    $cacheValue = $attributeList;
    $expiresAt  = \Carbon\Carbon::now()->addSecond(config('system.sys_td_cache'));
    \Illuminate\Support\Facades\Cache::put($cacheName, $cacheValue, $expiresAt);
    return $attributeList;
}

/**
 * @param        $attribute
 * @param string $attr
 * @return array
 * 构造查询属性条件
 */
function mAttributeWhere($attribute, $attr = '')
{
    $attrStrs = request('attr', $attr);
    //清空非合法属性格式
    if ($attrStrs && !preg_match('/^((\d+)(_(\d+))+-?)+$/', $attrStrs)) {
        $attrStrs = '';
    }
    $cacheName  = 'M_attribute_arr_' . md5(serialize($attribute)) . $attrStrs;
    $cacheValue = Illuminate\Support\Facades\Cache::get($cacheName);
    if ($cacheValue && true !== config('app.debug')) {
        return $cacheValue;
    }

    $where     = [];
    $attrValue = [];

    foreach (explode('-', $attrStrs) as $attrStr) {
        $attrArr = explode('_', $attrStr);
        $k       = array_shift($attrArr);
        sort($attrArr);
        if ('' == $k) {
            continue;
        }
        $attrValue[$k] = [];
        foreach ($attrArr as $v) {
            $attrValue[$k][$v] = $v;
        }
    }
    $key = 0;
    foreach ($attribute as $name => $values) {
        foreach ($values as $valueKey => $value) {
            if (isset($attrValue[$key][$valueKey])) {
                $where[] = $name . ":" . $value;
            }
        }
        ++$key;
    }

    $cacheValue = $where;
    $expiresAt  = \Carbon\Carbon::now()->addSecond(config('system.sys_td_cache'));
    \Illuminate\Support\Facades\Cache::put($cacheName, $cacheValue, $expiresAt);
    return $where;
}

/**
 * @param $date
 * @param string $format
 * @param bool $toZh
 * @return mixed|string
 * 将时间字符串中的单词替换成中文
 */
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

/**
 * @param $ip
 * @param int $type
 * @return mixed|string
 * 获取ip地理位置
 */
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

/**
 * @param $data
 * @param string $level smallest -> L M Q H ->best
 * @param int $size 1 - 10
 * @param int $margin 0 - N
 * @return string
 * 生成Qrcode静态文件
 * */
function mQrcode($data, $level = 'L', $size = 10, $margin = 0)
{
    if (!$data) {
        return mExists();
    }
    $dataMd5    = 'QRcode_' . md5($data . $level . $size . $margin);
    $filesystem = new \Illuminate\Filesystem\Filesystem();
    $qrFileDir  = storage_path('app/public/qrcode/');
    $qrFilePath = $qrFileDir . $dataMd5;
    if (!$filesystem->isDirectory($qrFileDir)) {
        $filesystem->makeDirectory($qrFileDir);
    }
    if (!$filesystem->isFile($qrFilePath)) {
        $QRcode = new \App\Library\QRcode();
        $QRcode->png($data, $qrFilePath, $level, $size, $margin);
    }
    return asset('storage/qrcode/' . $dataMd5);
}

/**
 * @param $name
 * @param $controller
 * @return array
 * 扫描模板
 */
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
            if (isset($themeInfo[md5($file)])) {
                $template['name'] = $themeInfo[md5($file)]['name'];
            } else {
                $template['name'] = $match[1];
            }
            $templateList[] = $template;
        }
    }
    return $templateList;
}

/**
 * @param bool $useBaseUrl
 * @return mixed
 * 生成处理url的preg
 */
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

/**
 * @param $to
 * @param string $title
 * @param string $content
 * @param string $chart
 * @param string $attachment
 * 使用PHPMailer发送邮件
 */
function mSendmail($to, $title = '', $content = '', $chart = 'utf-8', $attachment = '')
{
    $from               = 'test20121212@qq.com';
    $PHPMailer = new App\Library\PHPMailer();
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
 * 格式化文件大小
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