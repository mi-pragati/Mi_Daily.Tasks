<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $students = collect ([
            [
                'name'=>'Dudu',
            'email'=>'dudu@gmail.com'
            ],
            [
                'name'=>'bubu',
            'email'=>'bubu@gmail.com'
            ],
            [
                'name'=>'aditya',
            'email'=>'adn@gmail.com'
            ],
        [
            'name'=>'Pragati',
            'email'=>'pragatik@gmail.com'
        ]
            ]);

            $students->each(function($student){
                student::insert($student);
            });
    }
}
