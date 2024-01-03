<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $posts = Post::query()->where('active','=','1')
                              ->where('published_at','<',Carbon::now())
                              ->orderBy('published_at','desc')
                              ->paginate(5);
        return view('home',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if(!$post->active || $post->published_at > Carbon::now())
        {
            throw new NotFoundHttpException();
        }

        $next = Post::query()
                ->where('active',true)
                ->whereDate('published_at','<=',Carbon::now())
                ->whereDate('published_at','<',$post->published_at)
                ->orderBy('published_at','desc')
                ->limit(1)
                ->first();
        
        $prev = Post::query()
                ->where('active',true)
                ->whereDate('published_at','<=',Carbon::now())
                ->whereDate('published_at','>',$post->published_at)
                ->orderBy('published_at','asc')
                ->limit(1)
                ->first();

        return view('post.view',compact('post','next','prev'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
