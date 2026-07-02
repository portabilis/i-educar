<?php

namespace Tests\Unit\Services;

use App\Services\DisableUsersWithDaysGoneSinceLastAccessService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository;
use Mockery;
use Tests\TestCase;

class DisableUsersWithDaysGoneSinceLastAccessServiceTest extends TestCase
{
    protected function createService()
    {
        $config = Mockery::mock(Repository::class);

        $config->shouldReceive('get')
            ->andReturn(45);

        return new DisableUsersWithDaysGoneSinceLastAccessService($config);
    }

    protected function createUser(bool $isAdmin, int $daysGone)
    {
        $user = Mockery::mock(Authenticatable::class);

        $user->disabled = false;

        $user->shouldReceive('isAdmin')
            ->andReturn($isAdmin);

        $user->shouldReceive('getDaysSinceLastAccessOrEnabledUserDate')
            ->andReturn($daysGone);

        $user->shouldReceive('disable')
            ->andReturnUsing(function () use ($user) {
                $user->disabled = true;
            });

        return $user;
    }

    // CT01
    public function test_nao_desabilita_usuario_com_44_dias_sem_acesso()
    {
        $service = $this->createService();
        $user = $this->createUser(false, 44);

        $service->execute($user);

        $this->assertFalse($user->disabled);
    }

    // CT02
    public function test_desabilita_usuario_com_45_dias_sem_acesso()
    {
        $service = $this->createService();
        $user = $this->createUser(false, 45);

        $service->execute($user);

        $this->assertTrue($user->disabled);
    }

    // CT03
    public function test_desabilita_usuario_com_46_dias_sem_acesso()
    {
        $service = $this->createService();
        $user = $this->createUser(false, 46);

        $service->execute($user);

        $this->assertTrue($user->disabled);
    }

    // CT04
    public function test_nao_desabilita_administrador_com_45_dias_sem_acesso()
    {
        $service = $this->createService();
        $user = $this->createUser(true, 45);

        $service->execute($user);

        $this->assertFalse($user->disabled);
    }

    // CT05
    public function test_nao_desabilita_administrador_com_44_dias_sem_acesso()
    {
        $service = $this->createService();
        $user = $this->createUser(true, 44);

        $service->execute($user);

        $this->assertFalse($user->disabled);
    }

    // CT06
    public function test_nao_desabilita_usuario_com_zero_dias_sem_acesso()
    {
        $service = $this->createService();
        $user = $this->createUser(false, 0);

        $service->execute($user);

        $this->assertFalse($user->disabled);
    }

    // CT07
    public function test_nao_desabilita_usuario_com_dias_negativos()
    {
        $service = $this->createService();
        $user = $this->createUser(false, -1);

        $service->execute($user);

        $this->assertFalse($user->disabled);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
