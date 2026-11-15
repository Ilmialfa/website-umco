<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
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

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan & Audit';

    protected static ?string $navigationLabel = 'Pengaturan Website & CMS';

    protected static ?string $title = 'Pengaturan Website & Operasional';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public function mount(): void
    {
        $all = Settings::all();

        $bankAccounts = $all['payment.bank_accounts'] ?? [];
        if (is_string($bankAccounts)) {
            $bankAccounts = json_decode($bankAccounts, true) ?: [];
        }

        $this->form->fill([
            // Brand & Tampilan
            'brand_name' => $all['brand.brand_name'] ?? 'UMCO',
            'brand_short' => $all['brand.brand_short'] ?? 'UMCO',
            'tagline' => $all['brand.tagline'] ?? '',
            'story_snippet' => $all['brand.story_snippet'] ?? '',
            'footer_wordmark' => $all['brand.footer_wordmark'] ?? 'UMCO',
            'hero_headline' => $all['brand.hero_headline'] ?? '',
            'hero_description' => $all['brand.hero_description'] ?? '',
            'today_note' => $all['brand.today_note'] ?? '',
            'hero_cutout_upload' => $all['brand.hero_cutout_upload'] ?? null,
            'hero_cutout_image' => $all['brand.hero_cutout_image'] ?? '/images/hero-cutout.webp',
            'footer_cutout_upload' => $all['brand.footer_cutout_upload'] ?? null,
            'footer_cutout_image' => $all['brand.footer_cutout_image'] ?? '/images/footer-cutout.webp',
            'smooth_scroll' => (bool) ($all['motion.smooth_scroll'] ?? false),

            // Contact
            'address' => $all['contact.address'] ?? '',
            'coordinates' => $all['contact.coordinates'] ?? '',
            'plus_code' => $all['contact.plus_code'] ?? '',
            'google_maps_url' => $all['contact.google_maps_url'] ?? '',
            'instagram_url' => $all['contact.instagram_url'] ?? 'https://www.instagram.com/um.co_house',
            'instagram_handle' => $all['contact.instagram_handle'] ?? '@um.co_house',
            'instagram_access_token' => $all['contact.instagram_access_token'] ?? '',
            'phone_wa' => $all['contact.phone_wa'] ?? '02248001571',
            'business_wa' => $all['contact.business_wa'] ?? '622248001571',
            'collab_template' => $all['contact.collab_template'] ?? '',

            // Hours & Rating
            'opening_time' => $all['hours.opening_time'] ?? '08:00',
            'closing_time' => $all['hours.closing_time'] ?? '22:00',
            'operating_days' => $all['hours.operating_days'] ?? 'Buka setiap hari, 08.00 - 22.00 WIB',
            'google_rating' => $all['rating.google_rating'] ?? '4.9',
            'google_reviews_count' => $all['rating.google_reviews_count'] ?? '86',
            'price_range' => $all['rating.price_range'] ?? 'Rp 13.000 - Rp 25.500',

            // Reservation Rules
            'min_party' => $all['reservation.min_party'] ?? '2',
            'max_party_size_online' => $all['reservation.max_party_size_online'] ?? '12',
            'menu_required' => (bool) ($all['reservation.menu_required'] ?? true),
            'slot_interval' => $all['reservation.slot_interval'] ?? '30',
            'last_slot_before_close' => $all['reservation.last_slot_before_close'] ?? '60',
            'capacity_per_slot' => $all['reservation.capacity_per_slot'] ?? '40',
            'min_advance_minutes' => $all['reservation.min_advance_minutes'] ?? '60',
            'max_advance_days' => $all['reservation.max_advance_days'] ?? '14',
            'dp_percentage' => max(50, (int) ($all['reservation.dp_percentage'] ?? 50)),
            'full_payment_enabled' => (bool) ($all['reservation.full_payment_enabled'] ?? true),
            'grace_minutes' => $all['reservation.grace_minutes'] ?? '15',
            'remind_before_minutes' => $all['reservation.remind_before_minutes'] ?? '15',
            'sound_on_notification' => (bool) ($all['reservation.sound_on_notification'] ?? false),
            'floor_plan_image_path' => $all['reservation.floor_plan_image_path'] ?? null,
            'retention_days' => $all['reservation.retention_days'] ?? '90',

            // Payment
            'qris_image_path' => $all['payment.qris_image_path'] ?? null,
            'bank_accounts' => $bankAccounts,
            'payment_notes' => $all['payment.payment_notes'] ?? '',

            // WhatsApp Templates & Gateway
            'gateway_driver' => $all['whatsapp_templates.gateway_driver'] ?? 'log',
            'gateway_token' => $all['whatsapp_templates.gateway_token'] ?? '',
            'template_new_reservation' => $all['whatsapp_templates.new_reservation'] ?? '',
            'template_confirmation' => $all['whatsapp_templates.confirmation'] ?? '',
            'template_reminder' => $all['whatsapp_templates.reminder'] ?? '',
            'template_follow_up_proof' => $all['whatsapp_templates.follow_up_proof'] ?? '',
            'template_underpaid' => $all['whatsapp_templates.underpaid'] ?? '',
            'template_cancellation' => $all['whatsapp_templates.cancellation'] ?? '',

            // SEO
            'meta_title' => $all['seo.meta_title'] ?? '',
            'meta_description' => $all['seo.meta_description'] ?? '',
        ]);
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
                                Textarea::make('today_note')->label('Catatan Hari Ini')->rows(2)->columnSpanFull(),
                                Textarea::make('story_snippet')->label('Cuplikan Cerita')->rows(2)->columnSpanFull(),
                                FileUpload::make('hero_cutout_upload')
                                    ->label('Unggah Foto Hero Tanpa Background (PNG/WebP)')
                                    ->disk('public')
                                    ->directory('hero-cutouts')
                                    ->image()
                                    ->helperText('Disarankan gambar PNG/WebP transparan agar menyatu mulus dengan latar gradasi hijau hero.'),
                                FileUpload::make('footer_cutout_upload')
                                    ->label('Unggah Foto Gelas Footer Tanpa Background (PNG/WebP)')
                                    ->disk('public')
                                    ->directory('footer-cutouts')
                                    ->image()
                                    ->helperText('Muncul di depan tulisan besar UMCO pada bagian footer.'),
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
                                TextInput::make('coordinates')->label('Koordinat'),
                                TextInput::make('plus_code')->label('Plus Code'),
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
                                TextInput::make('price_range')->label('Rentang Harga'),
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
                                TextInput::make('retention_days')->label('Simpan Bukti Bayar (Hari)')->numeric(),
                                Toggle::make('menu_required')->label('Wajib Pesan Menu Saat Reservasi Online'),
                                Toggle::make('full_payment_enabled')->label('Aktifkan Opsi Bayar Lunas Langsung'),
                                Toggle::make('sound_on_notification')->label('Bunyi Lonceng Saat Notifikasi Baru Masuk Admin'),
                                FileUpload::make('floor_plan_image_path')
                                    ->label('Denah Area Duduk Sederhana (Opsional)')
                                    ->disk('public')
                                    ->directory('floor_plans')
                                    ->image()
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
                                    ->image()
                                    ->columnSpanFull(),

                                Textarea::make('payment_notes')
                                    ->label('Catatan Pembayaran di Halaman Reservasi')
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
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->isOwner() ?? false, 403);

        $state = $this->form->getState();

        $dpPct = max(50, (int) ($state['dp_percentage'] ?? 50));
        $businessWaRaw = trim((string) ($state['business_wa'] ?? $state['phone_wa'] ?? '02248001571'));
        $businessWaNormalized = PhoneNumber::normalizeBusiness($businessWaRaw);

        $mapping = [
            'brand.brand_name' => $state['brand_name'] ?? 'UMCO',
            'brand.brand_short' => $state['brand_short'] ?? 'UMCO',
            'brand.tagline' => $state['tagline'] ?? '',
            'brand.story_snippet' => $state['story_snippet'] ?? '',
            'brand.footer_wordmark' => $state['footer_wordmark'] ?? 'UMCO',
            'brand.hero_headline' => $state['hero_headline'] ?? '',
            'brand.hero_description' => $state['hero_description'] ?? '',
            'brand.today_note' => $state['today_note'] ?? '',
            'brand.hero_cutout_upload' => (string) ($state['hero_cutout_upload'] ?? ''),
            'brand.hero_cutout_image' => ! empty($state['hero_cutout_upload'])
                ? '/storage/' . ltrim((string) $state['hero_cutout_upload'], '/')
                : ($state['hero_cutout_image'] ?? '/images/hero-cutout.webp'),
            'brand.footer_cutout_upload' => (string) ($state['footer_cutout_upload'] ?? ''),
            'brand.footer_cutout_image' => ! empty($state['footer_cutout_upload'])
                ? '/storage/' . ltrim((string) $state['footer_cutout_upload'], '/')
                : ($state['footer_cutout_image'] ?? '/images/footer-cutout.webp'),
            'motion.smooth_scroll' => ! empty($state['smooth_scroll']) ? '1' : '0',

            'contact.phone_wa' => $state['phone_wa'] ?? '02248001571',
            'contact.business_wa' => $businessWaNormalized,
            'contact.instagram_handle' => $state['instagram_handle'] ?? '@um.co_house',
            'contact.instagram_url' => $state['instagram_url'] ?? 'https://www.instagram.com/um.co_house',
            'contact.instagram_access_token' => $state['instagram_access_token'] ?? '',
            'contact.google_maps_url' => $state['google_maps_url'] ?? '',
            'contact.coordinates' => $state['coordinates'] ?? '',
            'contact.plus_code' => $state['plus_code'] ?? '',
            'contact.address' => $state['address'] ?? '',
            'contact.collab_template' => $state['collab_template'] ?? '',

            'hours.opening_time' => $state['opening_time'] ?? '08:00',
            'hours.closing_time' => $state['closing_time'] ?? '22:00',
            'hours.operating_days' => $state['operating_days'] ?? 'Buka setiap hari, 08.00 - 22.00 WIB',
            'rating.google_rating' => $state['google_rating'] ?? '4.9',
            'rating.google_reviews_count' => $state['google_reviews_count'] ?? '86',
            'rating.price_range' => $state['price_range'] ?? 'Rp 13.000 - Rp 25.500',

            'reservation.min_party' => (string) max(2, (int) ($state['min_party'] ?? 2)),
            'reservation.max_party_size_online' => (string) max(2, (int) ($state['max_party_size_online'] ?? 12)),
            'reservation.menu_required' => ! empty($state['menu_required']) ? '1' : '0',
            'reservation.slot_interval' => (string) ($state['slot_interval'] ?? '30'),
            'reservation.last_slot_before_close' => (string) ($state['last_slot_before_close'] ?? '60'),
            'reservation.capacity_per_slot' => (string) ($state['capacity_per_slot'] ?? '40'),
            'reservation.min_advance_minutes' => (string) ($state['min_advance_minutes'] ?? '60'),
            'reservation.max_advance_days' => (string) ($state['max_advance_days'] ?? '14'),
            'reservation.dp_percentage' => (string) $dpPct,
            'reservation.full_payment_enabled' => ! empty($state['full_payment_enabled']) ? '1' : '0',
            'reservation.grace_minutes' => (string) max(5, (int) ($state['grace_minutes'] ?? 15)),
            'reservation.remind_before_minutes' => (string) max(5, (int) ($state['remind_before_minutes'] ?? 15)),
            'reservation.sound_on_notification' => ! empty($state['sound_on_notification']) ? '1' : '0',
            'reservation.floor_plan_image_path' => (string) ($state['floor_plan_image_path'] ?? ''),
            'reservation.retention_days' => (string) ($state['retention_days'] ?? '90'),

            'payment.qris_image_path' => (string) ($state['qris_image_path'] ?? ''),
            'payment.bank_accounts' => $state['bank_accounts'] ?? [],
            'payment.payment_notes' => $state['payment_notes'] ?? '',

            'whatsapp_templates.gateway_driver' => $state['gateway_driver'] ?? 'log',
            'whatsapp_templates.gateway_token' => $state['gateway_token'] ?? '',
            'whatsapp_templates.new_reservation' => $state['template_new_reservation'] ?? '',
            'whatsapp_templates.confirmation' => $state['template_confirmation'] ?? '',
            'whatsapp_templates.reminder' => $state['template_reminder'] ?? '',
            'whatsapp_templates.follow_up_proof' => $state['template_follow_up_proof'] ?? '',
            'whatsapp_templates.underpaid' => $state['template_underpaid'] ?? '',
            'whatsapp_templates.cancellation' => $state['template_cancellation'] ?? '',

            'seo.meta_title' => $state['meta_title'] ?? '',
            'seo.meta_description' => $state['meta_description'] ?? '',
        ];

        foreach ($mapping as $key => $val) {
            Settings::set($key, $val);
        }

        \App\Services\InstagramFeedService::flushCache();

        ActivityLog::record('setting_update', null, null, [
            'updated_by' => auth()->user()?->name ?? 'Owner',
        ]);

        Notification::make()
            ->title('Pengaturan disimpan')
            ->body('Perubahan langsung diterapkan ke situs publik.')
            ->success()
            ->send();
    }
}
