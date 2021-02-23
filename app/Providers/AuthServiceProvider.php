<?php declare(strict_types=1);

namespace App\Providers;

use App\Services\Auth\TokenAuth;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['auth']->viaRequest('api', function (Request $request) {
            $token = (string) $request->header('Authorization', '');

            if (strpos($token, 'Basic ') === 0) {
                $token = substr($token, 6);
            }

            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            if (!str_contains($route = $request->route()[1]['as'], ['doc', 'heartbeat'])) {
                return $this->app->make(TokenAuth::class)->authenticate($token, $route);
            }
        });
    }
}
