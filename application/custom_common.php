<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/
if (!function_exists('img')) {
    function img($img)
    {
//        global $_IMG_CDN_;
        return (stripos($img, 'http') === 0 || stripos($img, 'data:image') === 0) ?
            $img :
            rtrim(config('site.img_cdn_url'), '/') . $img;
    }
}

function getCodeAd($mark)
{
    $Adszone = new \addons\adszone\library\Adszone();
    $result = $Adszone->getAdsByMark($mark); //按照标记调用广告位
    return $result;
}

function getForceCommendBooks($ids,$type='cartoon')
{
//    trace(['id' => ['in',is_array($ids)?implode(',',$ids):$ids]]);
    return \app\service\BookService::getBooks($type,['id' => ['in',is_array($ids)?implode(',',$ids):$ids]]);
}

function check_module($s)
{
    if (!check_type($s))
        return 'cartoon';
    else
        return check_type($s);
}



function check_type($type)
{
    $arr = [
        'cartoon',
        'novel',
        'listen'
    ];
    if (in_array(strtolower($type),$arr))
        return strtolower($type);
    else
        return false;
}

function get_types()
{
    return [
        'cartoon',
        'novel',
        'listen'
    ];
}

function get_types_key()
{
    return [
        'cartoon' => null,
        'novel' => null,
        'listen' => null
    ];
}

function parse_key_value($str)
{
    $arr1 = explode('&',$str);
    $result = [];
    foreach ($arr1 as $item) {
        $arr2 = explode('=',$item);
        if (is_array($arr2))
        {
            if (count($arr2) == 2)
            {
                $result[$arr2[0]] = $arr2[1];
            }
        }
    }
    return $result;
}
