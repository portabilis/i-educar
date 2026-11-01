<?php

use App\Http\Middleware\AnnouncementMiddleware;
use App\Models\Announcement;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Cache::flush();
});

test('handle caches announcement query on get html request', function () {
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
});

test('handle skips on post request', function () {
    $user = LegacyUserFactory::new()->create();
    $cacheKey = "announcement.user_type.{$user->ref_cod_tipo_usuario}";

    $request = Request::create('/qualquer', 'POST');
    $request->setUserResolver(fn () => $user);

    (new AnnouncementMiddleware)->handle($request, fn () => null);

    $this->assertFalse(Cache::has($cacheKey));
});

test('handle skips on json request', function () {
    $user = LegacyUserFactory::new()->create();
    $cacheKey = "announcement.user_type.{$user->ref_cod_tipo_usuario}";

    $request = Request::create('/qualquer', 'GET', server: ['HTTP_ACCEPT' => 'application/json']);
    $request->setUserResolver(fn () => $user);

    (new AnnouncementMiddleware)->handle($request, fn () => null);

    $this->assertFalse(Cache::has($cacheKey));
});

test('handle skips on pjax request', function () {
    $user = LegacyUserFactory::new()->create();
    $cacheKey = "announcement.user_type.{$user->ref_cod_tipo_usuario}";

    $request = Request::create('/qualquer', 'GET', server: ['HTTP_X_PJAX' => 'true']);
    $request->setUserResolver(fn () => $user);

    (new AnnouncementMiddleware)->handle($request, fn () => null);

    $this->assertFalse(Cache::has($cacheKey));
});

test('observer clears cache when announcement saved', function () {
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
});

test('observer clears cache when announcement deleted', function () {
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
});
