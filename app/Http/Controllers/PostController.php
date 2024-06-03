<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()->where('active','=','1')
                              ->where('published_at','<',Carbon::now())
                              ->orderBy('published_at','desc')
                              ->paginate(5);
        return view('home',compact('posts'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

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

    public function byCategory(Category $category)
    {
        $posts = Post::query()
                ->leftJoin('category_post','posts.id','=','category_post.post_id')
                ->where('category_post.category_id','=',$category->id)
                ->where('active','=',true)
                ->whereDate('published_at','<=',Carbon::now())
                ->orderBy('published_at','desc')
                ->paginate('10');
        return view('post.index',compact('posts','category'));
    }

} 
