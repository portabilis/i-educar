<?php

use App\Models\PersonHasPlace;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('não grava endereço para pessoa que não existe', function () {
    $screen = new class
    {
        use LegacyAddressingFields;
    };

    (fn () => $this->saveAddress(0))->call($screen);

    expect(PersonHasPlace::query()->where('person_id', 0)->exists())->toBeFalse();
});
