<?php

Route::get('/', ['middleware' => 'locale', 'uses' => 'CategoryController@getCategory']);

Route::group(['prefix' => '{locale}', 'middleware' => 'locale'], function () {
    Route::group(['prefix' => 'qwentin'], function () {

        // IMPORTANT
        // All Prefixs Inside Admin Route Part Are Closely Connected With Table Aliases
        Route::group(['middleware' => 'auth:admin'], function () {
            Route::group(['prefix' => 'posts'], function () {
                Route::group(['prefix' => 'crud'], function () {
                    Route::get('/create_post', ['uses'=>'Admin\Posts\CrudController@createPost_get','as'=>'postCreateGet']);
                    Route::post('/create_post', ['uses'=>'Admin\Posts\CrudController@createPost_post','as'=>'postCreatePost']);

                    Route::group(['prefix' => 'update_post'], function () {
                        Route::group(['prefix' => '/main/{id}'], function () {
                            Route::get('/', ['uses'=>'Admin\Posts\CrudController@postMainDetails_get','as'=>'postMainDetailsGet'])->where(['id' => '^[0-9]*$']);
                            Route::post('/', ['uses'=>'Admin\Posts\CrudController@postMainDetails_post','as'=>'postMainDetailsPost'])->where(['id' => '^[0-9]*$']);
                            Route::group(['prefix' => 'parts'], function () {
                                Route::get('/', ['uses'=>'Admin\Posts\CrudController@postPartsDetails_get','as'=>'postPartsDetailsGet'])->where(['id' => '^[0-9]*$']);
                                Route::post('/', ['uses'=>'Admin\Posts\CrudController@postPartsDetails_post','as'=>'postPartsDetailsPost'])->where(['id' => '^[0-9]*$']);
                                Route::post('/delete', ['uses'=>'Admin\Posts\CrudController@postPartDelete_post','as'=>'postPartDeletePost'])->where(['id' => '^[0-9]*$']);
                                Route::post('/add-part', ['uses'=>'Admin\Posts\CrudController@postAddNewPart_post','as'=>'postAddNewParts'])->where(['id' => '^[0-9]*$']);
                            });
                        });
                        Route::get('/', ['uses'=>'Admin\Posts\CrudController@updatePost_get','as'=>'postEditGet']);
                        // this method also calls below
                        Route::post('/', ['uses'=>'Admin\Posts\CrudController@updatePost_post','as'=>'postEditPost']);
                        Route::post('/delete', ['uses'=>'Admin\Posts\CrudController@postDelete_post','as'=>'postDelete']);
                    });
                    // todo need to remove
//                    Route::group(['prefix' => 'attach_post_part'], function () {
//                        Route::get('/{id}', ['uses'=>'Admin\Posts\CrudController@postPartsAttach_get','as'=>'postPartsAttachGet'])->where(['id' => '^[0-9]*$']);
//                        // this method also calls above
//                        Route::post('/{id}', ['uses'=>'Admin\Posts\CrudController@updatePost_post','as'=>'postPartsAttachGetPostsPost'])->where(['id' => '^[0-9]*$']);
//                        Route::post('/{id}/save', ['uses'=>'Admin\Posts\CrudController@postPartsAttachSave_post','as'=>'postPartsAttachSavePost'])->where(['id' => '^[0-9]*$']);
//                    });
                });
                Route::group(['prefix' => 'hashtag'], function () {
                    Route::get('/create_hashtag', ['uses'=>'Admin\Posts\HashtagController@createHashtag_get','as'=>'hashtagCreateGet']);
                    Route::post('/create_hashtag', ['uses'=>'Admin\Posts\HashtagController@createHashtag_post','as'=>'hashtagCreatePost']);

                    Route::group(['prefix' => 'edit_hashtag'], function () {
                        Route::post('/save', ['uses'=>'Admin\Posts\HashtagController@editHashtagSave_post','as'=>'hashtagEditSavePost']);
                        Route::post('/delete', ['uses'=>'Admin\Posts\HashtagController@editHashtagDelete_post','as'=>'hashtagEditDeletePost']);
                        Route::get('/', ['uses'=>'Admin\Posts\HashtagController@editHashtag_get','as'=>'hashtagEditGet']);
                        Route::post('/', ['uses'=>'Admin\Posts\HashtagController@editHashtag_post','as'=>'hashtagEditPost']);
                    });

                    Route::get('/attach_hashtag', ['uses'=>'Admin\Posts\HashtagController@attachHashtag','as'=>'hashtagAttach']);
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


    Route::get('/', ['uses' => 'CategoryController@getCategory', 'as' => 'category']);

    Route::get('/home', ['uses'=>'CategoryController@getCategory','as'=>'category'])->where('home', 'home');

    Route::get('/hashtag/{alias}', ['uses'=>'HashtagController@getByHashtag','as'=>'byHashtag']);

    Route::get('/{category}/{subcategory}', ['uses'=>'SubcategoryController@getSubCategory','as'=>'subcategory'])
        ->where(['category' => '^[a-zA-Z0-9_]*$', 'subcategory' => '^[a-zA-Z0-9_]*$']);

    Route::get('/{category}/{subcategory}/{post}', ['uses'=>'PostController@getPost','as'=>'post'])
        ->where(['category' => '^[a-zA-Z0-9_]*$', 'subcategory' => '^[a-zA-Z0-9_]*$', 'post' => '^[a-zA-Z0-9_]*$']);

    Auth::routes();
});


// todo all validation should work as separate repositories
// todo language active status changer in admin part
// todo standardization the var names from front side and validation part standardization
// todo response prepare helper, standardize using it
// todo reread all front classes , make some corrections if needs
// todo not only db length, but also names should be in Settings
// todo user registration, password dont remember, reset, email sending
// todo users can comment under posts
// todo add main page slider capability, by default false , but configurable from Admin
// todo seo optimization , add alt's for images
// todo after post Part delete update number
// todo read yahoo front optimization and make them
// todo reread all methods
// todo testIt part for all
// ToDo Archive posts
// Todo numbers under text(DONE), but it doesn't work when we add dynamically