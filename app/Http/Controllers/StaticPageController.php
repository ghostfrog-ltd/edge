<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function terms(): View
    {
        return view('terms', [
            'terms' => Str::markdown(File::get(resource_path('markdown/terms.md'))),
        ]);
    }

    public function policy(): View
    {
        return view('policy', [
            'policy' => Str::markdown(File::get(resource_path('markdown/policy.md'))),
        ]);
    }

    public function contact(): View
    {
        return view('contact');
    }
}
