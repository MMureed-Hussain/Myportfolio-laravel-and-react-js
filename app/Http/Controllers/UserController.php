<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AffiliationReward;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Mail;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['access_token' => $token], 200);
        }

        return response()->json(['message' => 'Invalid email or password'], 401);
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $userInfo = DB::table('users')->where('email', $request->email)->select('email')->first();

        if (!empty($userInfo)) {
            $token = Str::random(64);
            $resetUrl = url('/api/password/reset/' . $token);

            Mail::send('email.reset-password', ['token' => $token, 'resetUrl' => $resetUrl], function ($message) use ($request) {
                $message->subject('Password Reset');
                $message->to($request->email);
            });

            DB::table('reset_passwords')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password reset link sent to your email',
                'reset_url' => $resetUrl,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, that email was not found in our records',
            ], 401);
        }
    }
    public function getemail_fromtoken(Request $request, $token)
    {
        $data = DB::table('reset_passwords')->where('token', $token)->first();

        if ($data) {
            return view('reset-form', ['token' => $token, 'email' => $data->email]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token!',

            ], 401);
        }
    }

    public function submitResetPasswordForm(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6',
            'confirmpassword' => 'required|min:6|same:password',
        ]);

        $updatePassword = DB::table('reset_passwords')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {

            return response()->json([
                'status' => false,
                'message' => 'Invalid token!',
            ], 401);
        }
        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('reset_passwords')->where(['email' => $request->email])->delete();
        return redirect("http://localhost:3000/login")->with([
            'status' => true,
            'message' => 'Your password has been updated. Please login again!'
        ]);

    }
}
