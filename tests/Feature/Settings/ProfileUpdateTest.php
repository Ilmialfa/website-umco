<?php

use App\Models\User;
use Filament\Auth\Pages\EditProfile;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('legacy starter kit profile endpoint is disabled', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/settings/profile')
        ->assertNotFound();
});

test('staff profile page renders inside admin panel and password update clears must_change_password flag', function () {
    $staff = User::factory()->create([
        'role' => 'admin',
        'must_change_password' => true,
        'password' => 'OldPassword#2026',
    ]);

    $this->actingAs($staff)
        ->get('/admin/profile')
        ->assertOk();

    Livewire::actingAs($staff)
        ->test(EditProfile::class)
        ->fillForm([
            'name' => 'Barista Kepala UMCO',
            'email' => $staff->email,
            'currentPassword' => 'OldPassword#2026',
            'password' => 'NewStrongPass#2026',
            'passwordConfirmation' => 'NewStrongPass#2026',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $staff->refresh();
    expect($staff->name)->toBe('Barista Kepala UMCO');
    expect($staff->must_change_password)->toBeFalse();
    expect(Hash::check('NewStrongPass#2026', $staff->password))->toBeTrue();
});
