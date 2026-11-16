<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Actions\Reservations\CreateReservation as CreateReservationAction;
use App\Actions\Reservations\QuoteReservation;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\MenuItem;
use App\Models\SeatingOption;
use App\Support\IndonesianFormat;
use App\Support\PhoneNumber;
use App\Support\Settings;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateReservation extends CreateRecord
{
    use HasWizard;

    protected static string $resource = ReservationResource::class;

    /**
     * @return array<int, Step>
     */
    protected function getSteps(): array
    {
        return [
            Step::make('Kapan & orang')
                ->schema([
                    Select::make('source')
                        ->label('Asal reservasi')
                        ->options([
                            'walk_in' => 'Datang langsung (Walk-in)',
                            'whatsapp' => 'WhatsApp',
                            'phone' => 'Telepon',
                            'web' => 'Website (input manual)',
                        ])
                        ->default('walk_in')
                        ->required(),

                    DatePicker::make('date')
                        ->label('Tanggal datang')
                        ->default(now()->format('Y-m-d'))
                        ->native(false)
                        ->required(),

                    Select::make('time')
                        ->label('Jam datang')
                        ->options(fn (): array => self::slotOptions())
                        ->default(array_key_first(self::slotOptions()) ?? '12:00')
                        ->required(),

                    TextInput::make('party_size')
                        ->label('Jumlah orang')
                        ->numeric()
                        ->default(2)
                        ->minValue(1)
                        ->maxValue(40)
                        ->live()
                        ->required(),

                    Toggle::make('skip_min_rules')
                        ->label('Lewati aturan minimal orang & belanja (khusus Owner)')
                        ->default(false)
                        ->live()
                        ->visible(fn (): bool => auth()->user()?->isOwner() ?? false)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Step::make('Tempat')
                ->schema([
                    Select::make('seating_option_id')
                        ->label('Pilihan area duduk')
                        ->options(fn (): array => SeatingOption::where('is_active', true)->orderBy('sort')->pluck('label', 'id')->all())
                        ->placeholder('Bebas di mana saja'),

                    Textarea::make('seating_note')
                        ->label('Catatan permintaan tempat')
                        ->placeholder('Contoh: Dekat colokan, ulang tahun, butuh meja gabung.')
                        ->rows(3),
                ]),

            Step::make('Menu')
                ->schema([
                    Repeater::make('items')
                        ->label('Daftar menu pesanan')
                        ->defaultItems(1)
                        ->live()
                        ->schema([
                            Select::make('menu_item_id')
                                ->label('Menu')
                                ->options(fn (): array => MenuItem::where('is_active', true)
                                    ->where('is_available', true)
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn ($m) => [$m->id => "{$m->name} (".IndonesianFormat::rupiah($m->price).')'])
                                    ->all())
                                ->searchable()
                                ->live()
                                ->required(),

                            TextInput::make('variant_label')
                                ->label('Varian (opsional)')
                                ->placeholder('Normal / Jumbo / Oat'),

                            Select::make('temperature')
                                ->label('Suhu')
                                ->options([
                                    'Es' => 'Es',
                                    'Panas' => 'Panas',
                                ])
                                ->placeholder('Standar'),

                            TextInput::make('qty')
                                ->label('Qty')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->live()
                                ->required(),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),

                    Placeholder::make('live_quote')
                        ->label('Ringkasan minimal belanja & DP')
                        ->content(function (Get $get): string {
                            $party = max(1, (int) ($get('party_size') ?? 2));
                            $items = is_array($get('items')) ? $get('items') : [];
                            $quote = QuoteReservation::calculate($party, $items, (string) ($get('payment_type') ?? 'dp'));
                            $status = $quote['meets_min_spend']
                                ? 'Memenuhi minimal belanja'
                                : 'Kurang '.IndonesianFormat::rupiah($quote['shortfall']);

                            return 'Subtotal: '.IndonesianFormat::rupiah($quote['subtotal'])
                                .' | Minimal '.$party.' orang: '.IndonesianFormat::rupiah($quote['min_spend'])
                                ." ({$status})"
                                .' | DP '.$quote['dp_percentage'].'%: '.IndonesianFormat::rupiah($quote['dp_amount']);
                        }),
                ]),

            Step::make('Data pelanggan')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama pelanggan')
                        ->required(),

                    TextInput::make('phone_input')
                        ->label('Nomor WhatsApp pelanggan')
                        ->placeholder('082212345678')
                        ->live(debounce: 300)
                        ->helperText(function (?string $state): string {
                            if (! $state) {
                                return 'Otomatis dinormalisasi ke 628xxx.';
                            }
                            try {
                                return 'Nomor WA: +'.PhoneNumber::normalize($state);
                            } catch (\InvalidArgumentException) {
                                return 'Format nomor belum valid (contoh 0822xxxx).';
                            }
                        })
                        ->required(),

                    Textarea::make('note')
                        ->label('Catatan tambahan')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Step::make('Pembayaran')
                ->schema([
                    Select::make('payment_type')
                        ->label('Skema pembayaran')
                        ->options([
                            'dp' => 'DP 50% (transfer/QRIS)',
                            'full' => 'Lunas (transfer/QRIS)',
                            'cash' => 'Bayar tunai langsung di kasir',
                        ])
                        ->default('dp')
                        ->live()
                        ->required(),

                    FileUpload::make('payment_proof_path')
                        ->label('Unggah bukti pembayaran')
                        ->disk('public')
                        ->directory('payment_proofs')
                        ->image()
                        ->maxSize(5120)
                        ->visible(fn (Get $get): bool => $get('payment_type') !== 'cash')
                        ->required(fn (Get $get): bool => $get('payment_type') !== 'cash'),

                    Textarea::make('admin_note')
                        ->label('Catatan internal staf (opsional)')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return CreateReservationAction::execute($data, null, auth()->user());
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first() ?? 'Data reservasi belum valid.';
            Notification::make()
                ->title('Tidak bisa menyimpan reservasi')
                ->body($firstError)
                ->danger()
                ->send();

            throw $e;
        }
    }

    /**
     * @return array<string, string>
     */
    private static function slotOptions(): array
    {
        $openStr = substr((string) Settings::get('hours.opening_time', '08:00'), 0, 5);
        $closeStr = substr((string) Settings::get('hours.closing_time', '22:00'), 0, 5);
        $interval = max(15, (int) Settings::get('reservation.slot_interval', 30));
        $lastBeforeClose = max(0, (int) Settings::get('reservation.last_slot_before_close', 60));

        $today = Carbon::now()->format('Y-m-d');
        $cursor = Carbon::createFromFormat('Y-m-d H:i', $today.' '.$openStr) ?: Carbon::now()->startOfDay()->setTime(8, 0);
        $last = (Carbon::createFromFormat('Y-m-d H:i', $today.' '.$closeStr) ?: Carbon::now()->startOfDay()->setTime(22, 0))->subMinutes($lastBeforeClose);

        $opts = [];
        while ($cursor->lte($last)) {
            $val = $cursor->format('H:i');
            $opts[$val] = str_replace(':', '.', $val).' WIB';
            $cursor->addMinutes($interval);
        }

        return $opts;
    }
}
