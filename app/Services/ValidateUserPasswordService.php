<?php

namespace App\Services;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ValidateUserPasswordService
{
    public const MIN_LENGTH_PASSWORD = 8;

    private $hash;

    public function __construct(Hasher $hash)
    {
        $this->hash = $hash;
    }

    public function execute(string $newPassword, string $oldPassword = null)
    {
        try {
            $this->validate($newPassword, $oldPassword);
        } catch (ValidationException $ex) {
            throw ValidationException::withMessages([
                'password' => [
                    'A senha deve conter pelo menos ' .
                    self::MIN_LENGTH_PASSWORD .
                    ' caracteres e uma combinação de letras maiúsculas e minúsculas, números e símbolos (!@#$%*).'
                ]
            ]);
        } catch (\Exception $ex) {
            throw ValidationException::withMessages([
                'password' => $ex->getMessage()
            ]);
        }
    }

    public function validate(string $newPassword, $oldPassword = null)
    {
        if ($this->hash->check($newPassword, $oldPassword)) {
            throw new \Exception('A senha informada foi usada recentemente. Por favor, escolha outra.');
        }

        validator(
            ['password' => $newPassword],
            [
                'password' => [
                    Password::min(self::MIN_LENGTH_PASSWORD)
                        ->mixedCase()
                        ->letters()
                        ->numbers()
                        ->symbols()
                ]
            ]
        )->validate();
    }
}
