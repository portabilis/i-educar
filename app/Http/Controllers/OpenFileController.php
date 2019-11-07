<?php

namespace App\Http\Controllers;

use App\Services\UrlPresigner;
use Illuminate\Http\Request;
use Redirect;

class OpenFileController extends Controller
{
    public function open(Request $request, UrlPresigner $urlPresigner)
    {
        $presignedUrl = $urlPresigner->getPresignedUrl($request->url);

        return Redirect::away($presignedUrl);
    }
}
