<?php

namespace App\Console\Commands;

use App\Models\LegacyEmployee;
use App\Models\LegacyPerson;
use App\Services\ImportUsersService;
use App\Support\Database\Connections;
use iEducar\Support\Output\NullOutput;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordCommand extends Command
{
    use Connections;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:password {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reseta a senha do usuário em todas as bases';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $username = $this->argument('username');

        $output = new NullOutput;
        $service = new ImportUsersService($output);

        $newPassword = Str::random(8);

        $employee = null;
        $this->eachConnection(function () use ($username, $newPassword, &$employee) {
            if (!LegacyEmployee::where('matricula', $username)->exists()) {
                return;
            }

            $employee = LegacyEmployee::where('matricula', $username)->first();
            $employee->senha = Hash::make($newPassword);
            $employee->save();
        });

        if ($employee) {
            $service->sendPasswordEmail(
                $employee->email,
                $username,
                $newPassword,
                LegacyPerson::find($employee->getKey())->name
            );
        }
    }
}
