<?php

namespace Tests\Unit\Services;

use App\Services\CacheManager;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::swap(new CacheManager(app()));
        Cache::flush();
    }

    public function test_flushed_tags_should_returns_empty()
    {
        Cache::tags(['testTag'])->put('test-key', 'Test value', 10);

        Cache::invalidateByTags(['testTag']);

        $this->assertFalse(Cache::has('test-key'));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_driver_not_support_tags_does_not_throw_exception()
    {
        Cache::tags(['testTag'])->put('test-key', 'Test value', 10);

        Cache::invalidateByTags(['testTag']);
    }

    public function test_driver_supports_prefix_does_not_throw_exception()
    {
        $this->assertFalse(Cache::has('test-key'));

        Cache::invalidateByTags(['testTag']);
    }
}
