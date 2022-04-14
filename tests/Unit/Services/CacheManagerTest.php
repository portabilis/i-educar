<?php

namespace Tests\Unit\Services;

use App\Services\CacheManager;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheManagerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Cache::swap(new CacheManager(app()));
        Cache::flush();
    }

    public function testFlushedTagsShouldReturnsEmpty()
    {
        Cache::tags(['testTag'])->put('test-key', 'Test value', 10);

        Cache::invalidateByTags(['testTag']);

        $this->assertFalse(Cache::has('test-key'));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testDriverNotSupportTagsDoesNotThrowException()
    {
        Cache::tags(['testTag'])->put('test-key', 'Test value', 10);

        Cache::invalidateByTags(['testTag']);
    }

    public function testDriverSupportsPrefixDoesNotThrowException()
    {
        $this->assertFalse(Cache::has('test-key'));

        Cache::invalidateByTags(['testTag']);
    }
}
