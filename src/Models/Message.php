<?php

namespace ITMOSOFT\MarketplaceMessaging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    protected $table = 'itmosoft_messages';

    protected $fillable = [
        'conversation_type', 'conversation_id', 'sender_type', 'sender_id',
        'body', 'metadata', 'edited_at', 'deleted_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function conversation(): MorphTo
    {
        return $this->morphTo();
    }

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(
            config('itmosoft-marketplace-messaging.attachment_model', MessageAttachment::class),
            'message'
        );
    }

    public function edit(string $body): self
    {
        $this->body = $body;
        $this->edited_at = now();
        $this->save();

        return $this;
    }

    public function softDeleteMessage(): self
    {
        $this->deleted_at = now();
        $this->body = '';
        $this->save();

        return $this;
    }
}
