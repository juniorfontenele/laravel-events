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
        $tablePrefix = config('events.table_prefix', 'laravel_events_');

        Schema::create($tablePrefix . 'event_registry', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';

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

        Schema::create($tablePrefix . 'events', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';

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
        $tablePrefix = config('events.table_prefix', 'laravel_events_');

        Schema::dropIfExists($tablePrefix . 'event_registry');
        Schema::dropIfExists($tablePrefix . 'events');
    }
};
