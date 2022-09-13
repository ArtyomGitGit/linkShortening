<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => $this->faker->randomNumber(),
            'original' => $this->faker->url(),
            'shortened' => config('app.url') . "/" . config('services.part_name_for_short_link') . "/" . $this->faker->randomNumber(),
            'click_count' => $this->faker->randomNumber()
        ];
    }
}
