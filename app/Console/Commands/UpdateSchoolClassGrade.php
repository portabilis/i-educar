<?php

namespace App\Console\Commands;

use App\Exceptions\Console\MissingSchoolCourseException;
use App\Exceptions\Console\MissingSchoolGradeException;
use App\Models\LegacyEnrollment;
use App\Models\LegacyLevel;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSchoolClassGrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:school-class-grade {schoolclass} {grade}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza a série da turma';

    /**
     * @var LegacyLevel
     */
    private $grade;

    /**
     * @var LegacySchoolClass
     */
    private $schoolClass;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->grade = LegacyLevel::findOrFail($this->argument('grade'));
        $this->schoolClass = LegacySchoolClass::findOrFail($this->argument('schoolclass'));

        $this->validateSchoolGrade();
        $this->validateSchoolCourse();

        DB::beginTransaction();

        $this->schoolClass->update([
            'ref_ref_cod_serie' => $this->grade->getKey(),
            'ref_cod_curso' =>  $this->grade->course->id,
        ]);

        $this->schoolClass->enrollments->map(function ($enrollment) {
            /** @var LegacyEnrollment $enrollment */
            $enrollment->registration->update([
                'ref_ref_cod_serie' => $this->grade->getKey(),
                'ref_cod_curso' => $this->grade->course->id,
            ]);
        });

        DB::commit();
    }

    /**
     * Valida se existe registro em escola_serie
     *
     * @throws MissingSchoolGradeException
     */
    private function validateSchoolGrade()
    {
        $existsSchoolGrade = LegacySchoolGrade::where('ref_cod_escola', $this->schoolClass->school_id)
            ->where('ref_cod_serie', $this->grade->getKey())
            ->exists();

        if ($existsSchoolGrade) {
            return;
        }

        throw new MissingSchoolGradeException($this->schoolClass->school, $this->grade);
    }

    /**
     * Valida se existe registro em escola_curso
     *
     * @throws MissingSchoolCourseException
     */
    private function validateSchoolCourse()
    {
        $existsSchoolCourse = LegacySchoolCourse::where('ref_cod_escola', $this->schoolClass->school_id)
            ->where('ref_cod_curso', $this->grade->course->id)
            ->exists();

        if ($existsSchoolCourse) {
            return;
        }

        throw new MissingSchoolCourseException($this->schoolClass->school, $this->grade->course);
    }

}
