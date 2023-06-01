<?php

namespace Database\Seeders;

use App\Models\job_form;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class job_formSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($j = 0; $j < 200; $j++)
        {
            $job_post = new job_form;
            $job_post->title = $faker->word;
            $job_post->description = $faker->sentence;
            $job_post->company_id = $faker->randomElement([1,2,3,4]);
            $job_post->due_date = '2023-07-07';
            $skill='';
            for ($i = 0; $i < 5; $i++) { // Change the number 10 to the desired number of skills
                
                $skill =$skill.' '.$faker->randomElement(['Python', 'C++', 'Java', 'C', 'JavaScript', 'HTML/CSS', 'PHP', 'SQL', 'TypeScript', 'C#']);
            }
            $job_post->skills = $skill;
            $job_post->save();
        }
    }
}
