<?php

namespace Tests\Feature\Commands;

use App\Mail\NewUserMail;
use App\Models\LegacyEmployee;
use App\Models\LegacyInstitution;
use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImportUsersCommandTest extends TestCase
{
    use DatabaseTransactions;

    #[DataProvider('forceResetPasswordProvider')]
    public function test_import_users_command_creates_user_and_sends_the_generated_password(bool $forceResetPassword): void
    {
        Mail::fake();

        $institution = LegacyInstitutionFactory::new()->create();
        $this->app->instance(LegacyInstitution::class, $institution);
        $this->ensureAdminUserTypeExists();

        $login = 'u' . substr(md5((string) microtime(true)), 0, 8);
        $email = $login . '@example.com';
        $filePath = $this->createImportFile([
            ['nome', 'sobrenome', 'email', 'matricula'],
            ['Ana', 'Silva', $email, $login],
        ]);

        try {
            $this->artisan('import:users', [
                'filename' => $filePath,
                '--force-reset-password' => $forceResetPassword,
            ])->assertExitCode(0);

            $employee = LegacyEmployee::query()->where('matricula', $login)->first();

            $this->assertNotNull($employee);
            $this->assertSame($email, $employee->email);
            $this->assertSame((int) $forceResetPassword, (int) $employee->force_reset_password);
            $this->assertSame('Ana Silva', $employee->person->nome);

            $user = LegacyUser::query()->find($employee->getKey());

            $this->assertNotNull($user);
            $this->assertSame($institution->getKey(), $user->ref_cod_instituicao);
            $this->assertSame(LegacyUserType::LEVEL_ADMIN, $user->ref_cod_tipo_usuario);

            Mail::assertSent(NewUserMail::class, function (NewUserMail $mail) use ($employee, $email, $login): bool {
                $this->assertTrue(Hash::check($mail->password, $employee->senha));

                return $mail->hasTo($email)
                    && $mail->username === $login
                    && $mail->name === 'Ana Silva';
            });
        } finally {
            @unlink($filePath);
        }
    }

    public static function forceResetPasswordProvider(): array
    {
        return [
            'keeps reset password optional' => [false],
            'forces password reset for imported users' => [true],
        ];
    }

    public function test_import_users_command_skips_existing_logins_without_sending_a_new_email(): void
    {
        Mail::fake();

        $institution = LegacyInstitutionFactory::new()->create();
        $this->app->instance(LegacyInstitution::class, $institution);
        $this->ensureAdminUserTypeExists();

        $existingEmployee = LegacyEmployeeFactory::new()->create([
            'matricula' => 'dup-login',
            'email' => 'existing@example.com',
        ]);

        $filePath = $this->createImportFile([
            ['nome', 'sobrenome', 'email', 'matricula'],
            ['Usuário', 'Existente', 'updated@example.com', 'dup-login'],
        ]);

        try {
            $this->artisan('import:users', [
                'filename' => $filePath,
            ])->assertExitCode(0);

            $this->assertSame(1, LegacyEmployee::query()->where('matricula', 'dup-login')->count());
            $this->assertSame('existing@example.com', $existingEmployee->fresh()->email);
            Mail::assertNothingSent();
        } finally {
            @unlink($filePath);
        }
    }

    private function ensureAdminUserTypeExists(): void
    {
        if (LegacyUserType::query()->find(LegacyUserType::LEVEL_ADMIN)) {
            return;
        }

        LegacyUserTypeFactory::new()->create([
            'cod_tipo_usuario' => LegacyUserType::LEVEL_ADMIN,
            'nivel' => LegacyUserType::LEVEL_ADMIN,
            'nm_tipo' => 'Administrador',
        ]);
    }

    private function createImportFile(array $rows): string
    {
        $filePath = tempnam(sys_get_temp_dir(), 'users-import-');

        $this->assertNotFalse($filePath);

        unlink($filePath);
        $filePath .= '.csv';

        $handle = fopen($filePath, 'wb');

        $this->assertNotFalse($handle);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $filePath;
    }
}
