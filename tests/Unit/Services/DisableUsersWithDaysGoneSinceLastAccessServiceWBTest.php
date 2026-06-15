<?php

namespace Tests\Unit\Services;

use App\Services\DisableUsersWithDaysGoneSinceLastAccessService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository;
use Mockery;
use Tests\TestCase;

class DisableUsersWithDaysGoneSinceLastAccessServiceWBTest extends TestCase
{
    protected function createService($expirationPeriod)
    {
        $config = Mockery::mock(Repository::class);

        $config->shouldReceive('get')
            ->andReturn($expirationPeriod);

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

    // CT01 - expirationPeriod vazio
    public function test_nao_desabilita_quando_expiration_period_esta_vazio()
    {
        $service = $this->createService(0);
        $user = $this->createUser(false, 100);

        $service->execute($user);

        $this->assertFalse($user->disabled);
    }

    // CT02 - expirationPeriod > daysGone
    public function test_nao_desabilita_quando_days_gone_e_menor_que_expiration_period()
    {
        $service = $this->createService(10);
        $user = $this->createUser(false, 1);

        $service->execute($user);

        $this->assertFalse($user->disabled);
    }

    // CT03 - expirationPeriod < daysGone
    public function test_desabilita_quando_days_gone_e_maior_que_expiration_period()
    {
        $service = $this->createService(10);
        $user = $this->createUser(false, 11);

        $service->execute($user);

        $this->assertTrue($user->disabled);
    }

    // CT04 - expirationPeriod == daysGone
    public function test_desabilita_quando_days_gone_e_igual_ao_expiration_period()
    {
        $service = $this->createService(10);
        $user = $this->createUser(false, 10);

        $service->execute($user);

        $this->assertTrue($user->disabled);
    }

    // CT05 - administrador
    public function test_nao_desabilita_usuario_administrador_ativo()
    {
        $service = $this->createService(30);
        $user = $this->createUser(true, 10);

        $service->execute($user);

        $this->assertFalse($user->disabled);
    }

    // CT06 - administrador com muitos dias sem acesso
    public function test_nao_desabilita_usuario_administrador_inativo()
    {
        $service = $this->createService(30);
        $user = $this->createUser(true, 45);

        $service->execute($user);

        $this->assertFalse($user->disabled);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}