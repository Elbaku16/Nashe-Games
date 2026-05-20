<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('deal_id');
            $table->string('game_id')->nullable();
            $table->string('title');
            $table->string('thumb', 500)->nullable();
            $table->decimal('purchase_price', 8, 2)->default(0);
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'purchased_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_items');
    }
};
