<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Laravel\Lumen\Application;

$app->get(
    '/',
    function (Application $app) {
        return $app->welcome();
    }
);

$app->group(
    [
        'prefix' => 'hooks',
        'middleware' => 'hooks'
    ],
    function (Application $app) {
        $app->post('customers', 'App\Http\Controllers\HooksController@customers');
        $app->post('orders', 'App\Http\Controllers\HooksController@orders');
        $app->get('unregister', 'App\Http\Controllers\HooksController@unregister');
        $app->get('register', 'App\Http\Controllers\HooksController@register');
    }
);
