<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/20
 * Time: 12:16
 */

namespace app\index\controller;

use app\index\model\Type as TypeModel;
use app\index\model\Category;

class Type extends BaseController
{
    // auth
    protected $beforeActionList = [
        'checkAuth' => ['only' => 'add,edit,all,del']
    ];

    // add
    public function add()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $post = $this->request->post();
            $type = new TypeModel();
            // 检测合同类别是否已存在
            $one = $type->where('name', 'eq', $post['name'])->find();
            if ($one) {
                return -1; // 合同类别存在
            }
            $result = $type->allowField(true)->save($post);
            return $result;
        }
        // 获取所有分类
        $categories = Category::all();
        $this->assign([
            'title' => '新增合同类别',
            'categories' => $categories
        ]);
        return $this->fetch('add');
    }
    // edit
    public function edit()
    {
        $id = $this->request->param('id');
        if ($this->request->isPost() && $this->request->isAjax()) {
            $post = $this->request->post();
            $type = new TypeModel();
            $result = $type->save($post, ['id' => $id]);
            return $result;
        }
        $type = TypeModel::get($id);
        // 获取所有合同分类
        $categories = Category::all();
        $this->assign([
            'title' => '合同类别修改',
            'type' => $type,
            'categories' => $categories
        ]);
        return $this->fetch();
    }
    // list
    public function all()
    {
        $types = TypeModel::with('category')->order('category_id desc, id desc')->select();
        $types = json_encode($types);
        $this->assign([
            'title' => '合同类别管理',
            'types' => $types
        ]);
        return $this->fetch();
    }
    // get list by category_id
    public function getByCategoryId()
    {
        if (!$this->request->isAjax()) {
            return -1;
        }
        $categoryId = $this->request->param('id');
        $types = TypeModel::where('category_id', 'eq', $categoryId)->select();
        return json($types);
    }

    // del
    public function del()
    {
        if (!$this->request->isDelete()) {
            return 'error';
        }
        $id = $this->request->param('id');
        if (!is_numeric($id) && !is_int($id)) {
            return 'error';
        }

        // 验证当前分类下是否存在合同，如果存在，不允许删除。
        // TODO...

        $result = TypeModel::destroy($id);
        return $result;
    }
    // 获取当前分类下类别的当前最大编号
    public function getNextSn()
    {
        // 必须为ajax请求
        if (!$this->request->isAjax()) {
            return -1;
        }
        $category_id = $this->request->param('id');
        // 获取当前最大合同类别编号，包含已删除的，不存在返回0
        $maxSn = TypeModel::withTrashed()->where('category_id', 'eq', $category_id)->max('sn');
        // 自增1
        $maxSn ++;
        // 如果位数小于3，则左侧补0
        if (strlen($maxSn) < 3) {
            $maxSn = str_pad($maxSn, 3, '0', STR_PAD_LEFT);
        }
        return $maxSn;
    }
}