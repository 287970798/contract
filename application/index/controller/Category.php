<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/20
 * Time: 11:42
 */

namespace app\index\controller;

use app\index\model\Category as CategoryModel;
use app\index\model\Type;

class Category extends BaseController
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
            $category = new CategoryModel();
            // 检测合同类别是否已存在
            $one = $category->where('name', 'eq', $post['name'])->find();
            if ($one) {
                return -1; // 合同类别存在
            }
            $result = $category->allowField(true)->save($post);
            return $result;
        }
        $this->assign([
            'title' => '新增合同类别'
        ]);
        return $this->fetch('add');
    }
    // edit
    public function edit()
    {
        $id = $this->request->param('id');
        if ($this->request->isPost() && $this->request->isAjax()) {
            $post = $this->request->post();
            $category = new CategoryModel();
            $result = $category->save($post, ['id' => $id]);
            return $result;
        }
        $category = CategoryModel::get($id);
        $this->assign([
            'title' => '合同分类修改',
            'category' => $category
        ]);
        return $this->fetch();
    }
    // list
    public function all()
    {
        $categories = CategoryModel::all();
        $categories = json_encode($categories);
        $this->assign([
            'title' => '合同分类管理',
            'categories' => $categories
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
        // 分类下是否有类别，存在类别则不允许删除
        $count = Type::where('category_id', 'eq', $id)->count();
        if ($count > 0) {
            return 'hasTypes';
        }

        $result = CategoryModel::destroy($id);
        return $result;
    }

}