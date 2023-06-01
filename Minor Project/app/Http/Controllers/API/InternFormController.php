<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserForm;
use App\Models\Intern_form;
use App\Models\job_form;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class InternFormController extends Controller
{
    use HttpResponses;

      /**
     * @OA\Post(
     *     path="/api/user/form",
     *     tags={"submit user form"},
     *     summary="submit the user form for a particular company",
     *     security={{"sanctum":{}}},
     *     operationId="submit_form",
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
     *                     property="user_id",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="job_id",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="description",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="pdf",
     *                     description="Updated name of the pet",
     *                     type="file",
     *                 ),
     *         
     *             )
     *         )
     *     )
     * )
     */




    public function submit_form(StoreUserForm $request){
    $request->validated($request->user_id);
    $user = Auth::user();
    $jobs = Intern_form::where('user_id', $request->user_id)->get();
    // dd($jobs);
    foreach( $jobs as $job)
    {
        if($job->job_id == $request->job_id)
        {
            return response()->json([
                'message' => 'you have already applied to this job '
            ]);
        }
    }
    
        $pdf = $request->pdf;
        $pdf_name = $pdf->getClientOriginalName();
        $pdf->storeAs('public/pdf', $pdf_name);
        $pdf->move(public_path('storage/pdf'), $pdf_name);
        $user_form = Intern_form::create([
            'user_id' => $request->user_id,
            'description' => $request->description,
            'job_id' => $request->job_id,
            'pdf' => $pdf_name,
        ]);


        return $this->success([
            'user_form' => $user_form,
        ]);
    
}

    /**
     * @OA\get(
     *     path="/api/update_user_form/{job_id}",
     *     tags={"user form details by job id"},
     *     summary="user form details of applied user for a job",
     *     security={{"sanctum":{}}},
     *     operationId="user_form_display",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *         @OA\Parameter(
     *         name="job_id",
     *         in="path",
     *         description="Status values that needed to be considered for filter",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="integer",
     *             
     *         ),
     *     )
     * )
     */


    public function user_form_display($job_id)
    {
        $user = Auth::user();
        //if we are given job_id and need to find the form for the given id
        // $user_form = Intern_form::where('job_id', $job_id)->where( 'user_id', $user->id);
        $user_forms = DB::select("select * from intern_forms where job_id = $job_id and user_id = $user->id");
        // $user_form = Intern_form::find($id);
        foreach($user_forms as $user_form){}
        if($user->id == $user_form->user_id)
        {
            // return  response()->download(public_path('storage\\pdf\\' . $user_form->pdf), $user_form->pdf);
            return $this->success([
                'user_form' => $user_form,
            ]);
        }
        else
        {
            return $this->success([
                'user_form' => 'You are not allowed to see other user form',
            ]);
        }
    }

      /**
     * @OA\Post(
     *     path="/api/update_user_form/{id}",
     *     tags={"update user form with an id"},
     *     summary="update intern",
     *     security={{"sanctum":{}}},
     *     operationId="update_user_form",
     *         
     * 
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *         @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Status values that needed to be considered for filter",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="integer",
     *             
     *         ),
     *     ),
     *      
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     * 
     *                 @OA\Property(
     *                     property="user_id",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="job_id",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="description",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="pdf",
     *                     description="Updated name of the pet",
     *                     type="file",
     *                 ),
     *          
     *         
     *             )
     *         )
     *     )
     * )
     */

    public function update_user_form(StoreUserForm $request, $id)
    {
        $request->validated($request->user_id);
        $user = Auth::user();
        
        if($user->id == $request->user_id) {

        //remove the current file    
        $user_form = Intern_form::find($id);
        $file_ = $user_form->pdf;
    
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



            $pdf = $request->pdf;
            $pdf_name = $pdf->getClientOriginalName();
            $pdf->storeAs('public/pdf', $pdf_name);
            $pdf->move(public_path('storage/pdf'), $pdf_name);

            $user_form = Intern_form::create([
                'user_id' => $request->user_id,
                'description' => $request->description,
                'job_id' => $request->job_id,
                'pdf' => $pdf_name,
            ]);
            
            DB::update('update user_forms set user_id = ?,description=?,job_id=?, pdf=? where id = ?',
            [$request->user_id,$request->description,$request->job_id, $pdf_name, $id]);
            
            $user_form = Intern_form::find($id);
            return $this->success([
                'user_form' => $user_form,
            ]);
        }
    }


    /**
     * @OA\get(
     *     path="/api/user/jobs_posts",
     *     tags={"applied jobs by user"},
     *     summary="get all the job post where user has applied intern for",
     *     security={{"sanctum":{}}},
     *     operationId="get_all_applied_job_forms",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *    
     *     )
     * )
     */

    public function get_all_applied_job_forms()
    {
        $user = Auth::user('user');
        $user_forms = Intern_form::where('user_id', $user->id)->get();
        $job_posts = array('');
        foreach ($user_forms as $user_form) {

            $job_post = job_form::find($user_form->job_id);
            array_push($job_posts, $job_post);
        }

        return $this->success([
            'job_post' => $job_posts,
        ]);
    }

    /**
     * @OA\post(
     *     path="/api/delete_user_form/{id}",
     *     tags={"delete user form"},
     *     summary="delete user form",
     *     security={{"sanctum":{}}},
     *     operationId="delete_user_form",
     *         @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user form id",
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

    public function delete_user_form($id){
        $user = Auth::user();
        $user_form = Intern_form::find($id);
        if($user->id == $user_form->user_id) {
    
            $file_ = $user_form->pdf;
            $user_form = Intern_form::find($id)->delete();

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

            return response()->json('Successfully deleted!');
        }
    }

    /**
     * @OA\post(
     *     path="/api/company/job_posts/{job_id}",
     *     tags={"get all user form"},
     *     summary="get all user form for a given job post",
     *     security={{"sanctum":{}}},
     *     operationId="get_applied_user_form",
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


    //used by company (company auth is required) to find the users who have applied for the internship
    public function get_applied_user_form($job_id)
    {
        $job_posted_by_which_company = job_form::find($job_id);
        $company = Auth::user('company');
        if($job_posted_by_which_company->company_id == $company->id)
        {
        $applied_users = DB::select("select * from intern_forms where job_id = $job_id");
        return $this->success([
            'users' => $applied_users,
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
     *     path="/api/company/job_posts/{job_id}/{id}",
     *     tags={"get user form pdf"},
     *     summary="get user form  pdf for a given job post",
     *     security={{"sanctum":{}}},
     *     operationId="user_form_pdf",
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
     *         description="user form id",
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

    //get user pdf
    public function user_form_pdf($job_id, $id)// id if user form id
    {
        $job_posted_by_which_company = job_form::find($job_id);
        $company = Auth::user('company');
        if($job_posted_by_which_company->company_id == $company->id)
        {
            $user_form = Intern_form::find($id);
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
     *     path="/api/company/job_post/{job_id}/{id}",
     *     tags={"get user details"},
     *     summary="get user form  details who has applied for a given job post",
     *     security={{"sanctum":{}}},
     *     operationId="user_details",
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

    public function user_details($job_id, $id)//id is user id
    {
        $job_posted_by_which_company = job_form::find($job_id);
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
