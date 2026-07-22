<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create(['role' => 'umum']);

    $response = $this->post('/login', [
        'identifier' => $user->email,
        'password' => 'password',
        'login_as' => 'umum',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('umum.dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create(['role' => 'umum']);

    $this->post('/login', [
        'identifier' => $user->email,
        'password' => 'wrong-password',
        'login_as' => 'umum',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});
