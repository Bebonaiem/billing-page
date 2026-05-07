<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pterodactyl_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('panel_url');
            $table->string('api_key', 500); // Encrypted
            $table->boolean('is_active')->default(true);
            $table->integer('max_servers')->nullable();
            $table->integer('current_servers')->default(0);
            $table->json('location_ids')->nullable(); // Available locations on this node
            $table->timestamps();
        });

        Schema::create('pterodactyl_eggs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('pterodactyl_nodes')->onDelete('cascade');
            $table->string('name');
            $table->string('egg_id'); // Pterodactyl egg ID
            $table->string('nest_id'); // Pterodactyl nest ID
            $table->string('docker_image');
            $table->json('startup_command');
            $table->json('environment_variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pterodactyl_eggs');
        Schema::dropIfExists('pterodactyl_nodes');
    }
};
