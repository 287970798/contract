<?php
namespace app\index\controller;


use think\Session;

class Index extends BaseController
{
    public function index()
    {
        $this->assign([
            'title' => '控制台'
        ]);
        return $this->fetch();
    }
}
