<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginCompanyRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    use HttpResponses;

    /**
     * @OA\Post(
     *     path="/api/company/login",
     *     tags={"login company"},
     *     summary="login admin by using email and password",
     *     operationId="login_company",
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

    public function login_company(LoginCompanyRequest $request)
    {
       $request->validated($request->all());

       if(!Auth::guard('company')->attempt(['email' => $request->email, 'password' => $request->password])){
            return $this->error('','Credentials do not match', 401);
       }

       $company = Company::where('email', $request->email)->first();
    //    $company = Auth::guard('company')->user();
       return $this->success([
        'user' => $company,
        'token' => $company->createToken( 'Minor',['company'])->plainTextToken,
       ]);
       
    }
       /**
     * @OA\Post(
     *     path="/api/company/register",
     *     tags={"Register company"},
     *     summary="Register new company",
     *     operationId="register_company",
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
     *                     property="registration_number",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     )
     * )
     */

    public function register_company(StoreCompanyRequest $request)
    {
        $request->validated($request->all());

        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'registration_number' => $request->registration_number,
            'password' => Hash::make($request->password),
        ]);


        return $this->success([
            'user' => $company,
            'token' => $company->createToken('Minor', ['company'])->plainTextToken
        ]);
    }


    /**
     * @OA\get(
     *     path="/api/company/details",
     *     tags={"company details"},
     *     summary="company details",
     *     security={{"sanctum":{}}},
     *     operationId="details_company",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */


    public function details_company()
    {
        $company = Auth::user();
        return $this->success([
            'user' => $company,
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/company/logout",
     *     tags={"logout"},
     *     summary="to logout company",
     *     security={{"sanctum":{}}},
     *     operationId="logout_company",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function logout_company(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'user logged out'
        ];
    }


    public function delete_comapny()
    {
        $company = Auth::user('user');
        Company::find($company->id)->delete();
        return $this->success([
            'message' => 'successfully deleted user',
        ]);
    }

     /**
     * @OA\Post(
     *     path="/api/company/update_profile",
     *     tags={"update company profile"},
     *     summary="update company profile",
     *     operationId="update_company_profile",
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
     *                     property="registration_number",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     )
     * )
     */

    public function update_company_profile(StoreCompanyRequest $request)
    {
        $request->validated($request->all());
        $company = Auth::user('comapny');
        $id = $company->id;
        
        $pass = Hash::make($request->password);
        DB::update('update companies set name = ?,email=?,registration_number=?,password=? where id = ?',
                [$request->name,$request->email,$request->registration_number, $pass, $id]);

        return $this->success([
            'message' => 'successfully updated company profile',
        ]);

    }
}
