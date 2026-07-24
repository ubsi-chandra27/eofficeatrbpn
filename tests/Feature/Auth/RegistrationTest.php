<?php

use App\Models\Surat;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'umum',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('umum.dashboard', absolute: false));

    $user = User::where('email', 'test@example.com')->firstOrFail();
    expect(Surat::where('user_id', $user->id)->where('jenis_surat', 'masuk')->count())->toBeGreaterThanOrEqual(4);
});
