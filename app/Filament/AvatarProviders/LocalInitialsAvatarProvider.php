<?php

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class LocalInitialsAvatarProvider implements AvatarProvider
{
    public function get(Model|Authenticatable $record): string
    {
        $name = trim((string) Filament::getNameForDefaultAvatar($record));
        $segments = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: ['U'];
        $initials = collect($segments)
            ->take(2)
            ->map(static fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        $initials = htmlspecialchars($initials !== '' ? $initials : 'U', ENT_QUOTES, 'UTF-8');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
            .'<rect width="64" height="64" rx="32" fill="#134731"/>'
            .'<text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" '
            .'fill="#FFFFFF" font-family="system-ui,-apple-system,sans-serif" font-size="24" font-weight="600">'
            .$initials
            .'</text></svg>';

        return 'data:image/svg+xml;charset=UTF-8,'.rawurlencode($svg);
    }
}
