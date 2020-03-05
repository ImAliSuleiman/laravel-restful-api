<?php

namespace App\Http\Controllers;

use App\Article;

class ArticlesController extends Controller
{
    public function index()
    {
        return Article::all();
    }

    public function show($id)
    {
        return Article::find($id);
    }

    public function store(Request $request)
    {
        return Article::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->update($request->all());
    }

    public function delete($id)
    {
        // Article::findOrFail($id)->delete();
        $article = Article::findOrFail($id);
        $article->delete();

        return 204;
    }
}
