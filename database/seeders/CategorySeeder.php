<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'राजनीति', 'slug' => 'rajniti'],
            ['name' => 'अर्थतन्त्र', 'slug' => 'arthatantra'],
            ['name' => 'खेलकुद', 'slug' => 'khelkud'],
            ['name' => 'मनोरञ्जन', 'slug' => 'manoranjan'],
            ['name' => 'प्रविधि', 'slug' => 'prabidhi'],
            ['name' => 'विश्व', 'slug' => 'bishwa'],
            ['name' => 'स्वास्थ्य', 'slug' => 'swasthya'],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(['slug' => $c['slug']], ['name' => $c['name']]);
        }
    }
}