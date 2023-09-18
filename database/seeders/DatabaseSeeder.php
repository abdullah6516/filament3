<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\
{
    User,
    Product,
    Category,
    Tag
};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::factory()->create([
            'name' => 'elian',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        Product::create([
            'name' => 'product',
            'price' => '50',
            'category_id' => '1',
        ]);


        Category::create([
            'name' => 'category-1',
        ]);

        Tag::create([
            'name' => 'tag-1',
        ]);

    }
}
