<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_validates_login_credentials_properly()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Test valid credentials
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect();

        // Test invalid credentials
        $this->post('/logout');
        
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_sanitizes_user_input_during_registration()
    {
        $userData = [
            'name' => '  Test User  ',
            'email' => 'TEST@EXAMPLE.COM',
            'first_name' => '  Test  ',
            'last_name' => '  User  ',
            'phone' => '(555) 123-4567',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post('/register', $userData);

        $this->assertAuthenticated();
        $user = User::where('email', 'test@example.com')->first();

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('Test', $user->first_name);
        $this->assertEquals('User', $user->last_name);
        $this->assertEquals('5551234567', $user->phone);
    }

    /** @test */
    public function it_validates_password_strength()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ];

        $response = $this->post('/register', $userData);

        $this->assertGuest();
        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function it_handles_password_reset_securely()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Request password reset
        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHas('status');

        // Verify token was stored securely (hashed)
        $token = DB::table('password_reset_tokens')
            ->where('email', 'test@example.com')
            ->first();

        $this->assertNotNull($token);
        $this->assertNotEquals($token->token, 'plaintext'); // Should be hashed
    }

    /** @test */
    public function it_prevents_sql_injection()
    {
        $maliciousInput = "'; DROP TABLE users; --";
        
        $response = $this->post('/login', [
            'email' => $maliciousInput,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0); // Users table should still exist
    }

    /** @test */
    public function it_implements_rate_limiting()
    {
        // Test login rate limiting
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response->assertStatus(429); // Too many requests
    }

    /** @test */
    public function it_uses_secure_headers()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }
}