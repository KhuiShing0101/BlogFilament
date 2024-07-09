<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\PostView;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function home(): View
    {
        //lastest Post
        $latest_post = Post::query()->where('active','=','1')
                        ->where('published_at','<',Carbon::now())
                        ->orderBy('published_at','desc')
                        ->limit(1)
                        ->first();
        // dd($latest_post);

        // most 3 popular posts
        $popular_posts = Post::query()
                            ->leftJoin('upvote_downvotes','post_id','=','upvote_downvotes.post_id')
                            ->select('posts.*', DB::raw('COUNT(upvote_downvotes.id) as upvote_count'))
                            ->where(function ($query){
                                $query->whereNull('upvote_downvotes.is_upvote')
                                      ->orWhere('upvote_downvotes.is_upvote','=',1);
                            })
                            ->where('active','=',1)
                            ->whereDate('published_at','<',Carbon::now())
                            ->orderByDesc('upvote_count')
                            ->groupBy([
                                'posts.id',
                                'posts.title',
                                'posts.slug',
                                'posts.thumbnail',
                                'posts.body',
                                'posts.active',
                                'posts.published_at',
                                'posts.user_id',
                                'posts.created_at',
                                'posts.updated_at',
                                'posts.meta_title',
                                'posts.meta_description',
                            ])
                            ->limit(5)
                            ->get();


        //show recommended posts(authorized person)
        $user = auth()->user();
        if ($user) {
            $user_id = $user->id;
            

            $leftJoin = "(SELECT cp.category_id, cp.post_id FROM upvote_downvotes
                        JOIN category_post cp ON upvote_downvotes.post_id = cp.post_id
                        WHERE upvote_downvotes.is_upvote = 1 and upvote_downvotes.user_id = ?) as t";

            $recommended_posts = Post::query()
                ->leftJoin('category_post as cp', 'posts.id', '=', 'cp.post_id')
                ->leftJoin(DB::raw($leftJoin), function ($join) {
                    $join->on('t.category_id', '=', 'cp.category_id')
                        ->whereColumn('t.post_id', '<>', 'cp.post_id');
                })
                ->select('posts.*')
                ->where('posts.id', '<>', DB::raw('t.post_id'))
                ->setBindings([$user_id])
                ->limit(3)
                ->get();
        }else {
            $recommended_posts = Post::query()
                ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
                ->select('posts.*', DB::raw('COUNT(post_views.id) as viewCount'))
                ->where('active', '=', 1)
                ->whereDate('published_at', '<', Carbon::now())
                ->orderByDesc('viewCount')
                ->groupBy([
                    'posts.id',
                    'posts.title',
                    'posts.slug',
                    'posts.thumbnail',
                    'posts.body',
                    'posts.active',
                    'posts.published_at',
                    'posts.user_id',
                    'posts.created_at',
                    'posts.updated_at',
                    'posts.meta_title',
                    'posts.meta_description',
                ])
                ->limit(3)
                ->get();
        }

        //popular posts base on view

        // Show recent categories with their latest posts
        $categories = Category::query()
                    ->whereHas('posts', function ($query) {
                        $query
                            ->where('active', '=', 1)
                            ->whereDate('published_at', '<', Carbon::now());
                    })
                    ->select('categories.*')
                    ->selectRaw('MAX(posts.published_at) as max_date')
                    ->leftJoin('category_post', 'categories.id', '=', 'category_post.category_id')
                    ->leftJoin('posts', 'posts.id', '=', 'category_post.post_id')
                    ->orderByDesc('max_date')
                    ->groupBy([
                        'categories.id',
                        'categories.title',
                        'categories.slug',
                        'categories.created_at',
                        'categories.updated_at',
                    ])
                    ->limit(5)
                    ->get();
        return view('home',compact('latest_post','popular_posts','recommended_posts','categories'));
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
