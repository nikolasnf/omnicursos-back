<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        return [
            'title' => $title,
            'description' => $this->faker->paragraph(),
            'thumbnail' => $this->faker->imageUrl(640, 480, 'education', true),
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'status' => 'published',
        ];
    }
}
