<?php

namespace addons\adszone\library;

use think\Config;
use Exception;
use think\Db;
use addons\adszone\model;

class Adszone
{

    protected $AdszoneZone = null;
    protected $AdszoneAds = null;

    public function __construct()
    {
        $this->AdszoneZone = new \addons\adszone\model\AdszoneZone();
        $this->AdszoneAds = new \addons\adszone\model\AdszoneAds();
    }

    /**
     * 根据Id获取广告位信息
     * @param integer $id 广告位ID
     * @return model\AdszoneZone|array|bool|null
     */
    public function getAdsById($id)
    {
        $adsZone = $this->AdszoneZone->get(function ($query) use ($id) {
            $query->field('id,name,mark,type,width,height,code');
            $query->where('id', $id);
        });
        if ($adsZone && $adsZone['type'] == 3) {
            $adsZone = $adsZone->toArray();
            unset($adsZone['width']);
            unset($adsZone['height']);
        } elseif ($adsZone) {
            $adsZone = $adsZone->toArray();
            unset($adsZone['code']);
            $adsImages = $this->AdszoneAds->all(function ($query) use ($id) {
                $time = time();
                $query->field('id,title,imageurl,linkurl,target,expiretime,weigh');
                $query->where('zone_id', $id);
                $query->where('effectime', "<=", $time);
                $query->where('expiretime', ">=", $time);
                $query->order('weigh', 'desc');
                $query->order('id', 'desc');
            });
            if ($adsImages) {
                if ($adsZone['type'] == 1) {
                    $adsZone['data'] = $adsImages[0]->toArray();
                } else {
                    $data = [];
                    foreach ($adsImages as $k => &$v) {
                        $data[] = $v->toArray();
                    }
                    $adsZone['data'] = $data;
                }
            } else {
                $adsZone['data'] = null;
            }
        }

        if ($adsZone) {
            return $adsZone;
        } else {
            return false;
        }
    }

    /**
     * 根据广告位标记获取广告位信息
     * @param $mark
     * @return model\AdszoneZone|array|bool|null
     */
    public function getAdsByMark($mark)
    {
        $adsZone = $this->AdszoneZone->get(function ($query) use ($mark) {
            $query->field('id,name,mark,type,width,height,code');
            $query->where('mark', $mark);
        });
        if ($adsZone && $adsZone['type'] == 3) {
            $adsZone = $adsZone->toArray();
            unset($adsZone['width']);
            unset($adsZone['height']);
        } elseif ($adsZone) {
            $adsZone = $adsZone->toArray();
            unset($adsZone['code']);
            $zone_id = $adsZone['id'];
            $adsImages = $this->AdszoneAds->all(function ($query) use ($zone_id) {
                $time = time();
                $query->field('id,title,imageurl,linkurl,target,expiretime,weigh');
                $query->where('zone_id', $zone_id);
                $query->where('effectime', "<=", $time);
                $query->where('expiretime', ">=", $time);
                $query->order('weigh', 'desc');
                $query->order('id', 'desc');
            });
            if ($adsImages) {
                if ($adsZone['type'] == 1) {
                    $adsZone['data'] = $adsImages[0]->toArray();
                } else {
                    $data = [];
                    foreach ($adsImages as $k => &$v) {
                        $data[] = $v->toArray();
                    }
                    $adsZone['data'] = $data;
                }
            } else {
                $adsZone['data'] = null;
            }
        }

        if ($adsZone) {
            return $adsZone;
        } else {
            return false;
        }
    }

}
