<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($j = 0; $j < 100; $j++)
        {
            $user = new User;
            $skill='';
            for ($i = 0; $i < 5; $i++) { // Change the number 10 to the desired number of skills
                
                $skill =$skill.' '.$faker->randomElement(['Python', 'C++', 'Java', 'C', 'JavaScript', 'HTML/CSS', 'PHP', 'SQL', 'TypeScript', 'C#']);
            }
            $user->name = $faker->name;
            $user->email = $faker->email;
            $user->password = $faker->password;
            $user->skills = $skill;
            $user->save();

        }
    }
}
