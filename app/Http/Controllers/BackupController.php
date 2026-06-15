<?php

namespace App\Http\Controllers;

use App\Services\BackupUrlPresigner;
use Illuminate\Http\Request;
use Redirect;

class BackupController extends Controller
{
    public function download(Request $request, BackupUrlPresigner $backupUrlPresigner)
    {
        return Redirect::away($request->url);
    }
}
