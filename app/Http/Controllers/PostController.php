<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\PostView;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PostController extends Controller
{
    public function home(): View
    {
        $posts = Post::query()->where('active','=','1')
                              ->where('published_at','<',Carbon::now())
                              ->orderBy('published_at','desc')
                              ->paginate(5);
        //lastest Post



        // most 3 popular posts


        //show recommended posts(authorized person)



        //popular posts base on view


        //recent categories with latest posts



        return view('home');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Post $post,Request $request)
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

        $user = $request->user();

        PostView::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'post_id' => $post->id,
            'user_id' => $user?->id
        ]);

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
