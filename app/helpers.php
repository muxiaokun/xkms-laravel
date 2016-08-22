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
// 公共函数

//构造本系统的URL连接 $type如果为空返回根目录
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

    $dateFormat  = ($isDetail) ? C('SYS_DATE_DETAIL') : C('SYS_DATE');
    $matchFormat = $dateFormat;
    $strSearch   = array('-', '[', ']', '(', ')', '^', '$');
    $strReplace  = array('\-', '\[', '\]', '\(', '\)', '\^', '\$');
    $matchFormat = '/' . str_replace($strSearch, $strReplace, $matchFormat) . '/';
    //注意d 一定要放第一个 否则会和替换值重复
    $dateSearch  = array('d', 'Y', 'm', 'H', 'i', 's');
    $dateReplace = array('(\d{2})', '(\d{4})', '(\d{2})', '(\d{2})', '(\d{2})', '(\d{2})');
    $pos          = array();
    foreach ($dateSearch as $v) {
        $pos[$v] = strpos($dateFormat, $v);
    }
    $matchFormat = str_replace($dateSearch, $dateReplace, $matchFormat);
    asort($pos);
    $i       = 1;
    $subPos = array();
    foreach ($pos as $k => $v) {
        if ($v !== false) {
            $subPos[$k] = $i++;
        }

    }
    $pos = $subPos;
    if (preg_match($matchFormat, $date, $sub)) {
        $time = mktime($sub[$pos['H']], $sub[$pos['i']], $sub[$pos['s']], $sub[$pos['m']], $sub[$pos['d']], $sub[$pos['Y']]);
    } else {
        $time = null;
    }
    return $time;
}

//创建where_info中时间范围的数组
function mMktimeRange($inputName)
{
    $timeRange = array();
    $gtTime    = mMktime(I($inputName . '_start'));
    $ltTime    = mMktime(I($inputName . '_end')) + 86400;
    if (I($inputName . '_start') && 0 < $gtTime) {
        $timeRange[] = array('gt', $gtTime);
    }

    if (I($inputName . '_end') && 0 < $ltTime) {
        $timeRange[] = array('lt', $ltTime);
    }

    return $timeRange;
}

function mExists($url, $isThumb = false)
{
    if (!$url || !is_file($url)) {
        $systemDefault = "Public/css/bimages/default.png";
        switch (MODULE_NAME) {
            case 'Home':
                $url = C('SYS_DEFAULT_IMAGE', null, $systemDefault);
                break;
            default:
                $url = $systemDefault;
        }
    } elseif ($isThumb) {
        $pathinfo = pathinfo($url);
        $newName = $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
        $newFile = $pathinfo['dirname'] . '/' . $newName;
        if (is_file($newFile)) {
            return $newFile;
        }

    }
    return $url;
}

// 获得字符串中的本站资源链接
function mGetContentUpload($str)
{
    preg_match_all('/(&quot;|\')\s*(?!(http:\/\/|https:\/\/|ftp:\/\/))[^\1]*?(Uploads[^\1]*?)\1/i', $str, $uploadLinks);
    return $uploadLinks[3];
}

//将内容中的IMG标签替换成异步IMG标签
function mSyncImg($content)
{
    $pattern = '/(<img.*?)\ssrc=([\'|\"])(.*?)\2(.*?\/?>)/i';
    $content = preg_replace_callback($pattern, function ($match) {
        $newElement = base64_encode($match[1] . $match[4]);
        $replacement = '<img src="' . mExists(C('SYS_SYNC_IMAGE')) . '" ';
        $replacement .= 'M_Img="' . $newElement . '" ';
        $replacement .= 'M_img_src=' . $match[2] . $match[3] . $match[2] . ' />';
        return $replacement;
    }, $content);
    return $content;
}

//创建CKplayer
function mCkplayer($config)
{
    $id           = $config['id'];
    $src          = $config['src'];
    $pathInfo    = pathinfo($src);
    $suffix       = $pathInfo['extension'];
    $allowSuffix = array('flv', 'f4v', 'mp4', 'm3u8', 'webm', 'ogg', 'flv', 'f4v', 'mp4');
    if (!$id || !$src || !in_array($suffix, $allowSuffix)) {
        return '';
    }

    $img        = ($config['img']) ? $config['img'] : ''; //default image
    $loop       = ($config['loop']) ? $config['loop'] : 2; //1repeat 2stop 2
    $volume     = ($config['volume']) ? $config['volume'] : 100; //0-100 100
    $autostart  = ($config['autostart']) ? $config['autostart'] : 2; //0stop 1play 2notload 2
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

    $allowSuffix = array('flv', 'f4v', 'mp4', 'm3u8', 'webm', 'ogg', 'flv', 'f4v', 'mp4');
    $attrPattern = array(
        '(\ssrc=(\'|")(.*?(' . implode('|', $allowSuffix) . '))\3)', //4
        '(\swidth=(\'|")(.*?)\7)', //8
        '(\sheight=(\'|")(.*?)\10)', //11
        '(\sautostart=(\'|")(.*?)\13)', //14
        '(\sloop=(\'|")(.*?)\16)', //17
    );
    $pattern = '/(' . implode('|', $attrPattern) . ')/i'; //1
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
            $divId    = ($customId) ? $customId : 'content2ckplayer_' . $id;
            if ($jop) {
                $width  = '100%';
                $height = '100%';
                $id     = mt_rand();
            }
            $config = array(
                'id'          => $divId,
                'src'         => $src,
                'img'         => $image,
                'autostart'   => $autostart,
                'loop'        => $loop,
                'right_close' => 'true',
            );
            $MTag = mCkplayer($config);
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
        $varUrl           = explode('?', $url);
        $varUrlArray     = array();
        $varUrlArrayStr = explode(',', $varUrl[1]);
        foreach ($varUrlArrayStr as $varUrlValue) {
            list($key, $value)   = explode('=', trim($varUrlValue));
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
    $attrStrs = I('attr');
    //清空非合法属性格式
    if ($attrStrs && !preg_match('/^((\d+)(_(\d+))+-?)+$/', $attrStrs)) {
        $attrStrs = '';
    }
    $request                        = I();
    $cateId && $request['cate_id'] = $cateId;
    if (C('TOKEN_ON')) {
        unset($request[C('TOKEN_NAME', null, '__hash__')]);
    }

    $cacheName  = 'M_attribute_arr' . serialize($attribute) . $attrStrs . serialize($request);
    $cacheValue = S($cacheName);
    if ($cacheValue && true !== APP_DEBUG) {
        return $cacheValue;
    }

    $validateData = array();
    $key           = 0;
    foreach ($attribute as $values) {
        $validateData[$key] = count($values);
        ++$key;
    }

    //解析attr字符串 开始
    $attrValue = array();
    foreach (explode('-', $attrStrs) as $attrStr) {
        $attrArr = explode('_', $attrStr);
        $k        = array_shift($attrArr);
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

    $attributeList = array();
    $key            = 0;
    foreach ($attribute as $name => $values) {
        unset($request['attr']);
        $data    = $attrValue;
        $checked = !isset($data[$key]);
        if (!$checked) {
            unset($data[$key]);
        }

        $dataStr                     = implode('-', $data);
        $dataStr && $request['attr'] = $dataStr;
        $attributeList[$key][]       = array(
            'name'    => $name,
            'checked' => $checked,
            'link'    => $checked ? 'javascript:void(0);' : mU('article_category', $request),
        );
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
                $checked  = true;
                $oldData = explode('_', preg_replace('/^' . $key . '_/', '', $data[$key]));
                $newData = array();
                //BUG创建新数据时错误
                array_walk($oldData, function ($v, $k) use ($valueKey, &$newData) {($v != $valueKey) && $newData[] = $v;});
                if (0 < count($newData)) {
                    $data[$key] = $key . '_' . implode('_', $newData);
                } else {
                    unset($data[$key]);
                }
            }
            $dataStr                     = implode('-', $data);
            $dataStr && $request['attr'] = $dataStr;
            $attributeList[$key][]       = array(
                'name'    => $value,
                'checked' => $checked,
                'link'    => mU('article_category', $request),
            );
        }
        ++$key;
    }

    $cacheValue = $attributeList;
    S($cacheName, $cacheValue, C('SYS_TD_CACHE'));

    return $attributeList;
}

function mAttributeWhere($attribute, $attr)
{
    $attrStrs = I('attr', $attr);
    //清空非合法属性格式
    if ($attrStrs && !preg_match('/^((\d+)(_(\d+))+-?)+$/', $attrStrs)) {
        $attrStrs = '';
    }

    $cacheName  = 'M_attribute_arr' . serialize($attribute) . $attrStrs;
    $cacheValue = S($cacheName);
    if ($cacheValue && true !== APP_DEBUG) {
        return $cacheValue;
    }

    $where      = array();
    $attrValue = array();
    foreach (explode('-', $attrStrs) as $attrStr) {
        $attrArr = explode('_', $attrStr);
        $k        = array_shift($attrArr);
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
        $twhere = array();
        foreach ($values as $valueKey => $value) {
            if (isset($attrValue[$key][$valueKey])) {
                $twhere[] = $name . ":" . $value;
            }
        }
        $where[] = $twhere;
        ++$key;
    }

    $cacheValue = $where;
    S($cacheName, $cacheValue, C('SYS_TD_CACHE'));

    return $where;
}

function mDate($timestamp, $format, $toZh = false)
{
    if (!$timestamp) {
        return '';
    }

    !$format && $format = C('SYS_DATE_DETAIL');
    $date               = date($format, $timestamp);
    if ($toZh) {
        $date = str_replace(array(
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
            'January', 'February', 'March', 'April', 'May', 'June', 'July',
            'August', 'September', 'October', 'November', 'December',
        ), array(
            L('week') . L('one'), L('week') . L('two'), L('week') . L('three'),
            L('week') . L('four'), L('week') . L('five'), L('week') . L('six'),
            L('week') . L('day'), L('one') . L('month'), L('two') . L('month'),
            L('three') . L('month'), L('four') . L('month'), L('five') . L('month'),
            L('six') . L('month'), L('seven') . L('month'), L('eight') . L('month'),
            L('nine') . L('month'), L('ten') . L('month'),
            L('ten') . L('one') . L('month'), L('ten') . L('two') . L('month'),
        ), $date);
    }
    return $date;
}

function mIptoadd($ip, $type = 0)
{
    if ('' == $ip) {
        return '';
    }

    $IpLocation = new \Org\Net\IpLocation('../../../../Public/UTFWry.dat');
    $ipInfo    = $IpLocation->getlocation($ip);
    $reStr     = '';
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
    $reStr = mb_convert_encoding($reStr, 'utf-8', array('gbk', 'utf-8'));
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
        $QRcode   = new \Common\Lib\QRcode();
        $pngPath = TEMP_PATH . md5($dataMd5) . '.png';
        $QRcode->png($data, $pngPath, $level, $size, $margin);
        $pngData = file_get_contents($pngPath);
        @unlink($pngPath);
        S($dataMd5, $pngData);
    }
    return U('Home/Index/cache', array('type' => 'qrcode', 'id' => $dataMd5));
}

// 构造Page html 必须放在公共函数中 配合ViewFilterBehavior
function mPage($config)
{
    $maxRow   = ($config['max_row']) ? $config['max_row'] : C('SYS_MAX_ROW');
    $countRow = ($config['count_row']) ? $config['count_row'] : 0;
    $roll      = ($config['roll']) ? $config['roll'] : 5;
    if ($maxRow >= $countRow) {
        return '';
    }
    $parameter = I('', '', 'urlencode');
    if (C('TOKEN_ON')) {
        unset($parameter[C('TOKEN_NAME')]);
    }

    $Page           = new \Think\Page($countRow, $maxRow, $parameter);
    $Page->rollPage = $roll;
    $Page->setConfig('header', '<span class="rows">' . L('inall') . ' %TOTAL_ROW% ' . L('inall') . L('item') . '</span>');
    $Page->setConfig('prev', L('previous') . L('page'));
    $Page->setConfig('next', L('next') . L('page'));
    $Page->setConfig('first', L('first') . L('page') . '...');
    $Page->setConfig('last', '...' . L('last') . L('one') . L('page'));
    $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');

    unset($parameter['p']);
    $inputJumpLink                             = U(ACTION_NAME, $parameter);
    !isset($config['preg_njump']) && $inputJump = <<<EOF
    <div class="fr">
        <form class="form-inline" action="{$inputJumpLink}">
            <input class="form-control w80" type="text" name="p" onKeyup="mInIntRange(this,1,{$countRow});" />
            <button class="btn btn-default" type="submit" >GO</button>
        </form>
    </div>
EOF;
    //默认的翻页样式
    $replacement = array(
        'preg_div'     => '<ul class="pagination">' . $inputJump . '\1</ul>',
        'preg_a'       => '<li>\1</li>',
        'preg_current' => '<li class="active"><a>\1</a></li>',
        'preg_rows'    => '<li><a>\1</a></li>',
    );
    isset($config['preg_div']) && $replacement['preg_div']         = $config['preg_div'];
    isset($config['preg_a']) && $replacement['preg_a']             = $config['preg_a'];
    isset($config['preg_current']) && $replacement['preg_current'] = $config['preg_current'];
    isset($config['preg_rows']) && $replacement['preg_rows']       = $config['preg_rows'];
    $pageStr                                                      = $Page->show();
    $pageStr                                                      = preg_replace('/<div>(.*)<\/div>/', $replacement['preg_div'], $pageStr);
    $pageStr                                                      = preg_replace('/(<a[^<]+<\/a>)/', $replacement['preg_a'], $pageStr);
    $pageStr                                                      = preg_replace('/<span class\=\"current.+>([^<]+)<\/span>/', $replacement['preg_current'], $pageStr);
    $pageStr                                                      = preg_replace('/<span class\=\"rows.+>([^<]+)<\/span>/', $replacement['preg_rows'], $pageStr);

    return $pageStr;
}

//扫描模板
function mScanTemplate($name, $module, $controller)
{
    $dir = APP_PATH . $module . '/' . C('DEFAULT_V_LAYER') . '/';
    C('DEFAULT_THEME') && $dir .= C('DEFAULT_THEME') . '/';
    $themeInfo = F('theme_info', '', $dir);

    if ('/' == C('TMPL_FILE_DEPR')) {
        $dir .= $controller . '/';
        $preg = '/^' . $name . '_(\w*)/';
    } else {
        $preg = '/^' . $controller . '_' . $name . '_(\w*)\./';
    }
    $templateList = array();
    $dirs          = scandir($dir);
    foreach ($dirs as $file) {
        if (preg_match($preg, $file, $match)) {
            $template = array('value' => $match[1]);
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
function mGetUrlpreg($prefix = '')
{
    $pregRoot = '((\.\.\/){0,})(?!';
    $pregRoot .= (__ROOT__) ? str_replace('/', '\/', __ROOT__) : '\/';
    $pregRoot .= '|#|\w*:)';
    $urlpreg['pattern'] = array(
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
    );
    $urlpreg['replacement'] = '\1\2' . $prefix . '\5\2\6';
    return $urlpreg;
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
    $PHPMailer->FromName = L('system'); //设置发件人的姓名
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
