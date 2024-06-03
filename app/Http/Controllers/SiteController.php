<?php

namespace App\Http\Controllers;

use App\Models\TextWidget;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function about()
    {
        $widget = TextWidget::query()
                ->where('active','=',true)
                ->where('key','=','about-us')
                ->first();
        return view('about',compact('widget'));
    }
}
