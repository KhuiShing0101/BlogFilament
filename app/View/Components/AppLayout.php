<?php

namespace App\View\Components;

use Closure;
use App\Models\Category;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class AppLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct( public ?string $metaTitle = null, public ?string $metaDescription = null)
    {
        //
    }

    public function render(): View|Closure|string
    {
        $categories = Category::query()
                    ->join('category_post','categories.id','=','category_post.category_id')
                    ->select('categories.id','categories.title','categories.slug',DB::raw('count(*) as Total'))
                    ->groupBy('categories.id')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get();
        return view('layouts.app',compact('categories'));
    }
}
