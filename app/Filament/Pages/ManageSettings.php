<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Services\InstagramFeedService;
use App\Support\PhoneNumber;
use App\Support\Settings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property Schema $form
 */
class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan & Keamanan';

    protected static ?string $navigationLabel = 'Pengaturan Website & CMS';

    protected static ?string $title = 'Pengaturan Website & Operasional';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.manage-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /**
     * Peta terpusat antara nama field form Filament dan kunci penyimpanan Settings ('group.key').
     *
     * @var array<string, array{setting: string, default: mixed, type: 'string'|'bool'|'int'|'json'}>
     */
    private const FIELD_MAP = [
        // Brand & Tampilan
        'brand_name' => ['setting' => 'brand.brand_name', 'default' => 'UMCO', 'type' => 'string'],
        'brand_short' => ['setting' => 'brand.brand_short', 'default' => 'UMCO', 'type' => 'string'],
        'tagline' => ['setting' => 'brand.tagline', 'default' => '', 'type' => 'string'],
        'story_snippet' => ['setting' => 'brand.story_snippet', 'default' => '', 'type' => 'string'],
        'footer_wordmark' => ['setting' => 'brand.footer_wordmark', 'default' => 'UMCO', 'type' => 'string'],
        'hero_headline' => ['setting' => 'brand.hero_headline', 'default' => '', 'type' => 'string'],
        'hero_description' => ['setting' => 'brand.hero_description', 'default' => '', 'type' => 'string'],
        'today_note' => ['setting' => 'brand.today_note', 'default' => '', 'type' => 'string'],
        'hero_cutout_upload' => ['setting' => 'brand.hero_cutout_upload', 'default' => null, 'type' => 'string'],
        'hero_cutout_image' => ['setting' => 'brand.hero_cutout_image', 'default' => '/images/hero-cutout.webp', 'type' => 'string'],
        'footer_cutout_upload' => ['setting' => 'brand.footer_cutout_upload', 'default' => null, 'type' => 'string'],
        'footer_cutout_image' => ['setting' => 'brand.footer_cutout_image', 'default' => '/images/footer-cutout.webp', 'type' => 'string'],
        'smooth_scroll' => ['setting' => 'motion.smooth_scroll', 'default' => false, 'type' => 'bool'],

        // Kontak & Lokasi
        'address' => ['setting' => 'contact.address', 'default' => '', 'type' => 'string'],
        'coordinates' => ['setting' => 'contact.coordinates', 'default' => '', 'type' => 'string'],
        'plus_code' => ['setting' => 'contact.plus_code', 'default' => '', 'type' => 'string'],
        'google_maps_url' => ['setting' => 'contact.google_maps_url', 'default' => '', 'type' => 'string'],
        'instagram_url' => ['setting' => 'contact.instagram_url', 'default' => 'https://www.instagram.com/um.co_house', 'type' => 'string'],
        'instagram_handle' => ['setting' => 'contact.instagram_handle', 'default' => '@um.co_house', 'type' => 'string'],
        'instagram_access_token' => ['setting' => 'contact.instagram_access_token', 'default' => '', 'type' => 'string'],
        'phone_wa' => ['setting' => 'contact.phone_wa', 'default' => '02248001571', 'type' => 'string'],
        'business_wa' => ['setting' => 'contact.business_wa', 'default' => '622248001571', 'type' => 'string'],
        'collab_template' => ['setting' => 'contact.collab_template', 'default' => '', 'type' => 'string'],

        // Jam Buka & Rating
        'opening_time' => ['setting' => 'hours.opening_time', 'default' => '08:00', 'type' => 'string'],
        'closing_time' => ['setting' => 'hours.closing_time', 'default' => '22:00', 'type' => 'string'],
        'operating_days' => ['setting' => 'hours.operating_days', 'default' => 'Buka setiap hari, 08.00 - 22.00 WIB', 'type' => 'string'],
        'google_rating' => ['setting' => 'rating.google_rating', 'default' => '4.9', 'type' => 'string'],
        'google_reviews_count' => ['setting' => 'rating.google_reviews_count', 'default' => '86', 'type' => 'string'],
        'price_range' => ['setting' => 'rating.price_range', 'default' => 'Rp 13.000 - Rp 25.500', 'type' => 'string'],

        // Aturan Reservasi
        'min_party' => ['setting' => 'reservation.min_party', 'default' => 2, 'type' => 'int'],
        'max_party_size_online' => ['setting' => 'reservation.max_party_size_online', 'default' => 12, 'type' => 'int'],
        'menu_required' => ['setting' => 'reservation.menu_required', 'default' => true, 'type' => 'bool'],
        'slot_interval' => ['setting' => 'reservation.slot_interval', 'default' => 30, 'type' => 'int'],
        'last_slot_before_close' => ['setting' => 'reservation.last_slot_before_close', 'default' => 60, 'type' => 'int'],
        'capacity_per_slot' => ['setting' => 'reservation.capacity_per_slot', 'default' => 40, 'type' => 'int'],
        'min_advance_minutes' => ['setting' => 'reservation.min_advance_minutes', 'default' => 60, 'type' => 'int'],
        'max_advance_days' => ['setting' => 'reservation.max_advance_days', 'default' => 14, 'type' => 'int'],
        'dp_percentage' => ['setting' => 'reservation.dp_percentage', 'default' => 50, 'type' => 'int'],
        'full_payment_enabled' => ['setting' => 'reservation.full_payment_enabled', 'default' => true, 'type' => 'bool'],
        'grace_minutes' => ['setting' => 'reservation.grace_minutes', 'default' => 15, 'type' => 'int'],
        'remind_before_minutes' => ['setting' => 'reservation.remind_before_minutes', 'default' => 15, 'type' => 'int'],
        'sound_on_notification' => ['setting' => 'reservation.sound_on_notification', 'default' => false, 'type' => 'bool'],
        'floor_plan_image_path' => ['setting' => 'reservation.floor_plan_image_path', 'default' => null, 'type' => 'string'],
        'retention_days' => ['setting' => 'reservation.retention_days', 'default' => 90, 'type' => 'int'],

        // Pembayaran & QRIS
        'qris_image_path' => ['setting' => 'payment.qris_image_path', 'default' => null, 'type' => 'string'],
        'bank_accounts' => ['setting' => 'payment.bank_accounts', 'default' => [], 'type' => 'json'],
        'payment_notes' => ['setting' => 'payment.payment_notes', 'default' => '', 'type' => 'string'],

        // WhatsApp & Gateway
        'gateway_driver' => ['setting' => 'whatsapp_templates.gateway_driver', 'default' => 'log', 'type' => 'string'],
        'gateway_token' => ['setting' => 'whatsapp_templates.gateway_token', 'default' => '', 'type' => 'string'],
        'template_new_reservation' => ['setting' => 'whatsapp_templates.new_reservation', 'default' => '', 'type' => 'string'],
        'template_confirmation' => ['setting' => 'whatsapp_templates.confirmation', 'default' => '', 'type' => 'string'],
        'template_reminder' => ['setting' => 'whatsapp_templates.reminder', 'default' => '', 'type' => 'string'],
        'template_follow_up_proof' => ['setting' => 'whatsapp_templates.follow_up_proof', 'default' => '', 'type' => 'string'],
        'template_underpaid' => ['setting' => 'whatsapp_templates.underpaid', 'default' => '', 'type' => 'string'],
        'template_cancellation' => ['setting' => 'whatsapp_templates.cancellation', 'default' => '', 'type' => 'string'],

        // SEO
        'meta_title' => ['setting' => 'seo.meta_title', 'default' => '', 'type' => 'string'],
        'meta_description' => ['setting' => 'seo.meta_description', 'default' => '', 'type' => 'string'],
        'og_image' => ['setting' => 'seo.og_image', 'default' => null, 'type' => 'string'],
    ];

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public function mount(): void
    {
        $all = Settings::all();
        $filled = [];

        foreach (self::FIELD_MAP as $field => $config) {
            $raw = $all[$config['setting']] ?? $config['default'];

            if ($config['type'] === 'bool') {
                $filled[$field] = (bool) $raw;
            } elseif ($config['type'] === 'json') {
                $filled[$field] = is_string($raw) ? (json_decode($raw, true) ?: []) : (is_array($raw) ? $raw : []);
            } elseif ($field === 'dp_percentage') {
                $filled[$field] = max(50, (int) $raw);
            } elseif ($field === 'og_image' && is_string($raw) && str_starts_with($raw, '/images/')) {
                $filled[$field] = null;
            } else {
                $filled[$field] = $raw;
            }
        }

        $this->form->fill($filled);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Pengaturan')
                    ->tabs([
                        Tabs\Tab::make('Brand & Tampilan')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                TextInput::make('brand_name')->label('Nama Brand')->required(),
                                TextInput::make('brand_short')->label('Nama Singkat')->required(),
                                TextInput::make('footer_wordmark')->label('Wordmark Raksasa Footer'),
                                TextInput::make('tagline')->label('Tagline'),
                                Textarea::make('hero_headline')->label('Headline Hero Beranda')->rows(2)->columnSpanFull(),
                                Textarea::make('hero_description')->label('Sub-headline Hero Beranda')->rows(2)->columnSpanFull(),
                                Textarea::make('today_note')
                                    ->label('Catatan Hari Ini (Opsional)')
                                    ->helperText('Jika diisi, tampil sebagai catatan bar di hero dan pengumuman atas halaman reservasi.')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Textarea::make('story_snippet')
                                    ->label('Cuplikan Cerita Singkat Brand')
                                    ->helperText('Tampil pada ringkasan footer dan pembuka halaman cerita.')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                FileUpload::make('hero_cutout_upload')
                                    ->label('Unggah Foto Hero Tanpa Background (PNG/WebP)')
                                    ->disk('public')
                                    ->directory('hero-cutouts')
                                    ->acceptedFileTypes(['image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->helperText('Rekomendasi lebar minimal 1200 px (rasio ~3:2, PNG/WebP transparan, maks. 5 MB).'),
                                FileUpload::make('footer_cutout_upload')
                                    ->label('Unggah Foto Gelas Footer Tanpa Background (PNG/WebP)')
                                    ->disk('public')
                                    ->directory('footer-cutouts')
                                    ->acceptedFileTypes(['image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->helperText('Rekomendasi lebar minimal 1024 px (rasio 3:2, PNG/WebP transparan, maks. 5 MB).'),
                                TextInput::make('hero_cutout_image')->label('Path Default Gambar Hero'),
                                TextInput::make('footer_cutout_image')->label('Path Default Gambar Footer'),
                                Toggle::make('smooth_scroll')->label('Aktifkan Smooth Scroll Lenis (hanya desktop mouse)'),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Kontak & Lokasi')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                TextInput::make('phone_wa')
                                    ->label('Nomor Telepon / WA Tampil')
                                    ->helperText('Contoh uji: 02248001571'),
                                TextInput::make('business_wa')
                                    ->label('Nomor WA Penerima Notifikasi Reservasi (Otomatis 62xx)')
                                    ->required(),
                                TextInput::make('instagram_handle')->label('Username Instagram')->placeholder('@um.co_house'),
                                TextInput::make('instagram_url')->label('URL Instagram')->url()->placeholder('https://www.instagram.com/um.co_house'),
                                TextInput::make('instagram_access_token')
                                    ->label('Token Akses Instagram Graph API (Opsional, Real-Time)')
                                    ->password()
                                    ->revealable()
                                    ->helperText('Jika kosong, website menyinkronkan profil publik @um.co_house otomatis. Isi Long-Lived Token untuk sync Graph API resmi.')
                                    ->columnSpanFull(),
                                TextInput::make('google_maps_url')->label('URL Google Maps')->url(),
                                TextInput::make('coordinates')
                                    ->label('Koordinat GPS (Lat, Lng)')
                                    ->placeholder('0.4712, 101.4105')
                                    ->helperText('Digunakan otomatis untuk Schema.org GeoCoordinates Google Search.'),
                                TextInput::make('plus_code')
                                    ->label('Google Plus Code')
                                    ->placeholder('FC66+XX Pekanbaru'),
                                Textarea::make('address')->label('Alamat Lengkap')->rows(2)->columnSpanFull(),
                                Textarea::make('collab_template')->label('Template WA Ajakan Kolaborasi')->rows(2)->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Jam Buka & Rating')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                TextInput::make('opening_time')->label('Jam Buka (HH:MM)')->placeholder('08:00'),
                                TextInput::make('closing_time')->label('Jam Tutup (HH:MM)')->placeholder('22:00'),
                                TextInput::make('operating_days')->label('Keterangan Jam Buka'),
                                TextInput::make('google_rating')->label('Rating Google Maps'),
                                TextInput::make('google_reviews_count')->label('Jumlah Ulasan'),
                                TextInput::make('price_range')->label('Rentang Harga Menu'),
                            ])
                            ->columns(3),

                        Tabs\Tab::make('Aturan Reservasi')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                TextInput::make('min_party')->label('Minimal Orang')->numeric()->minValue(2)->required(),
                                TextInput::make('max_party_size_online')->label('Maksimal Orang Online')->numeric()->minValue(2)->required(),
                                TextInput::make('capacity_per_slot')->label('Kapasitas per Slot')->numeric()->minValue(2)->required(),
                                TextInput::make('slot_interval')->label('Interval Slot (Menit)')->numeric()->required(),
                                TextInput::make('last_slot_before_close')->label('Slot Terakhir Sebelum Tutup (Menit)')->numeric()->required(),
                                TextInput::make('min_advance_minutes')->label('Minimal Pesan Sebelum Datang (Menit)')->numeric()->required(),
                                TextInput::make('max_advance_days')->label('Jangkauan Hari Ke Depan')->numeric()->required(),
                                TextInput::make('dp_percentage')->label('Persentase DP (%) — Minimal 50%')->numeric()->minValue(50)->maxValue(100)->required(),
                                TextInput::make('grace_minutes')->label('Batas Keterlambatan / Hangus (Menit)')->numeric()->minValue(5)->required(),
                                TextInput::make('remind_before_minutes')->label('Pengingat Staf Sebelum Datang (Menit)')->numeric()->minValue(5)->required(),
                                TextInput::make('retention_days')
                                    ->label('Simpan Bukti Bayar (Hari)')
                                    ->numeric()
                                    ->minValue(7)
                                    ->helperText('Bukti pembayaran berstatus selesai/batal lebih lama dari hari ini akan dibersihkan otomatis tiap malam.'),
                                Toggle::make('menu_required')->label('Wajib Pesan Menu Saat Reservasi Online'),
                                Toggle::make('full_payment_enabled')->label('Aktifkan Opsi Bayar Lunas Langsung'),
                                Toggle::make('sound_on_notification')->label('Bunyi Lonceng Saat Notifikasi Baru Masuk Admin'),
                                FileUpload::make('floor_plan_image_path')
                                    ->label('Denah Area Duduk Sederhana (Opsional)')
                                    ->disk('public')
                                    ->directory('floor-plans')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->helperText('Rekomendasi rasio landscape 16:9 atau 4:3, JPG/PNG/WebP maks. 5 MB.')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),

                        Tabs\Tab::make('Pembayaran & QRIS')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                FileUpload::make('qris_image_path')
                                    ->label('Gambar QRIS Statis Resmi UMCO')
                                    ->disk('public')
                                    ->directory('qris')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(3072)
                                    ->helperText('Rekomendasi rasio persegi atau 3:4 tajam (min. 600 px), JPG/PNG/WebP maks. 3 MB.')
                                    ->columnSpanFull(),

                                Textarea::make('payment_notes')
                                    ->label('Catatan Pembayaran di Halaman Reservasi')
                                    ->helperText('Ditampilkan langsung kepada tamu pada langkah pembayaran & halaman status karcis.')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Repeater::make('bank_accounts')
                                    ->label('Rekening Bank Alternatif')
                                    ->schema([
                                        TextInput::make('bank')->label('Nama Bank / Kanal')->required(),
                                        TextInput::make('account_number')->label('Nomor Rekening'),
                                        TextInput::make('account_name')->label('Atas Nama'),
                                        TextInput::make('notes')->label('Keterangan'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('WhatsApp & Gateway')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Section::make('Gateway Notifikasi Otomatis ke Nomor UMCO (Lapis 2)')
                                    ->description('Jika token kosong, sistem memakai driver log tanpa error.')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('gateway_driver')
                                            ->label('Driver Gateway')
                                            ->options([
                                                'log' => 'Log (tanpa biaya, catat ke log server)',
                                                'fonnte' => 'Fonnte API',
                                                'wablas' => 'Wablas API',
                                            ])
                                            ->default('log')
                                            ->required(),
                                        TextInput::make('gateway_token')
                                            ->label('API Token Gateway')
                                            ->password()
                                            ->revealable(),
                                    ]),

                                Section::make('Template Pesan WhatsApp (Tanpa Emotikon)')
                                    ->description('Placeholder: {kode}, {nama}, {wa}, {hari}, {tanggal}, {jam}, {jam_hangus}, {jumlah}, {tempat}, {pesanan}, {total}, {dp}, {nominal_bayar}, {nominal_masuk}, {kekurangan}, {alasan}, {link_admin}, {link_status}')
                                    ->schema([
                                        Textarea::make('template_new_reservation')->label('Notifikasi Reservasi Baru ke UMCO')->rows(4),
                                        Textarea::make('template_confirmation')->label('Konfirmasi ke Pelanggan')->rows(3),
                                        Textarea::make('template_reminder')->label('Pengingat Kedatangan')->rows(3),
                                        Textarea::make('template_follow_up_proof')->label('Follow Up Bukti Bayar')->rows(3),
                                        Textarea::make('template_underpaid')->label('Kurang Bayar')->rows(3),
                                        Textarea::make('template_cancellation')->label('Pembatalan')->rows(3),
                                    ]),
                            ]),

                        Tabs\Tab::make('SEO & Metadata')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                TextInput::make('meta_title')->label('Meta Title Default')->columnSpanFull(),
                                Textarea::make('meta_description')->label('Meta Description Default')->rows(3)->columnSpanFull(),
                                FileUpload::make('og_image')
                                    ->label('Gambar Pratinjau Bagikan Link (OG Image)')
                                    ->disk('public')
                                    ->directory('seo')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(3072)
                                    ->helperText('Disarankan rasio 1,91:1 (1200 x 630 px, PNG/WebP/JPG, maks. 3 MB) untuk pratinjau WhatsApp & media sosial.')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->isOwner() ?? false, 403);

        $state = $this->form->getState();

        $overrides = [
            'dp_percentage' => max(50, (int) ($state['dp_percentage'] ?? 50)),
            'min_party' => max(2, (int) ($state['min_party'] ?? 2)),
            'max_party_size_online' => max(2, (int) ($state['max_party_size_online'] ?? 12)),
            'grace_minutes' => max(5, (int) ($state['grace_minutes'] ?? 15)),
            'remind_before_minutes' => max(5, (int) ($state['remind_before_minutes'] ?? 15)),
            'retention_days' => max(7, (int) ($state['retention_days'] ?? 90)),
            'business_wa' => PhoneNumber::normalizeBusiness(trim((string) ($state['business_wa'] ?? $state['phone_wa'] ?? '02248001571'))),
            'hero_cutout_image' => ! empty($state['hero_cutout_upload'])
                ? '/storage/'.ltrim((string) $state['hero_cutout_upload'], '/')
                : ($state['hero_cutout_image'] ?? '/images/hero-cutout.webp'),
            'footer_cutout_image' => ! empty($state['footer_cutout_upload'])
                ? '/storage/'.ltrim((string) $state['footer_cutout_upload'], '/')
                : ($state['footer_cutout_image'] ?? '/images/footer-cutout.webp'),
        ];

        foreach (self::FIELD_MAP as $field => $config) {
            $value = array_key_exists($field, $overrides)
                ? $overrides[$field]
                : ($state[$field] ?? $config['default']);

            if ($config['type'] === 'bool') {
                $value = ! empty($value);
            } elseif ($config['type'] === 'int') {
                $value = (int) $value;
            } elseif ($config['type'] === 'json') {
                $value = is_array($value) ? $value : [];
            } else {
                $value = (string) ($value ?? '');
            }

            Settings::set($config['setting'], $value);
        }

        InstagramFeedService::flushCache();

        $currentUser = auth()->user();

        ActivityLog::record('setting_update', null, null, [
            'updated_by' => $currentUser->name,
        ]);

        Notification::make()
            ->title('Pengaturan disimpan')
            ->body('Perubahan langsung diterapkan ke situs publik.')
            ->success()
            ->send();
    }
}
