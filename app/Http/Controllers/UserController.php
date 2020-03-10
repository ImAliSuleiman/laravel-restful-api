<?php

namespace App\Http\Controllers;

use Cassandra\Exception\UnauthorizedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function logout($id = null)
    {
        if ($id == null) {
            if (!request()->has('id'))
                return response()->json(array('data' => null, 'message' => 'Unspecified user'), 200);

            $id = request()->input('id');
        }

        $token = request()->bearerToken();
        $count = DB::table('users')->where(['id' => $id, 'api_token' => $token])->update(['api_token' => null]);

        if ($count)
            return response()->json([
                'data' => ['count' => $count],
            ], 200);
        else
            return response()->json([
                'message' => 'No user logged out by the request.',
            ], 200);

    }

    public function activeUsers()
    {
        $active = request()->input('active');

        if ($active == 1) {
            $query = 'select id, name, email from users where api_token is not null';
            $status = 'active';
        } else {
            // $query = 'select * from users where api_token is null';
            $query = 'select id, name, email from users where api_token is null';
            $status = 'inactive';
        }

        $users = DB::select($query);

        if ($users)
            return response()->json(array('data' => $users), 200);
        else
            return response()->json(array('message' => 'No ' . $status . ' users found!'), 200);
    }

    public function userInfo($id = null)
    {
        if ($id == null) {
            if (!request()->has('id'))
                return response()->json(array('data' => null, 'message' => 'Unspecified user requested!'), 200);

            $id = request()->input('id');
        }

        // $user = DB::table('users')->select(['id', 'name', 'email'])->where(['id' => $id])->first();
        $user = DB::table('users')->select(['id', 'name', 'email'])->find($id);

        if ($user)
            return response()->json(array('data' => $user), 200);
        else
            return response()->json(array('data' => null, 'message' => 'User not found.'), 200);
    }

    public function profile()
    {
        if (!request()->hasHeader('Authorization'))
            abort(401, 'Unauthorized action.');

        // $token = request()->header('Authorization');
        $token = request()->bearerToken();

        Log::info('Requesting profile for token: ' . $token);

        $user = DB::table('users')->where(['api_token' => $token])->first();

        if ($user)
            return response()->json([
                'data' => $user,
            ], 200);
        else
            return response()->json([
                'message' => 'User not found.',
            ], 200);

    }

    public function emails()
    {
        $emails = DB::table('users')->pluck('email');

        if ($emails)
            return response()->json([
                'data' => $emails,
            ], 200);
        else
            return response()->json([
                'message' => 'No emails found.',
            ], 200);
    }

}
