<?php

namespace App\Http\Controllers;

class DownloadController extends Controller
{
    public function standardDocumentation($name)
    {
        if (isset($name)) {
            $file = storage_path('app/standard-documentation/' . $name);
            if (file_exists($file)) {
                header('Content-type: application/pdf');
                header('Content-Disposition: inline; filename="' . $name . '"');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: ' . filesize($file));
                header('Accept-Ranges: bytes');

                readfile($file);
            }
        }
    }
}
