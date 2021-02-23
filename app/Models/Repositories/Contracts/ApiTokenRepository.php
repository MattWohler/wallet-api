<?php declare(strict_types=1);

namespace App\Models\Repositories\Contracts;

use App\Models\ApiToken;

interface ApiTokenRepository
{
    /**
     * Validate the authorization with a token
     *
     * @param  string  $token
     * @param  string  $route
     */
    public function validateToken(string $token, string $route): void;

    /**
     * Find by token
     *
     * @param  string  $token
     * @return ApiToken|null
     */
    public function findByToken(string $token): ?ApiToken;

    /**
     * Find by name and target
     *
     * @param  string  $name
     * @param  string  $target
     * @return ApiToken
     */
    public function findByNameAndTarget(string $name, string $target): ApiToken;

    /**
     * Create an api token
     *
     * @param  string  $name
     * @param  string  $target
     * @param  array  $scopes
     * @return ApiToken
     */
    public function create(string $name, string $target, array $scopes): ApiToken;

    /**
     * Deactivate a token
     *
     * @param  ApiToken  $token
     */
    public function deactivate(ApiToken $token): void;
}
