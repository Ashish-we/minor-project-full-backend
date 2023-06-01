<?php

namespace App\Console;

use App\Models\Intern_form;
use App\Models\job_form;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () 
        {
            $today = Carbon::now()->format('Y-m-d');
            // dd($today);
            // Get the job posts that are due today
            $duePosts = job_form::where('due_date',$today)
                ->get();
                // dd($duePosts);
                // dd($duePosts);
                // Move the due posts to the expire table and delete them from the job_post table
                foreach ($duePosts as $post) {
                DB::table('expjobpost')->insert([
                    'title' => $post->title,
                    'description' => $post->description,
                    'due_date' => $post->due_date,
                    'job_id' => $post->job_id,
                    'company_id' => $post->company_id,
                    'skills' =>$post->skills,
                    // Add any other columns you want to move to the expire table
                ]);

                DB::table('job_forms')->where('job_id', $post->job_id)->delete();
            }
        })->name('scheduled-function-1')->everyMinute();

        $schedule->call(function () 
        {
            $expire_jobs = DB::table('expjobpost')->get();
            // dd($expire_jobs);
            foreach($expire_jobs as $expire_job)
            {
                // dd($expire_job->job_id);
                $exp_user_forms = Intern_form::where('job_id', $expire_job->job_id)->get();
                
                    foreach($exp_user_forms as $exp_user_form)
                    {
                        DB::table('expuserform')->insert([
                            'user_id' => $exp_user_form->user_id,
                            'description' => $exp_user_form->description,
                            'job_id' => $exp_user_form->job_id,
                            'pdf' => $exp_user_form->pdf,
                            'id' => $exp_user_form->id,
                        ]);
                        DB::table('intern_forms')->where('id',$exp_user_form->id )->delete();
                    
                    }
                
            }
        })->name('scheduled-function-2')->everyMinute();

            

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
