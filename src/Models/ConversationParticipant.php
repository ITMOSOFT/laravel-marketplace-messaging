<?php

namespace ITMOSOFT\MarketplaceMessaging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ConversationParticipant extends Model
{
    protected $table = 'itmosoft_conversation_participants';

    protected $attributes = [
        'role' => 'member',
        'status' => 'active',
    ];

    protected $fillable = [
        'conversation_type', 'conversation_id', 'participant_type', 'participant_id',
        'role', 'status', 'joined_at', 'left_at', 'last_read_at', 'metadata',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'last_read_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function conversation(): MorphTo
    {
        return $this->morphTo();
    }

    public function participant(): MorphTo
    {
        return $this->morphTo();
    }

    public function markRead(?\DateTimeInterface $at = null): self
    {
        $this->last_read_at = $at ?? now();
        $this->save();

        return $this;
    }
}
