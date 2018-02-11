<?php


namespace app\index\controller;

use app\index\model\Linkman as LinkmanModel;
use app\index\model\Customer;

class Linkman extends BaseController
{
    protected $beforeActionList = [
        'checkAuth' => ['only' => 'del']
    ];
    // add
    public function add()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $post = $this->request->post();
            $linkman = new LinkmanModel();
            // 检测联系人是否已存在
            $one = $linkman->where('phone', 'eq', $post['phone'])->find();
            if ($one) {
                return -1; // 联系人存在
            }
            $result = $linkman->allowField(true)->save($post);
            return $result;
        }
        // 获取所有客户
        $customers = Customer::all();
        $this->assign([
            'title' => '新增联系人',
            'customers' => $customers
        ]);
        return $this->fetch('add');
    }
    // edit
    public function edit()
    {
        $id = $this->request->param('id');
        if ($this->request->isPost() && $this->request->isAjax()) {
            $post = $this->request->post();
            $linkman = new LinkmanModel();
            $result = $linkman->save($post, ['id' => $id]);
            return $result;
        }
        $linkman = LinkmanModel::get($id);
        // 获取所有客户
        $customers = Customer::all();
        $this->assign([
            'title' => '联系人修改',
            'linkman' => $linkman,
            'customers' => $customers
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
        $result = LinkmanModel::destroy($id);
        return $result;
    }
    // list
    public function all()
    {
        $linkmans = LinkmanModel::with('customer')->select();
        $linkmans = json_encode($linkmans);
        $this->assign([
            'title' => '联系人管理',
            'linkmans' => $linkmans
        ]);
        return $this->fetch();
    }

}