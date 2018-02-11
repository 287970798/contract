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

// customer
Route::rule('linkman/add', 'linkman/add');
Route::rule('linkman/all', 'linkman/all');
Route::rule('linkman/edit/:id', 'linkman/edit');
Route::rule('linkman/del/:id', 'linkman/del');