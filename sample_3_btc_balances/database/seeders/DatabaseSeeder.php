<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Partner;
use App\Models\SiteBalance;
use App\Models\User;
use Database\Factories\SiteBalanceFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $user = User::factory()->create([
             'name' => 'Test User',
             'email' => 'test@example.com',
         ]);

         User::factory()->create([
             'name' => 'Bill Gates',
             'email' => 'test2@example.com',
         ]);

         User::factory()->create([
             'name' => 'Ivan Ivanov',
             'email' => 'ivanov@example.com',
         ]);

         Partner::factory()->create([
             'name' => 'Yandex',
         ]);

         Partner::factory()->create([
             'name' => 'Bing',
         ]);

         Partner::factory()->create([
             'name' => 'Google',
         ]);

         SiteBalance::factory()->create();
    }
}
