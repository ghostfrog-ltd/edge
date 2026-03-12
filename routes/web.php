<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminCreditLedgerController;
use App\Http\Controllers\AdminPlanController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminRoadmapController;
use App\Http\Controllers\AdminScanListController;
use App\Http\Controllers\AdminTeamController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BriefController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
        ['loc' => route('how-it-works'), 'changefreq' => 'weekly', 'priority' => '0.9'],
        ['loc' => route('contact.show'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => route('terms.show'), 'changefreq' => 'monthly', 'priority' => '0.4'],
        ['loc' => route('policy.show'), 'changefreq' => 'monthly', 'priority' => '0.4'],
    ];

    $xml = collect($urls)
        ->map(fn (array $url) => implode('', [
            '<url>',
            '<loc>'.e($url['loc']).'</loc>',
            '<changefreq>'.$url['changefreq'].'</changefreq>',
            '<priority>'.$url['priority'].'</priority>',
            '</url>',
        ]))
        ->implode('');

    return Response::make('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$xml.'</urlset>', 200, [
        'Content-Type' => 'application/xml; charset=UTF-8',
    ]);
})->name('sitemap');

Route::get('/robots.txt', function () {
    return Response::make(
        implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Sitemap: '.route('sitemap'),
        ]),
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8']
    );
})->name('robots');

Route::get('/llms.txt', function () {
    return Response::make(view('seo.llms')->render(), 200, [
        'Content-Type' => 'text/plain; charset=UTF-8',
    ]);
})->name('llms');

Route::get('/terms', [StaticPageController::class, 'terms'])->name('terms.show');
Route::get('/privacy', [StaticPageController::class, 'policy'])->name('policy.show');
Route::get('/contact', [StaticPageController::class, 'contact'])->name('contact.show');
Route::get('/how-it-works', BriefController::class)->name('how-it-works');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::middleware('can:access-admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/roadmap', AdminRoadmapController::class)->name('roadmap');
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
        Route::get('/teams', [AdminTeamController::class, 'index'])->name('teams.index');
        Route::get('/scans', [AdminScanListController::class, 'index'])->name('scans.index');
        Route::get('/credits', [AdminCreditLedgerController::class, 'index'])->name('credits.index');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    });

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/scans', [ScanController::class, 'index'])->name('scans.index');
    Route::get('/scans/new', [ScanController::class, 'create'])->name('scans.create');
    Route::post('/scans', [ScanController::class, 'store'])->name('scans.store');
    Route::get('/scans/{scan}', [ScanController::class, 'show'])->name('scans.show');
});
