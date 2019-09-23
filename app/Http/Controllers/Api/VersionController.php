<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Throwable;

class VersionController extends Controller
{
    /**
     * Return build version or last commit hash.
     *
     * @return string
     */
    private function getBuildVersion()
    {
        try {
            $process = new Process(['git', 'rev-parse', 'HEAD'], base_path());
            $process->run();
        } catch (Throwable $throwable) {
            return 'unknow';
        }

        return trim($process->getOutput());
    }
    /**
     * Return version data.
     *
     * @return array
     */
    public function version()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')));

        return [
            'entity' => config('legacy.app.name'),
            'version' => $composer->version,
            'build' => $this->getBuildVersion(),
        ];
    }
}
