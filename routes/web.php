<?php
Route::group(['prefix' => 'qwentin'], function () {

    // IMPORTANT
    // All Prefixs Inside Admin Route Part Are Closely Connected With Table Aliases
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::group(['prefix' => 'posts'], function () {
            Route::group(['prefix' => 'crud'], function () {
                Route::get('/create_post', ['uses'=>'Admin\Posts\CrudController@createPost_get','as'=>'postCreateGet']);
                Route::post('/create_post', ['uses'=>'Admin\Posts\CrudController@createPost_post','as'=>'postCreatePost']);

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

                Route::group(['prefix' => 'edit_category'], function () {
                    Route::post('/save', ['uses'=>'Admin\Categories\CategoriesController@editCategorySave_post','as'=>'categoriesEditSavePost']);
                    Route::get('/', ['uses'=>'Admin\Categories\CategoriesController@editCategory_get','as'=>'categoriesEditGet']);
                    Route::post('/', ['uses'=>'Admin\Categories\CategoriesController@editCategory_post','as'=>'categoriesEditPost']);
                });
            });
            Route::group(['prefix' => 'subcategories'], function () {
                Route::get('/create_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@createSubcategory_get','as'=>'subcategoriesCreateGet']);
                Route::post('/create_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@createSubcategory_post','as'=>'subcategoriesCreatePost']);

                Route::get('/delete_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@deleteSubcategory_get','as'=>'subcategoriesDeleteGet']);
                Route::post('/delete_subcategory', ['uses'=>'Admin\Categories\SubcategoriesController@deleteSubcategory_post','as'=>'subcategoriesDeletePost']);

                Route::group(['prefix' => 'edit_subcategory'], function () {
                    Route::post('/save', ['uses'=>'Admin\Categories\SubcategoriesController@editSubcategorySave_post','as'=>'subcategoriesEditSavePost']);
                    Route::get('/', ['uses'=>'Admin\Categories\SubcategoriesController@editSubcategory_get','as'=>'subcategoriesEditGet']);
                    Route::post('/', ['uses'=>'Admin\Categories\SubcategoriesController@editSubcategory_post','as'=>'subcategoriesEditPost']);
                });
            });
        });
    });
    Route::get('/{navbar}/{part}', ['uses'=>'AdminController@part','as'=>'adminNavbarPart']);
    Route::get('/login', ['uses'=>'Auth\AdminLoginController@showLoginForm','as'=>'adminLoginForm']);
    Route::post('/login', ['uses'=>'Auth\AdminLoginController@login','as'=>'adminLoginPost']);
    Route::get('/', ['uses'=>'AdminController@index','as'=>'adminDashboard']);
});


Route::get('/', ['uses'=>'CategoryController@getCategory','as'=>'category']);
Route::get('/home', ['uses'=>'CategoryController@getCategory','as'=>'category'])->where('home', 'home');

Route::get('/hashtag/{alias}', ['uses'=>'SubcategoryController@getByHashtag','as'=>'byHashtag']);

Route::get('/{category}/{subcategory}', ['uses'=>'SubcategoryController@getSubCategory','as'=>'subcategory'])
        ->where(['category' => '^[a-zA-Z0-9_]*$', 'subcategory' => '^[a-zA-Z0-9_]*$']);

Route::get('/{category}/{subcategory}/{post}', ['uses'=>'PostController@getPost','as'=>'post'])
        ->where(['category' => '^[a-zA-Z0-9_]*$', 'subcategory' => '^[a-zA-Z0-9_]*$', 'post' => '^[a-zA-Z0-9_]*$']);

Auth::routes();


// todo all '/' to DIRECTORY_SEPARATOR
// ToDo post edit + make changes in directoryEditor
// ToDO Atach Detach Hashtag
// ToDo Attach/Detach Subcategory to Category
// ToDO Attach/Detach Post to Subcategory
// Todo numbers under text, which will show best text long
// todo testIt part for all
//