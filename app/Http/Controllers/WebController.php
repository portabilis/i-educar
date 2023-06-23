<?php

namespace App\Http\Controllers;

use App\Models\LegacyInstitution;
use App\Services\MenuCacheService;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function user(Request $request)
    {
        return $request->user()->load('type');
    }

    public function menus(MenuCacheService $menus, Request $request)
    {
        return $menus->getMenuByUser($request->user());
    }

    public function config(Repository $config)
    {
        return [
            'footer' => $config->get('legacy.config.ieducar_internal_footer'),
        ];
    }

    public function institution()
    {
        return [
            'name' => LegacyInstitution::value('nm_instituicao'),
            'logo' => config('legacy.report.logo_file_name'),
        ];
    }

    public function fallback($uri)
    {
        if (str_starts_with($uri, 'web')) {
            return redirect('intranet/educar_index.php');
        }

        return abort(404);
    }
}
