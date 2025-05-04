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
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mail_driver')->default('smtp');
            $table->string('mail_host')->default('smtp.mailtrap.io');
            $table->string('mail_port')->default('2525');
            $table->string('mail_username')->default('username');
            $table->string('mail_password')->default('password');
            $table->string('mail_encryption')->default('tls');
            $table->string('mail_from_address')->default('noreplay@gmail.com');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};
