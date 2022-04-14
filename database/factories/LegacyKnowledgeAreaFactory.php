<?php

namespace Database\Factories;

use App\Models\LegacyKnowledgeArea;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyKnowledgeAreaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyKnowledgeArea::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'instituicao_id' => LegacyInstitutionFactory::new()->unique()->make(),
            'nome' => $this->faker->words(3, true),
        ];
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
