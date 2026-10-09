<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class CommandsTest extends TestCase
{
    public function test_console_command_inspire()
    {
        $this->artisan('inspire')->assertExitCode(0);
    }

    public function test_console_command_admin_password()
    {
        $this->artisan('admin:password 12345678')->assertExitCode(0);
    }

    public function test_console_command_disable_users_with_access_expired()
    {
        $this->artisan('disable:users-with-access-expired')->assertExitCode(0);
    }

    public function test_console_command_legacy_create_tests()
    {
        // Os testes gerados ficam em tests/Pages/Intranet, fora do Git: o que falta é
        // criado, e rodar de novo encontra os arquivos que já existem.
        @unlink(base_path('tests/Pages/Intranet/TopicIndexTest.php'));

        $this->artisan('legacy:create:tests')
            ->expectsOutput('Teste TopicIndexTest criado para a rota /intranet/topic_index.php')
            ->assertExitCode(0);
        $this->artisan('legacy:create:tests')
            ->expectsOutput('Teste TopicIndexTest criado para a rota /intranet/topic_index.php já criado ou houve falha')
            ->assertExitCode(0);
    }

    public function test_console_command_notifications_delete()
    {
        $this->artisan('notifications:delete')->assertExitCode(0);
    }

    public function test_console_command_reset_password()
    {
        $this->artisan('reset:password 12345678')->assertExitCode(0);
    }

    public function test_console_command_update_school_class_period()
    {
        $this->artisan('update:school-class-period')->assertExitCode(0);
    }
}
