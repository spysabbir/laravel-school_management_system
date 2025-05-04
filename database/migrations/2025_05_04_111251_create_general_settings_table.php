<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('My Application');
            $table->string('app_url')->default('http://localhost');
            $table->string('app_email')->default('info@gmail.com');
            $table->string('app_phone')->default('1234567890');
            $table->string('app_address')->default('123 Main St, City, Country');
            $table->string('app_logo')->default('logo.png');
            $table->string('app_favicon')->default('favicon.ico');
            $table->string('app_timezone')->default('UTC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
