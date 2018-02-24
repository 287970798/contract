<?php

use think\Route;

// user
Route::rule('user/add', 'user/add');
Route::rule('user/all', 'user/all');
Route::rule('user/edit/:id', 'user/edit');
Route::rule('user/del/:id', 'user/del');
Route::rule('login', 'user/login');
Route::rule('logout', 'user/logout');

// customer
Route::rule('customer/add', 'customer/add');
Route::rule('customer/all', 'customer/all');
Route::rule('customer/edit/:id', 'customer/edit');
Route::rule('customer/del/:id', 'customer/del');

// linkman
Route::rule('linkman/add', 'linkman/add');
Route::rule('linkman/all', 'linkman/all');
Route::rule('linkman/edit/:id', 'linkman/edit');
Route::rule('linkman/del/:id', 'linkman/del');
Route::rule('linkman/getByCustomerId/:id', 'linkman/getByCustomerId');

// category
Route::rule('category/add', 'category/add');
Route::rule('category/all', 'category/all');
Route::rule('category/edit/:id', 'category/edit');
Route::rule('category/del/:id', 'category/del');

// type
Route::rule('type/add', 'type/add');
Route::rule('type/all', 'type/all');
Route::rule('type/edit/:id', 'type/edit');
Route::rule('type/del/:id', 'type/del');
Route::rule('type/getNextSn/:id', 'type/getNextSn');
Route::rule('type/getByCategoryId/:id', 'type/getByCategoryId');

// contract
Route::rule('contract/add', 'contract/add');
Route::rule('contract/all', 'contract/all');
Route::rule('contract/expiring', 'contract/expiring');
Route::rule('contract/edit/:id', 'contract/edit');
Route::rule('contract/del/:id', 'contract/del');
Route::rule('contract/getContractSn/:category_id/:type_id', 'contract/getContractSn');

// contract extra
Route::rule('contractExtra/del/:id', 'contractExtra/del');

// upload
Route::rule('upload', 'file/upload');

// approval
Route::rule('approval/add', 'approval/add');
Route::rule('approval/edit/:id', 'approval/edit');
Route::rule('approval/del/:id', 'approval/del');
Route::rule('approval/all', 'approval/all');
Route::rule('approval/user/:id', 'approval/ApprovalByUser'); // 我发起的审批
Route::any('approval/:id', 'approval/ApprovalById', [], ['id'=>'\d+']);
Route::rule('approval/start/:id', 'approval/setStart');

// approval node
Route::rule('node/approval/:id', 'node/approval');
Route::rule('node/check$', 'node/check');

// test 测试curl发送邮件
Route::rule('test/mail', 'test/mail');