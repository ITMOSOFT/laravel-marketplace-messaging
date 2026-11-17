<?php

namespace ITMOSOFT\MarketplaceMessaging\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use ITMOSOFT\MarketplaceMessaging\Models\ConversationParticipant;

trait HasConversations
{
    public function conversationMemberships(): MorphMany
    {
        return $this->morphMany(
            config('itmosoft-marketplace-messaging.participant_model', ConversationParticipant::class),
            'participant'
        );
    }
}
