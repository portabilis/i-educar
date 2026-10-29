<?php

namespace Tests\Unit\Models;

use App\Models\LegacySchool;
use App\Models\SchoolNotice;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolNoticeFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('school notice define relacionamento belongsToMany com schools via factory', function () {
    $notice = SchoolNoticeFactory::new()->make();
    $relation = $notice->schools();

    expect($relation)->toBeInstanceOf(BelongsToMany::class)
        ->and($relation->getTable())->toBe('school_notice_schools')
        ->and($relation->getForeignPivotKeyName())->toBe('school_notice_id')
        ->and($relation->getRelatedPivotKeyName())->toBe('school_id')
        ->and($relation->getRelated())->toBeInstanceOf(LegacySchool::class);
});

test('school_id nao deve constar no fillable do comunicado', function () {
    $notice = SchoolNoticeFactory::new()->make();

    expect($notice->getFillable())->not->toContain('school_id')
        ->and($notice->getFillable())->toContain(
            'institution_id',
            'user_id',
            'title',
            'description',
            'date',
            'hour',
            'local'
        );
});

test('casts de data e hora estao devidamente configurados no model', function () {
    $notice = SchoolNoticeFactory::new()->make();

    expect($notice->getCasts())->toHaveKey('date', 'date')
        ->and($notice->getCasts())->toHaveKey('hour', 'datetime');
});

test('aceita instituicao e escola com codigo acima do limite de smallint', function () {
    $institution = LegacyInstitutionFactory::new()->create(['cod_instituicao' => 2_000_000_000]);
    $school = LegacySchoolFactory::new()->create([
        'cod_escola' => 2_000_000_000,
        'ref_cod_instituicao' => $institution,
    ]);

    $notice = SchoolNotice::create([
        'institution_id' => $institution->getKey(),
        'user_id' => LegacyUserFactory::new()->create()->getKey(),
        'title' => 'Reunião de pais',
        'description' => 'Entrega de boletins',
        'date' => '2026-10-02',
        'hour' => '19:00',
        'local' => 'Auditório',
    ]);

    $notice->schools()->attach($school);

    expect($notice->refresh())
        ->institution_id->toBe(2_000_000_000)
        ->and($notice->institution->is($institution))->toBeTrue()
        ->and($notice->schools->first()->is($school))->toBeTrue();
});
