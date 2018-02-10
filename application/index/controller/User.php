<?php


namespace app\index\controller;

use app\index\model\User as UserModel;

class User extends BaseController
{
    // add
    public function add()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $post = $this->request->post();
            $post['password'] = sha1($post['password']);
            $user = new UserModel();
            $result = $user->allowField(true)->save($post);
            return $result;
        }
        $this->assign([
            'title' => '新增用户'
        ]);
        return $this->fetch('add');
    }
    // edit
    public function edit()
    {
        $id = $this->request->param('id');
        $post = $this->request->post();
        $user = new UserModel();
        $result = $user->save($post, ['id'=>$id]);
        return $result;
    }

    // delete

    // list
    public function all()
    {
        $users = UserModel::all();
        $users = json_encode($users);
        $this->assign([
            'title' => '用户管理',
            'users' => $users
        ]);
        return $this->fetch();
    }
    // login

    // logout
}