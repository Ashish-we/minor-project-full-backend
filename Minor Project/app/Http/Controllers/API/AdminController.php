<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Admin;
use App\Models\Company;
use App\Models\job_form;
use App\Models\Intern_form;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use HttpResponses;
    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     tags={"login admin"},
     *     summary="Register new admin",
     *     operationId="login",
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
     *           
     *                  @OA\Property(
     *                     property="email",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="password",
     *                     description="Updated name of the pet",
     *                     type="password",
     *                 ),
     *                  
     *             )
     *         )
     *     )
     * )
     */

    public function login(LoginUserRequest $request)
    {
       $request->validated($request->all());

       if(!Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])){
            return $this->error('','Credentials do not match', 401);
       }

       $admin = Admin::where('email', $request->email)->first();

       return $this->success([
        'user' => $admin,
        'token' => $admin->createToken('Minor',['admin'])->plainTextToken,
       ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/register",
     *     tags={"Register admin"},
     *     summary="Register new admin",
     *     operationId="register",
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
     *                     property="name",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="email",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="password",
     *                     description="Updated name of the pet",
     *                     type="password",
     *                 ),
     *                  @OA\Property(
     *                     property="password_confirmation",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="phone",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     )
     * )
     */

    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        route('verify_email');
        return $this->success([
            'user' => $admin,
            'token' => $admin->createToken('Minor',['admin'])->plainTextToken
        ]);
    }

    /**
     * @OA\get(
     *     path="/api/admin/details",
     *     tags={"admin details"},
     *     summary="admin details",
     *     security={{"sanctum":{}}},
     *     operationId="details",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function details()
    {
        $admin = Auth::user();
        return $this->success([
            'user' => $admin,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/logout",
     *     tags={"logout"},
     *     summary="to logout admin",
     *     security={{"sanctum":{}}},
     *     operationId="logout_admin",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */
    public function logout_admin(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'user logged out'
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/admin/updater",
     *     tags={"update admin profile"},
     *     summary="update admin profile details",
     *     operationId="update_profile",
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
     *                     property="name",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="email",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="password",
     *                     description="Updated name of the pet",
     *                     type="password",
     *                 ),
     *                  @OA\Property(
     *                     property="password_confirmation",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="phone",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     )
     * )
     */

    public function update_profile(StoreUserRequest $request)
    {
        $request->validated($request->all());
        $user = Auth::user('admin');
        $id = $user->id;
        
        $pass = Hash::make($request->password);
        DB::update('update admins set name = ?,email=?,phone=?,password=? where id = ?',
                [$request->name,$request->email,$request->phone, $pass, $id]);

        return $this->success([
            'message' => 'successfully updated Admin profile',
        ]);

    }


    /**
     * @OA\get(
     *     path="/api/admin/users_list",
     *     tags={"user list"},
     *     summary="get all the list of users",
     *     security={{"sanctum":{}}},
     *     operationId="users_list",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function user_list()
    {
        $users = User::get();
        return $this->success([
            'users' => $users,
        ]);
    }

    /**
     * @OA\get(
     *     path="/api/admin/company_list",
     *     tags={"company list"},
     *     summary="get all the list of companies",
     *     security={{"sanctum":{}}},
     *     operationId="company_list",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */


    public function company_list()
    {
        $companies = Company::get();
        return $this->success([
            'companies' => $companies,
        ]);
    }

      /**
     * @OA\Post(
     *     path="/api/admin/delete_user/{id}",
     *     tags={"delete user with id"},
     *     summary="delete intern",
     *     security={{"sanctum":{}}},
     *     operationId="delete_user_with_id",
     *         
     * 
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *      @OA\Parameter(
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
     *     )
     * )
     */

    public function delete_user_with_id($id)
    {
        User::find($id)->delete();
        return $this->success([
            'message' => 'User deleted successfully',
        ]);

    }

    /**
     * @OA\Post(
     *     path="/api/admin/delete_company/{id}",
     *     tags={"delete company with id"},
     *     summary="delete a company by admin",
     *     security={{"sanctum":{}}},
     *     operationId="delete_company_with_id",
     *         
     * 
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *      @OA\Parameter(
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
     *     )
     * )
     */

    public function delete_company_with_id($id)
    {
        Company::find($id)->delete();
        return $this->success([
            'message' => 'Company deleted successfully',
        ]);

    }

    /**
     * @OA\Post(
     *     path="/api/admin/delete_job_post/{job_id}",
     *     tags={"delete job_post with id"},
     *     summary="delete a job_post by admin",
     *     security={{"sanctum":{}}},
     *     operationId="delete_job_post_by_id",
     *         
     * 
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *      @OA\Parameter(
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

    public function delete_job_post_by_id($job_id)
    {
        job_form::find($job_id)->delete();

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
        return $this->success([
            'message' => 'job post deleted successfully along with all the usersforms',
        ]);

    }
}
