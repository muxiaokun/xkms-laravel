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
function M_substr($str, $len, $suffix = true, $start = 0)
{
    $charset      = 'utf-8';
    $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $str_len = ($match[0]) ? count($match[0]) : strlen($str);
    if ($len > $str_len) {
        return $str;
    } else {
        $slice = join("", array_slice($match[0], $start, $len));
    }
    return $suffix ? $slice . '...' : $slice;
}

//转换本系统的格式时间到时间戳
//暂时只支持基本的格式YmdHis
function M_mktime($date, $is_detail = false)
{
    if ('' === $date) {
        return $date;
    }

    $date_format  = ($is_detail) ? C('SYS_DATE_DETAIL') : C('SYS_DATE');
    $match_format = $date_format;
    $str_search   = array('-', '[', ']', '(', ')', '^', '$');
    $str_replace  = array('\-', '\[', '\]', '\(', '\)', '\^', '\$');
    $match_format = '/' . str_replace($str_search, $str_replace, $match_format) . '/';
    //注意d 一定要放第一个 否则会和替换值重复
    $date_search  = array('d', 'Y', 'm', 'H', 'i', 's');
    $date_replace = array('(\d{2})', '(\d{4})', '(\d{2})', '(\d{2})', '(\d{2})', '(\d{2})');
    $pos          = array();
    foreach ($date_search as $v) {
        $pos[$v] = strpos($date_format, $v);
    }
    $match_format = str_replace($date_search, $date_replace, $match_format);
    asort($pos);
    $i       = 1;
    $sub_pos = array();
    foreach ($pos as $k => $v) {
        if ($v !== false) {
            $sub_pos[$k] = $i++;
        }

    }
    $pos = $sub_pos;
    if (preg_match($match_format, $date, $sub)) {
        $time = mktime($sub[$pos['H']], $sub[$pos['i']], $sub[$pos['s']], $sub[$pos['m']], $sub[$pos['d']], $sub[$pos['Y']]);
    } else {
        $time = null;
    }
    return $time;
}

//创建where_info中时间范围的数组
function M_mktime_range($input_name)
{
    $time_range = array();
    $gt_time    = M_mktime(I($input_name . '_start'));
    $lt_time    = M_mktime(I($input_name . '_end')) + 86400;
    if (I($input_name . '_start') && 0 < $gt_time) {
        $time_range[] = array('gt', $gt_time);
    }

    if (I($input_name . '_end') && 0 < $lt_time) {
        $time_range[] = array('lt', $lt_time);
    }

    return $time_range;
}

function M_exists($url, $is_thumb = false)
{
    if (!$url || !is_file($url)) {
        $system_default = "Public/css/bimages/default.png";
        switch (MODULE_NAME) {
            case 'Home':
                $url = C('SYS_DEFAULT_IMAGE', null, $system_default);
                break;
            default:
                $url = $system_default;
        }
    } elseif ($is_thumb) {
        $pathinfo = pathinfo($url);
        $new_name = $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
        $new_file = $pathinfo['dirname'] . '/' . $new_name;
        if (is_file($new_file)) {
            return $new_file;
        }

    }
    return $url;
}

// 获得字符串中的本站资源链接
function M_get_content_upload($str)
{
    preg_match_all('/(&quot;|\')\s*(?!(http:\/\/|https:\/\/|ftp:\/\/))[^\1]*?(Uploads[^\1]*?)\1/i', $str, $upload_links);
    return $upload_links[3];
}

//将内容中的IMG标签替换成异步IMG标签
function M_sync_img($content)
{
    $pattern = '/(<img.*?)\ssrc=([\'|\"])(.*?)\2(.*?\/?>)/i';
    $content = preg_replace_callback($pattern, function ($match) {
        $new_element = base64_encode($match[1] . $match[4]);
        $replacement = '<img src="' . M_exists(C('SYS_SYNC_IMAGE')) . '" ';
        $replacement .= 'M_Img="' . $new_element . '" ';
        $replacement .= 'M_img_src=' . $match[2] . $match[3] . $match[2] . ' />';
        return $replacement;
    }, $content);
    return $content;
}

//创建CKplayer
function M_ckplayer($config)
{
    $id           = $config['id'];
    $src          = $config['src'];
    $path_info    = pathinfo($src);
    $suffix       = $path_info['extension'];
    $allow_suffix = array('flv', 'f4v', 'mp4', 'm3u8', 'webm', 'ogg', 'flv', 'f4v', 'mp4');
    if (!$id || !$src || !in_array($suffix, $allow_suffix)) {
        return '';
    }

    $img        = ($config['img']) ? $config['img'] : ''; //default image
    $loop       = ($config['loop']) ? $config['loop'] : 2; //1repeat 2stop 2
    $volume     = ($config['volume']) ? $config['volume'] : 100; //0-100 100
    $autostart  = ($config['autostart']) ? $config['autostart'] : 2; //0stop 1play 2notload 2
    $config_xml = ($config['right_close']) ? 'ckplayer_min.xml' : 'ckplayer.xml';
    return <<<EOF
<script type="text/javascript" src="Public/ckplayer/ckplayer.js"></script>
<script type="text/javascript">
    var flashvars={f:<ntag url='{$src}' />,e:'{$loop}',v:'{$volume}',p:'{$autostart}',i:<ntag url='{$img}' />,c:1,x:'{$config_xml}'};
    CKobject.embed(<ntag url='Public/ckplayer/ckplayer.swf' />,'{$id}','','100%','100%',false,flashvars);
</script>
EOF;
}

//将内容中的视频替换成CKplayer
//custom_id 自定义输出div id
//jop=>just one player 只返回一个视频
function M_content2ckplayer($content, $image, $custom_id = false, $jop = false)
{
    if (!preg_match_all('/<embed.*?\/>/i', $content, $elements)) {
        return $content;
    }

    $allow_suffix = array('flv', 'f4v', 'mp4', 'm3u8', 'webm', 'ogg', 'flv', 'f4v', 'mp4');
    $attr_pattern = array(
        '(\ssrc=(\'|")(.*?(' . implode('|', $allow_suffix) . '))\3)', //4
        '(\swidth=(\'|")(.*?)\7)', //8
        '(\sheight=(\'|")(.*?)\10)', //11
        '(\sautostart=(\'|")(.*?)\13)', //14
        '(\sloop=(\'|")(.*?)\16)', //17
    );
    $pattern = '/(' . implode('|', $attr_pattern) . ')/i'; //1
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
            $image     = M_exists($image);
            $div_id    = ($custom_id) ? $custom_id : 'content2ckplayer_' . $id;
            if ($jop) {
                $width  = '100%';
                $height = '100%';
                $id     = mt_rand();
            }
            $config = array(
                'id'          => $div_id,
                'src'         => $src,
                'img'         => $image,
                'autostart'   => $autostart,
                'loop'        => $loop,
                'right_close' => 'true',
            );
            $M_tag = M_ckplayer($config);
            if ('' != $M_tag) {
                $re_content = 'span id="' . $div_id . '" style="display:block;margin:0 auto;';
                $re_content .= 'width:' . $width . ';height:' . $height . ';" >' . $M_tag . '</span';
                //替换时左右 <> 会被当成正则符号 所以默认返回内容缺少 <>
                if ($jop) {
                    return '<' . $re_content . '>';
                }

                $content = preg_replace($element, $re_content, $content, 1);
            }
        }
    }
    return $content;
}

// 自定义将字符串转换成系统连接
// 1 M/C/A?ars1=val1&ars2=val2
// 2 \w*://
function M_str2url($url)
{
    if ($url && preg_match('/^(\w*?:\/\/|javascript|#).*/', $url)) {
        return $url;
    } elseif ($url && preg_match('/^[\w\/]*?\??[\w=,]*?$/', $url)) {
        $var_url           = explode('?', $url);
        $var_url_array     = array();
        $var_url_array_str = explode(',', $var_url[1]);
        foreach ($var_url_array_str as $var_url_value) {
            list($key, $value)   = explode('=', trim($var_url_value));
            $var_url_array[$key] = $value;
        }
        return M_U(trim($var_url[0]), $var_url_array);
    } else {
        return ($url) ? '#' . $url : M_U();
    }
}

function M_in_array($target, $source)
{
    if (is_array($target)) {
        foreach ($target as $t) {
            if (!M_in_array($t, $source)) {
                return false;
            }

        }
        return true;
    } else {
        return in_array($target, $source);
    }
}

function M_attribute_arr($attribute, $cate_id = 0)
{
    $attr_strs = I('attr');
    //清空非合法属性格式
    if ($attr_strs && !preg_match('/^((\d+)(_(\d+))+-?)+$/', $attr_strs)) {
        $attr_strs = '';
    }
    $request                        = I();
    $cate_id && $request['cate_id'] = $cate_id;
    if (C('TOKEN_ON')) {
        unset($request[C('TOKEN_NAME', null, '__hash__')]);
    }

    $cache_name  = 'M_attribute_arr' . serialize($attribute) . $attr_strs . serialize($request);
    $cache_value = S($cache_name);
    if ($cache_value && true !== APP_DEBUG) {
        return $cache_value;
    }

    $validate_data = array();
    $key           = 0;
    foreach ($attribute as $values) {
        $validate_data[$key] = count($values);
        ++$key;
    }

    //解析attr字符串 开始
    $attr_value = array();
    foreach (explode('-', $attr_strs) as $attr_str) {
        $attr_arr = explode('_', $attr_str);
        $k        = array_shift($attr_arr);
        if ('' == $k) {
            continue;
        }

        foreach ($attr_arr as $v) {
            if (!preg_match('/_' . $value_key . '(?!\d)/', $data[$key]) && $v < $validate_data[$k]) {
                $attr_value[$k] .= '_' . $v;
            }
        }
        isset($attr_value[$k]) && $attr_value[$k] = $k . $attr_value[$k];
    }
    //解析attr字符串 结束

    $attribute_list = array();
    $key            = 0;
    foreach ($attribute as $name => $values) {
        unset($request['attr']);
        $data    = $attr_value;
        $checked = !isset($data[$key]);
        if (!$checked) {
            unset($data[$key]);
        }

        $data_str                     = implode('-', $data);
        $data_str && $request['attr'] = $data_str;
        $attribute_list[$key][]       = array(
            'name'    => $name,
            'checked' => $checked,
            'link'    => $checked ? 'javascript:void(0);' : M_U('article_category', $request),
        );
        foreach ($values as $value_key => $value) {
            unset($request['attr']);
            $data    = $attr_value;
            $checked = false;
            if (!preg_match('/_' . $value_key . '(?!\d)/', $data[$key])) {
                //添加参数
                if (!isset($data[$key])) {
                    $data[$key] = $key;
                }

                $data[$key] .= '_' . $value_key;
            } else {
                //削减参数
                $checked  = true;
                $old_data = explode('_', preg_replace('/^' . $key . '_/', '', $data[$key]));
                $new_data = array();
                //BUG创建新数据时错误
                array_walk($old_data, function ($v, $k) use ($value_key, &$new_data) {($v != $value_key) && $new_data[] = $v;});
                if (0 < count($new_data)) {
                    $data[$key] = $key . '_' . implode('_', $new_data);
                } else {
                    unset($data[$key]);
                }
            }
            $data_str                     = implode('-', $data);
            $data_str && $request['attr'] = $data_str;
            $attribute_list[$key][]       = array(
                'name'    => $value,
                'checked' => $checked,
                'link'    => M_U('article_category', $request),
            );
        }
        ++$key;
    }

    $cache_value = $attribute_list;
    S($cache_name, $cache_value, C('SYS_TD_CACHE'));

    return $attribute_list;
}

function M_attribute_where($attribute, $attr)
{
    $attr_strs = I('attr', $attr);
    //清空非合法属性格式
    if ($attr_strs && !preg_match('/^((\d+)(_(\d+))+-?)+$/', $attr_strs)) {
        $attr_strs = '';
    }

    $cache_name  = 'M_attribute_arr' . serialize($attribute) . $attr_strs;
    $cache_value = S($cache_name);
    if ($cache_value && true !== APP_DEBUG) {
        return $cache_value;
    }

    $where      = array();
    $attr_value = array();
    foreach (explode('-', $attr_strs) as $attr_str) {
        $attr_arr = explode('_', $attr_str);
        $k        = array_shift($attr_arr);
        if ('' == $k) {
            continue;
        }

        $attr_value[$k];
        foreach ($attr_arr as $v) {
            $attr_value[$k][$v] = $v;
        }
    }
    $key = 0;
    foreach ($attribute as $name => $values) {
        $twhere = array();
        foreach ($values as $value_key => $value) {
            if (isset($attr_value[$key][$value_key])) {
                $twhere[] = $name . ":" . $value;
            }
        }
        $where[] = $twhere;
        ++$key;
    }

    $cache_value = $where;
    S($cache_name, $cache_value, C('SYS_TD_CACHE'));

    return $where;
}

function M_date($timestamp, $format, $to_zh = false)
{
    if (!$timestamp) {
        return '';
    }

    !$format && $format = C('SYS_DATE_DETAIL');
    $date               = date($format, $timestamp);
    if ($to_zh) {
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

function M_iptoadd($ip, $type = 0)
{
    if ('' == $ip) {
        return '';
    }

    $IpLocation = new \Org\Net\IpLocation('../../../../Public/UTFWry.dat');
    $ip_info    = $IpLocation->getlocation($ip);
    $re_str     = '';
    switch ($type) {
        case 1:
            $ip_info['country'];
            break;
        case 2:
            $ip_info['area'];
            break;
        default:
            $re_str .= $ip_info['country'] . ' ' . $ip_info['area'];
    }
    $re_str = mb_convert_encoding($re_str, 'utf-8', array('gbk', 'utf-8'));
    return $re_str;
}

/*
 * 生成二维码方法
 * $level smallest -> L M Q H ->best
 * $size 1 - 10
 * $margin 0 - N
 * 并生成缓存 无失效期
 */
function M_qrcode($data, $level = 'H', $size = 10, $margin = 0)
{
    if (!$data) {
        return M_exists();
    }

    $data_md5 = 'QRcode_' . md5($data . $level . $size . $margin);
    $png_data = S($data_md5);
    if (!$png_data) {
        $QRcode   = new \Common\Lib\QRcode();
        $png_path = TEMP_PATH . md5($data_md5) . '.png';
        $QRcode->png($data, $png_path, $level, $size, $margin);
        $png_data = file_get_contents($png_path);
        @unlink($png_path);
        S($data_md5, $png_data);
    }
    return U('Home/Index/cache', array('type' => 'qrcode', 'id' => $data_md5));
}

// 构造Page html 必须放在公共函数中 配合ViewFilterBehavior
function M_page($config)
{
    $max_row   = ($config['max_row']) ? $config['max_row'] : C('SYS_MAX_ROW');
    $count_row = ($config['count_row']) ? $config['count_row'] : 0;
    $roll      = ($config['roll']) ? $config['roll'] : 5;
    if ($max_row >= $count_row) {
        return '';
    }
    $parameter = I('', '', 'urlencode');
    if (C('TOKEN_ON')) {
        unset($parameter[C('TOKEN_NAME')]);
    }

    $Page           = new \Think\Page($count_row, $max_row, $parameter);
    $Page->rollPage = $roll;
    $Page->setConfig('header', '<span class="rows">' . L('inall') . ' %TOTAL_ROW% ' . L('inall') . L('item') . '</span>');
    $Page->setConfig('prev', L('previous') . L('page'));
    $Page->setConfig('next', L('next') . L('page'));
    $Page->setConfig('first', L('first') . L('page') . '...');
    $Page->setConfig('last', '...' . L('last') . L('one') . L('page'));
    $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');

    unset($parameter['p']);
    $input_jump_link                             = U(ACTION_NAME, $parameter);
    !isset($config['preg_njump']) && $input_jump = <<<EOF
    <div class="fr">
        <form class="form-inline" action="{$input_jump_link}">
            <input class="form-control w80" type="text" name="p" onKeyup="M_in_int_range(this,1,{$count_row});" />
            <button class="btn btn-default" type="submit" >GO</button>
        </form>
    </div>
EOF;
    //默认的翻页样式
    $replacement = array(
        'preg_div'     => '<ul class="pagination">' . $input_jump . '\1</ul>',
        'preg_a'       => '<li>\1</li>',
        'preg_current' => '<li class="active"><a>\1</a></li>',
        'preg_rows'    => '<li><a>\1</a></li>',
    );
    isset($config['preg_div']) && $replacement['preg_div']         = $config['preg_div'];
    isset($config['preg_a']) && $replacement['preg_a']             = $config['preg_a'];
    isset($config['preg_current']) && $replacement['preg_current'] = $config['preg_current'];
    isset($config['preg_rows']) && $replacement['preg_rows']       = $config['preg_rows'];
    $page_str                                                      = $Page->show();
    $page_str                                                      = preg_replace('/<div>(.*)<\/div>/', $replacement['preg_div'], $page_str);
    $page_str                                                      = preg_replace('/(<a[^<]+<\/a>)/', $replacement['preg_a'], $page_str);
    $page_str                                                      = preg_replace('/<span class\=\"current.+>([^<]+)<\/span>/', $replacement['preg_current'], $page_str);
    $page_str                                                      = preg_replace('/<span class\=\"rows.+>([^<]+)<\/span>/', $replacement['preg_rows'], $page_str);

    return $page_str;
}

//扫描模板
function M_scan_template($name, $module, $controller)
{
    $dir = APP_PATH . $module . '/' . C('DEFAULT_V_LAYER') . '/';
    C('DEFAULT_THEME') && $dir .= C('DEFAULT_THEME') . '/';
    $theme_info = F('theme_info', '', $dir);

    if ('/' == C('TMPL_FILE_DEPR')) {
        $dir .= $controller . '/';
        $preg = '/^' . $name . '_(\w*)/';
    } else {
        $preg = '/^' . $controller . '_' . $name . '_(\w*)\./';
    }
    $template_list = array();
    $dirs          = scandir($dir);
    foreach ($dirs as $file) {
        if (preg_match($preg, $file, $match)) {
            $template = array('value' => $match[1]);
            if ($theme_info[md5($file)]) {
                $template['name'] = $theme_info[md5($file)]['name'];
            } else {
                $template['name'] = $match[1];
            }
            $template_list[] = $template;
        }
    }
    return $template_list;
}

//生成处理url的preg
function M_get_urlpreg($prefix = '')
{
    $preg_root = '((\.\.\/){0,})(?!';
    $preg_root .= (__ROOT__) ? str_replace('/', '\/', __ROOT__) : '\/';
    $preg_root .= '|#|\w*:)';
    $urlpreg['pattern'] = array(
        '/(<a.*?\shref=)([\'|\"])' . $preg_root . '(.*?)\2(.*?>)/is',
        '/(<script.*?\ssrc=)([\'|\"])' . $preg_root . '(.*?)\2(.*?>)/is',
        '/(<link.*?\shref=)([\'|\"])' . $preg_root . '(.*?)\2(.*?\/?>)/is',
        '/(<img.*?\ssrc=)([\'|\"])' . $preg_root . '(.*?)\2(.*?\/?>)/is',
        '/(url\()([\'|\"]?)' . $preg_root . '(.*?)\2(.*?\))/is',
        '/(<embed.*?\ssrc=)([\'|\"])' . $preg_root . '(.*?)\2(.*?\/?>)/is',
        //分割注视 上：html 下:自定义的
        //异步加载图片功能
        '/(<img.*?\sM_img_src=)([\'|\"])' . $preg_root . '(.*?)\2(.*?\/?>)/is',
        //处理获得src
        '/(.?)<ntag.*?\surl=([\'|\"])' . $preg_root . '(.*?)\2.*?\/?>(.?)/is',
    );
    $urlpreg['replacement'] = '\1\2' . $prefix . '\5\2\6';
    return $urlpreg;
}

//使用PHPMailer发送邮件
function M_sendmail($to, $title = '', $content = '', $chart = 'utf-8', $attachment = '')
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
