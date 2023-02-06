<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateQuantityMissingStudentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:quantity-missing-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza a quantidade de faltas do Aluno';

    public function handle()
    {
        $obj = new \clsModulesFrequencia();
        $obj->updateQuantityMissingStudents();
    }
}
