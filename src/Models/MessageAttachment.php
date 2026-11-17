<?php

namespace ITMOSOFT\MarketplaceMessaging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MessageAttachment extends Model
{
    protected $table = 'itmosoft_message_attachments';

    protected $fillable = [
        'message_type', 'message_id', 'disk', 'path', 'original_name',
        'mime_type', 'size', 'metadata',
    ];

    protected $casts = [
        'size' => 'integer',
        'metadata' => 'array',
    ];

    public function message(): MorphTo
    {
        return $this->morphTo();
    }
}
