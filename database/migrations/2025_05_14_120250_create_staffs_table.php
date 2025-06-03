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
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('designation_id')->constrained('designations')->onDelete('cascade');
            $table->string('national_id_no')->nullable();
            $table->date('joining_date')->nullable();
            $table->enum('type', ['Full Time', 'Part Time', 'Contractual'])->default('Full Time');
            $table->enum('status', ['Running', 'Resigned', 'Retired', 'Suspended'])->default('Running');
            $table->date('resignation_date')->nullable();
            $table->date('retirement_date')->nullable();
            $table->date('suspension_date')->nullable();
            $table->text('resignation_reason')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
