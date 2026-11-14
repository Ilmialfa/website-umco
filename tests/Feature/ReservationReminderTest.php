<?php

use App\Models\Reservation;
use App\Models\User;
use App\Support\Settings;
use Carbon\Carbon;
use Filament\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Artisan;

test('reservations:remind dispatches 15-minute upcoming reminder once and flags late bookings', function () {
    Settings::set('reservation.remind_before_minutes', '15', 'reservation');
    Settings::set('reservation.grace_minutes', '15', 'reservation');
    Settings::flush();

    $cashier = User::factory()->create(['role' => 'cashier']);
    $owner = User::factory()->create(['role' => 'owner']);

    $now = Carbon::now('Asia/Jakarta')->startOfMinute();
    $inTenMinutes = $now->copy()->addMinutes(10);
    $twoHoursLater = $now->copy()->addHours(2);
    $elevenMinutesAgo = $now->copy()->subMinutes(11); // grace 15 menit => hangus 4 menit lagi (< 5 menit)

    $upcoming = Reservation::create([
        'name' => 'Dinda Upcoming',
        'phone_input' => '082248001111',
        'date' => $inTenMinutes->format('Y-m-d'),
        'time' => $inTenMinutes->format('H:i'),
        'party_size' => 3,
        'status' => 'confirmed',
    ]);

    $farFuture = Reservation::create([
        'name' => 'Budi Jauh',
        'phone_input' => '082248002222',
        'date' => $twoHoursLater->format('Y-m-d'),
        'time' => $twoHoursLater->format('H:i'),
        'party_size' => 2,
        'status' => 'confirmed',
    ]);

    $lateGuest = Reservation::create([
        'name' => 'Rafi Terlambat',
        'phone_input' => '082248003333',
        'date' => $elevenMinutesAgo->format('Y-m-d'),
        'time' => $elevenMinutesAgo->format('H:i'),
        'party_size' => 4,
        'status' => 'confirmed',
        'reminder_notified_at' => $elevenMinutesAgo->copy()->subMinutes(15),
    ]);

    Artisan::call('reservations:remind');

    $upcoming->refresh();
    $farFuture->refresh();
    $lateGuest->refresh();

    expect($upcoming->reminder_notified_at)->not->toBeNull()
        ->and($farFuture->reminder_notified_at)->toBeNull()
        ->and($lateGuest->late_notified_at)->not->toBeNull();

    // Setiap staf (cashier + owner) mendapat 2 notifikasi: upcoming reminder + late warning
    expect($cashier->notifications()->count())->toBe(2)
        ->and($owner->notifications()->count())->toBe(2);

    // Panggilan kedua tidak menggandakan notifikasi
    Artisan::call('reservations:remind');

    expect($cashier->notifications()->count())->toBe(2);
});
