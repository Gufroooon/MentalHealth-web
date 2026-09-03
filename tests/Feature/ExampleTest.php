<?php

/**
 * Dokumentasi file: Test aplikasi.
 *
 * Menjelaskan tanggung jawab file tests/Feature/ExampleTest.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
 */

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
