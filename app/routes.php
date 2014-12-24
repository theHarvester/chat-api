<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array( "as"=>"home",  function()
{
    if(Auth::check()) {
        return View::make('chat');
    } else {
        return View::make('index');
    }
}));

Route::get('/test', function()
{
    return Response::json(
        array(
            'result' => 'success',
            'conversations' => 'something'
        )
    );
});

// Route group for API versioning
Route::group(array('prefix' => 'api', 'before' => 'auth.basic'), function()
{
    Route::get('auth', function()
    {
        return Response::json(
            array(
                'result' => 'success'
            )
        );
    });

    Route::resource('handle', 'HandleController');
    Route::get('conversations', 'ConversationController@index');
    Route::get('conversation/{id}/from/{ts}', 'ConversationController@showAfter');
    Route::resource('conversation', 'ConversationController');
    Route::resource('message', 'MessageController');
});

// Authentication
Route::group(array('prefix' => 'account', 'before' => 'auth.guest'), function()
{
    Route::get('register', 'AuthController@getRegister');
    Route::get('login', 'AuthController@getLogin');

    Route::post('create', 'AuthController@doRegister');
    Route::post('authenticate', 'AuthController@doLogin');
});

// Authentication
Route::group(array('prefix' => 'account', 'before' => 'auth.basic'), function()
{
    Route::get('logout', 'AuthController@doLogout');
});
