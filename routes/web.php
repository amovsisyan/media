<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', ['uses'=>'CategoryController@getCategory','as'=>'category']);
Route::get('/home', ['uses'=>'CategoryController@getCategory','as'=>'category']);

// ToDo make groups and prefix
Route::get('/admn/admin/login', ['uses'=>'Auth\AdminLoginController@showLoginForm','as'=>'adminLoginForm']);
Route::post('/admn/admin/login', ['uses'=>'Auth\AdminLoginController@login','as'=>'adminLoginPost']);
Route::get('/admn/admin', ['uses'=>'AdminController@index','as'=>'adminDashboard']);

// ToDo all should be in where a-z 0-9
Route::get('/hashtag/{alias}', ['uses'=>'SubcategoryController@getByHashtag','as'=>'byHashtag']);

Route::get('/{category}/{subcategory}', ['uses'=>'SubcategoryController@getSubCategory','as'=>'subcategory']);

Route::get('/{category}/{subcategory}/{post}', ['uses'=>'PostController@getPost','as'=>'post']);

// open when you will need users
Auth::routes();

