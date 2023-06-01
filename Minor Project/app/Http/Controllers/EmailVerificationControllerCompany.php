<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationControllerCompany extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = Company::find($id);// Retrieve user based on $id

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
