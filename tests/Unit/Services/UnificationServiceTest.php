<?php

use App\Models\LogUnificationOldData;
use App\Services\UnificationService;
use Database\Factories\LogUnificationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('stores old data as json and returns the persisted log', function () {
    $unification = LogUnificationFactory::new()->create();
    $keys = ['cod_aluno' => 123];
    $oldData = ['ativo' => 1, 'codigo_sistema' => 'EXT-1'];

    $log = (new UnificationService)->storeLogOldData(
        $unification->getKey(),
        'pmieducar.aluno',
        $keys,
        $oldData,
    );

    $this->assertInstanceOf(LogUnificationOldData::class, $log);
    $this->assertNotNull($log->getKey());
    $this->assertSame($unification->getKey(), $log->unification_id);
    $this->assertSame('pmieducar.aluno', $log->table);
    $this->assertSame([$keys], $log->keys);
    $this->assertSame($oldData, $log->old_data);

    $persisted = DB::table('log_unification_old_data')->find($log->getKey());

    $this->assertNotNull($persisted);
    $this->assertSame($unification->getKey(), $persisted->unification_id);
    $this->assertSame('pmieducar.aluno', $persisted->table);
    $this->assertSame([$keys], json_decode($persisted->keys, true));
    $this->assertSame($oldData, json_decode($persisted->old_data, true));
});
