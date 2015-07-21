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

use App\Http\Controllers\HooksController;
use App\Robin\Connect;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Application;
use Robin\Api\Robin;
use Robin\Connect\SEOShop\SEOShop;


$app->get(
    '/',
    function (Connect $connect) {
        $health = $connect->healthCheck();
        return view('home', compact("health"));
    }
);

$app->group(
    [
        'prefix' => 'hooks',
        'middleware' => 'hooks'
    ],
    function (Application $app) {
        $app->post('customers', HooksController::class . '@customers');
        $app->post('orders', HooksController::class . '@orders');
    }
);

$app->group(
    [
        'prefix' => 'hooks'
    ],
    function (Application $app) {
        $app->get('/', HooksController::class . '@index');
        $app->get('on', ['as' => 'hooks.on', 'uses' => HooksController::class . '@on']);
        $app->get('off', ['as' => 'hooks.off', 'uses' => HooksController::class . '@off']);
    }
);
