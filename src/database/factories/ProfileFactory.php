<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bio' => $this->faker->sentence,
            'phone_number' => $this->faker->sentence,
            'phone_verified_at' => \null,
            'country' => $this->faker->sentence,
            'city' => $this->faker->sentence,
            'postal-code' => $this->faker->word(),
            'address-line-1' => $this->faker->sentence,
            'address-line-2' => $this->faker->sentence,
            'photo_path' => $this->faker->sentence,
            'cover_path' => $this->faker->sentence,
            'education' => $this->faker->sentence,
        ];
    }
}
