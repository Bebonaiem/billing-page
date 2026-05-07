<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user or promote existing user to administrator';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== User Management ===');
        
        // Ask if user wants to create new user or promote existing
        $action = $this->choice('What would you like to do?', [
            'Create a new user',
            'Promote existing user to admin'
        ]);

        if ($action === 'Create a new user') {
            return $this->createNewUser();
        } else {
            return $this->promoteExistingUser();
        }
    }

    /**
     * Create a new user with admin privileges
     */
    private function createNewUser()
    {
        $this->info("\n=== Create New User ===");

        // Get user details
        $firstName = $this->ask('First name');
        $lastName = $this->ask('Last name');
        $email = $this->ask('Email address');
        $password = $this->secret('Password');
        $passwordConfirmation = $this->secret('Confirm password');

        // Validate input
        $validator = Validator::make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $this->error("\nValidation errors:");
            foreach ($validator->errors()->all() as $error) {
                $this->error("- {$error}");
            }
            return 1;
        }

        // Ask for role
        $role = $this->choice('Select user role:', [
            'admin' => 'Administrator (full access)',
            'user' => 'Regular user (limited access)'
        ]);

        // Create user
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $firstName . ' ' . $lastName,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => $role === 'admin',
            'status' => 'active',
        ]);

        $this->info("\n✅ User created successfully!");
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Role: " . ($user->is_admin ? 'Administrator' : 'User'));
        $this->info("Status: {$user->status}");

        return 0;
    }

    /**
     * Promote existing user to admin
     */
    private function promoteExistingUser()
    {
        $this->info("\n=== Promote Existing User ===");

        // Get email
        $email = $this->ask('Enter the email address of the user to promote');

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        // Show current user info
        $this->info("\nCurrent user information:");
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Current role: " . ($user->is_admin ? 'Administrator' : 'User'));
        $this->info("Status: {$user->status}");

        // Ask for role change
        $newRole = $this->choice('Select new role:', [
            'admin' => 'Administrator (full access)',
            'user' => 'Regular user (limited access)'
        ]);

        $isAdmin = $newRole === 'admin';

        // Update user
        $user->is_admin = $isAdmin;
        $user->save();

        $this->info("\n✅ User role updated successfully!");
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("New role: " . ($user->is_admin ? 'Administrator' : 'User'));

        return 0;
    }
}
