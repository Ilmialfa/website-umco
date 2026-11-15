<?php

namespace App\Console\Commands;

use App\Services\InstagramFeedService;
use Illuminate\Console\Command;

class SyncInstagramFeed extends Command
{
    protected $signature = 'instagram:sync {--limit=8 : Jumlah maksimum postingan yang disinkronkan}';

    protected $description = 'Sinkronisasi berkala feed Instagram @um.co_house ke dalam cache agar halaman beranda tidak memanggil HTTP secara sinkron.';

    public function handle(InstagramFeedService $service): int
    {
        $limit = max(1, min(12, (int) $this->option('limit')));
        $result = $service->getFeed($limit, true);
        $source = $result['source'] ?? 'curated';
        $count = is_array($result['posts'] ?? null) ? count($result['posts']) : 0;

        $this->info("Instagram sync selesai ({$source}): {$count} item.");

        return self::SUCCESS;
    }
}
