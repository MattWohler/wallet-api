<?php declare(strict_types=1);

namespace App\Services\Auth;

use App\Exceptions\Handled\AuthException;
use App\Models\Repositories\Contracts\ApiTokenRepository;
use Exception;
use Illuminate\Auth\GenericUser;

class TokenAuth
{
    /** @var ApiTokenRepository */
    protected $repository;

    public function __construct(ApiTokenRepository $repository)
    {
        $this->repository = $repository;
    }

    public function authenticate(string $token, string $route = ''): ?GenericUser
    {
        try {
            $this->repository->validateToken($token, $route);
        } catch (Exception $e) {
            throw new AuthException($e->getMessage(), $e->getCode(), $e);
        }

        return new GenericUser(['id' => $token, 'token' => $token]);
    }
}
