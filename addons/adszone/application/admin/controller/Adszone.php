<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\Config;

//use think\Config;

/**
 * 海报&广告管理
 *
 * @icon fa fa-circle-o
 */
class Adszone extends Backend {

	/**
	 * Test模型对象
	 * @var \app\admin\model\Test
	 */
	protected $AdszoneZone = null;
	protected $AdszoneAds = null;

	public function _initialize() {
		parent::_initialize();
		$this->AdszoneZone = new \addons\adszone\model\AdszoneZone();
		$this->AdszoneAds = new \addons\adszone\model\AdszoneAds();
	}

	/*
	 * 广告位列表
	 */

	public function index() {
		if ($this->request->isAjax()) {
			//weigh desc,
			$list = $this->AdszoneZone->order('id desc')->select();
			$total = count($list);
			$prefix = Config::get('database.prefix');
			$result = array("total" => $total, "rows" => $list, "prefix" => $prefix);
			return json($result);
		}
		//$adsConfig = $this->getConfig("developerTips");
		$adsConfig = get_addon_config('adszone');
		$adsHome = addon_url("adszone");
		$this->view->assign("adsConfig", $adsConfig);
		$this->view->assign("adsHome", $adsHome);
		return $this->view->fetch();
	}

	/*
	 * 添加广告位
	 */

	public function add() {
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			if ($params) {
				if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
					$params[$this->dataLimitField] = $this->auth->id;
				}
				try {
					//是否采用模型验证
					if ($this->modelValidate) {
						$name = str_replace("\\model\\", "\\validate\\", get_class($this->AdszoneZone));
						$validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
						$this->AdszoneZone->validate($validate);
					}
					if ($this->checkMark($params['mark'])) {
						$this->error("广告位标记已存在！");
					}
					$params['createtime'] = time();
					$params['updatetime'] = time();
					$result = $this->AdszoneZone->allowField(true)->save($params);
					$prefix = Config::get('database.prefix');
					if ($result !== false) {
						$this->success();
					} else {
						$this->error($this->AdszoneZone->getError());
					}
				} catch (\think\exception\PDOException $e) {
					$this->error($e->getMessage());
				} catch (\think\Exception $e) {
					$this->error($e->getMessage());
				}
			}
			$this->error(__('Parameter %s can not be empty', ''));
		}

		$prefix = Config::get('database.prefix');
		$this->view->assign("prefix", $prefix);
		return $this->view->fetch();
	}

	/*
	 * 编辑广告位
	 */

	public function edit($ids = NULL) {
		$row = $this->AdszoneZone->get($ids);

		if (!$row)
			$this->error(__('No Results were found'));
		$adminIds = $this->getDataLimitAdminIds();
		if (is_array($adminIds)) {
			if (!in_array($row[$this->dataLimitField], $adminIds)) {
				$this->error(__('You have no permission'));
			}
		}
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			if ($params) {
				try {
					//是否采用模型验证
					if ($this->modelValidate) {
						$name = basename(str_replace('\\', '/', get_class($this->AdszoneZone)));
						$validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
						$row->validate($validate);
					}
					if ($this->checkMark($params['mark'], $ids)) {
						$this->error("广告位标记已存在！");
					}
					//$params['updatetime'] = time();
					$result = $row->allowField(true)->save($params);
					if ($result !== false) {
						$this->success();
					} else {
						$this->error($row->getError());
					}
				} catch (\think\exception\PDOException $e) {
					$this->error($e->getMessage());
				} catch (\think\Exception $e) {
					$this->error($e->getMessage());
				}
			}
			$this->error(__('Parameter %s can not be empty', ''));
		}
		$prefix = Config::get('database.prefix');
		$this->view->assign("row", $row);
		$this->view->assign("prefix", $prefix);
		return $this->view->fetch();
	}

	/*
	 * 删除广告位
	 */

	public function del($ids = NULL) {
		if ($ids) {
			$pk = $this->AdszoneZone->getPk();
			$adminIds = $this->getDataLimitAdminIds();
			if (is_array($adminIds)) {
				$count = $this->AdszoneZone->where($this->dataLimitField, 'in', $adminIds);
			}
			$list = $this->AdszoneZone->where($pk, 'in', $ids)->select();
			$prefix = Config::get('database.prefix');
			$count = 0;
			foreach ($list as $k => $v) {
				try {
					$this->AdszoneAds->where("zone_id", '=', $v->id)->delete(); //删除广告内容
					$count += $v->delete();
				} catch (Exception $ex) {
					$this->error(__('No rows were deleted'));
				}
			}
			if ($count) {
				$this->success(__('删除成功！'), null, __('删除成功！'));
			} else {
				$this->error(__('No rows were deleted'));
			}
		}
		$this->error(__('Parameter %s can not be empty', 'ids'));
	}

	/*
	 * 广告内容列表
	 */

	public function ads($ids = NULL) {
		if ($ids == NULL) {
			$ids = intval($this->request->request('ids'));
		}
		$model = $this->AdszoneZone->get($ids);
		if (!$model) {
			$this->error(__('No Results were found'));
		}

		if ($this->request->isAjax()) {
			$list = $this->AdszoneAds->where("zone_id", '=', $ids)->order('weigh', 'desc')->order('id desc')->select();
			$total = count($list);
			$prefix = Config::get('database.prefix');
			$result = array("total" => $total, "rows" => $list, "prefix" => $prefix);
			return json($result);
		}
		$this->view->assign("ids", $ids);
		return $this->view->fetch();
	}

	/*
	 * 添加广告内容
	 */

	public function ads_add($aid = NULL) {
		$zoneAds = $this->AdszoneZone->get($aid);
		if (!$zoneAds)
			$this->error(__('No Results were found'));
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			if ($params) {
				try {
					//是否采用模型验证
					if ($this->modelValidate) {
						$name = str_replace("\\model\\", "\\validate\\", get_class($this->AdszoneAds));
						$validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.ads_add' : true) : $this->modelValidate;
						$this->AdszoneAds->validate($validate);
					}
					//$prefix = Config::get('database.prefix');
					$adsData = array();
					$adsData['zone_id'] = $params['zone_id'];
					$adsData['title'] = $params['title'];
					$adsData['imageurl'] = $params['imageurl'];
					$adsData['linkurl'] = $params['linkurl'];
					$adsData['target'] = $params['target'];
					$adsData['effectime'] = strtotime($params['effectime']);
					$adsData['expiretime'] = strtotime($params['expiretime']);
					//$adsData['weigh'] = isset($params['weigh'])?intval($params['weigh']):0;
					$adsData['code'] = "";

					$result = $this->AdszoneAds->allowField(true)->save($adsData);
					if ($result !== false) {
                                                $this->AdszoneAds->save(array("weigh"=>$this->AdszoneAds->id));
						$this->success();
					} else {
						$this->error($this->AdszoneAds->getError());
					}
				} catch (\think\exception\PDOException $e) {
					$this->error($e->getMessage());
				} catch (\think\Exception $e) {
					$this->error($e->getMessage());
				}
			}
			$this->error(__('Parameter %s can not be empty', ''));
		}
		$prefix = Config::get('database.prefix');
		$this->view->assign("prefix", $prefix);
		$this->view->assign("aid", $aid);
		return $this->view->fetch();
	}

	/*
	 * 编辑广告内容
	 */

	public function ads_edit($ids = NULL) {
		$row = $this->AdszoneAds->get($ids);
		if (!$row)
			$this->error(__('No Results were found'));
		$adminIds = $this->getDataLimitAdminIds();
		if (is_array($adminIds)) {
			if (!in_array($row[$this->dataLimitField], $adminIds)) {
				$this->error(__('You have no permission'));
			}
		}
		if ($this->request->isPost()) {
			$params = $this->request->post("row/a");
			if ($params) {
				try {
					//是否采用模型验证
					if ($this->modelValidate) {
						$name = str_replace("\\model\\", "\\validate\\", get_class($this->AdszoneAds));
						$validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.ads_edit' : true) : $this->modelValidate;
						$this->AdszoneAds->validate($validate);
					}
					//$prefix = Config::get('database.prefix');
					$adsData = array();
					//$adsData['id'] = $params['id'];
					//$adsData['zone_id'] = $params['zone_id'];
					$adsData['title'] = $params['title'];
					$adsData['imageurl'] = $params['imageurl'];
					$adsData['linkurl'] = $params['linkurl'];
					$adsData['target'] = $params['target'];
					$adsData['effectime'] = strtotime($params['effectime']);
					$adsData['expiretime'] = strtotime($params['expiretime']);
					//$adsData['weigh'] = isset($params['weigh'])?intval($params['weigh']):0;
					$adsData['code'] = "";

					$result = $row->allowField(true)->save($adsData);
					if ($result !== false) {
						$this->success();
					} else {
						$this->error($this->AdszoneAds->getError());
					}
				} catch (\think\exception\PDOException $e) {
					$this->error($e->getMessage());
				} catch (\think\Exception $e) {
					$this->error($e->getMessage());
				}
			}
			$this->error(__('Parameter %s can not be empty', ''));
		}
		$this->view->assign("row", $row);
		$this->view->assign("ids", $ids);
		return $this->view->fetch();
	}

	/*
	 * 删除广告内容
	 */

	public function ads_del($ids = NULL) {
		if ($ids) {
			$pk = $this->AdszoneAds->getPk();
			$adminIds = $this->getDataLimitAdminIds();
			if (is_array($adminIds)) {
				$count = $this->AdszoneAds->where($this->dataLimitField, 'in', $adminIds);
			}
			$list = $this->AdszoneAds->where($pk, 'in', $ids)->select();
			$count = 0;
			foreach ($list as $k => $v) {
				$count += $v->delete();
			}
			if ($count) {
				$this->success();
			} else {
				$this->error(__('No rows were deleted'));
			}
		}
		$this->error(__('Parameter %s can not be empty', 'ids'));
	}

	/*
	 * 检查mark是否存在
	 */

	protected function checkMark($mark = NULL, $adsId = 0) {/*
	  if ($mark == NULL) {
	  $mark = $this->request->request('mark');
	  }

	  if ($adsId == NULL) {
	  $adsId = intval($this->request->request('adsid'));
	  }
	 */
		$row = $this->AdszoneZone->where('mark', $mark)->find();
		if ($row) {
			if ($adsId > 0) {
				if ($adsId == $row['id']) {
					//不存在
					return false;
				} else {
					//存在
					return true;
				}
			} else {
				//存在
				return true;
			}
		} else {
			//不存在
			return false;
		}
	}

	/**
	 * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
	 * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
	 * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
	 */
}
