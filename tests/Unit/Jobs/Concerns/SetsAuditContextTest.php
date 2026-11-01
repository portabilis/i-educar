<?php

use App\Jobs\Concerns\SetsAuditContext;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('leva para a conexão o usuário e a origem guardados no contexto', function () {
    $job = new class
    {
        use SetsAuditContext;

        public function run(): void
        {
            $this->setAuditContextFromLaravelContext();
        }
    };

    config(['audit.enabled' => true]);
    Context::add('audit_user_id', 7);
    Context::add('audit_user_name', "Ana D'Ávila");

    $job->run();

    expect(json_decode(DB::selectOne("select current_setting('audit.context') as context")->context, true))
        ->toBe(['user_id' => 7, 'user_name' => "Ana D'Ávila", 'origin' => 'queue']);
});
