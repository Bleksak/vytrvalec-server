<?php

declare(strict_types=1);

namespace Tests\Feature;

it('can register a user', function () {
    $response = test()->api()->post('user', [
        'json' => [
            'email' => 'asdf+1@asdf.com',
            'password' => 'Qwerty145#!12',
            'first_name' => 'Testing',
            'last_name' => 'Test',
            'faculty' => 1,
        ],
    ]);

    expect($response->getStatusCode())->toBe(201);
    expect($response->getBody()->getContents())->toBeEmpty();
});

it('can login the user', function () {
    $response = test()->api()->post('user/login', [
        'json' => [
            'email' => 'asdf+1@asdf.com',
            'password' => 'Qwerty145#!12',
        ],
    ]);

    $data = json_decode($response->getBody()->getContents());

    expect($response->getStatusCode())->toBe(200);
    expect($data)->not()->toBeEmpty();
    expect($data)->toHaveKeys(['token', 'user']);
})->depends('it can register a user');

it('has an admin', function () {
    $token = test()->adminToken();

    $response = test()->api($token)->get('user/current');
    $data = json_decode($response->getBody()->getContents());

    expect($response->getStatusCode())->toBe(200);
    expect($data)->not()->toBeEmpty();
    expect($data->roles)->toContain('ROLE_STAFF');
});

it('has a user', function () {
    $token = test()->userToken();

    $response = test()->api($token)->get('user/current');
    $data = json_decode($response->getBody()->getContents());

    expect($response->getStatusCode())->toBe(200);
    expect($data)->not()->toBeEmpty();
    expect($data->roles)->toContain('ROLE_USER');
});
