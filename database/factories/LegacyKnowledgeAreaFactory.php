<?php

namespace Database\Factories;

use App\Models\LegacyKnowledgeArea;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyKnowledgeAreaFactory extends Factory
{
    protected $model = LegacyKnowledgeArea::class;

    public function definition(): array
    {
        return [
            'instituicao_id' => fn () => LegacyInstitutionFactory::new()->current(),
            'nome' => $this->faker->words(3, true),
        ];
    }

    public function earlyChildhoodEducation(): LegacyKnowledgeArea
    {
        $data = [
            'nome' => 'Educação Infantil',
        ];

        return LegacyKnowledgeArea::query()->where($data)->first() ?? $this->withEarlyChildhoodEducationDisciplines()->create($data);
    }

    public function elementarySchool(): LegacyKnowledgeArea
    {
        $data = [
            'nome' => 'Ensino Fundamental',
        ];

        return LegacyKnowledgeArea::query()->where($data)->first() ?? $this->withElementarySchoolDisciplines()->create($data);
    }

    public function withEarlyChildhoodEducationDisciplines(): static
    {
        return $this->afterCreating(function (LegacyKnowledgeArea $knowledgeArea) {
            LegacyDisciplineFactory::new()->createMany([
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'O eu, o outro e o nós',
                    'abbreviation' => 'EF',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Corpo, gestos e movimento',
                    'abbreviation' => 'EF',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Traços, sons, cores e formas',
                    'abbreviation' => 'EF',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Escuta, fala, pensamento e imaginação',
                    'abbreviation' => 'EF',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Espaços, tempos, quantidades, relações e transformações',
                    'abbreviation' => 'EF',
                ],
            ]);
        });
    }

    public function withElementarySchoolDisciplines(): static
    {
        return $this->afterCreating(function (LegacyKnowledgeArea $knowledgeArea) {
            LegacyDisciplineFactory::new()->createMany([
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Ensino Religioso',
                    'abbreviation' => 'ENR',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'História',
                    'abbreviation' => 'HIS',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Geografia',
                    'abbreviation' => 'GEO',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Matemática',
                    'abbreviation' => 'MAT',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Ciências',
                    'abbreviation' => 'CIE',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Língua Portuguesa',
                    'abbreviation' => 'POR',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Língua Inglesa',
                    'abbreviation' => 'ING',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Artes',
                    'abbreviation' => 'ART',
                ],
                [
                    'area_conhecimento_id' => $knowledgeArea,
                    'name' => 'Educação Física',
                    'abbreviation' => 'EDF',
                ],
            ]);
        });
    }

    public function unique(): self
    {
        return $this->state(function () {
            $knowledgeArea = LegacyKnowledgeArea::query()->first();

            if (empty($knowledgeArea)) {
                $knowledgeArea = LegacyKnowledgeAreaFactory::new()->create();
            }

            return [
                'id' => $knowledgeArea->getKey()
            ];
        });
    }
}
