<?php

return [
    'conversation_model' => ITMOSOFT\MarketplaceMessaging\Models\Conversation::class,
    'message_model' => ITMOSOFT\MarketplaceMessaging\Models\Message::class,
    'participant_model' => ITMOSOFT\MarketplaceMessaging\Models\ConversationParticipant::class,
    'attachment_model' => ITMOSOFT\MarketplaceMessaging\Models\MessageAttachment::class,
    'default_per_page' => 30,
    'maximum_per_page' => 100,
];
