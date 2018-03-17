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

Route::get('/', function () {
    return redirect('/tasks');
});

Route::get('/tasks','TaskController@list');

Auth::routes();

Route::get('/home', 'TaskController@list');
Route::get('/tasks/{id}/{beforeId}','TaskController@relocate');
Route::post('/tasks','TaskController@create');
Route::delete('/tasks/{id}','TaskController@destroy');
Route::patch('/tasks/{id}','TaskController@markAsDone');
Route::put('/tasks','TaskController@relocate');
