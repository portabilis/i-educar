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
        $this->artisan('legacy:create:tests')->assertExitCode(0);
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
