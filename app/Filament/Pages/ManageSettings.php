<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Support\Settings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan & Audit';

    protected static ?string $navigationLabel = 'Pengaturan Website & CMS';

    protected static ?string $title = 'Pengaturan 100% Zero-Code';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $all = Settings::all();

        // Decode bank accounts JSON if needed
        $bankAccounts = $all['payment.bank_accounts'] ?? [];
        if (is_string($bankAccounts)) {
            $bankAccounts = json_decode($bankAccounts, true) ?: [];
        }

        $this->form->fill([
            // Brand
            'brand_name' => $all['brand.brand_name'] ?? 'UM.CO House',
            'brand_short' => $all['brand.brand_short'] ?? 'UMCO',
            'tagline' => $all['brand.tagline'] ?? '',
            'story_snippet' => $all['brand.story_snippet'] ?? '',
            'footer_wordmark' => $all['brand.footer_wordmark'] ?? 'UM.CO',
            'hero_headline' => $all['brand.hero_headline'] ?? '',
            'hero_description' => $all['brand.hero_description'] ?? '',

            // Contact
            'address' => $all['contact.address'] ?? '',
            'coordinates' => $all['contact.coordinates'] ?? '',
            'plus_code' => $all['contact.plus_code'] ?? '',
            'google_maps_url' => $all['contact.google_maps_url'] ?? '',
            'instagram_url' => $all['contact.instagram_url'] ?? '',
            'instagram_handle' => $all['contact.instagram_handle'] ?? '',
            'phone_wa' => $all['contact.phone_wa'] ?? '',
            'collab_template' => $all['contact.collab_template'] ?? '',

            // Hours & Rating
            'opening_time' => $all['hours.opening_time'] ?? '08:00',
            'closing_time' => $all['hours.closing_time'] ?? '21:00',
            'operating_days' => $all['hours.operating_days'] ?? 'Buka Setiap Hari',
            'google_rating' => $all['rating.google_rating'] ?? '4.9',
            'google_reviews_count' => $all['rating.google_reviews_count'] ?? '86+',
            'price_range' => $all['rating.price_range'] ?? 'Rp 25.000 – Rp 50.000 / orang',

            // Reservation Rules
            'slot_interval' => $all['reservation.slot_interval'] ?? '30',
            'last_slot_before_close' => $all['reservation.last_slot_before_close'] ?? '60',
            'capacity_per_slot' => $all['reservation.capacity_per_slot'] ?? '40',
            'min_advance_minutes' => $all['reservation.min_advance_minutes'] ?? '60',
            'max_advance_days' => $all['reservation.max_advance_days'] ?? '14',
            'max_party_size_online' => $all['reservation.max_party_size_online'] ?? '12',
            'dp_percentage' => $all['reservation.dp_percentage'] ?? '30',
            'dp_minimum' => $all['reservation.dp_minimum'] ?? '25000',
            'retention_days' => $all['reservation.retention_days'] ?? '90',

            // Payment
            'bank_accounts' => $bankAccounts,
            'payment_notes' => $all['payment.payment_notes'] ?? '',

            // WhatsApp Templates
            'template_confirmation' => $all['whatsapp_templates.confirmation'] ?? '',
            'template_cancellation' => $all['whatsapp_templates.cancellation'] ?? '',
            'template_reminder' => $all['whatsapp_templates.reminder'] ?? '',

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
                        Tabs\Tab::make('Identitas Brand & Hero')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                TextInput::make('brand_name')
                                    ->label('Nama Brand Resmi')
                                    ->required(),
                                TextInput::make('brand_short')
                                    ->label('Singkatan / Akronim (Teks Pendek)')
                                    ->required(),
                                TextInput::make('tagline')
                                    ->label('Tagline / Slogan Brand')
                                    ->columnSpanFull(),
                                TextInput::make('footer_wordmark')
                                    ->label('Teks Wordmark Raksasa di Footer')
                                    ->helperText('Contoh: UM.CO (membentang di bagian bawah halaman)'),
                                Textarea::make('hero_headline')
                                    ->label('Headline Utama Beranda (Hero)')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Textarea::make('hero_description')
                                    ->label('Deskripsi Sub-Headline Beranda')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Textarea::make('story_snippet')
                                    ->label('Cuplikan Cerita Kafe (Home Snippet)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Kontak & Lokasi Kampus')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                TextInput::make('phone_wa')
                                    ->label('Nomor WhatsApp Admin / Owner')
                                    ->helperText('Format: 0822... atau 62822... Otomatis dinormalisasi.')
                                    ->required(),
                                TextInput::make('instagram_handle')
                                    ->label('Username Instagram')
                                    ->placeholder('@um.co_house'),
                                TextInput::make('instagram_url')
                                    ->label('URL Link Instagram')
                                    ->url(),
                                TextInput::make('google_maps_url')
                                    ->label('URL Google Maps Resmi')
                                    ->url(),
                                TextInput::make('coordinates')
                                    ->label('Koordinat Latitude, Longitude'),
                                TextInput::make('plus_code')
                                    ->label('Google Plus Code'),
                                Textarea::make('address')
                                    ->label('Alamat Lengkap di Area Kampus UMRI')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Textarea::make('collab_template')
                                    ->label('Template WhatsApp Tombol Ajak Kolaborasi Kampus')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Jam Buka & Rating')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                TextInput::make('opening_time')
                                    ->label('Jam Buka')
                                    ->placeholder('08:00'),
                                TextInput::make('closing_time')
                                    ->label('Jam Tutup')
                                    ->placeholder('21:00'),
                                TextInput::make('operating_days')
                                    ->label('Hari Buka')
                                    ->placeholder('Buka Setiap Hari'),
                                TextInput::make('google_rating')
                                    ->label('Rating Google Maps')
                                    ->placeholder('4.9'),
                                TextInput::make('google_reviews_count')
                                    ->label('Jumlah Ulasan Google')
                                    ->placeholder('86+'),
                                TextInput::make('price_range')
                                    ->label('Rentang Harga / Orang')
                                    ->placeholder('Rp 25.000 – Rp 50.000 / orang'),
                            ])
                            ->columns(3),

                        Tabs\Tab::make('Aturan Reservasi Meja')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                TextInput::make('slot_interval')
                                    ->label('Interval Slot Waktu (Menit)')
                                    ->numeric()
                                    ->helperText('Default: 30 menit'),
                                TextInput::make('last_slot_before_close')
                                    ->label('Slot Terakhir Sebelum Tutup (Menit)')
                                    ->numeric()
                                    ->helperText('Default: 60 menit (Slot terakhir jam 20:00)'),
                                TextInput::make('capacity_per_slot')
                                    ->label('Kapasitas Total Kursi per Slot (Orang)')
                                    ->numeric()
                                    ->helperText('Batas kursi gabungan indoor dan outdoor per slot'),
                                TextInput::make('min_advance_minutes')
                                    ->label('Waktu Minimal Pemesanan (Menit Sebelum Datang)')
                                    ->numeric()
                                    ->helperText('Default: 60 menit'),
                                TextInput::make('max_advance_days')
                                    ->label('Jangkauan Maksimal Reservasi ke Depan (Hari)')
                                    ->numeric()
                                    ->helperText('Default: 14 hari'),
                                TextInput::make('max_party_size_online')
                                    ->label('Maksimal Orang per Reservasi Online')
                                    ->numeric()
                                    ->helperText('Lebih dari ini diarahkan langsung ke WhatsApp admin'),
                                TextInput::make('dp_percentage')
                                    ->label('Persentase DP Pesan Menu (%)')
                                    ->numeric()
                                    ->helperText('Default: 30% dari total pesanan menu'),
                                TextInput::make('dp_minimum')
                                    ->label('Nominal Minimum DP (Rupiah)')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->helperText('Default: Rp 25.000'),
                                TextInput::make('retention_days')
                                    ->label('Masa Simpan Bukti Transfer (Hari)')
                                    ->numeric()
                                    ->helperText('Default: 90 hari'),
                            ])
                            ->columns(3),

                        Tabs\Tab::make('Rekening & Pembayaran')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Textarea::make('payment_notes')
                                    ->label('Pemberitahuan Pembayaran di Halaman Reservasi')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Repeater::make('bank_accounts')
                                    ->label('Daftar Rekening Bank untuk Transfer DP')
                                    ->schema([
                                        TextInput::make('bank')
                                            ->label('Nama Bank (contoh: Bank Mandiri, BCA)')
                                            ->required(),
                                        TextInput::make('account_number')
                                            ->label('Nomor Rekening')
                                            ->placeholder('Contoh: 1080012345678'),
                                        TextInput::make('account_name')
                                            ->label('Atas Nama Pemilik Rekening')
                                            ->placeholder('Contoh: UMCO HOUSE / Ahmad Fauzi'),
                                        TextInput::make('notes')
                                            ->label('Keterangan Tambahan')
                                            ->placeholder('Opsional'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Template WhatsApp')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Section::make('Placeholder yang Didukung')
                                    ->description('Gunakan placeholder ini dalam template: {nama}, {kode}, {jumlah}, {tanggal}, {jam}, {total}, {dp}, {catatan}')
                                    ->schema([
                                        Textarea::make('template_confirmation')
                                            ->label('Template Chat Konfirmasi Reservasi')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Textarea::make('template_cancellation')
                                            ->label('Template Chat Pembatalan / Menu Kosong')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Textarea::make('template_reminder')
                                            ->label('Template Chat Pengingat Kedatangan Tamu')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('SEO & Metadata')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title Default')
                                    ->columnSpanFull(),
                                Textarea::make('meta_description')
                                    ->label('Meta Description Default')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $mapping = [
            // Brand
            'brand.brand_name' => $state['brand_name'] ?? 'UM.CO House',
            'brand.brand_short' => $state['brand_short'] ?? 'UMCO',
            'brand.tagline' => $state['tagline'] ?? '',
            'brand.story_snippet' => $state['story_snippet'] ?? '',
            'brand.footer_wordmark' => $state['footer_wordmark'] ?? 'UM.CO',
            'brand.hero_headline' => $state['hero_headline'] ?? '',
            'brand.hero_description' => $state['hero_description'] ?? '',

            // Contact
            'contact.phone_wa' => $state['phone_wa'] ?? '',
            'contact.instagram_handle' => $state['instagram_handle'] ?? '',
            'contact.instagram_url' => $state['instagram_url'] ?? '',
            'contact.google_maps_url' => $state['google_maps_url'] ?? '',
            'contact.coordinates' => $state['coordinates'] ?? '',
            'contact.plus_code' => $state['plus_code'] ?? '',
            'contact.address' => $state['address'] ?? '',
            'contact.collab_template' => $state['collab_template'] ?? '',

            // Hours & Rating
            'hours.opening_time' => $state['opening_time'] ?? '08:00',
            'hours.closing_time' => $state['closing_time'] ?? '21:00',
            'hours.operating_days' => $state['operating_days'] ?? 'Buka Setiap Hari',
            'rating.google_rating' => $state['google_rating'] ?? '4.9',
            'rating.google_reviews_count' => $state['google_reviews_count'] ?? '86+',
            'rating.price_range' => $state['price_range'] ?? 'Rp 25.000 – Rp 50.000 / orang',

            // Reservation
            'reservation.slot_interval' => $state['slot_interval'] ?? '30',
            'reservation.last_slot_before_close' => $state['last_slot_before_close'] ?? '60',
            'reservation.capacity_per_slot' => $state['capacity_per_slot'] ?? '40',
            'reservation.min_advance_minutes' => $state['min_advance_minutes'] ?? '60',
            'reservation.max_advance_days' => $state['max_advance_days'] ?? '14',
            'reservation.max_party_size_online' => $state['max_party_size_online'] ?? '12',
            'reservation.dp_percentage' => $state['dp_percentage'] ?? '30',
            'reservation.dp_minimum' => $state['dp_minimum'] ?? '25000',
            'reservation.retention_days' => $state['retention_days'] ?? '90',

            // Payment
            'payment.bank_accounts' => $state['bank_accounts'] ?? [],
            'payment.payment_notes' => $state['payment_notes'] ?? '',

            // WhatsApp
            'whatsapp_templates.confirmation' => $state['template_confirmation'] ?? '',
            'whatsapp_templates.cancellation' => $state['template_cancellation'] ?? '',
            'whatsapp_templates.reminder' => $state['template_reminder'] ?? '',

            // SEO
            'seo.meta_title' => $state['meta_title'] ?? '',
            'seo.meta_description' => $state['meta_description'] ?? '',
        ];

        foreach ($mapping as $key => $val) {
            Settings::set($key, $val);
        }

        ActivityLog::record('setting_update', null, null, [
            'updated_by' => auth()->user()->name ?? 'Admin',
            'message' => 'Pengaturan website berhasil diperbarui',
        ]);

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan')
            ->body('Seluruh konten website dan aturan reservasi langsung diperbarui seketika.')
            ->success()
            ->send();
    }
}
