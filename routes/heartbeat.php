<?php declare(strict_types=1);

use Laravel\Lumen\Routing\Router;

/*
|--------------------------------------------------------------------------
| Application Heartbeat Route
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for documentation.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/** @var Router $router */

$router->get('status', [
    'as' => 'heartbeat'
    , function () { // cannot use static closure
        return response()->json(['application' => env('APP_NAME'), 'environment' => env('APP_ENV')]);
    }
]);