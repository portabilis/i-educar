<?php

namespace App\Extensions;

use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Validation\ValidationException;
use Throwable;

class LegacyUserProvider implements UserProvider
{
    /**
     * The hasher implementation.
     *
     * @var Hasher
     */
    protected $hasher;

    /**
     * Create a new database user provider.
     *
     * @param Hasher $hasher
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @inheritdoc
     */
    public function retrieveById($identifier)
    {
        return User::query()->find($identifier);
    }

    /**
     * @inheritdoc
     */
    public function retrieveByToken($identifier, $token)
    {
        $user = $this->retrieveById($identifier);

        return $user && $user->getRememberToken() && hash_equals($user->getRememberToken(), $token)
            ? $user : null;
    }

    /**
     * @inheritdoc
     *
     * @throws Throwable
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $user->saveOrFail();
    }

    /**
     * @inheritdoc
     */
    public function retrieveByCredentials(array $credentials)
    {
        return User::query()->whereHas('employee', function ($query) use ($credentials) {
            $query->where('matricula', $credentials['login']);
        })->first();
    }

    /**
     * @inheritdoc
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if ($user->isInactive()) {
            throw ValidationException::withMessages([
                $user->login => __('auth.inactive')
            ]);
        }

        $pass = $this->hasher->check(
            $credentials['password'],
            $user->getAuthPassword()
        );

        if (empty($pass)) {
            return $this->validateLegacyCredentials($user, $credentials);
        }

        return true;
    }

    /**
     * Validate legacy credentials and rehash if correct.
     *
     * @param User  $user
     * @param array $credentials
     *
     * @return bool
     */
    public function validateLegacyCredentials(User $user, array $credentials)
    {
        $plain = $credentials['password'];

        if (md5($plain) !== $user->getAuthPassword()) {
            return false;
        }

        $this->rehashPassword($user, $plain);

        return true;
    }

    /**
     * Rehash a legacy password that uses MD5.
     *
     * @param User   $user
     * @param string $password
     *
     * @return void
     */
    public function rehashPassword(User $user, $password)
    {
        $user->employee->password = $this->hasher->make($password);
        $user->employee->save();
    }
}
