<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LegacyLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create symbol link to public legacy path';

    /**
     * Create a symbol link for $path and show a info message.
     *
     * @param string $path
     * @param string $target
     *
     * @return void
     */
    private function createSymbolLink($path, $target = '../')
    {
        $legacy = $target . config('legacy.path') . '/' . $path;
        $public = public_path($path);

        if (is_link($public)) {
            unlink($public);
        }

        symlink($legacy, $public);

        $this->info("Symbol link created for: {$path}");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $links = [
            '../../' => [
                'intranet/arquivos',
                'intranet/downloads',
                'intranet/fonts',
                'intranet/fotos',
                'intranet/imagens',
                'intranet/scripts',
                'intranet/static',
                'intranet/styles',
                'intranet/tmp',
            ],
            '../' => [
                'modules',
            ],
        ];

        foreach ($links as $target => $paths) {
            foreach ($paths as $path) {
                $this->createSymbolLink($path, $target);
            }
        }
    }
}
