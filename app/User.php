<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function generateToken()
    {
        // $this->api_token = str_random(60);
        $this->api_token = $this->genToken($this->id);
        $this->save();

        return $this->api_token;
    }

    public static function genToken($id)
    {
        Log::info('Generating token for user: ' . $id);
//        $binary = openssl_random_pseudo_bytes(22);    // 22
//        $hex = bin2hex($binary);    // 44
//        $time = time();    // 10
//        $timeEncoded = base64_encode($time);    // 16
//
//        Log::debug('bin=' . strlen($binary) . ', hex=' . strlen($hex) . ', time=' . strlen($time) . ', timeEncoded=' . strlen($timeEncoded));
//        $token = $hex . $timeEncoded;
//        Log::debug('token (' . strlen($token) . ') = ' . $token);
//        return $token;

        return bin2hex(openssl_random_pseudo_bytes(22)) . base64_encode(time());
    }
}
