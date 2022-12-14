<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return redirect('/api/v1');
});

$router->group(['prefix' => '/api/v1'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return response()->json([
            'status' => 'success',
            'message' => 'API is running',
            'version' => '1.0.0'
        ], 200);
    });

        
    $router->group(['middleware' => ['auth']], function () use ($router) {
        $router->get('/institutions', 'InstitutionController@search');
    });
});
