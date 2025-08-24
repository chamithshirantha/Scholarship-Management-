<?php

namespace Database\Seeders;

use App\Models\CostCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $categories = [
            ['name' => 'Tuition', 'description' => 'Tuition fees and academic charges'],
            ['name' => 'Books', 'description' => 'Textbooks and study materials'],
            ['name' => 'Living Expenses', 'description' => 'Accommodation and daily living costs'],
            ['name' => 'Transportation', 'description' => 'Travel and transportation expenses'],
            ['name' => 'Equipment', 'description' => 'Study equipment and tools'],
            ['name' => 'Research Materials', 'description' => 'Research-related materials and resources'],
            ['name' => 'Thesis Expenses', 'description' => 'Thesis or dissertation preparation costs'],
            ['name' => 'Health Insurance', 'description' => 'Student health insurance coverage'],
            ['name' => 'Fees', 'description' => 'Miscellaneous academic fees'],
            ['name' => 'Other', 'description' => 'Other educational expenses'],
        ];

        foreach ($categories as $category) {
            CostCategory::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
        
        $this->command->info('Cost categories seeded successfully!');
    }
}
