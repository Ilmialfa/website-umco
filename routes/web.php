<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminWhatsAppRedirectController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationCalendarController;
use App\Livewire\ReservationPage;
use App\Livewire\ReservationStatusPage;
use App\Models\MenuItem;
use App\Services\InstagramFeedService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/instagram-feed', function (InstagramFeedService $instagramFeed) {
    $canRefresh = auth()->check() && request()->boolean('refresh');

    return response()->json($instagramFeed->getFeed(8, $canRefresh));
})->middleware('throttle:30,1')->name('instagram.feed');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/menu/{menuItem:slug}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/galeri', GalleryController::class)->name('galeri');
Route::get('/cerita', AboutController::class)->name('cerita');
Route::redirect('/about', '/cerita', 301);

Route::get('/reservasi', ReservationPage::class)->name('reservation');
Route::get('/reservasi/calendar/{code}', [ReservationCalendarController::class, 'download'])
    ->where('code', '[A-Za-z0-9\-]+')
    ->middleware('throttle:60,1')
    ->name('reservation.calendar');
Route::get('/reservasi/{code}', ReservationStatusPage::class)
    ->where('code', '[A-Za-z0-9\-]+')
    ->middleware('throttle:60,1')
    ->name('reservation.status');

Route::middleware('auth')
    ->get('/admin/wa/{reservation}/{template}', AdminWhatsAppRedirectController::class)
    ->name('admin.wa.redirect');

// Satukan seluruh jalur autentikasi staf & dasbor ke panel backoffice Filament.
Route::redirect('/login', '/admin/login', 302)->name('login');
Route::redirect('/dashboard', '/admin', 302)->name('dashboard');

Route::get('/sitemap.xml', function (): Response {
    $staticUrls = [
        ['loc' => url('/'), 'changefreq' => 'daily', 'priority' => '1.0'],
        ['loc' => url('/menu'), 'changefreq' => 'weekly', 'priority' => '0.9'],
        ['loc' => url('/galeri'), 'changefreq' => 'weekly', 'priority' => '0.8'],
        ['loc' => url('/cerita'), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => url('/reservasi'), 'changefreq' => 'daily', 'priority' => '0.9'],
    ];

    $menuUrls = MenuItem::query()
        ->where('is_active', true)
        ->orderBy('sort')
        ->get(['slug', 'updated_at'])
        ->map(fn (MenuItem $item): array => [
            'loc' => route('menu.show', $item->slug),
            'lastmod' => $item->updated_at?->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.7',
        ])
        ->all();

    $urls = array_merge($staticUrls, $menuUrls);

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach ($urls as $entry) {
        $xml .= "  <url>\n";
        $xml .= '    <loc>'.e($entry['loc'])."</loc>\n";
        if (! empty($entry['lastmod'])) {
            $xml .= '    <lastmod>'.e($entry['lastmod'])."</lastmod>\n";
        }
        $xml .= '    <changefreq>'.e($entry['changefreq'])."</changefreq>\n";
        $xml .= '    <priority>'.e($entry['priority'])."</priority>\n";
        $xml .= "  </url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
})->name('sitemap');
