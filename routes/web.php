<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminCreditLedgerController;
use App\Http\Controllers\AdminHumanTestPlanController;
use App\Http\Controllers\AdminPlanController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminRoadmapController;
use App\Http\Controllers\AdminScanListController;
use App\Http\Controllers\AdminTeamController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BriefController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailScanAccessController;
use App\Http\Controllers\EbayCategorySuggestionController;
use App\Http\Controllers\EngineScanCallbackController;
use App\Http\Controllers\InboxNotificationController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ScanPdfController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
        ['loc' => route('pricing'), 'changefreq' => 'weekly', 'priority' => '0.8'],
        ['loc' => route('how-it-works'), 'changefreq' => 'weekly', 'priority' => '0.9'],
        ['loc' => route('support.show'), 'changefreq' => 'monthly', 'priority' => '0.6'],
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
Route::get('/contact', [SupportController::class, 'show'])->name('contact.show');
Route::get('/support', [SupportController::class, 'show'])->name('support.show');
Route::post('/support', [SupportController::class, 'store'])->name('support.store');
Route::get('/pricing', PricingController::class)->name('pricing');
Route::get('/how-it-works', BriefController::class)->name('how-it-works');
Route::post('/api/engine/scans/{scan}/callback', EngineScanCallbackController::class)->name('engine.scans.callback');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::middleware('can:access-admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/roadmap', AdminRoadmapController::class)->name('roadmap');
        Route::get('/test-plan', AdminHumanTestPlanController::class)->name('test-plan');
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
        Route::get('/plans/create', [AdminPlanController::class, 'create'])->name('plans.create');
        Route::post('/plans', [AdminPlanController::class, 'store'])->name('plans.store');
        Route::get('/plans/{offer}/edit', [AdminPlanController::class, 'edit'])->name('plans.edit');
        Route::put('/plans/{offer}', [AdminPlanController::class, 'update'])->name('plans.update');
        Route::post('/plans/{offer}/sync-stripe', [AdminPlanController::class, 'syncStripe'])->name('plans.sync-stripe');
        Route::get('/teams', [AdminTeamController::class, 'index'])->name('teams.index');
        Route::get('/scans', [AdminScanListController::class, 'index'])->name('scans.index');
        Route::get('/credits', [AdminCreditLedgerController::class, 'index'])->name('credits.index');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    });

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/subscribe/{plan}', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::post('/billing/top-up/{pack}', [BillingController::class, 'topUp'])->name('billing.top-up');
    Route::post('/billing/portal', [BillingController::class, 'portal'])->name('billing.portal');
    Route::get('/email/scan-access/{scan}', EmailScanAccessController::class)
        ->middleware('signed')
        ->name('email.scans.access');
    Route::get('/notifications', [InboxNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [InboxNotificationController::class, 'read'])->name('notifications.read');
    Route::get('/notifications/unread-count', [InboxNotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/scans/ebay-category-suggestions', EbayCategorySuggestionController::class)->name('scans.ebay-category-suggestions');
    Route::get('/scans', [ScanController::class, 'index'])->name('scans.index');
    Route::get('/scans/new', [ScanController::class, 'create'])->name('scans.create');
    Route::post('/scans', [ScanController::class, 'store'])->name('scans.store');
    Route::get('/scans/{scan}/submitted', [ScanController::class, 'submitted'])->name('scans.submitted');
    Route::get('/scans/{scan}/submitted-status', [ScanController::class, 'submittedStatus'])->name('scans.submitted-status');
    Route::post('/scans/{scan}/retry', [ScanController::class, 'retry'])->name('scans.retry');
    Route::post('/scans/{scan}/feedback', [ScanController::class, 'feedback'])->name('scans.feedback');
    Route::get('/scans/{scan}/pdf', ScanPdfController::class)->name('scans.pdf');
    Route::get('/scans/{scan}', [ScanController::class, 'show'])->name('scans.show');
});
