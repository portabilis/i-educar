<?php

namespace App\Http\Controllers\Educacenso;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        throw new Exception('Importacao não implementada para ' . $request->get('ano'));
    }
}
