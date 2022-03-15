<?php


namespace addons\landingpage\controller;


use app\common\model\landingpage\Page;
use think\addons\Controller;
use think\View;

class Index extends Controller
{

    protected function _initialize()
    {
        parent::_initialize();
        $config = get_addon_config('landingpage');
        $config['avatars'] = explode(',',$config['avatars']);
        trace($config);
        $this->view -> assign([
            'config' => $config,
        ]);
    }

    public function index()
    {
        if ($id = input('id'))
        {
            $content = Page::get($id);
        }
        else
        {
            $content = Page::orderRaw('rand()') -> limit(1) -> find();
        }
        return view('',[
            'content' => mb_substr($content -> content,0,mb_strlen($content -> content)-20),
            'title' => $content -> title,
            'keywords' => $content -> keywords,
            'description' => $content -> description,
            'hide' => mb_substr($content -> content,-20),
        ]);
    }

    public function tips()
    {
        return view();
    }
}