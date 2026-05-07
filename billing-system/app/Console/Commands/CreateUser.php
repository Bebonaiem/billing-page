<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    protected $signature = 'app:user:create';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        $this->info('Create a new admin user');
        $this->line('');

        $email = $this->ask('Email address');

        if (User::where('email', $email)->exists()) {
            $this->error("User with email '{$email}' already exists!");
            return 1;
        }

        $name = $this->ask('Full name');
        $first_name = $this->ask('First name');
        $last_name = $this->ask('Last name');
        $password = $this->secret('Password');

        $confirm = $this->confirm('Create user?');
        if (!$confirm) {
            $this->line('Aborted.');
            return 0;
        }

        User::create([
            'name' => $name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => 'active',
        ]);

        $this->info("✓ User '{$email}' created successfully!");
        return 0;
    }
}
