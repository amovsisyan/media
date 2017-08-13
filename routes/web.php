<?php
Route::group(['prefix' => 'qwentin'], function () {

    // IMPORTANT
    // All Prefixs Inside Admin Route Part Are Closely Connected With Table Aliases
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::group(['prefix' => 'posts'], function () {
            Route::group(['prefix' => 'crud'], function () {
                Route::get('/create_post', ['uses'=>'Admin\Posts\CrudController@create','as'=>'crudCreate']);
                Route::get('/delete_post', ['uses'=>'Admin\Posts\CrudController@delete','as'=>'crudDelete']);
                Route::get('/update_post', ['uses'=>'Admin\Posts\CrudController@update','as'=>'crudUpdate']);
            });
            Route::group(['prefix' => 'hashtag'], function () {
                Route::get('/create_hashtag', ['uses'=>'Admin\Posts\HashtagController@createHashtag_get','as'=>'hashtagCreateGet']);
                Route::post('/create_hashtag', ['uses'=>'Admin\Posts\HashtagController@createHashtag_post','as'=>'hashtagCreatePost']);

                Route::get('/edit_hashtag', ['uses'=>'Admin\Posts\HashtagController@editHashtag','as'=>'hashtagEdit']);
                Route::get('/attach_hashtag', ['uses'=>'Admin\Posts\HashtagController@attachHashtag','as'=>'hashtagAttach']);
                Route::get('/delete_hashtag', ['uses'=>'Admin\Posts\HashtagController@deleteHashtag','as'=>'hashtagDelete']);
            });
        });
        Route::group(['prefix' => 'categories'], function () {
            Route::group(['prefix' => 'categories'], function () {
                Route::get('/create_category', ['uses'=>'Admin\Categories\CategoriesController@createCategory_get','as'=>'categoriesCreateGet']);
                Route::post('/create_category', ['uses'=>'Admin\Categories\CategoriesController@createCategory_post','as'=>'categoriesCreatePost']);

                Route::get('/delete_category', ['uses'=>'Admin\Categories\CategoriesController@deleteCategory_get','as'=>'categoriesDeleteGet']);
                Route::post('/delete_category', ['uses'=>'Admin\Categories\CategoriesController@deleteCategory_post','as'=>'categoriesDeletePost']);

                Route::get('/change', ['uses'=>'Admin\Categories\CategoriesController@change','as'=>'categoriesChange']);
            });
            Route::group(['prefix' => 'subcategories'], function () {
                Route::get('/create_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@createSubcategory_get','as'=>'subcategoriesCreateGet']);
                Route::post('/create_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@createSubcategory_post','as'=>'subcategoriesCreatePost']);

                Route::get('/delete_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@deleteSubcategory_get','as'=>'subcategoriesDeleteGet']);
                Route::post('/delete_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@deleteSubcategory_post','as'=>'subcategoriesDeletePost']);

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
