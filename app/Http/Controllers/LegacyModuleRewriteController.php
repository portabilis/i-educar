<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class LegacyModuleRewriteController extends Controller
{
    /**
     * Rewrite a URL like a web server rewrite rule.
     *
     * @param string $module
     * @param string $path
     * @param string $resource
     *
     * @return Response
     */
    public function rewrite($module, $path, $resource)
    {
        $filename = base_path("ieducar/intranet/{$path}/{$resource}");

        $contentFile = file_get_contents($filename);
        $contentType = mime_content_type($filename);

        return new Response($contentFile, Response::HTTP_OK, [
            'Content-Type' => $contentType
        ]);
    }
}
