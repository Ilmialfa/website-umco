<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminWhatsAppRedirectController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationCalendarController;
use App\Livewire\ReservationPage;
use App\Livewire\ReservationStatusPage;
use App\Services\InstagramFeedService;
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
