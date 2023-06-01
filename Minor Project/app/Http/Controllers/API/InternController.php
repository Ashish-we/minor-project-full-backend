<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginCompanyRequest;
use App\Http\Requests\LoginInternRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\StoreInternRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
class InternController extends Controller
{
    
    use HttpResponses;

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"login intern"},
     *     summary="login intern",
     *     operationId="login_intern",
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

    public function login_intern(LoginInternRequest $request)
    {
       $request->validated($request->all());

       if(!Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])){
            return $this->error('','Credentials do not match', 401);
       }

       $intern = User::where('email', $request->email)->first();

       return $this->success([
        'user' => $intern,
        'token' => $intern->createToken('Minor',['user'])->plainTextToken,
       ]);
        
        // $token = $intern->createToken('Minor',['user'])->plainTextToken;
        // return response()
        // ->json(['success' => 'success'], 200)   // JsonResponse object
        // ->withCookie(cookie('token', $token, $minute = 10));
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Register intern"},
     *     summary="Register new intern",
     *     operationId="register_intern",
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
     *                     property="skills",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     )
     * )
     */

    public function register_intern(StoreInternRequest $request)
    {
        $request->validated($request->all());

        $intern = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'skills' => $request->skills,
        ]);


        return $this->success([
            'user' => $intern,
            'token' => $intern->createToken('Minor',['user'])->plainTextToken
        ]);
    }

    /**
     * @OA\get(
     *     path="/api/details",
     *     tags={"user details"},
     *     summary="intern details",
     *     security={{"sanctum":{}}},
     *     operationId="details_intern",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function details_intern()
    {
        $intern = Auth::user();
        return $this->success([
            'user' => $intern,
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/user/logout",
     *     tags={"logout"},
     *     summary="Add skills",
     *     security={{"sanctum":{}}},
     *     operationId="logout",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'user logged out'
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/user/updater",
     *     tags={"update intern profile"},
     *     summary="update intern profile details",
     *     operationId="update_profile_intern",
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
     *                     property="skills",
     *                     description="Updated name of the pet",
     *                     type="string",
     *                 ),
     *             )
     *         )
     *     )
     * )
     */

    public function update_profile_intern(StoreInternRequest $request)
    {
        $request->validated($request->all());
        $user = Auth::user('user');
        $id = $user->id;
        
        $pass = Hash::make($request->password);
        DB::update('update users set name = ?,email=?,phone=?,skills=?,password=? where id = ?',
                [$request->name,$request->email,$request->phone,$request->skills, $pass, $id]);

        return $this->success([
            'message' => 'successfully updated user profile',
        ]);

    }

    /**
     * @OA\Post(
     *     path="/api/user/delete",
     *     tags={"delete intern"},
     *     summary="delete intern",
     *     security={{"sanctum":{}}},
     *     operationId="delete_user",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function delete_user()
    {
        $user = Auth::user('user');
        User::find($user->id)->delete();
        return $this->success([
            'message' => 'successfully deleted user',
        ]);
    }

}
