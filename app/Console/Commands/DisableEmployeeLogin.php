<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegacyEmployee;
use Illuminate\Support\Facades\DB;

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
        if ($this->confirm('Deseja realmente desativar o usuário '.$this->argument('registration').' ?', true)) {
            $registration = $this->argument('registration');

            if (LegacyEmployee::where('matricula', $registration)->exists()) {
                LegacyEmployee::where('matricula', $registration)
                    ->update([
                        'ativo' => 0,
                    ]);

                $this->info('Usuário desativado com sucesso!');
            }
        }
    }
}
