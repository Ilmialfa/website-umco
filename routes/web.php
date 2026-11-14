<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationCalendarController;
use App\Livewire\ReservationPage;
use App\Livewire\ReservationStatusPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/menu', MenuController::class)->name('menu');
Route::get('/cerita', AboutController::class)->name('cerita');
Route::redirect('/about', '/cerita', 301);

Route::get('/reservasi', ReservationPage::class)->name('reservation');
Route::get('/reservasi/calendar/{code}', [ReservationCalendarController::class, 'download'])->name('reservation.calendar');
Route::get('/reservasi/{code}', ReservationStatusPage::class)->name('reservation.status');

require __DIR__ . '/settings.php';
