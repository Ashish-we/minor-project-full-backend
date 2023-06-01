<?php

namespace App\Http\Controllers;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
class EmailVerificationController extends Controller 
{
    
    /**
     * @OA\get(
     *     path="/api/email/verification-notification",
     *     tags={"send email verification notification"},
     *     summary="to send the newly registered user email verification notification",
     *     security={{"sanctum":{}}},
     *     operationId="sendEmailVerification",
    *          @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     )
     * )
     */

    public function sendEmailVerification(Request $request)
    {
        if($request->user()->hasVerifiedEmail()){
                return[
                    'meaasge' => 'Already Verified'
                ];
        }

        $request->user()->sendEmailVerificationNotification();
        return ['status' => 'verification-link-sent'];
    }

    // public function verify(EmailVerificationRequest $request)
    // {
    //     if($request->user()->hasVerifiedEmail()){
    //         return [
    //             'message' => 'Email already verified'
    //         ];
    //     }
    //     if($request->user()->markEmailAsVerified()){
    //         event(new verified($request->user()));
    //     }

    //     return [
    //         'message' => 'Email has been verified'
    //     ];
    // }

    public function verify(Request $request, $id, $hash)
    {
        $user = Admin::find($id);// Retrieve user based on $id

        if ($user && hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
                return [
                            'message' => 'Email has been verified'
                        ];
            }
            else {
                return [
                                'message' => 'Email already verified'
                            ];
                }

            // Handle successful verification (e.g., redirect to a success page)
        } else {
            // Handle verification failure (e.g., redirect to an error page)
            return [
                'message' => 'Unable to  verify Email'
            ];
        }
    }
}
