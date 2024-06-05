<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class PasswordResetController extends Controller
{
    public function password_reset_mail_send(Request $request) {
        $request->validate([
            'email'=>'required|email'
        ]);

        $email = $request->email;

        $user = User::where('email', $email)->first();
        
        if(!$user) {
            return response([
                'message'=>'This Email is not exist please chosse valid email id',
                'status'=>'failed'
            ]);
        }

        $token = Str::random(60);

        $resetpass = new PasswordResetToken();
        $resetpass->email = $email;
        $resetpass->token = $token;
        $resetpass->created_at = Carbon::now();
        $resetpass->save();

        // dd("http://127.0.0.1:8000/api/user/reset".$token);

        // save in database table
        Mail::send('reset',['token'=>$token], function(Message $message)use($email) {
            $message->subject('Password Reset Mail');
            $message->to($email);
        });

        return response([
            'message'=>'Reset link send successfully...check mail',
            'status'=>'success'
        ], 200);

    }

    public function reset(Request $request, $token) {
                
        // Delete Token older than 2 minute
        $formatted = Carbon::now()->subMinutes(2)->toDateTimeString();
        PasswordResetToken::where('created_at', '<=', $formatted)->delete();
        
        $request->validate([
            'password'=>'required|confirmed'
    ]);

    $passwordreset = PasswordResetToken::where('token', $token)->first();

    if (!$passwordreset) {
        return response([
            'message'=>'Invalid or Expire Token',
            'status'=>'failed'
        ], 400);
    }

    $user = User::where('email', $passwordreset->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    PasswordResetToken::where('email',$user->email)->delete();

    return response([
        'message'=>'Password Reset Successfully',
        'status'=>'success'
    ], 200);

    }
}
