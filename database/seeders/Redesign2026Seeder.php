<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\BeanOrigin;
use App\Models\CrowdPattern;
use App\Models\GalleryItem;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\Rule;
use App\Models\SeatingOption;
use App\Models\Setting;
use App\Models\SpendTier;
use App\Models\StoryBlock;
use App\Models\StreetStall;
use App\Models\Testimonial;
use App\Models\User;
use App\Support\MenuImageProcessor;
use App\Support\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Redesign2026Seeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedSettings();
        $this->seedSpendTiers();
        $this->seedSeatingOptions();
        $this->seedCrowdPatterns();
        $this->seedRules();
        $this->seedBeanOrigins();
        $this->seedMenuFromFolder();
        $this->seedPageSections();
        $this->seedStoriesStallsGalleryTestimonials();
        Settings::flush();
    }

    private function seedUsers(): void
    {
        User::updateOrCreate(
            ['email' => 'owner@umco.house'],
            [
                'name' => 'Owner UMCO',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'must_change_password' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@umco.house'],
            [
                'name' => 'Admin Operasional UMCO',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'must_change_password' => false,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@umco.house'],
            [
                'name' => 'Kasir UMCO',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'must_change_password' => false,
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedSettings(): void
    {
        $settingsData = [
            // Brand
            ['group' => 'brand', 'key' => 'brand_name', 'payload' => 'UMCO'],
            ['group' => 'brand', 'key' => 'brand_short', 'payload' => 'UMCO'],
            ['group' => 'brand', 'key' => 'tagline', 'payload' => 'Kopi hangat, meja lapang di Kampus UMRI Pekanbaru.'],
            ['group' => 'brand', 'key' => 'story_snippet', 'payload' => 'Kedai kami di Kampus 2 UMRI buka tiap hari buat kamu yang butuh tempat nugas, diskusi kelompok, atau ngopi santai sampai malam.'],
            ['group' => 'brand', 'key' => 'footer_wordmark', 'payload' => 'UMCO'],
            ['group' => 'brand', 'key' => 'hero_cutout_image', 'payload' => '/images/hero-cutout.webp'],
            ['group' => 'brand', 'key' => 'footer_cutout_image', 'payload' => '/images/footer-cutout.webp'],
            ['group' => 'brand', 'key' => 'signature_drink_image', 'payload' => '/images/footer-cutout.webp'],
            ['group' => 'brand', 'key' => 'hero_headline', 'payload' => 'Kopi hangat, meja lapang di tepi kampus.'],
            ['group' => 'brand', 'key' => 'hero_description', 'payload' => 'Kedai kami di Kampus 2 UMRI buka tiap hari dari pagi sampai malam. Datang sendiri buat nugas atau ramean bareng teman.'],
            ['group' => 'brand', 'key' => 'today_note', 'payload' => 'Gayo baru turun dari mesin sangrai Selasa lalu. Cocok diseduh V60.'],

            // Motion
            ['group' => 'motion', 'key' => 'smooth_scroll', 'payload' => '0'],

            // Contact
            ['group' => 'contact', 'key' => 'address', 'payload' => 'Kampus 2 UMRI, Jl. Tuanku Tambusai No. 2, Delima, Binawidya, Pekanbaru 28291'],
            ['group' => 'contact', 'key' => 'coordinates', 'payload' => '0.4999292, 101.4155284'],
            ['group' => 'contact', 'key' => 'plus_code', 'payload' => 'FCX8+X6 Delima, Pekanbaru'],
            ['group' => 'contact', 'key' => 'google_maps_url', 'payload' => 'https://www.google.com/maps/place/UMCO+HOUSE/@0.4999292,101.4155284,17z'],
            ['group' => 'contact', 'key' => 'instagram_url', 'payload' => 'https://www.instagram.com/um.co_house'],
            ['group' => 'contact', 'key' => 'instagram_handle', 'payload' => '@um.co_house'],
            ['group' => 'contact', 'key' => 'phone_wa', 'payload' => '02248001571'],
            ['group' => 'contact', 'key' => 'business_wa', 'payload' => '622248001571'],
            ['group' => 'contact', 'key' => 'collab_template', 'payload' => 'Halo UMCO, saya [nama] dari [komunitas/kampus]. Kami berencana bikin acara kecil atau kolaborasi di UMCO. Boleh tanya jadwal dan ketentuannya?'],

            // Hours (default bagian 16 no 3: 08.00 - 22.00)
            ['group' => 'hours', 'key' => 'opening_time', 'payload' => '08:00'],
            ['group' => 'hours', 'key' => 'closing_time', 'payload' => '22:00'],
            ['group' => 'hours', 'key' => 'operating_days', 'payload' => 'Buka setiap hari, 08.00 - 22.00 WIB'],

            // Rating
            ['group' => 'rating', 'key' => 'google_rating', 'payload' => '4.9'],
            ['group' => 'rating', 'key' => 'google_reviews_count', 'payload' => '86'],
            ['group' => 'rating', 'key' => 'price_range', 'payload' => 'Rp 13.000 - Rp 25.500'],
            ['group' => 'rating', 'key' => 'last_rating_sync', 'payload' => 'November 2026'],

            // Reservation rules (Bagian 11.1 & 16)
            ['group' => 'reservation', 'key' => 'min_party', 'payload' => '2'],
            ['group' => 'reservation', 'key' => 'max_party_size_online', 'payload' => '12'],
            ['group' => 'reservation', 'key' => 'menu_required', 'payload' => '1'],
            ['group' => 'reservation', 'key' => 'slot_interval', 'payload' => '30'],
            ['group' => 'reservation', 'key' => 'last_slot_before_close', 'payload' => '60'],
            ['group' => 'reservation', 'key' => 'capacity_per_slot', 'payload' => '40'],
            ['group' => 'reservation', 'key' => 'min_advance_minutes', 'payload' => '60'],
            ['group' => 'reservation', 'key' => 'max_advance_days', 'payload' => '14'],
            ['group' => 'reservation', 'key' => 'grace_minutes', 'payload' => '15'],
            ['group' => 'reservation', 'key' => 'remind_before_minutes', 'payload' => '15'],
            ['group' => 'reservation', 'key' => 'dp_percentage', 'payload' => '50'],
            ['group' => 'reservation', 'key' => 'full_payment_enabled', 'payload' => '1'],
            ['group' => 'reservation', 'key' => 'sound_on_notification', 'payload' => '0'],
            ['group' => 'reservation', 'key' => 'floor_plan_image_path', 'payload' => ''],
            ['group' => 'reservation', 'key' => 'retention_days', 'payload' => '90'],

            // Payment
            ['group' => 'payment', 'key' => 'qris_image_path', 'payload' => ''],
            ['group' => 'payment', 'key' => 'bank_accounts', 'payload' => json_encode([
                ['bank' => 'QRIS UMCO', 'account_number' => 'Scan kode QRIS di atas', 'account_name' => 'UMCO Coffee House', 'notes' => 'Bisa bayar lewat m-banking atau dompet digital apa saja'],
            ])],
            ['group' => 'payment', 'key' => 'payment_notes', 'payload' => 'Transfer sesuai nominal DP minimal 50% atau lunas, lalu unggah bukti bayar agar kasir segera cek.'],

            // WhatsApp templates & gateway (zero emojis)
            ['group' => 'whatsapp_templates', 'key' => 'gateway_driver', 'payload' => 'log'],
            ['group' => 'whatsapp_templates', 'key' => 'gateway_token', 'payload' => ''],
            ['group' => 'whatsapp_templates', 'key' => 'new_reservation', 'payload' => "Reservasi baru {kode}\nNama: {nama} ({wa})\nWaktu: {hari}, {tanggal} jam {jam}\nOrang: {jumlah}\nTempat: {tempat}\nPesanan: {pesanan}\nTotal: {total} | Bayar: {jenis_bayar} {nominal_bayar}\nCek bukti: {link_admin}"],
            ['group' => 'whatsapp_templates', 'key' => 'confirmation', 'payload' => 'Halo {nama}, reservasi {kode} untuk {jumlah} orang hari {hari} {tanggal} jam {jam} sudah kami terima dan pembayarannya sudah kami cek. Sampai ketemu di UMCO.'],
            ['group' => 'whatsapp_templates', 'key' => 'reminder', 'payload' => 'Halo {nama}, mengingatkan reservasi {kode} jam {jam} hari ini. Mejamu kami tahan sampai {jam_hangus}. Kalau ada perubahan, kabari kami di sini ya.'],
            ['group' => 'whatsapp_templates', 'key' => 'follow_up_proof', 'payload' => 'Halo {nama}, bukti pembayaran untuk {kode} belum terbaca jelas. Boleh kirim ulang lewat link ini: {link_status}'],
            ['group' => 'whatsapp_templates', 'key' => 'underpaid', 'payload' => 'Halo {nama}, nominal yang masuk {nominal_masuk}, kurang {kekurangan} dari tagihan {kode}.'],
            ['group' => 'whatsapp_templates', 'key' => 'cancellation', 'payload' => 'Halo {nama}, mohon maaf reservasi {kode} terpaksa kami batalkan karena {alasan}. Kami bantu atur ulang kalau kamu mau.'],

            // SEO
            ['group' => 'seo', 'key' => 'meta_title', 'payload' => 'UMCO — Kedai Kopi di Kampus UMRI Pekanbaru'],
            ['group' => 'seo', 'key' => 'meta_description', 'payload' => 'Kedai kopi di Kampus 2 UMRI Pekanbaru. Kopi harian, camilan, dan pesan meja dengan pilihan menu siap saji.'],
            ['group' => 'seo', 'key' => 'og_image', 'payload' => '/images/hero-photo.jpg'],
        ];

        foreach ($settingsData as $row) {
            Setting::updateOrCreate(
                ['key' => $row['key']],
                ['group' => $row['group'], 'payload' => $row['payload']]
            );
        }
    }

    private function seedSpendTiers(): void
    {
        SpendTier::query()->delete();

        $tiers = [
            ['party_min' => 2, 'party_max' => 2, 'min_spend' => 50000, 'per_extra_person' => null],
            ['party_min' => 3, 'party_max' => 3, 'min_spend' => 100000, 'per_extra_person' => null],
            ['party_min' => 4, 'party_max' => 4, 'min_spend' => 125000, 'per_extra_person' => null],
            ['party_min' => 5, 'party_max' => 5, 'min_spend' => 150000, 'per_extra_person' => null],
            ['party_min' => 6, 'party_max' => null, 'min_spend' => 175000, 'per_extra_person' => 25000],
        ];

        foreach ($tiers as $tier) {
            SpendTier::create($tier);
        }
    }

    private function seedSeatingOptions(): void
    {
        $options = [
            ['label' => 'Dekat jendela', 'sort' => 1],
            ['label' => 'Dekat colokan', 'sort' => 2],
            ['label' => 'Sofa', 'sort' => 3],
            ['label' => 'Area luar', 'sort' => 4],
            ['label' => 'Bebas di mana saja', 'sort' => 5],
        ];

        foreach ($options as $opt) {
            SeatingOption::updateOrCreate(
                ['label' => $opt['label']],
                ['sort' => $opt['sort'], 'is_active' => true]
            );
        }
    }

    private function seedCrowdPatterns(): void
    {
        // Jam 07..22 untuk 7 hari (1=Senin .. 7=Minggu)
        $weekdayCurve = [
            7 => 12, 8 => 28, 9 => 46, 10 => 62, 11 => 58,
            12 => 72, 13 => 68, 14 => 74, 15 => 86, 16 => 92,
            17 => 88, 18 => 64, 19 => 78, 20 => 84, 21 => 70, 22 => 36,
        ];
        $weekendCurve = [
            7 => 10, 8 => 22, 9 => 38, 10 => 50, 11 => 56,
            12 => 64, 13 => 68, 14 => 72, 15 => 80, 16 => 88,
            17 => 90, 18 => 75, 19 => 94, 20 => 96, 21 => 82, 22 => 45,
        ];

        for ($day = 1; $day <= 7; $day++) {
            $curve = $day >= 6 ? $weekendCurve : $weekdayCurve;
            foreach ($curve as $hour => $level) {
                $jitter = ($day % 3 - 1) * 4;
                CrowdPattern::updateOrCreate(
                    ['day_of_week' => $day, 'hour' => $hour],
                    ['level' => max(5, min(100, $level + $jitter))]
                );
            }
        }
    }

    private function seedRules(): void
    {
        $rules = [
            [
                'title' => 'Minimal 2 orang per reservasi',
                'body' => 'Pemesanan meja lewat situs dibuka mulai dua orang agar kursi kelompok tidak pecah. Datang sendirian tetap bisa langsung masuk ke kedai.',
                'sort' => 1,
            ],
            [
                'title' => 'Minimal belanja bertingkat',
                'body' => 'Untuk 2 orang minimal belanja Rp 50.000, 3 orang Rp 100.000, 4 orang Rp 125.000, 5 orang Rp 150.000, dan tiap tambahan orang berikutnya ditambah Rp 25.000.',
                'sort' => 2,
            ],
            [
                'title' => 'DP minimal 50% atau langsung lunas',
                'body' => 'Pembayaran dilakukan lewat QRIS sebelum karcis dicetak. Kamu bisa pilih bayar setengah dulu atau langsung lunas di awal.',
                'sort' => 3,
            ],
            [
                'title' => 'Permintaan tempat duduk dicatat',
                'body' => 'Mau duduk dekat jendela, colokan, atau di luar? Tulis saja saat pesan. Kami usahakan penuhi, tapi tetap menyesuaikan meja yang kosong di jam kedatangan.',
                'sort' => 4,
            ],
            [
                'title' => 'Karcis hangus lewat 15 menit',
                'body' => 'Meja kami tahan selama 15 menit sejak jam pesan. Jika terlambat, kabari kasir lewat WhatsApp sebelum waktunya habis.',
                'sort' => 5,
            ],
            [
                'title' => 'Pindah jadwal maksimal 2 jam sebelumnya',
                'body' => 'DP yang sudah masuk tidak dapat dikembalikan, namun bisa dipakai untuk pindah jadwal satu kali bila kamu memberi kabar minimal 2 jam sebelum jam datang.',
                'sort' => 6,
            ],
            [
                'title' => 'Datang langsung juga boleh',
                'body' => 'Tidak wajib reservasi kalau sekadar singgah. Selama masih ada meja kosong, kamu bisa langsung pesan di meja kasir.',
                'sort' => 7,
            ],
        ];

        foreach ($rules as $rule) {
            Rule::updateOrCreate(
                ['title' => $rule['title']],
                ['body' => $rule['body'], 'sort' => $rule['sort'], 'is_active' => true]
            );
        }
    }

    private function seedBeanOrigins(): void
    {
        BeanOrigin::query()->delete();

        $origins = [
            [
                'name' => 'Gayo Pantan Musara',
                'region' => 'Aceh Tengah, Sumatra',
                'altitude' => 1550,
                'note' => 'Rasa cokelat hitam, gula merah, dan asam jeruk yang lembut untuk V60 maupun es seduh Jepang.',
                'sort' => 1,
            ],
            [
                'name' => 'Kerinci Kayu Aro',
                'region' => 'Jambi, Sumatra',
                'altitude' => 1450,
                'note' => 'Wangi rempah manis dan badan medium, enak diminum panas tanpa tambahan gula.',
                'sort' => 2,
            ],
            [
                'name' => 'Preanger Pangalengan',
                'region' => 'Jawa Barat',
                'altitude' => 1350,
                'note' => 'Racikan utama espresso harian untuk kopi susu aren, latte, dan cappuccino.',
                'sort' => 3,
            ],
            [
                'name' => 'Toraja Sapan',
                'region' => 'Sulawesi Selatan',
                'altitude' => 1650,
                'note' => 'Kopi tinggi yang bersih dengan jejak karamel dan sedikit rasa buah beri.',
                'sort' => 4,
            ],
        ];

        foreach ($origins as $origin) {
            BeanOrigin::create($origin);
        }
    }

    private function seedMenuFromFolder(): void
    {
        $categorySpecs = [
            ['slug' => 'kopi-hitam', 'name' => 'Kopi hitam', 'sort' => 1],
            ['slug' => 'kopi-susu', 'name' => 'Kopi susu', 'sort' => 2],
            ['slug' => 'susu', 'name' => 'Susu', 'sort' => 3],
            ['slug' => 'teh', 'name' => 'Teh', 'sort' => 4],
            ['slug' => 'mocktail', 'name' => 'Mocktail', 'sort' => 5],
            ['slug' => 'makanan', 'name' => 'Makanan', 'sort' => 6],
            ['slug' => 'camilan', 'name' => 'Camilan', 'sort' => 7],
            ['slug' => 'manis', 'name' => 'Manis', 'sort' => 8],
        ];

        $categories = [];
        foreach ($categorySpecs as $cat) {
            $categories[$cat['slug']] = MenuCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'sort' => $cat['sort'], 'is_active' => true]
            );
        }

        // Hapus kategori lama yang sudah tidak dipakai bila kosong
        $validCatSlugs = array_keys($categories);
        MenuCategory::whereNotIn('slug', $validCatSlugs)->update(['is_active' => false]);

        $items = [
            // Kopi hitam (4)
            [
                'cat' => 'kopi-hitam', 'name' => 'Americano', 'price' => 24000, 'has_temp' => true,
                'file' => 'Coffe Base/Americano (Grape 25k, Aren 25k, sugar_non sugar 24k).JPG',
                'ingredients' => 'Espresso, air mineral',
                'desc' => 'Bisa pilih biasa (dengan atau tanpa gula), sentuhan aren, atau anggur.',
                'variants' => [
                    ['label' => 'Biasa (gula / tanpa gula)', 'price' => 24000],
                    ['label' => 'Aren', 'price' => 25000],
                    ['label' => 'Anggur', 'price' => 25000],
                ],
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'kopi-hitam', 'name' => 'Japanese', 'price' => 22000, 'has_temp' => false,
                'file' => 'Coffe Base/Japanase 22k.JPG',
                'ingredients' => 'Kopi single origin, es batu',
                'desc' => 'Seduh manual langsung di atas es biar wangi kopinya tetap segar.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'kopi-hitam', 'name' => 'V60', 'price' => 22000, 'has_temp' => true,
                'file' => 'Coffe Base/V60 22k.JPG',
                'ingredients' => 'Kopi single origin, air seduh',
                'desc' => 'Diseduh per cangkir sesuai biji yang sedang tersedia di rak bar.',
                'fav' => true, 'fav_order' => 2, 'home' => true,
            ],
            [
                'cat' => 'kopi-hitam', 'name' => 'Vietnam Drip', 'price' => 23000, 'has_temp' => true,
                'file' => 'Coffe Base/Vietnam Drip 23k.JPG',
                'ingredients' => 'Kopi sangrai gelap, kental manis',
                'desc' => 'Tetesan pelan kopi pekat berpadu kental manis di dasar gelas.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],

            // Kopi susu (9)
            [
                'cat' => 'kopi-susu', 'name' => 'Aren Coffee', 'price' => 25500, 'has_temp' => true,
                'file' => 'Coffee Milk (Hot_Ice)/Aren Coffe 25,5k (Pulm Sugar, Freshmilk, Coffe).JPG',
                'ingredients' => 'Gula aren, susu segar, espresso',
                'desc' => 'Menu yang paling sering balik dipesan buat teman kerja atau kuliah.',
                'fav' => true, 'fav_order' => 1, 'home' => true,
            ],
            [
                'cat' => 'kopi-susu', 'name' => 'Bliss Butter Cream', 'price' => 25500, 'has_temp' => true,
                'file' => 'Coffee Milk (Hot_Ice)/Bliss Butter Cream 25,5k (Sirup Vanilla, Sirup Butterscouth, freshmilk, Creamer Foam, Coffe, Bubuk Chocolate).JPG',
                'ingredients' => 'Sirup vanila, sirup butterscotch, susu segar, foam krimer, kopi, bubuk cokelat',
                'desc' => 'Lembut dengan lapisan foam gurih dan taburan cokelat di atasnya.',
                'fav' => true, 'fav_order' => 3, 'home' => true,
            ],
            [
                'cat' => 'kopi-susu', 'name' => 'Butterscotch Coffee', 'price' => 25500, 'has_temp' => true,
                'file' => 'Coffee Milk (Hot_Ice)/Butterscoth Coffee 25,5k (Sirup Butterscouth, Fres Milk, Coffee).JPG',
                'ingredients' => 'Sirup butterscotch, susu segar, kopi',
                'desc' => 'Kopi susu manis gurih dengan wangi karamel mentega.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'kopi-susu', 'name' => 'Cafe Latte', 'price' => 24000, 'has_temp' => true,
                'file' => 'Coffee Milk (Hot_Ice)/Cafe Latte 24k (Sugar_Non Sugar).jpg',
                'ingredients' => 'Espresso, susu segar (gula atau tanpa gula)',
                'desc' => 'Rasa kopi dan susu yang imbang. Bisa minta tanpa gula.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'kopi-susu', 'name' => 'Cappuccino', 'price' => 24000, 'has_temp' => true,
                'file' => 'Coffee Milk (Hot_Ice)/Cappucino 24k (Fresh Milk, Coffe).JPG',
                'ingredients' => 'Susu segar, espresso',
                'desc' => 'Espresso dan susu dengan busa lembut.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'kopi-susu', 'name' => 'Caramel Latte', 'price' => 25500, 'has_temp' => true,
                'file' => 'Coffee Milk (Hot_Ice)/Caramel Latte 25,5k (Sirup Caramel, Fresh Milk, Coffe).JPG',
                'ingredients' => 'Sirup karamel, susu segar, kopi',
                'desc' => 'Latte manis lembut dengan saus karamel.',
                'fav' => true, 'fav_order' => 4, 'home' => true,
            ],
            [
                'cat' => 'kopi-susu', 'name' => 'Classic Coffee', 'price' => 25500, 'has_temp' => true,
                'file' => 'Coffee Milk (Hot_Ice)/Classic Coffee 25,5k.jpg',
                'ingredients' => 'Espresso, susu segar',
                'desc' => 'Racikan kopi susu klasik ala bar UMCO.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'kopi-susu', 'name' => 'Moccachino', 'price' => 25500, 'has_temp' => true,
                'file' => 'Coffee Milk (Hot_Ice)/Moccachino 25,5k (Chocolate, Fresh Milk, Coffee).JPG',
                'ingredients' => 'Cokelat, susu segar, kopi',
                'desc' => 'Kopi bertemu cokelat pekat dalam susu hangat atau dingin.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'kopi-susu', 'name' => 'Vanilla Latte', 'price' => 25500, 'has_temp' => true,
                'file' => 'Coffee Milk (Hot_Ice)/Vanilla Latte 25,5k (Sirup Vanila, Fresh Milk, Coffe).JPG',
                'ingredients' => 'Sirup vanila, susu segar, kopi',
                'desc' => 'Aroma vanila ringan yang tidak menutupi rasa kopinya.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],

            // Susu (4)
            [
                'cat' => 'susu', 'name' => 'Candy Pop', 'price' => 25500, 'has_temp' => true,
                'file' => 'Milk Base (Hot_Ice)/Candy Pop 25,5k (Sirup Cotton Candy, Fresh Milk).JPG',
                'ingredients' => 'Sirup cotton candy, susu segar',
                'desc' => 'Manis ringan non-kopi dengan warna ceria.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'susu', 'name' => 'Chocolate', 'price' => 25500, 'has_temp' => true,
                'file' => 'Milk Base (Hot_Ice)/Chocolate 25,5k (Bubuk Chocolate, Fresh Milk).JPG',
                'ingredients' => 'Bubuk cokelat, susu segar',
                'desc' => 'Cokelat kental tanpa rasa enek, enak panas saat hujan.',
                'fav' => false, 'fav_order' => 0, 'home' => true,
            ],
            [
                'cat' => 'susu', 'name' => 'Matcha', 'price' => 25500, 'has_temp' => true,
                'file' => 'Milk Base (Hot_Ice)/Matcha 25,5k (Bubuk Matcha, Fresh Milk).JPG',
                'ingredients' => 'Bubuk matcha, susu segar',
                'desc' => 'Teh hijau lembut berpadu susu segar.',
                'fav' => true, 'fav_order' => 5, 'home' => true,
            ],
            [
                'cat' => 'susu', 'name' => 'Red Velvet', 'price' => 25500, 'has_temp' => true,
                'file' => 'Milk Base (Hot_Ice)/Red Velvet 25,5k (Bubuk Red Velvet, Fresh Milk).JPG',
                'ingredients' => 'Bubuk red velvet, susu segar',
                'desc' => 'Manis creamy rasa red velvet dingin atau hangat.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],

            // Teh (2)
            [
                'cat' => 'teh', 'name' => 'Lemon Tea', 'price' => 23000, 'has_temp' => true,
                'file' => 'Tea Base (Hot_Ice)/Lemon Tea 23k.JPG',
                'ingredients' => 'Teh seduh, perasan lemon',
                'desc' => 'Asam manis segar penghilang gerah siang Pekanbaru.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'teh', 'name' => 'Lychee Tea', 'price' => 23000, 'has_temp' => true,
                'file' => 'Tea Base (Hot_Ice)/Lychee Tea 23k.JPG',
                'ingredients' => 'Teh seduh, buah leci',
                'desc' => 'Teh wangi dengan dua buah leci utuh di dalam gelas.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],

            // Mocktail (4)
            [
                'cat' => 'mocktail', 'name' => 'Coffee Bree', 'price' => 25500, 'has_temp' => false,
                'file' => 'Mocktail/Coffee Bree 25,5k (Coffee, Soda, Jamaica_Roun).JPG',
                'ingredients' => 'Kopi, air soda, sirup jamaica',
                'desc' => 'Kopi berkarbonasi dengan wangi rempah buah ringan.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'mocktail', 'name' => 'Jade Fizzy', 'price' => 25500, 'has_temp' => false,
                'file' => 'Mocktail/Jade Fizzy 25,5k (Sirup Blue, Jungle Juice, Mint, Soda, Vanila, creamer).JPG',
                'ingredients' => 'Sirup blue, jungle juice, mint, soda, vanila, krimer',
                'desc' => 'Mocktail biru segar dengan daun mint dan soda dingin.',
                'fav' => true, 'fav_order' => 6, 'home' => true,
            ],
            [
                'cat' => 'mocktail', 'name' => 'Mocurry', 'price' => 25500, 'has_temp' => false,
                'file' => 'Mocktail/Mocurry 25,5k (Sirup Grape, Sirup Blue, Soda, Creamer, Fresh Milk).JPG',
                'ingredients' => 'Sirup anggur, sirup blue, soda, krimer, susu segar',
                'desc' => 'Soda anggur berpadu susu creamy, manis segar.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'mocktail', 'name' => 'Tropical Matcha', 'price' => 25000, 'has_temp' => false,
                'file' => 'Mocktail/Tropical Matcha 25,0k (Coconut Water, Basil seeds, Matcha).JPG',
                'ingredients' => 'Air kelapa, selasih, matcha',
                'desc' => 'Air kelapa dingin dengan selasih dan lapisan matcha di atasnya.',
                'fav' => false, 'fav_order' => 0, 'home' => false, 'new' => true,
            ],

            // Makanan (7)
            [
                'cat' => 'makanan', 'name' => 'Mie Kuning Nusantara', 'price' => 24000, 'has_temp' => false,
                'file' => 'Food/Mie Kuning Nusantara 24k.JPG',
                'ingredients' => 'Mi kuning goreng, bumbu rempah, pelengkap',
                'desc' => 'Mi kuning goreng berbumbu gurih hangat buat pengisi perut.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'makanan', 'name' => 'Mie Lava UMCO', 'price' => 22000, 'has_temp' => false,
                'file' => 'Food/Mie Lava Umco 22k.JPG',
                'ingredients' => 'Mi pedas, sambal racik UMCO, telur',
                'desc' => 'Mi pedas berkuah kental yang sering dipesan buat ramean.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'makanan', 'name' => 'Nasi Ayam Remuk', 'price' => 24000, 'has_temp' => false,
                'file' => 'Food/Nasi Ayam Remuk 24k.JPG',
                'ingredients' => 'Nasi hangat, ayam goreng remuk, sambal',
                'desc' => 'Ayam renyah digeprek renyah dengan sambal bawang segar.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'makanan', 'name' => 'Nasi Ayam Sambal Cabe Hijau', 'price' => 24000, 'has_temp' => false,
                'file' => 'Food/Nasi Ayam Sambal Cabe Hijau 24k.JPG',
                'ingredients' => 'Nasi hangat, ayam goreng, sambal cabai hijau',
                'desc' => 'Sambal hijau ulek kasar yang gurih dan pedas pas.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'makanan', 'name' => 'Nasi Ayam Sambal Kemangi', 'price' => 24000, 'has_temp' => false,
                'file' => 'Food/Nasi Ayam Sambal Kemangi 24k.JPG',
                'ingredients' => 'Nasi hangat, ayam, sambal kemangi wangi',
                'desc' => 'Nasi ayam berbumbu sambal kemangi segar.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'makanan', 'name' => 'Nasi Ayam Sambal Matah', 'price' => 24000, 'has_temp' => false,
                'file' => 'Food/Nasi Ayam Sambal Matah 24k.JPG',
                'ingredients' => 'Nasi hangat, ayam fillet renyah, sambal matah segar',
                'desc' => 'Andalan makan siang mahasiswa di antara jam kuliah.',
                'fav' => true, 'fav_order' => 7, 'home' => true,
            ],
            [
                'cat' => 'makanan', 'name' => 'Nasi Goreng Cabe Hijau', 'price' => 24000, 'has_temp' => false,
                'file' => 'Food/Nasi Goreng Cabe Hijau 24k.JPG',
                'ingredients' => 'Nasi goreng, cabai hijau, telur',
                'desc' => 'Nasi goreng berbumbu cabai hijau harum.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],

            // Camilan (7)
            [
                'cat' => 'camilan', 'name' => 'Burger', 'price' => 20000, 'has_temp' => false,
                'file' => 'Snack/Burger 20k (Keju, Daging, Tomat, Timun, Selasa, Saos).JPG',
                'ingredients' => 'Keju, daging, tomat, timun, selada, saus',
                'desc' => 'Roti isi patty daging, keju leleh, dan sayur segar.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'camilan', 'name' => 'Classic Savory Platter', 'price' => 23000, 'has_temp' => false,
                'file' => 'Snack/Classic Savory Platter 23k (Risoles, Pastel, Samosa).JPG',
                'ingredients' => 'Risoles, pastel, samosa',
                'desc' => 'Satu piring gorengan gurih buat dibagi bertiga atau berempat.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'camilan', 'name' => 'French Fries', 'price' => 16500, 'has_temp' => false,
                'file' => 'Snack/French Fries 16,5k.JPG',
                'ingredients' => 'Kentang goreng, garam, saus sambal',
                'desc' => 'Kentang goreng renyah buat teman ngobrol sore.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'camilan', 'name' => 'Mix Platter', 'price' => 23000, 'has_temp' => false,
                'file' => 'Snack/Mix Platter 23k (Kentang, Sosis, Nugget).JPG',
                'ingredients' => 'Kentang goreng, sosis, nugget',
                'desc' => 'Piring camilan klasik buat dibagi satu meja.',
                'fav' => true, 'fav_order' => 8, 'home' => true,
            ],
            [
                'cat' => 'camilan', 'name' => 'Pisang Goreng Cokelat Keju', 'price' => 24000, 'has_temp' => false,
                'file' => 'Snack/Pisang Goreng Coklat Keju 24k.JPG',
                'ingredients' => 'Pisang goreng, meses cokelat, keju parut',
                'desc' => 'Pisang goreng hangat bertabur cokelat dan keju.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'camilan', 'name' => 'Pisang Goreng Original', 'price' => 16500, 'has_temp' => false,
                'file' => 'Snack/Pisang Goreng Original 16,5k.JPG',
                'ingredients' => 'Pisang kepok, tepung renyah',
                'desc' => 'Pisang goreng renyah tanpa tambahan topping.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'camilan', 'name' => 'Sandwich', 'price' => 13000, 'has_temp' => false,
                'file' => 'Snack/Sandwich 13k ( Keju, Telur, Tomat, Timun, Selada, Saos).JPG',
                'ingredients' => 'Keju, telur, tomat, timun, selada, saus',
                'desc' => 'Roti lapis cepat saji sebelum kelas pagi dimulai.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],

            // Manis (2)
            [
                'cat' => 'manis', 'name' => 'Roti Bakar Cokelat', 'price' => 17500, 'has_temp' => false,
                'file' => 'Dessert/Roti Bakar Coklat 17,5k (Spread Chocolate Crunchy, Roti).JPG',
                'ingredients' => 'Selai cokelat renyah, roti panggang',
                'desc' => 'Roti tebal panggang garing dengan olesan cokelat renyah.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
            [
                'cat' => 'manis', 'name' => 'Roti Bakar Sarikaya', 'price' => 16500, 'has_temp' => false,
                'file' => 'Dessert/Roti Bakar Sarikaya 16,5k.JPG',
                'ingredients' => 'Selai srikaya, roti panggang',
                'desc' => 'Roti bakar klasik isi srikaya wangi pandan.',
                'fav' => false, 'fav_order' => 0, 'home' => false,
            ],
        ];

        $seededSlugs = [];
        $sortCounter = 1;
        $runMediaProcessing = ! app()->runningUnitTests();

        foreach ($items as $row) {
            $slug = Str::slug($row['name']);
            $seededSlugs[] = $slug;
            $sourceFile = base_path('docs/Menu Umco - belum lengkap/' . $row['file']);

            $imagePath = null;
            $imageVariants = null;

            if ($runMediaProcessing && file_exists($sourceFile)) {
                $processed = MenuImageProcessor::process($sourceFile, $slug);
                $imagePath = $processed['image_path'] ?: null;
                $imageVariants = $processed['image_variants'] ?: null;
            } elseif (file_exists(storage_path("app/public/menu/{$slug}-800.webp"))) {
                $imagePath = "menu/{$slug}-800.webp";
                $imageVariants = [
                    'webp' => [
                        '480' => "menu/{$slug}-480.webp",
                        '800' => "menu/{$slug}-800.webp",
                        '1200' => "menu/{$slug}-1200.webp",
                    ],
                ];
            }

            MenuItem::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categories[$row['cat']]->id,
                    'name' => $row['name'],
                    'description' => $row['desc'],
                    'ingredients' => $row['ingredients'],
                    'has_temperature' => $row['has_temp'],
                    'price' => $row['price'],
                    'variants' => $row['variants'] ?? null,
                    'image_path' => $imagePath,
                    'image_variants' => $imageVariants,
                    'is_placeholder' => empty($imagePath),
                    'is_favorite' => $row['fav'],
                    'show_on_home' => $row['home'],
                    'favorite_order' => $row['fav_order'],
                    'is_new' => $row['new'] ?? false,
                    'is_available' => true,
                    'is_active' => true,
                    'sort' => $sortCounter++,
                ]
            );
        }

        // Tandai item lama yang tidak punya foto folder sebagai placeholder agar terlacak di admin
        MenuItem::whereNotIn('slug', $seededSlugs)
            ->whereNull('image_path')
            ->update(['is_placeholder' => true]);
    }

    private function seedPageSections(): void
    {
        PageSection::where('page', 'home')
            ->whereIn('key', ['led_board', 'story_line', 'brew', 'crowd', 'gallery', 'location'])
            ->delete();

        $homeSections = [
            [
                'page' => 'home',
                'key' => 'hero',
                'sort' => 1,
                'heading' => 'Kopi hangat, meja lapang di tepi kampus.',
                'subheading' => 'Buka tiap hari 08.00 - 22.00 WIB di Kampus 2 UMRI Pekanbaru. Pesan meja dan menu langsung sebelum datang.',
                'body' => 'Kampus 2 UMRI, Pekanbaru',
                'cta_label' => 'Pesan meja',
                'cta_url' => '/reservasi',
                'media' => ['cutout' => '/images/hero-cutout.webp'],
                'data' => ['karcis_no' => '0127', 'default_people' => 2],
            ],
            [
                'page' => 'home',
                'key' => 'marquee',
                'sort' => 2,
                'heading' => 'Semua yang kamu butuh buat duduk tenang',
                'subheading' => 'Dari kopi pagi sebelum kelas sampai meja panjang untuk diskusi malam di Kampus 2 UMRI.',
                'data' => [
                    'eyebrow' => 'Suasana Kedai',
                    'phrases' => [
                        'Buka tiap hari 08.00 - 22.00 WIB',
                        'Aren Coffee Rp 25.500',
                        'Minimal pesan meja 2 orang',
                        'Nasi Ayam Sambal Matah Rp 24.000',
                        'Colokan di banyak sudut',
                        'Seduhan V60 tiap hari',
                    ],
                ],
            ],
            [
                'page' => 'home',
                'key' => 'promo_banner',
                'sort' => 3,
                'heading' => 'Meja lapang, fokus kerja, kopi siap diantar',
                'subheading' => 'Ruang nyaman di kawasan kampus buat nugas sendirian, kerja kelompok, atau diskusi komunitas.',
                'cta_label' => 'Lihat selengkapnya',
                'cta_url' => '/cerita',
                'media' => ['banner' => '/images/bistro-patio-table.jpg'],
                'data' => ['eyebrow' => 'Sorotan UMCO'],
            ],
            [
                'page' => 'home',
                'key' => 'signature_reel',
                'sort' => 4,
                'heading' => 'Racikan yang sering balik dipesan',
                'subheading' => 'Lima menu favorit pengunjung di meja bar dan sudut diskusi UMCO.',
                'data' => ['eyebrow' => 'Menu Andalan'],
            ],
            [
                'page' => 'home',
                'key' => 'menu_nota',
                'sort' => 5,
                'heading' => 'Pilih duluan sebelum datang',
                'subheading' => 'Klik menu di kiri, isiannya otomatis masuk ke nota di kanan. Langsung terbawa saat lanjut pesan meja.',
                'cta_label' => 'Lihat semua 39 menu',
                'cta_url' => '/menu',
                'data' => ['eyebrow' => 'Nota Pilihan'],
            ],
            [
                'page' => 'home',
                'key' => 'karcis_teaser',
                'sort' => 6,
                'heading' => 'Pesan meja, karcis terisi sendiri',
                'subheading' => 'Atur hari dan jumlah orang di sini, cek ketentuannya, lalu lanjut pilih jam, tempat duduk, dan pembayaran.',
                'cta_label' => 'Lanjut pilih jam, tempat, dan menu',
                'cta_url' => '/reservasi',
                'data' => ['eyebrow' => 'Reservasi Meja'],
            ],
            [
                'page' => 'home',
                'key' => 'gallery_portrait',
                'sort' => 7,
                'heading' => 'Ruang untuk melambat sebentar',
                'subheading' => 'Potret suasana kedai dan cangkir harian yang terus bergulir. Geser kiri atau kanan sesukamu.',
                'data' => ['eyebrow' => 'Galeri Portrait'],
            ],
            [
                'page' => 'home',
                'key' => 'gallery_diagonal',
                'sort' => 8,
                'heading' => 'Momen dari berbagai sudut',
                'subheading' => 'Tampilan bebas semua rasio foto dalam alur diagonal yang bergerak mengikuti guliranmu.',
                'data' => ['eyebrow' => 'Galeri Bebas Rasio'],
            ],
            [
                'page' => 'home',
                'key' => 'testimonials',
                'sort' => 9,
                'heading' => 'Kenapa banyak yang balik lagi',
                'subheading' => 'Suara nyata mahasiswa, pekerja lepas, dan komunitas yang sering singgah.',
                'data' => ['eyebrow' => 'Kata Pengunjung'],
            ],
            [
                'page' => 'home',
                'key' => 'cta_footer',
                'sort' => 10,
                'heading' => 'Mampir dulu, kursimu kami siapkan.',
                'subheading' => 'Buka setiap hari pukul 08.00 - 22.00 WIB di Kampus 2 UMRI. Pesan meja lebih dulu atau hubungi kami untuk acara dan kolaborasi.',
                'cta_label' => 'Pesan meja',
                'cta_url' => '/reservasi',
                'data' => [
                    'eyebrow' => 'Kampus 2 UMRI · Pekanbaru',
                    'secondary_label' => 'Hubungi kami',
                ],
            ],
        ];

        $otherSections = [
            [
                'page' => 'cerita',
                'key' => 'hero',
                'sort' => 1,
                'heading' => 'Dari kampus, buat yang lagi butuh tempat.',
                'subheading' => 'Kampus 2 UMRI, Jalan Tuanku Tambusai Ujung.',
                'body' => 'Kami membangun UMCO agar mahasiswa, dosen, dan tamu punya meja yang enak buat duduk lama maupun singgah sebentar.',
                'media' => ['desktop' => '/images/bistro-patio-table.jpg'],
            ],
            [
                'page' => 'cerita',
                'key' => 'altimeter',
                'sort' => 2,
                'heading' => 'Dari mana bijinya datang',
                'subheading' => 'Gulir pelan untuk melihat ketinggian kebun biji yang sering masuk ke grinder bar.',
            ],
            [
                'page' => 'cerita',
                'key' => 'collab',
                'sort' => 3,
                'heading' => 'Mau bikin acara atau kumpul komunitas?',
                'body' => 'Diskusi buku, buka puasa bareng jurusan, sampai sesi ngobrol santai sering mampir ke sini. Kabari kami beberapa hari sebelumnya.',
                'cta_label' => 'Tanya lewat WhatsApp',
                'cta_url' => '/reservasi',
            ],
            [
                'page' => 'menu',
                'key' => 'hero',
                'sort' => 1,
                'heading' => 'Menu',
                'subheading' => 'Harga sudah termasuk senyum kasir.',
            ],
        ];

        foreach (array_merge($homeSections, $otherSections) as $sec) {
            PageSection::updateOrCreate(
                ['page' => $sec['page'], 'key' => $sec['key']],
                [
                    'is_active' => true,
                    'sort' => $sec['sort'],
                    'heading' => $sec['heading'] ?? null,
                    'subheading' => $sec['subheading'] ?? null,
                    'body' => $sec['body'] ?? null,
                    'cta_label' => $sec['cta_label'] ?? null,
                    'cta_url' => $sec['cta_url'] ?? null,
                    'media' => $sec['media'] ?? null,
                    'data' => $sec['data'] ?? null,
                ]
            );
        }
    }

    private function seedStoriesStallsGalleryTestimonials(): void
    {
        // Bersihkan cerita lama yang menyebut arsitektur H / Wing
        StoryBlock::query()->where('key', 'like', '%architecture%')->delete();

        $stories = [
            [
                'page' => 'cerita',
                'key' => 'pegangan_1',
                'title' => 'Kopi diseduh satu-satu',
                'body' => 'Setiap V60 maupun espresso ditakar langsung saat dipesan. Bukan kopi batch berjam-jam yang tinggal dipanaskan ulang.',
                'sort' => 1,
            ],
            [
                'page' => 'cerita',
                'key' => 'pegangan_2',
                'title' => 'Harga masuk akal buat kantong mahasiswa',
                'body' => 'Dari sandwich 13 ribu sampai kopi susu aren 25,5 ribu, semuanya dibuat supaya kamu bisa datang seminggu tiga kali tanpa mikir panjang.',
                'sort' => 2,
            ],
            [
                'page' => 'cerita',
                'key' => 'pegangan_3',
                'title' => 'Boleh duduk lama',
                'body' => 'Nugas tiga jam dengan satu gelas kopi? Silakan. Colokan listrik dan kursi panjang memang disiapkan untuk itu.',
                'sort' => 3,
            ],
        ];

        foreach ($stories as $s) {
            StoryBlock::updateOrCreate(
                ['page' => $s['page'], 'key' => $s['key']],
                ['title' => $s['title'], 'body' => $s['body'], 'sort' => $s['sort']]
            );
        }

        // Hapus referensi gedung H / indoor/outdoor dari street stalls & testimonials
        StreetStall::query()->where('description', 'like', '%outdoor%')->delete();

        $stalls = [
            [
                'name' => 'Tahu Walik Kampus',
                'signature_item' => 'Tahu Walik Isi Ayam + Sambal Kecap',
                'description' => 'Digoreng dadakan tiap sore dengan cabai rawit ulek.',
                'active_hours' => '15.00 - 21.30 WIB',
                'sort' => 1,
            ],
            [
                'name' => 'Sate Taichan Sore',
                'signature_item' => 'Taichan Dada + Lontong',
                'description' => 'Sambal jeruk nipis hangat yang paling pas masuk menjelang magrib.',
                'active_hours' => '16.00 - 22.00 WIB',
                'sort' => 2,
            ],
            [
                'name' => 'Dapur Roti Panggang',
                'signature_item' => 'Roti Bakar Cokelat Renyah',
                'description' => 'Teman minum teh lemon atau kopi susu di sela obrolan malam.',
                'active_hours' => '08.00 - 22.00 WIB',
                'sort' => 3,
            ],
        ];

        foreach ($stalls as $stall) {
            StreetStall::updateOrCreate(
                ['name' => $stall['name']],
                [
                    'signature_item' => $stall['signature_item'],
                    'description' => $stall['description'],
                    'active_hours' => $stall['active_hours'],
                    'sort' => $stall['sort'],
                    'is_active' => true,
                ]
            );
        }

        Banner::updateOrCreate(
            ['title' => 'Meja lapang buat diskusi dan tugas sore'],
            [
                'description' => 'Meja panjang bercatu daya, seduhan segar dari pagi, dan menu hangat yang bisa dipesan sekaligus saat reservasi meja.',
                'image_path' => '/images/bistro-patio-table.jpg',
                'cta_label' => 'Lihat selengkapnya',
                'cta_url' => '/cerita',
                'is_active' => true,
                'sort' => 1,
            ]
        );

        Banner::updateOrCreate(
            ['title' => 'Pesan meja sekalian menu tanpa antre'],
            [
                'description' => 'Pilih jadwal kedatangan minimal 2 orang, tentukan permintaan tempat duduk, lalu kunci karcis meja lewat QRIS.',
                'image_path' => '/images/hero-banner-full.jpg',
                'cta_label' => 'Lihat selengkapnya',
                'cta_url' => '/reservasi',
                'is_active' => true,
                'sort' => 2,
            ]
        );

        $testimonials = [
            [
                'name' => 'Rani',
                'role' => 'Mahasiswi UMRI · Ulasan Google',
                'rating' => 5,
                'body' => 'Sering mampir ke sini buat ngerjain revisi. Aren Coffee-nya konsisten dan Nasi Ayam Sambal Matah-nya bikin nggak perlu keluar kampus lagi cari makan siang.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 1,
            ],
            [
                'name' => 'Fajar Ramadhan',
                'role' => 'Mahasiswa Teknik · Ulasan Google',
                'rating' => 5,
                'body' => 'Meja kerjanya luas dan colokannya gampang dijangkau. Kalau sore mulai rame tapi suasananya tetap enak buat ngobrol sama teman sekelas.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 2,
            ],
            [
                'name' => 'Sarah Nabilah',
                'role' => 'Pekerja lepas · Ulasan Google',
                'rating' => 5,
                'body' => 'Pagi masih tenang buat kerja, sore sampai malam ramai sama anak kampus dan camilan hangat. Praktis banget bisa pesan meja sekalian menu dari website.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 3,
            ],
            [
                'name' => 'Dimas Pratama',
                'role' => 'Komunitas kampus · Ulasan Google',
                'rating' => 5,
                'body' => 'Matcha sama Bliss Butter Cream-nya wajib coba. Kalau bawa tim lima orang gampang banget atur meja dan pesannya dari jauh-jauh jam.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 4,
            ],
            [
                'name' => 'Nadia Putri',
                'role' => 'Mahasiswi Akuntansi · Ulasan Google',
                'rating' => 5,
                'body' => 'Baristanya ramah, nggak pernah bikin sungkan buat nanya menu non-kopi. Tempat paling aman kalau butuh wifi kencang sebelum presentasi.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 5,
            ],
            [
                'name' => 'Reza Mahendra',
                'role' => 'Alumni UMRI · Ulasan Google',
                'rating' => 5,
                'body' => 'Dulu cuma mampir buat ngopi cepat, sekarang sering jadi titik ketemu klien atau teman lama karena lokasinya gampang dicari dan parkir lega.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 6,
            ],
        ];

        // Bersihkan testimonial lama yang masih menyinggung narasi bangunan/AC lama
        Testimonial::query()->where('body', 'like', '%huruf ' . 'H%')->orWhere('body', 'like', '%AC-nya%')->delete();

        foreach ($testimonials as $t) {
            Testimonial::updateOrCreate(
                ['name' => $t['name']],
                [
                    'role' => $t['role'],
                    'rating' => $t['rating'],
                    'body' => $t['body'],
                    'source' => $t['source'],
                    'is_featured' => $t['is_featured'],
                    'is_sample' => $t['is_sample'],
                    'sort' => $t['sort'],
                ]
            );
        }

        // Galeri Portrait & Diagonal Campuran tanpa duplikasi berlebih
        GalleryItem::query()->whereIn('group', ['indoor', 'outdoor'])->update(['group' => 'ruang']);

        $gallerySeeds = [
            // Portrait infinite gallery (ratio 3:4 & 1:1 clean crops)
            ['group' => 'kopi', 'orientation' => 'portrait', 'image_path' => '/storage/menu/aren-coffee-800.webp', 'caption' => 'Aren Coffee andalan meja pagi', 'sort' => 1],
            ['group' => 'kopi', 'orientation' => 'portrait', 'image_path' => '/storage/menu/matcha-800.webp', 'caption' => 'Matcha dingin di sudut terang', 'sort' => 2],
            ['group' => 'kopi', 'orientation' => 'portrait', 'image_path' => '/storage/menu/v60-800.webp', 'caption' => 'Seduhan V60 manual tiap pagi', 'sort' => 3],
            ['group' => 'kopi', 'orientation' => 'portrait', 'image_path' => '/storage/menu/bliss-butter-cream-800.webp', 'caption' => 'Bliss Butter Cream favorit sore', 'sort' => 4],
            ['group' => 'kopi', 'orientation' => 'portrait', 'image_path' => '/storage/menu/vietnam-drip-800.webp', 'caption' => 'Tetesan pelan Vietnam Drip', 'sort' => 5],
            ['group' => 'makanan', 'orientation' => 'portrait', 'image_path' => '/storage/menu/nasi-ayam-sambal-matah-800.webp', 'caption' => 'Makan siang kampus tanpa repot', 'sort' => 6],

            // Diagonal gallery (bebas semua rasio — portrait, square, landscape tanpa duplikasi gambar bersebelahan)
            ['group' => 'suasana', 'orientation' => 'diagonal', 'image_path' => '/images/bistro-patio-table.jpg', 'caption' => 'Meja sore paling sering dipilih', 'sort' => 7],
            ['group' => 'suasana', 'orientation' => 'diagonal', 'image_path' => '/images/hero-banner-full.jpg', 'caption' => 'Halaman teduh depan kedai', 'sort' => 8],
            ['group' => 'kopi', 'orientation' => 'diagonal', 'image_path' => '/images/drinks-trio-banner.jpg', 'caption' => 'Tiga gelas meja diskusi', 'sort' => 9],
            ['group' => 'kopi', 'orientation' => 'diagonal', 'image_path' => '/images/hero-iced-coffee-splash.jpg', 'caption' => 'Susu segar bertemu espresso', 'sort' => 10],
            ['group' => 'kopi', 'orientation' => 'diagonal', 'image_path' => '/storage/menu/americano-800.webp', 'caption' => 'Americano dingin sebelum kelas', 'sort' => 11],
            ['group' => 'makanan', 'orientation' => 'diagonal', 'image_path' => '/storage/menu/mix-platter-800.webp', 'caption' => 'Mix platter teman nugas', 'sort' => 12],
            ['group' => 'kopi', 'orientation' => 'diagonal', 'image_path' => '/storage/menu/caramel-latte-800.webp', 'caption' => 'Caramel Latte di jam istirahat', 'sort' => 13],
            ['group' => 'kopi', 'orientation' => 'diagonal', 'image_path' => '/storage/menu/japanese-800.webp', 'caption' => 'Japanese iced filter brew', 'sort' => 14],
        ];

        GalleryItem::query()->whereNotIn('caption', collect($gallerySeeds)->pluck('caption'))->delete();

        foreach ($gallerySeeds as $g) {
            GalleryItem::updateOrCreate(
                ['caption' => $g['caption']],
                [
                    'group' => $g['group'],
                    'type' => 'image',
                    'orientation' => $g['orientation'],
                    'image_path' => $g['image_path'],
                    'sort' => $g['sort'],
                    'is_active' => true,
                ]
            );
        }
    }
}
