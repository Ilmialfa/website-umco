<?php

use App\Actions\Reservations\CreateReservation;
use App\Actions\Reservations\QuoteReservation;
use App\Jobs\NotifyUmcoWhatsApp;
use App\Livewire\ReservationPage;
use App\Livewire\ReservationStatusPage;
use App\Models\ActivityLog;
use App\Models\ClosedDate;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\SeatingOption;
use App\Models\SpendTier;
use App\Models\User;
use App\Notifications\NewReservationNotification;
use App\Support\Settings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');

    SpendTier::create(['party_min' => 2, 'party_max' => 2, 'min_spend' => 50000]);
    SpendTier::create(['party_min' => 3, 'party_max' => 3, 'min_spend' => 100000]);
    SpendTier::create(['party_min' => 4, 'party_max' => 4, 'min_spend' => 125000]);
    SpendTier::create(['party_min' => 5, 'party_max' => 5, 'min_spend' => 150000]);
    SpendTier::create(['party_min' => 6, 'party_max' => null, 'min_spend' => 175000, 'per_extra_person' => 25000]);

    Settings::set('hours.opening_time', '08:00', 'hours');
    Settings::set('hours.closing_time', '22:00', 'hours');
    Settings::set('reservation.min_party', '2', 'reservation');
    Settings::set('reservation.dp_percentage', '50', 'reservation');
    Settings::set('reservation.capacity_per_slot', '40', 'reservation');
    Settings::set('reservation.menu_required', '1', 'reservation');
    Settings::set('reservation.full_payment_enabled', '1', 'reservation');
    Settings::set('contact.business_wa', '02248001571', 'contact');
    Settings::flush();
});

function createSampleMenus(): array
{
    $cat = MenuCategory::create(['name' => 'Signature', 'slug' => 'signature', 'is_active' => true, 'sort' => 1]);
    $coffee = MenuItem::create([
        'category_id' => $cat->id,
        'name' => 'Kopi Susu UMCO',
        'slug' => 'kopi-susu-umco',
        'price' => 25000,
        'has_temperature' => true,
        'is_active' => true,
        'is_available' => true,
    ]);
    $pasta = MenuItem::create([
        'category_id' => $cat->id,
        'name' => 'Mie Lava UMCO',
        'slug' => 'mie-lava-umco',
        'price' => 23500,
        'has_temperature' => false,
        'is_active' => true,
        'is_available' => true,
    ]);

    return [$coffee, $pasta];
}

test('calculates tiered minimum spend and rounds 50% DP up to nearest 1000 on server', function () {
    [$coffee, $pasta] = createSampleMenus();

    expect(SpendTier::minSpendFor(2))->toBe(50000)
        ->and(SpendTier::minSpendFor(3))->toBe(100000)
        ->and(SpendTier::minSpendFor(4))->toBe(125000)
        ->and(SpendTier::minSpendFor(5))->toBe(150000)
        ->and(SpendTier::minSpendFor(6))->toBe(175000)
        ->and(SpendTier::minSpendFor(7))->toBe(200000);

    // 2 coffee (50.000) + 2 pasta (47.000) = 97.000 => 50% = 48.500 => rounded up = 49.000
    $quote = QuoteReservation::calculate(3, [
        ['menu_item_id' => $coffee->id, 'qty' => 2],
        ['menu_item_id' => $pasta->id, 'qty' => 2],
    ], 'dp');

    expect($quote['subtotal'])->toBe(97000)
        ->and($quote['min_spend'])->toBe(100000)
        ->and($quote['shortfall'])->toBe(3000)
        ->and($quote['meets_min_spend'])->toBeFalse()
        ->and($quote['dp_percentage'])->toBe(50)
        ->and($quote['dp_amount'])->toBe(49000)
        ->and($quote['amount_due'])->toBe(49000);

    // Enforce minimum 50% even if settings somehow contain lower value
    Settings::set('reservation.dp_percentage', '20', 'reservation');
    Settings::flush();

    $quoteFull = QuoteReservation::calculate(2, [
        ['menu_item_id' => $coffee->id, 'qty' => 2],
    ], 'full');

    expect($quoteFull['dp_percentage'])->toBe(50)
        ->and($quoteFull['dp_amount'])->toBe(25000)
        ->and($quoteFull['amount_due'])->toBe(50000);
});

test('rejects reservation below 2 people, empty menu, below minimum spend, missing proof, closed date, and full slot', function () {
    [$coffee] = createSampleMenus();
    $tomorrow = now('Asia/Jakarta')->addDay()->format('Y-m-d');
    $proof = UploadedFile::fake()->image('bukti.jpg', 600, 800);

    $base = [
        'source' => 'web',
        'date' => $tomorrow,
        'time' => '19:00',
        'party_size' => 2,
        'name' => 'Dinda Kirana',
        'phone_input' => '082212345678',
        'payment_type' => 'dp',
        'items' => [['menu_item_id' => $coffee->id, 'qty' => 2]],
    ];

    // < 2 orang
    expect(fn () => CreateReservation::execute([...$base, 'party_size' => 1], $proof))
        ->toThrow(ValidationException::class);

    // Menu kosong
    expect(fn () => CreateReservation::execute([...$base, 'items' => []], $proof))
        ->toThrow(ValidationException::class);

    // Kurang minimal belanja (3 orang = 100k, hanya pesan 50k)
    expect(fn () => CreateReservation::execute([...$base, 'party_size' => 3], $proof))
        ->toThrow(ValidationException::class);

    // Tanpa bukti bayar
    expect(fn () => CreateReservation::execute($base, null))
        ->toThrow(ValidationException::class);

    // Hari tutup
    $closedDay = now('Asia/Jakarta')->addDays(3)->format('Y-m-d');
    ClosedDate::create(['date' => $closedDay, 'reason' => 'Libur Lebaran']);
    expect(fn () => CreateReservation::execute([...$base, 'date' => $closedDay], $proof))
        ->toThrow(ValidationException::class);

    // Slot penuh (sudah ada 38 kursi di 19:00, masuk 3 orang => 41 > 40)
    Reservation::create([
        'name' => 'Grup Besar',
        'phone_input' => '081234567890',
        'date' => $tomorrow,
        'time' => '19:00',
        'party_size' => 38,
        'status' => 'confirmed',
    ]);
    expect(fn () => CreateReservation::execute([
        ...$base,
        'party_size' => 3,
        'items' => [['menu_item_id' => $coffee->id, 'qty' => 4]],
    ], $proof))->toThrow(ValidationException::class);
});

test('creates reservation via CreateReservation action, saves items and proof, notifies staff, and dispatches WhatsApp notification to 622248001571', function () {
    Notification::fake();
    Bus::fake([NotifyUmcoWhatsApp::class]);

    [$coffee] = createSampleMenus();
    $seating = SeatingOption::create(['label' => 'Dekat colokan', 'sort' => 1, 'is_active' => true]);
    $owner = User::factory()->create(['role' => 'owner']);
    $cashier = User::factory()->create(['role' => 'cashier']);

    $tomorrow = now('Asia/Jakarta')->addDay()->format('Y-m-d');
    $proof = UploadedFile::fake()->image('bukti-qris.jpg', 800, 1000);

    $reservation = CreateReservation::execute([
        'source' => 'web',
        'date' => $tomorrow,
        'time' => '18:30',
        'party_size' => 3,
        'seating_option_id' => $seating->id,
        'seating_note' => 'Dekat colokan 3 lubang ya',
        'name' => 'Rafi Mahasiswa',
        'phone_input' => '+62 0822-4800-1571',
        'payment_type' => 'dp',
        'items' => [
            ['menu_item_id' => $coffee->id, 'temperature' => 'Es', 'qty' => 4], // 100.000
        ],
    ], $proof);

    expect($reservation->code)->toStartWith('UMCO-')
        ->and($reservation->phone_wa)->toBe('6282248001571')
        ->and($reservation->subtotal)->toBe(100000)
        ->and($reservation->min_spend_snapshot)->toBe(100000)
        ->and($reservation->dp_amount)->toBe(50000)
        ->and($reservation->amount_due)->toBe(50000)
        ->and($reservation->status)->toBe('pending')
        ->and($reservation->payment_status)->toBe('proof_uploaded')
        ->and($reservation->items)->toHaveCount(1);

    Storage::disk('public')->assertExists($reservation->payment_proof_path);

    expect(ActivityLog::where('action', 'reservation.created')->where('subject_id', $reservation->id)->exists())->toBeTrue();

    Notification::assertSentTo([$owner, $cashier], NewReservationNotification::class);
    Bus::assertDispatched(NotifyUmcoWhatsApp::class);

    $waLink = NotifyUmcoWhatsApp::waLink($reservation);
    expect($waLink)->toStartWith('https://wa.me/622248001571?text=')
        ->and(NotifyUmcoWhatsApp::buildMessage($reservation))->toContain($reservation->code);
});

test('public Livewire ReservationPage guides through 5 steps and prints ticket', function () {
    Notification::fake();
    Bus::fake([NotifyUmcoWhatsApp::class]);

    [$coffee] = createSampleMenus();
    $seating = SeatingOption::create(['label' => 'Sofa', 'sort' => 1, 'is_active' => true]);
    $tomorrow = now('Asia/Jakarta')->addDay()->format('Y-m-d');
    $proof = UploadedFile::fake()->image('transfer.png', 700, 900);

    Livewire::withQueryParams(['date' => $tomorrow, 'time' => '14:00', 'party' => 2])
        ->test(ReservationPage::class)
        ->call('nextStep') // -> 2
        ->set('seatingOptionId', $seating->id)
        ->call('nextStep') // -> 3
        ->call('addMenu', $coffee->id)
        ->call('addMenu', $coffee->id) // 50.000 memenuhi min 2 orang
        ->call('nextStep') // -> 4
        ->set('name', 'Alya Putri')
        ->set('phoneInput', '081234567890')
        ->call('nextStep') // -> 5
        ->set('paymentProof', $proof)
        ->set('acceptedRules', true)
        ->call('submitReservation')
        ->assertSet('step', 6)
        ->assertSee('Karcismu sudah tercatat.');

    expect(Reservation::where('name', 'Alya Putri')->exists())->toBeTrue();
});

test('ReservationStatusPage gates ticket behind last 4 digits of WA and supports proof reupload when rejected', function () {
    Notification::fake();
    Bus::fake([NotifyUmcoWhatsApp::class]);

    $reservation = Reservation::create([
        'name' => 'Dinda Test',
        'phone_input' => '082248005678',
        'date' => now('Asia/Jakarta')->addDay()->format('Y-m-d'),
        'time' => '16:00',
        'party_size' => 2,
        'status' => 'pending',
        'payment_status' => 'rejected',
        'payment_type' => 'dp',
        'subtotal' => 60000,
        'min_spend_snapshot' => 50000,
        'dp_amount' => 30000,
        'amount_due' => 30000,
        'payment_rejected_reason' => 'Nominal terpotong di foto.',
    ]);

    Livewire::test(ReservationStatusPage::class, ['code' => $reservation->code])
        ->assertSet('verified', false)
        ->set('digitsInput', '9999')
        ->call('verifyDigits')
        ->assertHasErrors(['digitsInput'])
        ->set('digitsInput', '5678')
        ->call('verifyDigits')
        ->assertSet('verified', true)
        ->assertSee('BUKTI DITOLAK')
        ->assertSee('Nominal terpotong di foto.')
        ->set('newProof', UploadedFile::fake()->image('ulang.jpg', 600, 800))
        ->call('reuploadProof')
        ->assertSet('reuploadSuccess', true)
        ->assertSee('MENUNGGU CEK');

    expect($reservation->fresh()->payment_status)->toBe('proof_uploaded');
});
