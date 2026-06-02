<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\AnnouncementMiddleware;
use App\Models\Announcement;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AnnouncementMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_handle_caches_announcement_query_on_get_html_request()
    {
        $user = LegacyUserFactory::new()->create();
        $cacheKey = "announcement.user_type.{$user->ref_cod_tipo_usuario}";

        $announcement = Announcement::create([
            'name' => 'teste',
            'description' => 'teste',
            'show_confirmation' => false,
            'created_by_user_id' => $user->getKey(),
        ]);
        $announcement->userTypes()->sync([$user->ref_cod_tipo_usuario]);

        Cache::flush();
        $this->assertFalse(Cache::has($cacheKey));

        $request = Request::create('/intranet/index.php');
        $request->setUserResolver(fn () => $user);

        (new AnnouncementMiddleware)->handle($request, fn () => null);

        $this->assertTrue(Cache::has($cacheKey));
    }

    public function test_handle_skips_on_post_request()
    {
        $user = LegacyUserFactory::new()->create();
        $cacheKey = "announcement.user_type.{$user->ref_cod_tipo_usuario}";

        $request = Request::create('/qualquer', 'POST');
        $request->setUserResolver(fn () => $user);

        (new AnnouncementMiddleware)->handle($request, fn () => null);

        $this->assertFalse(Cache::has($cacheKey));
    }

    public function test_handle_skips_on_json_request()
    {
        $user = LegacyUserFactory::new()->create();
        $cacheKey = "announcement.user_type.{$user->ref_cod_tipo_usuario}";

        $request = Request::create('/qualquer', 'GET', server: ['HTTP_ACCEPT' => 'application/json']);
        $request->setUserResolver(fn () => $user);

        (new AnnouncementMiddleware)->handle($request, fn () => null);

        $this->assertFalse(Cache::has($cacheKey));
    }

    public function test_handle_skips_on_pjax_request()
    {
        $user = LegacyUserFactory::new()->create();
        $cacheKey = "announcement.user_type.{$user->ref_cod_tipo_usuario}";

        $request = Request::create('/qualquer', 'GET', server: ['HTTP_X_PJAX' => 'true']);
        $request->setUserResolver(fn () => $user);

        (new AnnouncementMiddleware)->handle($request, fn () => null);

        $this->assertFalse(Cache::has($cacheKey));
    }

    public function test_observer_clears_cache_when_announcement_saved()
    {
        $user = LegacyUserFactory::new()->create();
        $cacheKey = "announcement.user_type.{$user->ref_cod_tipo_usuario}";

        Cache::put($cacheKey, 'DUMMY', now()->addWeek());
        $this->assertTrue(Cache::has($cacheKey));

        Announcement::create([
            'name' => 'teste',
            'description' => 'teste',
            'created_by_user_id' => $user->getKey(),
        ]);

        $this->assertFalse(Cache::has($cacheKey));
    }

    public function test_observer_clears_cache_when_announcement_deleted()
    {
        $user = LegacyUserFactory::new()->create();
        $cacheKey = "announcement.user_type.{$user->ref_cod_tipo_usuario}";

        $announcement = Announcement::create([
            'name' => 'teste',
            'description' => 'teste',
            'created_by_user_id' => $user->getKey(),
        ]);

        Cache::put($cacheKey, 'DUMMY', now()->addWeek());
        $this->assertTrue(Cache::has($cacheKey));

        $announcement->delete();

        $this->assertFalse(Cache::has($cacheKey));
    }
}
