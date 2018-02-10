<?php

use think\Route;

Route::rule('user/add', 'user/add');
Route::rule('user/all', 'user/all');
Route::rule('user/edit/:id', 'user/edit');
Route::rule('user/del/:id', 'user/del');
Route::rule('login', 'user/login');