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
        Schema::create('partner', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('balance')->default(0)->nullable(false);
            $table->timestamps();
        });

        Schema::create('site_balance', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->bigInteger('balance');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_balance');
        Schema::dropIfExists('partner');
    }
};
