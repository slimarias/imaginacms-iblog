<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/iblog'], function (Router $router) {

    $router->post('post/upload/image', [
        'as' => 'iblog.gallery.upload',
        'uses' => 'PostController@uploadGalleryimage',
    ]);
    $router->post('post/delete/img', [
        'as' => 'iblog.gallery.delete',
        'uses' => 'PostController@deleteGalleryimage',
    ]);
    $router->group(['prefix' => 'posts'], function (Router $router) {
        $router->bind('post', function ($id) {
            return app('Modules\Iblog\Repositories\PostRepository')->find($id);
        });
        $router->get('/', [
            'as' => 'admin.iblog.post.index',
            'uses' => 'PostController@index',
            'middleware' => 'can:iblog.posts.index'
        ]);
        $router->get('create', [
            // dd('hgh'),
            'as' => 'admin.iblog.post.create',
            'uses' => 'PostController@create',
            'middleware' => 'can:iblog.posts.create'
        ]);
        $router->post('/', [
            'as' => 'admin.iblog.post.store',
            'uses' => 'PostController@store',
            'middleware' => 'can:iblog.posts.create'
        ]);
        $router->get('{post}/edit', [
            'as' => 'admin.iblog.post.edit',
            'uses' => 'PostController@edit',
            'middleware' => 'can:iblog.posts.edit'
        ]);
        $router->put('{post}', [
            'as' => 'admin.iblog.post.update',
            'uses' => 'PostController@update',
            'middleware' => 'can:iblog.posts.edit'
        ]);
        $router->delete('{post}', [
            'as' => 'admin.iblog.post.destroy',
            'uses' => 'PostController@destroy',
            'middleware' => 'can:iblog.posts.destroy'
        ]);
        $router->post('gallery', [
            'as' => 'iblog.posts.gallery.store',
            'uses' => 'PostController@galleryStore',
            //'middleware' => ['api.token','token-can:iblog.posts.create']
        ]);
        $router->post('gallery/delete', [
            'as' => 'iblog.posts.gallery.delete',
            'uses' => 'PostController@galleryDelete',
            // 'middleware' => ['api.token','token-can:iblog.posts.create']
        ]);
    });

    $router->group(['prefix' => 'categories'], function (Router $router) {
        $router->bind('category', function ($id) {
            return app('Modules\Iblog\Repositories\CategoryRepository')->find($id);
        });
        $router->get('/', [
            'as' => 'admin.iblog.category.index',
            'uses' => 'CategoryController@index',
            'middleware' => 'can:iblog.categories.index'
        ]);
        $router->get('create', [
            'as' => 'admin.iblog.category.create',
            'uses' => 'CategoryController@create',
            'middleware' => 'can:iblog.categories.create'
        ]);
        $router->post('', [
            'as' => 'admin.iblog.category.store',
            'uses' => 'CategoryController@store',
            'middleware' => 'can:iblog.categories.create'
        ]);
        $router->get('{category}/edit', [
            'as' => 'admin.iblog.category.edit',
            'uses' => 'CategoryController@edit',
            'middleware' => 'can:iblog.categories.edit'
        ]);
        $router->put('{category}', [
            'as' => 'admin.iblog.category.update',
            'uses' => 'CategoryController@update',
            'middleware' => 'can:iblog.categories.edit'
        ]);
        $router->delete('{category}', [
            'as' => 'admin.iblog.category.destroy',
            'uses' => 'CategoryController@destroy',
            'middleware' => 'can:iblog.categories.destroy'
        ]);
    });
});

