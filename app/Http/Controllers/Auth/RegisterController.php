<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function generateToken()
    {
        $binary = openssl_random_pseudo_bytes(24);
        $hex = bin2hex($binary);
        $time = time();
        $timeEncoded = base64_encode($time);

        Log::debug('bin=' . strlen($binary) . ', hex=' . strlen($hex) . ', time=' . strlen($time) . ', timeEncoded=' . strlen($timeEncoded));
        $token = $hex . $timeEncoded;
        Log::debug('token (' . strlen($token) . ') = ' . $token);

        return response()->json(array(
            'token' => $token
        ), 200);
        // return $token;
        // return bin2hex(openssl_random_pseudo_bytes(24)) . base64_encode(time());
    }

    protected function registered(Request $request, $user)
    {
        $user->generateToken();

        return response()->json([
            'data' => $user->toArray(),
        ], 201);

    }
}
