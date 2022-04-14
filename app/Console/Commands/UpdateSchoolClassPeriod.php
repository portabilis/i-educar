<?php

namespace App\Console\Commands;

use App\Models\LegacySchoolClass;
use App\Services\SchoolClass\PeriodService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSchoolClassPeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:school-class-period';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insere turno nas turmas que ainda não tem mediante o horário inicial e final';

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
     * @return mixed
     */
    public function handle()
    {
        $schoolClasses = LegacySchoolClass::whereNull('turma_turno_id')
            ->whereNotNull('hora_inicial')
            ->whereNotNull('hora_final')
            ->where('ano', '>=', 2019)
            ->get();

        $service = new PeriodService();

        DB::beginTransaction();

        foreach ($schoolClasses as $schoolClass) {
            $newPeriod = $service->getPeriodByTime($schoolClass->hora_inicial, $schoolClass->hora_final);

            $schoolClass->turma_turno_id = $newPeriod;
            $schoolClass->save();
        }

        DB::commit();

        $this->info("Turmas alteradas: {$schoolClasses->count()}");
    }
}
