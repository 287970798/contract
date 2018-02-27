<?php


namespace app\index\controller;


use think\Session;

class Log extends BaseController
{
    // 记录日志
    // $data = ['model'=>'modelName', 'user_id'=>10, 'note'=>'']
    public static function add(Array $data)
    {
        $class = $data['model'];
        $model = self::LogModel($class);
        $result = $model->allowField(true)->save($data);
    }

    // 显示日志
    public function show()
    {
        // 创建数据表
        // 创建模型
        // 设置关联
        // 配置日志模型
        // 创建视图

        // 配置日志模型
        // function($query) {$query->useSoftDelete('id', ['>', 0]) 关联已删除的数据
        $config = [
            'login' => [
                'name' => '用户登录日志',
                'with' => [
                    'user'=>function($query) {$query->useSoftDelete('id', ['>', 0])->field('id, name');}
                ]
            ],
            'contract' => [
                'name' => '合同修改日志',
                'with' => [
                    'user' => function($query) {$query->useSoftDelete('id', ['>', 0])->field('id, name');},
                    'contract' => function($query) {$query->useSoftDelete('id', ['>', 0])->field('id, name');}
                ]
            ]
        ];
        // 日志类型
        $type = $this->request->param('type');
        $type = strtolower($type);
        // 日志模型名
        $class = ucfirst($type) . 'Log';
        $model = $this->LogModel($class);

        // 关联模型
        $with = $config[$type]['with'];
        $title = $config[$type]['name'];

        $logs = $model->with($with)->order('id desc')->select();
        $this->assign([
            "title" => $title,
            "logs" => $logs
        ]);
        return $this->fetch($type);
    }

    public static function LogModel($class)
    {
        if (!class_exists($class)) {
            $class = 'app\\index\\model\\'.$class;
        }
        if (!class_exists($class)) {
            return false;
        }
        // 实例化模型
        $model = new $class;
        return $model;
    }

}