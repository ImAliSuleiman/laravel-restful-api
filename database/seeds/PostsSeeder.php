<?php

use App\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::truncate();

        $facker = \Faker\Factory::create();

        $ids = DB::table('users')->pluck('id');
        foreach ($ids as $id) {
            for ($i = 0; $i < 4; $i++) {
                Post::create([
                    'body' => $facker->text(),
                    'uid' => $id,
                ]);
            }
        }

    }
}
