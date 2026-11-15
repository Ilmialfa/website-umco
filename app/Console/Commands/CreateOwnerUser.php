<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateOwnerUser extends Command
{
    protected $signature = 'umco:create-owner
        {--name=Owner UMCO : Nama lengkap owner}
        {--email= : Email login owner}
        {--password= : Kata sandi minimal 10 karakter (kosongkan untuk buat sandi acak aman)}
        {--force-change : Wajibkan ganti kata sandi saat login pertama}';

    protected $description = 'Buat atau perbarui akun owner UMCO dengan kata sandi aman untuk lingkungan produksi';

    public function handle(): int
    {
        $name = trim((string) $this->option('name')) ?: 'Owner UMCO';
        $email = trim((string) ($this->option('email') ?: $this->ask('Email owner')));

        $emailValidator = Validator::make(['email' => $email], [
            'email' => ['required', 'email'],
        ]);

        if ($emailValidator->fails()) {
            $this->error('Format email tidak valid.');

            return self::FAILURE;
        }

        $rawPassword = $this->option('password');
        $generated = false;

        if ($rawPassword === null && $this->input->isInteractive()) {
            $rawPassword = $this->secret('Kata sandi minimal 10 karakter (kosongkan untuk membuat otomatis)');
        }

        if (! is_string($rawPassword) || $rawPassword === '') {
            $rawPassword = Str::password(16);
            $generated = true;
        }

        if (mb_strlen($rawPassword) < 10) {
            $this->error('Kata sandi minimal 10 karakter.');

            return self::FAILURE;
        }

        $mustChange = (bool) $this->option('force-change') || $generated;

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($rawPassword),
                'role' => 'owner',
                'must_change_password' => $mustChange,
                'email_verified_at' => now(),
            ]
        );

        $this->info("Akun owner siap: {$user->email}");

        if ($generated) {
            $this->warn("Kata sandi sementara (simpan sekarang, tidak ditampilkan lagi): {$rawPassword}");
        }

        if ($mustChange) {
            $this->line('User wajib mengganti kata sandi di halaman Profil (/admin/profile) setelah login pertama.');
        }

        return self::SUCCESS;
    }
}
