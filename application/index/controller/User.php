<?php


namespace app\index\controller;

use app\index\model\User as UserModel;
use think\Exception;
use think\Session;

class User extends BaseController
{
    protected $beforeActionList = [
        'checkAuth' => ['only' => 'add,all,del']
    ];
    // add
    public function add()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $post = $this->request->post();
            $post['password'] = sha1($post['password']);
            $user = new UserModel();
            // 检测用户是否已存在
            $one = $user->where('username', 'eq', $post['username'])->find();
            if ($one) {
                return -1; // 用户存在
            }
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
        $admin = Session::get('admin');

        // 禁止修改别人资料
        if ($admin['level'] != 1 && $id != $admin['id']){
            $this->error('非法操作，无权限');
        }

        if ($this->request->isPost() && $this->request->isAjax()) {
            $post = $this->request->post();
            // 如果有密码并且为空，则不修改
            if (isset($post['password'])) {
                if ($post['password'] == '') {
                    unset($post['password']);
                } else {
                    $post['password'] = sha1($post['password']);
                }
            }
            $user = new UserModel();
            $result = $user->save($post, ['id' => $id]);
            return $result;
        }
        $user = UserModel::get($id);
        $this->assign([
            'title' => '用户修改',
            'user' => $user
        ]);
        return $this->fetch();
    }

    // delete
    public function del()
    {
        if (!$this->request->isDelete()) {
            return 'error';
        }
        $id = $this->request->param('id');
        if (!is_numeric($id) && !is_int($id)) {
            return 'error';
        }
        $result = UserModel::destroy($id);
        return $result;
    }
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
    public function login()
    {
        if ($this->request->isPost()){
            $post = $this->request->post();
            $ip = $this->request->ip();
            $address = ip2address($ip);
            $username = $post['username'];
            $password = sha1($post['password']);
            $user = UserModel::where('username', 'eq', $username)->where('password', 'eq', $password)->find();
            if ($user) {
                $user->ip = $ip;
                $user->address = $address;
                // 记录session
                Session::set('admin', $user);
                Log::add(['model'=>'LoginLog', 'user_id'=>$user->id, 'ip'=>$ip, 'location'=>$address]);
                return 1;
            } else {
                return 2;
            }
        }
        $this->assign([
            'title' => '用户登录'
        ]);
        return $this->fetch();
    }
    // logout
    public function logout()
    {
        Session::delete('admin');
        $this->redirect('/login', 302);
    }
}