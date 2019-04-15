<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update i-Educar';

    /**
     * Execute the console command.
     *
     * @param Filesystem $filesystem
     *
     * @return mixed
     */
    public function handle(Filesystem $filesystem)
    {
        $this->line('Updating..');

        $path = database_path('migrations/legacy');
        $table = config('database.migrations');

        DB::table($table)->where([
            'migration' => '2018_12_05_151224_adiciona_coluna_tipo_base_historico_disciplinas'
        ])->update([
            'migration' => '2019_01_01_800000_adiciona_coluna_tipo_base_historico_disciplinas'
        ]);

        DB::table($table)->where([
            'migration' => '2018_12_06_161653_altera_tamanho_coluna_cartorio_na_tabela_historico_documento'
        ])->update([
            'migration' => '2019_01_01_800000_altera_tamanho_coluna_cartorio_na_tabela_historico_documento'
        ]);

        DB::table($table)->where([
            'migration' => '2019_01_04_153440_adiciona_coluna_bloquear_cadastro_aluno_configuracoes_gerais'
        ])->update([
            'migration' => '2019_01_01_800000_adiciona_coluna_bloquear_cadastro_aluno_configuracoes_gerais'
        ]);

        DB::table($table)->where([
            'migration' => '2019_01_22_142832_inserir_tipos_logradouros'
        ])->update([
            'migration' => '2019_01_01_800000_inserir_tipos_logradouros'
        ]);

        $files = $filesystem->allFiles($path);

        foreach ($files as $file) {
            $name = $file->getBasename('.php');

            DB::table($table)->updateOrInsert([
                'migration' => $name,
            ], [
                'migration' => $name,
                'batch' => 1
            ]);
        }

        $this->info('Updated');
    }
}
