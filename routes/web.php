<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationCalendarController;
use App\Livewire\ReservationPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/menu', MenuController::class)->name('menu');
Route::get('/about', AboutController::class)->name('about');
Route::get('/reservasi', ReservationPage::class)->name('reservation');
Route::get('/reservasi/calendar/{code}', [ReservationCalendarController::class, 'download'])->name('reservation.calendar');
Route::view('/specimen', 'specimen')->name('specimen');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
