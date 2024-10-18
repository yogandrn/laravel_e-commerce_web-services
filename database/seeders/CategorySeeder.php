<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Handphone dan Tablet',
            'slug' => 'handphone-dan-tablet',
        ]);
        Category::create([
            'name' => 'Komputer dan Laptop',
            'slug' => 'komputer-dan-laptop',
        ]);
        Category::create([
            'name' => 'Aksesories',
            'slug' => 'aksesories',
        ]);
    }
}
