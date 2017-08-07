<?php
Route::group(['prefix' => 'qwentin'], function () {

    // IMPORTANT
    // All Prefixs Inside Admin Route Part Are Closely Connected With Table Aliases
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::group(['prefix' => 'posts'], function () {
            Route::group(['prefix' => 'crud'], function () {
                Route::get('/create', ['uses'=>'Admin\Posts\CrudController@create','as'=>'crudCreate']);
                Route::get('/delete', ['uses'=>'Admin\Posts\CrudController@delete','as'=>'crudDelete']);
                Route::get('/update', ['uses'=>'Admin\Posts\CrudController@update','as'=>'crudUpdate']);
            });
            Route::group(['prefix' => 'hashtag'], function () {
                Route::get('/edit', ['uses'=>'Admin\Posts\HashtagController@edit','as'=>'hashtagEdit']);
                Route::get('/attach', ['uses'=>'Admin\Posts\HashtagController@attach','as'=>'hashtagAttach']);
                Route::get('/remove', ['uses'=>'Admin\Posts\HashtagController@remove','as'=>'hashtagRemove']);
            });
        });
        Route::group(['prefix' => 'categories'], function () {
            Route::group(['prefix' => 'categories'], function () {
                Route::get('/create_category', ['uses'=>'Admin\Categories\CategoriesController@createCategory_get','as'=>'categoriesCreateGet']);
                Route::post('/create_category', ['uses'=>'Admin\Categories\CategoriesController@createCategory_post','as'=>'categoriesCreatePost']);
                Route::get('/change', ['uses'=>'Admin\Categories\CategoriesController@change','as'=>'categoriesChange']);
            });
            Route::group(['prefix' => 'subcategories'], function () {
                Route::get('/create_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@createSubcategory_get','as'=>'subcategoriesCreateGet']);
                Route::post('/create_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@createSubcategory_post','as'=>'subcategoriesCreatePost']);
                Route::get('/change', ['uses'=>'Admin\Categories\SubcategoriesController@change','as'=>'subcategoriesChange']);
            });
        });
    });
    Route::get('/{navbar}/{part}', ['uses'=>'AdminController@part','as'=>'adminNavbarPart']);
    Route::get('/login', ['uses'=>'Auth\AdminLoginController@showLoginForm','as'=>'adminLoginForm']);
    Route::post('/login', ['uses'=>'Auth\AdminLoginController@login','as'=>'adminLoginPost']);
    Route::get('/', ['uses'=>'AdminController@index','as'=>'adminDashboard']);
});


Route::get('/', ['uses'=>'CategoryController@getCategory','as'=>'category']);
Route::get('/home', ['uses'=>'CategoryController@getCategory','as'=>'category']);

// ToDo all should be in where a-z 0-9
Route::get('/hashtag/{alias}', ['uses'=>'SubcategoryController@getByHashtag','as'=>'byHashtag']);

Route::get('/{category}/{subcategory}', ['uses'=>'SubcategoryController@getSubCategory','as'=>'subcategory']);

Route::get('/{category}/{subcategory}/{post}', ['uses'=>'PostController@getPost','as'=>'post']);


Auth::routes();
