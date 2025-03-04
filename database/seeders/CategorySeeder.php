<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $categories = [
            ['name_en' => 'Sports', 'name_ar' => 'رياضة'],
            ['name_en' => 'Science', 'name_ar' => 'علوم'],
            ['name_en' => 'History', 'name_ar' => 'تاريخ'],
            ['name_en' => 'Technology', 'name_ar' => 'تكنولوجيا'],
      
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
};

    

