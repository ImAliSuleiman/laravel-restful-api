<?php

namespace App\Http\Controllers;

use App\User;
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

    public function userPosts($uid = null)
    {
        if ($uid == null) {
            if (!request()->has('uid'))
                return response()->json([
                    'message' => 'Unspecified user'
                ]);

            $uid = request()->input('uid');
        }

        $posts = DB::table('users')
            ->join('posts', 'users.id', '=', 'posts.uid')
            ->select(
                'users.id as user_id', 'users.name', 'users.email',
                'posts.id as post_id', 'posts.body as post_body',
                'posts.created_at', 'posts.updated_at')
            ->where(['users.id' => $uid])
            ->get();

        if ($posts->count() > 0)
            return response()->json([
                'data' => $posts
//                'data' => [
//                    'user' => array('id' => $uid, 'email' => '', 'name' => ''),
//                ]
            ]);
        else
            return response()->json([
                'message' => 'No user post found'
            ]);
    }

    public function addPost()
    {
        if (!request()->hasHeader('Authorization'))
            abort(401, 'Unauthorized action.');

        // $token = request()->header('Authorization');
        $token = request()->bearerToken();

        Log::info('Requesting profile for token: ' . $token);

        $user = DB::table('users')->where(['api_token' => $token])->first();

        if ($user) {
            $postAdded = DB::table('posts')->insert([
                'uid' => $user->id,
                'body' => request()->input('post'),
            ]);

            if ($postAdded)
                return response()->json([
                    'message' => 'Post added'
                ]);
        } else
            return response()->json([
                'message' => 'Invalid token'
            ]);

    }

    public function delete($id)
    {
        // Article::findOrFail($id)->delete();
        // $article = Article::findOrFail($id);
        $count = DB::table('users')->delete($id);

        if ($count)
            return response()->json([
                'message' => $count . ' user(s) deleted',
            ], 204);
        else
            return response()->json([
                'message' => 'No user deleted',
            ], 204);
    }

    public function registerAlt()
    {
        $validated = request()->validate([
            'name' => 'required|string|max:50',
            'email' => ['bail', 'required', 'string', 'email', 'max:30', 'unique:users'],
            'password' => 'bail|required|string|min:8'
        ]);

        if ($validated) {
            $encrypted_password = password_hash(request()->input('password'), PASSWORD_BCRYPT);
            $isUserCreated = DB::table('users')->insert(array(
                'name' => request()->input('name'),
                'email' => request()->input('email'),
                'password' => $encrypted_password,
            ));

            if ($isUserCreated)
                return response()->json([
                    'message' => 'User created'
                ]);
            else
                return response()->json([
                    'message' => 'An error has occurred'
                ]);
        }


    }

    public function loginAlt()
    {
        $validated = request()->validate([
            'email' => ['bail', 'required', 'string', 'email', 'max:30'],
            'password' => 'bail|required|string|min:8'
        ]);

        if ($validated) {
            // $encrypted = password_hash(request()->input('password'), PASSWORD_BCRYPT);
            $user = DB::table('users')->where([
                'email' => request()->input('email'),
            ])->first();

            if ($user) {
                $encryptedPassword = $user->password;
                if (password_verify(request()->input('password'), $encryptedPassword)) {

                    $token = User::genToken($user->id);
                    $user->api_token = $token;
                    $count = DB::table('users')->where(['id' => $user->id])->update(['api_token' => $token]);

                    if ($count > 0)
                        return response()->json([
                            'data' => $user
                        ]);
                } else
                    return response()->json([
                        'message' => 'Wrong email or password'
                    ]);
            } else
                return response()->json([
                    'message' => 'Wrong email or password'
                ]);
        }

    }


}
