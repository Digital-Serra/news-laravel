<?php
/**
 * @author    Mauri de Souza Nunes <mauri870@gmail.com>
 * @copyright Copyright (c) 2015, Mauri de Souza Nunes <github.com/mauri870>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

Route::group(['middleware' => ['auth', 'SetGlobalAuthVariables'],'namespace' => 'DigitalSerra\NewsLaravel\Http\Controllers','prefix'=>'news'], function () {
    Route::get('/', ['as'=>'news.index','uses'=>'NewsController@index']);
    Route::get('new/', ['as'=>'news.add','uses'=>'NewsController@add']);
    Route::post('new/', ['as'=>'news.post_add','uses'=>'NewsController@post_add']);
    Route::get('delete/{id}/', ['as'=>'news.delete','uses'=>'NewsController@delete']);
    Route::get('edit/{id}/', ['as'=>'news.edit','uses'=>'NewsController@edit']);
    Route::post('edit/{id}/', ['as'=>'news.post_edit','uses'=>'NewsController@post_edit']);
});