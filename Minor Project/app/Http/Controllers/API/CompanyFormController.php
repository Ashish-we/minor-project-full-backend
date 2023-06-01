<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyForm;
use App\Models\job_form;
use App\Models\User;
use App\Models\Intern_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\DB;
class CompanyFormController extends Controller
{
    use HttpResponses;

    /**
     * @OA\Post(
     *     path="/api/company/forms_submit",
     *     tags={"submit jobform"},
     *     summary="submit new form to add a new job post",
     *     operationId="submit_job_form",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     * 
     *                 @OA\Property(
     *                     property="title",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="description",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="company_id",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="due_date",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="skills",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     )
     * )
     */



    public function submit_job_form(StoreCompanyForm $request)
    {
        $request->validated($request->all());

        $job_post = job_form::create([
            'title' => $request->title,
            'due_date' => $request->due_date,
            'description' => $request->description,
            'company_id' => $request->company_id,
            'skills' => $request->skills,
        ]);


        return $this->success([
            'job_post' => $job_post,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/job_posted",
     *     tags={"jobs_posted"},
     *     summary="get all skills",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="get_all_post",
     * 
     *         @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status values that needed to be considered for filter",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="string",
     *             
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     * )
     */


    public function get_all_post()
    {
        $job_post = job_form::get();

        return $this->success([
            'job_post' => $job_post,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/company/update_job_forms/{job_id}",
     *     tags={"update job form display"},
     *     summary="update job form display",
     *     
     *     operationId="update_job_form_display",
     * 
     *         @OA\Parameter(
     *         name="status",
     *         in="path",
     *         description="job id",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="string",
     *             
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     * )
     */
    

    public function update_job_form_display($job_id)
    {
        $job_form = job_form::find($job_id);
        return $this->success([
            'job_post' => $job_form,
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/company/update_job_form/{job_id}",
     *     tags={"update jobform"},
     *     summary="update job form to add edit job post",
     *     operationId="update_job_form",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *    @OA\Parameter(
     *         name="job_id",
     *         in="path",
     *         description="job form id",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="integer",
     *             
     *         ),
     * ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     * 
     *                 @OA\Property(
     *                     property="title",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="description",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="company_id",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="due_date",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="skills",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     )
     * )
     */

    public function update_job_form(StoreCompanyForm $request, $job_id)
    {
        $request->validated($request->all());
        $user = Auth::user();
        if($user->id == $request->company_id) {
            DB::update('update job_posts set title = ?,due_date=?,description=?,company_id=?,skills=? where job_id = ?',
                [$request->title,$request->due_date,$request->description, $request->company_id, $request->skills, $job_id]);

            $job_post = job_form::find($job_id);
            return $this->success([
                'job_post' => $job_post,
            ]);
        }
        else{
            return response()->json([
                'message' => 'You are not allowed to perform this operation',
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/company/delete_job_form/{job_id}",
     *     tags={"delete job form"},
     *     summary="delete job form posted by a given company",
     *     
     *     operationId="delete_job_form",
     * 
     *         @OA\Parameter(
     *         name="status",
     *         in="path",
     *         description="job id",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="string",
     *             
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     * )
     */


    public function delete_job_form($job_id)
    {   
        $user = Auth::user();
        $job_post = job_form::find($job_id);
        if($job_post->company_id == $user->id){
            job_form::find($job_id)->delete();

            //to delete all the users form associated with this job
            $user_forms = Intern_form::where('job_id',$job_id)->get();
            foreach($user_forms as $user_form){
                $file_ = $user_form->pdf;
                $user_form = Intern_form::find($user_form->id)->delete();

                $file_path = public_path().'/storage/pdf/';
                $file_pat = storage_path().'/app/public/pdf/';
                // dd($image_pat);
                $file = $file_path . $file_;
                $file1 = $file_pat . $file_;
                if(file_exists($file))
                {
                    unlink($file);
                }
                if(file_exists($file1))
                {
                    unlink($file1);
                }
            }


            return response()->json('Successfully deleted!');
        }
        else{
            return response()->json([
                'message' => 'You are not allowed to perform this operation',
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/company/job_list",
     *     tags={"get job posted by given company"},
     *     summary="get job posted by given company",
     *     security={{"sanctum":{}}},
     *     operationId="job_list",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */


    public function job_list()
    {
        $company = Auth::user();
        $job_posted = job_form::where('company_id', $company->id)->get();
        return $this->success([
            'job_post' => $job_posted,
        ]);

    }

    /**
     * @OA\Post(
     *     path="/api/company/expired_job_list",
     *     tags={"get expired job posted by given company"},
     *     summary="get expired job posted by given company",
     *     security={{"sanctum":{}}},
     *     operationId="get_exp_job_list",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function get_exp_job_list()
    {
        $company = Auth::user('company');
        $expjoblist = DB::table('expjobpost')->where('company_id', $company->id)->get();
        return $this->success([
            'job_list' => $expjoblist,
        ]);
    }


    /**
     * @OA\post(
     *     path="/api/company/expired_job_list/{job_id}",
     *     tags={"get all user form from a expired job"},
     *     summary="get all user form for a given expired job post",
     *     security={{"sanctum":{}}},
     *     operationId="get_applied_user_form_exp_job",
     *         @OA\Parameter(
     *         name="job_id",
     *         in="path",
     *         description="job post id",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="integer",
     *             
     *         ),
     *     ),
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function get_applied_user_form_exp_job($job_id)
    {
        $job_posted_by_which_company = DB::table('expjobpost')->where('job_id', $job_id)->first();
        $company = Auth::user('company');
        if($job_posted_by_which_company->company_id == $company->id)
        {
        $applied_users = DB::select("select * from expuserform where job_id = $job_id");
        return $this->success([
            'applied_users_form' => $applied_users,
        ]);
        }
        else {
            return response()->json([
                'users' => null,
            ]);
        }
    }

    /**
     * @OA\post(
     *     path="/api/company/expired_job_list/pdf/{job_id}/{id}",
     *     tags={"get user form pdf from exp job"},
     *     summary="get user form  pdf for a given job post",
     *     security={{"sanctum":{}}},
     *     operationId="user_exp_form_pdf",
     *         @OA\Parameter(
     *         name="job_id",
     *         in="path",
     *         description="exp job post id",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="integer",
     *             
     *         ),
     *     ),
     *         @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="exp user form id",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="integer",
     *             
     *         ),
     *     ),
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function user_exp_form_pdf($job_id, $id)
    {
        $job_posted_by_which_company = DB::table('expjobpost')->where('job_id', $job_id)->first();
        $company = Auth::user('company');
        if($job_posted_by_which_company->company_id == $company->id)
        {
            $user_form = DB::table('expuserform')->where('id', $id)->first();
            return  response()->download(public_path('storage\\pdf\\' . $user_form->pdf), $user_form->pdf);
        }
        else {
            return response()->json([
                'users' => null,
            ]);
        }
    }

    /**
     * @OA\post(
     *     path="/api/company/expired_job_user_detail/{job_id}/{id}",
     *     tags={"get user details"},
     *     summary="get user form  details who has applied for a given job post tht has been expired",
     *     security={{"sanctum":{}}},
     *     operationId="user_details_exp",
     *         @OA\Parameter(
     *         name="job_id",
     *         in="path",
     *         description="job post id",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="integer",
     *             
     *         ),
     *     ),
     *         @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="integer",
     *             
     *         ),
     *     ),
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function user_details_exp($job_id, $id)//id is user id
    {
        $job_posted_by_which_company = DB::table('expjobpost')->where('job_id', $job_id)->first();
        $company = Auth::user('company');
        if($job_posted_by_which_company->company_id == $company->id)
        {
            $user = User::find($id);
            return response()->json([
                'user' => $user,
            ]);
        }
        else {
            return response()->json([
                'users' => 'null',
            ]);
        }
    }
}
