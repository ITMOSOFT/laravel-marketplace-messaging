<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itmosoft_conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->morphs('conversation');
            $table->morphs('participant');
            $table->string('role', 32)->default('member');
            $table->string('status', 16)->default('active')->index();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(
                ['conversation_type', 'conversation_id', 'participant_type', 'participant_id'],
                'itmosoft_conversation_participant_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itmosoft_conversation_participants');
    }
};
