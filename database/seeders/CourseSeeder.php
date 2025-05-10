<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::inRandomOrder()->first();

        Course::factory()->count(10)->create([
            'category_id' => $category->id,
        ]);
    }
}
