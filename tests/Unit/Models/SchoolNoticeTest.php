<?php

namespace Tests\Unit\Models;

use App\Models\LegacySchool;
use Database\Factories\SchoolNoticeFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

uses(TestCase::class);

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

