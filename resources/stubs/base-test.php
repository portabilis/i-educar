<?php declare(strict_types=1);

namespace Tests\Feature\Intranet;

use App\User;
use Tests\TestCase;

class BaseTestName extends TestCase
{
    /**
     * Route.
     *
     * @var string
     */
    public const ROUTE = 'ProjectRoute';

    public function testSuccessResponse()
    {

        $this->actingAs(User::query()->first());

        $this
            ->get(self::ROUTE)
            ->assertSuccessful()
        ;
    }
}
