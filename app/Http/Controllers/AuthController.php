<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|confirmed',
            'phone' => 'required'
        ]);

//        $data['password'] = Crypt::encryptString($data['password']);

        $data['type'] = 'visitor';
        $data['phone'] = $request->country . $request->phone;
        $data['whatsapp'] = $data['phone'];

        $user = User::create($data);

//        $token = $user->createToken('food')->plainTextToken;

        return response()->json([
            'user' => $user,
//            'access_token' => $token
        ]);
    }

    public function login(Request $request)
    {

        $this->validate($request, [
//            'email' => 'required',
//            'username' => 'required',
            'password' => 'required'
        ]);
        if ($request->input('email')) {
            $user = User::whereEmail($request->input('email'))
               ->orWhere('username', $request->input('email'))->first();
        } elseif ($request->input('username')) {
            $user = User::where('username', $request->input('username'))
                ->whereApproved(1)->first();
        }

        if (!$user) {
            return response()->json(['msg' => trans('auth.failed')], 401);
        }


        if ($request->input('username')) {
            if ($user->password == $request->password) {
                $token = $user->createToken('food')->plainTextToken;
                return response()->json([
                    'user' => $user,
                    'access_token' => $token
                ]);
            } else {

                return response()->json(['msg' => 'Wrong Password'], 401);
            }
        }
        if (Crypt::decryptString($user->password) == $request->password) {

            $token = $user->createToken('food')->plainTextToken;


            return response()->json([
                'user' => $user,
                'access_token' => $token
            ]);
        } else {

            return response()->json(['msg' => 'Wrong Password'], 401);
        }
    }

    public function submitForgetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        Mail::send('email.forgetPassword', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json([
            "message" => "password reset mail sent",
        ], Response::HTTP_OK);
    }

    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required'
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {
            return response()->json([
                "message" => "password token is invalid",
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        User::where('email', $request->email)
            ->update(['password' => Crypt::encryptString($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return response()->json([
            "message" => "password changed",
        ], Response::HTTP_OK);;
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        $request->user()->device_token = null;
        $request->user()->save();

        return response()->json([
            "message" => __("responses.logout"),
        ], Response::HTTP_OK);
    }
}
