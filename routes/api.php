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

Route::middleware('auth:api')->get('/user', function (Request $request) {
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

Route::get('articles', 'ArticlesController@index');
Route::get('articles/{article}', 'ArticlesController@show');
Route::post('articles', 'ArticlesController@store');
Route::put('articles/{article}', 'ArticlesController@update');
Route::delete('articles/{article}', 'ArticlesController@delete');
