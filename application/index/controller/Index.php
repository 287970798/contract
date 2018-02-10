<?php
namespace app\index\controller;


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
