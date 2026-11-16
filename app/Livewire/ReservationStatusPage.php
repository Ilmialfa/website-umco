<?php

namespace App\Livewire;

use App\Jobs\NotifyUmcoWhatsApp;
use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\NewReservationNotification;
use App\Support\PhoneNumber;
use App\Support\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * @property-read Reservation $reservation
 */
#[Layout('components.layouts.public')]
#[Title('Status Karcis — UMCO House')]
class ReservationStatusPage extends Component
{
    use WithFileUploads;

    #[Locked]
    public string $code = '';

    public string $digitsInput = '';

    #[Locked]
    public bool $verified = false;

    /** @var TemporaryUploadedFile|null */
    public $newProof = null;

    public bool $reuploadSuccess = false;

    public function mount(string $code): void
    {
        $reservation = Reservation::where('code', $code)->firstOrFail();
        $this->code = $reservation->code;

        if (session()->get("umco_status_verified_{$this->code}") === true) {
            $this->verified = true;
        }
    }

    #[Computed]
    public function reservation(): Reservation
    {
        return Reservation::query()
            ->with(['items', 'seatingOption'])
            ->where('code', $this->code)
            ->firstOrFail();
    }

    public function verifyDigits(): void
    {
        $this->resetErrorBag();
        $throttleKey = 'umco:status-verify:'.$this->code.':'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 6)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('digitsInput', "Terlalu banyak percobaan. Tunggu {$seconds} detik ya.");

            return;
        }

        $input = preg_replace('/\D+/', '', $this->digitsInput);
        if (strlen($input) !== 4) {
            $this->addError('digitsInput', 'Masukkan tepat 4 digit terakhir nomor WhatsApp kamu.');

            return;
        }

        $reservation = $this->reservation;
        $last4 = substr((string) $reservation->phone_wa, -4);

        if ($input !== $last4) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('digitsInput', '4 digit terakhir belum cocok dengan nomor saat reservasi.');

            return;
        }

        RateLimiter::clear($throttleKey);
        session()->put("umco_status_verified_{$this->code}", true);
        $this->verified = true;
    }

    public function refreshStatus(): void
    {
        unset($this->reservation);
    }

    public function reuploadProof(): void
    {
        $this->resetErrorBag();
        if (! $this->verified) {
            abort(403);
        }

        $throttleKey = 'umco:proof-reupload:'.$this->code.':'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('newProof', "Terlalu sering mengunggah. Tunggu {$seconds} detik ya.");

            return;
        }

        $reservation = $this->reservation;
        if ($reservation->payment_status !== 'rejected') {
            return;
        }

        $this->validate([
            'newProof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,heic,heif', 'max:5120'],
        ], [
            'newProof.required' => 'Pilih file bukti pembayaran dulu.',
            'newProof.mimes' => 'Format harus JPG, PNG, WebP, atau HEIC.',
            'newProof.max' => 'Ukuran maksimal 5 MB.',
        ]);

        $ext = strtolower($this->newProof?->getClientOriginalExtension() ?: $this->newProof?->extension() ?: 'jpg');
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $realPath = $this->newProof?->getRealPath();
            if (! $realPath || ! is_array(@getimagesize($realPath))) {
                $this->addError('newProof', 'File tidak terbaca sebagai gambar yang sah.');

                return;
            }
        }
        RateLimiter::hit($throttleKey, 60);
        $path = $this->newProof?->storeAs('payment_proofs', Str::uuid()->toString().'.'.$ext, 'public');

        $reservation->update([
            'payment_proof_path' => $path,
            'payment_status' => 'proof_uploaded',
            'payment_rejected_reason' => null,
        ]);

        ActivityLog::record('reservation.proof_reuploaded', Reservation::class, $reservation->id, [
            'code' => $reservation->code,
            'path' => $path,
        ]);

        $staff = User::query()->whereIn('role', ['owner', 'admin'])->get();
        if ($staff->isNotEmpty()) {
            Notification::send($staff, new NewReservationNotification($reservation->fresh(['items', 'seatingOption'])));
        }
        NotifyUmcoWhatsApp::dispatch($reservation->id);

        $this->newProof = null;
        $this->reuploadSuccess = true;
        unset($this->reservation);
    }

    /**
     * @return array{label: string, hex: string, class: string}
     */
    #[Computed]
    public function stampInfo(): array
    {
        $r = $this->reservation;

        if ($r->status === 'cancelled') {
            return ['label' => 'DIBATALKAN', 'hex' => '#C4472E', 'class' => 'text-[var(--stempel)] border-[var(--stempel)]'];
        }
        if ($r->status === 'completed') {
            return ['label' => 'SELESAI', 'hex' => '#22263A', 'class' => 'text-[var(--tinta)] border-[var(--tinta)]'];
        }
        if ($r->payment_status === 'rejected') {
            return ['label' => 'BUKTI DITOLAK', 'hex' => '#C4472E', 'class' => 'text-[var(--stempel)] border-[var(--stempel)]'];
        }
        if ($r->status === 'confirmed') {
            if ($r->payment_type === 'full' || $r->amount_paid >= $r->subtotal) {
                return ['label' => 'LUNAS', 'hex' => '#22263A', 'class' => 'text-[var(--tinta)] border-[var(--tinta)]'];
            }

            return ['label' => 'DP DITERIMA', 'hex' => '#22263A', 'class' => 'text-[var(--tinta)] border-[var(--tinta)]'];
        }

        return ['label' => 'MENUNGGU CEK', 'hex' => '#C4472E', 'class' => 'text-[var(--stempel)] border-[var(--stempel)]'];
    }

    #[Computed]
    public function umcoWaLink(): string
    {
        $target = PhoneNumber::normalizeBusiness((string) Settings::get('contact.business_wa', '02248001571'));
        $msg = "Halo UMCO, ini karcis reservasi saya {$this->code}.";

        return 'https://wa.me/'.$target.'?text='.rawurlencode($msg);
    }

    public function render(): View
    {
        return view('livewire.reservation-status-page');
    }
}
