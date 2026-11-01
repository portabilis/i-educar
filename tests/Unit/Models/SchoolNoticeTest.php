<?php

namespace Tests\Unit\Models;

use App\Models\SchoolNotice;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('aceita instituicao e escola com codigo acima do limite de smallint', function () {
    $institution = LegacyInstitutionFactory::new()->create(['cod_instituicao' => 2_000_000_000]);
    $school = LegacySchoolFactory::new()->create([
        'cod_escola' => 2_000_000_000,
        'ref_cod_instituicao' => $institution,
    ]);

    $notice = SchoolNotice::create([
        'institution_id' => $institution->getKey(),
        'user_id' => LegacyUserFactory::new()->create()->getKey(),
        'school_id' => $school->getKey(),
        'title' => 'Reunião de pais',
        'description' => 'Entrega de boletins',
        'date' => '2026-10-02',
        'hour' => '19:00',
        'local' => 'Auditório',
    ]);

    expect($notice->refresh())
        ->institution_id->toBe(2_000_000_000)
        ->school_id->toBe(2_000_000_000)
        ->and($notice->institution->is($institution))->toBeTrue()
        ->and($notice->school->is($school))->toBeTrue();
});
