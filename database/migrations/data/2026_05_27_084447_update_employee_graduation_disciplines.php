<?php

use Database\Seeders\DefaultEmployeeGraduationDisciplines;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => DefaultEmployeeGraduationDisciplines::class,
            '--force' => true,
        ]);
    }
};
