<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => '', 'middleware' => 'App\Http\Middleware\CorsMiddleware::class'], function () use ($router) {
    $router->post('screens',  ['uses' => 'SeatInfoController@create']);
    $router->post('screens/{screen_name}/reserve', ['uses' => 'SeatInfoController@reserve']);
    $router->get('screens/{screen_name}/seats', ['uses' => 'SeatInfoController@getReq']);
});
