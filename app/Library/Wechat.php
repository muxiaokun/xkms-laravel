<?php
// Wechat Class
namespace App\Library;

class Wechat
{
    public $Oauth2_code;
    private $appId;
    private $appSecret;
    private $token;
    private $encodingAesKey;
    private $block_size;
    private $ErrorCode = array(
        'OK'                     => 0,
        'ValidateSignatureError' => -40001, //签名验证错误
        'ParseXmlError'          => -40002, //xml解析失败
        'ComputeSignatureError'  => -40003, //sha加密生成签名失败
        'IllegalAesKey'          => -40004, //encodingAesKey 非法
        'ValidateAppidError'     => -40005, //appid 校验错误
        'EncryptAESError'        => -40006, //aes 加密失败
        'DecryptAESError'        => -40007, //aes 解密失败
        'IllegalBuffer'          => -40008, //解密后得到的buffer非法
        'EncodeBase64Error'      => -40009, //base64加密失败
        'DecodeBase64Error'      => -40010, //base64解密失败
        'GenReturnXmlError'      => -40011, //生成xml失败
    );
/**
 * 构造函数
 * @param $appId string 公众平台的appId
 * @param $token string 公众平台上，开发者设置的token
 * @param $encodingAesKey string 公众平台上，开发者设置的EncodingAESKey
 */
    public function __construct()
    {
        $this->appId          = config('system.wechat_id');
        $this->appSecret      = config('system.wechat_secret');
        $this->token          = config('system.wechat_token');
        $this->encodingAesKey = config('system.wechat_aeskey');
        //给默认值一般不许修改 不可动态配置
        $this->block_size  = 32;
        $this->Oauth2_code = 'code';
    }

    /*
     * 微信验证平台
     * @param $encrypt_msg string 微信加密签名，signature结合了开发者填写的token参数和请求中的timestamp参数、nonce参数。
     * @param $timestamp string 时间戳
     * @param $nonce string 随机数
     * @param $echostr string 随机字符串
     */
    public function checkSignature($signature, $timestamp, $nonce)
    {
        if (!$this->token) {
            return false;
        }
        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        if ($signature == sha1($tmpStr)) {
            //验证成功
            return true;
        } else {
            //验证失败
            return false;
        }
    }

    public function get_access_token()
    {
        $access_token = S('wechat_access_token');
        if (!$access_token) {
            $access_token = $this->access_token();
            S('wechat_access_token', $access_token['access_token'], $access_token['expires_in'] - 60);
            $access_token = $access_token['access_token'];
        }
        return $access_token;
    }

    /*
     * 获取access_token
     */
    private function access_token()
    {
        $appId         = $this->appId;
        $appSecret     = $this->appSecret;
        $enlink_fonmot = 'https://api.weixin.qq.com/cgi-bin/token?'
            . 'grant_type=client_credential'
            . '&appid=%s'
            . '&secret=%s';
        $enlink       = sprintf($enlink_fonmot, $appId, $appSecret);
        $json_str     = file_get_contents($enlink);
        $access_token = json_decode($json_str, true);
        return $access_token;
    }

    /*
     * 获取服务器ip list
     * @param $access_token string
     */
    public function server_ip()
    {
        $access_token = $this->get_access_token();
        if (!$access_token) {
            return false;
        }

        $enlink_fonmot = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=%s';
        $enlink        = sprintf($enlink_fonmot, $access_token);
        $json_str      = file_get_contents($enlink);
        $server_ip     = json_decode($json_str, true);
        return $server_ip['ip_list'];
    }

    /*
     * Oauth2 验证平台
     * @param $url string 需要生成的回调链接。
     * @param $scope string 获取用户信息类型
     * @param $state string 用户扩展信息
     */
    public function Oauth2_enlink($url, $scope = '', $state = '')
    {
        $appId      = $this->appId;
        $code       = $this->Oauth2_code;
        $url        = urlencode($url);
        $scope_type = array(
            'snsapi_base',
            'snsapi_userinfo',
        );
        $scope         = (in_array($scope, $scope_type)) ? $scope : 'snsapi_base';
        $enlink_fonmot = 'https://open.weixin.qq.com/connect/oauth2/authorize?'
            . 'appid=%s'
            . '&redirect_uri=%s'
            . '&response_type=%s'
            . '&scope=%s'
            . '&state=%s'
            . '#wechat_redirect';
        $enlink = sprintf($enlink_fonmot, $appId, $url, $code, $scope, $state);
        return $enlink;
        //返回的数据案例 在这个位置获取code
        //redirect_uri/?code=CODE&state=STATE。
    }

    /*
     * Oauth2 获取access_token
     * @param $code string 获取的code后。
     */
    public function Oauth2_access_token($code)
    {
        $appId         = $this->appId;
        $appSecret     = $this->appSecret;
        $code          = urlencode($code);
        $enlink_fonmot = 'https://api.weixin.qq.com/sns/oauth2/access_token?'
            . 'appid=%s'
            . '&secret=%s'
            . '&code=%s'
            . '&grant_type=authorization_code';
        $enlink       = sprintf($enlink_fonmot, $appId, $appSecret, $code);
        $json_str     = file_get_contents($enlink);
        $access_token = json_decode($json_str, true);
        return $access_token;
    }

    /*
     * Oauth2 获取access_token
     * @param $code string 获取的code后。
     * 由于access_token拥有较短的有效期，当access_token超时后，可以使用refresh_token进行刷新，
     * refresh_token拥有较长的有效期（7天、30天、60天、90天），当refresh_token失效的后，需要用户重新授权。
     */
    public function Oauth2_refresh_token($refresh_code)
    {
        $appId         = $this->appId;
        $enlink_fonmot = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?'
            . 'appid=%s'
            . '&grant_type=refresh_token'
            . '&refresh_token=%s';
        $enlink       = sprintf($enlink_fonmot, $appId, $refresh_code);
        $json_str     = file_get_contents($enlink);
        $access_token = json_decode($json_str, true);
        return $access_token;
    }
    /*
     * Oauth2_access_token/Oauth2_refresh_token 返回数组示例
     * access_token 网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
     * expires_in access_token接口调用凭证超时时间，单位（秒）
     * refresh_token 用户刷新access_token
     * openid 用户唯一标识
     * scope 用户授权的作用域，使用逗号（,）分隔
     */

    /*
     * Oauth2 获取user
     * @param $access_token string
     * @param $openid string
     */
    public function Oauth2_user($access_token, $openid)
    {
        $enlink_fonmot = 'https://api.weixin.qq.com/sns/userinfo?'
            . 'access_token=%s'
            . '&openid=%s'
            . '&lang=zh_CN';
        $enlink   = sprintf($enlink_fonmot, $access_token, $openid);
        $json_str = file_get_contents($enlink);
        $user     = json_decode($json_str, true);
        return $user;
    }
    /*
     * Oauth2_user 返回数组示例
     * openid 用户的唯一标识
     * nickname 用户昵称
     * sex 用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
     * province 用户个人资料填写的省份
     * city 普通用户个人资料填写的城市
     * country 国家，如中国为CN
     * headimgurl 用户头像，（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
     * privilege 用户特权信息，json 数组
     * unionid 只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。详见：获取用户个人信息（UnionID机制）
     */

    /*
     * Oauth2 验证access_token
     * @param $code string 获取的code后。
     */
    public function Oauth2_check_token($access_token, $openid)
    {
        $enlink_fonmot = 'https://api.weixin.qq.com/sns/auth?access_token=%s&openid=%s';
        $enlink        = sprintf($enlink_fonmot, $access_token, $openid);
        $json_str      = file_get_contents($enlink);
        $access_token  = json_decode($json_str, true);
        return $access_token;
    }

    //获取模板真实名称
    public function get_template($template_id_short)
    {
        $access_token = $this->get_access_token();
        if (!$access_token) {
            return false;
        }

        $enlink_fonmot = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=%s';
        $enlink        = sprintf($enlink_fonmot, $access_token);
        $data          = json_encode(array('template_id_short' => $template_id_short));
        $get_template  = $this->post($enlink, $data);
        $template_id = json_decode($get_template, true);
        return $template_id;
    }

    //发送模板
    public function put_template($data)
    {
        $access_token = $this->get_access_token();
        if (!$access_token || !$data) {
            return false;
        }

        $enlink_fonmot = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s';
        $enlink        = sprintf($enlink_fonmot, $access_token);
        $data          = json_encode($data);
        $put_template  = $this->post($enlink, $data);
        $put_template = json_decode($put_template, true);
        return $put_template;
    }

    //发送客服消息接口
    public function put_msg($data)
    {
        $access_token = $this->get_access_token();
        if (!$access_token || !$data) {
            return false;
        }

        $enlink_fonmot = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s';
        $enlink        = sprintf($enlink_fonmot, $access_token);
        $data          = json_encode($data);
        $put_msg       = $this->post($enlink, $data);
        return $put_msg;
    }

    //POST提交数据
    private function post($link, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $link); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        //curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // 模拟用户使用的浏览器
        // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        // curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl); //捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

    /*
     * 微信 Array to XML
     * @param $code string 获取的code后。
     */
    public function msg_encode($data)
    {
        $xml      = new \DOMDocument();
        $node_xml = $xml->createElement('xml');
        foreach ($data as $key => $value) {
            if (preg_match('/^\d*$/', $value)) {
                $newnode = $xml->createElement($key, $value);
            } else {
                $newnode = $xml->createElement($key);
                $cddata  = $xml->createCDATASection($value);
                $newnode->appendChild($cddata);
            }
            $node_xml->appendChild($newnode);
        }
        $xml->appendChild($node_xml);
        $xml_str = $xml->saveXML();
        $xml_str = preg_replace('/<\?.*?\?>\n/', '', $xml_str);
        return $xml_str;
    }

    /*
     * 微信 XML to Array
     * @param $code string 获取的code后。
     */
    public function msg_decode($xmltext, $node_cfg)
    {
        if (empty($xmltext) || !is_array($node_cfg)) {
            return false;
        }

        $xml = new \DOMDocument();
        $xml->loadXML($xmltext);
        $xml_info = array();
        foreach ($node_cfg as $node) {
            $node_value = $xml->getElementsByTagName($node)->item(0)->nodeValue;
            if ($node_value) {
                $xml_info[$node] = $node_value;
            }

        }
        return $xml_info;
    }

//上面都是我自己写的   下面是复制进来的

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
     * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
     * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
     *                      当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function encryptMsg($replyMsg, $timeStamp, $nonce, &$encryptMsg)
    {
        //加密
        $array = $this->encrypt($replyMsg, $this->appId);
        $ret   = $array[0];
        if ($ret != 0) {
            return $ret;
        }

        if ($timeStamp == null) {
            $timeStamp = time();
        }
        $encrypt = $array[1];

        //生成安全签名
        $array = $this->getSHA1($timeStamp, $nonce, $encrypt);
        $ret   = $array[0];
        if ($ret != 0) {
            return $ret;
        }
        $signature = $array[1];

        //生成发送的xml
        $encryptMsg = $this->generate($encrypt, $signature, $timeStamp, $nonce);
        return $this->ErrorCode['OK'];
    }

    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *    <li>利用收到的密文生成安全签名，进行签名验证</li>
     *    <li>若验证通过，则提取xml中的加密消息</li>
     *    <li>对消息进行解密</li>
     * </ol>
     *
     * @param $msgSignature string 签名串，对应URL参数的msg_signature
     * @param $timestamp string 时间戳 对应URL参数的timestamp
     * @param $nonce string 随机串，对应URL参数的nonce
     * @param $postData string 密文，对应POST请求的数据
     * @param &$msg string 解密后的原文，当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptMsg($msgSignature, $timestamp = null, $nonce, $postData, &$msg)
    {
        if (strlen($this->encodingAesKey) != 43) {
            return $this->ErrorCode['IllegalAesKey'];
        }
        //提取密文
        $array = $this->extract($postData);
        $ret   = $array[0];
        if ($ret != 0) {
            return $ret;
        }
        if ($timestamp == null) {
            $timestamp = time();
        }
        $encrypt     = $array[1];
        $touser_name = $array[2];
        $array       = $this->getSHA1($timestamp, $nonce, $encrypt);
        $ret         = $array[0];
        if ($ret != 0) {
            return $ret;
        }
        $signature = $array[1];
        if ($signature != $msgSignature) {
            return $this->ErrorCode['ValidateSignatureError'];
        }
        $result = $this->decrypt($encrypt, $this->appId);
        if ($result[0] != 0) {
            return $result[0];
        }
        $msg = $result[1];
        return $this->ErrorCode['OK'];
    }

    /**
     * 提取出xml数据包中的加密消息
     * @param string $xmltext 待提取的xml字符串
     * @return string 提取出的加密消息字符串
     */
    public function extract($xmltext)
    {
        try {
            $xml = new \DOMDocument();
            $xml->loadXML($xmltext);
            $array_e    = $xml->getElementsByTagName('Encrypt');
            $array_a    = $xml->getElementsByTagName('ToUserName');
            $encrypt    = $array_e->item(0)->nodeValue;
            $tousername = $array_a->item(0)->nodeValue;
            return array(0, $encrypt, $tousername);
        } catch (Exception $e) {
            //print $e . "\n";
            return array($this->ErrorCode['ParseXmlError'], null, null);
        }
    }

    /**
     * 生成xml消息
     * @param string $encrypt 加密后的消息密文
     * @param string $signature 安全签名
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     */
    public function generate($encrypt, $signature, $timestamp, $nonce)
    {
        $format = "<xml>
        <Encrypt><![CDATA[%s]]></Encrypt>
        <MsgSignature><![CDATA[%s]]></MsgSignature>
        <TimeStamp>%s</TimeStamp>
        <Nonce><![CDATA[%s]]></Nonce>
        </xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }

    /**
     * 用SHA1算法生成安全签名
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encrypt 密文消息
     */
    public function getSHA1($timestamp, $nonce, $encrypt_msg)
    {
        //排序
        try {
            $array = array($encrypt_msg, $this->token, $timestamp, $nonce);
            sort($array, SORT_STRING);
            $str = implode($array);
            return array($this->ErrorCode['OK'], sha1($str));
        } catch (Exception $e) {
            //print $e . "\n";
            return array($this->ErrorCode['ComputeSignatureError'], null);
        }
    }

    /**
     * 提供基于PKCS7算法的加解密接口.
     * 对需要加密的明文进行填充补位
     * @param $text 需要进行填充补位操作的明文
     * @return 补齐明文字符串
     */
    public function encode($text)
    {
        $block_size  = $this->block_size;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = $block_size - ($text_length % $this->block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = $block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp     = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 提供基于PKCS7算法的加解密接口.
     * 对解密后的明文进行补位删除
     * @param decrypted 解密后的明文
     * @return 删除填充补位后的明文
     */
    public function decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @return string 加密后的密文
     */
    public function encrypt($text, $appid)
    {
        try {
            //获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr();
            $text   = $random . pack("N", strlen($text)) . $text . $appid;
            // 网络字节序
            $size   = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv     = substr($this->encodingAesKey, 0, 16);
            //使用自定义的填充方式对明文进行补位填充
            $text = $this->encode($text);
            mcrypt_generic_init($module, $this->encodingAesKey, $iv);
            //加密
            $encrypted = mcrypt_generic($module, $text);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);

            //print(base64_encode($encrypted));
            //使用BASE64对加密后的字符串进行编码
            return array($this->ErrorCode['OK'], base64_encode($encrypted));
        } catch (Exception $e) {
            //print $e;
            return array($this->ErrorCode['EncryptAESError'], null);
        }
    }

    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @return string 解密得到的明文
     */
    public function decrypt($encrypted, $appid)
    {
        try {
            //使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $module         = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv             = substr($this->encodingAesKey, 0, 16);
            mcrypt_generic_init($module, $this->encodingAesKey, $iv);

            //解密
            $decrypted = mdecrypt_generic($module, $ciphertext_dec);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            return array($this->ErrorCode['DecryptAESError'], null);
        }

        try {
            //去除补位字符
            $result = $this->decode($decrypted);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) {
                return "";
            }

            $content     = substr($result, 16, strlen($result));
            $len_list    = unpack("N", substr($content, 0, 4));
            $xml_len     = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid  = substr($content, $xml_len + 4);
        } catch (Exception $e) {
            //print $e;
            return array($this->ErrorCode['IllegalBuffer'], null);
        }
        if ($from_appid != $appid) {
            return array($this->ErrorCode['ValidateAppidError'], null);
        }

        return array(0, $xml_content);

    }

    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    public function getRandomStr()
    {
        $str     = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max     = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}
