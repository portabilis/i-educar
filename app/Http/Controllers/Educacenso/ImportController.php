<?php

namespace App\Http\Controllers\Educacenso;

use App\Http\Controllers\Controller;
use App\Services\Educacenso\ImportServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $file = $request->file('arquivo');

        $importService = ImportServiceFactory::createImportService($file, $request->get('ano'), Auth::user());

        $importService->handleFile($file);
    }
}
