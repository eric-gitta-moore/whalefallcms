<?php
error_reporting(0);
/*
*外部编程接口处理标签内容示范文件
*该文件内自动系统的三个参数$LabelArray ,$LabelUrl
*对任意采集的标签都适用请对标签内容处理后直接将该数组serialize($LabelArray)输出，
*采集器内部即可接收到该标签的内容，对比以前的接口规则，新规则可以实现标签之间的数据调用和处理
*参数说明：
  *$LabelArray    -  标签名及标签内容集合 结构如：Array('栏目id' => 2,'出处'=>  '新浪微博','内容'=>'<center><b>暴笑短信')  ##
  *$LabelUrl      -  当前采集的页面的Url地址
  *$LabelCookie   -  当前采集页面，服务器返回的Cookie信息。
  * 特别注意:如果是处理列表页,默认页,多页时会有以下两个标签
    $LabelArray['Html']       网页的源代码,没有经过采集器处理的,直接下载后的数据.修改这里的数据,请将新值赋予$LabelArray['Html']
    $LabelArray['PageType']   值可能为 List, Content ,Pages, Save 分别代表处理列表页,默认页,多页,保存时
*以上语句建议不更改,以下为用户操作区域  该区域只限对数组值进行操作，不得有打印输出产生，不得直接增加或删除相应标签名
*/
global $LabelArray,$LabelUrl,$LabelCookie;
/**
 * utf-8 转unicode
 * @param string $name
 * @return string
 */
function myutf8_unicode($name)
{
    $name = iconv('UTF-8', 'UCS-2BE', $name);
    $len = strlen($name);
    $str = '';
    for ($i = 0; $i < $len - 1; $i = $i + 2) {
        $c = $name[$i];
        $c2 = $name[$i + 1];
        if (ord($c) > 0) {
            $str .= '\u' . base_convert(ord($c), 10, 16) . str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
        } else {
            $str .= '\u' . str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
        }
    }
    return $str;
}

/**
 * unicode 转 utf-8
 *
 * @param string $name
 * @return string
 */
function myunicode_decode($name)
{
    $name = strtolower($name);
    // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
    $pattern = '/\\\u([\w]{4})/i';
    preg_match_all($pattern, $name, $matches);
    $name = preg_replace_callback($pattern, function ($matches) {
        $name = '';
        $str = $matches[0];
        if (strpos($str, '\\u') === 0) {
            $code = base_convert(substr($str, 2, 2), 16, 10);
            $code2 = base_convert(substr($str, 4), 16, 10);
            $c = chr($code) . chr($code2);
            $c = iconv('UCS-2BE', 'UTF-8', $c);
            $name .= $c;
        } else {
            $name .= $str;
        }
        return $name;
    }, $name);

    return $name;
}

function curl_post($url, $postfields = '', $headers = '', $timeout = 20, $file = 0)
{
//    return false;
    $result = $info = $msg = '';
    $code = 0;
    if (function_exists('curl_init')) {
        $ch = curl_init();//初始化一个的curl对话，返回一个链接资源句柄
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_NOBODY => false,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        );
        if (is_array($postfields) && $file == 0) {
            $options[CURLOPT_POSTFIELDS] = http_build_query($postfields);
        } else {
            $options[CURLOPT_POSTFIELDS] = $postfields;
        }
        curl_setopt_array($ch, $options);//
        if (is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $result = curl_exec($ch);//执行一个的curl对话
        $code = curl_errno($ch);//返回一个的包含当前对话错误消息的数字编号
        $msg = curl_error($ch);//返回一个的包含当前对话错误消息的char串
        $info = curl_getinfo($ch);//获取一个的curl连接资源的消息
        curl_close($ch);//关闭对话，并释放资源
    }
    return array(
        'data' => $result,
        'code' => $code,
        'msg' => $msg,
        'info' => $info
    );
}

function post($LabelUrl,$default = '')
{
    $parse = parse_url($LabelUrl);
    $html = $default;
    if ($parse['host'])
    {
        $url_without_query = $parse['scheme'] . '://' . $parse['host'] . $parse['path'];
        if (!empty($parse['query'])) {
            $result = curl_post($url_without_query, $parse['query']);
            $result = $result['data'];
            $html = $result;
        }
    }
    return $html;
}

/**
 * @desc xmsb_multiSort PHP对多维数组内每个数组及其值进行排序
 * @param array $originalArray 要排序的数组
 * @param boolean $isReverse 是否为倒序 默认为false
 * @param boolean $keepKey 是否保留键值对应 默认为false
 * @return array|mixed
 */
function xmsb_multiSort($originalArray,$isReverse = false, $keepKey = false)
{
    $isReverse ? arsort($originalArray) : asort($originalArray);
    if(count($originalArray) != count($originalArray, 1))
    {
        function callback($array, $isReverse, $keepKey)
        {
            foreach($array as $arrKey => $arrValue)
            {
                if(is_array($arrValue))
                {
                    $isReverse ? arsort($arrValue) : asort($arrValue);
                    $array[$arrKey] = $keepKey ? callback($arrValue, $isReverse, $keepKey) : callback(array_values($arrValue), $isReverse, $keepKey);
                }
            }
            return $array;
        }

        $newArray = $keepKey ? $originalArray : array_values($originalArray);
        return callback($newArray, $isReverse, $keepKey);
    }
    else
    {
        return $keepKey ? $originalArray : array_values($originalArray);
    }
}

function json_recode($json_text)
{
    $json = json_decode($json_text, true);
    if (is_array($json)) {
//        $json = array_map('urlencode',$json);
//        $json = urlencode($json);
        $json = xmsb_multiSort($json,true,true);

        $json = json_encode($json);
//        return $json;
        $json = myunicode_decode(urldecode($json));
        $json = str_replace('": "', '":"', $json);
        $json = str_replace('\"', '"', $json);
        $json = str_replace('\/', '/', $json);
        $json = trim($json, '"');
    }
    else
    {
        $json = $json_text;
    }
    return $json;
}

//--------------------------------------------------

$LabelArray['Html'] = post($LabelUrl,$LabelArray['Html']);


//if ($LabelArray['PageType'] == "Content") {
//    if (array_key_exists('book_detail',$LabelArray)
//        && array_key_exists('bid',$LabelArray)
//        && array_key_exists('token',$LabelArray))
//    {
//        $tpl = 'https://api.iheyman.com/api/cartoon/info?token={token}&bid={bid}';
//        $url = str_replace(array('{token}','{bid}'),array($LabelArray['token'],$LabelArray['bid']),$tpl);
//        $LabelArray['book_detail'] = post($url);
//    }
//    if (empty($LabelArray['book_detail']))
//    {
//        $LabelArray['book_detail'] = '未获取到任何信息';
//    }
//}

//echo serialize($LabelArray);exit();
$tmp = $LabelArray['Html'];
$LabelArray['Html'] = json_recode($tmp);

echo serialize($LabelArray);

?>