<?php

use App\Services\CacheManager;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Cache::swap(new CacheManager(app()));
    Cache::flush();
});

test('flushed tags should returns empty', function () {
    Cache::tags(['testTag'])->put('test-key', 'Test value', 10);

    Cache::invalidateByTags(['testTag']);

    $this->assertFalse(Cache::has('test-key'));
});

test('driver not support tags does not throw exception', function () {
    $this->expectNotToPerformAssertions();

    Cache::tags(['testTag'])->put('test-key', 'Test value', 10);

    Cache::invalidateByTags(['testTag']);
});

test('driver supports prefix does not throw exception', function () {
    $this->assertFalse(Cache::has('test-key'));

    Cache::invalidateByTags(['testTag']);
});

test('store sem suporte a tags ignora as tags', function () {
    config(['cache.default' => 'file']);
    $manager = new CacheManager(app());

    $this->assertSame($manager->store(), $manager->tags(['qualquer']));
});

test('redis usa o apelido do cliente como prefixo', function () {
    config(['cache.default' => 'redis', 'app.nickname' => 'cliente-teste']);

    $this->assertSame('cliente-teste', (new CacheManager(app()))->getPrefix());
});
