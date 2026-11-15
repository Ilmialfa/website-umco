<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminWhatsAppRedirectController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationCalendarController;
use App\Livewire\ReservationPage;
use App\Livewire\ReservationStatusPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/menu/{menuItem:slug}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/galeri', GalleryController::class)->name('galeri');
Route::get('/cerita', AboutController::class)->name('cerita');
Route::redirect('/about', '/cerita', 301);

Route::get('/reservasi', ReservationPage::class)->name('reservation');
Route::get('/reservasi/calendar/{code}', [ReservationCalendarController::class, 'download'])->name('reservation.calendar');
Route::get('/reservasi/{code}', ReservationStatusPage::class)->name('reservation.status');

Route::middleware('auth')
    ->get('/admin/wa/{reservation}/{template}', AdminWhatsAppRedirectController::class)
    ->name('admin.wa.redirect');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__ . '/settings.php';
