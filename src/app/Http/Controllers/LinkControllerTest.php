<?php

use Tests\TestCase;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);

it('has welcome page')->get('/')->assertStatus(200);

it('does not create a shortened link without a link field', function () {
    $response = $this->postJson('/api/shorten/link', []);
    $response->assertStatus(400);
});

it('can create a shortened link', function () {
    $link = Link::factory()->make();
    $response = $this->postJson('/api/shorten/link', ['link' => $link->original]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('links', ['original' => $link->original]);
});

it('can fetch links', function () {
    $response = $this->getJson("/api/shorten/links");
    $response->assertStatus(200);
});
