<?php

namespace ITMOSOFT\MarketplaceMessaging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ITMOSOFT\MarketplaceMessaging\Exceptions\InvalidConversationOperation;

class Conversation extends Model
{
    protected $table = 'itmosoft_conversations';

    protected $attributes = [
        'status' => 'open',
    ];

    protected $fillable = [
        'subject_type', 'subject_id', 'type', 'title', 'status',
        'last_message_at', 'metadata',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function participants(): MorphMany
    {
        return $this->morphMany(
            config('itmosoft-marketplace-messaging.participant_model', ConversationParticipant::class),
            'conversation'
        );
    }

    public function messages(): MorphMany
    {
        return $this->morphMany(
            config('itmosoft-marketplace-messaging.message_model', Message::class),
            'conversation'
        )->orderBy('id');
    }

    public function addParticipant(Model $participant, string $role = 'member'): ConversationParticipant
    {
        if ($this->status !== 'open') {
            throw new InvalidConversationOperation('Participants cannot be added to a closed conversation.');
        }

        $membership = $this->participants()->firstOrNew([
            'participant_type' => $participant->getMorphClass(),
            'participant_id' => $participant->getKey(),
        ]);
        $membership->role = $role;
        $membership->status = 'active';
        $membership->joined_at ??= now();
        $membership->left_at = null;
        $membership->save();

        return $membership;
    }

    public function removeParticipant(Model $participant): self
    {
        $membership = $this->participants()
            ->where('participant_type', $participant->getMorphClass())
            ->where('participant_id', $participant->getKey())
            ->first();

        if ($membership) {
            $membership->status = 'left';
            $membership->left_at = now();
            $membership->save();
        }

        return $this;
    }

    public function sendMessage(Model $sender, string $body, array $metadata = []): Message
    {
        if ($this->status !== 'open') {
            throw new InvalidConversationOperation('Messages cannot be sent to a closed conversation.');
        }

        $membership = $this->participants()
            ->where('participant_type', $sender->getMorphClass())
            ->where('participant_id', $sender->getKey())
            ->where('status', 'active')
            ->first();

        if (! $membership) {
            throw new InvalidConversationOperation('The sender must be an active conversation participant.');
        }

        $messageModel = config('itmosoft-marketplace-messaging.message_model', Message::class);
        $message = new $messageModel([
            'body' => $body,
            'metadata' => $metadata,
        ]);
        $message->sender()->associate($sender);
        $this->messages()->save($message);

        $this->last_message_at = $message->created_at ?? now();
        $this->save();

        return $message;
    }

    public function close(): self
    {
        $this->status = 'closed';
        $this->save();

        return $this;
    }

    public function reopen(): self
    {
        $this->status = 'open';
        $this->save();

        return $this;
    }
}
