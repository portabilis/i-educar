<?php

namespace App\Http\Controllers;

use App\Services\BackupUrlPresigner;
use Illuminate\Http\Request;
use Redirect;

class BackupController extends Controller
{
    public function download(Request $request)
    {
        $presignedUrl = (new BackupUrlPresigner($request->url))->getPresignedUrl();

        return Redirect::away($presignedUrl);
    }
}
