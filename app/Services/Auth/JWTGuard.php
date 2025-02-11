<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Entities\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use InvalidArgumentException;

class JWTGuard implements Guard
{
    protected UserProvider $provider;

    private JwtProvider $jwtProvider;

    protected ?Authenticatable $user;

    private array $userClaims;

    private Dispatcher $eventsDispatcher;

    public function __construct(UserProvider $provider, JwtProvider $jwtProvider, Dispatcher $events)
    {
        $this->provider = $provider;
        $this->jwtProvider = $jwtProvider;
        $this->user = null;
        $this->userClaims = [];
        $this->eventsDispatcher = $events;
    }

    public function attemptJwt(Request $request)
    {
        // if we logged in into "JWT guard" from test environment
        if (ENVIRONMENT === 'testing' && !$this->guest() && !$request->hasHeader('Authorization')) {
            return true;
        }

        if (!$request->hasHeader('Authorization')) {
            return false;
        }

        $authHeader = $request->header('Authorization');
        if (empty($authHeader) || !is_string($authHeader)) {
            return false;
        }

        if (!Str::startsWith($authHeader, 'Bearer')) {
            return false;
        }

        $jwtKey = str_replace('Bearer ', '', $authHeader);

        if (!$this->validate(['jwtKey' => $jwtKey])) {
            return false;
        }

        if (!array_key_exists('sub', $this->userClaims)) {
            return false;
        }

        $user = $this->provider->retrieveById($this->userClaims['sub']);
        if (!$user instanceof User) {
            return false;
        }

        $issuedAt = $this->userClaims['iat'];
        $passwordChanged = $user->getPasswordChangedAt();
        if ($passwordChanged && $passwordChanged >= $issuedAt) {
            return false;
        }

        $this->setUser($user);

        return true;
    }

    public function attemptCredentials(Request $request): bool
    {
        $credentials = $request->all(['handle', 'username', 'password', 'gcode']);
        $credentials['ip'] = $request->ip();
        $user = $this->provider->retrieveByCredentials($credentials);
        if (!$user instanceof Authenticatable) {
            return false;
        }

        if (!$this->provider->validateCredentials($user, $credentials)) {
            $this->fireFailedEvent($user, $credentials);

            return false;
        }

        $this->setUser($user);

        return true;
    }

    public function check(): bool
    {
        return null !== $this->user();
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function user(): ?Authenticatable
    {
        return $this->user;
    }

    public function id(): ?int
    {
        $user = $this->user();
        if ($user) {
            return $user->getAuthIdentifier();
        }

        return null;
    }

    public function validate(array $credentials = []): bool
    {
        if (empty($credentials['jwtKey'])) {
            return false;
        }

        try {
            $this->userClaims = $this->jwtProvider->parseJwtKey($credentials['jwtKey']);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    public function setUser(Authenticatable $user): JWTGuard
    {
        $this->fireLoginEvent($user);
        $this->user = $user;

        return $this;
    }

    protected function fireLoginEvent(Authenticatable $user): void
    {
        $this->eventsDispatcher->dispatch(new Login(
            self::class,
            $user,
            false,
        ));
    }

    protected function fireFailedEvent(Authenticatable $user, array $credentials): void
    {
        $this->eventsDispatcher->dispatch(new Failed(
            self::class,
            $user,
            $credentials,
        ));
    }

    public function hasUser(): bool
    {
        return $this->user !== null;
    }
}
