<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idempotency_records', function (Blueprint $table) {

            $table->id();

            $table->string('key_hash')->unique();

            $table->string('fingerprint');

            $table->string('status');

            $table->unsignedSmallInteger('status_code')->nullable();

            $table->json('headers')->nullable();

            $table->longText('body')->nullable();

            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_records');
    }
};