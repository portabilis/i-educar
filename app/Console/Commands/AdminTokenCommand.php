<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class AdminTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:token {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate admin token';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        $user = User::query()->whereHas('employee', fn ($q) => $q->where('matricula', 'admin'))->first();

        $token = $user->createToken($name);

        $this->info('Generated token:');
        $this->info('  ' . $token->plainTextToken);
    }
}
