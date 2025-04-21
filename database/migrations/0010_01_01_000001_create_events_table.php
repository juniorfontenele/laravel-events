<?php

declare(strict_types = 1);

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
        Schema::create('event_registry', function (Blueprint $table) {
            $table->defaultCharset();
            $table->id();
            $table->string('name');
            $table->string('className')->unique();
            $table->string('type')->index();
            $table->string('level')->index();
            $table->string('description')->nullable();
            $table->boolean('shouldLog')->default(true);
            $table->boolean('shouldWriteToDatabase')->default(true);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->defaultCharset();
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('event_id')->nullable()->index();
            $table->string('event_class')->index();
            $table->string('level')->index();
            $table->string('type')->index();
            $table->string('description')->default('');
            $table->string('subject_id')->nullable()->index();
            $table->string('subject_type')->nullable()->index();
            $table->string('causer_id')->nullable()->index();
            $table->string('causer_type')->nullable()->index();
            $table->string('trace_id')->nullable()->index();
            $table->string('request_id')->nullable()->index();
            $table->longText('context')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registry');
        Schema::dropIfExists('events');
    }
};
