<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforcePasswordChange
{
    /**
     * Redirect authenticated staff flagged with must_change_password to the profile password page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (bool) ($user->must_change_password ?? false)) {
            if (! $request->routeIs('filament.admin.auth.profile', 'filament.admin.auth.logout') && ! $request->expectsJson()) {
                Notification::make()
                    ->title('Ganti sandi sementara')
                    ->body('Demi keamanan akun, silakan perbarui kata sandi Anda terlebih dahulu sebelum mengakses backoffice.')
                    ->warning()
                    ->send();

                return redirect()->route('filament.admin.auth.profile');
            }
        }

        return $next($request);
    }
}
