<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDeficiency;
use App\Models\LegacyDocument;
use App\Models\LegacyIndividual;
use App\Models\LegacyIndividualPicture;
use App\Models\LegacyPerson;
use App\Models\LegacyRace;
use App\Models\LegacyStudent;
use App\Models\Nationality;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyIndividualFactory;
use Tests\EloquentTestCase;

class LegacyIndividualTest extends EloquentTestCase
{
    public $relations = [
        'race' => LegacyRace::class,
        'deficiency' => LegacyDeficiency::class,
        'person' => LegacyPerson::class,
        'student' => LegacyStudent::class,
        'document' => LegacyDocument::class,
        'picture' => LegacyIndividualPicture::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyIndividual::class;
    }

    public function test_informing_cpf_clears_absence_reason(): void
    {
        $individual = LegacyIndividualFactory::new()->create([
            'document_absence_reason_id' => DocumentAbsenceReasonFactory::new(),
            'document_absence_observation' => 'Documentos perdidos',
        ]);

        $individual->update(['cpf' => 81388232758]);
        $individual->refresh();

        $this->assertNull($individual->document_absence_reason_id);
        $this->assertNull($individual->document_absence_observation);
    }

    public function test_informing_zero_cpf_keeps_absence_reason(): void
    {
        $reason = DocumentAbsenceReasonFactory::new()->create();
        $individual = LegacyIndividualFactory::new()->create([
            'document_absence_reason_id' => $reason->getKey(),
        ]);

        $individual->update(['cpf' => 0]);

        $this->assertSame($reason->getKey(), $individual->refresh()->document_absence_reason_id);
    }

    public function test_possui_cpf_follows_the_cpf(): void
    {
        $individual = LegacyIndividualFactory::new()->create(['cpf' => 81388232758]);
        $this->assertTrue($individual->refresh()->possui_cpf);

        $individual->update(['cpf' => 0]);
        $this->assertFalse($individual->refresh()->possui_cpf);

        $individual->update(['cpf' => null]);
        $this->assertFalse($individual->refresh()->possui_cpf);
    }

    public function test_saving_without_changing_cpf_keeps_absence_reason(): void
    {
        $reason = DocumentAbsenceReasonFactory::new()->create();
        $individual = LegacyIndividualFactory::new()->create([
            'document_absence_reason_id' => $reason->getKey(),
        ]);

        LegacyIndividual::find($individual->getKey(), ['idpes', 'ativo'])->update(['ativo' => 0]);
        $individual->refresh();

        $this->assertSame($reason->getKey(), $individual->document_absence_reason_id);
    }

    public function test_nationality_name_is_null_without_nationality(): void
    {
        $individual = LegacyIndividualFactory::new()->create(['nacionalidade' => null]);

        $this->assertNull($individual->refresh()->nationality_name);
    }

    public function test_nationality_name_describes_the_nationality(): void
    {
        $individual = LegacyIndividualFactory::new()->create(['nacionalidade' => Nationality::FOREIGN]);

        $this->assertSame('Estrangeira', $individual->refresh()->nationality_name);
    }
}
