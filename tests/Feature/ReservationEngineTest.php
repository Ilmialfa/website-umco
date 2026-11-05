<?php

use App\Livewire\ReservationPage;
use App\Models\ClosedDate;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Reservation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('creates Mode A reservation without DP and normalizes phone number', function () {
    $date = now()->addDay()->format('Y-m-d');

    Livewire::test(ReservationPage::class)
        ->set('mode', 'a')
        ->set('name', 'Rizky Ramadhan')
        ->set('phone', '0812-9876-5432')
        ->set('date', $date)
        ->set('time', '15:00')
        ->set('party_size', 4)
        ->set('note', 'Meja dekat jendela colokan')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('step', 'success');

    $reservation = Reservation::where('name', 'Rizky Ramadhan')->first();
    expect($reservation)->not->toBeNull()
        ->and($reservation->phone_wa)->toBe('6281298765432')
        ->and($reservation->mode)->toBe('table_only')
        ->and($reservation->status)->toBe('pending')
        ->and($reservation->payment_status)->toBe('none')
        ->and($reservation->dp_amount)->toBe(0)
        ->and($reservation->party_size)->toBe(4);
});

it('calculates Mode B DP correctly (30% min 25000 rounded up)', function () {
    Storage::fake('public');

    $category = MenuCategory::create([
        'name' => 'Kopi Utama',
        'slug' => 'kopi-utama',
        'sort' => 1,
        'is_active' => true,
    ]);

    $item1 = MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Signature Aren',
        'slug' => 'signature-aren',
        'price' => 20000,
        'is_active' => true,
        'is_available' => true,
    ]);

    $item2 = MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Rice Bowl Beef Teriyaki',
        'slug' => 'rice-bowl-beef',
        'price' => 35000,
        'is_active' => true,
        'is_available' => true,
    ]);

    // Subtotal: 2 * 20.000 + 1 * 35.000 = 75.000
    // 30% of 75.000 = 22.500 -> min is 25.000 -> DP = 25.000
    $date = now()->addDays(2)->format('Y-m-d');
    $dummyProof = UploadedFile::fake()->image('transfer.jpg');

    $test = Livewire::test(ReservationPage::class)
        ->set('mode', 'b')
        ->set('name', 'Nadia Putri')
        ->set('phone', '0852-1122-3344')
        ->set('date', $date)
        ->set('time', '16:00')
        ->set('party_size', 3)
        ->call('addItem', $item1->id)
        ->call('addItem', $item1->id)
        ->call('addItem', $item2->id)
        ->set('payment_proof', $dummyProof)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('step', 'success');

    $reservation = Reservation::where('name', 'Nadia Putri')->first();
    expect($reservation)->not->toBeNull()
        ->and($reservation->mode)->toBe('table_menu')
        ->and($reservation->subtotal)->toBe(75000)
        ->and($reservation->dp_amount)->toBe(25000)
        ->and($reservation->payment_status)->toBe('proof_uploaded')
        ->and($reservation->items)->toHaveCount(2);

    // Test a larger order where 30% > 25000
    // Subtotal = 200.000 -> 30% = 60.000
    $itemLarge = MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Sharing Platter',
        'slug' => 'sharing-platter',
        'price' => 100000,
        'is_active' => true,
        'is_available' => true,
    ]);

    $dummyProof2 = UploadedFile::fake()->image('transfer2.jpg');

    Livewire::test(ReservationPage::class)
        ->set('mode', 'b')
        ->set('name', 'Dosen Rapat')
        ->set('phone', '0813-7788-9900')
        ->set('date', $date)
        ->set('time', '17:00')
        ->set('party_size', 8)
        ->call('addItem', $itemLarge->id)
        ->call('addItem', $itemLarge->id)
        ->set('payment_proof', $dummyProof2)
        ->call('submit')
        ->assertHasNoErrors();

    $resLarge = Reservation::where('name', 'Dosen Rapat')->first();
    expect($resLarge->subtotal)->toBe(200000)
        ->and($resLarge->dp_amount)->toBe(60000);
});

it('prevents slot overbooking when capacity exceeds 40 seats', function () {
    $date = now()->addDays(3)->format('Y-m-d');
    $time = '19:00';

    // Existing bookings total 38 pax
    Reservation::create([
        'name' => 'Kelompok 1',
        'phone_input' => '081200000001',
        'date' => $date,
        'time' => $time,
        'party_size' => 20,
        'mode' => 'table_only',
        'status' => 'confirmed',
    ]);
    Reservation::create([
        'name' => 'Kelompok 2',
        'phone_input' => '081200000002',
        'date' => $date,
        'time' => $time,
        'party_size' => 18,
        'mode' => 'table_only',
        'status' => 'pending',
    ]);

    // Remaining capacity is 40 - 38 = 2
    // Attempting to book 4 pax should fail
    Livewire::test(ReservationPage::class)
        ->set('mode', 'a')
        ->set('name', 'Kelompok Baru')
        ->set('phone', '081299998888')
        ->set('date', $date)
        ->set('time', $time)
        ->set('party_size', 4)
        ->call('submit')
        ->assertHasErrors(['time']);
});

it('blocks reservations on closed dates', function () {
    $closedDate = now()->addDays(5)->format('Y-m-d');
    ClosedDate::create([
        'date' => $closedDate,
        'reason' => 'Renovasi Barista Slow Bar',
    ]);

    Livewire::test(ReservationPage::class)
        ->set('mode', 'a')
        ->set('name', 'Tamu Libur')
        ->set('phone', '081211112222')
        ->set('date', $closedDate)
        ->set('time', '14:00')
        ->set('party_size', 2)
        ->call('submit')
        ->assertHasErrors(['date']);
});
