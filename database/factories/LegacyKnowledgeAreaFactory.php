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

    /**
     * Retorna a área de conhecimento "Educação Infantil".
     */
    public function earlyChildhoodEducation(): LegacyKnowledgeArea
    {
        $data = [
            'nome' => 'Educação Infantil',
        ];

        return LegacyKnowledgeArea::query()->where($data)->first() ?? $this->withEarlyChildhoodEducationDisciplines()->create($data);
    }

    /**
     * Retorna a área de conhecimento "Ensino Fundamental".
     */
    public function elementarySchool(): LegacyKnowledgeArea
    {
        $data = [
            'nome' => 'Ensino Fundamental',
        ];

        return LegacyKnowledgeArea::query()->where($data)->first() ?? $this->withElementarySchoolDisciplines()->create($data);
    }

    /**
     * Adiciona as disciplinas padrão da área de conhecimento "Educação Infantil".
     */
    public function withEarlyChildhoodEducationDisciplines(): static
    {
        return $this->afterCreating(function (LegacyKnowledgeArea $knowledgeArea) {
            LegacyDisciplineFactory::new()->state([
                'area_conhecimento_id' => $knowledgeArea,
                'abbreviation' => 'EF',
            ])->createMany([
                ['name' => 'O eu, o outro e o nós'],
                ['name' => 'Corpo, gestos e movimento'],
                ['name' => 'Traços, sons, cores e formas'],
                ['name' => 'Escuta, fala, pensamento e imaginação'],
                ['name' => 'Espaços, tempos, quantidades, relações e transformações'],
            ]);
        });
    }

    /**
     * Adiciona as disciplinas padrão da área de conhecimento "Ensino Fundamental".
     */
    public function withElementarySchoolDisciplines(): static
    {
        return $this->afterCreating(function (LegacyKnowledgeArea $knowledgeArea) {
            LegacyDisciplineFactory::new()->state([
                'area_conhecimento_id' => $knowledgeArea,
            ])->createMany([
                ['name' => 'Ensino Religioso', 'abbreviation' => 'ENR'],
                ['name' => 'História', 'abbreviation' => 'HIS'],
                ['name' => 'Geografia', 'abbreviation' => 'GEO'],
                ['name' => 'Matemática', 'abbreviation' => 'MAT'],
                ['name' => 'Ciências', 'abbreviation' => 'CIE'],
                ['name' => 'Língua Portuguesa', 'abbreviation' => 'POR'],
                ['name' => 'Língua Inglesa', 'abbreviation' => 'ING'],
                ['name' => 'Artes', 'abbreviation' => 'ART'],
                ['name' => 'Educação Física', 'abbreviation' => 'EDF'],
            ]);
        });
    }

    /**
     * Adiciona disciplinas padrão.
     */
    public function withOneDiscipline(): static
    {
        return $this->afterCreating(function (LegacyKnowledgeArea $knowledgeArea) {
            LegacyDisciplineFactory::new()->state([
                'area_conhecimento_id' => $knowledgeArea,
            ])->createMany([
                ['name' => 'Disciplina Padrão', 'abbreviation' => 'DIP'],
            ]);
        });
    }

    /**
     * Adiciona disciplinas padrão.
     *
     * @deprecated
     * @see LegacyKnowledgeAreaFactory::earlyChildhoodEducation()
     * @see LegacyKnowledgeAreaFactory::elementarySchool()
     */
    public function unique(): self
    {
        return $this->state(function () {
            $knowledgeArea = LegacyKnowledgeArea::query()->first();

            if (empty($knowledgeArea)) {
                $knowledgeArea = LegacyKnowledgeAreaFactory::new()->create();
            }

            return [
                'id' => $knowledgeArea->getKey(),
            ];
        });
    }
}
