<?php declare(strict_types=1);

namespace App\Providers;

use Elastica\Client as ElasticsearchClient;
use Illuminate\Support\ServiceProvider;

class ElasticServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ElasticsearchClient::class, static function () {
            return new ElasticsearchClient(config('elasticsearch'));
        });
    }
}
