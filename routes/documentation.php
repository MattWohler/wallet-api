<?php declare(strict_types=1);

use Laravel\Lumen\Routing\Router;

/*
|--------------------------------------------------------------------------
| Application Documentation Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for documentation.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/** @var Router $router */

$router->group(['namespace' => 'Docs'], static function (Router $router) {

    $router->get('/', [
        'as' => 'doc-swagger-ui',
        'uses' => 'SwaggerController@getSwaggerUi'
    ]);

    $router->get('/doc', [
        'as' => 'doc-swagger-content',
        'uses' => 'SwaggerController@getDocumentation'
    ]);

    $router->group(['prefix' => 'v1'], static function () use ($router) {
        $router->get('/swagger.json', [
            'as' => 'doc-swagger-json',
            'uses' => 'SwaggerController@getSwaggerJson'
        ]);
    });
});
