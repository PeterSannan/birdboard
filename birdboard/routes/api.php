<?php
use Illuminate\Support\Facades\Route;

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


Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('projects', 'ProjectsController@store');
    Route::get('projects', 'ProjectsController@index');
    Route::get('projects/{project}', 'ProjectsController@show');
    Route::patch('projects/{project}', 'ProjectsController@update');
    Route::delete('projects/{project}', 'ProjectsController@destroy');

    Route::post('/projects/{project}/tasks', 'ProjectTasksController@store');
    Route::patch('/projects/{project}/tasks/{task}', 'ProjectTasksController@update');

    Route::post('/userimage', 'ProjectsController@addimage');

    Route::post('projects/{project}/invite', 'ProjectInvitationsController@store');
});


