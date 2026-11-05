<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Models\StoryBlock;
use App\Models\StreetStall;
use App\Models\Testimonial;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $owner = User::updateOrCreate(
            ['email' => 'owner@umco.house'],
            [
                'name' => 'Owner UM.CO House',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'must_change_password' => true,
                'email_verified_at' => now(),
            ]
        );

        $cashier = User::updateOrCreate(
            ['email' => 'kasir@umco.house'],
            [
                'name' => 'Kasir & Admin UMCO',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'must_change_password' => false,
                'email_verified_at' => now(),
            ]
        );

        // 2. Settings
        $settingsData = [
            // Brand
            ['group' => 'brand', 'key' => 'brand_name', 'payload' => 'UM.CO House'],
            ['group' => 'brand', 'key' => 'brand_short', 'payload' => 'UMCO'],
            ['group' => 'brand', 'key' => 'tagline', 'payload' => 'Ruang tenang untuk nugas, tempat ramai untuk bercerita.'],
            ['group' => 'brand', 'key' => 'story_snippet', 'payload' => 'Berada tepat di dalam Kampus Universitas Muhammadiyah Riau (UMRI), UM.CO House hadir dengan arsitektur modern berdenah huruf H. Area indoor ber-AC sejuk untuk fokus nugas dan kerja, berpadu dengan area outdoor bersemarak aneka stan street food sejak sore hingga malam.'],
            ['group' => 'brand', 'key' => 'footer_wordmark', 'payload' => 'UM.CO'],
            ['group' => 'brand', 'key' => 'signature_drink_image', 'payload' => '/images/signature-drink.png'],
            ['group' => 'brand', 'key' => 'hero_headline', 'payload' => 'Ruang tenang untuk nugas, tempat ramai untuk bercerita.'],
            ['group' => 'brand', 'key' => 'hero_description', 'payload' => 'Coffee shop modern berarsitektur huruf H di jantung Kampus UMRI Pekanbaru. Kopi pilihan, donat artisan, dan street food outdoor yang hidup sejak pagi hingga malam.'],

            // Contact
            ['group' => 'contact', 'key' => 'address', 'payload' => 'KAMPUS UMRI, Jl. Tuanku Tambusai No.2, Delima, Binawidya, Kota Pekanbaru, Riau 28291'],
            ['group' => 'contact', 'key' => 'coordinates', 'payload' => '0.4999292, 101.4155284'],
            ['group' => 'contact', 'key' => 'plus_code', 'payload' => 'FCX8+X6 Delima, Kota Pekanbaru, Riau'],
            ['group' => 'contact', 'key' => 'google_maps_url', 'payload' => 'https://www.google.com/maps/place/UMCO+HOUSE/@0.4999292,101.4155284,17z/data=!4m17!1m10!3m9!1s0x31d5a9dbd040689f:0x576c80063acf228c!2sUMCO+HOUSE!8m2!3d0.4999292!4d101.4155284!10e5!14m1!1BCgIYIQ!16s%2Fg%2F11y73ysxhk!3m5!1s0x31d5a9dbd040689f:0x576c80063acf228c!8m2!3d0.4999292!4d101.4155284!16s%2Fg%2F11y73ysxhk'],
            ['group' => 'contact', 'key' => 'instagram_url', 'payload' => 'https://www.instagram.com/um.co_house'],
            ['group' => 'contact', 'key' => 'instagram_handle', 'payload' => '@um.co_house'],
            ['group' => 'contact', 'key' => 'phone_wa', 'payload' => '082248001571'],
            ['group' => 'contact', 'key' => 'collab_template', 'payload' => 'Halo UM.CO House, saya [nama] dari [himpunan/komunitas]. Kami ingin berkolaborasi / mengadakan acara di UM.CO House. Boleh minta info dan syarat ketentuannya?'],

            // Hours
            ['group' => 'hours', 'key' => 'opening_time', 'payload' => '08:00'],
            ['group' => 'hours', 'key' => 'closing_time', 'payload' => '21:00'],
            ['group' => 'hours', 'key' => 'operating_days', 'payload' => 'Buka Setiap Hari (Senin - Minggu)'],

            // Stats & Google Rating
            ['group' => 'rating', 'key' => 'google_rating', 'payload' => '4.9'],
            ['group' => 'rating', 'key' => 'google_reviews_count', 'payload' => '86+'],
            ['group' => 'rating', 'key' => 'price_range', 'payload' => 'Rp 25.000 – Rp 50.000 / orang'],
            ['group' => 'rating', 'key' => 'last_rating_sync', 'payload' => 'November 2026'],

            // Reservation Rules
            ['group' => 'reservation', 'key' => 'slot_interval', 'payload' => '30'],
            ['group' => 'reservation', 'key' => 'last_slot_before_close', 'payload' => '60'],
            ['group' => 'reservation', 'key' => 'capacity_per_slot', 'payload' => '40'],
            ['group' => 'reservation', 'key' => 'min_advance_minutes', 'payload' => '60'],
            ['group' => 'reservation', 'key' => 'max_advance_days', 'payload' => '14'],
            ['group' => 'reservation', 'key' => 'max_party_size_online', 'payload' => '12'],
            ['group' => 'reservation', 'key' => 'dp_percentage', 'payload' => '30'],
            ['group' => 'reservation', 'key' => 'dp_minimum', 'payload' => '25000'],
            ['group' => 'reservation', 'key' => 'retention_days', 'payload' => '90'],

            // Payment (Placeholder - requires client confirmation)
            ['group' => 'payment', 'key' => 'bank_accounts', 'payload' => json_encode([
                ['bank' => 'Bank Mandiri', 'account_number' => '', 'account_name' => '', 'notes' => 'Menunggu data rekening resmi dari Owner'],
                ['bank' => 'Bank BCA', 'account_number' => '', 'account_name' => '', 'notes' => 'Menunggu data rekening resmi dari Owner'],
            ])],
            ['group' => 'payment', 'key' => 'qris_image_path', 'payload' => ''],
            ['group' => 'payment', 'key' => 'payment_notes', 'payload' => 'Informasi transfer bank & QRIS sedang dalam verifikasi pengelola. Silakan konfirmasi bukti transfer atau reservasi langsung ke WhatsApp Admin.'],

            // WhatsApp Templates
            ['group' => 'whatsapp_templates', 'key' => 'confirmation', 'payload' => 'Halo Kak {nama}, kami dari Admin UM.CO House mengonfirmasi reservasi Kakak (Kode: {kode}) untuk {jumlah} orang pada {tanggal} pukul {jam} WIB. Ditunggu kehadirannya di UM.CO House ya!'],
            ['group' => 'whatsapp_templates', 'key' => 'cancellation', 'payload' => 'Halo Kak {nama}, mohon maaf dari Admin UM.CO House terkait reservasi {kode} pada {tanggal} {jam} WIB ada penyesuaian jadwal / menu habis. Silakan hubungi kami untuk opsi meja pengganti.'],
            ['group' => 'whatsapp_templates', 'key' => 'reminder', 'payload' => 'Halo Kak {nama}, mengingatkan reservasi Kakak di UM.CO House hari ini pukul {jam} WIB ({jumlah} orang, Kode: {kode}). Meja sudah disiapkan!'],

            // SEO
            ['group' => 'seo', 'key' => 'meta_title', 'payload' => 'UM.CO House — Coffee Shop & Ruang Kreatif Kampus UMRI Pekanbaru'],
            ['group' => 'seo', 'key' => 'meta_description', 'payload' => 'Coffee shop modern berdenah huruf H di dalam Kampus UMRI Pekanbaru. Pilihan indoor full AC untuk nugas dan kerja remote, serta area outdoor street food yang hidup sore hingga malam.'],
            ['group' => 'seo', 'key' => 'og_image', 'payload' => '/images/og-image.jpg'],
        ];

        foreach ($settingsData as $s) {
            Setting::updateOrCreate(
                ['key' => $s['key']],
                ['group' => $s['group'], 'payload' => $s['payload']]
            );
        }
        Settings::flush();

        // 3. Menu Categories
        $categories = [
            ['name' => 'Signature Coffee', 'slug' => 'signature-coffee', 'sort' => 1],
            ['name' => 'Espresso Based', 'slug' => 'espresso-based', 'sort' => 2],
            ['name' => 'Non-Coffee', 'slug' => 'non-coffee', 'sort' => 3],
            ['name' => 'Artisan Donuts & Pastry', 'slug' => 'donuts-pastry', 'sort' => 4],
            ['name' => 'Street Food & Snacks', 'slug' => 'street-food-snacks', 'sort' => 5],
            ['name' => 'Makanan Berat', 'slug' => 'makanan-berat', 'sort' => 6],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['slug']] = MenuCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'sort' => $cat['sort'], 'is_active' => true]
            );
        }

        // 4. Menu Items (Realistic prices Rp 25.000 - Rp 42.000, marked as sample/curated)
        $items = [
            // Signature Coffee
            [
                'category_slug' => 'signature-coffee',
                'name' => 'Kopi Susu UMCO Aren',
                'slug' => 'kopi-susu-umco-aren',
                'description' => 'Espresso arabika blend dengan gula aren cair organik dan susu segar gurih creamy. Rasa seimbang khas kampus.',
                'price' => 28000,
                'is_favorite' => true,
                'favorite_order' => 1,
                'is_new' => false,
                'is_available' => true,
                'sort' => 1,
            ],
            [
                'category_slug' => 'signature-coffee',
                'name' => 'Creamy Vanilla Latte',
                'slug' => 'creamy-vanilla-latte',
                'description' => 'Espresso double shot dengan sirup vanilla madagascar dan lapisan microfoam susu lembut.',
                'price' => 32000,
                'is_favorite' => true,
                'favorite_order' => 2,
                'is_new' => false,
                'is_available' => true,
                'sort' => 2,
            ],
            [
                'category_slug' => 'signature-coffee',
                'name' => 'Coconut Pandan Cold Brew',
                'slug' => 'coconut-pandan-cold-brew',
                'description' => 'Cold brew steeping 18 jam berpadu air kelapa segar dan aroma pandan wangi. Sangat segar di siang terik Pekanbaru.',
                'price' => 34000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => true,
                'is_available' => true,
                'sort' => 3,
            ],
            [
                'category_slug' => 'signature-coffee',
                'name' => 'Spanish Latte UMRI',
                'slug' => 'spanish-latte-umri',
                'description' => 'Perpaduan espresso pekat dengan condensed milk manis gurih. Teman setia sesi nugas malam.',
                'price' => 32000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 4,
            ],

            // Espresso Based
            [
                'category_slug' => 'espresso-based',
                'name' => 'Cafe Latte',
                'slug' => 'cafe-latte',
                'description' => 'Espresso double shot dengan steamed fresh milk dan latte art presisi.',
                'price' => 28000,
                'is_favorite' => true,
                'favorite_order' => 3,
                'is_new' => false,
                'is_available' => true,
                'sort' => 1,
            ],
            [
                'category_slug' => 'espresso-based',
                'name' => 'Cappuccino',
                'slug' => 'cappuccino',
                'description' => 'Keseimbangan sepertiga espresso, steamed milk, dan foam tebal bertabur bubuk cokelat kakao.',
                'price' => 28000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 2,
            ],
            [
                'category_slug' => 'espresso-based',
                'name' => 'Americano / Long Black',
                'slug' => 'americano-long-black',
                'description' => 'Double shot espresso diekstraksi di atas air mineral panas atau dingin dengan krema keemasan utuh.',
                'price' => 25000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 3,
            ],
            [
                'category_slug' => 'espresso-based',
                'name' => 'Caramel Macchiato',
                'slug' => 'caramel-macchiato',
                'description' => 'Steamed milk dengan sirup vanilla, ditimpa espresso pekat dan siraman saus karamel panggang.',
                'price' => 34000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 4,
            ],

            // Non-Coffee
            [
                'category_slug' => 'non-coffee',
                'name' => 'Matcha Latte Uji',
                'slug' => 'matcha-latte-uji',
                'description' => 'Bubuk matcha murni asal Uji Jepang dengan rasa earthy manis lembut dan susu segar.',
                'price' => 32000,
                'is_favorite' => true,
                'favorite_order' => 4,
                'is_new' => false,
                'is_available' => true,
                'sort' => 1,
            ],
            [
                'category_slug' => 'non-coffee',
                'name' => 'Artisan Dark Chocolate',
                'slug' => 'artisan-dark-chocolate',
                'description' => 'Cokelat hitam 70% kakao meleleh kaya rasa tanpa rasa getir berlebih, disajikan panas atau dingin.',
                'price' => 30000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 2,
            ],
            [
                'category_slug' => 'non-coffee',
                'name' => 'Earl Grey Berry Iced Tea',
                'slug' => 'earl-grey-berry-iced-tea',
                'description' => 'Seduhan teh Earl Grey beraroma bergamot dengan sirup mixed berry segar dan irisan lemon.',
                'price' => 26000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 3,
            ],
            [
                'category_slug' => 'non-coffee',
                'name' => 'Lychee Sparkling Yakult',
                'slug' => 'lychee-sparkling-yakult',
                'description' => 'Soda berkarbonasi dingin dengan probiotik Yakult dan buah leci asli kenyal.',
                'price' => 28000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => true,
                'is_available' => true,
                'sort' => 4,
            ],

            // Artisan Donuts & Pastry
            [
                'category_slug' => 'donuts-pastry',
                'name' => 'Donat Artisan Brulee Cinnamon',
                'slug' => 'donat-artisan-brulee-cinnamon',
                'description' => 'Donat kentang artisan empuk dengan lapisan gula terkaramelisasi garing dan aroma kayu manis.',
                'price' => 18000,
                'is_favorite' => true,
                'favorite_order' => 5,
                'is_new' => false,
                'is_available' => true,
                'sort' => 1,
            ],
            [
                'category_slug' => 'donuts-pastry',
                'name' => 'Donat Cokelat Ganache Klasik',
                'slug' => 'donat-cokelat-ganache-klasik',
                'description' => 'Donat montok berselimut dark chocolate ganache tebal bertabur dark cocoa nibs.',
                'price' => 18000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 2,
            ],
            [
                'category_slug' => 'donuts-pastry',
                'name' => 'Butter Croissant Flaky',
                'slug' => 'butter-croissant-flaky',
                'description' => 'Pastry mentega Prancis dengan lapisan renyah garing di luar dan rongga sarang lebah lembut di dalam.',
                'price' => 25000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 3,
            ],

            // Street Food & Snacks
            [
                'category_slug' => 'street-food-snacks',
                'name' => 'Tahu Walik Crispy Sambal Kecap',
                'slug' => 'tahu-walik-crispy-sambal-kecap',
                'description' => 'Tahu pong dibalik isi adonan daging ayam kenyal berbumbu, digoreng garing dengan cocolan sambal kecap cabai rawit pedas.',
                'price' => 24000,
                'is_favorite' => true,
                'favorite_order' => 6,
                'is_new' => false,
                'is_available' => true,
                'sort' => 1,
            ],
            [
                'category_slug' => 'street-food-snacks',
                'name' => 'Cireng Salju Bumbu Rujak',
                'slug' => 'cireng-salju-bumbu-rujak',
                'description' => 'Cireng garing di luar kenyal lembut di dalam, disajikan hangat dengan kuah rujak asam pedas manis.',
                'price' => 22000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 2,
            ],
            [
                'category_slug' => 'street-food-snacks',
                'name' => 'Crispy Chicken Popcorn BBQ',
                'slug' => 'crispy-chicken-popcorn-bbq',
                'description' => 'Potongan paha ayam renyah tanpa tulang bertabur bumbu smokey BBQ dan rempah gurih.',
                'price' => 28000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 3,
            ],

            // Makanan Berat
            [
                'category_slug' => 'makanan-berat',
                'name' => 'Rice Bowl Ayam Sambal Matah',
                'slug' => 'rice-bowl-ayam-sambal-matah',
                'description' => 'Nasi putih hangat dengan ayam goreng krispi keemasan, sambal matah khas Bali segar wangi serai, dan telur mata sapi setengah matang.',
                'price' => 36000,
                'is_favorite' => true,
                'favorite_order' => 7,
                'is_new' => false,
                'is_available' => true,
                'sort' => 1,
            ],
            [
                'category_slug' => 'makanan-berat',
                'name' => 'Nasi Goreng Kampung UMCO',
                'slug' => 'nasi-goreng-kampung-umco',
                'description' => 'Nasi goreng harum rempah tradisional dengan suwiran ayam, bakso, telur ceplok, acar segar, dan kerupuk renyah.',
                'price' => 34000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 2,
            ],
            [
                'category_slug' => 'makanan-berat',
                'name' => 'Spaghetti Aglio Olio Smoked Beef',
                'slug' => 'spaghetti-aglio-olio-smoked-beef',
                'description' => 'Pasta al dente ditumis dengan minyak zaitun, irisan bawang putih renyah, cabai kering, dan potongan smoked beef gurih.',
                'price' => 38000,
                'is_favorite' => false,
                'favorite_order' => 0,
                'is_new' => false,
                'is_available' => true,
                'sort' => 3,
            ],
        ];

        foreach ($items as $item) {
            $cat = $catModels[$item['category_slug']] ?? null;
            if ($cat) {
                MenuItem::updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'category_id' => $cat->id,
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'is_favorite' => $item['is_favorite'],
                        'favorite_order' => $item['favorite_order'],
                        'is_new' => $item['is_new'],
                        'is_available' => $item['is_available'],
                        'is_active' => true,
                        'sort' => $item['sort'],
                    ]
                );
            }
        }

        // 5. Banners (Serbaguna dinamis)
        Banner::updateOrCreate(
            ['title' => 'Ruang Nugas Ber-AC Kampus UMRI'],
            [
                'description' => 'Nikmati koneksi Wi-Fi kencang, colokan listrik di setiap meja, dan racikan kopi fresh untuk teman skripsi dan tugas kelompokmu.',
                'cta_label' => 'Jelajahi Suasana Kami',
                'cta_url' => '/about',
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'sort' => 1,
            ]
        );

        Banner::updateOrCreate(
            ['title' => 'Sore Santai & Street Food Outdoor'],
            [
                'description' => 'Saat matahari mulai condong, nikmati angin sejuk outdoor dengan beragam sajian camilan dan jajanan gerobak street food favorit.',
                'cta_label' => 'Lihat Menu Lengkap',
                'cta_url' => '/menu#street-food-snacks',
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'sort' => 2,
            ]
        );

        // 6. Facilities
        $facilities = [
            ['label' => 'Indoor Full AC Sejuk', 'icon' => 'sparkles', 'sort' => 1],
            ['label' => 'Colokan Listrik Lengkap', 'icon' => 'bolt', 'sort' => 2],
            ['label' => 'Wi-Fi Cepat Ramah Nugas', 'icon' => 'wifi', 'sort' => 3],
            ['label' => 'Area Outdoor Terbuka & Adem', 'icon' => 'sun', 'sort' => 4],
            ['label' => 'Stan Street Food Aneka Pilihan', 'icon' => 'cake', 'sort' => 5],
            ['label' => 'Parkir Luas Area Kampus UMRI', 'icon' => 'truck', 'sort' => 6],
            ['label' => 'Layanan Dine-in & Takeaway', 'icon' => 'shopping-bag', 'sort' => 7],
            ['label' => 'Musholla & Toilet Kampus Bersih', 'icon' => 'building-storefront', 'sort' => 8],
        ];

        foreach ($facilities as $fac) {
            Facility::updateOrCreate(
                ['label' => $fac['label']],
                ['icon' => $fac['icon'], 'sort' => $fac['sort'], 'is_active' => true]
            );
        }

        // 7. Street Food Stalls (Outdoor directory)
        $stalls = [
            [
                'name' => 'Stan Tahu Walik Crispy Maknyus',
                'signature_item' => 'Tahu Walik Isi Daging Ayam',
                'description' => 'Gorengan tahu khas krispi dengan cocolan sambal kecap cabai rawit pedas mantap.',
                'active_hours' => '15:00 - 21:00 WIB',
                'sort' => 1,
            ],
            [
                'name' => 'Corner Donat Artisan & Bakery',
                'signature_item' => 'Donat Brulee Cinnamon & Croissant',
                'description' => 'Pastry dan donat kentang lembut yang dipanggang segar setiap pagi dan sore hari.',
                'active_hours' => '08:00 - 21:00 WIB',
                'sort' => 2,
            ],
            [
                'name' => 'Sate Taichan & Grill Skewers Kampus',
                'signature_item' => 'Taichan Daging Dada Sambal Jeruk Nipis',
                'description' => 'Sate ayam panggang gurih tanpa kecap dengan sambal ulek pedas gurih menyegarkan.',
                'active_hours' => '16:00 - 21:00 WIB',
                'sort' => 3,
            ],
            [
                'name' => 'Kedai Mocktail Segar & Sparkling',
                'signature_item' => 'Lychee Mint Cooler & Yakult Mix',
                'description' => 'Minuman dingin menyegarkan pelepas dahaga setelah kuliah padat seharian.',
                'active_hours' => '10:00 - 21:00 WIB',
                'sort' => 4,
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

        // 8. Story Blocks (About Page Narrative)
        $stories = [
            [
                'page' => 'about',
                'key' => 'philosophy',
                'title' => 'Dari Kampus, Untuk Ruang Berpikir dan Berbagi Rasa',
                'body' => 'UM.CO House lahir dari pemahaman sederhana: kehidupan kampus membutuhkan tempat singgah yang menghargai ketenangan berpikir di siang hari, dan kehangatan obrolan di malam hari. Kami bukan sekadar kedai kopi, melainkan titik temu tempat gagasan akademik, karya kreatif, dan pertemanan mahasiswa dirajut dalam suasana yang nyaman.',
                'sort' => 1,
            ],
            [
                'page' => 'about',
                'key' => 'architecture_h',
                'title' => 'Arsitektur Huruf H: Dua Sayap Harmonis',
                'body' => 'Bangunan UM.CO House memiliki tata ruang unik berdenah huruf H. Sayap kiri menaungi area Indoor full AC berpenataan tenang, dilengkapi stopkontak di setiap sudut meja dan pencahayaan lembut yang cocok untuk laptopan, menyusun skripsi, atau rapat komunitas. Palang tengah menjadi lorong barista penghubung, sementara sayap kanan membentang ke area Outdoor bersemarak aneka stan street food kuliner lokal yang hidup dan syahdu sejak senja tiba.',
                'sort' => 2,
            ],
            [
                'page' => 'about',
                'key' => 'bean_journey_1',
                'title' => '1. Kurasi Biji & Sangrai Terukur',
                'body' => 'Kami memilih biji kopi Nusantara pilihan dengan profil sangrai medium-dark yang menghadirkan karakter cokelat, kacang, dan sentuhan karamel manis alami tanpa rasa asam menusuk.',
                'sort' => 3,
            ],
            [
                'page' => 'about',
                'key' => 'bean_journey_2',
                'title' => '2. Ekstraksi Presisi Tiap Cangkir',
                'body' => 'Barista kami mengukur rasio gilingan, suhu air, dan tekanan ekstraksi secara teliti, memastikan tiap shot espresso memiliki krema keemasan utuh yang memanjakan lidah.',
                'sort' => 4,
            ],
            [
                'page' => 'about',
                'key' => 'bean_journey_3',
                'title' => '3. Sentuhan Rasa Ramah Kampus',
                'body' => 'Ditemani susu segar pilihan dan racikan gula aren murni, sajian UMCO mengalirkan energi hangat yang siap menemani setiap obrolan dan pencapaian akademikmu.',
                'sort' => 5,
            ],
        ];

        foreach ($stories as $story) {
            StoryBlock::updateOrCreate(
                ['page' => $story['page'], 'key' => $story['key']],
                [
                    'title' => $story['title'],
                    'body' => $story['body'],
                    'sort' => $story['sort'],
                ]
            );
        }

        // 9. Testimonials (Marked as is_sample = true for transparency)
        $testimonials = [
            [
                'name' => 'Fajar Ramadhan',
                'role' => 'Mahasiswa Teknik UMRI',
                'rating' => 5,
                'body' => 'Tempat nugas paling juara di dalam kampus. AC-nya dingin, colokan listrik melimpah di tiap meja, dan Kopi Susu Aren-nya pas banget di kantong mahasiswa. Sangat terbantu buat beresin skripsi.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 1,
            ],
            [
                'name' => 'Sarah Nabilah',
                'role' => 'Pekerja Remote Pekanbaru',
                'rating' => 5,
                'body' => 'Denah gedungnya unik berbentuk H. Siang kerja fokus di indoor yang tenang, sore menjelang malam pindah ke outdoor menikmati udara terbuka sambil jajan tahu walik dan cireng. Suasananya hidup banget.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 2,
            ],
            [
                'name' => 'Dimas Pratama',
                'role' => 'Komunitas Kreatif Riau',
                'rating' => 5,
                'body' => 'Cafe latte-nya solid, donat artisan brulee-nya legit banget! Pelayanannya ramah dan space outdoor-nya luas buat gathering santai bareng teman kampus.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 3,
            ],
            [
                'name' => 'Anisa Rahmawati',
                'role' => 'Mahasiswi Ekonomi UMRI',
                'rating' => 5,
                'body' => 'Suka banget sama Matcha Latte dan Rice Bowl Ayam Sambal Matah-nya. Bersih, adem, stafnya sigap, dan parkirnya gampang karena di dalam area kampus.',
                'source' => 'google',
                'is_featured' => true,
                'is_sample' => true,
                'sort' => 4,
            ],
        ];

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
    }
}
