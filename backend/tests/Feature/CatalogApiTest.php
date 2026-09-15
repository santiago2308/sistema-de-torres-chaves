<?php

use App\Models\Service;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('lists all active services with formatted prices', function () {
    Service::factory()->create([
        'name' => 'Sonido Básico',
        'category' => 'Sonido',
        'price' => 120000,
    ]);

    getJson('/api/v1/services')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [[
                'id', 'name', 'category', 'description', 'price', 'price_formatted',
            ]],
        ])
        ->assertJsonPath('data.0.price_formatted', '120.000');
});

it('filters services by category', function () {
    Service::factory()->create(['name' => 'Sonido Pro', 'category' => 'Sonido', 'price' => 200000]);
    Service::factory()->create(['name' => 'Luces', 'category' => 'Iluminación', 'price' => 100000]);

    getJson('/api/v1/services?category=Sonido')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Sonido Pro');
});

it('does not include disabled services', function () {
    Service::factory()->create(['name' => 'Oculto', 'is_active' => false]);

    getJson('/api/v1/services')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('returns a single service by id', function () {
    $service = Service::factory()->create(['name' => 'DJ + Sonido', 'price' => 280000]);

    getJson("/api/v1/services/{$service->id}")
        ->assertOk()
        ->assertJsonPath('data.name', 'DJ + Sonido')
        ->assertJsonPath('data.price_formatted', '280.000');
});

it('returns 404 for a missing service', function () {
    getJson('/api/v1/services/99999')->assertNotFound();
});

it('chatbot responds with service prices for a price question', function () {
    Service::factory()->create(['name' => 'Sonido Profesional', 'category' => 'Sonido', 'price' => 220000]);

    postJson('/api/v1/chat/message', [
        'session_id' => 'test-price',
        'message' => 'cuánto cuesta el sonido profesional',
    ])
        ->assertOk()
        ->assertJsonPath('data.show_whatsapp', true)
        ->assertJsonPath('data.session_id', 'test-price')
        ->assertJsonPath('data.message', fn (string $message) => str_contains($message, 'Sonido Profesional'));
});

it('chatbot stores the conversation history', function () {
    postJson('/api/v1/chat/message', [
        'session_id' => 'test-history',
        'message' => 'hola',
    ])->assertOk();

    getJson('/api/v1/chat/history/test-history')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('validates chat input', function () {
    postJson('/api/v1/chat/message', ['session_id' => 'x'])
        ->assertUnprocessable();
});