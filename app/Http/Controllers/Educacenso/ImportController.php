<?php

namespace App\Http\Controllers\Educacenso;

use App\Http\Controllers\Controller;
use App\Services\Educacenso\SplitFileService;
use Exception;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $file = $request->file('arquivo');
        $splitFileService = new SplitFileService($file);
        $schools = $splitFileService->getSplitedSchools();

        throw new Exception('Importacao não implementada para ' . $request->get('ano'));
    }
}
