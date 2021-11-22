<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegacyEmployee;
use Illuminate\Support\Facades\DB;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm('Deseja realmente ativar o usuário '.$this->argument('registration').' ?', true)) {
            $registration = $this->argument('registration');

            if (LegacyEmployee::where('matricula', $registration)->exists()) {
                LegacyEmployee::where('matricula', $registration)
                    ->update([
                        'ativo' => 1,
                    ]);

                $this->info('Usuário ativado com sucesso!');
            }
        }
    }
}
