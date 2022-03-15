<?php

namespace addons\comment\controller;

use think\addons\Controller;

class Index extends Controller
{

    protected $layout = 'default';

    protected $config = [];

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        return $this->view->fetch();
    }

}
