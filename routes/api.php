<?php

use App\Article;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')
    ->get('/user', function (Request $request) {
        return $request->user();
    });

//Route::get('articles', function () {
//    return Article::all();
//});
//
//Route::get('articles/{id}', function ($id) {
//    return Article::find($id);
//});
//
//Route::post('articles', function (Request $request) {
//    return Article::create($request->all());
//});
//
//Route::put('articles/{id}', function (Request $request, $id) {
//    $article = Article::findOrFail($id);
//    $article->update($request->all());
//
//    return $article;
//});
//
//Route::delete('articles/{id}', function ($id) {
//    Article::find($id)->delete();
//
//    return 204;
//});

// For articles
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('articles', 'ArticlesController@index');
    Route::any('articles/{article}', 'ArticlesController@show');
    Route::post('articles', 'ArticlesController@store');
    Route::put('articles/{article}', 'ArticlesController@update');
    Route::delete('articles/{article}', 'ArticlesController@delete');
});

//Route::get('articles', 'ArticlesController@index');
//Route::get('articles/{article}', 'ArticlesController@show');
//Route::post('articles', 'ArticlesController@store');
//Route::put('articles/{article}', 'ArticlesController@update');
//Route::delete('articles/{article}', 'ArticlesController@delete');

// For users
Route::post('register', 'Auth\RegisterController@register');
Route::any('login', 'Auth\LoginController@login')->name('login');
Route::post('logout', 'Auth\LoginController@logout');
Route::any('genToken', 'Auth\RegisterController@generateToken');
Route::any('delete-users/{id}', 'Auth\UserController@delete');
Route::post('register-alt', 'UserController@registerAlt');
Route::post('login-alt', 'UserController@loginAlt');

Route::group(array('middleware' => 'auth:api'), function () {
//    Route::resource(['users' => 'UserController']);
//    ->names([
//            'create' => 'users.create',
//            'store' => 'users.store',
//            'show' => 'users.show',
//            'edit' => 'users.edit',
//            'update' => 'users.update',
//            'destroy' => 'users.delete',
//        ]
//    );

    Route::get('active-users', 'UserController@activeUsers');
    Route::any('user-info/{id?}', 'UserController@userInfo');
    Route::any('profile', 'UserController@profile');
    Route::get('emails', 'UserController@emails');
    Route::any('sign-out/{id?}', 'UserController@logout');
    Route::any('user-posts/{uid?}', 'UserController@userPosts');
    Route::post('add-post/', 'UserController@addPost');
});
