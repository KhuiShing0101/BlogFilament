<?php

namespace App\View\Components;

use Closure;
use App\Models\Category;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class Sidebar extends Component
{
    public function __construct()
    {

    }

    public function render(): View|Closure|string
    {
        $categories = Category::query()
                    ->join('category_post','categories.id','=','category_post.category_id')
                    ->select('categories.id','categories.title','categories.slug',DB::raw('count(*) as total'))
                    ->groupBy('categories.id')
                    ->orderByDesc('total')
                    ->get();
        return view('components.sidebar',compact('categories'));
    }
}
 