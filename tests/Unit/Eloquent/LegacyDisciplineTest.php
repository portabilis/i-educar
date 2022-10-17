<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacyKnowledgeArea;
use Tests\EloquentTestCase;

class LegacyDisciplineTest extends EloquentTestCase
{
    private LegacyDiscipline $legacyDiscipline;

    protected $relations = [
        'knowledgeArea' => LegacyKnowledgeArea::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDiscipline::class;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->legacyDiscipline = $this->createNewModel();
    }

    /** @teste */
    public function getInstitutionIdAttribute()
    {
        $this->assertEquals($this->legacyDiscipline, $this->legacyDiscipline->getInstitutionIdAttribute());
    }

    /** @teste */
    public function getKnowledgeAreaIdAttribute()
    {
        $this->assertEquals($this->legacyDiscipline, $this->legacyDiscipline->getKnowledgeAreaIdAttribute());
    }

    /** @teste */
    public function getNameAttribute()
    {
        $this->assertEquals($this->legacyDiscipline, $this->legacyDiscipline->getNameAttribute());
    }

    /** @teste */
    public function getAbbreviationAttribute()
    {
        $this->assertEquals($this->legacyDiscipline, $this->legacyDiscipline->getAbbreviationAttribute());
    }

    /** @teste */
    public function getFoundationTypeAttribute()
    {
        $this->assertEquals($this->legacyDiscipline, $this->legacyDiscipline->getFoundationTypeAttribute());
    }

    /** @teste */
    public function getOrderAttribute()
    {
        $this->assertEquals($this->legacyDiscipline, $this->legacyDiscipline->getOrderAttribute());
    }

    /** @teste */
    public function getEducacensoCodeAttribute()
    {
        $this->assertEquals($this->legacyDiscipline, $this->legacyDiscipline->getEducacensoCodeAttribute());
    }
}
