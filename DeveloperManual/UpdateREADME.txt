--------------------------------------------------------------------------------
引入的插件列表
Bootstrap                   3.3.4                   getbootstrap.com
KindEditor                  4.1.11-20150912         kindeditor.net GitHub(kindsoft/kindeditor)
jQuery                      1.11.3                  jquery.com
jQuery base64               0.1                     jquery.com
jQuery Cookie               0.1                     github.com/carhartl/jquery-cookie
jQuery UI                   1.11.4                  jqueryui.com
jQuery Timepicker Addon     1.5.4                   GitHub(trentrichardson/jQuery-Timepicker-Addon)
ZeroClipboard               2.2.0                   GitHub(zeroclipboard/zeroclipboard)
ckplayer                    6.7                     ckplayer.com
CssMin                      3.0.1                   github.com/natxet/CssMin
JSMin                       1.1.2                   github.com/mrclay/jsmin-php
--------------------------------------------------------------------------------
修改  ThinkPHP\Library\Think\Template.class.php 184 模板正则缺少对 js对象格式的过滤  2015 02 13
    $content = preg_replace_callback('/('.$this->config['tmpl_begin'].')(?!\'|\")([^\d\w\s'.$this->config['tmpl_begin'].$this->config['tmpl_end'].'].+?)('.$this->config['tmpl_end'].')/is', array($this, 'parseTag'),$content);
--------------------------------------------------------------------------------
修改 ThinkPHP\Library\Behavior\CheckLangBehavior.class.php 67 自动加载公共控制器语言包也就是Common\Lang\zh-cn\admin.php 2015 03 16
一般语言包 放在Common/Lang/zh-cn.php Common/Lang/zh-cn/*.php Module/Lang/zh-cn.php Module/Lang/zh-cn/*.php 具体参考CheckLangBehavior.class.php
        // 读取应用公共模块语言包
        $file   =  LANG_PATH.LANG_SET.'/'.strtolower(CONTROLLER_NAME).'.php';
        if(is_file($file))
            L(include $file);
--------------------------------------------------------------------------------
修改  Common/functions.php中的function I() 将$default的默认赋值去掉 下文isset没有意义  2015 04 07
    function I($name,$default,$filter=null,$datas=null) {
--------------------------------------------------------------------------------
修改 Xkms\Common\Lib\JSMin.class.php 82 修改两行代码加速运行 2015 07 23
  public function minify($js) {
    $this->input       = str_replace("\r\n", "\n", $js);
    $this->inputLength = strlen($this->input);
    return $this->min();
    }
--------------------------------------------------------------------------------
修改 Xkms\Common\Lib\CssMin.class.php 原文件不支持namespace 新增加后需要进行正则替换 2015 07 23
除了 1614 行 2075 行不能进行替换
    get_class\((.*?)\)
    str_replace('Common\\Lib\\\\','',get_class($1))