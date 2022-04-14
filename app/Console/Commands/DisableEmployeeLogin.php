<?php

namespace App\Console\Commands;

use App\Models\LegacyEmployee;
use Illuminate\Console\Command;

class DisableEmployeeLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disable:login {registration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inativa login de funcionário pela matricula';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $registration = $this->argument('registration');

        $cancel = $this->confirm('Deseja realmente desativar o usuário ' . $registration . ' ?', true) === false;

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
                'ativo' => 0,
            ]);

        $this->info('Usuário desativado com sucesso!');
    }
}
