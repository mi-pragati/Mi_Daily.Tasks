<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call([
           // StudentSeeder::class
        //]);

        Employee::factory()->count(15)->create();




        // User::factory(10)->create();

        //User::factory()->create([
          //  'name' => 'Test User',
            //'email' => 'test@example.com',

    }
}
