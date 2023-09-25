<?php
namespace App\Http\Controllers;
use Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
class GoogleController extends Controller
{
    public function signInwithGoogle()
    {
        
        // return Socialite::driver('google')->redirect();
        return response()->json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
    }
    public function callbackToGoogle()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            $existingUser = User::where('google_id', $user->id)->first();

            if ($existingUser) {
                Auth::login($existingUser);
                return redirect('http://localhost:3000/');
            } else {
           
                $user = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'email_verified_at' => now(),
                    'password' => encrypt('123456dummy')
                ]);

                Auth::login($user);
                $token = $user->createToken('authToken')->plainTextToken;

                return response()->json([
                    'user' => $user,
                    'token' => $token
                ]);
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}