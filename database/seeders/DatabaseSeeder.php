<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CatalogueSeeder::class,
            DemoLabourSeeder::class,
        ]);

        // A customer account for quick testing.
        User::firstOrCreate(
            ['mobile' => '9999999999'],
            ['role' => User::ROLE_CUSTOMER, 'status' => 'active', 'preferred_locale' => 'hi'],
        );
    }
}