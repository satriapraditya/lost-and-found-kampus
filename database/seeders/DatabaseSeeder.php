<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach (['Elektronik', 'Dokumen', 'Aksesoris', 'Pakaian', 'Lainnya'] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
