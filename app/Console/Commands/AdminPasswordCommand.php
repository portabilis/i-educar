<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:password {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates admin\'s user password';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $password = Hash::make($this->argument('password'));

        DB::connection('pgsql')->update('UPDATE portal.funcionario SET senha = ? WHERE matricula = ?', [$password, 'admin']);

        $this->info('Password updated.');
    }
}
