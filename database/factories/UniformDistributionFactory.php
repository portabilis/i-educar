<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UniformDistribution>
 */
class UniformDistributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'student_id' => fn () => LegacyStudentFactory::new()->create(),
            'school_id' => fn () => LegacySchoolFactory::new()->create(),
            'year' => now()->year,
            'distribution_date' => now()->format('d/m/Y'),
            'complete_kit' => $this->faker->boolean,
            'coat_pants_qty' => $this->faker->randomDigitNotZero(),
            'coat_jacket_qty' => $this->faker->randomDigitNotZero(),
            'shirt_short_qty' => $this->faker->randomDigitNotZero(),
            'shirt_long_qty' => $this->faker->randomDigitNotZero(),
            'socks_qty' => $this->faker->randomDigitNotZero(),
            'shorts_tactel_qty' => $this->faker->randomDigitNotZero(),
            'shorts_coton_qty' => $this->faker->randomDigitNotZero(),
            'sneakers_qty' => $this->faker->randomDigitNotZero(),
            'coat_pants_tm' => $this->faker->randomElement(['P', 'M', 'G']),
            'coat_jacket_tm' => $this->faker->randomElement(['P', 'M', 'G']),
            'shirt_short_tm' => $this->faker->randomElement(['P', 'M', 'G']),
            'shirt_long_tm' => $this->faker->randomElement(['P', 'M', 'G']),
            'socks_tm' => $this->faker->randomDigitNotZero(),
            'shorts_tactel_tm' => $this->faker->randomElement(['P', 'M', 'G']),
            'shorts_coton_tm' => $this->faker->randomElement(['P', 'M', 'G']),
            'sneakers_tm' => $this->faker->randomElement(['P', 'M', 'G']),
            'kids_shirt_qty' => $this->faker->randomDigitNotZero(),
            'kids_shirt_tm' => $this->faker->randomElement(['P', 'M', 'G']),
            'pants_jeans_qty' => $this->faker->randomDigitNotZero(),
            'pants_jeans_tm' => $this->faker->randomElement(['P', 'M', 'G']),
            'skirt_qty' => $this->faker->randomDigitNotZero(),
            'skirt_tm' => $this->faker->randomDigitNotZero(),
            'type' => $this->faker->randomElement(['Solicitado', 'Entregue']),
        ];
    }
}
