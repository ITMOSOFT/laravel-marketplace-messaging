<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itmosoft_messages', function (Blueprint $table) {
            $table->id();
            $table->morphs('conversation');
            $table->nullableMorphs('sender');
            $table->longText('body');
            $table->json('metadata')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->index(['conversation_type', 'conversation_id', 'id'], 'itmosoft_message_conversation_order_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itmosoft_messages');
    }
};
