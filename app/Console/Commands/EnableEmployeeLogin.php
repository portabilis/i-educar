<?php

namespace App\Console\Commands;

use App\Models\LegacyEmployee;
use Illuminate\Console\Command;

class EnableEmployeeLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enable:login {registration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ativa login de funcionário pela matricula';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $registration = $this->argument('registration');

        $cancel = $this->confirm('Deseja realmente ativar o usuário ' . $registration . ' ?', true) === false;

        if ($cancel) {
            return;
        }

        $notExists = LegacyEmployee::where('matricula', $registration)->exists() === false;

        if ($notExists) {
            $this->info('Usuário não encontrado.');

            return;
        }

        LegacyEmployee::where('matricula', $registration)
            ->update([
                'ativo' => 1,
            ]);

        $this->info('Usuário ativado com sucesso!');
    }
}
