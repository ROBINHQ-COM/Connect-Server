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

use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Application;
use Robin\Api\Robin;
use Robin\Connect\SEOShop\SEOShop;


$app->get(
    '/',
    function (Application $app, SEOShop $SEOShop, Robin $robin) {
        $hooksCount = $SEOShop->count("hooks");
//        $hooks = $SEOShop->hooks()
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
    }
);

$app->get('hooks/unregister', 'App\Http\Controllers\HooksController@unregister');
$app->get('hooks/register', 'App\Http\Controllers\HooksController@register');
