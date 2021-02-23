<?php declare(strict_types=1);

namespace App\Models\Repositories;

use App\Models\ApiToken;
use App\Models\Repositories\Contracts\ApiTokenRepository as ApiTokenRepositoryContract;
use RuntimeException;

class ApiTokenRepository implements ApiTokenRepositoryContract
{
    public function validateToken(string $token, string $route): void
    {
        $found = $this->findByToken($token);

        if ($found === null) {
            throw new RuntimeException('Token not found', 401);
        }

        if (!$found->is_active) {
            throw new RuntimeException('Deactivated token', 401);
        }

        if (!in_array($route, $found->scopes, true)) {
            throw new RuntimeException('Unauthorized route', 403);
        }
    }

    public function findByToken(string $token): ?ApiToken
    {
        return ApiToken::query()->where('token', '=', $token)->first();
    }

    public function findByNameAndTarget(string $name, string $target): ApiToken
    {
        return ApiToken::query()->where([
            ['name', '=', $name],
            ['target', '=', $target],
        ])->firstOrFail();
    }

    public function create(string $name, string $target, array $scopes): ApiToken
    {
        $this->validateScopes($scopes);
        $length = config('auth.token.bearer.length');

        return ApiToken::create([
            'name' => $name,
            'target' => $target,
            'token' => app('encrypter')->encrypt([bin2hex(random_bytes($length))]),
            'scopes' => $scopes
        ]);
    }

    private function validateScopes(array $scopes): void
    {
        if (empty($scopes)) {
            throw new RuntimeException('Missing scopes');
        }

        foreach ($scopes as $scope) {
            route($scope); // check if there's an existing route for every scope
        }
    }

    public function deactivate(ApiToken $token): void
    {
        $token->is_active = false;
        $token->save();
    }
}
