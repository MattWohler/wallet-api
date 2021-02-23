<?php declare(strict_types=1);

use Laravel\Lumen\Routing\Router;

/*
|--------------------------------------------------------------------------
| Application API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/** @var Router $router */

$router->group(['prefix' => 'api'], static function (Router $router) {
    $router->group(['prefix' => 'v1', 'middleware' => ['logger', 'auth:api']], static function () use ($router) {

        $router->get('/authenticate/{account}', [
            'as' => 'authenticate-account',
            'uses' => 'AuthController@authenticate',
        ]);

        $router->get('/player/{account}', [
            'as' => 'get-account-info',
            'uses' => 'AuthController@getPlayer',
        ]);

        $router->get('/player', [
            'as' => 'get-account-info-by-batch',
            'uses' => 'AuthController@getPlayers',
        ]);

        $router->get('/balance/{account}', [
            'as' => 'get-balance-by-account',
            'uses' => 'BalanceController@getBalance',
        ]);

        $router->get('/figure/{account}', [
            'as' => 'get-figure-by-account',
            'uses' => 'FigureController@getFigure',
        ]);

        $router->group(['middleware' => ['idem']], static function () use ($router) {
            $router->post('/transaction', [
                'as' => 'process-transaction',
                'uses' => 'TransactionController@process',
            ]);

            $router->post('/rollback', [
                'as' => 'process-rollback',
                'uses' => 'TransactionController@rollback',
            ]);
        });

        $router->group(['namespace' => 'Histories', 'prefix' => 'histories'], static function () use ($router) {
            $router->get('/transaction', [
                'as' => 'get-histories-transaction',
                'uses' => 'TransactionController@getTransactions',
            ]);
        });
    });
});
