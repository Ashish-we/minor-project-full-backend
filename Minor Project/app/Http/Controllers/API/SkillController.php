<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkillFilter;
use App\Http\Requests\SkillRequest;
use App\Http\Requests\UserSkillRquest;
use App\Models\job_form;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SkillController extends Controller
{
    use HttpResponses;
    // public function skill_add(SkillRequest $request)
    // {
    //     $request->validated($request->all());
    //     $skill = Skill::create([
    //         'skill' => $request->skill,
    //     ]);

    //     return $this->success([
    //         'skill' => $skill
    //     ]);
    // } 

    // public function user_skill_submin(UserSkillRquest $request)
    // {
    //     $request->validate($request->all());
    //     $user = Auth::user();
    //     DB::insert('insert into intern_skills (user_id, skill_id) values (?, ?)',[$user->id, $request->val1] );
    //     DB::insert('insert into intern_skills (user_id, skill_id) values (?, ?)',[$user->id, $request->val2] );
    //     DB::insert('insert into intern_skills (user_id, skill_id) values (?, ?)',[$user->id, $request->val3] );
    //     DB::insert('insert into intern_skills (user_id, skill_id) values (?, ?)',[$user->id, $request->val4] );
    //     DB::insert('insert into intern_skills (user_id, skill_id) values (?, ?)',[$user->id, $request->val5] );

    // }

    public function filter_skill(SkillFilter $request)
    {
        // $request->validate($request->all());
        $skill = $request->skill;
        // $job_post = DB::select("select * from job_forms where skills like '%$skill%'");
        $job_post = job_form::search(request(key : 'skill'))->get();

        return $this->success([
            'job_post' => $job_post,
        ]);
    }

    public function skills_match()
    {
        $job_list = job_form::get();
        $user = Auth::user();
        $jobs= array('');
        foreach ($job_list as $job) {

            $parts1 = explode(" ", $job->skills);
            $parts2 = explode(" ", $user->skills);
            $matchingSkills = array_intersect($parts1, $parts2);
            // dd($matchingSkills);
            $weight = 0;
            // dd(count($matchingSkills));
            $recommendationScore = $weight + count($matchingSkills);
            // dd($recommendationScore);
            if ($recommendationScore >= 2) {
                // $recommendation = "Based on your skills and experience, you are a strong match for this job.";
                array_push($jobs, $job);
            }
        }
        return response()->json([
            'job_list' => $jobs
        ]);
    }



}
